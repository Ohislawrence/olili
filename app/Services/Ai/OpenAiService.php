<?php
// app/Services/Ai/OpenAiService.php

namespace App\Services\Ai;

class OpenAiService extends BaseAiService
{
    // Updated constructor to match BaseAiService changes
    public function __construct(?string $model = null)
    {
        parent::__construct($model);
    }

    public function getProviderCode(): string
    {
        return 'openai';
    }

    protected function getDefaultBaseUrl(): string
    {
        return 'https://api.openai.com/v1/chat/completions';
    }

    protected function formatPayload(array $messages, array $parameters = []): array
    {
        return array_merge([
            'model' => $this->model,
            'messages' => $messages,
            'max_tokens' => $parameters['max_tokens'] ?? $this->getMaxTokens(),
            'temperature' => $parameters['temperature'] ?? 0.7,
            'top_p' => $parameters['top_p'] ?? 1.0,
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
