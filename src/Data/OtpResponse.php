<?php

namespace TechLegend\LaravelBeemAfrica\Data;

readonly class OtpResponse
{
    public function __construct(
        public bool $successful,
        public ?string $requestId,
        public string $message,
        public ?bool $valid,
        public int $statusCode,
        public array $data,
    ) {}

    public static function fromApiPayload(array $decoded, int $statusCode): self
    {
        return new self(
            successful: (bool) ($decoded['successful'] ?? ($statusCode >= 200 && $statusCode < 300)),
            requestId: isset($decoded['request_id']) ? (string) $decoded['request_id'] : null,
            message: (string) ($decoded['message'] ?? ''),
            valid: isset($decoded['valid']) ? (bool) $decoded['valid'] : null,
            statusCode: $statusCode,
            data: $decoded,
        );
    }
}
