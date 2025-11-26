<?php
// app/Services/Ai/DeepSeekService.php

namespace App\Services\Ai;

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
