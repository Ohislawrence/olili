<?php

// app/Notifications/PushNotification.php
namespace App\Notifications;

use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class PushNotification extends Notification
{
    private $title;
    private $body;
    private $url;

    public function __construct($title, $body = '', $url = null)
    {
        $this->title = $title;
        $this->body = $body;
        $this->url = $url ?? url('/');
    }

    public function via($notifiable)
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title($this->title)
            ->body($this->body)
            ->icon('/icons/icon-192x192.png')
            ->badge('/icons/badge-72x72.png')
            ->action('View', 'open_app')
            ->action('Close', 'close')
            ->data([
                'url' => $this->url,
                'id' => $notification->id
            ]);
    }
}
