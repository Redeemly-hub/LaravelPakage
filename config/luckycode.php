<?php

return [
    'base_url' => env('LUCKYCODE_BASE_URL', ''),
    'ssl_verify' => env('LUCKYCODE_SSL_VERIFY', true),
    'access_credential' => [
        'api_key' => env('LUCKYCODE_API_KEY', ''),
        'client_id' => env('LUCKYCODE_CLIENT_ID', ''),
    ],
];

