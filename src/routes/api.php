<?php

use Illuminate\Support\Facades\Route;
use SmartPayment\Http\Controllers\PaymentController;

Route::middleware('auth:api')->prefix('payment')->group(function () {
    Route::post('pay', [PaymentController::class, 'initiate'])->name('payment.initiate');
});

Route::get('payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');
