<?php
// app/Http/Controllers/Admin/AiProviderController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiProvider;
use App\Models\AiUsageLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Http;

class AiProviderController extends Controller
{
    public function index()
    {
        $providers = AiProvider::withCount(['usageLogs as successful_requests_count' => function ($query) {
                $query->where('success', true);
            }])
            ->withCount(['usageLogs as failed_requests_count' => function ($query) {
                $query->where('success', false);
            }])
            ->withSum('usageLogs as total_cost', 'cost')
            ->get();

        $usageStats = AiUsageLog::selectRaw('
                ai_provider_id,
                SUM(prompt_tokens) as total_prompt_tokens,
                SUM(completion_tokens) as total_completion_tokens,
                SUM(total_tokens) as total_tokens,
                COUNT(*) as total_requests
            ')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('ai_provider_id')
            ->get()
            ->keyBy('ai_provider_id');

        return Inertia::render('Admin/AiProviders/Index', [
            'providers' => $providers->map(function ($provider) use ($usageStats) {
                $stats = $usageStats->get($provider->id);
                return [
                    'id' => $provider->id,
                    'name' => $provider->name,
                    'code' => $provider->code,
                    'is_active' => $provider->is_active,
                    'is_default' => $provider->is_default,
                    'available_models' => $provider->available_models,
                    'successful_requests' => $provider->successful_requests_count,
                    'failed_requests' => $provider->failed_requests_count,
                    'total_cost' => $provider->total_cost,
                    'total_tokens' => $stats->total_tokens ?? 0,
                    'total_requests' => $stats->total_requests ?? 0,
                ];
            }),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/AiProviders/Create', [
            'supported_providers' => [
                'openai' => 'OpenAI',
                'deepseek' => 'DeepSeek',
                'anthropic' => 'Anthropic Claude',
                'google' => 'Google Gemini',
                'openrouter' => 'OpenRouter',
            ],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:ai_providers,code',
            'api_key' => 'required|string',
            'base_url' => 'nullable|url',
            'available_models' => 'required|array|min:1',
            'available_models.*' => 'string',
            'cost_per_token' => 'required|numeric|min:0',
            'max_tokens_per_request' => 'required|integer|min:1',
            'rate_limit_per_minute' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        // If setting as default, remove default from other providers
        if ($request->is_default) {
            AiProvider::where('is_default', true)->update(['is_default' => false]);
        }

        AiProvider::create([
            'name' => $request->name,
            'code' => $request->code,
            'api_key' => $request->api_key,
            'base_url' => $request->base_url,
            'available_models' => $request->available_models,
            'cost_per_token' => $request->cost_per_token,
            'max_tokens_per_request' => $request->max_tokens_per_request,
            'rate_limit_per_minute' => $request->rate_limit_per_minute,
            'is_active' => $request->is_active ?? false,
            'is_default' => $request->is_default ?? false,
        ]);

        return redirect()->route('admin.ai-providers.index')
            ->with('success', 'AI Provider created successfully.');
    }

    public function edit(AiProvider $aiProvider)
    {
        return Inertia::render('Admin/AiProviders/Edit', [
            'provider' => $aiProvider,
            'supported_providers' => [
                'openai' => 'OpenAI',
                'deepseek' => 'DeepSeek',
                'anthropic' => 'Anthropic Claude',
                'google' => 'Google Gemini',
                'openrouter' => 'OpenRouter',
            ],
        ]);
    }

    public function update(Request $request, AiProvider $aiProvider)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:ai_providers,code,' . $aiProvider->id,
            'api_key' => 'required|string',
            'base_url' => 'nullable|url',
            'available_models' => 'required|array|min:1',
            'available_models.*' => 'string',
            'cost_per_token' => 'required|numeric|min:0',
            'max_tokens_per_request' => 'required|integer|min:1',
            'rate_limit_per_minute' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        // If setting as default, remove default from other providers
        if ($request->is_default && !$aiProvider->is_default) {
            AiProvider::where('is_default', true)->update(['is_default' => false]);
        }

        $aiProvider->update([
            'name' => $request->name,
            'code' => $request->code,
            'api_key' => $request->api_key,
            'base_url' => $request->base_url,
            'available_models' => $request->available_models,
            'cost_per_token' => $request->cost_per_token,
            'max_tokens_per_request' => $request->max_tokens_per_request,
            'rate_limit_per_minute' => $request->rate_limit_per_minute,
            'is_active' => $request->is_active ?? $aiProvider->is_active,
            'is_default' => $request->is_default ?? $aiProvider->is_default,
        ]);

        return redirect()->route('admin.ai-providers.index')
            ->with('success', 'AI Provider updated successfully.');
    }

    public function destroy(AiProvider $aiProvider)
    {
        if ($aiProvider->is_default) {
            return redirect()->back()->with('error', 'Cannot delete the default AI provider.');
        }

        if ($aiProvider->usageLogs()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete AI provider with usage history.');
        }

        $aiProvider->delete();

        return redirect()->route('admin.ai-providers.index')
            ->with('success', 'AI Provider deleted successfully.');
    }

    public function testConnection(AiProvider $aiProvider)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $aiProvider->api_key,
                'Content-Type' => 'application/json',
            ])
            ->timeout(10)
            ->post($aiProvider->base_url ?? $this->getDefaultBaseUrl($aiProvider->code), [
                'model' => $aiProvider->available_models[0] ?? 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'user', 'content' => 'Hello, this is a test message.']
                ],
                'max_tokens' => 10,
            ]);

            $success = $response->successful();
            $message = $success ? 'Connection successful!' : 'Connection failed: ' . $response->body();

            return response()->json([
                'success' => $success,
                'message' => $message,
                'status_code' => $response->status(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage(),
                'status_code' => 500,
            ]);
        }
    }

    public function toggleActive(AiProvider $aiProvider)
    {
        $aiProvider->update([
            'is_active' => !$aiProvider->is_active,
        ]);

        $status = $aiProvider->is_active ? 'activated' : 'deactivated';

        return redirect()->back()->with('success', "AI Provider {$status} successfully.");
    }

    public function setDefault(AiProvider $aiProvider)
    {
        // Remove default from other providers
        AiProvider::where('is_default', true)->update(['is_default' => false]);

        // Set this as default
        $aiProvider->update(['is_default' => true]);

        return redirect()->back()->with('success', 'Default AI provider updated successfully.');
    }

    private function getDefaultBaseUrl(string $providerCode): string
    {
        return match($providerCode) {
            'openai' => 'https://api.openai.com/v1/chat/completions',
            'deepseek' => 'https://api.deepseek.com/v1/chat/completions',
            'anthropic' => 'https://api.anthropic.com/v1/messages',
            'google' => 'https://generativelanguage.googleapis.com/v1/models/gemini-pro:generateContent',
            default => 'https://api.openai.com/v1/chat/completions',
        };
    }

    public function getUsageStats(AiProvider $aiProvider, Request $request)
    {
        $period = $request->get('period', '7d');

        switch ($period) {
            case '30d':
                $days = 30;
                break;
            case '90d':
                $days = 90;
                break;
            default:
                $days = 7;
        }

        $usageData = AiUsageLog::where('ai_provider_id', $aiProvider->id)
            ->where('created_at', '>=', now()->subDays($days))
            ->selectRaw('
                DATE(created_at) as date,
                SUM(prompt_tokens) as prompt_tokens,
                SUM(completion_tokens) as completion_tokens,
                SUM(cost) as cost,
                COUNT(*) as requests,
                SUM(CASE WHEN success = 1 THEN 1 ELSE 0 END) as successful_requests,
                SUM(CASE WHEN success = 0 THEN 1 ELSE 0 END) as failed_requests
            ')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $usageByPurpose = AiUsageLog::where('ai_provider_id', $aiProvider->id)
            ->where('created_at', '>=', now()->subDays($days))
            ->selectRaw('
                purpose,
                SUM(cost) as total_cost,
                COUNT(*) as total_requests,
                AVG(CASE WHEN success = 1 THEN 1 ELSE 0 END) * 100 as success_rate
            ')
            ->groupBy('purpose')
            ->get();

        return response()->json([
            'daily_usage' => $usageData,
            'usage_by_purpose' => $usageByPurpose,
        ]);
    }
}
