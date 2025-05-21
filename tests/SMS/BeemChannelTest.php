<?php

use Illuminate\Notifications\Notification;
use TechLegend\LaravelBeemAfrica\SMS\Beem;
use TechLegend\LaravelBeemAfrica\SMS\BeemChannel;
use TechLegend\LaravelBeemAfrica\SMS\BeemMessage;

beforeEach(function () {
    // Create a mock of the Beem API client
    $this->beem = mock(Beem::class);
    $this->channel = new BeemChannel($this->beem);
});

it('sends a message using the Beem client', function () {
    $message = BeemMessage::create('Hello')->sender('TechLegend');

    $notifiable = new class
    {
        public function routeNotificationFor($channel)
        {
            return ['255713071267', '255789988188'];
        }
    };

    $notification = new class($message) extends Notification
    {
        private BeemMessage $message;

        public function __construct($message)
        {
            $this->message = $message;
        }

        public function toBeem($notifiable)
        {
            return $this->message;
        }
    };

    // Expected recipient structure
    $expectedRecipients = [
        ['recipient_id' => 0, 'dest_addr' => '255713071267'],
        ['recipient_id' => 1, 'dest_addr' => '255789988188'],
    ];

    $this->beem
        ->shouldReceive('sendMessage')
        ->once()
        ->with($message, $expectedRecipients)
        ->andReturn('ok');

    $result = $this->channel->send($notifiable, $notification);

    expect($result)->toBe('ok');
});

it('returns an empty array if no recipients found', function () {
    $notifiable = new class
    {
        public function routeNotificationFor($channel)
        {
            return [];
        }
    };

    $recipients = $this->channel->getRecipients($notifiable);

    expect($recipients)->toBe([]);
});
