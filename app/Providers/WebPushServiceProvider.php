<?php

// app/Providers/WebPushServiceProvider.php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Minishlink\WebPush\WebPush;

class WebPushServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/webpush.php', 'webpush');

        // Register WebPush service
        $this->app->singleton('webpush', function ($app) {
            $vapid = config('webpush.vapid');

            $auth = [
                'VAPID' => [
                    'subject' => $vapid['subject'],
                    'publicKey' => $vapid['public_key'],
                    'privateKey' => $vapid['private_key'],
                ],
            ];

            return new WebPush($auth);
        });

        // Alias for easy access
        $this->app->alias('webpush', WebPush::class);
    }

    public function boot(): void
    {
        // Publish config file if needed
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/webpush.php' => config_path('webpush.php'),
            ], 'webpush-config');
        }
    }
}
