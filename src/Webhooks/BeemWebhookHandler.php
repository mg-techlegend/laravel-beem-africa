<?php

namespace TechLegend\LaravelBeemAfrica\Webhooks;

use Illuminate\Http\Request;

final class BeemWebhookHandler
{
    public function __construct(
        private readonly string $secret,
    ) {}

    public function handle(Request $request): array
    {
        if ($this->secret !== '' && ! $this->verifySignature($request)) {
            return [
                'successful' => false,
                'message' => 'Invalid webhook signature',
                'status_code' => 401,
            ];
        }

        $payload = $request->all();

        return [
            'successful' => true,
            'event_type' => $payload['event_type'] ?? 'unknown',
            'data' => $payload,
            'message' => 'Webhook processed successfully',
            'status_code' => 200,
        ];
    }

    public function verifySignature(Request $request): bool
    {
        $signature = $request->header('X-Beem-Signature');

        if (empty($signature)) {
            return false;
        }

        $payload = $request->getContent();
        $expectedSignature = hash_hmac('sha256', $payload, $this->secret);

        return hash_equals($expectedSignature, $signature);
    }

    public function processDeliveryReport(array $payload): array
    {
        return [
            'successful' => true,
            'request_id' => $payload['request_id'] ?? null,
            'status' => $payload['status'] ?? 'unknown',
            'phone_number' => $payload['phone_number'] ?? null,
            'message' => $payload['message'] ?? '',
        ];
    }

    public function processOtpVerification(array $payload): array
    {
        return [
            'successful' => true,
            'phone_number' => $payload['phone_number'] ?? null,
            'verified' => $payload['verified'] ?? false,
            'request_id' => $payload['request_id'] ?? null,
        ];
    }

    public function processAirtimeTransaction(array $payload): array
    {
        return [
            'successful' => true,
            'transaction_id' => $payload['transaction_id'] ?? null,
            'status' => $payload['status'] ?? 'unknown',
            'phone_number' => $payload['phone_number'] ?? null,
            'amount' => $payload['amount'] ?? 0,
        ];
    }
}
