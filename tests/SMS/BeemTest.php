<?php

use TechLegend\LaravelBeemAfrica\SMS\Beem;
use TechLegend\LaravelBeemAfrica\SMS\BeemMessage;

it('can send a message successfully (mocked)', function () {
    $mock = Mockery::mock(Beem::class)->makePartial();
    $mock->shouldReceive('sendMessage')->andReturn('{"status":"success"}');

    $message = BeemMessage::create('Test SMS')->sender('TestSender');
    $recipients = [
        ['recipient_id' => 0, 'dest_addr' => '255713071267'],
    ];

    $response = $mock->sendMessage($message, $recipients);

    expect($response)->toContain('success');
});
