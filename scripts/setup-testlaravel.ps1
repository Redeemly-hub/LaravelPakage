# Requires: PHP 8.2+ and Composer in PATH
param(
    [string]$AppName = "TestLaravel",
    [string]$PackageRelativePath = "../laravel",
    [string]$BaseUrl = "",
    [string]$ApiKey = "",
    [string]$ClientId = ""
)

function Fail($msg) {
    Write-Error $msg
    exit 1
}

Write-Host "==> Checking prerequisites (php, composer)" -ForegroundColor Cyan
if (-not (Get-Command php -ErrorAction SilentlyContinue)) { Fail "php not found in PATH." }
if (-not (Get-Command composer -ErrorAction SilentlyContinue)) { Fail "composer not found in PATH." }

# Optional: warn if 7-Zip is missing (helps Composer use dist archives)
if (-not (Get-Command 7z -ErrorAction SilentlyContinue)) {
    Write-Host "==> Tip: 7-Zip not found; install '7zip' to improve composer dist installs" -ForegroundColor Yellow
}

# Increase Composer process timeout to avoid network timeouts on slow connections
composer config -g process-timeout 1800 | Out-Null

$repoRoot = Split-Path -Parent $PSScriptRoot
Set-Location $repoRoot

# Determine composer create-project flags (skip ext-fileinfo if missing)
$cpArgs = @('--no-interaction')
try {
    $fileinfoLoaded = & php -r "echo extension_loaded('fileinfo') ? '1' : '0';"
    if ($fileinfoLoaded -ne '1') {
        Write-Host "==> PHP ext-fileinfo missing; create-project will ignore this requirement" -ForegroundColor Yellow
        $cpArgs += '--ignore-platform-req=ext-fileinfo'
    }
} catch {
    # If php check fails, continue with default flags
}

function Invoke-CreateProject([string]$targetDir) {
    & composer create-project laravel/laravel $targetDir @cpArgs
    if ($LASTEXITCODE -ne 0) {
        if (Test-Path (Join-Path $targetDir 'composer.json')) {
            Write-Host "==> create-project reported failure, but files exist; continuing" -ForegroundColor Yellow
            return
        }
        Write-Host "==> create-project failed; retrying with --prefer-source (git clone)" -ForegroundColor Yellow
        $retryArgs = $cpArgs + '--prefer-source'
        & composer create-project laravel/laravel $targetDir @retryArgs
        if ($LASTEXITCODE -ne 0) {
            if (Test-Path (Join-Path $targetDir 'composer.json')) {
                Write-Host "==> create-project (prefer-source) failed, but files exist; continuing" -ForegroundColor Yellow
                return
            }
            Fail "composer create-project failed ($targetDir)"
        }
    }
}

if (-not (Test-Path $AppName)) {
    Write-Host "==> Creating Laravel app: $AppName" -ForegroundColor Cyan
    Invoke-CreateProject $AppName
} else {
    if (-not (Test-Path (Join-Path $AppName 'composer.json'))) {
        Write-Host "==> $AppName exists but no composer.json; scaffolding into a temporary directory and copying over" -ForegroundColor Yellow
        $tempDir = "$AppName.build." + ([guid]::NewGuid().ToString())
        Invoke-CreateProject $tempDir

        # Ensure destination exists
        if (-not (Test-Path $AppName)) { New-Item -ItemType Directory -Path $AppName | Out-Null }
        # Copy all contents from temp to existing $AppName (robust copy with robocopy)
        Write-Host "==> Copying scaffolded files into $AppName" -ForegroundColor Cyan
        $src = (Resolve-Path $tempDir).Path
        $dst = (Resolve-Path $AppName).Path
        $rcCmd = @('robocopy', $src, $dst, '/MIR', '/COPY:DAT', '/R:2', '/W:2', '/NFL', '/NDL', '/NJH', '/NJS', '/NP')
        $proc = Start-Process -FilePath $rcCmd[0] -ArgumentList $rcCmd[1..($rcCmd.Length-1)] -NoNewWindow -PassThru -Wait
        $rc = $proc.ExitCode
        # robocopy exit codes 0-7 are success
        if ($rc -gt 7) { Fail "robocopy failed with exit code $rc" }
        # Cleanup temp dir
        Remove-Item -Path $tempDir -Recurse -Force -ErrorAction SilentlyContinue
    } else {
        Write-Host "==> Skipping create-project; $AppName already initialized" -ForegroundColor Yellow
    }
}

