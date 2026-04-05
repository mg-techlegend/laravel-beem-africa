<?php

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use TechLegend\LaravelBeemAfrica\BeemAfricaClient;
use TechLegend\LaravelBeemAfrica\BeemAfricaMessage;
use TechLegend\LaravelBeemAfrica\Channels\BeemAfricaChannel;
use TechLegend\LaravelBeemAfrica\Exceptions\BeemAfricaAuthenticationException;
use TechLegend\LaravelBeemAfrica\Exceptions\BeemAfricaRequestException;
use TechLegend\LaravelBeemAfrica\Exceptions\BeemAfricaValidationException;
use TechLegend\LaravelBeemAfrica\LaravelBeemAfrica;
use TechLegend\LaravelBeemAfrica\Webhooks\BeemWebhookHandler;

// ── Helpers ──────────────────────────────────────────────────────────

function smsSuccessPayload(): array
{
    return [
        'successful' => true,
        'request_id' => 12345,
        'message' => 'Message Submitted Successfully',
        'valid' => 1,
        'invalid' => 0,
        'duplicates' => 0,
    ];
}

function otpGeneratePayload(): array
{
    return [
        'successful' => true,
        'request_id' => 'otp_123456',
        'message' => 'OTP generated successfully',
    ];
}

function otpVerifyPayload(): array
{
    return [
        'successful' => true,
        'valid' => true,
        'message' => 'OTP verified successfully',
    ];
}

function balancePayload(): array
{
    return [
        'successful' => true,
        'balance' => 5000.00,
        'currency' => 'TZS',
    ];
}

// ── Setup ────────────────────────────────────────────────────────────

beforeEach(function () {
    // Reset singletons
    foreach ([BeemAfricaChannel::class, LaravelBeemAfrica::class, BeemAfricaClient::class, BeemWebhookHandler::class] as $abstract) {
        if (app()->bound($abstract)) {
            app()->forgetInstance($abstract);
        }
    }

    config([
        'beem-africa.api_key' => 'test-api-key',
        'beem-africa.secret_key' => 'test-secret-key',
        'beem-africa.sender_name' => 'TestSender',
        'beem-africa.base_url' => 'https://apisms.beem.africa/v1',
        'beem-africa.timeout' => 30,
        'beem-africa.connect_timeout' => 10,
        'beem-africa.http_retry_attempts' => 1,
        'beem-africa.http_retry_delay_ms' => 250,
        'beem-africa.defaults' => [
            'sms' => ['encoding' => 0],
            'otp' => ['length' => 6, 'expiry' => 300, 'type' => 'numeric'],
            'airtime' => ['currency' => 'TZS'],
            'voice' => ['language' => 'en', 'voice_id' => 1],
        ],
        'beem-africa.webhooks' => [
            'enabled' => false,
            'secret' => '',
        ],
    ]);

    Http::preventStrayRequests();
});

// ── SMS Tests ────────────────────────────────────────────────────────

it('sends a single SMS successfully', function () {
    Http::fake([
        'https://apisms.beem.africa/v1/send' => Http::response(smsSuccessPayload(), 200),
    ]);

    $response = app(LaravelBeemAfrica::class)->sendSms('255700000001', 'Hello from Beem!');

    expect($response->successful)->toBeTrue()
        ->and($response->requestId)->toBe(12345)
        ->and($response->message)->toBe('Message Submitted Successfully')
        ->and($response->valid)->toBe(1);

    Http::assertSent(function ($request) {
        $data = $request->data();

        return $data['source_addr'] === 'TestSender'
            && $data['message'] === 'Hello from Beem!'
            && $data['recipients'][0]['dest_addr'] === '255700000001';
    });
});

it('sends a single SMS with custom sender', function () {
    Http::fake([
        'https://apisms.beem.africa/v1/send' => Http::response(smsSuccessPayload(), 200),
    ]);

    app(LaravelBeemAfrica::class)->sendSms('255700000001', 'Hello!', 'CustomSender');

    Http::assertSent(function ($request) {
        return $request->data()['source_addr'] === 'CustomSender';
    });
});

