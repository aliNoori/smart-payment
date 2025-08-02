<?php

return [
    'default' => 'zarinpal',

    'gateways' => [
        'zarinpal' => [
            'merchant_id' => env('ZARINPAL_MERCHANT_ID','e7403b01-5bf0-43d3-b4ef-f0a00a2335a1'),
            'sandbox' => true,
        ],
        'parsian' => [
            'merchant_id' => env('PARSIAN_MERCHANT_ID','e7403b01-5bf0-43d3-b4ef-f0a00a2335a1'),
            'sandbox' => true,
        ],
        // بقیه درگاه‌ها بعداً اضافه می‌شن
    ],
];
