<?php

namespace LuckyCode\IntegrationHelper;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\Facades\File;
use Psr\Log\LoggerInterface;
use LuckyCode\IntegrationHelper\Services\Contracts\LuckyCodeServiceContract;
use LuckyCode\IntegrationHelper\Services\LuckyCodeService;

class IntegrationHelperServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function register(): void
    {
        // دمج إعدادات الباكج مع إعدادات المشروع
        $this->mergeConfigFrom(_DIR_.'/../config/luckycode.php', 'luckycode');

        // ربط LuckyCodeServiceContract بالخدمة الفعلية
        $this->app->bind(LuckyCodeServiceContract::class, function ($app) {
            /** @var LoggerInterface|null $logger */
            $logger = $app->has(LoggerInterface::class) ? $app->get(LoggerInterface::class) : null;
            return new LuckyCodeService(
                baseUrl: (string) config('luckycode.base_url'),
                apiKey: (string) config('luckycode.access_credential.api_key'),
                clientId: (string) config('luckycode.access_credential.client_id'),
                sslVerify: (bool) config('luckycode.ssl_verify', true),
                logger: $logger
            );
        });
    }

    public function boot(): void
    {
        // نشر ملف config للبروجيكت
        $this->publishes([
            _DIR_.'/../config/luckycode.php' => config_path('luckycode.php'),
        ], 'config');

        // إنشاء ملف routes/luckycode.php تلقائيًا إذا لم يكن موجود
        $routePath = base_path('routes/luckycode.php');
        if (!File::exists($routePath)) {
            File::put($routePath, <<<PHP
<?php
use Illuminate\Support\Facades\Route;
use LuckyCode\IntegrationHelper\Http\Controllers\LuckyCodeController;

Route::prefix('api/lucky-code')->group(function () {
    Route::post('pull', [LuckyCodeController::class, 'pullCode']);
    Route::post('reveal', [LuckyCodeController::class, 'revealCode']);
    Route::post('redeem', [LuckyCodeController::class, 'redeemCode']);
    Route::post('multi-pull', [LuckyCodeController::class, 'multiPull']);
    Route::get('check-serialcode', [LuckyCodeController::class, 'checkSerialCode']);
    Route::get('customer-log', [LuckyCodeController::class, 'getCustomersLog']);
});
PHP
            );
        }

        // تحميل المسارات من الملف المنشأ
        $this->loadRoutesFrom($routePath);
    }

    public function provides(): array
    {
        return [LuckyCodeServiceContract::class];
    }
}