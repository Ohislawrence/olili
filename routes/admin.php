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
use App\Http\Controllers\Admin\BlogController;
use Illuminate\Support\Facades\Route;

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
    Route::get('/users/{user}/login-history', [UserController::class, 'loginHistory'])->name('users.login-history');
    Route::get('/users/{user}/payment-history', [UserController::class, 'loginHistory'])->name('users.payment-history');

    // Course Management
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/create', [CourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
    Route::get('/courses/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit');
    Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');
    Route::post('/courses/{course}/update-progress', [CourseController::class, 'updateProgress'])->name('courses.update-progress');
    Route::post('/courses/{course}/regenerate-outline', [CourseController::class, 'regenerateOutline'])->name('courses.regenerate-outline');
    Route::get('/courses/{course}/analytics', [CourseController::class, 'getCourseAnalytics'])->name('courses.analytics');

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

    // blog posts
    Route::get('/blog/create', [BlogController::class, 'create'])->name('blog.create');
    Route::post('/blog', [BlogController::class, 'store'])->name('blog.store');
    Route::get('/blog/{blog}/edit', [BlogController::class, 'edit'])->name('blog.edit');
    Route::put('/blog/{blog}', [BlogController::class, 'update'])->name('blog.update');
    Route::delete('/blog/{blog}', [BlogController::class, 'destroy'])->name('blog.destroy');
});
