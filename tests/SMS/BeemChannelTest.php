<?php

use Illuminate\Notifications\Notification;
use TechLegend\LaravelBeemAfrica\SMS\Beem;
use TechLegend\LaravelBeemAfrica\SMS\BeemChannel;
use TechLegend\LaravelBeemAfrica\SMS\BeemMessage;

beforeEach(function () {
    $this->beem = Mockery::mock(Beem::class);
    $this->channel = new BeemChannel($this->beem);
});

it('sends a message and returns Beem response array', function () {
    $message = BeemMessage::create('Hello World')->sender('TestSender');

    $notifiable = new class {
        public function routeNotificationFor($channel) {
            return ['255700000001', '255700000002'];
        }
    };

    $notification = new class($message) extends Notification {
        private $message;

        public function __construct($message) {
            $this->message = $message;
        }

        public function toBeem($notifiable) {
            return $this->message;
        }
    };

    $expectedRecipients = [
        ['recipient_id' => 0, 'dest_addr' => '255700000001'],
        ['recipient_id' => 1, 'dest_addr' => '255700000002'],
    ];

    $mockResponse = [
        'successful' => true,
        'request_id' => 123,
        'message' => 'Mocked Response',
        'valid' => 2,
        'invalid' => 0,
        'duplicates' => 0,
        'status_code' => 200,
    ];

    $this->beem
        ->shouldReceive('sendMessage')
        ->once()
        ->with($message, $expectedRecipients)
        ->andReturn($mockResponse);

    $result = $this->channel->send($notifiable, $notification);

    expect($result)->toBeArray();
    expect($result['successful'])->toBeTrue();
    expect($result['message'])->toBe('Mocked Response');
});
