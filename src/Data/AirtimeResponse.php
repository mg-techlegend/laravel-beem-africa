<?php

namespace TechLegend\LaravelBeemAfrica\Data;

readonly class AirtimeResponse
{
    public function __construct(
        public bool $successful,
        public ?string $transactionId,
        public string $message,
        public int $statusCode,
        public array $data,
    ) {}

    public static function fromApiPayload(array $decoded, int $statusCode): self
    {
        return new self(
            successful: (bool) ($decoded['successful'] ?? ($statusCode >= 200 && $statusCode < 300)),
            transactionId: isset($decoded['transaction_id']) ? (string) $decoded['transaction_id'] : null,
            message: (string) ($decoded['message'] ?? ''),
            statusCode: $statusCode,
            data: $decoded,
        );
    }
}
