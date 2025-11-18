<?php
// app/Http/Controllers/Admin/DashboardController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\AiUsageLog;
use App\Models\AiProvider;
use App\Services\LoginTrackerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = $this->getDashboardStats();
        $recentActivity = $this->getRecentActivity();
        $aiUsage = $this->getAiUsageStats();
        $systemHealth = $this->getSystemHealth();
        $loginStats = app(LoginTrackerService::class)->getPlatformLoginStats(7);

        return inertia('Admin/Dashboard', [
            'stats' => $stats,
            'recentActivity' => $recentActivity,
            'aiUsage' => $aiUsage,
            'systemHealth' => $systemHealth,
            'loginStats' => $loginStats,
        ]);
    }

    private function getDashboardStats()
    {
        $totalStudents = User::role('student')->count();
        $totalTutors = User::role('tutor')->count();
        $totalCourses = Course::count();
        $activeCourses = Course::where('status', 'active')->count();

        $today = now()->format('Y-m-d');
        $newStudentsToday = User::role('student')
            ->whereDate('created_at', $today)
            ->count();

        $aiCostToday = AiUsageLog::whereDate('created_at', $today)
            ->sum('cost');

        $completionRate = Course::where('status', 'completed')
            ->count() / max($totalCourses, 1) * 100;

        return [
            'total_students' => $totalStudents,
            'total_tutors' => $totalTutors,
            'total_courses' => $totalCourses,
            'active_courses' => $activeCourses,
            'new_students_today' => $newStudentsToday,
            'ai_cost_today' => round($aiCostToday, 4),
            'completion_rate' => round($completionRate, 1),
        ];
    }

    private function getRecentActivity()
    {
        $recentCourses = Course::with(['studentProfile.user'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'student_name' => $course->studentProfile->user->name,
                    'status' => $course->status,
                    'created_at' => $course->created_at->diffForHumans(),
                ];
            });

        $recentAiUsage = AiUsageLog::with(['user', 'aiProvider'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'user_name' => $log->user?->name ?? 'System',
                    'provider' => $log->aiProvider->name,
                    'purpose' => $log->purpose,
                    'cost' => $log->cost,
                    'created_at' => $log->created_at->diffForHumans(),
                ];
            });

        return [
            'recent_courses' => $recentCourses,
            'recent_ai_usage' => $recentAiUsage,
        ];
    }

    private function getAiUsageStats()
    {
        $usageByProvider = AiUsageLog::select([
                'ai_provider_id',
                DB::raw('SUM(prompt_tokens) as total_prompt_tokens'),
                DB::raw('SUM(completion_tokens) as total_completion_tokens'),
                DB::raw('SUM(cost) as total_cost'),
                DB::raw('COUNT(*) as total_requests'),
            ])
            ->with('aiProvider')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('ai_provider_id')
            ->get()
            ->map(function ($stat) {
                return [
                    'provider' => $stat->aiProvider->name,
                    'prompt_tokens' => $stat->total_prompt_tokens,
                    'completion_tokens' => $stat->total_completion_tokens,
                    'total_cost' => round($stat->total_cost, 4),
                    'total_requests' => $stat->total_requests,
                ];
            });

        $usageByPurpose = AiUsageLog::select([
                'purpose',
                DB::raw('SUM(cost) as total_cost'),
                DB::raw('COUNT(*) as total_requests'),
            ])
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('purpose')
            ->get()
            ->map(function ($stat) {
                return [
                    'purpose' => $stat->purpose,
                    'total_cost' => round($stat->total_cost, 4),
                    'total_requests' => $stat->total_requests,
                ];
            });

        return [
            'by_provider' => $usageByProvider,
            'by_purpose' => $usageByPurpose,
        ];
    }

    private function getSystemHealth()
    {
        $failedRequests = AiUsageLog::where('success', false)
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        $activeSessions = DB::table('chat_sessions')
            ->where('is_active', true)
            ->where('last_activity_at', '>=', now()->subHours(2))
            ->count();

        $recentErrors = DB::table('failed_jobs')
            ->where('failed_at', '>=', now()->subDays(1))
            ->count();

        return [
            'failed_requests' => $failedRequests,
            'active_sessions' => $activeSessions,
            'recent_errors' => $recentErrors,
            'status' => $failedRequests < 10 && $recentErrors === 0 ? 'healthy' : 'degraded',
        ];
    }

    public function getChartData(Request $request)
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

        $userRegistrations = User::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $courseCreations = Course::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $aiCosts = AiUsageLog::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(cost) as total_cost')
            )
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'user_registrations' => $userRegistrations,
            'course_creations' => $courseCreations,
            'ai_costs' => $aiCosts,
        ]);
    }
}
