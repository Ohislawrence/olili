<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use App\Models\CourseCreationReminder;
use App\Models\Quiz;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TestingController extends Controller
{
    public function test()
    {
        try {
        $response = Http::withoutVerifying()->timeout(60)
            ->withHeaders([
                'Authorization' => 'Bearer ' . env('DEEPSEEK_API_KEY'),
                'Content-Type'  => 'application/json',
            ])
            ->post('https://api.deepseek.com/chat/completions', [
                'model' => 'deepseek-chat',
                'messages' => [
                    ['role' => 'user', 'content' => 'Say hello in one sentence.']
                ],
                'stream' => false,  // IMPORTANT: Prevent cURL error 18
            ]);

        if ($response->failed()) {
            return [
                'status' => 'error',
                'http_code' => $response->status(),
                'body' => $response->body()
            ];
        }

        $data = $response->json();

        return [
            'status' => 'success',
            'reply'  => $data['choices'][0]['message']['content'] ?? null
        ];

    } catch (\Exception $e) {
        return [
            'status' => 'exception',
            'message' => $e->getMessage(),
        ];
    }
    }
}
