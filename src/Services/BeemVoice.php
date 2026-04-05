<?php

namespace TechLegend\LaravelBeemAfrica\Services;

use InvalidArgumentException;
use TechLegend\LaravelBeemAfrica\BeemAfricaClient;
use TechLegend\LaravelBeemAfrica\Data\VoiceResponse;

final class BeemVoice
{
    public function __construct(
        private readonly BeemAfricaClient $client,
    ) {}

    public function makeCall(string $phoneNumber, string $message, array $options = []): VoiceResponse
    {
        if (trim($phoneNumber) === '') {
            throw new InvalidArgumentException('[Beem Africa] Phone number is required for voice call.');
        }
        if (trim($message) === '') {
            throw new InvalidArgumentException('[Beem Africa] Message is required for voice call.');
        }

        $normalized = $this->client->getNormalizer()->normalize($phoneNumber);

        $payload = [
            'phone_number' => $normalized,
            'message' => $message,
            'language' => $options['language'] ?? $this->client->getDefault('voice', 'language', 'en'),
            'voice_id' => $options['voice_id'] ?? $this->client->getDefault('voice', 'voice_id', 1),
            'repeat_count' => $options['repeat_count'] ?? 1,
        ];

        $response = $this->client->post('/voice/call', $payload);

        return $this->client->handleJsonResponse($response, [VoiceResponse::class, 'fromApiPayload']);
    }

    public function getCallStatus(string $callId): VoiceResponse
    {
        if (trim($callId) === '') {
            throw new InvalidArgumentException('[Beem Africa] Call ID cannot be empty.');
        }

        $response = $this->client->get('/voice/calls/' . rawurlencode($callId));

        return $this->client->handleJsonResponse($response, [VoiceResponse::class, 'fromApiPayload']);
    }

    public function getCallHistory(array $filters = []): VoiceResponse
    {
        $query = array_filter([
            'start_date' => $filters['start_date'] ?? null,
            'end_date' => $filters['end_date'] ?? null,
            'phone_number' => $filters['phone_number'] ?? null,
            'status' => $filters['status'] ?? null,
        ], fn ($v) => $v !== null);

        $response = $this->client->get('/voice/calls', $query);

        return $this->client->handleJsonResponse($response, [VoiceResponse::class, 'fromApiPayload']);
    }

    public function cancelCall(string $callId): VoiceResponse
    {
        if (trim($callId) === '') {
            throw new InvalidArgumentException('[Beem Africa] Call ID cannot be empty.');
        }

        $response = $this->client->delete('/voice/calls/' . rawurlencode($callId));

        return $this->client->handleJsonResponse($response, [VoiceResponse::class, 'fromApiPayload']);
    }

    public function getAvailableVoices(): VoiceResponse
    {
        $response = $this->client->get('/voice/voices');

        return $this->client->handleJsonResponse($response, [VoiceResponse::class, 'fromApiPayload']);
    }
}
