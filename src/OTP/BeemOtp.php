<?php

namespace TechLegend\LaravelBeemAfrica\OTP;

use TechLegend\LaravelBeemAfrica\BeemApiClient;

class BeemOtp extends BeemApiClient
{
    public function generateOtp(string $phoneNumber, array $options = []): array
    {
        $payload = [
            'phone_number' => $phoneNumber,
            'length' => $options['length'] ?? $this->getDefault('otp', 'length', 6),
            'expiry' => $options['expiry'] ?? $this->getDefault('otp', 'expiry', 300),
            'type' => $options['type'] ?? $this->getDefault('otp', 'type', 'numeric'),
            'message' => $options['message'] ?? 'Your verification code is: {code}',
        ];

        $response = $this->makeRequest('POST', $this->getEndpoint('otp') . '/generate', $payload);

        if ($response['successful']) {
            $data = $response['data'];
            return [
                'successful' => true,
                'request_id' => $data['request_id'] ?? null,
                'message' => $data['message'] ?? 'OTP generated successfully',
                'status_code' => $response['status_code'],
                'data' => $data,
            ];
        }

        return [
            'successful' => false,
            'request_id' => null,
            'message' => 'OTP generation failed: ' . ($response['error'] ?? 'Unknown error'),
            'status_code' => $response['status_code'],
            'error' => $response['error'] ?? null,
        ];
    }

    public function verifyOtp(string $phoneNumber, string $code, string $requestId = null): array
    {
        $payload = [
            'phone_number' => $phoneNumber,
            'code' => $code,
        ];

        if ($requestId) {
            $payload['request_id'] = $requestId;
        }

        $response = $this->makeRequest('POST', $this->getEndpoint('otp') . '/verify', $payload);

        if ($response['successful']) {
            $data = $response['data'];
            return [
                'successful' => true,
                'valid' => $data['valid'] ?? false,
                'message' => $data['message'] ?? 'OTP verification completed',
                'status_code' => $response['status_code'],
                'data' => $data,
            ];
        }

        return [
            'successful' => false,
            'valid' => false,
            'message' => 'OTP verification failed: ' . ($response['error'] ?? 'Unknown error'),
            'status_code' => $response['status_code'],
            'error' => $response['error'] ?? null,
        ];
    }

    public function resendOtp(string $phoneNumber, string $requestId): array
    {
        $payload = [
            'phone_number' => $phoneNumber,
            'request_id' => $requestId,
        ];

        $response = $this->makeRequest('POST', $this->getEndpoint('otp') . '/resend', $payload);

        if ($response['successful']) {
            $data = $response['data'];
            return [
                'successful' => true,
                'request_id' => $data['request_id'] ?? null,
                'message' => $data['message'] ?? 'OTP resent successfully',
                'status_code' => $response['status_code'],
                'data' => $data,
            ];
        }

        return [
            'successful' => false,
            'request_id' => null,
            'message' => 'OTP resend failed: ' . ($response['error'] ?? 'Unknown error'),
            'status_code' => $response['status_code'],
            'error' => $response['error'] ?? null,
        ];
    }
}
