<?php

// app/Notifications/WebPushNotification.php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class WebPushNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $title;
    protected $body;
    protected $url;
    protected $icon;
    protected $actions;
    protected $badge;
    protected $tag;
    protected $requireInteraction;

    public function __construct(
        string $title,
        string $body = '',
        ?string $url = null,
        ?string $icon = null,
        array $actions = [],
        ?string $badge = null,
        ?string $tag = null,
        bool $requireInteraction = false
    ) {
        $this->title = $title;
        $this->body = $body;
        $this->url = $url ?? url('/');
        $this->icon = $icon ?? '/icons/icon-192x192.png';
        $this->actions = $actions;
        $this->badge = $badge ?? '/icons/badge-72x72.png';
        $this->tag = $tag;
        $this->requireInteraction = $requireInteraction;
    }

    public function via($notifiable): array
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification): WebPushMessage
    {
        $message = (new WebPushMessage)
            ->title($this->title)
            ->icon($this->icon)
            ->body($this->body)
            ->badge($this->badge)
            ->data([
                'url' => $this->url,
                'id' => $notification->id,
                'timestamp' => now()->timestamp,
            ]);

        // Add default actions
        $message->action('Open', 'open');
        $message->action('Close', 'close');

        // Add custom actions
        foreach ($this->actions as $action) {
            $message->action($action['title'], $action['action']);
        }

        // Optional settings
        if ($this->tag) {
            $message->tag($this->tag);
        }

        if ($this->requireInteraction) {
            $message->requireInteraction();
        }

        return $message;
    }
}
