<?php

namespace TechLegend\LaravelBeemAfrica\Data;

readonly class BalanceResponse
{
    public function __construct(
        public bool $successful,
        public float $balance,
        public string $currency,
        public int $statusCode,
        public array $data,
    ) {}

    public static function fromApiPayload(array $decoded, int $statusCode): self
    {
        return new self(
            successful: (bool) ($decoded['successful'] ?? ($statusCode >= 200 && $statusCode < 300)),
            balance: (float) ($decoded['balance'] ?? 0),
            currency: (string) ($decoded['currency'] ?? 'TZS'),
            statusCode: $statusCode,
            data: $decoded,
        );
    }
}