it('sends bulk SMS successfully', function () {
    Http::fake([
        'https://apisms.beem.africa/v1/send' => Http::response(smsSuccessPayload(), 200),
    ]);

    $response = app(LaravelBeemAfrica::class)->sendSmsBulk(
        ['255700000001', '255700000002'],
        'Bulk message'
    );

    expect($response->successful)->toBeTrue();

    Http::assertSent(function ($request) {
        $data = $request->data();

        return count($data['recipients']) === 2
            && $data['recipients'][0]['dest_addr'] === '255700000001'
            && $data['recipients'][1]['dest_addr'] === '255700000002'
            && $data['recipients'][0]['recipient_id'] === 0
            && $data['recipients'][1]['recipient_id'] === 1;
    });
});

it('sends SMS using fluent message builder', function () {
    Http::fake([
        'https://apisms.beem.africa/v1/send' => Http::response(smsSuccessPayload(), 200),
    ]);

    $message = BeemAfricaMessage::make()
        ->to('255700000001')
        ->content('Builder message')
        ->sender('MyApp')
        ->encoding(1);

    $response = app(LaravelBeemAfrica::class)->sendSmsMessage($message);

    expect($response->successful)->toBeTrue();

    Http::assertSent(function ($request) {
        $data = $request->data();

        return $data['source_addr'] === 'MyApp'
            && $data['encoding'] === 1
            && $data['message'] === 'Builder message';
    });
});

it('uses Basic auth header for API requests', function () {
    Http::fake([
        'https://apisms.beem.africa/v1/send' => Http::response(smsSuccessPayload(), 200),
    ]);

    app(LaravelBeemAfrica::class)->sendSms('255700000001', 'Test auth');

    Http::assertSent(function ($request) {
        $authHeader = $request->header('Authorization')[0] ?? '';
        $expected = 'Basic ' . base64_encode('test-api-key:test-secret-key');

        return $authHeader === $expected;
    });
});

// ── Error Handling Tests ─────────────────────────────────────────────

it('throws authentication exception on 401', function () {
    Http::fake([
        'https://apisms.beem.africa/v1/send' => Http::response([
            'message' => 'Unauthenticated',
        ], 401),
    ]);

    expect(fn () => app(LaravelBeemAfrica::class)->sendSms('255700000001', 'Test'))
        ->toThrow(BeemAfricaAuthenticationException::class, 'Unauthenticated');
});

it('throws validation exception on 422', function () {
    Http::fake([
        'https://apisms.beem.africa/v1/send' => Http::response([
            'message' => 'Invalid phone number format',
        ], 422),
    ]);

    expect(fn () => app(LaravelBeemAfrica::class)->sendSms('invalid', 'Test'))
        ->toThrow(BeemAfricaValidationException::class, 'Invalid phone number format');
});

it('throws request exception on 500', function () {
    Http::fake([
        'https://apisms.beem.africa/v1/send' => Http::response([
            'message' => 'Internal server error',
        ], 500),
    ]);

    expect(fn () => app(LaravelBeemAfrica::class)->sendSms('255700000001', 'Test'))
        ->toThrow(BeemAfricaRequestException::class, 'Internal server error');
});

it('preserves API payload in exception', function () {
    Http::fake([
        'https://apisms.beem.africa/v1/send' => Http::response([
            'message' => 'Bad request',
            'errors' => ['phone' => 'required'],
        ], 400),
    ]);

    try {
        app(LaravelBeemAfrica::class)->sendSms('255700000001', 'Test');
        $this->fail('Expected exception');
    } catch (BeemAfricaValidationException $e) {
        expect($e->payload)->toHaveKey('errors')
            ->and($e->payload['errors'])->toBe(['phone' => 'required']);
    }
});

// ── OTP Tests ────────────────────────────────────────────────────────

it('generates OTP successfully', function () {
    Http::fake([
        'https://apisms.beem.africa/v1/otp/generate' => Http::response(otpGeneratePayload(), 200),
    ]);

    $response = app(LaravelBeemAfrica::class)->generateOtp('255700000001');

    expect($response->successful)->toBeTrue()
        ->and($response->requestId)->toBe('otp_123456')
        ->and($response->message)->toBe('OTP generated successfully');
});

it('verifies OTP successfully', function () {
    Http::fake([
        'https://apisms.beem.africa/v1/otp/verify' => Http::response(otpVerifyPayload(), 200),
    ]);

    $response = app(LaravelBeemAfrica::class)->verifyOtp('255700000001', '123456');

    expect($response->successful)->toBeTrue()
        ->and($response->valid)->toBeTrue()
        ->and($response->message)->toBe('OTP verified successfully');
});

