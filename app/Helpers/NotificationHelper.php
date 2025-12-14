<?php

namespace App\Helpers;

use App\Models\User;
use App\Services\WebPush\WebPushService;

class NotificationHelper
{
    /**
     * Send push notification to user
     */
    public static function sendToUser(User $user, string $title, string $body, ?string $url = null, ?string $icon = null): array
    {
        $service = new WebPushService();
        $notification = $service->createNotification($title, $body, $url, $icon);

        return $service->sendToUser($user, $notification);
    }

    /**
     * Send push notification to all users
     */
    public static function broadcast(string $title, string $body, ?string $url = null, ?string $icon = null): array
    {
        $users = User::whereHas('pushSubscriptions', function ($query) {
            $query->active();
        })->get();

        $service = new WebPushService();
        $notification = $service->createNotification($title, $body, $url, $icon);

        $totalResults = [
            'total_users' => $users->count(),
            'total_sent' => 0,
            'failed_users' => [],
        ];

        foreach ($users as $user) {
            $result = $service->sendToUser($user, $notification);

            if ($result['success'] > 0) {
                $totalResults['total_sent']++;
            } else {
                $totalResults['failed_users'][] = $user->id;
            }
        }

        return $totalResults;
    }

    /**
     * Send order notification
     */
    public static function sendOrderNotification(User $user, $order): array
    {
        return self::sendToUser(
            $user,
            'Order Update',
            "Your order #{$order->id} has been updated.",
            route('orders.show', $order),
            '/icons/order.png'
        );
    }

    /**
     * Send message notification
     */
    public static function sendMessageNotification(User $user, $message): array
    {
        return self::sendToUser(
            $user,
            'New Message',
            "You have a new message from {$message->sender->name}",
            route('messages.show', $message),
            '/icons/message.png'
        );
    }
}
