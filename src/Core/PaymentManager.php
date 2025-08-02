<?php

namespace SmartPayment\Core;

use SmartPayment\Contracts\PaymentGatewayInterface;
use InvalidArgumentException;
use SmartPayment\Gateways\ZarinpalGateway;

class PaymentManager
{
    protected static array $drivers = [];

    public static function resolve(string $gateway): PaymentGatewayInterface
    {
        $gateway = strtolower($gateway);

        return match ($gateway) {
            'zarinpal' => self::$drivers['zarinpal'] ??= new ZarinpalGateway(),
            default => throw new InvalidArgumentException("Unsupported gateway: {$gateway}"),
        };
    }
}
