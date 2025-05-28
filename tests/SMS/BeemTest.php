<?php

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use TechLegend\LaravelBeemAfrica\SMS\Beem;
use TechLegend\LaravelBeemAfrica\SMS\BeemMessage;

it('sends message successfully', function () {
    $mockClient = Mockery::mock(Client::class);
    $responseBody = json_encode([
        'successful' => true,
        'request_id' => 123,
        'message' => 'Message Submitted Successfully',
        'valid' => 2,
        'invalid' => 0,
        'duplicates' => 0,
    ]);
    $mockResponse = new Response(200, [], $responseBody);

    $mockClient->shouldReceive('post')
        ->once()
        ->andReturn($mockResponse);

    $config = [
        'api_key' => 'fake_api_key',
        'secret_key' => 'fake_secret',
        'sender_name' => 'TestSender',
    ];

    $beem = new Beem($config, $mockClient);

    $message = BeemMessage::create('Test message')->sender('TestSender');

    $recipients = [
        ['recipient_id' => 0, 'dest_addr' => '255700000001'],
        ['recipient_id' => 1, 'dest_addr' => '255700000002'],
    ];

    $result = $beem->sendMessage($message, $recipients);

    expect($result['successful'])->toBeTrue();
    expect($result['message'])->toBe('Message Submitted Successfully');
    expect($result['status_code'])->toBe(200);
});
