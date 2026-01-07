<?php
// app/Http/Controllers/Admin/UserController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\StudentProfile;
use App\Models\ExamBoard;
use App\Models\LoginHistory;
use App\Models\Certificate;
use App\Services\LoginTrackerService;
use App\Helpers\CertificateHelper;
use Spatie\Permission\Models\Role;
use Lab404\Impersonate\Services\ImpersonateManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Services\ProgressTrackingService;

class UserController extends Controller
{
    protected $loginTrackerService;
    protected $progressService;

    public function __construct(LoginTrackerService $loginTrackerService, ProgressTrackingService $progressService)
    {
        $this->loginTrackerService = $loginTrackerService;
        $this->progressService = $progressService;
    }

    public function index(Request $request)
    {
        // FIXED: Remove courses_count and use enrollments_count instead
        $query = User::with(['roles', 'studentProfile.examBoard'])
            ->withCount(['quizAttempts', 'loginHistories']);

        // We can add enrollments count through a subquery
        $query->addSelect([
            'enrollments_count' => CourseEnrollment::selectRaw('count(*)')
                ->whereColumn('user_id', 'users.id')
        ]);

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhereHas('studentProfile', function ($q) use ($request) {
                      $q->where('current_level', 'like', "%{$request->search}%")
                        ->orWhere('target_level', 'like', "%{$request->search}%");
                  });
            });
        }

        // Filter by role
        if ($request->has('role') && $request->role) {
            $query->role($request->role);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'locked') {
                $query->whereNotNull('account_locked_until')
                      ->where('account_locked_until', '>', now());
            }
        }

        // Filter by verification status
        if ($request->has('verified') && $request->verified !== '') {
            if ($request->verified === '1') {
                $query->whereNotNull('email_verified_at');
            } else {
                $query->whereNull('email_verified_at');
            }
        }

        $users = $query->latest()->paginate(20);

        // Manually add enrollments_count to each user
        foreach ($users as $user) {
            $user->enrollments_count = CourseEnrollment::where('user_id', $user->id)->count();
        }

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => $request->only(['search', 'role', 'status', 'verified']),
            'roles' => Role::all()->pluck('name'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Users/Create', [
            'roles' => Role::all()->pluck('name'),
            'exam_boards' => ExamBoard::active()->get(),
            'levels' => ['beginner', 'intermediate', 'advanced', 'expert'],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|string|in:admin,tutor,student',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'bio' => 'nullable|string|max:1000',
            'is_active' => 'boolean',

            // Student profile fields
            'exam_board_id' => 'nullable|exists:exam_boards,id',
            'current_level' => 'required_if:role,student|in:beginner,intermediate,advanced,expert',
            'target_level' => 'required_if:role,student|in:beginner,intermediate,advanced,expert',
            'target_completion_date' => 'required_if:role,student|date|after:today',
            'weekly_study_hours' => 'required_if:role,student|integer|min:1|max:40',
            'current_grade' => 'nullable|numeric|between:0,100',
            'target_grade' => 'nullable|numeric|between:0,100',
            'learning_goals' => 'nullable|array',
            'learning_goals.*' => 'string|max:500',
            'learning_preferences' => 'nullable|array',
        ]);

        \DB::transaction(function () use ($request) {
            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'bio' => $request->bio,
                'is_active' => $request->is_active ?? true,
                'email_verified_at' => $request->email_verified ? now() : null,
            ]);

            // Assign role
            $user->assignRole($request->role);

            // Create student profile if role is student
            if ($request->role === 'student') {
                StudentProfile::create([
                    'user_id' => $user->id,
                    'exam_board_id' => $request->exam_board_id,
                    'current_level' => $request->current_level,
                    'target_level' => $request->target_level,
                    'target_completion_date' => $request->target_completion_date,
                    'weekly_study_hours' => $request->weekly_study_hours,
                    'current_grade' => $request->current_grade,
                    'target_grade' => $request->target_grade,
                    'learning_goals' => $request->learning_goals,
                    'learning_preferences' => $request->learning_preferences,
                ]);
            }
        });

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        // Load the user with correct relationships
        $user->load([
            'roles',
            'studentProfile.examBoard',
            'courseEnrollments.course',
            'courseEnrollments.course.creator',
            'courseEnrollments.course.examBoard',
            'certificates' => function ($query) {
                $query->with('course')->latest()->limit(5);
            },
            'currentSubscription.plan',
            'activeSubscription.plan',
            'subscriptions.plan',
            'loginHistories' => function ($query) {
                $query->latest()->limit(5);
            },
            'quizAttempts.quiz',
            'progressTracking',
        ]);

        // Calculate enrollment statistics
        $enrollmentStats = $this->getUserEnrollmentStats($user);

        // Get certificate stats
        $certificateStats = $this->calculateCertificateStats($user);

        // Get login stats
        $loginStats = $this->loginTrackerService->getUserLoginStats($user);

        // Get overall statistics
        $stats = array_merge($enrollmentStats, $certificateStats, [
            'total_quiz_attempts' => $user->quizAttempts->count(),
            'average_quiz_score' => round($user->quizAttempts->avg('percentage') ?? 0, 1),
            'total_logins' => $user->loginHistories->count(),
            'total_chat_sessions' => $user->chatSessions()->count() ?? 0,
        ]);

        // Get recent enrollments
        $recentEnrollments = $user->courseEnrollments()
            ->with('course')
            ->latest()
            ->limit(5)
            ->get();

        return Inertia::render('Admin/Users/Show', [
            'user' => $user,
            'stats' => $stats,
            'loginStats' => $loginStats,
            'recentEnrollments' => $recentEnrollments,
        ]);
    }

    private function getUserEnrollmentStats(User $user): array
    {
        $enrollments = $user->courseEnrollments;
        $studyData = $this->progressService->getLearningAnalytics($user);

        // Extract enrollment statistics
        $stats = [
            'total_enrollments' => $enrollments->count(),
            'active_enrollments' => $enrollments->whereIn('status', ['enrolled', 'active'])->count(),
            'completed_enrollments' => $enrollments->where('status', 'completed')->count(),
            'dropped_enrollments' => $enrollments->where('status', 'dropped')->count(),

            // Study time statistics
            'total_study_hours' => $studyData['total_study_time_hours'] ?? 0,
            'average_daily_study_hours' => $this->calculateAverageDailyStudy($studyData),
            'total_study_minutes' => $studyData['total_study_time_hours'] * 60,

            // Completion statistics
            'completed_items' => $studyData['completed_items'] ?? 0,
            'completed_topics' => $studyData['completed_topics'] ?? 0,
            'average_score' => $studyData['average_score'] ?? 0,

            // Activity statistics
            'active_courses' => $studyData['active_courses'] ?? 0,
            'streak_days' => $studyData['streak_days'] ?? 0,
            'weekly_activity_days' => $this->getWeeklyActivityDays($studyData),

            // Efficiency metrics
            'efficiency_score' => $studyData['efficiency_metrics']['efficiency_score'] ?? 0,
            'minutes_per_item' => $studyData['efficiency_metrics']['minutes_per_item'] ?? 0,
            'completion_rate' => $studyData['efficiency_metrics']['completion_rate'] ?? 0,

            // Course progress overview
            'course_progress' => $studyData['course_progress'] ?? [],
            'average_course_completion' => $this->calculateAverageCourseCompletion($studyData),

            // Enrollment completion rate
            'enrollment_completion_rate' => 0,
            'last_active_date' => $this->getLastActiveDate($user),
            'enrollment_status_distribution' => $this->getEnrollmentStatusDistribution($enrollments),
        ];

        // Calculate enrollment completion rate
        if ($stats['total_enrollments'] > 0) {
            $stats['enrollment_completion_rate'] = round(($stats['completed_enrollments'] / $stats['total_enrollments']) * 100, 1);
        }

        // Add detailed progress breakdown
        $stats['progress_breakdown'] = $this->getProgressBreakdown($studyData, $enrollments);

        // Add weekly progress data
        $stats['weekly_progress'] = $studyData['weekly_progress'] ?? [];
        $stats['weekly_study_hours'] = $this->calculateWeeklyStudyHours($studyData);

        // Add time-based metrics
        $stats['estimated_remaining_hours'] = $this->calculateTotalRemainingHours($studyData);
        $stats['study_efficiency_rating'] = $this->getEfficiencyRating($stats['efficiency_score']);

        return $stats;
    }

    /**
     * Helper method to calculate average daily study hours
     */
    private function calculateAverageDailyStudy(array $studyData): float
    {
        if (empty($studyData['weekly_progress'])) {
            return 0;
        }

        $weeklyHours = array_sum(array_column($studyData['weekly_progress'], 'study_time_hours'));
        return round($weeklyHours / min(7, count($studyData['weekly_progress'])), 1);
    }

    /**
     * Helper method to get number of active days in the past week
     */
    private function getWeeklyActivityDays(array $studyData): int
    {
        if (empty($studyData['weekly_progress'])) {
            return 0;
        }

        return count(array_filter($studyData['weekly_progress'], function($day) {
            return ($day['study_time_minutes'] ?? 0) > 0;
        }));
    }

    /**
     * Helper method to calculate average course completion percentage
     */
    private function calculateAverageCourseCompletion(array $studyData): float
    {
        if (empty($studyData['course_progress'])) {
            return 0;
        }

        $totalCompletion = array_sum(array_column($studyData['course_progress'], 'overall_completion'));
        return round($totalCompletion / count($studyData['course_progress']), 1);
    }

    /**
     * Helper method to get last active date
     */
    private function getLastActiveDate(User $user): ?string
    {
        $lastProgress = \App\Models\ProgressTracking::where('user_id', $user->id)
            ->latest('updated_at')
            ->first();

        return $lastProgress ? $lastProgress->updated_at->format('Y-m-d H:i:s') : null;
    }

    /**
     * Helper method to get enrollment status distribution
     */
    private function getEnrollmentStatusDistribution($enrollments): array
    {
        $statusCounts = $enrollments->groupBy('status')->map->count();

        $distribution = [
            'enrolled' => $statusCounts->get('enrolled', 0),
            'active' => $statusCounts->get('active', 0),
            'completed' => $statusCounts->get('completed', 0),
            'dropped' => $statusCounts->get('dropped', 0),
            'pending' => $statusCounts->get('pending', 0),
            'suspended' => $statusCounts->get('suspended', 0),
        ];

        // Calculate percentages
        $total = array_sum($distribution);
        if ($total > 0) {
            foreach ($distribution as $status => $count) {
                $distribution[$status . '_percent'] = round(($count / $total) * 100, 1);
            }
        }

        return $distribution;
    }

    /**
     * Helper method to get detailed progress breakdown
     */
    private function getProgressBreakdown(array $studyData, $enrollments): array
    {
        $breakdown = [
            'by_course' => [],
            'by_module_type' => [],
            'by_time_of_day' => [],
        ];

        // Add course-specific progress
        foreach ($studyData['course_progress'] as $courseProgress) {
            $breakdown['by_course'][] = [
                'course_id' => $courseProgress['course_id'],
                'course_title' => $courseProgress['course_title'],
                'completion' => $courseProgress['overall_completion'],
                'modules_completed' => $courseProgress['module_completion'],
                'topics_completed' => $courseProgress['topic_completion'],
                'time_spent_hours' => $courseProgress['time_spent_hours'],
                'remaining_hours' => $courseProgress['estimated_remaining_hours'],
            ];
        }

        // Add efficiency metrics breakdown
        $breakdown['efficiency_metrics'] = $studyData['efficiency_metrics'] ?? [];

        // Add weekly progress summary
        $breakdown['weekly_summary'] = [
            'total_days_active' => $this->getWeeklyActivityDays($studyData),
            'average_daily_hours' => $this->calculateAverageDailyStudy($studyData),
            'total_weekly_hours' => $this->calculateWeeklyStudyHours($studyData),
            'most_active_day' => $this->getMostActiveDay($studyData),
        ];

        return $breakdown;
    }

    /**
     * Helper method to calculate total weekly study hours
     */
    private function calculateWeeklyStudyHours(array $studyData): float
    {
        if (empty($studyData['weekly_progress'])) {
            return 0;
        }

        return round(array_sum(array_column($studyData['weekly_progress'], 'study_time_hours')), 1);
    }

    /**
     * Helper method to calculate total remaining hours across all courses
     */
    private function calculateTotalRemainingHours(array $studyData): float
    {
        if (empty($studyData['course_progress'])) {
            return 0;
        }

        $totalRemaining = array_sum(array_column($studyData['course_progress'], 'estimated_remaining_hours'));
        return round($totalRemaining, 1);
    }

    /**
     * Helper method to get most active day of the week
     */
    private function getMostActiveDay(array $studyData): ?string
    {
        if (empty($studyData['weekly_progress'])) {
            return null;
        }

        $days = $studyData['weekly_progress'];
        uasort($days, function($a, $b) {
            return ($b['study_time_minutes'] ?? 0) <=> ($a['study_time_minutes'] ?? 0);
        });

        $mostActiveDate = key($days);
        return $mostActiveDate ? date('l', strtotime($mostActiveDate)) : null;
    }

    /**
     * Helper method to get efficiency rating based on score
     */
    private function getEfficiencyRating(float $score): string
    {
        if ($score >= 80) return 'Excellent';
        if ($score >= 60) return 'Good';
        if ($score >= 40) return 'Average';
        if ($score >= 20) return 'Needs Improvement';
        return 'Poor';
    }

    private function calculateCertificateStats(User $user): array
    {
        $totalCertificates = $user->certificates()->count();
        $activeCertificates = $user->certificates()->where('status', 'active')->count();
        $expiredCertificates = $user->certificates()->where('status', 'expired')->count();
        $recentCertificates = $user->certificates()
            ->where('issue_date', '>=', now()->subDays(30))
            ->count();

        $totalDownloads = $user->certificates()->sum('download_count');

        // Calculate certificate rate based on completed enrollments
        $completedEnrollments = $this->getUserEnrollmentStats($user)['completed_enrollments'];
        $completedEnrollmentsWithCertificates = $user->courseEnrollments()
            ->where('status', 'completed')
            ->whereHas('course.certificates', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->count();

        $certificateRate = $completedEnrollments > 0
            ? round(($completedEnrollmentsWithCertificates / $completedEnrollments) * 100, 1)
            : 0;

        return [
            'total_certificates' => $totalCertificates,
            'active_certificates' => $activeCertificates,
            'expired_certificates' => $expiredCertificates,
            'recent_certificates_30d' => $recentCertificates,
            'total_certificate_downloads' => $totalDownloads,
            'completed_enrollments_with_certificates' => $completedEnrollmentsWithCertificates,
            'certificate_rate' => $certificateRate,
        ];
    }

    public function edit(User $user)
    {
        $user->load(['roles', 'studentProfile']);

        return Inertia::render('Admin/Users/Edit', [
            'user' => $user,
            'roles' => Role::all()->pluck('name'),
            'exam_boards' => ExamBoard::active()->get(),
            'levels' => ['beginner', 'intermediate', 'advanced', 'expert'],
            'current_role' => $user->roles->first()->name ?? null,
        ]);
    }

    public function update(Request $request, User $user)
    {
        // ... keep the existing update method unchanged ...
        // (It doesn't reference courses, so it should work)
    }

    // ... other methods like destroy, toggleStatus, unlockAccount, etc.
    // ... keep them as they are since they don't reference courses

    public function enrollments(User $user, Request $request)
    {
        $query = $user->courseEnrollments()
            ->with(['course', 'course.creator', 'course.examBoard'])
            ->latest();

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by course
        if ($request->has('course_id') && $request->course_id) {
            $query->where('course_id', $request->course_id);
        }

        // Filter by progress
        if ($request->has('progress') && $request->progress) {
            if ($request->progress === 'completed') {
                $query->where('progress_percentage', '>=', 100);
            } elseif ($request->progress === 'in_progress') {
                $query->where('progress_percentage', '>', 0)
                      ->where('progress_percentage', '<', 100);
            } elseif ($request->progress === 'not_started') {
                $query->where('progress_percentage', 0);
            }
        }

        $enrollments = $query->paginate(20);

        // Get user's enrolled courses for filter dropdown
        $userCourses = Course::whereHas('enrollments', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->select('id', 'title')
        ->orderBy('title')
        ->get();

        // Enrollment statistics
        $enrollmentStats = $this->getUserEnrollmentStats($user);

        return Inertia::render('Admin/Users/Enrollments', [
            'user' => $user->load('roles'),
            'enrollments' => $enrollments,
            'filters' => $request->only(['status', 'course_id', 'progress']),
            'userCourses' => $userCourses,
            'enrollmentStats' => $enrollmentStats,
            'statuses' => ['enrolled', 'active', 'paused', 'completed', 'dropped'],
        ]);
    }

    public function enrollUserInCourse(Request $request, User $user)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $course = Course::findOrFail($request->course_id);

        // Check if user can enroll
        if (!$user->hasRole('student')) {
            return redirect()->back()
                ->with('error', 'Only students can enroll in courses.');
        }

        if (!$user->studentProfile) {
            return redirect()->back()
                ->with('error', 'User does not have a student profile.');
        }

        if (!$course->canEnroll($user)) {
            return redirect()->back()
                ->with('error', 'User cannot enroll in this course.');
        }

        // Check if already enrolled
        $existingEnrollment = CourseEnrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existingEnrollment) {
            return redirect()->back()
                ->with('error', 'User is already enrolled in this course.');
        }

        // Enroll user
        try {
            $enrollment = $course->enrollStudent($user);

            if (!$enrollment) {
                return redirect()->back()
                    ->with('error', 'Failed to enroll user in course.');
            }

            return redirect()->back()
                ->with('success', "User enrolled in '{$course->title}' successfully.");
        } catch (\Exception $e) {
            \Log::error('Enrollment failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to enroll user: ' . $e->getMessage());
        }
    }

    public function updateEnrollmentStatus(Request $request, User $user, CourseEnrollment $enrollment)
    {
        $request->validate([
            'status' => 'required|in:active,paused,completed,dropped',
        ]);

        // Ensure enrollment belongs to user
        if ($enrollment->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $oldStatus = $enrollment->status;
        $newStatus = $request->status;

        // Update enrollment status
        $enrollment->update(['status' => $newStatus]);

        // If marking as completed, update progress to 100%
        if ($newStatus === 'completed') {
            $enrollment->update([
                'progress_percentage' => 100,
                'completed_at' => now(),
            ]);
        }

        // If marking as dropped, decrement course enrollment count
        if ($newStatus === 'dropped') {
            $enrollment->course()->decrement('current_enrollment');
        }

        return redirect()->back()
            ->with('success', "Enrollment status changed from '{$oldStatus}' to '{$newStatus}'.");
    }

    // ... other methods like certificates, generateCertificate, etc.
    // Update them to use courseEnrollments instead of studentProfile->enrollments

    public function certificates(User $user, Request $request)
    {
        // Get filters from request
        $filters = $request->only(['search', 'status', 'course_id', 'date_from', 'date_to']);

        // Build query for certificates
        $query = Certificate::where('user_id', $user->id)
            ->with(['course', 'organization'])
            ->latest();

        // Apply filters
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('certificate_number', 'like', "%{$filters['search']}%")
                  ->orWhere('title', 'like', "%{$filters['search']}%")
                  ->orWhereHas('course', function ($q) use ($filters) {
                      $q->where('title', 'like', "%{$filters['search']}%");
                  });
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['course_id'])) {
            $query->where('course_id', $filters['course_id']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('issue_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('issue_date', '<=', $filters['date_to']);
        }

        $certificates = $query->paginate(20);

        // Get certificate statistics
        $stats = $this->getUserCertificateStats($user);

        // Get user's enrolled courses for filter dropdown
        $courses = Course::whereHas('enrollments', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->select('id', 'title')
        ->orderBy('title')
        ->get();

        // Get all certificate statuses for filter
        $statuses = ['active', 'expired', 'revoked', 'pending'];

        return Inertia::render('Admin/Users/Certificates', [
            'user' => $user->load('roles'),
            'certificates' => $certificates,
            'filters' => $filters,
            'stats' => $stats,
            'courses' => $courses,
            'statuses' => $statuses,
            'total_certificates' => $stats['total'],
        ]);
    }

    /**
     * Generate certificate for a user
     */
    public function generateCertificate(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
            'organization_id' => 'nullable|exists:organization_profiles,id',
        ]);

        $user = User::findOrFail($request->user_id);
        $course = Course::findOrFail($request->course_id);

        // Check if user has completed enrollment for this course
        $enrollment = $user->courseEnrollments()
            ->where('course_id', $course->id)
            ->where('status', 'completed')
            ->first();

        if (!$enrollment) {
            return redirect()->back()
                ->with('error', 'User must complete the course enrollment to receive a certificate.');
        }

        // Check if certificate already exists
        $existing = Certificate::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->first();

        if ($existing) {
            return redirect()->back()
                ->with('error', 'Certificate already exists for this course.');
        }

        try {
            // Generate certificate using enrollment data
            $certificate = Certificate::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'course_enrollment_id' => $enrollment->id,
                'certificate_number' => $this->generateCertificateNumber(),
                'issue_date' => now(),
                'completion_date' => $enrollment->completed_at,
                'status' => 'active',
                'metadata' => [
                    'enrollment_id' => $enrollment->id,
                    'progress_percentage' => $enrollment->progress_percentage,
                    'total_study_hours' => $enrollment->actual_duration_hours,
                ],
            ]);

            return redirect()->route('admin.users.certificates', $user->id)
                ->with('success', 'Certificate generated successfully!');

        } catch (\Exception $e) {
            \Log::error('Failed to generate certificate: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to generate certificate: ' . $e->getMessage());
        }
    }

    private function generateCertificateNumber(): string
    {
        return 'CERT-' . strtoupper(uniqid()) . '-' . date('Ymd');
    }

    public function usersStats(Request $request)
    {
        // Get filter parameters
        $role = $request->get('role');
        $status = $request->get('status');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        // Base query
        $query = User::with(['roles', 'courseEnrollments.course', 'certificates'])
            ->withCount([
                'courseEnrollments',
                'courseEnrollments as active_enrollments_count' => function ($q) {
                    $q->whereIn('status', ['enrolled', 'active']);
                },
                'courseEnrollments as completed_enrollments_count' => function ($q) {
                    $q->where('status', 'completed');
                },
                'certificates',
                'certificates as active_certificates_count' => function ($q) {
                    $q->where('status', 'active');
                },
                'certificates as expired_certificates_count' => function ($q) {
                    $q->where('status', 'expired');
                },
            ]);

        // Apply filters
        if ($role) {
            $query->whereHas('roles', function ($q) use ($role) {
                $q->where('name', $role);
            });
        }

        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }

        if ($dateFrom) {
            $query->where('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->where('created_at', '<=', $dateTo);
        }

        // Get paginated users
        $users = $query->paginate(20)->withQueryString();

        // Get aggregated statistics
        $stats = $this->getAggregatedStats($role, $status, $dateFrom, $dateTo);

        // Get role distribution
        $roleDistribution = $this->getRoleDistribution();

        // Get monthly user growth
        $monthlyGrowth = $this->getMonthlyGrowth();

        // Get top performers
        $topPerformers = $this->getTopPerformers();

        // Get learning analytics summary
        $learningAnalytics = $this->getLearningAnalyticsSummary();

        return inertia('Admin/Users/Stats', [
            'users' => $users,
            'stats' => $stats,
            'roleDistribution' => $roleDistribution,
            'monthlyGrowth' => $monthlyGrowth,
            'topPerformers' => $topPerformers,
            'learningAnalytics' => $learningAnalytics,
            'filters' => [
                'role' => $role,
                'status' => $status,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ],
        ]);
    }

    /**
     * Get aggregated statistics for all users
     */
    private function getAggregatedStats($role = null, $status = null, $dateFrom = null, $dateTo = null)
    {
        $query = User::query();

        if ($role) {
            $query->whereHas('roles', function ($q) use ($role) {
                $q->where('name', $role);
            });
        }

        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }

        if ($dateFrom) {
            $query->where('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->where('created_at', '<=', $dateTo);
        }

        // Basic user stats
        $stats = [
            'total_users' => $query->count(),
            'active_users' => $query->clone()->where('is_active', true)->count(),
            'verified_users' => $query->clone()->whereNotNull('email_verified_at')->count(),
            'new_users_today' => $query->clone()->whereDate('created_at', today())->count(),
            'new_users_week' => $query->clone()->where('created_at', '>=', now()->subWeek())->count(),
            'new_users_month' => $query->clone()->where('created_at', '>=', now()->subMonth())->count(),
        ];

        // Enrollment stats
        $enrollmentStats = DB::table('course_enrollments')
            ->selectRaw('COUNT(*) as total_enrollments')
            ->selectRaw('SUM(CASE WHEN status IN ("enrolled", "active") THEN 1 ELSE 0 END) as active_enrollments')
            ->selectRaw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_enrollments')
            ->selectRaw('AVG(progress_percentage) as avg_progress')
            ->when($role, function ($q) use ($role) {
                $q->whereIn('user_id', function ($subquery) use ($role) {
                    $subquery->select('users.id')
                        ->from('users')
                        ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                        ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                        ->where('model_has_roles.model_type', User::class)
                        ->where('roles.name', $role);
                });
            })
            ->first();

        $stats['total_enrollments'] = $enrollmentStats->total_enrollments ?? 0;
        $stats['active_enrollments'] = $enrollmentStats->active_enrollments ?? 0;
        $stats['completed_enrollments'] = $enrollmentStats->completed_enrollments ?? 0;
        $stats['avg_progress'] = round($enrollmentStats->avg_progress ?? 0, 1);

        // Certificate stats
        $certificateStats = DB::table('certificates')
            ->selectRaw('COUNT(*) as total_certificates')
            ->selectRaw('SUM(CASE WHEN status = "active" THEN 1 ELSE 0 END) as active_certificates')
            ->selectRaw('SUM(CASE WHEN status = "expired" THEN 1 ELSE 0 END) as expired_certificates')
            ->selectRaw('SUM(download_count) as total_downloads')
            ->when($role, function ($q) use ($role) {
                $q->whereIn('user_id', function ($subquery) use ($role) {
                    $subquery->select('users.id')
                        ->from('users')
                        ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                        ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                        ->where('model_has_roles.model_type', User::class)
                        ->where('roles.name', $role);
                });
            })
            ->first();

        $stats['total_certificates'] = $certificateStats->total_certificates ?? 0;
        $stats['active_certificates'] = $certificateStats->active_certificates ?? 0;
        $stats['expired_certificates'] = $certificateStats->expired_certificates ?? 0;
        $stats['total_certificate_downloads'] = $certificateStats->total_downloads ?? 0;

        // Study time stats
        $studyStats = DB::table('progress_tracking')
            ->selectRaw('SUM(time_spent_minutes) as total_study_minutes')
            ->selectRaw('AVG(time_spent_minutes) as avg_study_minutes')
            ->selectRaw('COUNT(DISTINCT user_id) as active_students')
            ->when($role, function ($q) use ($role) {
                $q->whereIn('user_id', function ($subquery) use ($role) {
                    $subquery->select('users.id')
                        ->from('users')
                        ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                        ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                        ->where('model_has_roles.model_type', User::class)
                        ->where('roles.name', $role);
                });
            })
            ->first();

        $stats['total_study_hours'] = round(($studyStats->total_study_minutes ?? 0) / 60, 1);
        $stats['avg_study_hours'] = round(($studyStats->avg_study_minutes ?? 0) / 60, 1);
        $stats['active_students'] = $studyStats->active_students ?? 0;

        // Calculate completion rate
        if ($stats['total_enrollments'] > 0) {
            $stats['completion_rate'] = round(($stats['completed_enrollments'] / $stats['total_enrollments']) * 100, 1);
        } else {
            $stats['completion_rate'] = 0;
        }

        // Calculate certificate rate
        if ($stats['completed_enrollments'] > 0) {
            $stats['certificate_rate'] = round(($stats['total_certificates'] / $stats['completed_enrollments']) * 100, 1);
        } else {
            $stats['certificate_rate'] = 0;
        }

        return $stats;
    }

    /**
     * Get role distribution statistics
     */
    private function getRoleDistribution()
    {
        return DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_type', User::class)
            ->select('roles.name as role', DB::raw('COUNT(*) as count'))
            ->groupBy('roles.name')
            ->orderBy('count', 'desc')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->role => $item->count];
            })
            ->toArray();
    }

    /**
     * Get monthly user growth
     */
    private function getMonthlyGrowth()
    {
        $data = DB::table('users')
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subYear())
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return [
            'labels' => $data->pluck('month'),
            'data' => $data->pluck('count'),
        ];
    }

    /**
     * Get top performing users
     */
    private function getTopPerformers()
    {
        return User::with(['roles'])
            ->withCount(['certificates', 'courseEnrollments as completed_enrollments_count' => function ($q) {
                $q->where('status', 'completed');
            }])
            ->whereHas('courseEnrollments', function ($q) {
                $q->where('status', 'completed');
            })
            ->withSum('progressTracking as total_study_minutes', 'time_spent_minutes')
            ->orderBy('completed_enrollments_count', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->roles->first()->name ?? 'N/A',
                    'completed_courses' => $user->completed_enrollments_count,
                    'certificates' => $user->certificates_count,
                    'study_hours' => round(($user->total_study_minutes ?? 0) / 60, 1),
                    'last_active' => $user->last_login_at,
                ];
            });
    }

    /**
     * Get learning analytics summary
     */
    private function getLearningAnalyticsSummary()
    {
        $summary = DB::table('progress_tracking')
            ->select(
                DB::raw('AVG(time_spent_minutes) as avg_study_minutes'),
                DB::raw('AVG(completion_score) as avg_score'),
                DB::raw('COUNT(DISTINCT user_id) as active_learners'),
                DB::raw('COUNT(DISTINCT DATE(created_at)) as active_days')
            )
            ->where('created_at', '>=', now()->subMonth())
            ->first();

        // Get streak distribution
        $streakDistribution = DB::table('progress_tracking')
            ->select(
                DB::raw('user_id'),
                DB::raw('COUNT(DISTINCT DATE(created_at)) as consecutive_days')
            )
            ->where('created_at', '>=', now()->subWeek())
            ->groupBy('user_id')
            ->get()
            ->groupBy('consecutive_days')
            ->map->count()
            ->toArray();

        return [
            'avg_study_hours' => round(($summary->avg_study_minutes ?? 0) / 60, 1),
            'avg_score' => round($summary->avg_score ?? 0, 1),
            'active_learners' => $summary->active_learners ?? 0,
            'active_days' => $summary->active_days ?? 0,
            'streak_distribution' => $streakDistribution,
        ];
    }

    /**
     * Export users statistics
     */
    public function exportStats(Request $request)
    {
        // Get the same stats
        $stats = $this->getAggregatedStats(
            $request->get('role'),
            $request->get('status'),
            $request->get('date_from'),
            $request->get('date_to')
        );

        // Generate CSV or Excel file
        // This is a simplified version - you might want to use a package like Laravel Excel
        $filename = 'users_stats_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($stats) {
            $file = fopen('php://output', 'w');

            // Header
            fputcsv($file, ['Statistic', 'Value']);

            // Data
            foreach ($stats as $key => $value) {
                $label = str_replace('_', ' ', ucwords($key, '_'));
                fputcsv($file, [$label, $value]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

}
