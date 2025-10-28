# 🚀 LuckyCode Integration Helper

*LuckyCode Integration Helper* is a PHP library for integrating with the *LuckyCode API*.
It provides a clean abstraction layer with typed request/response DTOs, automatic token management,
and optional Laravel support for quick and seamless integration.

---

## 🧩 Features

✅ Framework-agnostic core (works with Laravel, Symfony, CodeIgniter, etc.)
✅ Optional Laravel bridge (Service Provider, Config, Routes)
✅ Guzzle-based HTTP client
✅ In-memory token caching and auto-refresh
✅ Strongly typed DTOs and consistent ApiResponse model
✅ Optional PSR-3 compatible logging

---

## ⚙ Requirements

* PHP *8.2+*
* Composer
* Optional: Laravel *11* or *12*

---

## 📦 Installation

### 🧱 Option 1 — Local Path Repository

In your main project’s composer.json, add the local path repository:

json
{
  "repositories": [
    { "type": "path", "url": "./laravel" }
  ]
}


Then require the package:

bash
composer require luckycode/integration-helper:dev-main


---

## ⚙ Configuration

Add the following environment variables (or your framework’s equivalent):

bash
LUCKYCODE_BASE_URL=https://api.example.com
LUCKYCODE_API_KEY=your_api_key_here
LUCKYCODE_CLIENT_ID=your_client_id_here
LUCKYCODE_SSL_VERIFY=true


---

## 💡 Usage (Framework-Agnostic)

php
use LuckyCode\IntegrationHelper\Services\LuckyCodeService;
use LuckyCode\IntegrationHelper\Models\PullCodeRequest;

$service = new LuckyCodeService(
    baseUrl: getenv('LUCKYCODE_BASE_URL') ?: 'https://api.example.com',
    apiKey: getenv('LUCKYCODE_API_KEY') ?: '',
    clientId: getenv('LUCKYCODE_CLIENT_ID') ?: '',
    sslVerify: true
);

$request = new PullCodeRequest([
    'orderRef' => 'ORDER-001',
    'customerRef' => 'CUST-001'
]);

$response = $service->pullCode($request);

if ($response->success) {
    print_r($response->data);
} else {
    echo "Error: " . $response->error->message;
}


---

## 🧩 Laravel Integration (Optional)

### 1️⃣ Publish Configuration (optional)

bash
php artisan vendor:publish --tag=config --provider="LuckyCode\\IntegrationHelper\\IntegrationHelperServiceProvider"


This will create the config file:


config/luckycode.php


---

### 2️⃣ Add environment variables in .env

bash
LUCKYCODE_BASE_URL=https://api.example.com
LUCKYCODE_API_KEY=your_api_key_here
LUCKYCODE_CLIENT_ID=your_client_id_here
LUCKYCODE_SSL_VERIFY=true


---

### 3️⃣ Use it in your Laravel code

php
use LuckyCode\IntegrationHelper\Services\Contracts\LuckyCodeServiceContract;
use LuckyCode\IntegrationHelper\Models\PullCodeRequest;

$service = app(LuckyCodeServiceContract::class);

$response = $service->pullCode(new PullCodeRequest([
    'orderRef' => 'ORD-123',
    'customerRef' => 'CUST-456',
]));

if ($response->success) {
    dd($response->data);
} else {
    dd($response->error);
}


---

## 🌐 Available Laravel Routes

If routes are enabled in your project, the following endpoints will be available:

| HTTP     | Endpoint                                                              | Description                 |
| -------- | --------------------------------------------------------------------- | --------------------------- |
| *POST* | /api/lucky-code/pull                                                | Pull a single code          |
| *POST* | /api/lucky-code/reveal                                              | Reveal a code               |
| *POST* | /api/lucky-code/redeem                                              | Redeem a code               |
| *POST* | /api/lucky-code/multi-pull                                          | Pull multiple codes at once |
| *GET*  | /api/lucky-code/check-serialcode?serialCode=CODE123                 | Validate a serial code      |
| *GET*  | /api/lucky-code/customer-log?page=1&pageSize=30&customerRef=CUST001 | Retrieve customer code log  |

---

## 🪵 Logging (Optional)

You can pass any *PSR-3 compatible logger*, or leave it null to disable logging.

php
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use LuckyCode\IntegrationHelper\Services\LuckyCodeService;

$logger = new Logger('luckycode');
$logger->pushHandler(new StreamHandler(__DIR__.'/luckycode.log', Logger::INFO));

$service = new LuckyCodeService(
    baseUrl: 'https://api.example.com',
    apiKey: 'your_api_key',
    clientId: 'your_client_id',
    sslVerify: true,
    logger: $logger
);


---

## 🔐 Security Notes

⚠ Never commit your API keys or client IDs to source control.
Always use environment variables or a secure secret store.

---

## 🧾 Directory Structure


src/
 ├── Models/                # DTO classes (PullCodeRequest, RevealCodeRequest, etc.)
 ├── Services/              # Service logic for LuckyCode API
 ├── Support/               # ApiResponse, ErrorDto, Helpers
 ├── Http/Controllers/      # Laravel bridge (optional)
 ├── IntegrationHelperServiceProvider.php  # Laravel service provider
config/
 └── luckycode.php


---

## 🧠 Tips

* Use *multi-pull* when you need to pull several codes in one request.
* Use *getCustomersLog* to retrieve the customer’s prize or code history.
* Every API call returns an ApiResponse object:

  php
  $response->success;   // bool
  $response->data;      // mixed
  $response->error;     // ErrorDto|null
  

---

## 🧰 Troubleshooting

| Problem            | Possible Cause               | Solution                                                 |
| ------------------ | ---------------------------- | -------------------------------------------------------- |
| SSL error          | Self-signed certificate      | Set LUCKYCODE_SSL_VERIFY=false (only in development)   |
| “401 Unauthorized” | Invalid API key or client ID | Check .env values                                      |
| Empty response     | Wrong base URL               | Make sure LUCKYCODE_BASE_URL points to the correct API |
| Timeout            | API not reachable            | Verify the endpoint or network connectivity              |

---

## 🧩 License

MIT License © Redeemly LuckyCode Integration Helper