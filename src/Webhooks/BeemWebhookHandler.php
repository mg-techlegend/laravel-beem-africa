<?php

namespace TechLegend\LaravelBeemAfrica\Webhooks;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BeemWebhookHandler
{
    protected string $secret;

    public function __construct(string $secret)
    {
        $this->secret = $secret;
    }

    public function handle(Request $request): array
    {
        try {
            // Verify webhook signature if secret is provided
            if (!empty($this->secret)) {
                if (!$this->verifySignature($request)) {
                    return [
                        'successful' => false,
                        'message' => 'Invalid webhook signature',
                        'status_code' => 401,
                    ];
                }
            }

            $payload = $request->all();
            $eventType = $payload['event_type'] ?? 'unknown';

            Log::info('Beem Webhook Received', [
                'event_type' => $eventType,
                'payload' => $payload,
            ]);

            return [
                'successful' => true,
                'event_type' => $eventType,
                'data' => $payload,
                'message' => 'Webhook processed successfully',
                'status_code' => 200,
            ];
        } catch (\Exception $e) {
            Log::error('Beem Webhook Processing Failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'successful' => false,
                'message' => 'Webhook processing failed: ' . $e->getMessage(),
                'status_code' => 500,
                'error' => $e->getMessage(),
            ];
        }
    }

    protected function verifySignature(Request $request): bool
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
        $requestId = $payload['request_id'] ?? null;
        $status = $payload['status'] ?? 'unknown';
        $phoneNumber = $payload['phone_number'] ?? null;
        $message = $payload['message'] ?? '';

        Log::info('Beem Delivery Report', [
            'request_id' => $requestId,
            'status' => $status,
            'phone_number' => $phoneNumber,
            'message' => $message,
        ]);

        return [
            'successful' => true,
            'request_id' => $requestId,
            'status' => $status,
            'phone_number' => $phoneNumber,
            'message' => $message,
        ];
    }

    public function processOtpVerification(array $payload): array
    {
        $phoneNumber = $payload['phone_number'] ?? null;
        $verified = $payload['verified'] ?? false;
        $requestId = $payload['request_id'] ?? null;

        Log::info('Beem OTP Verification', [
            'phone_number' => $phoneNumber,
            'verified' => $verified,
            'request_id' => $requestId,
        ]);

        return [
            'successful' => true,
            'phone_number' => $phoneNumber,
            'verified' => $verified,
            'request_id' => $requestId,
        ];
    }

    public function processAirtimeTransaction(array $payload): array
    {
        $transactionId = $payload['transaction_id'] ?? null;
        $status = $payload['status'] ?? 'unknown';
        $phoneNumber = $payload['phone_number'] ?? null;
        $amount = $payload['amount'] ?? 0;

        Log::info('Beem Airtime Transaction', [
            'transaction_id' => $transactionId,
            'status' => $status,
            'phone_number' => $phoneNumber,
            'amount' => $amount,
        ]);

        return [
            'successful' => true,
            'transaction_id' => $transactionId,
            'status' => $status,
            'phone_number' => $phoneNumber,
            'amount' => $amount,
        ];
    }
}
