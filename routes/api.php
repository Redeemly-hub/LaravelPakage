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

