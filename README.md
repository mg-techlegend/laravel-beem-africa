# Laravel Beem Africa

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mg-techlegend/laravel-beem-africa.svg?style=flat-square)](https://packagist.org/packages/mg-techlegend/laravel-beem-africa)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/mg-techlegend/laravel-beem-africa/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/mg-techlegend/laravel-beem-africa/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/mg-techlegend/laravel-beem-africa/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/mg-techlegend/laravel-beem-africa/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/mg-techlegend/laravel-beem-africa.svg?style=flat-square)](https://packagist.org/packages/mg-techlegend/laravel-beem-africa)

**A comprehensive Laravel package to integrate with Beem Africa's API services.**

This package provides a simple, elegant way to use [Beem Africa](https://beem.africa)'s API services in your Laravel applications. It integrates seamlessly with Laravel's native notification system and provides easy access to all Beem services including SMS, OTP, Airtime, USSD, Voice, and Insights.

---

## 📦 Installation

You can install the package via Composer:

```bash
composer require mg-techlegend/laravel-beem-africa
```

Then, publish the config file:

```bash
php artisan vendor:publish --tag="laravel-beem-africa-config"
```

Update your `.env` file with your Beem credentials:

```env
BEEM_API_KEY=your-api-key
BEEM_SECRET_KEY=your-secret
BEEM_SENDER_NAME=your-sender-name

# Optional: Custom endpoints (if needed)
BEEM_SMS_ENDPOINT=https://apisms.beem.africa/v1/send
BEEM_OTP_ENDPOINT=https://apisms.beem.africa/v1/otp
BEEM_AIRTIME_ENDPOINT=https://apisms.beem.africa/v1/airtime
BEEM_USSD_ENDPOINT=https://apisms.beem.africa/v1/ussd
BEEM_VOICE_ENDPOINT=https://apisms.beem.africa/v1/voice
BEEM_INSIGHTS_ENDPOINT=https://apisms.beem.africa/v1/insights

# Webhook settings (optional)
BEEM_WEBHOOKS_ENABLED=true
BEEM_WEBHOOK_SECRET=your-webhook-secret
BEEM_WEBHOOK_ENDPOINT=https://your-app.com/webhooks/beem
```

---

## 🛠 Configuration

The config file `config/beem.php` contains comprehensive settings:

```php
return [
    'api_key' => env('BEEM_API_KEY'),
    'secret_key' => env('BEEM_SECRET_KEY'),
    'sender_name' => env('BEEM_SENDER_NAME', 'INFO'),
    
    // API Endpoints
    'endpoints' => [
        'sms' => env('BEEM_SMS_ENDPOINT', 'https://apisms.beem.africa/v1/send'),
        'otp' => env('BEEM_OTP_ENDPOINT', 'https://apisms.beem.africa/v1/otp'),
        'airtime' => env('BEEM_AIRTIME_ENDPOINT', 'https://apisms.beem.africa/v1/airtime'),
        'ussd' => env('BEEM_USSD_ENDPOINT', 'https://apisms.beem.africa/v1/ussd'),
        'voice' => env('BEEM_VOICE_ENDPOINT', 'https://apisms.beem.africa/v1/voice'),
        'insights' => env('BEEM_INSIGHTS_ENDPOINT', 'https://apisms.beem.africa/v1/insights'),
    ],
    
    // Default settings for different services
    'defaults' => [
        'sms' => [
            'encoding' => 0, // 0 for GSM7, 1 for UCS2
        ],
        'otp' => [
            'length' => 6,
            'expiry' => 300, // 5 minutes in seconds
            'type' => 'numeric', // numeric, alphanumeric
        ],
        'airtime' => [
            'currency' => 'TZS',
        ],
        'voice' => [
            'language' => 'en',
            'voice_id' => 1,
        ],
    ],
    
    // Webhook settings
    'webhooks' => [
        'enabled' => env('BEEM_WEBHOOKS_ENABLED', false),
        'secret' => env('BEEM_WEBHOOK_SECRET'),
        'endpoint' => env('BEEM_WEBHOOK_ENDPOINT'),
    ],
];
```

---

## 🚀 Usage

### 1. SMS Service

#### Using the Facade (Quick Methods)

```php
use TechLegend\LaravelBeemAfrica\BeemFacade;

// Simple SMS send
$result = BeemFacade::sendSms('255700000001', 'Hello from Beem!', 'MyApp');
```

#### Using the SMS Service Directly

```php
use TechLegend\LaravelBeemAfrica\SMS\BeemMessage;

$smsService = BeemFacade::sms();
$message = BeemMessage::create('Your order has been shipped!')
    ->sender('MyStore')
    ->encoding(0); // GSM7 encoding

$recipients = [
    ['recipient_id' => 0, 'dest_addr' => '255700000001'],
    ['recipient_id' => 1, 'dest_addr' => '255700000002'],
];

$result = $smsService->sendMessage($message, $recipients);
```

#### Laravel Notifications

```php
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

// Use in your model
$user->notify(new OrderShippedNotification());
```

### 2. OTP Service

```php
// Generate OTP
$otpResult = BeemFacade::generateOtp('255700000001', [
    'length' => 6,
    'expiry' => 300, // 5 minutes
    'type' => 'numeric',
    'message' => 'Your verification code is: {code}',
]);

// Verify OTP
$verifyResult = BeemFacade::verifyOtp('255700000001', '123456', $otpResult['request_id']);

// Resend OTP
$resendResult = BeemFacade::otp()->resendOtp('255700000001', $otpResult['request_id']);
```

### 3. Airtime Service

```php
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
```

### 4. USSD Service

```php
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
```

### 5. Voice Service

```php
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
```

### 6. Insights Service

```php
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
```

### 7. Webhooks

```php
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
```

### 8. Dependency Injection

```php
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
```

### 9. Error Handling

```php
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
```

---

## 📋 Available Services

| Service | Description | Methods |
|---------|-------------|---------|
| **SMS** | Send SMS messages | `sendSms()`, `sendMessage()` |
| **OTP** | Generate and verify OTP codes | `generateOtp()`, `verifyOtp()`, `resendOtp()` |
| **Airtime** | Send airtime and manage transactions | `sendAirtime()`, `getAirtimeBalance()`, `getTransactionHistory()` |
| **USSD** | Create and manage USSD menus | `createMenu()`, `getMenu()`, `updateMenu()`, `deleteMenu()` |
| **Voice** | Make voice calls | `makeCall()`, `getCallStatus()`, `getCallHistory()` |
| **Insights** | Analytics and reporting | `getBalance()`, `getMessageStatistics()`, `getMessageHistory()` |
| **Webhooks** | Handle incoming webhooks | `handle()`, `processDeliveryReport()` |

---

## ✅ Testing

```bash
composer test
```

Tests are written using [Pest](https://pestphp.com/) and run automatically via GitHub Actions on every push.

---

## 📄 Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

---

## 🤝 Contributing

Contributions are welcome! Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

---

## 🔒 Security

If you discover any security issues, please review [our security policy](../../security/policy) to report them.

---

## 👨‍💻 Credits

* [Thomson Maguru](https://github.com/tomsgad)
* [TechLegend](https://github.com/mg-techlegend)
* [All Contributors](../../contributors)

---

## 📜 License

The MIT License (MIT). Please see the [LICENSE](LICENSE.md) file for more information.
