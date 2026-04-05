<?php

namespace TechLegend\LaravelBeemAfrica;

use InvalidArgumentException;

final class BeemAfricaMessage
{
    private function __construct(
        private readonly ?string $content,
        private readonly ?string $sender,
        private readonly ?int $encoding,
        private readonly array $recipients,
    ) {}

    public static function make(): self
    {
        return new self(null, null, null, []);
    }

    public function content(string $content): self
    {
        return new self($content, $this->sender, $this->encoding, $this->recipients);
    }

    public function sender(?string $sender): self
    {
        return new self($this->content, $sender, $this->encoding, $this->recipients);
    }

    public function encoding(int $encoding): self
    {
        return new self($this->content, $this->sender, $encoding, $this->recipients);
    }

    public function to(string $phone): self
    {
        return new self($this->content, $this->sender, $this->encoding, [
            ['recipient_id' => 0, 'dest_addr' => $phone],
        ]);
    }

    public function recipients(array $phones): self
    {
        $recipients = [];
        foreach (array_values($phones) as $index => $phone) {
            $recipients[] = [
                'recipient_id' => $index,
                'dest_addr' => (string) $phone,
            ];
        }

        return new self($this->content, $this->sender, $this->encoding, $recipients);
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function getSender(): ?string
    {
        return $this->sender;
    }

    public function getEncoding(): ?int
    {
        return $this->encoding;
    }

    public function getRecipients(): array
    {
        return $this->recipients;
    }

    public function resolvedSender(string $fallback): string
    {
        return $this->sender !== null && trim($this->sender) !== ''
            ? $this->sender
            : $fallback;
    }

    public function resolvedEncoding(int $fallback): int
    {
        return $this->encoding ?? $fallback;
    }

    public function assertComplete(): void
    {
        if ($this->content === null || trim($this->content) === '') {
            throw new InvalidArgumentException('[Beem Africa] Message content is required.');
        }

        if ($this->recipients === []) {
            throw new InvalidArgumentException('[Beem Africa] At least one recipient is required.');
        }
    }
}
