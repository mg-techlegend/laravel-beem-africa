<?php

// config for TechLegend/LaravelBeemAfrica
return [
    'api_key' => env('BEEM_API_KEY'),
    'secret_key' => env('BEEM_SECRET_KEY'),
    'sender_name' => env('BEEM_SENDER_NAME', 'INFO'),
    
    // API Endpoints
    'endpoints' => [
        'sms' => env('BEEM_SMS_ENDPOINT', 'https://apisms.beem.africa/v1/send'),
        'otp' => env('BEEM_OTP_ENDPOINT', 'https://apisms.beem.africa/v1/otp'),
        'airtime' => env('BEEM_AIRTIME_ENDPOINT', 'https://apisms.beem.africa/v1/airtime'),
        'ussd' => env('BEEM_USSD_ENDPOINT', 'https://apisms.beem.africa/v1/ussd'),
        'voice' => env('BEEM_VOICE_ENDPOINT', 'https://apisms.beem.africa/v1/voice'),
        'insights' => env('BEEM_INSIGHTS_ENDPOINT', 'https://apisms.beem.africa/v1/insights'),
    ],
    
    // Default settings for different services
    'defaults' => [
        'sms' => [
            'encoding' => 0, // 0 for GSM7, 1 for UCS2
        ],
        'otp' => [
            'length' => 6,
            'expiry' => 300, // 5 minutes in seconds
            'type' => 'numeric', // numeric, alphanumeric
        ],
        'airtime' => [
            'currency' => 'TZS',
        ],
        'voice' => [
            'language' => 'en',
            'voice_id' => 1,
        ],
    ],
    
    // Webhook settings
    'webhooks' => [
        'enabled' => env('BEEM_WEBHOOKS_ENABLED', false),
        'secret' => env('BEEM_WEBHOOK_SECRET'),
        'endpoint' => env('BEEM_WEBHOOK_ENDPOINT'),
    ],
];
