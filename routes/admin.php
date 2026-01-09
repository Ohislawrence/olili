<?php
// routes/admin.php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\AiProviderController;
use App\Http\Controllers\Admin\ExamBoardController;
use App\Http\Controllers\Admin\AiAnalyticsController;
use App\Http\Controllers\Admin\SystemSettingsController;
use App\Http\Controllers\Admin\SubscriptionPlanController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\BlogPostController;
use App\Http\Controllers\Admin\CommunityController as AdminCommunityController;
use App\Http\Controllers\Admin\CourseOutlineController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\EmailController;
use App\Http\Controllers\Admin\NotificationController;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\CoursesController;
use App\Http\Controllers\Admin\EnrollmentController;
use App\Http\Controllers\Admin\MassEnrollmentController;
use Inertia\Inertia;


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'role:admin',
])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/chart-data', [DashboardController::class, 'getChartData'])->name('dashboard.chart-data');

    // User Management
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('/users/{user}/impersonate', [UserController::class, 'impersonate'])->name('users.impersonate');
    Route::post('/impersonate/leave', [UserController::class, 'leaveImpersonate'])->name('impersonate.leave');
    Route::get('/users/{user}/login-history', [UserController::class, 'loginHistory'])->name('users.login-history');
    Route::get('/users/{user}/payment-history', [UserController::class, 'paymentHistory'])->name('users.payment-history');
    Route::get('/users/{user}/certificates', [UserController::class, 'certificates'])->name('users.certificates');
    Route::get('/all/users/stats', [UserController::class, 'usersStats'])->name('users.stats');

    // Export statistics
    Route::get('/users/stats/export', [UserController::class, 'exportStats'])->name('users.stats.export');


    // Certificate Management Routes
    Route::prefix('certificates')->name('certificates.')->group(function () {
        Route::get('/create', [UserController::class, 'showCertificate'])->name('create');
        Route::get('/{certificate}', [UserController::class, 'showCertificate'])->name('show');
        Route::post('/generate', [UserController::class, 'generateCertificate'])->name('generate');
        Route::post('/batch-generate', [UserController::class, 'batchGenerateCertificates'])->name('batch-generate');
        Route::patch('/{certificate}/status', [UserController::class, 'updateCertificateStatus'])->name('update-status');
        Route::delete('/{certificate}', [UserController::class, 'deleteCertificate'])->name('delete');
        Route::post('/{certificate}/send', [UserController::class, 'sendCertificate'])->name('send');
        Route::post('/users/{user}/export', [UserController::class, 'exportCertificates'])->name('export');
        Route::get('/{certificate}/preview', [UserController::class, 'previewCertificate'])->name('preview');
    });

    // Course Management
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/create', [CourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
    Route::get('courses/show/{course}/show', [CourseController::class, 'show'])->name('courses.show');
    Route::get('/courses/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit');
    Route::post('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');
    Route::post('/courses/{course}/update-progress', [CourseController::class, 'updateProgress'])->name('courses.update-progress');
    Route::post('/courses/{course}/regenerate-outline', [CourseController::class, 'regenerateOutline'])->name('courses.regenerate-outline');
    Route::get('/courses/{course}/analytics', [CourseController::class, 'getCourseAnalytics'])->name('courses.analytics');
    Route::get('/courses/{course}/flashcards', [CourseController::class, 'flashcards'])->name('courses.flashcards');
    Route::post('courses/{course}/publish', [CourseController::class, 'publish'])->name('courses.publish');
    Route::post('courses/{course}/unpublish', [CourseController::class, 'unpublish'])->name('courses.unpublish');
    Route::get('/courses/outline/{course}/outline', [CourseController::class, 'outline'])->name('courses.outline');

        //modules
    Route::get('/courses/modules/{course}/outline/mod', [CourseController::class, 'outline'])->name('quizzes.create');
    Route::get('/courses/modules/{course}/outline/mo', [CourseController::class, 'outline'])->name('courses.modules.edit');
    Route::get('/courses/modules/{course}/outline', [CourseController::class, 'outline'])->name('courses.modules.topics.create');
    Route::get('/courses/modules/edit/{course}/outline', [CourseController::class, 'outline'])->name('courses.modules.topics.edit');


    Route::get('/courses/modules/{course}/edit', [CourseController::class, 'edit'])->name('courses.modules.create');

    //generate content this
    Route::prefix('courses/{course}/modules/{module}/topics/{topic}')->group(function () {
        // Generate topic content
        Route::post('/content/generate', [CourseOutlineController::class, 'generateContent'])
            ->name('courses.modules.topics.content.generate');

        Route::post('general/quiz/content/generate', [CourseOutlineController::class, 'generateContentForGeneralQuiz'])
            ->name('courses.modules.topics.content.general.quiz');

        // Generate quiz for topic
        Route::post('/quiz/generate', [CourseOutlineController::class, 'generateQuiz'])
            ->name('courses.modules.topics.quiz.generate');

        // Preview generated content
        Route::get('/content/preview', [CourseOutlineController::class, 'previewContent'])
            ->name('courses.modules.topics.content.preview');
    });
    // Course-wide generation routes
    Route::prefix('courses/{course}')->group(function () {
        // Generate all pending content
        Route::post('/content/generate-all', [CourseController::class, 'generateAllContent'])
            ->name('courses.content.generate-all');

        // Generate capstone project
        Route::post('/capstone/generate', [CourseController::class, 'generateCapstoneProject'])
            ->name('courses.capstone.generate');

        // Regenerate entire course outline
        Route::post('/outline/regenerate', [CourseController::class, 'regenerateOutline'])
            ->name('courses.outline.regenerate');
    });

    Route::get('/courses/{course}/batch/{batch}/progress', [CourseController::class, 'batchProgress'])
    ->name('courses.batch.progress');

    // Enrollment management for courses
    Route::prefix('courses/{course}/enrollments')->name('courses.enrollments.')->group(function () {
        Route::get('/', [EnrollmentController::class, 'index'])->name('index');
        Route::get('/create', [EnrollmentController::class, 'create'])->name('create');
        Route::post('/', [EnrollmentController::class, 'store'])->name('store');
        Route::get('/{enrollment}', [EnrollmentController::class, 'show'])->name('show');
        Route::get('/{enrollment}/edit', [EnrollmentController::class, 'edit'])->name('edit');
        Route::patch('/{enrollment}', [EnrollmentController::class, 'update'])->name('update');
        Route::delete('/{enrollment}', [EnrollmentController::class, 'destroy'])->name('destroy');

        // Bulk actions
        Route::post('/bulk-update', [EnrollmentController::class, 'bulkUpdate'])->name('bulk-update');
        Route::post('/bulk-destroy', [EnrollmentController::class, 'bulkDestroy'])->name('bulk-destroy');

        // Import/Export
        Route::post('/import', [EnrollmentController::class, 'import'])->name('import');
        Route::get('/export', [EnrollmentController::class, 'export'])->name('export');
        Route::get('/export-template', [EnrollmentController::class, 'exportTemplate'])->name('export-template');
    });

    //mass enrollment
    Route::prefix('assign/courses')->group(function () {
        Route::get('/mass-enrollment', [MassEnrollmentController::class, 'index'])
            ->name('courses.mass-enrollment.index');
        Route::post('/mass-enrollment', [MassEnrollmentController::class, 'store'])
            ->name('courses.mass-enrollment.store');
        Route::get('/{course}/mass-enrollment/eligible-students', [MassEnrollmentController::class, 'getEligibleStudents'])
            ->name('courses.mass-enrollment.get-eligible-students');
        Route::get('/{course}/mass-enrollment/stats', [MassEnrollmentController::class, 'getEnrollmentStats'])
            ->name('courses.mass-enrollment.stats');
        Route::post('/mass-enrollment/upload-csv', [MassEnrollmentController::class, 'uploadCsv'])
            ->name('courses.mass-enrollment.upload-csv');
    });


    // AI Providers Management
    Route::get('/ai-providers', [AiProviderController::class, 'index'])->name('ai-providers.index');
    Route::get('/ai-providers/create', [AiProviderController::class, 'create'])->name('ai-providers.create');
    Route::post('/ai-providers', [AiProviderController::class, 'store'])->name('ai-providers.store');
    Route::get('/ai-providers/{aiProvider}/edit', [AiProviderController::class, 'edit'])->name('ai-providers.edit');
    Route::put('/ai-providers/{aiProvider}', [AiProviderController::class, 'update'])->name('ai-providers.update');
    Route::delete('/ai-providers/{aiProvider}', [AiProviderController::class, 'destroy'])->name('ai-providers.destroy');
    Route::post('/ai-providers/{aiProvider}/test-connection', [AiProviderController::class, 'testConnection'])->name('ai-providers.test-connection');
    Route::post('/ai-providers/{aiProvider}/toggle-active', [AiProviderController::class, 'toggleActive'])->name('ai-providers.toggle-active');
    Route::post('/ai-providers/{aiProvider}/set-default', [AiProviderController::class, 'setDefault'])->name('ai-providers.set-default');
    Route::get('/ai-providers/{aiProvider}/usage-stats', [AiProviderController::class, 'getUsageStats'])->name('ai-providers.usage-stats');

    // Exam Boards Management
    Route::get('/exam-boards', [ExamBoardController::class, 'index'])->name('exam-boards.index');
    Route::get('/exam-boards/create', [ExamBoardController::class, 'create'])->name('exam-boards.create');
    Route::post('/exam-boards', [ExamBoardController::class, 'store'])->name('exam-boards.store');
    Route::get('/exam-boards/{examBoard}', [ExamBoardController::class, 'show'])->name('exam-boards.show');
    Route::get('/exam-boards/{examBoard}/edit', [ExamBoardController::class, 'edit'])->name('exam-boards.edit');
    Route::put('/exam-boards/{examBoard}', [ExamBoardController::class, 'update'])->name('exam-boards.update');
    Route::delete('/exam-boards/{examBoard}', [ExamBoardController::class, 'destroy'])->name('exam-boards.destroy');
    Route::post('/exam-boards/{examBoard}/toggle-active', [ExamBoardController::class, 'toggleActive'])->name('exam-boards.toggle-active');

    // AI Analytics
    Route::get('/ai-analytics', [AiAnalyticsController::class, 'index'])->name('ai-analytics.index');
    Route::get('/ai-analytics/{aiUsageLog}', [AiAnalyticsController::class, 'show'])->name('ai-analytics.show');
    Route::get('/ai-analytics/stats/usage', [AiAnalyticsController::class, 'getUsageStats'])->name('ai-analytics.usage-stats');
    Route::get('/ai-analytics/stats/user-usage', [AiAnalyticsController::class, 'getUserUsageStats'])->name('ai-analytics.user-usage-stats');
    Route::get('/ai-analytics/export', [AiAnalyticsController::class, 'exportUsageData'])->name('ai-analytics.export');

    // System Settings
    Route::get('/settings', [SystemSettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/ai', [SystemSettingsController::class, 'updateAiSettings'])->name('settings.ai.update');
    Route::post('/settings/courses', [SystemSettingsController::class, 'updateCourseSettings'])->name('settings.courses.update');
    Route::post('/settings/clear-cache', [SystemSettingsController::class, 'clearCache'])->name('settings.clear-cache');
    Route::post('/settings/run-maintenance', [SystemSettingsController::class, 'runMaintenance'])->name('settings.run-maintenance');
    Route::get('/settings/system-logs', [SystemSettingsController::class, 'getSystemLogs'])->name('settings.system-logs');

    // Subscription Plans
    Route::get('/subscription-plans', [SubscriptionPlanController::class, 'index'])->name('subscription-plans.index');
    Route::get('/subscription-plans/create', [SubscriptionPlanController::class, 'create'])->name('subscription-plans.create');
    Route::post('/subscription-plans', [SubscriptionPlanController::class, 'store'])->name('subscription-plans.store');
    Route::get('/subscription-plans/{subscriptionPlan}', [SubscriptionPlanController::class, 'show'])->name('subscription-plans.show');
    Route::get('/subscription-plans/{subscriptionPlan}/edit', [SubscriptionPlanController::class, 'edit'])->name('subscription-plans.edit');
    Route::put('/subscription-plans/{subscriptionPlan}', [SubscriptionPlanController::class, 'update'])->name('subscription-plans.update');
    Route::delete('/subscription-plans/{subscriptionPlan}', [SubscriptionPlanController::class, 'destroy'])->name('subscription-plans.destroy');
    Route::post('/subscription-plans/{subscriptionPlan}/toggle-active', [SubscriptionPlanController::class, 'toggleActive'])->name('subscription-plans.toggle-active');

    // Subscriptions
    Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::get('/subscriptions/create', [SubscriptionController::class, 'create'])->name('subscriptions.create');
    Route::post('/subscriptions', [SubscriptionController::class, 'store'])->name('subscriptions.store');
    Route::get('/subscriptions/{subscription}', [SubscriptionController::class, 'show'])->name('subscriptions.show');
    Route::get('/subscriptions/{subscription}/edit', [SubscriptionController::class, 'edit'])->name('subscriptions.edit');
    Route::put('/subscriptions/{subscription}', [SubscriptionController::class, 'update'])->name('subscriptions.update');
    Route::delete('/subscriptions/{subscription}', [SubscriptionController::class, 'destroy'])->name('subscriptions.destroy');
    Route::post('/subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
    Route::post('/subscriptions/{subscription}/renew', [SubscriptionController::class, 'renew'])->name('subscriptions.renew');

    // Blog Posts Routes
    Route::get('/blog-posts', [BlogPostController::class, 'index'])->name('blog-posts.index');
    Route::get('/blog-posts/create', [BlogPostController::class, 'create'])->name('blog-posts.create');
    Route::post('/blog-posts', [BlogPostController::class, 'store'])->name('blog-posts.store');
    Route::get('/blog-posts/{blogPost}', [BlogPostController::class, 'show'])->name('blog-posts.show');
    Route::get('/blog-posts/{blogPost}/edit', [BlogPostController::class, 'edit'])->name('blog-posts.edit');
    Route::put('/blog-posts/{blogPost}', [BlogPostController::class, 'update'])->name('blog-posts.update');
    Route::delete('/blog-posts/{blogPost}', [BlogPostController::class, 'destroy'])->name('blog-posts.destroy');
    Route::post('/upload-image', [BlogPostController::class, 'uploadImage'])->name('upload-image');

    //email
    Route::get('/email', [EmailController::class, 'index'])->name('email.index');
    Route::post('/email/send', [EmailController::class, 'send'])->name('email.send');

    // Notification routes
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/create', [NotificationController::class, 'create'])->name('notifications.create');
        Route::post('/', [NotificationController::class, 'store'])->name('notifications.store');
        Route::get('/{notification}', [NotificationController::class, 'show'])->name('notifications.show');
        Route::get('/{notification}/edit', [NotificationController::class, 'edit'])->name('notifications.edit');
        Route::put('/{notification}', [NotificationController::class, 'update'])->name('notifications.update');
        Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
        Route::post('/{notification}/send-now', [NotificationController::class, 'sendNow'])->name('notifications.send-now');
        Route::get('/{notification}/preview', [NotificationController::class, 'preview'])->name('notifications.preview');
        Route::post('/bulk-action', [NotificationController::class, 'bulkAction'])->name('notifications.bulk-action');
        Route::post('/{notification}/resend-failed', [NotificationController::class, 'resendFailed'])->name('notifications.resend-failed');
        Route::get('/{notification}/logs', [NotificationController::class, 'logs'])->name('notifications.logs');
    });

    // catelog

    Route::resource('catalog/courses', CoursesController::class)->names([
                                        'index' => 'catalog.courses.index',
                                        'create' => 'catalog.courses.create',
                                        'store' => 'catalog.courses.store',
                                        'show' => 'catalog.courses.show2',
                                        'edit' => 'catalog.courses.edit',
                                        'update' => 'catalog.courses.update',
                                        'destroy' => 'catalog.courses.destroy',
                                    ]);
    Route::get('catelog/courses/{course}/enrollments', [CoursesController::class, 'enrollments'])->name('catalog.courses.enrollments');
    Route::post('catelog/courses/{course}/toggle-enrollment', [CoursesController::class, 'toggleEnrollment'])->name('catalog.courses.toggle-enrollment');
    Route::post('catalog/students/search', [CoursesController::class, 'search'])->name('catalog.search.student');
    Route::post('catalog/courses/{course}/bulk-enroll', [CoursesController::class, 'bulkEnroll'])->name('catalog.bulk.enroll');
    Route::put('catalog/enrollments/{enrollment}', [CoursesController::class, 'update'])->name('catalog.update.enrollment');
    Route::post('/catelog/courses/{course}/regenerate', [CoursesController::class, 'regenerate'])->name('catalog.courses.regenerate');


    /**
    // Community Management
    Route::prefix('mod')->name('mod.')->group(function () {
        Route::get('/check', [AdminCommunityController::class, 'index'])->name('index');
        // Posts management
        Route::prefix('posts')->name('posts.')->group(function () {
            Route::get('/{post}', [AdminCommunityController::class, 'show'])->name('show');
            Route::post('/{post}/approve', [AdminCommunityController::class, 'approve'])->name('approve');
            Route::post('/{post}/reject', [AdminCommunityController::class, 'reject'])->name('reject');
            Route::post('/{post}/pin', [AdminCommunityController::class, 'pin'])->name('pin');
            Route::post('/{post}/unpin', [AdminCommunityController::class, 'unpin'])->name('unpin');
            Route::delete('/{post}', [AdminCommunityController::class, 'destroy'])->name('destroy');
        });

        // Bulk actions
        Route::post('/bulk-action', [AdminCommunityController::class, 'bulkAction'])->name('bulk-action');

        // Comments moderation
        Route::get('/comments', [AdminCommunityController::class, 'moderateComments'])->name('comments.index');
        Route::post('/comments/{comment}/approve', [AdminCommunityController::class, 'approveComment'])->name('comments.approve');
        Route::delete('/comments/{comment}', [AdminCommunityController::class, 'deleteComment'])->name('comments.destroy');

        // User activity
        Route::get('/users/{user}/activity', [AdminCommunityController::class, 'userActivity'])->name('users.activity');

        // Export
        Route::get('/export', [AdminCommunityController::class, 'export'])->name('export');
    });

 */
    // user search route
    Route::get('/users/search', function (Request $request) {
        try {
            $search = $request->get('search', '');
            $limit = $request->get('limit', 20);

            if (empty($search) || strlen($search) < 2) {
                return response()->json([]);
            }

            $users = \App\Models\User::where(function($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                ->with('roles')
                ->where('is_active', true)
                ->limit($limit)
                ->get(['id', 'name', 'email'])
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'roles' => $user->roles->map(function ($role) {
                            return [
                                'id' => $role->id,
                                'name' => $role->name
                            ];
                        })
                    ];
                });

            return response()->json($users);
        } catch (\Exception $e) {
            \Log::error('User search error: ' . $e->getMessage());
            return response()->json([], 500);
        }
    })->name('users.search');

    Route::get('/users/by-role/{role}', function ($role) {
        try {
            $validRoles = ['admin', 'student', 'tutor', 'organization'];

            if (!in_array($role, $validRoles)) {
                return response()->json([], 400);
            }

            $users = \App\Models\User::role($role)
                ->with('roles')
                ->where('is_active', true)
                ->get(['id', 'name', 'email'])
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'roles' => $user->roles->map(function ($role) {
                            return [
                                'id' => $role->id,
                                'name' => $role->name
                            ];
                        })
                    ];
                });

            return response()->json($users);
        } catch (\Exception $e) {
            \Log::error('Users by role error: ' . $e->getMessage());
            return response()->json([], 500);
        }
    })->name('users.by-role');

    Route::post('/users/by-ids', function (Request $request) {
        try {
            $userIds = $request->input('ids', []);

            if (empty($userIds)) {
                return response()->json([]);
            }

            $users = \App\Models\User::whereIn('id', $userIds)
                ->with('roles')
                ->get(['id', 'name', 'email'])
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'roles' => $user->roles->map(function ($role) {
                            return [
                                'id' => $role->id,
                                'name' => $role->name
                            ];
                        })
                    ];
                });

            return response()->json($users);
        } catch (\Exception $e) {
            \Log::error('Users by IDs error: ' . $e->getMessage());
            return response()->json([], 500);
        }
    })->name('users.by-ids');
});
