<?php
// app/Services/Ai/OpenRouterService.php

namespace App\Services\Ai;

class OpenRouterService extends BaseAiService
{
    public function __construct(?string $model = null)
    {
        parent::__construct($model);
    }

    public function getProviderCode(): string
    {
        return 'openrouter';
    }

    protected function getDefaultBaseUrl(): string
    {
        return 'https://openrouter.ai/api/v1/chat/completions';
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

    protected function getHeaders(): array
    {
        // Use minimal headers like your working test
        return [
            'Authorization' => 'Bearer ' . $this->provider->api_key,
            'Content-Type' => 'application/json',
        ];
    }

    protected function getSiteUrl(): string
    {
        return $this->provider->getConfigValue('site_url') ??
               config('app.url') ??
               'https://tutorapp.test';
    }

    protected function getAppName(): string
    {
        return $this->provider->getConfigValue('app_name') ??
               config('app.name') ??
               'Your App Name';
    }
}
