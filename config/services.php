<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'kpay' => [
        'api_url' => env('KPAY_API_URL', 'https://api.kpay.com.mm'),
        'merchant_id' => env('KPAY_MERCHANT_ID'),
        'api_key' => env('KPAY_API_KEY'),
        'test_mode' => env('KPAY_TEST_MODE', true),
    ],

    'wavepay' => [
        'api_url' => env('WAVEPAY_API_URL', 'https://api.wavepay.com.mm'),
        'merchant_id' => env('WAVEPAY_MERCHANT_ID'),
        'api_key' => env('WAVEPAY_API_KEY'),
        'test_mode' => env('WAVEPAY_TEST_MODE', true),
    ],
];
