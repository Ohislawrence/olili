<?php
<?php

namespace App\Traits;

use App\Models\PushSubscription;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasPushSubscriptions
{
    public function pushSubscriptions(): MorphMany
    {
        return $this->morphMany(PushSubscription::class, 'subscribable');
    }

    public function updatePushSubscription(string $endpoint, ?string $publicKey, ?string $authToken, ?string $contentEncoding = 'aesgcm'): PushSubscription
    {
        return $this->pushSubscriptions()->updateOrCreate(
            ['endpoint' => $endpoint],
            [
                'public_key' => $publicKey,
                'auth_token' => $authToken,
                'content_encoding' => $contentEncoding,
            ]
        );
    }

    public function deletePushSubscription(string $endpoint): void
    {
        $this->pushSubscriptions()
            ->where('endpoint', $endpoint)
            ->delete();
    }

    public function routeNotificationForWebPush()
    {
        return $this->pushSubscriptions;
    }
}
