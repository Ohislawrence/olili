<?php
// app/Services/Ai/DeepSeekService.php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class DeepSeekService extends BaseAiService
{
    public function getProviderCode(): string
    {
        return 'deepseek';
    }

    protected function getDefaultBaseUrl(): string
    {
        return 'https://api.deepseek.com/v1/chat/completions';
    }

    protected function getBaseUrl(): string
    {
        return $this->provider->base_url ?? $this->getDefaultBaseUrl();
    }

    protected function formatPayload(array $messages, array $parameters = []): array
    {
        return array_merge([
            'model' => $this->model,
            'messages' => $messages,
            'max_tokens' => $parameters['max_tokens'] ?? $this->getMaxTokens(),
            'temperature' => $parameters['temperature'] ?? 0.7,
            'top_p' => $parameters['top_p'] ?? 1.0,
            'stream' => false,
        ], $parameters);
    }

    protected function makeRequest(array $payload): array
    {
        $apiKey = $this->provider->api_key ?? config('services.deepseek.api_key');

        if (!$apiKey) {
            throw new \Exception('DeepSeek API key is not configured.');
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
            ->timeout(120)
            ->read_timeout(120)
            ->connectTimeout(30)
            ->retry(3, 100)
            ->throw()
            ->post($this->getBaseUrl(), $payload);

            return $response->json();

        } catch (RequestException $e) {
            // Log the error for debugging
            \Log::error('DeepSeek API Request Failed:', [
                'url' => $this->getBaseUrl(),
                'error' => $e->getMessage(),
                'payload_size' => strlen(json_encode($payload))
            ]);

            throw new \Exception("DeepSeek API request failed: " . $e->getMessage());
        }
    }

    protected function extractContentFromResponse(array $response): string
    {
        return $response['choices'][0]['message']['content'] ?? '';
    }

    protected function extractUsageFromResponse(array $response): array
    {
        return [
            'prompt_tokens' => $response['usage']['prompt_tokens'] ?? 0,
            'completion_tokens' => $response['usage']['completion_tokens'] ?? 0,
            'total_tokens' => $response['usage']['total_tokens'] ?? 0,
        ];
    }
}