Set-Location $AppName

Write-Host "==> Configuring path repository to local package ($PackageRelativePath)" -ForegroundColor Cyan
# Resolve absolute package path to avoid relative path issues
$packagePath = (Resolve-Path $repoRoot).Path
composer config repositories.luckycode path $packagePath
if ($LASTEXITCODE -ne 0) { Fail "composer config repository failed" }

Write-Host "==> Allowing dev stability for path package (prefer-stable on)" -ForegroundColor Cyan
composer config minimum-stability dev
if ($LASTEXITCODE -ne 0) { Fail "composer config minimum-stability failed" }
composer config prefer-stable true
if ($LASTEXITCODE -ne 0) { Fail "composer config prefer-stable failed" }

Write-Host "==> Requiring luckycode/integration-helper (dev-main)" -ForegroundColor Cyan
composer require luckycode/integration-helper:dev-main --no-interaction
if ($LASTEXITCODE -ne 0) { Fail "composer require failed" }

Write-Host "==> Publishing config" -ForegroundColor Cyan
php artisan vendor:publish --tag=config --provider="LuckyCode\IntegrationHelper\IntegrationHelperServiceProvider" --no-interaction | Out-Null
if ($LASTEXITCODE -ne 0) { Fail "artisan vendor:publish failed" }

$envPath = Join-Path (Get-Location) ".env"
if (-not (Test-Path $envPath)) {
    Write-Host "==> .env not found, creating from .env.example" -ForegroundColor Yellow
    Copy-Item ".env.example" ".env" -Force
}

if ($BaseUrl -or $ApiKey -or $ClientId) {
    Write-Host "==> Setting environment variables from provided parameters" -ForegroundColor Cyan
    $envContent = Get-Content $envPath -Raw

    function Set-Or-Replace([string]$content, [string]$key, [string]$value) {
        $pattern = "(?m)^" + [regex]::Escape($key) + "=.*$"
        if ([regex]::IsMatch($content, $pattern)) {
            return [regex]::Replace($content, $pattern, "$key=$value")
        } else {
            return ($content.TrimEnd() + "`n$key=$value`n")
        }
    }

    if ($BaseUrl) { $envContent = Set-Or-Replace $envContent "LUCKYCODE_BASE_URL" $BaseUrl }
    if ($ApiKey) { $envContent = Set-Or-Replace $envContent "LUCKYCODE_API_KEY" $ApiKey }
    if ($ClientId) { $envContent = Set-Or-Replace $envContent "LUCKYCODE_CLIENT_ID" $ClientId }
    Set-Content -Path $envPath -Value $envContent -NoNewline
} else {
    Write-Host "==> Skipping .env credentials; please set LUCKYCODE_* in TestLaravel/.env" -ForegroundColor Yellow
}

Write-Host "==> Creating demo controller and routes" -ForegroundColor Cyan
$controllerDir = Join-Path (Get-Location) "app/Http/Controllers"
$controllerPath = Join-Path $controllerDir "LuckyCodeDemoController.php"
if (-not (Test-Path $controllerDir)) { Fail "Controllers directory not found" }

$controllerCode = @'
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use LuckyCode\IntegrationHelper\Services\Contracts\LuckyCodeServiceContract;

class LuckyCodeDemoController extends Controller
{
    public function health(LuckyCodeServiceContract $service)
    {
        return response()->json([
            'ok' => true,
            'baseUrl' => config('luckycode.base_url'),
        ]);
    }

    public function token(LuckyCodeServiceContract $service)
    {
        $res = $service->getToken();
        return response()->json($res);
    }
}
'@
Set-Content -Path $controllerPath -Value $controllerCode -NoNewline

$webRoutes = Join-Path (Get-Location) "routes/web.php"
if (-not (Test-Path $webRoutes)) { Fail "routes/web.php not found" }
$webContent = Get-Content $webRoutes -Raw
$routesSnippet = @'
use App\Http\Controllers\LuckyCodeDemoController;

Route::get('/lc-health', [LuckyCodeDemoController::class, 'health']);
Route::get('/lc-token', [LuckyCodeDemoController::class, 'token']);
'@
if ($webContent -notmatch "LuckyCodeDemoController") {
    Add-Content -Path $webRoutes -Value "`n$routesSnippet`n"
}

Write-Host "==> Done. You can now run: php artisan serve" -ForegroundColor Green

