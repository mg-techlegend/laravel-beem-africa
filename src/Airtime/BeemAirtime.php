<?php

namespace TechLegend\LaravelBeemAfrica\Airtime;

use TechLegend\LaravelBeemAfrica\BeemApiClient;

class BeemAirtime extends BeemApiClient
{
    public function sendAirtime(string $phoneNumber, float $amount, array $options = []): array
    {
        $payload = [
            'phone_number' => $phoneNumber,
            'amount' => $amount,
            'currency' => $options['currency'] ?? $this->getDefault('airtime', 'currency', 'TZS'),
            'message' => $options['message'] ?? 'Airtime sent successfully',
        ];

        $response = $this->makeRequest('POST', $this->getEndpoint('airtime') . '/send', $payload);

        if ($response['successful']) {
            $data = $response['data'];
            return [
                'successful' => true,
                'transaction_id' => $data['transaction_id'] ?? null,
                'message' => $data['message'] ?? 'Airtime sent successfully',
                'status_code' => $response['status_code'],
                'data' => $data,
            ];
        }

        return [
            'successful' => false,
            'transaction_id' => null,
            'message' => 'Airtime send failed: ' . ($response['error'] ?? 'Unknown error'),
            'status_code' => $response['status_code'],
            'error' => $response['error'] ?? null,
        ];
    }

    public function getAirtimeBalance(): array
    {
        $response = $this->makeRequest('GET', $this->getEndpoint('airtime') . '/balance');

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
            'message' => 'Failed to get airtime balance: ' . ($response['error'] ?? 'Unknown error'),
            'status_code' => $response['status_code'],
            'error' => $response['error'] ?? null,
        ];
    }

    public function getTransactionHistory(array $filters = []): array
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

        $endpoint = $this->getEndpoint('airtime') . '/transactions';
        if (!empty($queryParams)) {
            $endpoint .= '?' . http_build_query($queryParams);
        }

        $response = $this->makeRequest('GET', $endpoint);

        if ($response['successful']) {
            $data = $response['data'];
            return [
                'successful' => true,
                'transactions' => $data['transactions'] ?? [],
                'total' => $data['total'] ?? 0,
                'status_code' => $response['status_code'],
                'data' => $data,
            ];
        }

        return [
            'successful' => false,
            'transactions' => [],
            'message' => 'Failed to get transaction history: ' . ($response['error'] ?? 'Unknown error'),
            'status_code' => $response['status_code'],
            'error' => $response['error'] ?? null,
        ];
    }

    public function getTransactionStatus(string $transactionId): array
    {
        $response = $this->makeRequest('GET', $this->getEndpoint('airtime') . '/transactions/' . $transactionId);

        if ($response['successful']) {
            $data = $response['data'];
            return [
                'successful' => true,
                'transaction_id' => $data['transaction_id'] ?? null,
                'status' => $data['status'] ?? 'unknown',
                'message' => $data['message'] ?? 'Transaction status retrieved',
                'status_code' => $response['status_code'],
                'data' => $data,
            ];
        }

        return [
            'successful' => false,
            'transaction_id' => $transactionId,
            'status' => 'unknown',
            'message' => 'Failed to get transaction status: ' . ($response['error'] ?? 'Unknown error'),
            'status_code' => $response['status_code'],
            'error' => $response['error'] ?? null,
        ];
    }
}
