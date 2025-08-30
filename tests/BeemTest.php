<?php

use TechLegend\LaravelBeemAfrica\Beem;

it('initializes all services correctly', function () {
    $config = [
        'api_key' => 'test_api_key',
        'secret_key' => 'test_secret_key',
        'sender_name' => 'TestSender',
        'endpoints' => [
            'sms' => 'https://apisms.beem.africa/v1/send',
            'otp' => 'https://apisms.beem.africa/v1/otp',
            'airtime' => 'https://apisms.beem.africa/v1/airtime',
            'ussd' => 'https://apisms.beem.africa/v1/ussd',
            'voice' => 'https://apisms.beem.africa/v1/voice',
            'insights' => 'https://apisms.beem.africa/v1/insights',
        ],
        'defaults' => [
            'sms' => ['encoding' => 0],
            'otp' => ['length' => 6, 'expiry' => 300, 'type' => 'numeric'],
            'airtime' => ['currency' => 'TZS'],
            'voice' => ['language' => 'en', 'voice_id' => 1],
        ],
        'webhooks' => [
            'enabled' => true,
            'secret' => 'test_secret',
            'endpoint' => 'https://test.com/webhook',
        ],
    ];

    $beem = new Beem($config);

    // Test that all services are accessible
    expect($beem->sms())->toBeInstanceOf(\TechLegend\LaravelBeemAfrica\SMS\Beem::class);
    expect($beem->otp())->toBeInstanceOf(\TechLegend\LaravelBeemAfrica\OTP\BeemOtp::class);
    expect($beem->airtime())->toBeInstanceOf(\TechLegend\LaravelBeemAfrica\Airtime\BeemAirtime::class);
    expect($beem->ussd())->toBeInstanceOf(\TechLegend\LaravelBeemAfrica\USSD\BeemUssd::class);
    expect($beem->voice())->toBeInstanceOf(\TechLegend\LaravelBeemAfrica\Voice\BeemVoice::class);
    expect($beem->insights())->toBeInstanceOf(\TechLegend\LaravelBeemAfrica\Insights\BeemInsights::class);
    expect($beem->webhooks())->toBeInstanceOf(\TechLegend\LaravelBeemAfrica\Webhooks\BeemWebhookHandler::class);

    // Test configuration access
    expect($beem->getConfig())->toBe($config);
});

it('provides quick SMS method', function () {
    $config = [
        'api_key' => 'test_api_key',
        'secret_key' => 'test_secret_key',
        'sender_name' => 'TestSender',
        'endpoints' => [
            'sms' => 'https://apisms.beem.africa/v1/send',
        ],
    ];

    $beem = new Beem($config);

    // Mock the SMS service
    $mockSms = Mockery::mock(\TechLegend\LaravelBeemAfrica\SMS\Beem::class);
    $mockSms->shouldReceive('sendMessage')
        ->once()
        ->andReturn([
            'successful' => true,
            'request_id' => 'test_123',
            'message' => 'Message sent successfully',
        ]);

    // Replace the SMS service with mock
    $reflection = new ReflectionClass($beem);
    $property = $reflection->getProperty('sms');
    $property->setAccessible(true);
    $property->setValue($beem, $mockSms);

    $result = $beem->sendSms('255700000001', 'Test message', 'TestSender');

    expect($result['successful'])->toBeTrue();
    expect($result['request_id'])->toBe('test_123');
});

