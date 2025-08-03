<?php

use Illuminate\Support\Facades\Route;
use SmartPayment\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| SmartPayment API Routes
|--------------------------------------------------------------------------
|
| These routes handle payment initiation and gateway callbacks.
| The `pay` route is protected by API authentication middleware,
| while the `callback` route is publicly accessible for gateway responses.
|
*/

Route::middleware('auth:api')->prefix('payment')->group(function () {
    // Initiate a payment request (requires authentication)
    Route::post('pay', [PaymentController::class, 'initiate'])->name('payment.initiate');
});

// Handle payment gateway callback (publicly accessible)
Route::get('payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');
