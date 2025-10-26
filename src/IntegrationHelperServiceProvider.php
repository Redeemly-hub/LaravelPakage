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
$this->mergeConfigFrom(__DIR__.'/../config/luckycode.php', 'luckycode');


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
            __DIR__.'/../config/luckycode.php' => config_path('luckycode.php'),
        ], 'config');
    
        // إنشاء ملف routes/luckycode.php تلقائيًا إذا لم يكن موجود
        $apiFile = base_path('routes/api.php');

        $routeCode = <<<PHP
    
    // LuckyCode package routes
    use LuckyCode\IntegrationHelper\Http\Controllers\LuckyCodeController;
    
    Route::prefix('lucky-code')->group(function () {
        Route::post('pull', [LuckyCodeController::class, 'pullCode']);
        Route::post('reveal', [LuckyCodeController::class, 'revealCode']);
        Route::post('redeem', [LuckyCodeController::class, 'redeemCode']);
        Route::post('multi-pull', [LuckyCodeController::class, 'multiPull']);
        Route::get('check-serialcode', [LuckyCodeController::class, 'checkSerialCode']);
        Route::get('customer-log', [LuckyCodeController::class, 'getCustomersLog']);
    });
    PHP;
    
        // التأكد من عدم وجود نفس الكود مسبقًا
        if (!str_contains(File::get($apiFile), 'LuckyCode package routes')) {
            File::append($apiFile, $routeCode);
        }
       
      
    }
    
    public function provides(): array
    {
        return [LuckyCodeServiceContract::class];
    }
}