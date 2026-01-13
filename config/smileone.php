<?php
return [

    'uid' => env('SMILE_ONE_UID', ''), // Merchant UID
    'email' => env('SMILE_ONE_EMAIL', ''), // Merchant Email
    'key' => env('SMILE_ONE_KEY', ''), // Merchant Key
    'domain' => env('SMILE_ONE_DOMAIN', 'https://www.smile.one'), //Domain URL
    'default_region' => env('SMILE_ONE_REGION', 'br'), // Default region (br, ph, etc.)

    "API_URL" => [
        "get-server-list" => "/smilecoin/api/getserver",
        "get-product" => "/smilecoin/api/product",
        "get-query-points" => "/smilecoin/api/querypoints",
        "get-product-list" => "/smilecoin/api/productlist",
        "role-query" => "/smilecoin/api/getrole",
        "purchase" => "/smilecoin/api/createorder"
    ]

];