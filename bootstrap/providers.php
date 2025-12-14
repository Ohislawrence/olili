<?php

return [
    App\Providers\AiServiceProvider::class,
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
    App\Providers\CourseServiceProvider::class,
    App\Providers\FortifyServiceProvider::class,
    App\Providers\JetstreamServiceProvider::class,
    App\Providers\WebPushServiceProvider::class,
    Lab404\Impersonate\ImpersonateServiceProvider::class,
    Laravel\Socialite\SocialiteServiceProvider::class,
    App\Providers\WebPushServiceProvider::class,
    NotificationChannels\WebPush\WebPushServiceProvider::class,
];
