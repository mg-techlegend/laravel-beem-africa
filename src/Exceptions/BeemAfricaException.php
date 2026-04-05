<?php

namespace TechLegend\LaravelBeemAfrica\Exceptions;

use Exception;

abstract class BeemAfricaException extends Exception
{
    public function __construct(
        string $message,
        public readonly ?array $payload = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }
}
