<?php

return [
    'vapid' => [
        'subject' => env('VAPID_SUBJECT', 'mailto:admin@olilearn.com'),
        'public_key' => env('VAPID_PUBLIC_KEY'),
        'private_key' => env('VAPID_PRIVATE_KEY'),
    ],

    'enabled' => env('WEB_PUSH_ENABLED', true),

    'default_icon' => '/icons/icon-192x192.png',
    'default_badge' => '/icons/badge-72x72.png',

    'ttl' => 2419200, // 4 weeks in seconds

    'guzzle' => [
        'timeout' => 10,
        'verify' => env('APP_ENV') === 'production',
    ],
];
