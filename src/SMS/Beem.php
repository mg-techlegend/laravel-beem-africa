<?php

namespace TechLegend\LaravelBeemAfrica\SMS;

use TechLegend\LaravelBeemAfrica\BeemApiClient;

class Beem extends BeemApiClient
{
    protected string $senderName;

    public function __construct(array $config, $client = null)
    {
        parent::__construct($config, $client);
        $this->senderName = $config['sender_name'] ?? 'INFO';
    }

    public function sendMessage(BeemMessage $message, array $recipients): array
    {
        $payload = [
            'source_addr' => $message->sender ?: $this->senderName,
            'encoding' => $message->encoding ?? $this->getDefault('sms', 'encoding', 0),
            'message' => $message->content,
            'recipients' => $recipients,
        ];

        $response = $this->makeRequest('POST', $this->getEndpoint('sms'), $payload);

        if ($response['successful']) {
            $data = $response['data'];
            return [
                'successful' => $data['successful'] ?? false,
                'request_id' => $data['request_id'] ?? null,
                'message' => $data['message'] ?? 'No message returned from Beem.',
                'valid' => $data['valid'] ?? 0,
                'invalid' => $data['invalid'] ?? 0,
                'duplicates' => $data['duplicates'] ?? 0,
                'status_code' => $response['status_code'],
                'raw_response' => $data,
            ];
        }

        return [
            'successful' => false,
            'request_id' => null,
            'message' => 'Beem SMS request failed: ' . ($response['error'] ?? 'Unknown error'),
            'valid' => 0,
            'invalid' => 0,
            'duplicates' => 0,
            'status_code' => $response['status_code'],
            'error' => $response['error'] ?? null,
        ];
    }
}
