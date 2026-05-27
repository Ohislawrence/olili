<?php
// database/seeders/SubscriptionPlanSeeder.php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run()
    {
        $plans = [
            [
                'name' => 'Student Free',
                'code' => 'student_free',
                'description' => 'Perfect for getting started',
                'price' => 0,
                'max_courses' => 5,
                'max_ai_requests_per_month' => 50,
                'ai_grading' => false,
                'priority_support' => false,
                'features' => ['Basic AI Tutor', 'Up to 5 Courses', 'Mobile Access'],
                'sort_order' => 1,
                'role' => 'student',
                'tier' => 'free',
            ],
            [
                'name' => 'Tutor Starter',
                'code' => 'tutor_starter',
                'description' => 'Start teaching today',
                'price' => 0,
                'max_courses' => 3,
                'max_ai_requests_per_month' => 20,
                'ai_grading' => false,
                'priority_support' => false,
                'features' => ['3 Active Courses', 'Basic Analytics', 'Student Management'],
                'sort_order' => 1,
                'role' => 'tutor',
                'tier' => 'starter',
            ],
            [
                'name' => 'Organization Starter',
                'code' => 'org_starter',
                'description' => 'For small schools',
                'price' => 0,
                'max_courses' => 10,
                'max_ai_requests_per_month' => 100,
                'ai_grading' => false,
                'priority_support' => false,
                'features' => ['10 Active Courses', 'Teacher Management', 'Basic Reporting'],
                'sort_order' => 1,
                'role' => 'organization',
                'tier' => 'starter',
            ],
            [
                'name' => 'Pro',
                'code' => 'pro',
                'description' => 'For serious learners',
                'price' => 5000,
                'max_courses' => 25,
                'max_ai_requests_per_month' => 500,
                'ai_grading' => true,
                'priority_support' => false,
                'features' => ['Advanced AI Tutor', 'Unlimited Courses', 'AI Project Grading'],
                'sort_order' => 2,
                'role' => 'student',
                'tier' => 'pro',
            ],
            [
                'name' => 'Enterprise',
                'code' => 'enterprise',
                'description' => 'For power users and institutions',
                'price' => 15000,
                'max_courses' => -1, // Unlimited
                'max_ai_requests_per_month' => -1, // Unlimited
                'ai_grading' => true,
                'priority_support' => true,
                'features' => ['Unlimited Everything', 'Priority Support', 'Advanced Analytics'],
                'sort_order' => 3,
                'role' => 'student',
                'tier' => 'premium',
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(
                ['code' => $plan['code']],
                $plan
            );
        }
    }
}
