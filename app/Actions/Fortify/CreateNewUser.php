<?php
// app/Actions/Fortify/CreateNewUser.php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\StudentProfile;
use App\Models\TutorProfile;
use App\Models\OrganizationProfile;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;
use App\Mail\WelcomeStudentMail;
use App\Models\SocialAccount;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;


    public function create(array $input): User
    {
        // Check if this is a social registration (has social data)
        $isSocialRegistration = isset($input['provider']) && isset($input['provider_id']);

        if ($isSocialRegistration) {
            $existingUser = User::where('email', $input['email'])->first();
            if ($existingUser) {
                Log::info('Linking social account to existing user', [
                    'user_id' => $existingUser->id,
                    'provider' => $input['provider']
                ]);
                return $this->linkToExistingUser($existingUser, $input);
            }
        }
        
        // Enhanced validation for role-based registration
        $validationRules = [
            'role' => ['required', 'in:student,tutor,organization'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ];

        // Only require password for non-social registrations
        if (!$isSocialRegistration) {
            $validationRules['password'] = $this->passwordRules();
        }

        // Role-specific validation rules
        switch ($input['role']) {
            case 'student':
                $validationRules = array_merge($validationRules, [
                    'current_level' => ['sometimes', 'in:beginner,intermediate,advanced'],
                    'target_level' => ['sometimes', 'in:intermediate,advanced,expert'],
                    'weekly_study_hours' => ['sometimes', 'in:2,5,10'],
                    'target_completion_date' => ['sometimes', 'date', 'after:today'],
                    'learning_goals' => ['sometimes', 'array'],
                    'learning_preferences' => ['sometimes', 'array'],
                ]);
                break;

            case 'tutor':
                $validationRules = array_merge($validationRules, [
                    'qualification' => ['sometimes', 'string', 'max:255'],
                    'years_of_experience' => ['sometimes', 'in:0,1,3,5'],
                    'specialties' => ['sometimes', 'array'],
                    'bio' => ['sometimes', 'string', 'max:1000'],
                    'hourly_rate' => ['sometimes', 'numeric', 'min:0'],
                    'timezone' => ['sometimes', 'string', 'max:50'],
                ]);
                break;

            case 'organization':
                $validationRules = array_merge($validationRules, [
                    'website' => ['sometimes', 'nullable', 'url'],
                    'phone' => ['sometimes', 'string', 'max:20'],
                    'description' => ['sometimes', 'string', 'max:500'],
                    'founded_year' => ['sometimes', 'integer', 'min:1900', 'max:' . date('Y')],
                    'address' => ['sometimes', 'string', 'max:255'],
                    'city' => ['sometimes', 'string', 'max:100'],
                    'state' => ['sometimes', 'string', 'max:100'],
                    'country' => ['sometimes', 'string', 'max:100'],
                    'postal_code' => ['sometimes', 'string', 'max:20'],
                ]);
                break;
        }

        // For social registration, skip some validations
        if ($isSocialRegistration) {
            $validator = Validator::make($input, array_intersect_key($validationRules, [
                'role' => true,
                'name' => true,
                'email' => true,
                'terms' => true,
            ]));
        } else {
            $validator = Validator::make($input, $validationRules);
        }

        $validator->validate();

        // Prepare user data
        $userData = [
            'name' => $input['name'],
            'email' => $input['email'] ,
            'provider'=> $input['provider'] ?? null,
            'provider_id'=> $input['provider_id'] ?? null,
            'provider_token'=> $input['provider_token'] ?? null,
        ];

        // Set password for social vs regular registration
        if ($isSocialRegistration) {
            // Generate random password for social registrations
            $userData['password'] = Hash::make(Str::random(32));
            $userData['email_verified_at'] = now(); 
        } else {
            $userData['password'] = Hash::make($input['password']);
        }

        // Create user
        $user = User::create($userData);

        // Assign role based on selection
        $user->assignRole($input['role']);

        // Create role-specific profile
        $this->createRoleProfile($user, $input, $isSocialRegistration);

        // Assign first tier subscription plan for the role
        $this->assignStarterSubscription($user, $input['role']);

        // Create social account if this is a social registration
        if ($isSocialRegistration) {
            SocialAccount::create([
                'user_id' => $user->id,
                'provider' => $input['provider'],
                'provider_id' => $input['provider_id'],
                'token' => $input['provider_token'] ?? null,
                'refresh_token' => $input['social_refresh_token'] ?? null,
                'expires_at' => isset($input['social_expires_in']) ? now()->addSeconds($input['social_expires_in']) : null,
                'provider_data' => $input['social_user_data'] ?? null,
            ]);
        }

        if($input['role'] == 'student'){
            Mail::to($user->email)->queue(new WelcomeStudentMail($user));
        }

        return $user;
    }

    protected function createRoleProfile(User $user, array $input, bool $isSocialRegistration = false): void
    {
        switch ($input['role']) {
            case 'student':
                $this->createStudentProfile($user, $input, $isSocialRegistration);
                break;

            case 'tutor':
                $this->createTutorProfile($user, $input);
                break;

            case 'organization':
                $this->createOrganizationProfile($user, $input);
                break;
        }
    }

    protected function createStudentProfile(User $user, array $input, bool $isSocialRegistration = false): void
    {
        // Set default values for social registrations
        if ($isSocialRegistration) {
            $profileData = [
                'user_id' => $user->id,
                'current_level' => 'beginner',
                'target_level' => 'intermediate',
                'target_completion_date' => now()->addMonths(3),
                'weekly_study_hours' => '5', // 5-7 hours
                'learning_goals' => ['exam_preparation'], // Exam Preparation
                'learning_preferences' => ['reading'], // Reading
            ];
        } else {
            $profileData = [
                'user_id' => $user->id,
                'current_level' => $input['current_level'] ?? 'beginner',
                'target_level' => $input['target_level'] ?? 'intermediate',
                'target_completion_date' => $input['target_completion_date'] ?? now()->addMonths(6),
                'weekly_study_hours' => $input['weekly_study_hours'] ?? 5,
                'learning_goals' => $input['learning_goals'] ?? [],
                'learning_preferences' => $input['learning_preferences'] ?? [],
            ];
        }

        // Add exam board if provided
        if (isset($input['exam_board_id']) && !empty($input['exam_board_id'])) {
            $profileData['exam_board_id'] = $input['exam_board_id'];
        }

        StudentProfile::create($profileData);
    }

    protected function createTutorProfile(User $user, array $input): void
    {
        TutorProfile::create([
            'user_id' => $user->id,
            'qualification' => $input['qualification'] ?? null,
            'bio' => $input['bio'] ?? null,
            'specialties' => $input['specialties'] ?? [],
            'years_of_experience' => $input['years_of_experience'] ?? 0,
            'hourly_rate' => $input['hourly_rate'] ?? null,
            'timezone' => $input['timezone'] ?? null,
            'is_online' => false,
            'is_verified' => false,
            'max_students' => 10, // Default for starter tier
        ]);
    }

    protected function createOrganizationProfile(User $user, array $input): void
    {
        OrganizationProfile::create([
            'user_id' => $user->id,
            'name' => $input['name'],
            'slug' => \Illuminate\Support\Str::slug($input['name']),
            'description' => $input['description'] ?? null,
            'website' => $input['website'] ?? null,
            'phone' => $input['phone'] ?? null,
            'address' => $input['address'] ?? null,
            'city' => $input['city'] ?? null,
            'state' => $input['state'] ?? null,
            'country' => $input['country'] ?? null,
            'postal_code' => $input['postal_code'] ?? null,
            'founded_year' => $input['founded_year'] ?? null,
            'total_students' => 0,
            'total_tutors' => 0,
            'max_students' => 50, // Default for starter tier
            'max_tutors' => 5,    // Default for starter tier
            'is_verified' => false,
            'is_active' => true,
        ]);
    }

    protected function assignStarterSubscription(User $user, string $role): void
    {
        // Map roles to their starter plan codes
        $starterPlans = [
            'student' => 'student_free', // or 'student_basic' if you want paid starter
            'tutor' => 'tutor_starter',
            'organization' => 'org_starter',
        ];

        $planCode = $starterPlans[$role] ?? null;

        if (!$planCode) {
            return;
        }

        // Find the starter plan
        $plan = SubscriptionPlan::where('code', $planCode)->active()->first();

        if (!$plan) {
            // Fallback: find any active plan for the role
            $plan = SubscriptionPlan::where('role', $role)->active()->orderBy('price')->first();
        }

        if ($plan) {
            $this->createSubscription($user, $plan);
        }
    }

    protected function createSubscription(User $user, SubscriptionPlan $plan): void
    {
        $subscriptionData = [
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addDays($plan->billing_cycle_days),
        ];

        // Add trial period for paid plans
        if ($plan->price > 0) {
            $subscriptionData['trial_ends_at'] = now()->addDays(14); // 14-day trial
        }

        Subscription::create($subscriptionData);
    }

    protected function linkToExistingUser(User $user, array $input): User
    {
        // Link social account to existing user
        SocialAccount::updateOrCreate(
            [
                'provider' => $input['provider'],
                'provider_id' => $input['provider_id'],
            ],
            [
                'user_id' => $user->id,
                'token' => $input['provider_token'] ?? null,
                'refresh_token' => $input['social_refresh_token'] ?? null,
                'expires_at' => isset($input['social_expires_in']) ? 
                    now()->addSeconds($input['social_expires_in']) : null,
                'provider_data' => $input['social_user_data'] ?? null,
            ]
        );

        return $user;
    }
}
