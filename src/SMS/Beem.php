<?php

namespace TechLegend\LaravelBeemAfrica\SMS;

use GuzzleHttp\Client;
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

    public function sendMessage(BeemMessage $message, array $recipients): string
    {
        $client = new Client;

        try {
            $response = $client->post($this->smsApiUrl, [
                'verify' => false,
                'auth' => [$this->apiKey, $this->secretKey],
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'source_addr' => $message->sender ?: $this->senderName,
                    'message' => $message->content,
                    'encoding' => 0,
                    'recipients' => $recipients,
                ],
            ]);
        } catch (GuzzleException $e) {
            return $e->getMessage();
        }

        return $response->getBody()->getContents();
    }
}