it('generates OTP with custom options', function () {
    Http::fake([
        'https://apisms.beem.africa/v1/otp/generate' => Http::response(otpGeneratePayload(), 200),
    ]);

    app(LaravelBeemAfrica::class)->generateOtp('255700000001', [
        'length' => 8,
        'expiry' => 600,
        'type' => 'alphanumeric',
        'message' => 'Code: {code}',
    ]);

    Http::assertSent(function ($request) {
        $data = $request->data();

        return $data['length'] === 8
            && $data['expiry'] === 600
            && $data['type'] === 'alphanumeric'
            && $data['message'] === 'Code: {code}';
    });
});

// ── Balance Tests ────────────────────────────────────────────────────

it('gets account balance', function () {
    Http::fake([
        'https://apisms.beem.africa/v1/insights/balance' => Http::response(balancePayload(), 200),
    ]);

    $response = app(LaravelBeemAfrica::class)->getBalance();

    expect($response->successful)->toBeTrue()
        ->and($response->balance)->toBe(5000.00)
        ->and($response->currency)->toBe('TZS');
});

// ── Airtime Tests ────────────────────────────────────────────────────

it('sends airtime successfully', function () {
    Http::fake([
        'https://apisms.beem.africa/v1/airtime/send' => Http::response([
            'successful' => true,
            'transaction_id' => 'txn_123',
            'message' => 'Airtime sent successfully',
        ], 200),
    ]);

    $response = app(LaravelBeemAfrica::class)->sendAirtime('255700000001', 1000.00);

    expect($response->successful)->toBeTrue()
        ->and($response->transactionId)->toBe('txn_123');
});

// ── Voice Tests ──────────────────────────────────────────────────────

it('makes a voice call', function () {
    Http::fake([
        'https://apisms.beem.africa/v1/voice/call' => Http::response([
            'successful' => true,
            'call_id' => 'call_123',
            'message' => 'Call initiated',
        ], 200),
    ]);

    $response = app(LaravelBeemAfrica::class)->makeCall('255700000001', 'Hello voice');

    expect($response->successful)->toBeTrue()
        ->and($response->callId)->toBe('call_123');
});

// ── Phone Normalization Tests ────────────────────────────────────────

it('applies default country calling code for local numbers', function () {
    config(['beem-africa.default_country_calling_code' => '255']);

    // Reset singletons to pick up new config
    app()->forgetInstance(BeemAfricaClient::class);
    app()->forgetInstance(LaravelBeemAfrica::class);

    Http::fake([
        'https://apisms.beem.africa/v1/send' => Http::response(smsSuccessPayload(), 200),
    ]);

    app(LaravelBeemAfrica::class)->sendSms('0712345678', 'Local number test');

    Http::assertSent(function ($request) {
        return $request->data()['recipients'][0]['dest_addr'] === '2550712345678';
    });
});

it('strips spaces and special characters from phone numbers', function () {
    Http::fake([
        'https://apisms.beem.africa/v1/send' => Http::response(smsSuccessPayload(), 200),
    ]);

    app(LaravelBeemAfrica::class)->sendSms('+255 700 000 001', 'Formatted number');

    Http::assertSent(function ($request) {
        return $request->data()['recipients'][0]['dest_addr'] === '255700000001';
    });
});

// ── Notification Channel Tests ───────────────────────────────────────

it('sends via notification channel using routeNotificationForBeemAfrica', function () {
    Http::fake([
        'https://apisms.beem.africa/v1/send' => Http::response(smsSuccessPayload(), 200),
    ]);

    $notifiable = new class
    {
        public function routeNotificationForBeemAfrica(): string
        {
            return '255700000001';
        }
    };

    $notification = new class extends Notification
    {
        public function via(object $notifiable): array
        {
            return [BeemAfricaChannel::class];
        }

        public function toBeemAfrica(object $notifiable): BeemAfricaMessage
        {
            return BeemAfricaMessage::make()->content('Notification body');
        }
    };

    app(BeemAfricaChannel::class)->send($notifiable, $notification);

    Http::assertSent(function ($request) {
        $data = $request->data();

        return $data['message'] === 'Notification body'
            && $data['recipients'][0]['dest_addr'] === '255700000001';
    });
});

