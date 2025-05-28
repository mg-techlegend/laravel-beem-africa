<?php

namespace TechLegend\LaravelBeemAfrica\SMS;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Exception\GuzzleException;

class Beem
{
    protected string $apiKey;

    protected string $secretKey;

    protected string $senderName;

    protected string $smsApiUrl = 'https://apisms.beem.africa/v1/send';

    public function __construct(array $config)
    {
        $this->apiKey = $config['api_key'];
        $this->secretKey = $config['secret_key'];
        $this->senderName = $config['sender_name'] ?? 'INFO';
    }

    public function sendMessage(BeemMessage $message, array $recipients): array
    {
        $client = new Client;

        $payload = [
            'source_addr' => $message->sender ?: $this->senderName,
            'encoding' => 0,
            'message' => $message->content,
            'recipients' => $recipients,
        ];

        try {
            $response = $client->post($this->smsApiUrl, [
                'verify' => false,
                'headers' => [
                    'Authorization' => 'Basic '.base64_encode($this->apiKey.':'.$this->secretKey),
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => $payload,
            ]);

            $responseBody = $response->getBody()->getContents();
            $data = json_decode($responseBody, true);

            return [
                'successful' => $data['successful'] ?? false,
                'request_id' => $data['request_id'] ?? null,
                'message' => $data['message'] ?? 'No message returned from Beem.',
                'valid' => $data['valid'] ?? 0,
                'invalid' => $data['invalid'] ?? 0,
                'duplicates' => $data['duplicates'] ?? 0,
                'status_code' => $response->getStatusCode(),
                'raw_response' => $data,
            ];
        } catch (GuzzleException $e) {
            // Log the error with full trace for debugging
            Log::error('Beem SMS Request Failed', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'trace' => $e->getTraceAsString(),
                'payload' => $payload,
            ]);

            return [
                'successful' => false,
                'request_id' => null,
                'message' => 'Beem SMS request failed: '.$e->getMessage(),
                'valid' => 0,
                'invalid' => 0,
                'duplicates' => 0,
                'status_code' => $e->getCode() ?: 500,
                'exception' => [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ],
            ];
        }
    }
}
