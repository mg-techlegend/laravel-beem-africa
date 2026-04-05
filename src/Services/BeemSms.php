<?php

namespace TechLegend\LaravelBeemAfrica\Services;

use InvalidArgumentException;
use TechLegend\LaravelBeemAfrica\BeemAfricaClient;
use TechLegend\LaravelBeemAfrica\BeemAfricaMessage;
use TechLegend\LaravelBeemAfrica\Data\SendSmsResponse;

final class BeemSms
{
    public function __construct(
        private readonly BeemAfricaClient $client,
    ) {}

    public function sendMessage(BeemAfricaMessage $message): SendSmsResponse
    {
        $message->assertComplete();

        $normalizer = $this->client->getNormalizer();
        $recipients = [];
        foreach ($message->getRecipients() as $recipient) {
            $recipients[] = [
                'recipient_id' => $recipient['recipient_id'],
                'dest_addr' => $normalizer->normalize($recipient['dest_addr']),
            ];
        }

        $payload = [
            'source_addr' => $message->resolvedSender($this->client->getDefaultSenderName() ?? 'INFO'),
            'encoding' => $message->resolvedEncoding((int) $this->client->getDefault('sms', 'encoding', 0)),
            'message' => $message->getContent(),
            'recipients' => $recipients,
        ];

        $response = $this->client->post('/send', $payload);

        return $this->client->handleJsonResponse($response, [SendSmsResponse::class, 'fromApiPayload']);
    }

    /**
     * Quick send to a single recipient.
     */
    public function send(string $phone, string $content, ?string $sender = null): SendSmsResponse
    {
        $message = BeemAfricaMessage::make()
            ->to($phone)
            ->content($content);

        if ($sender !== null) {
            $message = $message->sender($sender);
        }

        return $this->sendMessage($message);
    }

    /**
     * Send the same message to multiple recipients.
     *
     * @param  array<int, string>  $phones
     */
    public function sendBulk(array $phones, string $content, ?string $sender = null): SendSmsResponse
    {
        if ($phones === []) {
            throw new InvalidArgumentException('[Beem Africa] Bulk SMS requires at least one phone number.');
        }
        if (trim($content) === '') {
            throw new InvalidArgumentException('[Beem Africa] SMS message content cannot be empty.');
        }

        $message = BeemAfricaMessage::make()
            ->recipients($phones)
            ->content($content);

        if ($sender !== null) {
            $message = $message->sender($sender);
        }

        return $this->sendMessage($message);
    }
}
