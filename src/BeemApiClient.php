<?php

namespace TechLegend\LaravelBeemAfrica;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

abstract class BeemApiClient
{
    protected Client $client;
    protected string $apiKey;
    protected string $secretKey;
    protected array $config;
    protected array $endpoints;

    public function __construct(array $config, ?Client $client = null)
    {
        $this->config = $config;
        $this->apiKey = $config['api_key'];
        $this->secretKey = $config['secret_key'];
        $this->endpoints = $config['endpoints'] ?? [];
        $this->client = $client ?? new Client([
            'timeout' => 30,
            'verify' => true,
        ]);
    }

    protected function makeRequest(string $method, string $endpoint, array $payload = []): array
    {
        try {
            $response = $this->client->request($method, $endpoint, [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode("{$this->apiKey}:{$this->secretKey}"),
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => $payload,
            ]);

            $responseBody = $response->getBody()->getContents();
            $data = json_decode($responseBody, true);

            return [
                'successful' => $response->getStatusCode() >= 200 && $response->getStatusCode() < 300,
                'status_code' => $response->getStatusCode(),
                'data' => $data,
                'raw_response' => $responseBody,
            ];
        } catch (GuzzleException $e) {
            Log::error('Beem API Request Failed', [
                'endpoint' => $endpoint,
                'method' => $method,
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'payload' => $payload,
            ]);

            return [
                'successful' => false,
                'status_code' => $e->getCode() ?: 500,
                'error' => $e->getMessage(),
                'exception' => [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ],
            ];
        }
    }

    protected function getEndpoint(string $service): string
    {
        return $this->endpoints[$service] ?? '';
    }

    protected function getDefault(string $service, string $key, $default = null)
    {
        return $this->config['defaults'][$service][$key] ?? $default;
    }
}
