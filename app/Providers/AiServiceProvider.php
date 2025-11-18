<?php
// app/Providers/AiServiceProvider.php

namespace App\Providers;

use App\Services\Ai\AiServiceManager;
use Illuminate\Support\ServiceProvider;

class AiServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(AiServiceManager::class, function ($app) {
            return new AiServiceManager($app);
        });

        // Register AI drivers
        $this->app->singleton('ai.driver.openai', function ($app) {
            return $app->make(AiServiceManager::class)->driver('openai');
        });

        $this->app->singleton('ai.driver.deepseek', function ($app) {
            return $app->make(AiServiceManager::class)->driver('deepseek');
        });
    }

    public function boot()
    {
        //
    }
}
