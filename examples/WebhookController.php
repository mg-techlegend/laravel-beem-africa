<?php

/**
 * Example Webhook Controller for Beem Africa
 * 
 * This is an example controller that shows how to handle incoming webhooks
 * from Beem Africa in your Laravel application.
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use TechLegend\LaravelBeemAfrica\BeemFacade;

class BeemWebhookController extends Controller
{
    /**
     * Handle incoming webhooks from Beem Africa
     */
    public function handle(Request $request): JsonResponse
    {
        try {
            // Process the webhook using the Beem webhook handler
            $result = BeemFacade::webhooks()->handle($request);
            
            if (!$result['successful']) {
                Log::error('Beem webhook processing failed', [
                    'error' => $result['message'],
                    'status_code' => $result['status_code'],
                ]);
                
                return response()->json([
                    'status' => 'error',
                    'message' => $result['message'],
                ], $result['status_code']);
            }
            
            $eventType = $result['event_type'];
            $data = $result['data'];
            
            // Handle different types of webhooks
            switch ($eventType) {
                case 'delivery_report':
                    $this->handleDeliveryReport($data);
                    break;
                    
                case 'otp_verification':
                    $this->handleOtpVerification($data);
                    break;
                    
                case 'airtime_transaction':
                    $this->handleAirtimeTransaction($data);
                    break;
                    
                case 'voice_call_status':
                    $this->handleVoiceCallStatus($data);
                    break;
                    
                default:
                    Log::info('Unhandled webhook event type', [
                        'event_type' => $eventType,
                        'data' => $data,
                    ]);
                    break;
            }
            
            return response()->json([
                'status' => 'success',
                'message' => 'Webhook processed successfully',
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('Beem webhook exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Internal server error',
            ], 500);
        }
    }
    
    /**
     * Handle SMS delivery reports
     */
    protected function handleDeliveryReport(array $data): void
    {
        $requestId = $data['request_id'] ?? null;
        $status = $data['status'] ?? 'unknown';
        $phoneNumber = $data['phone_number'] ?? null;
        $message = $data['message'] ?? '';
        
        Log::info('SMS Delivery Report', [
            'request_id' => $requestId,
            'status' => $status,
            'phone_number' => $phoneNumber,
            'message' => $message,
        ]);
        
        // Update your database or trigger other actions based on delivery status
        if ($status === 'delivered') {
            // SMS was delivered successfully
            $this->updateSmsStatus($requestId, 'delivered');
        } elseif ($status === 'failed') {
            // SMS delivery failed
            $this->updateSmsStatus($requestId, 'failed');
            $this->handleFailedDelivery($requestId, $phoneNumber, $message);
        }
    }
    
    /**
     * Handle OTP verification results
     */
    protected function handleOtpVerification(array $data): void
    {
        $phoneNumber = $data['phone_number'] ?? null;
        $verified = $data['verified'] ?? false;
        $requestId = $data['request_id'] ?? null;
        
        Log::info('OTP Verification Result', [
            'phone_number' => $phoneNumber,
            'verified' => $verified,
            'request_id' => $requestId,
        ]);
        
        if ($verified) {
            // OTP was verified successfully
            $this->markOtpAsVerified($requestId, $phoneNumber);
        } else {
            // OTP verification failed
            $this->handleOtpFailure($requestId, $phoneNumber);
        }
    }
    
    /**
     * Handle airtime transaction updates
     */
    protected function handleAirtimeTransaction(array $data): void
    {
        $transactionId = $data['transaction_id'] ?? null;
        $status = $data['status'] ?? 'unknown';
        $phoneNumber = $data['phone_number'] ?? null;
        $amount = $data['amount'] ?? 0;
        
        Log::info('Airtime Transaction Update', [
            'transaction_id' => $transactionId,
            'status' => $status,
            'phone_number' => $phoneNumber,
            'amount' => $amount,
        ]);
        
        if ($status === 'completed') {
            // Airtime transaction completed successfully
            $this->updateAirtimeTransaction($transactionId, 'completed');
        } elseif ($status === 'failed') {
            // Airtime transaction failed
            $this->updateAirtimeTransaction($transactionId, 'failed');
            $this->handleAirtimeFailure($transactionId, $phoneNumber, $amount);
        }
    }
    
    /**
     * Handle voice call status updates
     */
    protected function handleVoiceCallStatus(array $data): void
    {
        $callId = $data['call_id'] ?? null;
        $status = $data['status'] ?? 'unknown';
        $duration = $data['duration'] ?? 0;
        $phoneNumber = $data['phone_number'] ?? null;
        
        Log::info('Voice Call Status Update', [
            'call_id' => $callId,
            'status' => $status,
            'duration' => $duration,
            'phone_number' => $phoneNumber,
        ]);
        
        if ($status === 'completed') {
            // Voice call completed successfully
            $this->updateVoiceCallStatus($callId, 'completed', $duration);
        } elseif ($status === 'failed') {
            // Voice call failed
            $this->updateVoiceCallStatus($callId, 'failed');
            $this->handleVoiceCallFailure($callId, $phoneNumber);
        }
    }
    
    /**
     * Update SMS status in your database
     */
    protected function updateSmsStatus(?string $requestId, string $status): void
    {
        // Implement your database update logic here
        // Example:
        // if ($requestId) {
        //     SmsLog::where('request_id', $requestId)->update(['status' => $status]);
        // }
    }
    
    /**
     * Handle failed SMS delivery
     */
    protected function handleFailedDelivery(?string $requestId, ?string $phoneNumber, string $message): void
    {
        // Implement your failed delivery handling logic here
        // Example: Retry sending, notify admin, etc.
    }
    
    /**
     * Mark OTP as verified in your database
     */
    protected function markOtpAsVerified(?string $requestId, ?string $phoneNumber): void
    {
        // Implement your OTP verification logic here
        // Example:
        // if ($requestId) {
        //     OtpLog::where('request_id', $requestId)->update(['verified' => true]);
        // }
    }
    
    /**
     * Handle OTP verification failure
     */
    protected function handleOtpFailure(?string $requestId, ?string $phoneNumber): void
    {
        // Implement your OTP failure handling logic here
        // Example: Increment failed attempts, block user, etc.
    }
    
    /**
     * Update airtime transaction status
     */
    protected function updateAirtimeTransaction(?string $transactionId, string $status): void
    {
        // Implement your airtime transaction update logic here
        // Example:
        // if ($transactionId) {
        //     AirtimeTransaction::where('transaction_id', $transactionId)->update(['status' => $status]);
        // }
    }
    
    /**
     * Handle airtime transaction failure
     */
    protected function handleAirtimeFailure(?string $transactionId, ?string $phoneNumber, float $amount): void
    {
        // Implement your airtime failure handling logic here
        // Example: Refund user, notify admin, etc.
    }
    
    /**
     * Update voice call status
     */
    protected function updateVoiceCallStatus(?string $callId, string $status, int $duration = 0): void
    {
        // Implement your voice call status update logic here
        // Example:
        // if ($callId) {
        //     VoiceCall::where('call_id', $callId)->update([
        //         'status' => $status,
        //         'duration' => $duration,
        //     ]);
        // }
    }
    
    /**
     * Handle voice call failure
     */
    protected function handleVoiceCallFailure(?string $callId, ?string $phoneNumber): void
    {
        // Implement your voice call failure handling logic here
        // Example: Retry call, notify user, etc.
    }
}
