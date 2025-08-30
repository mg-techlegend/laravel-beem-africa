<?php

namespace TechLegend\LaravelBeemAfrica\Voice;

use TechLegend\LaravelBeemAfrica\BeemApiClient;

class BeemVoice extends BeemApiClient
{
    public function makeCall(string $phoneNumber, string $message, array $options = []): array
    {
        $payload = [
            'phone_number' => $phoneNumber,
            'message' => $message,
            'language' => $options['language'] ?? $this->getDefault('voice', 'language', 'en'),
            'voice_id' => $options['voice_id'] ?? $this->getDefault('voice', 'voice_id', 1),
            'repeat_count' => $options['repeat_count'] ?? 1,
        ];

        $response = $this->makeRequest('POST', $this->getEndpoint('voice') . '/call', $payload);

        if ($response['successful']) {
            $data = $response['data'];
            return [
                'successful' => true,
                'call_id' => $data['call_id'] ?? null,
                'message' => $data['message'] ?? 'Voice call initiated successfully',
                'status_code' => $response['status_code'],
                'data' => $data,
            ];
        }

        return [
            'successful' => false,
            'call_id' => null,
            'message' => 'Voice call failed: ' . ($response['error'] ?? 'Unknown error'),
            'status_code' => $response['status_code'],
            'error' => $response['error'] ?? null,
        ];
    }

    public function getCallStatus(string $callId): array
    {
        $response = $this->makeRequest('GET', $this->getEndpoint('voice') . '/calls/' . $callId);

        if ($response['successful']) {
            $data = $response['data'];
            return [
                'successful' => true,
                'call_id' => $data['call_id'] ?? $callId,
                'status' => $data['status'] ?? 'unknown',
                'duration' => $data['duration'] ?? 0,
                'message' => $data['message'] ?? 'Call status retrieved',
                'status_code' => $response['status_code'],
                'data' => $data,
            ];
        }

        return [
            'successful' => false,
            'call_id' => $callId,
            'status' => 'unknown',
            'message' => 'Failed to get call status: ' . ($response['error'] ?? 'Unknown error'),
            'status_code' => $response['status_code'],
            'error' => $response['error'] ?? null,
        ];
    }

    public function getCallHistory(array $filters = []): array
    {
        $queryParams = [];
        
        if (isset($filters['start_date'])) {
            $queryParams['start_date'] = $filters['start_date'];
        }
        
        if (isset($filters['end_date'])) {
            $queryParams['end_date'] = $filters['end_date'];
        }
        
        if (isset($filters['phone_number'])) {
            $queryParams['phone_number'] = $filters['phone_number'];
        }
        
        if (isset($filters['status'])) {
            $queryParams['status'] = $filters['status'];
        }

        $endpoint = $this->getEndpoint('voice') . '/calls';
        if (!empty($queryParams)) {
            $endpoint .= '?' . http_build_query($queryParams);
        }

        $response = $this->makeRequest('GET', $endpoint);

        if ($response['successful']) {
            $data = $response['data'];
            return [
                'successful' => true,
                'calls' => $data['calls'] ?? [],
                'total' => $data['total'] ?? 0,
                'status_code' => $response['status_code'],
                'data' => $data,
            ];
        }

        return [
            'successful' => false,
            'calls' => [],
            'message' => 'Failed to get call history: ' . ($response['error'] ?? 'Unknown error'),
            'status_code' => $response['status_code'],
            'error' => $response['error'] ?? null,
        ];
    }

    public function cancelCall(string $callId): array
    {
        $response = $this->makeRequest('DELETE', $this->getEndpoint('voice') . '/calls/' . $callId);

        if ($response['successful']) {
            $data = $response['data'];
            return [
                'successful' => true,
                'call_id' => $callId,
                'message' => $data['message'] ?? 'Call cancelled successfully',
                'status_code' => $response['status_code'],
                'data' => $data,
            ];
        }

        return [
            'successful' => false,
            'call_id' => $callId,
            'message' => 'Call cancellation failed: ' . ($response['error'] ?? 'Unknown error'),
            'status_code' => $response['status_code'],
            'error' => $response['error'] ?? null,
        ];
    }

    public function getAvailableVoices(): array
    {
        $response = $this->makeRequest('GET', $this->getEndpoint('voice') . '/voices');

        if ($response['successful']) {
            $data = $response['data'];
            return [
                'successful' => true,
                'voices' => $data['voices'] ?? [],
                'status_code' => $response['status_code'],
                'data' => $data,
            ];
        }

        return [
            'successful' => false,
            'voices' => [],
            'message' => 'Failed to get available voices: ' . ($response['error'] ?? 'Unknown error'),
            'status_code' => $response['status_code'],
            'error' => $response['error'] ?? null,
        ];
    }
}
