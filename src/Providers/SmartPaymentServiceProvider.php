<?php

namespace SmartPayment\Providers;

use Illuminate\Support\ServiceProvider;
use SmartPayment\Console\Commands\FixModelNamespace;
use SmartPayment\Contracts\PaymentServiceInterface;
use SmartPayment\Services\PaymentService;

class SmartPaymentServiceProvider extends ServiceProvider
{
    private string $basePath;

    public function boot(): void
    {
        $this->basePath = dirname(__DIR__);

        $this->loadRoutesFrom($this->basePath .'/routes/api.php');

        // Register bindings and config merge
        $this->mergeConfigFrom($this->basePath .'/config/smart-payment.php', 'smart-payment');

        // Load routes, migrations, configs, etc.
        $this->publishes([
            $this->basePath .'/config/smart-payment.php' => config_path('smart-payment.php'),
        ], 'smart-payment-config');

        // Load and publish translations
        $this->loadTranslationsFrom($this->basePath . '/resources/lang', 'smart-payment');

        $this->publishes([
            $this->basePath . '/resources/lang' => resource_path('lang/vendor/smart-payment'),
        ], 'smart-payment-translations');

        // Load and publish migrations
        $this->loadMigrationsFrom($this->basePath . '/Database/Migrations');

        $this->publishes([
            $this->basePath . '/Database/Migrations' => database_path('migrations'),
        ], 'smart-payment-migrations');

        // Optionally publish model stub for customization
        $this->publishes([
            $this->basePath . '/Models' => app_path('Models'),
        ], 'smart-payment-models');

        // Register internal service providers
        //$this->app->register(EventServiceProvider::class);
        //$this->app->register(RouteServiceProvider::class);



    }
    public function register(): void
    {
        $this->app->bind(
            PaymentServiceInterface::class,
            PaymentService::class
        );

        // Register artisan commands
        $this->commands([
            FixModelNamespace::class,
        ]);
    }
}
