<?php

/**
 * Laravel Beem Africa Package - Usage Examples
 * 
 * This file contains comprehensive examples of how to use all the features
 * of the Laravel Beem Africa package.
 */

// 1. SMS Examples
// ===============

// Using the facade for quick SMS sending
use TechLegend\LaravelBeemAfrica\BeemFacade;

// Simple SMS send
$result = BeemFacade::sendSms('255700000001', 'Hello from Beem!', 'MyApp');

// Using the SMS service directly
$smsService = BeemFacade::sms();
$message = new \TechLegend\LaravelBeemAfrica\SMS\BeemMessage('Your order has been shipped!')
    ->sender('MyStore')
    ->encoding(0); // GSM7 encoding

$recipients = [
    ['recipient_id' => 0, 'dest_addr' => '255700000001'],
    ['recipient_id' => 1, 'dest_addr' => '255700000002'],
];

$result = $smsService->sendMessage($message, $recipients);

// 2. OTP Examples
// ===============

// Generate OTP
$otpResult = BeemFacade::generateOtp('255700000001', [
    'length' => 6,
    'expiry' => 300, // 5 minutes
    'type' => 'numeric',
    'message' => 'Your verification code is: {code}',
]);

// Verify OTP
$verifyResult = BeemFacade::verifyOtp('255700000001', '123456', $otpResult['request_id'] ?? null);

// Resend OTP
$resendResult = BeemFacade::otp()->resendOtp('255700000001', $otpResult['request_id']);

// 3. Airtime Examples
// ===================

// Send airtime
$airtimeResult = BeemFacade::sendAirtime('255700000001', 1000.00, [
    'currency' => 'TZS',
    'message' => 'Airtime sent successfully',
]);

// Get airtime balance
$balanceResult = BeemFacade::airtime()->getAirtimeBalance();

// Get transaction history
$historyResult = BeemFacade::airtime()->getTransactionHistory([
    'start_date' => '2024-01-01',
    'end_date' => '2024-12-31',
    'phone_number' => '255700000001',
]);

// Get transaction status
$statusResult = BeemFacade::airtime()->getTransactionStatus($airtimeResult['transaction_id']);

// 4. USSD Examples
// ================

// Create USSD menu
$menuData = [
    'menu_name' => 'My Service Menu',
    'menu_items' => [
        [
            'id' => 1,
            'text' => 'Check Balance',
            'action' => 'check_balance',
        ],
        [
            'id' => 2,
            'text' => 'Send Money',
            'action' => 'send_money',
        ],
    ],
    'welcome_message' => 'Welcome to My Service',
    'goodbye_message' => 'Thank you for using our service',
];

$menuResult = BeemFacade::ussd()->createMenu($menuData);

// Get menu details
$menuDetails = BeemFacade::ussd()->getMenu($menuResult['menu_id']);

// List all menus
$menus = BeemFacade::ussd()->listMenus(['page' => 1, 'limit' => 10]);

// Update menu
$updateResult = BeemFacade::ussd()->updateMenu($menuResult['menu_id'], [
    'welcome_message' => 'Updated welcome message',
]);

// Delete menu
$deleteResult = BeemFacade::ussd()->deleteMenu($menuResult['menu_id']);

// 5. Voice Examples
// =================

// Make voice call
$callResult = BeemFacade::makeCall('255700000001', 'Hello, this is a voice message', [
    'language' => 'en',
    'voice_id' => 1,
    'repeat_count' => 2,
]);

// Get call status
$callStatus = BeemFacade::voice()->getCallStatus($callResult['call_id']);

// Get call history
$callHistory = BeemFacade::voice()->getCallHistory([
    'start_date' => '2024-01-01',
    'end_date' => '2024-12-31',
    'phone_number' => '255700000001',
    'status' => 'completed',
]);

// Cancel call
$cancelResult = BeemFacade::voice()->cancelCall($callResult['call_id']);

// Get available voices
$voices = BeemFacade::voice()->getAvailableVoices();

// 6. Insights Examples
// ====================

// Get account balance
$accountBalance = BeemFacade::getBalance();

// Get message delivery report
$deliveryReport = BeemFacade::insights()->getMessageDeliveryReport($result['request_id']);

// Get message statistics
$statistics = BeemFacade::insights()->getMessageStatistics([
    'start_date' => '2024-01-01',
    'end_date' => '2024-12-31',
    'sender_name' => 'MyApp',
]);

// Get message history
$messageHistory = BeemFacade::insights()->getMessageHistory([
    'start_date' => '2024-01-01',
    'end_date' => '2024-12-31',
    'phone_number' => '255700000001',
    'status' => 'delivered',
    'page' => 1,
    'limit' => 20,
]);

// Get failed messages
$failedMessages = BeemFacade::insights()->getFailedMessages([
    'start_date' => '2024-01-01',
    'end_date' => '2024-12-31',
    'error_code' => 'INVALID_NUMBER',
]);

// Get error codes
$errorCodes = BeemFacade::insights()->getErrorCodes();

// 7. Webhook Examples
// ===================

// In your webhook controller
use Illuminate\Http\Request;

public function handleWebhook(Request $request)
{
    $result = BeemFacade::webhooks()->handle($request);
    
    if ($result['successful']) {
        $eventType = $result['event_type'];
        $data = $result['data'];
        
        switch ($eventType) {
            case 'delivery_report':
                $processed = BeemFacade::webhooks()->processDeliveryReport($data);
                break;
            case 'otp_verification':
                $processed = BeemFacade::webhooks()->processOtpVerification($data);
                break;
            case 'airtime_transaction':
                $processed = BeemFacade::webhooks()->processAirtimeTransaction($data);
                break;
        }
        
        return response()->json(['status' => 'success'], 200);
    }
    
    return response()->json(['status' => 'error'], 400);
}

// 8. Dependency Injection Examples
// =================================

// In your controller or service
use TechLegend\LaravelBeemAfrica\Beem;

class NotificationController
{
    public function __construct(private Beem $beem)
    {
    }
    
    public function sendNotification()
    {
        // Send SMS
        $smsResult = $this->beem->sendSms('255700000001', 'Hello!');
        
        // Generate OTP
        $otpResult = $this->beem->generateOtp('255700000001');
        
        // Send airtime
        $airtimeResult = $this->beem->sendAirtime('255700000001', 500.00);
        
        // Make voice call
        $callResult = $this->beem->makeCall('255700000001', 'Important message');
        
        return response()->json([
            'sms' => $smsResult,
            'otp' => $otpResult,
            'airtime' => $airtimeResult,
            'call' => $callResult,
        ]);
    }
}

// 9. Laravel Notification Examples
// ================================

// Create a notification class
use Illuminate\Notifications\Notification;
use TechLegend\LaravelBeemAfrica\SMS\BeemMessage;

class OrderShippedNotification extends Notification
{
    public function via($notifiable): array
    {
        return ['beem'];
    }

    public function toBeem($notifiable): BeemMessage
    {
        return BeemMessage::create('Your order has been shipped!')
            ->sender('MyStore')
            ->encoding(0);
    }
}

// Use the notification
$user->notify(new OrderShippedNotification());

// 10. Error Handling Examples
// ===========================

try {
    $result = BeemFacade::sendSms('255700000001', 'Test message');
    
    if (!$result['successful']) {
        // Handle error
        Log::error('SMS sending failed', [
            'error' => $result['message'],
            'status_code' => $result['status_code'],
        ]);
        
        // You can also check for specific error types
        if (isset($result['error'])) {
            // Handle specific error
        }
    }
} catch (\Exception $e) {
    Log::error('Beem service exception', [
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
    ]);
}
