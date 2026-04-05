<?php

namespace TechLegend\LaravelBeemAfrica\Data;

readonly class UssdResponse
{
    public function __construct(
        public bool $successful,
        public ?string $menuId,
        public string $message,
        public int $statusCode,
        public array $data,
    ) {}

    public static function fromApiPayload(array $decoded, int $statusCode): self
    {
        return new self(
            successful: (bool) ($decoded['successful'] ?? ($statusCode >= 200 && $statusCode < 300)),
            menuId: isset($decoded['menu_id']) ? (string) $decoded['menu_id'] : null,
            message: (string) ($decoded['message'] ?? ''),
            statusCode: $statusCode,
            data: $decoded,
        );
    }
}
