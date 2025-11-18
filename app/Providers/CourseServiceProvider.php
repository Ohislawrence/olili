<?php
// app/Providers/CourseServiceProvider.php

namespace App\Providers;

use App\Services\CourseGenerationService;
use App\Services\ContentGenerationService;
use App\Services\QuizGenerationService;
use Illuminate\Support\ServiceProvider;

class CourseServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(CourseGenerationService::class, function ($app) {
            return new CourseGenerationService(
                $app->make('ai.driver.openai') // Default driver
            );
        });

        $this->app->singleton(ContentGenerationService::class, function ($app) {
            return new ContentGenerationService(
                $app->make('ai.driver.openai')
            );
        });

        $this->app->singleton(QuizGenerationService::class, function ($app) {
            return new QuizGenerationService(
                $app->make('ai.driver.openai')
            );
        });
    }

    public function boot()
    {
        //
    }
}
