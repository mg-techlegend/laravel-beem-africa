<?php

namespace TechLegend\LaravelBeemAfrica\Services;

use InvalidArgumentException;
use TechLegend\LaravelBeemAfrica\BeemAfricaClient;
use TechLegend\LaravelBeemAfrica\Data\OtpResponse;

final class BeemOtp
{
    public function __construct(
        private readonly BeemAfricaClient $client,
    ) {}

    public function generate(string $phoneNumber, array $options = []): OtpResponse
    {
        if (trim($phoneNumber) === '') {
            throw new InvalidArgumentException('[Beem Africa] Phone number is required for OTP generation.');
        }

        $normalized = $this->client->getNormalizer()->normalize($phoneNumber);

        $payload = [
            'phone_number' => $normalized,
            'length' => $options['length'] ?? $this->client->getDefault('otp', 'length', 6),
            'expiry' => $options['expiry'] ?? $this->client->getDefault('otp', 'expiry', 300),
            'type' => $options['type'] ?? $this->client->getDefault('otp', 'type', 'numeric'),
            'message' => $options['message'] ?? 'Your verification code is: {code}',
        ];

        $response = $this->client->post('/otp/generate', $payload);

        return $this->client->handleJsonResponse($response, [OtpResponse::class, 'fromApiPayload']);
    }

    public function verify(string $phoneNumber, string $code, ?string $requestId = null): OtpResponse
    {
        if (trim($phoneNumber) === '') {
            throw new InvalidArgumentException('[Beem Africa] Phone number is required for OTP verification.');
        }
        if (trim($code) === '') {
            throw new InvalidArgumentException('[Beem Africa] OTP code is required for verification.');
        }

        $normalized = $this->client->getNormalizer()->normalize($phoneNumber);

        $payload = [
            'phone_number' => $normalized,
            'code' => $code,
        ];

        if ($requestId !== null) {
            $payload['request_id'] = $requestId;
        }

        $response = $this->client->post('/otp/verify', $payload);

        return $this->client->handleJsonResponse($response, [OtpResponse::class, 'fromApiPayload']);
    }

    public function resend(string $phoneNumber, string $requestId): OtpResponse
    {
        if (trim($phoneNumber) === '') {
            throw new InvalidArgumentException('[Beem Africa] Phone number is required for OTP resend.');
        }
        if (trim($requestId) === '') {
            throw new InvalidArgumentException('[Beem Africa] Request ID is required for OTP resend.');
        }

        $normalized = $this->client->getNormalizer()->normalize($phoneNumber);

        $payload = [
            'phone_number' => $normalized,
            'request_id' => $requestId,
        ];

        $response = $this->client->post('/otp/resend', $payload);

        return $this->client->handleJsonResponse($response, [OtpResponse::class, 'fromApiPayload']);
    }
}
