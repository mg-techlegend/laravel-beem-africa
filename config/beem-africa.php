<?php

// config for TechLegend/LaravelBeemAfrica
return [
    'api_key' => env('BEEM_API_KEY'),
    'secret_key' => env('BEEM_SECRET_KEY'),
    'sender_name' => env('BEEM_SENDER_NAME', 'INFO'),

    // Base URL for all Beem Africa API endpoints
    'base_url' => env('BEEM_BASE_URL', 'https://apisms.beem.africa/v1'),

    // HTTP client settings
    'timeout' => (float) env('BEEM_TIMEOUT', 30),
    'connect_timeout' => (float) env('BEEM_CONNECT_TIMEOUT', 10),
    'http_retry_attempts' => max(1, (int) env('BEEM_HTTP_RETRY_ATTEMPTS', 1)),
    'http_retry_delay_ms' => max(0, (int) env('BEEM_HTTP_RETRY_DELAY_MS', 250)),

    // Default settings for different services
    'defaults' => [
        'sms' => [
            'encoding' => (int) env('BEEM_SMS_ENCODING', 0), // 0 = GSM7, 1 = UCS2
        ],
        'otp' => [
            'length' => (int) env('BEEM_OTP_LENGTH', 6),
            'expiry' => (int) env('BEEM_OTP_EXPIRY', 300), // seconds
            'type' => env('BEEM_OTP_TYPE', 'numeric'), // numeric, alphanumeric
        ],
        'airtime' => [
            'currency' => env('BEEM_AIRTIME_CURRENCY', 'TZS'),
        ],
        'voice' => [
            'language' => env('BEEM_VOICE_LANGUAGE', 'en'),
            'voice_id' => (int) env('BEEM_VOICE_ID', 1),
        ],
    ],

    // Phone number normalization
    'default_country_calling_code' => env('BEEM_DEFAULT_COUNTRY_CODE'),

    // Webhook settings
    'webhooks' => [
        'enabled' => (bool) env('BEEM_WEBHOOKS_ENABLED', false),
        'secret' => env('BEEM_WEBHOOK_SECRET'),
    ],
];
