<?php

namespace LuckyCode\IntegrationHelper;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;
use Psr\Log\LoggerInterface;
use LuckyCode\IntegrationHelper\Services\Contracts\LuckyCodeServiceContract;
use LuckyCode\IntegrationHelper\Services\LuckyCodeService;

class IntegrationHelperServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/luckycode.php', 'luckycode');

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
        $this->publishes([
            __DIR__.'/../config/luckycode.php' => config_path('luckycode.php'),
        ], 'config');

        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }

    public function provides(): array
    {
        return [LuckyCodeServiceContract::class];
    }
}

