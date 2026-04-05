<?php

namespace TechLegend\LaravelBeemAfrica\Channels;

use Illuminate\Notifications\Notification;
use InvalidArgumentException;
use TechLegend\LaravelBeemAfrica\BeemAfricaMessage;
use TechLegend\LaravelBeemAfrica\LaravelBeemAfrica;

final class BeemAfricaChannel
{
    public function __construct(
        private readonly LaravelBeemAfrica $beemAfrica,
    ) {}

    public function send(mixed $notifiable, Notification $notification): void
    {
        if (! is_object($notifiable) || ! method_exists($notifiable, 'routeNotificationForBeemAfrica')) {
            throw new InvalidArgumentException(
                '[Beem Africa] The notifiable must implement routeNotificationForBeemAfrica().'
            );
        }

        $route = $notifiable->routeNotificationForBeemAfrica();

        if (! method_exists($notification, 'toBeemAfrica')) {
            throw new InvalidArgumentException(
                '[Beem Africa] The notification must implement toBeemAfrica($notifiable).'
            );
        }

        $message = $notification->toBeemAfrica($notifiable);

        if (! $message instanceof BeemAfricaMessage) {
            throw new InvalidArgumentException(
                '[Beem Africa] toBeemAfrica() must return an instance of ' . BeemAfricaMessage::class . '.'
            );
        }

        // Handle single phone or array of phones
        if (is_array($route)) {
            $withRecipient = $message->getRecipients() === []
                ? $message->recipients($route)
                : $message;
        } elseif (is_string($route) && trim($route) !== '') {
            $withRecipient = $message->getRecipients() === []
                ? $message->to($route)
                : $message;
        } else {
            throw new InvalidArgumentException(
                '[Beem Africa] routeNotificationForBeemAfrica() must return a non-empty string or array of phone numbers.'
            );
        }

        $this->beemAfrica->sendSmsMessage($withRecipient);
    }
}
