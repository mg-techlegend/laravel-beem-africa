<?php

use TechLegend\LaravelBeemAfrica\SMS\Beem;
use TechLegend\LaravelBeemAfrica\SMS\BeemMessage;
use GuzzleHttp\Psr7\Response;

it('returns a structured array when sending a message', function () {
    $config = [
        'api_key' => 'fake_key',
        'secret_key' => 'fake_secret',
        'sender_name' => 'TestSender',
    ];

    $mockedResponse = [
        'successful' => true,
        'request_id' => 123456,
        'message' => 'Message Submitted Successfully',
        'valid' => 1,
        'invalid' => 0,
        'duplicates' => 0,
    ];

    // Mock Guzzle Client
    Mockery::mock('overload:GuzzleHttp\Client')
        ->shouldReceive('post')
        ->once()
        ->andReturn(new Response(200, [], json_encode($mockedResponse)));

    $beem = new Beem($config);

    $message = BeemMessage::create('Test message');
    $recipients = [
        ['recipient_id' => 0, 'dest_addr' => '255700000001'],
    ];

    $result = $beem->sendMessage($message, $recipients);

    expect($result)->toBeArray();
    expect($result['successful'])->toBeTrue();
    expect($result['message'])->toBe('Message Submitted Successfully');
    expect($result['status_code'])->toBe(200);
});
