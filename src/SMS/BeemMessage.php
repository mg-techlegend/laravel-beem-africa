<?php

namespace TechLegend\LaravelBeemAfrica\SMS;

class BeemMessage
{
    public string $content = '';
    public string $sender = '';
    public string $apiKey = '';
    public string $secretKey = '';

    /**
     * Static constructor for fluent chaining.
     */
    public static function create(string $content = ''): self
    {
        return new self($content);
    }

    public function __construct(string $content = '')
    {
        $this->content = $content;
    }

    public function content(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function sender(string $sender): self
    {
        $this->sender = $sender;
        return $this;
    }

    public function apiKey(string $apiKey): self
    {
        $this->apiKey = $apiKey;
        return $this;
    }

    public function secretKey(string $secretKey): self
    {
        $this->secretKey = $secretKey;
        return $this;
    }
}
