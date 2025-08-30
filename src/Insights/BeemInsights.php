<?php

namespace TechLegend\LaravelBeemAfrica\Insights;

use TechLegend\LaravelBeemAfrica\BeemApiClient;

class BeemInsights extends BeemApiClient
{
    public function getMessageDeliveryReport(string $requestId): array
    {
        $response = $this->makeRequest('GET', $this->getEndpoint('insights') . '/delivery-report/' . $requestId);

        if ($response['successful']) {
            $data = $response['data'];
            return [
                'successful' => true,
                'request_id' => $requestId,
                'delivery_report' => $data,
                'status_code' => $response['status_code'],
                'data' => $data,
            ];
        }

        return [
            'successful' => false,
            'request_id' => $requestId,
            'delivery_report' => null,
            'message' => 'Failed to get delivery report: ' . ($response['error'] ?? 'Unknown error'),
            'status_code' => $response['status_code'],
            'error' => $response['error'] ?? null,
        ];
    }

    public function getMessageStatistics(array $filters = []): array
    {
        $queryParams = [];
        
        if (isset($filters['start_date'])) {
            $queryParams['start_date'] = $filters['start_date'];
        }
        
        if (isset($filters['end_date'])) {
            $queryParams['end_date'] = $filters['end_date'];
        }
        
        if (isset($filters['sender_name'])) {
            $queryParams['sender_name'] = $filters['sender_name'];
        }

        $endpoint = $this->getEndpoint('insights') . '/statistics';
        if (!empty($queryParams)) {
            $endpoint .= '?' . http_build_query($queryParams);
        }

        $response = $this->makeRequest('GET', $endpoint);

        if ($response['successful']) {
            $data = $response['data'];
            return [
                'successful' => true,
                'statistics' => $data,
                'status_code' => $response['status_code'],
                'data' => $data,
            ];
        }

        return [
            'successful' => false,
            'statistics' => null,
            'message' => 'Failed to get message statistics: ' . ($response['error'] ?? 'Unknown error'),
            'status_code' => $response['status_code'],
            'error' => $response['error'] ?? null,
        ];
    }

    public function getAccountBalance(): array
    {
        $response = $this->makeRequest('GET', $this->getEndpoint('insights') . '/balance');

        if ($response['successful']) {
            $data = $response['data'];
            return [
                'successful' => true,
                'balance' => $data['balance'] ?? 0,
                'currency' => $data['currency'] ?? 'TZS',
                'status_code' => $response['status_code'],
                'data' => $data,
            ];
        }

        return [
            'successful' => false,
            'balance' => 0,
            'message' => 'Failed to get account balance: ' . ($response['error'] ?? 'Unknown error'),
            'status_code' => $response['status_code'],
            'error' => $response['error'] ?? null,
        ];
    }

    public function getMessageHistory(array $filters = []): array
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
        
        if (isset($filters['page'])) {
            $queryParams['page'] = $filters['page'];
        }
        
        if (isset($filters['limit'])) {
            $queryParams['limit'] = $filters['limit'];
        }

        $endpoint = $this->getEndpoint('insights') . '/messages';
        if (!empty($queryParams)) {
            $endpoint .= '?' . http_build_query($queryParams);
        }

        $response = $this->makeRequest('GET', $endpoint);

        if ($response['successful']) {
            $data = $response['data'];
            return [
                'successful' => true,
                'messages' => $data['messages'] ?? [],
                'total' => $data['total'] ?? 0,
                'page' => $data['page'] ?? 1,
                'limit' => $data['limit'] ?? 10,
                'status_code' => $response['status_code'],
                'data' => $data,
            ];
        }

        return [
            'successful' => false,
            'messages' => [],
            'message' => 'Failed to get message history: ' . ($response['error'] ?? 'Unknown error'),
            'status_code' => $response['status_code'],
            'error' => $response['error'] ?? null,
        ];
    }

    public function getFailedMessages(array $filters = []): array
    {
        $queryParams = [];
        
        if (isset($filters['start_date'])) {
            $queryParams['start_date'] = $filters['start_date'];
        }
        
        if (isset($filters['end_date'])) {
            $queryParams['end_date'] = $filters['end_date'];
        }
        
        if (isset($filters['error_code'])) {
            $queryParams['error_code'] = $filters['error_code'];
        }

        $endpoint = $this->getEndpoint('insights') . '/failed-messages';
        if (!empty($queryParams)) {
            $endpoint .= '?' . http_build_query($queryParams);
        }

        $response = $this->makeRequest('GET', $endpoint);

        if ($response['successful']) {
            $data = $response['data'];
            return [
                'successful' => true,
                'failed_messages' => $data['failed_messages'] ?? [],
                'total' => $data['total'] ?? 0,
                'status_code' => $response['status_code'],
                'data' => $data,
            ];
        }

        return [
            'successful' => false,
            'failed_messages' => [],
            'message' => 'Failed to get failed messages: ' . ($response['error'] ?? 'Unknown error'),
            'status_code' => $response['status_code'],
            'error' => $response['error'] ?? null,
        ];
    }

    public function getErrorCodes(): array
    {
        $response = $this->makeRequest('GET', $this->getEndpoint('insights') . '/error-codes');

        if ($response['successful']) {
            $data = $response['data'];
            return [
                'successful' => true,
                'error_codes' => $data['error_codes'] ?? [],
                'status_code' => $response['status_code'],
                'data' => $data,
            ];
        }

        return [
            'successful' => false,
            'error_codes' => [],
            'message' => 'Failed to get error codes: ' . ($response['error'] ?? 'Unknown error'),
            'status_code' => $response['status_code'],
            'error' => $response['error'] ?? null,
        ];
    }
}
