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
                'name' => 'Pro',
                'code' => 'pro',
                'description' => 'For serious learners',
                'price' => 5000,
                'max_courses' => 10,
                'max_ai_requests_per_month' => 500,
                'ai_grading' => true,
                'priority_support' => false,
                'features' => ['Advanced AI Tutor', 'More Courses', 'AI Project Grading'],
                'sort_order' => 2,
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
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::create($plan);
        }
    }
}
