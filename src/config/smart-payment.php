<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Payment Gateway
    |--------------------------------------------------------------------------
    |
    | This value determines which gateway will be used by default
    | when none is explicitly specified in the request.
    |
    */
    'default' => 'zarinpal',

    /*
    |--------------------------------------------------------------------------
    | Available Payment Gateways
    |--------------------------------------------------------------------------
    |
    | Define the list of supported gateways and map each one to its
    | corresponding class. You can add or override gateways as needed.
    |
    */
    'gateways' => [
        'zarinpal' => \SmartPayment\Gateways\ZarinpalGateway::class,
        // 'idpay' => \SmartPayment\Gateways\IDPayGateway::class,
    ],

    'merchant_id' => env('GATEWAY_MERCHANT_ID','e7403b01-5bf0-43d3-b4ef-f0a00a2335a1'),
    'sandbox' => true,

    /*
    |--------------------------------------------------------------------------
    | Model Bindings
    |--------------------------------------------------------------------------
    |
    | Define the Eloquent models used by the package. You can override
    | these with your own models to customize behavior or relationships.
    |
    */
    'models' => [
        'order' => \SmartPayment\Models\Order::class,
        'transaction' => \SmartPayment\Models\Transaction::class,
    ],
];
