<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TestingController extends Controller
{
    public function test()
    {
        $subscriptionData = [
            'user_id' => auth()->user()->id,
            'subscription_plan_id' => SubscriptionPlan::where('id', 4)->first()->id,
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addDays(30),
        ];

        // Add trial period for paid plans
        //if ($plan->price > 0) {
         //   $subscriptionData['trial_ends_at'] = now()->addDays(14); // 14-day trial
        //}

        Subscription::create($subscriptionData);

        //$quiz = Quiz::find(1);
        //dd($quiz->courseOutline->module->course->student_profile_id);
        //dd(auth()->user()->studentProfile->id);
        /**
        $model = \App\Models\AiProvider::where('code', 'deepseek')->first();

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $model->api_key,
            'Content-Type' => 'application/json',
        ])->withoutVerifying()->post('https://api.deepseek.com/chat/completions', [
            'model' => 'deepseek-chat',
            'messages' => [
                ['role' => 'user', 'content' => 'Hello, how are you?']
            ],
            'max_tokens' => 100,
            'temperature' => 0.7
        ]);

        dd($response->json());
        */

        /**
         \App\Models\AiProvider::updateOrCreate(
        ['code' => 'openrouter'],
        [
            'name' => 'OpenRouter',
            'code' => 'openrouter',
            'available_models' => [
                'deepseek/deepseek-chat-v3.1:free',
                'openai/gpt-oss-20b:free',
                'openai/gpt-4',
                'openai/gpt-4-turbo',
                'openai/gpt-3.5-turbo',
                'anthropic/claude-3-sonnet',
                'anthropic/claude-3-haiku',
                'anthropic/claude-2',
                'meta-llama/llama-3-70b-instruct',
                'google/gemini-pro',
            ],

         $model = \App\Models\AiProvider::where('code', 'openrouter')->first();
            dd([
                'base_url' => $model->base_url,
                'api_key' => $model->api_key ? 'Set' : 'Missing',
                'is_active' => $model->is_active,
                'available_models' => $model->available_models
            ]);


            'api_key' => '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',
            'base_url' => 'https://openrouter.ai/api/v1',
            'cost_per_token' => 0.000002, // Adjust based on actual costs
            'max_tokens_per_request' => 4000,
            'is_active' => true,
            'is_default' => false,
            'config' => [
                'site_url' => config('app.url'),
                'app_name' => config('app.name'),
                'timeout' => 120,
            ],
        ]
    );


        $apiKey = 'sk-c56ec64188094eac9e8884a3fcda7d6b';
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->withoutVerifying()->post('https://api.deepseek.com/chat/completions', [
            'model' => 'deepseek-chat',
            'messages' => [
                ['role' => 'user', 'content' => 'Hello, how are you?']
            ],
            'max_tokens' => 100,
            'temperature' => 0.7
        ]);

        dd($response->json());
         */
    }
}
