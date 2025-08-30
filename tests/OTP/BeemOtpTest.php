<?php

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use TechLegend\LaravelBeemAfrica\OTP\BeemOtp;

it('generates OTP successfully', function () {
    $mockClient = Mockery::mock(Client::class);
    $responseBody = json_encode([
        'successful' => true,
        'request_id' => 'otp_123456',
        'message' => 'OTP generated successfully',
    ]);
    $mockResponse = new Response(200, [], $responseBody);

    $mockClient->shouldReceive('request')
        ->once()
        ->andReturn($mockResponse);

    $config = [
        'api_key' => 'fake_api_key',
        'secret_key' => 'fake_secret',
        'endpoints' => [
            'otp' => 'https://apisms.beem.africa/v1/otp',
        ],
        'defaults' => [
            'otp' => [
                'length' => 6,
                'expiry' => 300,
                'type' => 'numeric',
            ],
        ],
    ];

    $beemOtp = new BeemOtp($config, $mockClient);

    $result = $beemOtp->generateOtp('255700000001', [
        'length' => 6,
        'expiry' => 300,
        'type' => 'numeric',
        'message' => 'Your verification code is: {code}',
    ]);

    expect($result['successful'])->toBeTrue();
    expect($result['request_id'])->toBe('otp_123456');
    expect($result['message'])->toBe('OTP generated successfully');
    expect($result['status_code'])->toBe(200);
});

it('verifies OTP successfully', function () {
    $mockClient = Mockery::mock(Client::class);
    $responseBody = json_encode([
        'successful' => true,
        'valid' => true,
        'message' => 'OTP verified successfully',
    ]);
    $mockResponse = new Response(200, [], $responseBody);

    $mockClient->shouldReceive('request')
        ->once()
        ->andReturn($mockResponse);

    $config = [
        'api_key' => 'fake_api_key',
        'secret_key' => 'fake_secret',
        'endpoints' => [
            'otp' => 'https://apisms.beem.africa/v1/otp',
        ],
    ];

    $beemOtp = new BeemOtp($config, $mockClient);

    $result = $beemOtp->verifyOtp('255700000001', '123456', 'otp_123456');

    expect($result['successful'])->toBeTrue();
    expect($result['valid'])->toBeTrue();
    expect($result['message'])->toBe('OTP verified successfully');
    expect($result['status_code'])->toBe(200);
});

it('resends OTP successfully', function () {
    $mockClient = Mockery::mock(Client::class);
    $responseBody = json_encode([
        'successful' => true,
        'request_id' => 'otp_123456',
        'message' => 'OTP resent successfully',
    ]);
    $mockResponse = new Response(200, [], $responseBody);

    $mockClient->shouldReceive('request')
        ->once()
        ->andReturn($mockResponse);

    $config = [
        'api_key' => 'fake_api_key',
        'secret_key' => 'fake_secret',
        'endpoints' => [
            'otp' => 'https://apisms.beem.africa/v1/otp',
        ],
    ];

    $beemOtp = new BeemOtp($config, $mockClient);

    $result = $beemOtp->resendOtp('255700000001', 'otp_123456');

    expect($result['successful'])->toBeTrue();
    expect($result['request_id'])->toBe('otp_123456');
    expect($result['message'])->toBe('OTP resent successfully');
    expect($result['status_code'])->toBe(200);
});

it('handles OTP generation failure', function () {
    $mockClient = Mockery::mock(Client::class);
    $mockClient->shouldReceive('request')
        ->once()
        ->andThrow(new \GuzzleHttp\Exception\RequestException(
            'Request failed',
            new \GuzzleHttp\Psr7\Request('POST', 'test')
        ));

    $config = [
        'api_key' => 'fake_api_key',
        'secret_key' => 'fake_secret',
        'endpoints' => [
            'otp' => 'https://apisms.beem.africa/v1/otp',
        ],
    ];

    $beemOtp = new BeemOtp($config, $mockClient);

    $result = $beemOtp->generateOtp('255700000001');

    expect($result['successful'])->toBeFalse();
    expect($result['request_id'])->toBeNull();
    expect($result['message'])->toContain('OTP generation failed');
    expect($result['status_code'])->toBe(500);
});
