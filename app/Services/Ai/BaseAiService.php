<?php
// app/Services/Ai/BaseAiService.php

namespace App\Services\Ai;

use App\Models\AiUsageLog;
use App\Models\AiProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

abstract class BaseAiService
{
    protected $provider;
    protected $model;

    public function __construct(?string $model = null)
    {
        // Get the provider by code
        $this->provider = AiProvider::where('code', 'deepseek')
            ->active()
            ->firstOrFail();

        // Set the model - use provided model, default from provider, or first available model
        $this->model = $model ?? $this->getDefaultModel();

        // Validate the model is available
        $this->validateModel($this->model);
    }

    abstract public function getProviderCode(): string;

    abstract protected function formatPayload(array $messages, array $parameters = []): array;
    abstract protected function extractContentFromResponse(array $response): string;
    abstract protected function extractUsageFromResponse(array $response): array;

    public function setModel(string $model): self
    {
        $this->validateModel($model);
        $this->model = $model;
        return $this;
    }

    protected function validateModel(string $model): void
    {
        $availableModels = $this->getAvailableModels();

        // Ensure $availableModels is always an array
        if (!is_array($availableModels)) {
            Log::warning("Available models is not an array for provider [{$this->provider->code}], converting from: " . gettype($availableModels));
            $availableModels = (array) $availableModels;
        }

        if (!in_array($model, $availableModels)) {
            throw new \InvalidArgumentException(
                "Model [$model] is not available for provider [{$this->provider->code}]. " .
                "Available models: " . implode(', ', $availableModels)
            );
        }
    }

    protected function getDefaultModel(): string
    {
        $availableModels = $this->getAvailableModels();

        // Ensure $availableModels is always an array
        if (!is_array($availableModels)) {
            $availableModels = (array) $availableModels;
        }

        if (empty($availableModels)) {
            throw new \RuntimeException("No available models configured for provider [{$this->provider->code}]");
        }

        return $availableModels[0];
    }

    public function getAvailableModels(): array
    {
        $models = $this->provider->getAvailableModelsArray();

        // Double-check that we're returning an array
        if (!is_array($models)) {
            Log::error("getAvailableModelsArray() returned non-array for provider [{$this->provider->code}]: " . gettype($models));
            return [];
        }

        return $models;
    }

    // ... rest of your methods remain the same
    public function chat(array $messages, array $parameters = [], string $purpose = 'chat'): string
    {
        $usageLog = $this->createUsageLog($purpose);

        try {
            $payload = $this->formatPayload($messages, $parameters);

            $response = Http::withHeaders($this->getHeaders())
                ->timeout($this->getTimeout())
                ->withoutVerifying()
                ->post($this->getBaseUrl(), $payload);

            if (!$response->successful()) {
                throw new \Exception("API request failed: " . $response->body());
            }

            $responseData = $response->json();
            $content = $this->extractContentFromResponse($responseData);
            $usage = $this->extractUsageFromResponse($responseData);

            $cost = $this->calculateCost($usage['prompt_tokens'], $usage['completion_tokens']);

            $usageLog->logSuccess(
                $usage['prompt_tokens'],
                $usage['completion_tokens'],
                $cost,
                ['response' => $responseData]
            );

            return $content;

        } catch (\Exception $e) {
            $usageLog->logFailure($e->getMessage(), ['payload' => $payload ?? []]);
            throw $e;
        }
    }

    protected function getHeaders(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->provider->api_key,
            'Content-Type' => 'application/json',
        ];
    }

    protected function getBaseUrl(): string
    {
        return $this->provider->base_url ?? $this->getDefaultBaseUrl();
    }

    abstract protected function getDefaultBaseUrl(): string;

    protected function getTimeout(): int
    {
        return $this->provider->getConfigValue('timeout', 120);
    }

    protected function createUsageLog(string $purpose): AiUsageLog
    {
        return AiUsageLog::create([
            'ai_provider_id' => $this->provider->id,
            'user_id' => auth()->id(),
            'model_used' => $this->model,
            'endpoint' => 'chat',
            'purpose' => $purpose,
            'request_metadata' => [
                'model' => $this->model,
                'provider' => $this->provider->code,
                'provider_name' => $this->provider->name,
            ],
        ]);
    }

    protected function calculateCost(int $promptTokens, int $completionTokens): float
    {
        return $this->provider->calculateCost($promptTokens, $completionTokens);
    }

    public function getMaxTokens(): int
    {
        return $this->provider->max_tokens_per_request ?? 4000;
    }

    public function getCurrentModel(): string
    {
        return $this->model;
    }

    public function getProvider(): AiProvider
    {
        return $this->provider;
    }

    public function isConfigured(): bool
    {
        return $this->provider->isConfigured();
    }

    // Helper method to get a default provider service
    public static function getDefaultService(string $providerCode, ?string $model = null): self
    {
        // This would need to be implemented based on your service container
        // For example, using Laravel's service container
        $provider = AiProvider::where('code', $providerCode)
            ->active()
            ->first();

        if (!$provider) {
            throw new \RuntimeException("No active provider found for code: $providerCode");
        }

        // You'll need to map provider codes to service classes
        $serviceMap = [
            'openai' => \App\Services\Ai\OpenAiService::class,
            'deepseek' => \App\Services\Ai\DeepSeekService::class,
            // Add other providers
        ];

        $serviceClass = $serviceMap[$providerCode] ?? null;

        if (!$serviceClass) {
            throw new \RuntimeException("No service class mapped for provider: $providerCode");
        }

        return new $serviceClass($model);
    }
}
