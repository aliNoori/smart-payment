<?php

namespace SmartPayment\Core;

use SmartPayment\Contracts\PaymentGatewayInterface;
use InvalidArgumentException;

/**
 * Class PaymentManager
 *
 * Manages payment gateway instances with support for dynamic registration
 * and class-based mapping. Acts as a service locator for resolving gateways.
 */
class PaymentManager
{
    /**
     * Cached gateway instances.
     *
     * @var array<string, PaymentGatewayInterface>
     */
    protected static array $drivers = [];

    /**
     * Mapping of gateway names to their corresponding class names.
     *
     * @var array<string, class-string<PaymentGatewayInterface>>
     */
    protected static array $map = [];

    /**
     * Manually register a gateway instance.
     *
     * @param string $name Gateway name (e.g. 'idpay')
     * @param PaymentGatewayInterface $driver Gateway instance
     */
    public static function register(string $name, PaymentGatewayInterface $driver): void
    {
        self::$drivers[strtolower($name)] = $driver;
    }

    /**
     * Map a gateway name to its class for lazy instantiation.
     *
     * @param string $name Gateway name
     * @param class-string<PaymentGatewayInterface> $className Fully qualified class name
     */
    public static function map(string $name, string $className): void
    {
        self::$map[strtolower($name)] = $className;
    }

    /**
     * Resolve a gateway instance by name.
     *
     * If the instance is already registered, it returns it.
     * Otherwise, it attempts to instantiate the mapped class.
     *
     * @param string $gateway Gateway name (e.g. 'zarinpal')
     * @return PaymentGatewayInterface
     *
     * @throws InvalidArgumentException If the gateway is not supported
     */
    public static function resolve(string $gateway): PaymentGatewayInterface
    {
        $gateway = strtolower($gateway);

        // Return existing instance if already registered
        if (isset(self::$drivers[$gateway])) {
            return self::$drivers[$gateway];
        }

        // Instantiate and cache the gateway if mapped
        if (isset(self::$map[$gateway])) {
            $className = self::$map[$gateway];
            return self::$drivers[$gateway] = new $className();
        }

        throw new InvalidArgumentException("Unsupported gateway: {$gateway}");
    }
}
