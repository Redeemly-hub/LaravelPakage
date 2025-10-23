# LuckyCode Integration Helper

Framework-agnostic PHP library for integrating with the LuckyCode API. It provides typed request/response DTOs, token management with in-memory caching, and an optional Laravel bridge for seamless framework integration.

## Features

- Clean, framework-agnostic core (pure PHP)
- Optional Laravel bridge (service provider, routes, config publish)
- Guzzle-based HTTP client with configurable SSL verification
- In-memory access token caching and refresh
- Typed DTOs and consistent `ApiResponse` wrapper
- PSR-3 compatible optional logging

## Requirements

- PHP 8.2+
- Works with any PHP framework (Laravel, CodeIgniter, Symfony, etc.)

## Installation (local path repository example)

1) Add a local path repository to your root `composer.json` (adjust the path as needed):

```
{"repositories": [{ "type": "path", "url": "./laravel" }]}
```

2) Require the package:

```
composer require luckycode/integration-helper:dev-main
```

3) Laravel only (optional): publish the config file

```
php artisan vendor:publish --tag=config --provider="LuckyCode\\IntegrationHelper\\IntegrationHelperServiceProvider"
```

## Configuration

Set the following environment variables in your application (names are suggestions; use whatever configuration system your framework provides):

```
LUCKYCODE_BASE_URL=https://example.com
LUCKYCODE_API_KEY=your_api_key
LUCKYCODE_CLIENT_ID=your_client_id
```

## Usage (Framework-agnostic)

```php
use LuckyCode\IntegrationHelper\Services\LuckyCodeService;
use LuckyCode\IntegrationHelper\Models\PullCodeRequest;

$service = new LuckyCodeService(
    baseUrl: getenv('LUCKYCODE_BASE_URL') ?: 'https://example.com',
    apiKey: getenv('LUCKYCODE_API_KEY') ?: '',
    clientId: getenv('LUCKYCODE_CLIENT_ID') ?: '',
    sslVerify: true, // set according to your environment
    logger: null // optionally pass a PSR-3 logger
);

$response = $service->pullCode(new PullCodeRequest([
    'orderRef' => 'ORD-1',
    'customerRef' => 'CUST-1',
]));

if ($response->success) {
    // handle $response->data
} else {
    // inspect $response->error
}
```

## Laravel Integration (Optional)

This package ships with a service provider that wires configuration and (optionally) a PSR-3 logger from the host Laravel application.

1) Ensure `.env` contains:

```
LUCKYCODE_BASE_URL=https://example.com
LUCKYCODE_API_KEY=your_api_key
LUCKYCODE_CLIENT_ID=your_client_id
LUCKYCODE_SSL_VERIFY=true
```

2) Resolve the service via the container:

```php
use LuckyCode\IntegrationHelper\Services\Contracts\LuckyCodeServiceContract;
use LuckyCode\IntegrationHelper\Models\PullCodeRequest;

$service = app(LuckyCodeServiceContract::class);
$response = $service->pullCode(new PullCodeRequest([
    'orderRef' => 'ORD-1',
    'customerRef' => 'CUST-1',
]));
```

### Optional Laravel Routes

If you keep the bundled routes enabled, the following endpoints are available:

- POST `api/lucky-code/pull`
- POST `api/lucky-code/reveal`
- POST `api/lucky-code/redeem`
- POST `api/lucky-code/multi-pull`
- GET  `api/lucky-code/check-serialcode?serialCode=...`
- GET  `api/lucky-code/customer-log?page=&pageSize=&customerRef=`

## Logging

The core library accepts any PSR-3 compatible logger. If none is provided, logging is disabled.

## Security

Avoid committing credentials. Always provide API keys and client IDs through environment variables or a secure secret store.

## Versioning

This package follows semantic versioning when tagged releases are available. Until then, `dev-main` may introduce changes between commits.

## License

MIT

