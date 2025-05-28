<?php

namespace TechLegend\LaravelBeemAfrica\SMS;

use Illuminate\Notifications\Notification;

class BeemChannel
{
    public Beem $beem;

    public function __construct(Beem $beem)
    {
        $this->beem = $beem;
    }

    public function send($notifiable, Notification $notification): array
    {
        $message = $notification->toBeem($notifiable);
        $recipients = $this->getRecipients($notifiable);

        return $this->beem->sendMessage($message, $recipients);
    }

    public function getRecipients($notifiable): array
    {
        $arrayContacts = [];

        if ($notifiable->routeNotificationFor('beem')) {
            $phoneNumbers = $notifiable->routeNotificationFor('beem');

            foreach ($phoneNumbers as $index => $phone) {
                $arrayContacts[] = [
                    'recipient_id' => $index,
                    'dest_addr' => (string) $phone,
                ];
            }
        }

        return $arrayContacts;
    }
}
