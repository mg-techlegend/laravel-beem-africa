# Laravel Beem Africa

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mg-techlegend/laravel-beem-africa.svg?style=flat-square)](https://packagist.org/packages/mg-techlegend/laravel-beem-africa)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/mg-techlegend/laravel-beem-africa/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/mg-techlegend/laravel-beem-africa/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/mg-techlegend/laravel-beem-africa.svg?style=flat-square)](https://packagist.org/packages/mg-techlegend/laravel-beem-africa)

A comprehensive Laravel package for integrating with [Beem Africa's](https://beem.africa) API services. Provides a clean, expressive interface for SMS, OTP, Airtime, USSD, Voice, and Insights with Laravel Notifications support, typed responses, and robust error handling.

## Features

- **SMS** - Single and bulk messaging with fluent message builder
- **OTP** - Generate, verify, and resend one-time passwords
- **Airtime** - Send airtime, check balance, transaction history
- **USSD** - Create, update, list, and delete USSD menus
- **Voice** - Make calls, check status, list available voices
- **Insights** - Delivery reports, message statistics, account balance
- **Webhooks** - Handle delivery reports with HMAC signature verification
- **Laravel Notifications** - Native notification channel integration
- **Typed Responses** - Readonly DTOs for all API responses
- **Error Handling** - Specific exceptions for auth, validation, and request errors
- **Phone Normalization** - Automatic formatting with optional country code prefixing
- **HTTP Retries** - Configurable retry logic for resilient requests

## Installation

```bash
composer require mg-techlegend/laravel-beem-africa
```

Publish the configuration file:

```bash
php artisan vendor:publish --tag="laravel-beem-africa-config"
```

## Configuration

Add these to your `.env` file:

```env
BEEM_API_KEY=your-api-key
BEEM_SECRET_KEY=your-secret-key
BEEM_SENDER_NAME=YourApp

# Optional
BEEM_DEFAULT_COUNTRY_CODE=255
BEEM_TIMEOUT=30
BEEM_HTTP_RETRY_ATTEMPTS=3
```

## Usage

### Quick SMS

```php
use TechLegend\LaravelBeemAfrica\Facades\LaravelBeemAfrica as BeemAfrica;

// Single SMS
$response = BeemAfrica::sendSms('255700000001', 'Hello from Beem!');

// With custom sender
$response = BeemAfrica::sendSms('255700000001', 'Hello!', 'MyApp');

// Bulk SMS
$response = BeemAfrica::sendSmsBulk(
    ['255700000001', '255700000002'],
    'Bulk message'
);
```

### Fluent Message Builder

```php
use TechLegend\LaravelBeemAfrica\BeemAfricaMessage;
use TechLegend\LaravelBeemAfrica\Facades\LaravelBeemAfrica as BeemAfrica;

$message = BeemAfricaMessage::make()
    ->to('255700000001')
    ->content('Hello World')
    ->sender('MyApp')
    ->encoding(1); // UCS2 encoding

$response = BeemAfrica::sendSmsMessage($message);

echo $response->successful;  // true
echo $response->requestId;   // 12345
echo $response->valid;       // 1
```

### OTP Service

```php
// Generate OTP
$response = BeemAfrica::generateOtp('255700000001', [
    'length' => 6,
    'expiry' => 300,
    'type' => 'numeric',
    'message' => 'Your code is: {code}',
]);

echo $response->requestId; // 'otp_123456'

// Verify OTP
$response = BeemAfrica::verifyOtp('255700000001', '123456');

echo $response->valid; // true

// Resend OTP (via service accessor)
$response = BeemAfrica::otp()->resend('255700000001', 'otp_123456');
```

### Airtime

```php
// Send airtime
$response = BeemAfrica::sendAirtime('255700000001', 1000.00, [
    'currency' => 'TZS',
]);

echo $response->transactionId; // 'txn_123'

// Check balance
$balance = BeemAfrica::airtime()->getBalance();
echo $balance->balance;   // 5000.00
echo $balance->currency;  // 'TZS'
```

### Voice

```php
$response = BeemAfrica::makeCall('255700000001', 'Hello, this is a voice message', [
    'language' => 'en',
    'voice_id' => 1,
    'repeat_count' => 2,
]);

echo $response->callId; // 'call_123'
```

### USSD Menus

```php
$response = BeemAfrica::ussd()->createMenu([
    'menu_name' => 'My Service',
    'menu_items' => [...],
    'welcome_message' => 'Welcome!',
]);

echo $response->menuId; // 'menu_123'
```

### Insights & Reporting

```php
// Account balance
$balance = BeemAfrica::getBalance();

// Delivery report
$report = BeemAfrica::insights()->getDeliveryReport('request_123');

// Message statistics
$stats = BeemAfrica::insights()->getMessageStatistics([
    'start_date' => '2024-01-01',
    'end_date' => '2024-01-31',
]);
```

### Service Accessors

Access individual services for advanced use:

```php
BeemAfrica::sms();       // BeemSms
BeemAfrica::otp();       // BeemOtp
BeemAfrica::airtime();   // BeemAirtime
BeemAfrica::ussd();      // BeemUssd
BeemAfrica::voice();     // BeemVoice
BeemAfrica::insights();  // BeemInsights
BeemAfrica::webhooks();  // BeemWebhookHandler
```

### Laravel Notifications

```php
use Illuminate\Notifications\Notification;
use TechLegend\LaravelBeemAfrica\BeemAfricaMessage;
use TechLegend\LaravelBeemAfrica\Channels\BeemAfricaChannel;

class OrderShipped extends Notification
{
    public function via(object $notifiable): array
    {
        return [BeemAfricaChannel::class];
    }

    public function toBeemAfrica(object $notifiable): BeemAfricaMessage
    {
        return BeemAfricaMessage::make()
            ->content('Your order has been shipped!')
            ->sender('MyShop');
    }
}
```

Add the routing method to your notifiable model:

```php
class User extends Authenticatable
{
    use Notifiable;

    public function routeNotificationForBeemAfrica(): string
    {
        return $this->phone_number;
    }
}
```

### Dependency Injection

```php
use TechLegend\LaravelBeemAfrica\LaravelBeemAfrica;

class SmsController extends Controller
{
    public function __construct(
        private LaravelBeemAfrica $beem,
    ) {}

    public function send()
    {
        $response = $this->beem->sendSms('255700000001', 'Hello!');
    }
}
```

### Webhooks

```php
use TechLegend\LaravelBeemAfrica\Facades\LaravelBeemAfrica as BeemAfrica;

Route::post('/webhooks/beem', function (Request $request) {
    $result = BeemAfrica::webhooks()->handle($request);

    if ($result['successful']) {
        // Process the webhook data
        $eventType = $result['event_type'];
        $data = $result['data'];
    }

    return response()->json(['status' => 'ok']);
});
```

### Error Handling

```php
use TechLegend\LaravelBeemAfrica\Exceptions\BeemAfricaAuthenticationException;
use TechLegend\LaravelBeemAfrica\Exceptions\BeemAfricaValidationException;
use TechLegend\LaravelBeemAfrica\Exceptions\BeemAfricaRequestException;

try {
    $response = BeemAfrica::sendSms('255700000001', 'Hello!');
} catch (BeemAfricaAuthenticationException $e) {
    // Invalid API credentials (401/403)
    $e->payload; // Original API response
} catch (BeemAfricaValidationException $e) {
    // Invalid request data (400/422)
} catch (BeemAfricaRequestException $e) {
    // Server error or connection issue (5xx)
}
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Thomson Maguru](https://github.com/mg-techlegend)
- [Beem Africa](https://beem.africa)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
