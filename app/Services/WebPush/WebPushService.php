<?php

namespace App\Services\WebPush;

use App\Models\PushSubscription;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebPushService
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
    public function sendToSubscription(PushSubscription $subscription, array $notification): bool
    {
        try {
            $payload = json_encode($notification);

            // Encrypt the payload
            $encryptionService = new Encryption();
            $encrypted = $encryptionService->encrypt(
                $payload,
                $subscription->p256dh_key,
                $subscription->auth_key,
                $this->privateKey
            );

            // Extract endpoint host for VAPID audience
            $endpoint = $subscription->endpoint;
            $parsedUrl = parse_url($endpoint);
            $audience = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];

            // Generate VAPID JWT
            $vapidJWT = $encryptionService->generateVapidJWT($audience, $this->subject, $this->privateKey);

            // Prepare headers
            $headers = [
                'Authorization' => 'vapid t=' . $vapidJWT . ', k=' . $this->publicKey,
                'Content-Encoding' => 'aesgcm',
                'Encryption' => 'salt=' . $encrypted['salt'],
                'Crypto-Key' => 'dh=' . $encrypted['local_public_key'],
                'TTL' => '2419200', // 4 weeks
                'Content-Type' => 'application/octet-stream',
            ];

            // Make the request
            $response = Http::withHeaders($headers)
                ->withBody($encrypted['ciphertext'] . $encrypted['auth_tag'], 'application/octet-stream')
                ->timeout(10)
                ->post($endpoint);

            $status = $response->status();

            if ($status === 201) {
                Log::info('Push notification sent successfully', [
                    'subscription_id' => $subscription->id,
                    'endpoint' => $subscription->endpoint,
                ]);

                // Update last used
                $subscription->update(['last_used_at' => now()]);
                return true;

            } elseif ($status === 410) {
                // Subscription expired
                Log::info('Push subscription expired, deleting', [
                    'subscription_id' => $subscription->id,
                ]);
                $subscription->update(['is_active' => false]);
                return false;

            } else {
                Log::warning('Push notification failed', [
                    'status' => $status,
                    'response' => $response->body(),
                    'subscription_id' => $subscription->id,
                ]);
                return false;
            }

        } catch (\Exception $e) {
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
            $success = $this->sendToSubscription($subscription, $notification);

            if ($success) {
                $results['success']++;
            } else {
                $results['failed']++;
            }
        }

        return $results;
    }

    /**
     * Send notification to a user
     */
    public function sendToUser($user, array $notification): array
    {
        $subscriptions = $user->pushSubscriptions()->active()->get();
        return $this->sendToSubscriptions($subscriptions, $notification);
    }

    /**
     * Create notification payload
     */
    public function createNotification(
        string $title,
        string $body,
        ?string $url = null,
        ?string $icon = null,
        ?string $badge = null,
        array $data = []
    ): array {
        return [
            'title' => $title,
            'body' => $body,
            'icon' => $icon ?? '/icons/icon-192x192.png',
            'badge' => $badge ?? '/icons/badge-72x72.png',
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
            'actions' => [
                ['action' => 'open', 'title' => 'Open'],
                ['action' => 'close', 'title' => 'Close']
            ]
        ];
    }
}
