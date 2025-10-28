# 🚀 Redeemly-likecard/luckycode-package

**LuckyCode Integration Helper** is a PHP library for integrating with the **LuckyCode API**.
It provides a clean abstraction layer with typed request/response DTOs, automatic token management,
and optional Laravel support for quick and seamless integration.

---

## 🧩 Features

✅ Framework-agnostic core (works with Laravel, Symfony, CodeIgniter, etc.)
✅ Optional Laravel bridge (Service Provider, Config, Routes)
✅ Guzzle-based HTTP client
✅ In-memory token caching and auto-refresh
✅ Strongly typed DTOs and consistent `ApiResponse` model
✅ Optional PSR-3 compatible logging

---

## ⚙️ Requirements

* PHP **8.2+**
* Composer
* Optional: Laravel **11** or **12**

---



## ⚙️ Configuration

Add the following environment variables (or your framework’s equivalent):

```bash
LUCKYCODE_BASE_URL=https://api.example.com
LUCKYCODE_API_KEY=your_api_key_here
LUCKYCODE_CLIENT_ID=your_client_id_here
LUCKYCODE_SSL_VERIFY=true
```

---


## 🧩  Integration 


```
composer require redeemly-likecard/luckycode-package
```

### 1️⃣ Publish Configuration 


```bash
php artisan vendor:publish --tag=config  
```

This will create the config file:

```
config/luckycode.php 
```

And will put the routes api.php like this :
```
use LuckyCode\IntegrationHelper\Http\Controllers\LuckyCodeController;

Route::prefix('lucky-code')->group(function () {
    Route::post('pull', [LuckyCodeController::class, 'pullCode']);
    Route::post('reveal', [LuckyCodeController::class, 'revealCode']);
    Route::post('redeem', [LuckyCodeController::class, 'redeemCode']);
    Route::post('multi-pull', [LuckyCodeController::class, 'multiPull']);
    Route::get('check-serialcode', [LuckyCodeController::class, 'checkSerialCode']);
    Route::get('customer-log', [LuckyCodeController::class, 'getCustomersLog']);
});
```

---

### 2️⃣ Add environment variables in `.env`

```bash
LUCKYCODE_BASE_URL=https://api.example.com
LUCKYCODE_API_KEY=your_api_key_here
LUCKYCODE_CLIENT_ID=your_client_id_here
LUCKYCODE_SSL_VERIFY=true
```

---

### 3️⃣ Use it in your Laravel code

```php
// in your PHPcontroller 

use LuckyCode\IntegrationHelper\Services\LuckyCodeService;
use LuckyCode\IntegrationHelper\Models\PullCodeRequest;

    private LuckyCodeService $luckyCodeService;

    public function __construct()
    {
        // Initialize LuckyCode service with configuration
        $this->luckyCodeService = new LuckyCodeService(
            baseUrl: config('luckycode.base_url'),
            apiKey: config('luckycode.access_credential.api_key'),
            clientId: config('luckycode.access_credential.client_id'),
            sslVerify: config('luckycode.ssl_verify', true)
        );
        
    }



//Call the pull api whenever a trigger happen: complete order

 $pullRequest = new PullCodeRequest([
                'orderRef' => 'GIFT_' . time() . '_' . rand(1000, 9999),
                'customerRef' => $customerRef,
                'orderDetails' => [
                    'product_id' => 'GIFT_PRODUCT_' . rand(1000, 9999),
                    'quantity' => $quantity,
                ],
            ]);

            // Call multi-pull API
            $response = $this->luckyCodeService->multiPull($pullRequest);

            if ($response->success) {
                \Log::info('Gift provided successfully', [
                    'customer_ref' => $customerRef,
                    'quantity' => $quantity,
                    'response' => $response
                ]);

                return [
                    'status' => 'success',
                    'message' => 'Gift provided successfully',
                    'codes' => $response->data ?? [],
                    'quantity_provided' => $quantity,
                    'provided_at' => now()->toISOString()
                ];
            } else {
                \Log::warning('Gift provision failed', [
                    'customer_ref' => $customerRef,
                    'quantity' => $quantity,
                    'error' => $response->error
                ]);

                return [
                    'status' => 'failed',
                    'message' => 'Failed to provide gift',
                    'error' => $response->error,
                    'attempted_at' => now()->toISOString()
                ];
            }

```

---

## 🌐 Available Laravel Routes

If routes are enabled in your project, the following endpoints will be available:

| HTTP     | Endpoint                                                              | Description                 |
| -------- | --------------------------------------------------------------------- | --------------------------- |
| **POST** | `/api/lucky-code/pull`                                                | Pull a single code          |
| **POST** | `/api/lucky-code/reveal`                                              | Reveal a code               |
| **POST** | `/api/lucky-code/redeem`                                              | Redeem a code               |
| **POST** | `/api/lucky-code/multi-pull`                                          | Pull multiple codes at once |
| **GET**  | `/api/lucky-code/check-serialcode?serialCode=CODE123`                 | Validate a serial code      |
| **GET**  | `/api/lucky-code/customer-log?page=1&pageSize=30&customerRef=CUST001` | Retrieve customer code log  |

---




## 🔐 Security Notes

⚠️ Never commit your API keys or client IDs to source control.
Always use environment variables or a secure secret store.

 

---

## 🧠 Tips

* Use **multi-pull** when you need to pull codes in one request.
* Use **getCustomersLog** to retrieve the customer’s prize or code history.
* Every API call returns an `ApiResponse` object:

  ```php
  $response->success;   // bool
  $response->data;      // mixed
  $response->error;     // ErrorDto|null
  ```

---

## 🧰 Troubleshooting

| Problem            | Possible Cause               | Solution                                                 |
| ------------------ | ---------------------------- | -------------------------------------------------------- |
| SSL error          | Self-signed certificate      | Set `LUCKYCODE_SSL_VERIFY=false` (only in development)   |
| “401 Unauthorized” | Invalid API key or client ID | Check `.env` values                                      |
| Empty response     | Wrong base URL               | Make sure `LUCKYCODE_BASE_URL` points to the correct API |
| Timeout            | API not reachable            | Verify the endpoint or network connectivity              |

---

## 🧩 License

MIT License © Redeemly LuckyCode Integration Helper