it('sends via notification channel with multiple recipients', function () {
    Http::fake([
        'https://apisms.beem.africa/v1/send' => Http::response(smsSuccessPayload(), 200),
    ]);

    $notifiable = new class
    {
        public function routeNotificationForBeemAfrica(): array
        {
            return ['255700000001', '255700000002'];
        }
    };

    $notification = new class extends Notification
    {
        public function via(object $notifiable): array
        {
            return [BeemAfricaChannel::class];
        }

        public function toBeemAfrica(object $notifiable): BeemAfricaMessage
        {
            return BeemAfricaMessage::make()->content('Multi-recipient');
        }
    };

    app(BeemAfricaChannel::class)->send($notifiable, $notification);

    Http::assertSent(function ($request) {
        return count($request->data()['recipients']) === 2;
    });
});

// ── Message Builder Tests ────────────────────────────────────────────

it('creates an immutable message via fluent builder', function () {
    $original = BeemAfricaMessage::make();
    $withContent = $original->content('Hello');
    $withSender = $withContent->sender('MySender');

    // Original remains unchanged
    expect($original->getContent())->toBeNull()
        ->and($original->getSender())->toBeNull();

    // New instances have correct values
    expect($withContent->getContent())->toBe('Hello')
        ->and($withContent->getSender())->toBeNull();

    expect($withSender->getContent())->toBe('Hello')
        ->and($withSender->getSender())->toBe('MySender');
});

it('throws when sending message without content', function () {
    $message = BeemAfricaMessage::make()->to('255700000001');

    expect(fn () => $message->assertComplete())
        ->toThrow(InvalidArgumentException::class, 'content is required');
});

it('throws when sending message without recipients', function () {
    $message = BeemAfricaMessage::make()->content('Hello');

    expect(fn () => $message->assertComplete())
        ->toThrow(InvalidArgumentException::class, 'recipient is required');
});

// ── Configuration Tests ──────────────────────────────────────────────

it('throws when api key is missing', function () {
    config(['beem-africa.api_key' => '']);

    app()->forgetInstance(BeemAfricaClient::class);

    expect(fn () => app(BeemAfricaClient::class))
        ->toThrow(InvalidArgumentException::class, 'Missing API key');
});

it('throws when secret key is missing', function () {
    config(['beem-africa.secret_key' => '']);

    app()->forgetInstance(BeemAfricaClient::class);

    expect(fn () => app(BeemAfricaClient::class))
        ->toThrow(InvalidArgumentException::class, 'Missing secret key');
});

// ── Facade Tests ─────────────────────────────────────────────────────

it('resolves via facade', function () {
    $resolved = app('beem-africa');

    expect($resolved)->toBeInstanceOf(LaravelBeemAfrica::class);
});

// ── Service Accessor Tests ───────────────────────────────────────────

it('provides access to all service classes', function () {
    $beem = app(LaravelBeemAfrica::class);

    expect($beem->sms())->toBeInstanceOf(\TechLegend\LaravelBeemAfrica\Services\BeemSms::class)
        ->and($beem->otp())->toBeInstanceOf(\TechLegend\LaravelBeemAfrica\Services\BeemOtp::class)
        ->and($beem->airtime())->toBeInstanceOf(\TechLegend\LaravelBeemAfrica\Services\BeemAirtime::class)
        ->and($beem->ussd())->toBeInstanceOf(\TechLegend\LaravelBeemAfrica\Services\BeemUssd::class)
        ->and($beem->voice())->toBeInstanceOf(\TechLegend\LaravelBeemAfrica\Services\BeemVoice::class)
        ->and($beem->insights())->toBeInstanceOf(\TechLegend\LaravelBeemAfrica\Services\BeemInsights::class)
        ->and($beem->webhooks())->toBeInstanceOf(\TechLegend\LaravelBeemAfrica\Webhooks\BeemWebhookHandler::class);
});

it('returns a fresh message builder', function () {
    $beem = app(LaravelBeemAfrica::class);

    expect($beem->message())->toBeInstanceOf(BeemAfricaMessage::class)
        ->and($beem->message()->getContent())->toBeNull();
});

// ── Retry Tests ──────────────────────────────────────────────────────

it('retries on server error when configured', function () {
    config([
        'beem-africa.http_retry_attempts' => 3,
        'beem-africa.http_retry_delay_ms' => 0,
    ]);

    app()->forgetInstance(BeemAfricaClient::class);
    app()->forgetInstance(LaravelBeemAfrica::class);

    Http::fake([
        'https://apisms.beem.africa/v1/send' => Http::response(smsSuccessPayload(), 200),
    ]);

    $response = app(LaravelBeemAfrica::class)->sendSms('255700000001', 'Retry test');

    expect($response->successful)->toBeTrue();
});
