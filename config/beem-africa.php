<?php

// config for TechLegend/LaravelBeemAfrica
return [
    'api_key' => env('BEEM_API_KEY'),
    'secret_key' => env('BEEM_SECRET_KEY'),
    'sender_name' => env('BEEM_SENDER_NAME', 'INFO'),
];
