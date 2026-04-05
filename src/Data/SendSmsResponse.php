<?php

namespace TechLegend\LaravelBeemAfrica\Data;

readonly class SendSmsResponse
{
    public function __construct(
        public bool $successful,
        public ?int $requestId,
        public string $message,
        public int $valid,
        public int $invalid,
        public int $duplicates,
        public int $statusCode,
    ) {}

    public static function fromApiPayload(array $decoded, int $statusCode): self
    {
        return new self(
            successful: (bool) ($decoded['successful'] ?? false),
            requestId: isset($decoded['request_id']) ? (int) $decoded['request_id'] : null,
            message: (string) ($decoded['message'] ?? ''),
            valid: (int) ($decoded['valid'] ?? 0),
            invalid: (int) ($decoded['invalid'] ?? 0),
            duplicates: (int) ($decoded['duplicates'] ?? 0),
            statusCode: $statusCode,
        );
    }
}
