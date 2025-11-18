<?php
// app/Http/Controllers/Admin/SystemSettingsController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SystemSettingsController extends Controller
{


    public function index()
    {
        try {
            $systemInfo = $this->getSystemInfo();
            $aiSettings = $this->getAiSettings();
            $courseSettings = $this->getCourseSettings();

            return Inertia::render('Admin/Settings/Index', [
                'system_info' => $systemInfo,
                'ai_settings' => $aiSettings,
                'course_settings' => $courseSettings,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to load system settings page: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load system settings.');
        }
    }

    public function updateAiSettings(Request $request)
    {
        $validated = $request->validate([
            'default_provider' => 'required|string|in:openai,anthropic,deepseek,azure',
            'max_tokens_per_request' => 'required|integer|min:100|max:32000',
            'default_temperature' => 'required|numeric|between:0,2',
            'cost_alert_threshold' => 'required|numeric|min:0|max:10000',
            'enable_cost_tracking' => 'boolean',
            'auto_switch_provider' => 'boolean',
        ]);

        try {
            // Use Laravel's config helper or a settings table
            settings()->set([
                'ai.default_provider' => $validated['default_provider'],
                'ai.max_tokens_per_request' => $validated['max_tokens_per_request'],
                'ai.default_temperature' => $validated['default_temperature'],
                'ai.cost_alert_threshold' => $validated['cost_alert_threshold'],
                'ai.enable_cost_tracking' => $validated['enable_cost_tracking'] ?? false,
                'ai.auto_switch_provider' => $validated['auto_switch_provider'] ?? false,
            ]);

            // Clear relevant caches
            Cache::forget('ai_settings');
            Cache::forget('available_ai_providers');

            Log::info('AI settings updated by user: ' . auth()->id());

            return redirect()->back()->with('success', 'AI settings updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update AI settings: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update AI settings.');
        }
    }

    public function updateCourseSettings(Request $request)
    {
        $validated = $request->validate([
            'auto_generate_content' => 'boolean',
            'enable_ai_grading' => 'boolean',
            'max_weekly_study_hours' => 'required|integer|min:1|max:168',
            'default_course_duration_weeks' => 'required|integer|min:1|max:104',
            'enable_progress_tracking' => 'boolean',
            'enable_chat_restrictions' => 'boolean',
        ]);

        try {
            settings()->set([
                'courses.auto_generate_content' => $validated['auto_generate_content'] ?? true,
                'courses.enable_ai_grading' => $validated['enable_ai_grading'] ?? true,
                'courses.max_weekly_study_hours' => $validated['max_weekly_study_hours'],
                'courses.default_course_duration_weeks' => $validated['default_course_duration_weeks'],
                'courses.enable_progress_tracking' => $validated['enable_progress_tracking'] ?? true,
                'courses.enable_chat_restrictions' => $validated['enable_chat_restrictions'] ?? true,
            ]);

            Cache::forget('course_settings');

            Log::info('Course settings updated by user: ' . auth()->id());

            return redirect()->back()->with('success', 'Course settings updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update course settings: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update course settings.');
        }
    }

    public function clearCache(Request $request)
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');

            // Clear application-specific caches
            Cache::flush();

            Log::info('System cache cleared by user: ' . auth()->id());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'System cache cleared successfully.'
                ]);
            }

            return redirect()->back()->with('success', 'System cache cleared successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to clear cache: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to clear system cache.'
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to clear system cache.');
        }
    }

    public function runMaintenance(Request $request)
    {
        try {
            // Restart queue workers
            Artisan::call('queue:restart');

            // Run scheduled tasks
            Artisan::call('schedule:run');

            // Run optimization commands in production
            if (app()->environment('production')) {
                Artisan::call('config:cache');
                Artisan::call('route:cache');
                Artisan::call('view:cache');
            }

            Log::info('Maintenance tasks executed by user: ' . auth()->id());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Maintenance tasks executed successfully.'
                ]);
            }

            return redirect()->back()->with('success', 'Maintenance tasks executed successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to run maintenance: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to execute maintenance tasks.'
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to execute maintenance tasks.');
        }
    }

    public function getSystemLogs(Request $request)
    {
        try {
            $request->validate([
                'type' => 'sometimes|in:laravel,ai,jobs,http',
                'lines' => 'sometimes|integer|min:10|max:1000',
            ]);

            $logType = $request->get('type', 'laravel');
            $lines = $request->get('lines', 100);

            $logPath = $this->getLogPath($logType);

            if (!file_exists($logPath) || !is_readable($logPath)) {
                return response()->json([
                    'logs' => ['Log file not found or not readable.']
                ]);
            }

            $logs = $this->tailCustom($logPath, $lines);

            return response()->json([
                'logs' => $logs,
                'file' => basename($logPath),
                'last_modified' => filemtime($logPath),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve system logs: ' . $e->getMessage());
            return response()->json([
                'logs' => ['Error retrieving log file.']
            ], 500);
        }
    }

    public function getSystemHealth(Request $request)
    {
        try {
            $health = [
                'database' => $this->checkDatabaseConnection(),
                'cache' => $this->checkCacheConnection(),
                'storage' => $this->checkStorage(),
                'queue' => $this->checkQueueStatus(),
                'last_cron_run' => Cache::get('last_cron_run'),
            ];

            return response()->json($health);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to check system health'
            ], 500);
        }
    }

    private function getSystemInfo(): array
    {
        return [
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'database_driver' => config('database.default'),
            'database_version' => $this->getDatabaseVersion(),
            'cache_driver' => config('cache.default'),
            'queue_driver' => config('queue.default'),
            'timezone' => config('app.timezone'),
            'environment' => app()->environment(),
            'debug_mode' => config('app.debug'),
            'maintenance_mode' => app()->isDownForMaintenance(),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
        ];
    }

    private function getAiSettings(): array
    {
        // Fallback to config if settings package not available
        if (function_exists('settings')) {
            return [
                'default_provider' => settings('ai.default_provider', 'openai'),
                'max_tokens_per_request' => settings('ai.max_tokens_per_request', 4000),
                'default_temperature' => settings('ai.default_temperature', 0.7),
                'cost_alert_threshold' => settings('ai.cost_alert_threshold', 100),
                'enable_cost_tracking' => settings('ai.enable_cost_tracking', true),
                'auto_switch_provider' => settings('ai.auto_switch_provider', false),
            ];
        }

        return [
            'default_provider' => config('ai.default_provider', 'openai'),
            'max_tokens_per_request' => config('ai.max_tokens_per_request', 4000),
            'default_temperature' => config('ai.default_temperature', 0.7),
            'cost_alert_threshold' => config('ai.cost_alert_threshold', 100),
            'enable_cost_tracking' => config('ai.enable_cost_tracking', true),
            'auto_switch_provider' => config('ai.auto_switch_provider', false),
        ];
    }

    private function getCourseSettings(): array
    {
        // Fallback to config if settings package not available
        if (function_exists('settings')) {
            return [
                'auto_generate_content' => settings('courses.auto_generate_content', true),
                'enable_ai_grading' => settings('courses.enable_ai_grading', true),
                'max_weekly_study_hours' => settings('courses.max_weekly_study_hours', 20),
                'default_course_duration_weeks' => settings('courses.default_course_duration_weeks', 12),
                'enable_progress_tracking' => settings('courses.enable_progress_tracking', true),
                'enable_chat_restrictions' => settings('courses.enable_chat_restrictions', true),
            ];
        }

        return [
            'auto_generate_content' => config('courses.auto_generate_content', true),
            'enable_ai_grading' => config('courses.enable_ai_grading', true),
            'max_weekly_study_hours' => config('courses.max_weekly_study_hours', 20),
            'default_course_duration_weeks' => config('courses.default_course_duration_weeks', 12),
            'enable_progress_tracking' => config('courses.enable_progress_tracking', true),
            'enable_chat_restrictions' => config('courses.enable_chat_restrictions', true),
        ];
    }

    private function getLogPath(string $type): string
    {
        return match($type) {
            'laravel' => storage_path('logs/laravel.log'),
            'ai' => storage_path('logs/ai.log'),
            'jobs' => storage_path('logs/queue.log'),
            'http' => storage_path('logs/http.log'),
            default => storage_path('logs/laravel.log'),
        };
    }

    private function tailCustom(string $filepath, int $lines = 100): array
    {
        if (!file_exists($filepath)) {
            return ['Log file not found.'];
        }

        $file = new \SplFileObject($filepath, 'r');
        $file->seek(PHP_INT_MAX);
        $lastLine = $file->key();

        $lines = min($lastLine, $lines);
        $file->seek(max(0, $lastLine - $lines));

        $logContent = [];
        while (!$file->eof() && $lines > 0) {
            $line = $file->current();
            if ($line !== false) {
                $logContent[] = rtrim($line);
            }
            $file->next();
            $lines--;
        }

        return array_reverse(array_filter($logContent));
    }

    private function getDatabaseVersion(): string
    {
        try {
            $result = DB::select('SELECT VERSION() as version');
            return $result[0]->version ?? 'Unknown';
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    private function checkDatabaseConnection(): bool
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function checkCacheConnection(): bool
    {
        try {
            Cache::get('health_check');
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function checkStorage(): bool
    {
        try {
            Storage::disk('local')->put('health_check.txt', 'test');
            Storage::disk('local')->delete('health_check.txt');
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function checkQueueStatus(): bool
    {
        try {
            // Simple queue check
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
