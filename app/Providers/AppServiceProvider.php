<?php
// app/Providers/AppServiceProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\LoginTrackerService;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        // AI Services
        $this->app->register(\App\Providers\AiServiceProvider::class);

        // Course Services
        $this->app->register(\App\Providers\CourseServiceProvider::class);

        // Other services
        $this->app->singleton(\App\Services\ChatService::class, function ($app) {
            return new \App\Services\ChatService($app->make('ai.driver.openai'));
        });

        $this->app->singleton(\App\Services\ProgressTrackingService::class);
        $this->app->singleton(\App\Services\CapstoneGradingService::class, function ($app) {
            return new \App\Services\CapstoneGradingService($app->make('ai.driver.openai'));
        });

        //login services
        $this->app->singleton(LoginTrackerService::class, function ($app) {
            return new LoginTrackerService();
        });
    }

    public function boot()
    {
        if (app()->environment('production')) {
            URL::forceHttps();
        }
    }
}
