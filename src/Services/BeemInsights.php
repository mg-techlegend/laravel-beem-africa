<?php

namespace TechLegend\LaravelBeemAfrica\Services;

use InvalidArgumentException;
use TechLegend\LaravelBeemAfrica\BeemAfricaClient;
use TechLegend\LaravelBeemAfrica\Data\BalanceResponse;
use TechLegend\LaravelBeemAfrica\Data\InsightsResponse;

final class BeemInsights
{
    public function __construct(
        private readonly BeemAfricaClient $client,
    ) {}

    public function getDeliveryReport(string $requestId): InsightsResponse
    {
        if (trim($requestId) === '') {
            throw new InvalidArgumentException('[Beem Africa] Request ID is required for delivery report.');
        }

        $response = $this->client->get('/insights/delivery-report/' . rawurlencode($requestId));

        return $this->client->handleJsonResponse($response, [InsightsResponse::class, 'fromApiPayload']);
    }

    public function getMessageStatistics(array $filters = []): InsightsResponse
    {
        $query = array_filter([
            'start_date' => $filters['start_date'] ?? null,
            'end_date' => $filters['end_date'] ?? null,
            'sender_name' => $filters['sender_name'] ?? null,
        ], fn ($v) => $v !== null);

        $response = $this->client->get('/insights/statistics', $query);

        return $this->client->handleJsonResponse($response, [InsightsResponse::class, 'fromApiPayload']);
    }

    public function getAccountBalance(): BalanceResponse
    {
        $response = $this->client->get('/insights/balance');

        return $this->client->handleJsonResponse($response, [BalanceResponse::class, 'fromApiPayload']);
    }

    public function getMessageHistory(array $filters = []): InsightsResponse
    {
        $query = array_filter([
            'start_date' => $filters['start_date'] ?? null,
            'end_date' => $filters['end_date'] ?? null,
            'phone_number' => $filters['phone_number'] ?? null,
            'status' => $filters['status'] ?? null,
            'page' => $filters['page'] ?? null,
            'limit' => $filters['limit'] ?? null,
        ], fn ($v) => $v !== null);

        $response = $this->client->get('/insights/messages', $query);

        return $this->client->handleJsonResponse($response, [InsightsResponse::class, 'fromApiPayload']);
    }

    public function getFailedMessages(array $filters = []): InsightsResponse
    {
        $query = array_filter([
            'start_date' => $filters['start_date'] ?? null,
            'end_date' => $filters['end_date'] ?? null,
            'error_code' => $filters['error_code'] ?? null,
        ], fn ($v) => $v !== null);

        $response = $this->client->get('/insights/failed-messages', $query);

        return $this->client->handleJsonResponse($response, [InsightsResponse::class, 'fromApiPayload']);
    }

    public function getErrorCodes(): InsightsResponse
    {
        $response = $this->client->get('/insights/error-codes');

        return $this->client->handleJsonResponse($response, [InsightsResponse::class, 'fromApiPayload']);
    }
}
