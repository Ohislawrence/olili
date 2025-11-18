<?php
// app/Services/Ai/AiServiceManager.php

namespace App\Services\Ai;

use App\Models\AiProvider;
use Illuminate\Contracts\Foundation\Application;
use InvalidArgumentException;

class AiServiceManager
{
    protected $app;
    protected $drivers = [];

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function driver($driver = null, $model = null)
    {
        $driver = $driver ?: $this->getDefaultDriver();

        // Create a unique key for driver + model combination
        $key = $model ? "{$driver}:{$model}" : $driver;

        if (!isset($this->drivers[$key])) {
            $this->drivers[$key] = $this->createDriver($driver, $model);
        }

        return $this->drivers[$key];
    }

    protected function createDriver($driver, $model = null)
    {
        $method = 'create' . ucfirst($driver) . 'Driver';

        if (method_exists($this, $method)) {
            return $this->$method($model);
        }

        throw new InvalidArgumentException("Driver [$driver] is not supported.");
    }

    protected function createOpenaiDriver($model = null)
    {
        // Validate provider exists and is active
        $provider = AiProvider::where('code', 'openai')->active()->first();

        if (!$provider) {
            throw new InvalidArgumentException("AI Provider [openai] is not configured or active.");
        }

        // Pass only the model name (string) to the constructor
        return new OpenAiService($model);
    }

    protected function createDeepseekDriver($model = null)
    {
        // Validate provider exists and is active
        $provider = AiProvider::where('code', 'deepseek')->active()->first();

        if (!$provider) {
            throw new InvalidArgumentException("AI Provider [deepseek] is not configured or active.");
        }

        // Pass only the model name (string) to the constructor
        return new DeepSeekService($model);
    }

    protected function createOpenrouterDriver($model = null)
    {
        // Validate provider exists and is active
        $provider = AiProvider::where('code', 'openrouter')->active()->first();

        if (!$provider) {
            throw new InvalidArgumentException("AI Provider [openrouter] is not configured or active.");
        }

        // Pass only the model name (string) to the constructor
        return new OpenRouterService($model);
    }

    public function getDefaultDriver()
    {
        $defaultProvider = AiProvider::where('is_default', true)->first();
        return $defaultProvider ? $defaultProvider->code : 'openai';
    }

    public function getAvailableDrivers()
    {
        return AiProvider::active()->pluck('code')->toArray();
    }

    // Helper method to get provider configuration (if needed elsewhere)
    public function getProviderConfig($providerCode)
    {
        $provider = AiProvider::where('code', $providerCode)->first();

        if (!$provider) {
            throw new InvalidArgumentException("AI Provider [$providerCode] not found.");
        }

        return [
            'api_key' => $provider->api_key,
            'base_url' => $provider->base_url,
            'models' => $provider->available_models,
            'max_tokens' => $provider->max_tokens_per_request,
            'cost_per_token' => $provider->cost_per_token,
            'config' => $provider->config,
        ];
    }

    // Method to get available models for a provider
    public function getAvailableModels($providerCode)
    {
        $provider = AiProvider::where('code', $providerCode)->active()->first();

        if (!$provider) {
            return [];
        }

        return $provider->getAvailableModelsArray();
    }

    // Method to get default model for a provider
    public function getDefaultModel($providerCode)
    {
        $models = $this->getAvailableModels($providerCode);
        return !empty($models) ? $models[0] : null;
    }
}