it('provides quick OTP methods', function () {
    $config = [
        'api_key' => 'test_api_key',
        'secret_key' => 'test_secret_key',
        'endpoints' => [
            'otp' => 'https://apisms.beem.africa/v1/otp',
        ],
    ];

    $beem = new Beem($config);

    // Mock the OTP service
    $mockOtp = Mockery::mock(\TechLegend\LaravelBeemAfrica\OTP\BeemOtp::class);
    $mockOtp->shouldReceive('generateOtp')
        ->once()
        ->andReturn([
            'successful' => true,
            'request_id' => 'otp_123',
            'message' => 'OTP generated successfully',
        ]);

    $mockOtp->shouldReceive('verifyOtp')
        ->once()
        ->andReturn([
            'successful' => true,
            'valid' => true,
            'message' => 'OTP verified successfully',
        ]);

    // Replace the OTP service with mock
    $reflection = new ReflectionClass($beem);
    $property = $reflection->getProperty('otp');
    $property->setAccessible(true);
    $property->setValue($beem, $mockOtp);

    $generateResult = $beem->generateOtp('255700000001');
    $verifyResult = $beem->verifyOtp('255700000001', '123456');

    expect($generateResult['successful'])->toBeTrue();
    expect($generateResult['request_id'])->toBe('otp_123');
    expect($verifyResult['successful'])->toBeTrue();
    expect($verifyResult['valid'])->toBeTrue();
});

it('provides quick airtime method', function () {
    $config = [
        'api_key' => 'test_api_key',
        'secret_key' => 'test_secret_key',
        'endpoints' => [
            'airtime' => 'https://apisms.beem.africa/v1/airtime',
        ],
    ];

    $beem = new Beem($config);

    // Mock the Airtime service
    $mockAirtime = Mockery::mock(\TechLegend\LaravelBeemAfrica\Airtime\BeemAirtime::class);
    $mockAirtime->shouldReceive('sendAirtime')
        ->once()
        ->andReturn([
            'successful' => true,
            'transaction_id' => 'airtime_123',
            'message' => 'Airtime sent successfully',
        ]);

    // Replace the Airtime service with mock
    $reflection = new ReflectionClass($beem);
    $property = $reflection->getProperty('airtime');
    $property->setAccessible(true);
    $property->setValue($beem, $mockAirtime);

    $result = $beem->sendAirtime('255700000001', 1000.00);

    expect($result['successful'])->toBeTrue();
    expect($result['transaction_id'])->toBe('airtime_123');
});

it('provides quick voice call method', function () {
    $config = [
        'api_key' => 'test_api_key',
        'secret_key' => 'test_secret_key',
        'endpoints' => [
            'voice' => 'https://apisms.beem.africa/v1/voice',
        ],
    ];

    $beem = new Beem($config);

    // Mock the Voice service
    $mockVoice = Mockery::mock(\TechLegend\LaravelBeemAfrica\Voice\BeemVoice::class);
    $mockVoice->shouldReceive('makeCall')
        ->once()
        ->andReturn([
            'successful' => true,
            'call_id' => 'call_123',
            'message' => 'Call initiated successfully',
        ]);

    // Replace the Voice service with mock
    $reflection = new ReflectionClass($beem);
    $property = $reflection->getProperty('voice');
    $property->setAccessible(true);
    $property->setValue($beem, $mockVoice);

    $result = $beem->makeCall('255700000001', 'Test voice message');

    expect($result['successful'])->toBeTrue();
    expect($result['call_id'])->toBe('call_123');
});

it('provides balance method', function () {
    $config = [
        'api_key' => 'test_api_key',
        'secret_key' => 'test_secret_key',
        'endpoints' => [
            'insights' => 'https://apisms.beem.africa/v1/insights',
        ],
    ];

    $beem = new Beem($config);

    // Mock the Insights service
    $mockInsights = Mockery::mock(\TechLegend\LaravelBeemAfrica\Insights\BeemInsights::class);
    $mockInsights->shouldReceive('getAccountBalance')
        ->once()
        ->andReturn([
            'successful' => true,
            'balance' => 5000.00,
            'currency' => 'TZS',
        ]);

    // Replace the Insights service with mock
    $reflection = new ReflectionClass($beem);
    $property = $reflection->getProperty('insights');
    $property->setAccessible(true);
    $property->setValue($beem, $mockInsights);

    $result = $beem->getBalance();

    expect($result['successful'])->toBeTrue();
    expect($result['balance'])->toBe(5000.00);
    expect($result['currency'])->toBe('TZS');
});
