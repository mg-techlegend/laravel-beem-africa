<?php

use TechLegend\LaravelBeemAfrica\SMS\BeemMessage;

it('can set content and sender fluently', function () {
    $message = BeemMessage::create('Test content')->sender('TestSender');

    expect($message->content)->toBe('Test content')
        ->and($message->sender)->toBe('TestSender');
});

it('can update content and sender after construction', function () {
    $message = new BeemMessage;
    $message->content('Updated')->sender('UpdatedSender');

    expect($message->content)->toBe('Updated')
        ->and($message->sender)->toBe('UpdatedSender');
});
