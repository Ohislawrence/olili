<?php

namespace App\Services\WebPush;

use App\Models\WebPushSubscription;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class Sender
{
    private string $publicKey;
    private string $privateKey;
    private string $subject;

    public function __construct()
    {
        $this->publicKey = config('webpush.vapid.public_key');
        $this->privateKey = config('webpush.vapid.private_key');
        $this->subject = config('webpush.vapid.subject', 'mailto:admin@example.com');
    }

    /**
     * Send notification to a subscription
     */
    public function send(WebPushSubscription $subscription, array $notification): bool
    {
        try {
            $payload = json_encode($notification);

            // Encrypt the payload
            $encrypted = Encryption::encrypt(
                $payload,
                $subscription->p256dh_key,
                $subscription->auth_key,
                $this->privateKey
            );

            // Extract endpoint info
            $endpoint = $subscription->endpoint;

            // Generate VAPID JWT
            $audience = parse_url($endpoint, PHP_URL_SCHEME) . '://' .
                       parse_url($endpoint, PHP_URL_HOST);
            $vapidJWT = Encryption::generateVapidJWT($audience, $this->subject, $this->privateKey);

            // Prepare headers
            $headers = [
                'Authorization' => 'vapid t=' . $vapidJWT . ', k=' . $this->publicKey,
                'Content-Encoding' => 'aesgcm',
                'Encryption' => 'salt=' . $encrypted['salt'],
                'Crypto-Key' => 'dh=' . $encrypted['local_public_key'] . ';' .
                               'p256ecdsa=' . $this->publicKey,
                'TTL' => '2419200', // 4 weeks
                'Content-Type' => 'application/octet-stream',
            ];

            // Make the request
            $response = Http::withHeaders($headers)
                ->withBody($encrypted['ciphertext'] . $encrypted['auth_tag'], 'application/octet-stream')
                ->post($endpoint);

            $status = $response->status();

            if ($status === 201) {
                Log::info('Push notification sent successfully', [
                    'subscription_id' => $subscription->id,
                    'endpoint' => $subscription->endpoint,
                ]);
                return true;
            } elseif ($status === 410) {
                // Subscription expired
                Log::info('Push subscription expired, deleting', [
                    'subscription_id' => $subscription->id,
                ]);
                $subscription->delete();
                return false;
            } else {
                Log::warning('Push notification failed', [
                    'status' => $status,
                    'response' => $response->body(),
                    'subscription_id' => $subscription->id,
                ]);
                return false;
            }

        } catch (Exception $e) {
            Log::error('Error sending push notification', [
                'error' => $e->getMessage(),
                'subscription_id' => $subscription->id,
            ]);
            return false;
        }
    }

    /**
     * Send notification to multiple subscriptions
     */
    public function sendToSubscriptions($subscriptions, array $notification): array
    {
        $results = [
            'total' => count($subscriptions),
            'success' => 0,
            'failed' => 0,
            'expired' => 0,
        ];

        foreach ($subscriptions as $subscription) {
            $success = $this->send($subscription, $notification);

            if ($success) {
                $results['success']++;
            } else {
                $results['failed']++;

                // Check if it was an expiration (410)
                // This would need to be tracked in the send method
            }
        }

        return $results;
    }

    /**
     * Create notification payload
     */
    public function createNotification(
        string $title,
        string $body,
        ?string $url = null,
        ?string $icon = null,
        array $data = []
    ): array {
        return [
            'title' => $title,
            'body' => $body,
            'icon' => $icon ?? '/icons/icon-192x192.png',
            'badge' => '/icons/badge-72x72.png',
            'url' => $url ?? url('/'),
            'timestamp' => time(),
            'data' => array_merge($data, [
                'id' => uniqid(),
                'url' => $url ?? url('/'),
            ]),
            'vibrate' => [200, 100, 200],
            'requireInteraction' => false,
            'renotify' => false,
            'silent' => false,
        ];
    }
}
