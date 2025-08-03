<?php

namespace SmartPayment\Gateways\Factories;

use Illuminate\Support\Facades\Config;
use InvalidArgumentException;
use SmartPayment\Contracts\PaymentGatewayInterface;

class GatewayFactory
{
    public static function make(string $gateway): PaymentGatewayInterface
    {
        $gateways = Config::get('smart-payment.gateways');

        if (!array_key_exists($gateway, $gateways)) {
            throw new InvalidArgumentException("Gateway '{$gateway}' is not defined.");
        }

        $class = $gateways[$gateway];

        if (!class_exists($class)) {
            throw new InvalidArgumentException("Gateway class '{$class}' not found.");
        }

        $instance = app($class);

        if (!$instance instanceof PaymentGatewayInterface) {
            throw new InvalidArgumentException("Gateway class '{$class}' must implement GatewayInterface.");
        }

        return $instance;
    }
}
