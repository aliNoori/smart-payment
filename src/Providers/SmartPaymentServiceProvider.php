<?php

namespace SmartPayment\Providers;

use Illuminate\Support\ServiceProvider;
use SmartPayment\Console\Commands\FixModelNamespace;
use SmartPayment\Contracts\PaymentServiceInterface;
use SmartPayment\Core\PaymentManager;
use SmartPayment\Services\PaymentService;

/**
 * Class SmartPaymentServiceProvider
 *
 * Registers and bootstraps the SmartPayment package components,
 * including routes, migrations, translations, configuration, and services.
 *
 * @package SmartPayment\Providers
 */
class SmartPaymentServiceProvider extends ServiceProvider
{
    /**
     * Base path of the package.
     *
     * @var string
     */
    private string $basePath;

    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->basePath = dirname(__DIR__);

        // Load package routes
        $this->loadRoutesFrom($this->basePath . '/routes/api.php');

        // Merge default config with application's config
        $this->mergeConfigFrom($this->basePath . '/config/smart-payment.php', 'smart-payment');

        // Publish config file to application
        $this->publishes([
            $this->basePath . '/config/smart-payment.php' => config_path('smart-payment.php'),
        ], 'smart-payment-config');

        // Load and publish translation files
        $this->loadTranslationsFrom($this->basePath . '/resources/lang', 'smart-payment');

        $this->publishes([
            $this->basePath . '/resources/lang' => resource_path('lang/vendor/smart-payment'),
        ], 'smart-payment-translations');

        // Load and publish migration files
        $this->loadMigrationsFrom($this->basePath . '/Database/Migrations');

        $this->publishes([
            $this->basePath . '/Database/Migrations' => database_path('migrations'),
        ], 'smart-payment-migrations');

        // Optionally publish model stubs for customization
        $this->publishes([
            $this->basePath . '/Models' => app_path('Models'),
        ], 'smart-payment-models');

        // Dynamically map configured gateways to the PaymentManager
        $gateways = config('smart-payment.gateways', []);

        foreach ($gateways as $key => $class) {
            if (is_string($class) && class_exists($class)) {
                PaymentManager::map($key, $class);
            }
        }

        // Optional: register internal service providers
        // $this->app->register(EventServiceProvider::class);
        // $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        // Bind the payment service interface to its implementation
        $this->app->bind(
            PaymentServiceInterface::class,
            PaymentService::class
        );

        // Register custom artisan commands
        $this->commands([
            FixModelNamespace::class,
        ]);
    }
}
