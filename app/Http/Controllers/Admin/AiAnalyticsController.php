<?php
// app/Http/Controllers/Admin/AiAnalyticsController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiUsageLog;
use App\Models\AiProvider;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class AiAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $query = AiUsageLog::with(['user', 'aiProvider'])
            ->latest();

        // Filter by provider
        if ($request->has('provider') && $request->provider) {
            $query->whereHas('aiProvider', function ($q) use ($request) {
                $q->where('code', $request->provider);
            });
        }

        // Filter by purpose
        if ($request->has('purpose') && $request->purpose) {
            $query->where('purpose', $request->purpose);
        }

        // Filter by success
        if ($request->has('success') && $request->success !== '') {
            $query->where('success', $request->success);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $usageLogs = $query->paginate(25);

        $summary = $this->getUsageSummary($request);

        return Inertia::render('Admin/AiAnalytics/Index', [
            'usage_logs' => $usageLogs,
            'summary' => $summary,
            'filters' => $request->only(['provider', 'purpose', 'success', 'date_from', 'date_to']),
            'providers' => AiProvider::active()->get()->pluck('name', 'code'),
            'purposes' => AiUsageLog::distinct()->pluck('purpose'),
        ]);
    }

    public function show(AiUsageLog $aiUsageLog)
    {
        $aiUsageLog->load(['user', 'aiProvider']);

        return Inertia::render('Admin/AiAnalytics/Show', [
            'usage_log' => $aiUsageLog,
        ]);
    }

    public function getUsageStats(Request $request)
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

        $dailyUsage = AiUsageLog::selectRaw('
                DATE(created_at) as date,
                SUM(prompt_tokens) as prompt_tokens,
                SUM(completion_tokens) as completion_tokens,
                SUM(total_tokens) as total_tokens,
                SUM(cost) as total_cost,
                COUNT(*) as total_requests,
                SUM(CASE WHEN success = 1 THEN 1 ELSE 0 END) as successful_requests
            ')
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $usageByProvider = AiUsageLog::selectRaw('
                ai_provider_id,
                SUM(cost) as total_cost,
                SUM(total_tokens) as total_tokens,
                COUNT(*) as total_requests,
                AVG(CASE WHEN success = 1 THEN 1 ELSE 0 END) * 100 as success_rate
            ')
            ->with('aiProvider')
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('ai_provider_id')
            ->get();

        $usageByPurpose = AiUsageLog::selectRaw('
                purpose,
                SUM(cost) as total_cost,
                COUNT(*) as total_requests,
                AVG(CASE WHEN success = 1 THEN 1 ELSE 0 END) * 100 as success_rate,
                AVG(total_tokens) as avg_tokens_per_request
            ')
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('purpose')
            ->get();

        $costEfficiency = AiUsageLog::selectRaw('
                DATE(created_at) as date,
                SUM(cost) as total_cost,
                SUM(total_tokens) as total_tokens,
                CASE
                    WHEN SUM(total_tokens) > 0 THEN SUM(cost) / SUM(total_tokens) * 1000
                    ELSE 0
                END as cost_per_thousand_tokens
            ')
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'daily_usage' => $dailyUsage,
            'usage_by_provider' => $usageByProvider,
            'usage_by_purpose' => $usageByPurpose,
            'cost_efficiency' => $costEfficiency,
        ]);
    }

    public function getUserUsageStats(Request $request)
    {
        $topUsers = AiUsageLog::selectRaw('
                user_id,
                SUM(cost) as total_cost,
                SUM(total_tokens) as total_tokens,
                COUNT(*) as total_requests,
                AVG(CASE WHEN success = 1 THEN 1 ELSE 0 END) * 100 as success_rate
            ')
            ->with('user')
            ->where('created_at', '>=', now()->subDays(30))
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->orderBy('total_cost', 'desc')
            ->limit(20)
            ->get();

        return response()->json([
            'top_users' => $topUsers,
        ]);
    }

    private function getUsageSummary(Request $request)
    {
        $query = AiUsageLog::query();

        // Apply filters
        if ($request->has('provider') && $request->provider) {
            $query->whereHas('aiProvider', function ($q) use ($request) {
                $q->where('code', $request->provider);
            });
        }

        if ($request->has('purpose') && $request->purpose) {
            $query->where('purpose', $request->purpose);
        }

        if ($request->has('success') && $request->success !== '') {
            $query->where('success', $request->success);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        return $query->selectRaw('
                SUM(cost) as total_cost,
                SUM(prompt_tokens) as total_prompt_tokens,
                SUM(completion_tokens) as total_completion_tokens,
                SUM(total_tokens) as total_tokens,
                COUNT(*) as total_requests,
                SUM(CASE WHEN success = 1 THEN 1 ELSE 0 END) as successful_requests,
                SUM(CASE WHEN success = 0 THEN 1 ELSE 0 END) as failed_requests
            ')
            ->first();
    }

    public function exportUsageData(Request $request)
    {
        $query = AiUsageLog::with(['user', 'aiProvider']);

        // Apply filters
        if ($request->has('provider') && $request->provider) {
            $query->whereHas('aiProvider', function ($q) use ($request) {
                $q->where('code', $request->provider);
            });
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $data = $query->get()->map(function ($log) {
            return [
                'ID' => $log->id,
                'User' => $log->user?->name ?? 'System',
                'AI Provider' => $log->aiProvider->name,
                'Model' => $log->model_used,
                'Purpose' => $log->purpose,
                'Prompt Tokens' => $log->prompt_tokens,
                'Completion Tokens' => $log->completion_tokens,
                'Total Tokens' => $log->total_tokens,
                'Cost' => $log->cost,
                'Success' => $log->success ? 'Yes' : 'No',
                'Error Message' => $log->error_message,
                'Created At' => $log->created_at->format('Y-m-d H:i:s'),
            ];
        });

        $filename = 'ai-usage-export-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');

            // Add headers
            if ($data->isNotEmpty()) {
                fputcsv($file, array_keys($data->first()));
            }

            // Add data
            foreach ($data as $row) {
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
