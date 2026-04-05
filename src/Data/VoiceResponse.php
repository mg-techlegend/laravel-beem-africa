<?php

namespace TechLegend\LaravelBeemAfrica\Data;

readonly class VoiceResponse
{
    public function __construct(
        public bool $successful,
        public ?string $callId,
        public string $message,
        public ?string $status,
        public ?int $duration,
        public int $statusCode,
        public array $data,
    ) {}

    public static function fromApiPayload(array $decoded, int $statusCode): self
    {
        return new self(
            successful: (bool) ($decoded['successful'] ?? ($statusCode >= 200 && $statusCode < 300)),
            callId: isset($decoded['call_id']) ? (string) $decoded['call_id'] : null,
            message: (string) ($decoded['message'] ?? ''),
            status: isset($decoded['status']) ? (string) $decoded['status'] : null,
            duration: isset($decoded['duration']) ? (int) $decoded['duration'] : null,
            statusCode: $statusCode,
            data: $decoded,
        );
    }
}
