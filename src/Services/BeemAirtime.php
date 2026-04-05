<?php

namespace TechLegend\LaravelBeemAfrica\Services;

use InvalidArgumentException;
use TechLegend\LaravelBeemAfrica\BeemAfricaClient;
use TechLegend\LaravelBeemAfrica\Data\AirtimeResponse;
use TechLegend\LaravelBeemAfrica\Data\BalanceResponse;

final class BeemAirtime
{
    public function __construct(
        private readonly BeemAfricaClient $client,
    ) {}

    public function send(string $phoneNumber, float $amount, array $options = []): AirtimeResponse
    {
        if (trim($phoneNumber) === '') {
            throw new InvalidArgumentException('[Beem Africa] Phone number is required for airtime.');
        }
        if ($amount <= 0) {
            throw new InvalidArgumentException('[Beem Africa] Airtime amount must be greater than zero.');
        }

        $normalized = $this->client->getNormalizer()->normalize($phoneNumber);

        $payload = [
            'phone_number' => $normalized,
            'amount' => $amount,
            'currency' => $options['currency'] ?? $this->client->getDefault('airtime', 'currency', 'TZS'),
        ];

        if (isset($options['message'])) {
            $payload['message'] = $options['message'];
        }

        $response = $this->client->post('/airtime/send', $payload);

        return $this->client->handleJsonResponse($response, [AirtimeResponse::class, 'fromApiPayload']);
    }

    public function getBalance(): BalanceResponse
    {
        $response = $this->client->get('/airtime/balance');

        return $this->client->handleJsonResponse($response, [BalanceResponse::class, 'fromApiPayload']);
    }

    public function getTransactionHistory(array $filters = []): AirtimeResponse
    {
        $query = array_filter([
            'start_date' => $filters['start_date'] ?? null,
            'end_date' => $filters['end_date'] ?? null,
            'phone_number' => $filters['phone_number'] ?? null,
        ], fn ($v) => $v !== null);

        $response = $this->client->get('/airtime/transactions', $query);

        return $this->client->handleJsonResponse($response, [AirtimeResponse::class, 'fromApiPayload']);
    }

    public function getTransactionStatus(string $transactionId): AirtimeResponse
    {
        if (trim($transactionId) === '') {
            throw new InvalidArgumentException('[Beem Africa] Transaction ID cannot be empty.');
        }

        $response = $this->client->get('/airtime/transactions/' . rawurlencode($transactionId));

        return $this->client->handleJsonResponse($response, [AirtimeResponse::class, 'fromApiPayload']);
    }
}
