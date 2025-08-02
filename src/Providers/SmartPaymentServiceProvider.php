<?php

namespace SmartPayment\Providers;

use Illuminate\Support\ServiceProvider;
use SmartPayment\Contracts\PaymentServiceInterface;
use SmartPayment\Services\PaymentService;

class SmartPaymentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            PaymentServiceInterface::class,
            PaymentService::class
        );

        // Register bindings and config merge
        $this->mergeConfigFrom(__DIR__.'/config/smart-payment.php', 'smart-payment');
    }

    public function boot(): void
    {
        // Load routes, migrations, configs, etc.
        $this->publishes([
            __DIR__.'/config/smart-payment.php' => config_path('smart-payment.php'),
        ], 'smart-payment-config');

        $this->loadRoutesFrom(__DIR__.'/routes/api.php');
    }
}
