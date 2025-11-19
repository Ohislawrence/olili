<?php
// app/Http/Middleware/CheckSubscription.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Inertia\Inertia;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ?string $feature = null): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        // Admins have access to everything
        if ($user->isAdmin()) {
            return $next($request);
        }

        // FIX: Use the accessor to get the model, not the relationship
        $subscription = $user->current_subscription;

        // Define free features based on user role
        $freeFeatures = $this->getFreeFeaturesByRole($user);

        // If no subscription and feature is not free, redirect to pricing
        if (!$subscription && $feature && !in_array($feature, $freeFeatures)) {
            return $this->subscriptionRequiredResponse($request, 'You need an active subscription to access this feature.');
        }

        // Check specific feature access for subscribed users
        if ($subscription && $feature && !$this->hasFeatureAccess($user, $subscription, $feature)) {
            return $this->featureNotAvailableResponse($request, 'This feature is not available in your current plan.');
        }

        // Check role-specific limits
        if ($feature && !$this->checkRoleSpecificLimits($user, $subscription, $feature)) {
            return $this->limitReachedResponse($request, $feature);
        }

        return $next($request);
    }

    /**
     * Get free features based on user role
     */
    protected function getFreeFeaturesByRole($user): array
    {
        $baseFeatures = ['view_courses', 'basic_chat', 'view_profile'];

        if ($user->isStudent()) {
            return array_merge($baseFeatures, [
                'take_quizzes',
                'view_progress',
            ]);
        }

        if ($user->isTutor()) {
            return array_merge($baseFeatures, [
                'view_students',
                'basic_analytics',
            ]);
        }

        return $baseFeatures;
    }

    /**
     * Check if user has access to a specific feature
     */
    protected function hasFeatureAccess($user, $subscription, string $feature): bool
    {
        // Check if feature exists in plan
        if (!$subscription->hasFeature($feature)) {
            return false;
        }else{
            return true;
        }

        // Role-specific feature validation
        //return $this->validateRoleSpecificFeature($user, $subscription, $feature);
    }

    /**
     * Validate role-specific feature access
     */
    protected function validateRoleSpecificFeature($user, $subscription, string $feature): bool
    {
        $plan = $subscription->plan;

        if ($user->isStudent()) {
            return $this->validateStudentFeature($plan, $feature);
        }

        if ($user->isTutor()) {
            return $this->validateTutorFeature($plan, $feature);
        }

        // Organization features
        return $this->validateOrganizationFeature($plan, $feature);
    }

    /**
     * Validate student feature access
     */
    protected function validateStudentFeature($plan, string $feature): bool
    {
        $studentFeatures = [
            'create_course' => ['basic','pro', 'premium'],
            'unlimited_ai_learning' => ['basic','pro', 'premium'],
            'full_course_library' => ['pro', 'premium'],
            'advanced_explanations' => ['pro', 'premium'],
            'voice_explanations' => ['pro', 'premium'],
            'exam_preparation' => ['pro', 'premium'],
            'tutor_custom_lessons' => ['premium'],
            'personalized_study_plan' => ['premium'],
            'offline_downloads' => ['premium'],
            'priority_support' => ['premium'],
        ];

        return $this->checkFeatureInPlan($feature, $studentFeatures, $plan->code);
    }

    /**
     * Validate tutor feature access
     */
    protected function validateTutorFeature($plan, string $feature): bool
    {
        $tutorFeatures = [
            'create_courses' => ['starter', 'pro', 'business'],
            'manage_students' => ['starter', 'pro', 'business'],
            'unlimited_courses' => ['pro', 'business'],
            'unlimited_students' => ['pro', 'business'],
            'advanced_analytics' => ['pro', 'business'],
            'ai_course_creation' => ['pro', 'business'],
            'ai_quiz_generator' => ['pro', 'business'],
            'payment_integration' => ['pro', 'business'],
            'team_collaboration' => ['business'],
            'advanced_ai_content' => ['business'],
            'video_hosting' => ['business'],
            'live_classes' => ['business'],
            'certificate_generation' => ['business'],
            'branded_pages' => ['business'],
        ];

        return $this->checkFeatureInPlan($feature, $tutorFeatures, $plan->code);
    }

    /**
     * Validate organization feature access
     */
    protected function validateOrganizationFeature($plan, string $feature): bool
    {
        $orgFeatures = [
            'create_school_courses' => ['starter', 'growth', 'enterprise'],
            'student_management' => ['starter', 'growth', 'enterprise'],
            'unlimited_students' => ['growth', 'enterprise'],
            'unlimited_tutors' => ['growth', 'enterprise'],
            'attendance_tracking' => ['growth', 'enterprise'],
            'advanced_reporting' => ['growth', 'enterprise'],
            'ai_grading' => ['growth', 'enterprise'],
            'custom_portal' => ['growth', 'enterprise'],
            'parent_accounts' => ['growth', 'enterprise'],
            'sso_security' => ['enterprise'],
            'dedicated_support' => ['enterprise'],
            'api_access' => ['enterprise'],
            'lms_integration' => ['enterprise'],
            'custom_onboarding' => ['enterprise'],
            'custom_slas' => ['enterprise'],
        ];

        return $this->checkFeatureInPlan($feature, $orgFeatures, $plan->code);
    }

    /**
     * Check if feature is available in plan
     */
    protected function checkFeatureInPlan(string $feature, array $featureMap, string $planCode): bool
    {
        if (!isset($featureMap[$feature])) {
            return false;
        }

        return in_array($planCode, $featureMap[$feature]);
    }

    /**
     * Check role-specific limits
     */
    protected function checkRoleSpecificLimits($user, $subscription, string $feature): bool
    {
        if (!$subscription) {
            return false;
        }

        $plan = $subscription->plan;

        if ($user->isStudent()) {
            return $this->checkStudentLimits($user, $plan, $feature);
        }

        if ($user->isTutor()) {
            return $this->checkTutorLimits($user, $plan, $feature);
        }

        return $this->checkOrganizationLimits($user, $plan, $feature);
    }

    /**
     * Check student-specific limits
     */
    protected function checkStudentLimits($user, $plan, string $feature): bool
    {
        switch ($feature) {
            case 'ai_request':
                if ($plan->max_ai_requests_per_month === -1) return true;
                $monthlyUsage = $user->aiUsageLogs()
                    ->where('created_at', '>=', now()->startOfMonth())
                    ->count();
                return $monthlyUsage < $plan->max_ai_requests_per_month;

            case 'create_course':
                if ($plan->max_courses === -1) return true;
                $currentCourses = $user->courses()->count();
                return $currentCourses < $plan->max_courses;

            case 'offline_downloads':
                return $plan->hasFeature('offline_downloads');
        }

        return true;
    }

    /**
     * Check tutor-specific limits
     */
    protected function checkTutorLimits($user, $plan, string $feature): bool
    {
        switch ($feature) {
            case 'create_courses':
                if ($plan->max_courses === -1) return true;
                $currentCourses = $user->courses()->count();
                return $currentCourses < $plan->max_courses;

            case 'manage_students':
                if ($plan->max_students === -1) return true;
                $currentStudents = $user->students()->count();
                return $currentStudents < $plan->max_students;

            case 'ai_content_generation':
                if ($plan->max_ai_requests_per_month === -1) return true;
                $monthlyUsage = $user->aiUsageLogs()
                    ->where('created_at', '>=', now()->startOfMonth())
                    ->count();
                return $monthlyUsage < $plan->max_ai_requests_per_month;
        }

        return true;
    }

    /**
     * Check organization-specific limits
     */
    protected function checkOrganizationLimits($user, $plan, string $feature): bool
    {
        switch ($feature) {
            case 'student_management':
                if ($plan->max_students === -1) return true;
                $currentStudents = $user->organization->students()->count();
                return $currentStudents < $plan->max_students;

            case 'tutor_management':
                if ($plan->max_tutors === -1) return true;
                $currentTutors = $user->organization->tutors()->count();
                return $currentTutors < $plan->max_tutors;
        }

        return true;
    }

    /**
     * Subscription required response
     */
    protected function subscriptionRequiredResponse(Request $request, string $message)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Subscription required',
                'message' => $message,
            ], 403);
        }

        // For Inertia requests
        if ($request->header('X-Inertia')) {
            return Inertia::location(route('payment.pricing'));
        }

        return redirect()->route('payment.pricing')
            ->with('error', $message);
    }

    /**
     * Feature not available response
     */
    protected function featureNotAvailableResponse(Request $request, string $message)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Feature not available',
                'message' => $message,
            ], 403);
        }

        // For Inertia requests
        if ($request->header('X-Inertia')) {
            return Inertia::location(route('payment.pricing'));
        }

        return redirect()->route('payment.pricing')
            ->with('error', $message);
    }

    /**
     * Limit reached response
     */
    protected function limitReachedResponse(Request $request, string $feature)
    {
        $messages = [
            'ai_request' => 'You have reached your monthly AI request limit.',
            'create_course' => 'You have reached the maximum number of courses for your plan.',
            'manage_students' => 'You have reached the maximum number of students for your plan.',
            'student_management' => 'You have reached the maximum number of students for your plan.',
        ];

        $message = $messages[$feature] ?? 'You have reached the limit for this feature.';

        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Limit reached',
                'message' => $message,
            ], 429);
        }

        // For Inertia, use session flash
        return back()->with('error', $message);
    }
}
