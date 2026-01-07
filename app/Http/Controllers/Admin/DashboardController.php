<?php
// app/Http\Controllers\Admin/DashboardController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\CourseEnrollment;
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
        $totalCourses = Course::where('created_by', 'admin')->count();
        $activeCourses = Course::where('created_by', 'admin')
            ->where('status', 'active')
            ->count();

        $today = now()->format('Y-m-d');
        $newStudentsToday = User::role('student')
            ->whereDate('created_at', $today)
            ->count();

        $aiCostToday = AiUsageLog::whereDate('created_at', $today)
            ->sum('cost');

        // Calculate overall enrollment completion rate
        $totalEnrollments = CourseEnrollment::whereHas('course', function ($q) {
            $q->where('created_by', 'admin');
        })->count();

        $completedEnrollments = CourseEnrollment::whereHas('course', function ($q) {
            $q->where('created_by', 'admin');
        })->where('status', 'completed')->count();

        $completionRate = $totalEnrollments > 0
            ? ($completedEnrollments / $totalEnrollments) * 100
            : 0;

        // New stats for enrollment system
        $totalEnrollmentsCount = CourseEnrollment::count();
        $activeEnrollmentsCount = CourseEnrollment::whereIn('status', ['enrolled', 'active'])->count();
        $avgEnrollmentProgress = round(CourseEnrollment::avg('progress_percentage') ?? 0, 1);
        $publicCoursesCount = Course::where('created_by', 'admin')
            ->where('is_public', true)
            ->where('visibility', 'public')
            ->count();

        return [
            'total_students' => $totalStudents,
            'total_tutors' => $totalTutors,
            'total_courses' => $totalCourses,
            'active_courses' => $activeCourses,
            'public_courses' => $publicCoursesCount,
            'new_students_today' => $newStudentsToday,
            'ai_cost_today' => round($aiCostToday, 4),
            'completion_rate' => round($completionRate, 1),
            'total_enrollments' => $totalEnrollmentsCount,
            'active_enrollments' => $activeEnrollmentsCount,
            'avg_enrollment_progress' => $avgEnrollmentProgress,
        ];
    }

    private function getRecentActivity()
    {
        // Recent admin-created courses
        $recentCourses = Course::with(['creator', 'examBoard'])
            ->where('created_by', 'admin')
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'code' => $course->code,
                    'creator_name' => $course->creator?->name ?? 'System',
                    'status' => $course->status,
                    'enrollment_count' => $course->current_enrollment,
                    'created_at' => $course->created_at->diffForHumans(),
                ];
            });

        // Recent enrollments
        $recentEnrollments = CourseEnrollment::with(['course', 'user'])
            ->whereHas('course', function ($q) {
                $q->where('created_by', 'admin');
            })
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($enrollment) {
                return [
                    'id' => $enrollment->id,
                    'course_title' => $enrollment->course->title,
                    'student_name' => $enrollment->user->name,
                    'status' => $enrollment->status,
                    'progress' => $enrollment->progress_percentage,
                    'enrolled_at' => $enrollment->enrolled_at->diffForHumans(),
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
            'recent_enrollments' => $recentEnrollments,
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

        // Check for courses with enrollment issues
        $coursesNearCapacity = Course::where('created_by', 'admin')
            ->where('is_public', true)
            ->whereNotNull('enrollment_limit')
            ->whereRaw('current_enrollment >= enrollment_limit * 0.9')
            ->count();

        // Check for inactive enrollments (no activity in 7 days)
        $staleEnrollments = CourseEnrollment::where('status', 'active')
            ->where('last_accessed_at', '<', now()->subDays(7))
            ->count();

        $healthStatus = 'healthy';
        if ($failedRequests > 10 || $recentErrors > 5 || $staleEnrollments > 50) {
            $healthStatus = 'degraded';
        }
        if ($failedRequests > 50 || $recentErrors > 20 || $staleEnrollments > 200) {
            $healthStatus = 'critical';
        }

        return [
            'failed_requests' => $failedRequests,
            'active_sessions' => $activeSessions,
            'recent_errors' => $recentErrors,
            'courses_near_capacity' => $coursesNearCapacity,
            'stale_enrollments' => $staleEnrollments,
            'status' => $healthStatus,
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

        // User registrations (students)
        $userRegistrations = User::role('student')->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Admin course creations
        $courseCreations = Course::where('created_by', 'admin')->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Course enrollments
        $courseEnrollments = CourseEnrollment::select(
                DB::raw('DATE(enrolled_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('enrolled_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Course completions
        $courseCompletions = CourseEnrollment::select(
                DB::raw('DATE(completed_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('completed_at', '>=', now()->subDays($days))
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

        // Popular courses (by enrollment)
        $popularCourses = Course::where('created_by', 'admin')
            ->withCount('enrollments')
            ->orderBy('enrollments_count', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($course) {
                return [
                    'title' => $course->title,
                    'enrollments' => $course->enrollments_count,
                    'progress_rate' => $course->enrollments()->count() > 0
                        ? round($course->completedEnrollments()->count() / $course->enrollments()->count() * 100, 1)
                        : 0,
                ];
            });

        return response()->json([
            'user_registrations' => $userRegistrations,
            'course_creations' => $courseCreations,
            'course_enrollments' => $courseEnrollments,
            'course_completions' => $courseCompletions,
            'ai_costs' => $aiCosts,
            'popular_courses' => $popularCourses,
        ]);
    }

    // New method for enrollment analytics
    public function getEnrollmentAnalytics(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        // Enrollment growth
        $enrollmentGrowth = CourseEnrollment::select(
                DB::raw('DATE(enrolled_at) as date'),
                DB::raw('COUNT(*) as enrollments')
            )
            ->whereBetween('enrolled_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Completion rates by course
        $completionByCourse = Course::where('created_by', 'admin')
            ->withCount(['enrollments', 'completedEnrollments'])
            ->having('enrollments_count', '>', 0)
            ->orderBy('enrollments_count', 'desc')
            ->limit(15)
            ->get()
            ->map(function ($course) {
                return [
                    'course' => $course->title,
                    'enrollments' => $course->enrollments_count,
                    'completions' => $course->completed_enrollments_count,
                    'completion_rate' => round(($course->completed_enrollments_count / $course->enrollments_count) * 100, 1),
                    'avg_progress' => round($course->enrollments()->avg('progress_percentage') ?? 0, 1),
                ];
            });

        // Enrollment status distribution
        $statusDistribution = CourseEnrollment::select(
                'status',
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween('enrolled_at', [$startDate, $endDate])
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->status => $item->count];
            });

        // Top performing students
        $topStudents = User::role('student')
            ->withCount(['courseEnrollments', 'courseEnrollments as completed_enrollments_count' => function ($q) {
                $q->where('status', 'completed');
            }])
            ->withSum('courseEnrollments as total_study_time', 'actual_duration_hours')
            ->having('course_enrollments_count', '>', 0)
            ->orderBy('completed_enrollments_count', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($user) {
                return [
                    'name' => $user->name,
                    'email' => $user->email,
                    'total_courses' => $user->course_enrollments_count,
                    'completed_courses' => $user->completed_enrollments_count,
                    'completion_rate' => round(($user->completed_enrollments_count / $user->course_enrollments_count) * 100, 1),
                    'total_study_hours' => round($user->total_study_time ?? 0, 1),
                ];
            });

        return response()->json([
            'enrollment_growth' => $enrollmentGrowth,
            'completion_by_course' => $completionByCourse,
            'status_distribution' => $statusDistribution,
            'top_students' => $topStudents,
        ]);
    }
}
