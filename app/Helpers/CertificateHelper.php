<?php
// app/Helpers/CertificateHelper.php

namespace App\Helpers;

use App\Models\Course;
use App\Models\User;

class CertificateHelper
{
    /**
     * Check if a user is eligible for a certificate for a specific course
     */
    public static function isEligibleForCertificate(User $user, Course $course): bool
    {
        // 1. Check if course is marked as completed
        if ($course->status !== 'completed') {
            return false;
        }

        // 2. Check if user is the course owner
        if ($course->student_profile_id !== $user->studentProfile->id) {
            return false;
        }

        // 3. Check if all modules are completed
        $totalModules = $course->modules()->count();
        $completedModules = $course->modules()->where('is_completed', true)->count();
        
        if ($totalModules === 0 || $completedModules !== $totalModules) {
            return false;
        }

        // 4. Check if capstone project exists and is approved
        $capstone = $course->capstoneProject;
        if (!$capstone) {
            return false;
        }
        
        if (!$capstone->is_approved) {
            return false;
        }

        // 5. Check if user has at least 70% progress
        if ($course->progress_percentage < 70) {
            return false;
        }

        // 6. Check if certificate doesn't already exist
        $existingCertificate = \App\Models\Certificate::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->first();
        
        if ($existingCertificate) {
            return false;
        }

        return true;
    }

    /**
     * Get certificate eligibility details for debugging
     */
    public static function getEligibilityDetails(User $user, Course $course): array
    {
        $details = [
            'eligible' => false,
            'checks' => [],
            'missing_requirements' => [],
        ];

        // Check 1: Course status
        $courseCompleted = $course->status === 'completed';
        $details['checks']['course_completed'] = $courseCompleted;
        if (!$courseCompleted) {
            $details['missing_requirements'][] = 'Course is not marked as completed';
        }

        // Check 2: User ownership
        $userOwnsCourse = $course->student_profile_id === $user->studentProfile->id;
        $details['checks']['user_owns_course'] = $userOwnsCourse;
        if (!$userOwnsCourse) {
            $details['missing_requirements'][] = 'User does not own this course';
        }

        // Check 3: Modules completion
        $totalModules = $course->modules()->count();
        $completedModules = $course->modules()->where('is_completed', true)->count();
        $allModulesCompleted = $totalModules > 0 && $completedModules === $totalModules;
        $details['checks']['modules_completed'] = [
            'completed' => $completedModules,
            'total' => $totalModules,
            'all_completed' => $allModulesCompleted,
        ];
        if (!$allModulesCompleted) {
            $details['missing_requirements'][] = "Not all modules completed ({$completedModules}/{$totalModules})";
        }

        // Check 4: Capstone project
        $capstone = $course->capstoneProject;
        $hasCapstone = (bool) $capstone;
        $capstoneApproved = $capstone && $capstone->is_approved;
        $details['checks']['capstone'] = [
            'has_capstone' => $hasCapstone,
            'approved' => $capstoneApproved,
        ];
        if (!$hasCapstone) {
            $details['missing_requirements'][] = 'No capstone project found';
        } elseif (!$capstoneApproved) {
            $details['missing_requirements'][] = 'Capstone project not approved';
        }

        // Check 5: Progress percentage
        $hasMinimumProgress = $course->progress_percentage >= 70;
        $details['checks']['progress_percentage'] = [
            'current' => $course->progress_percentage,
            'minimum_required' => 70,
            'meets_requirement' => $hasMinimumProgress,
        ];
        if (!$hasMinimumProgress) {
            $details['missing_requirements'][] = "Progress too low ({$course->progress_percentage}%, needs 70%)";
        }

        // Check 6: No existing certificate
        $existingCertificate = \App\Models\Certificate::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->first();
        $noExistingCertificate = !$existingCertificate;
        $details['checks']['no_existing_certificate'] = $noExistingCertificate;
        if (!$noExistingCertificate) {
            $details['missing_requirements'][] = 'Certificate already exists';
        }

        // Determine overall eligibility
        $details['eligible'] = $courseCompleted 
            && $userOwnsCourse 
            && $allModulesCompleted 
            && $hasCapstone 
            && $capstoneApproved 
            && $hasMinimumProgress 
            && $noExistingCertificate;

        return $details;
    }

    /**
     * Quick check - returns boolean only
     */
    public static function checkEligibility(User $user, Course $course): bool
    {
        return self::isEligibleForCertificate($user, $course);
    }

    /**
     * Generate a certificate for eligible users
     */
    public static function generateCertificateIfEligible(User $user, Course $course): ?\App\Models\Certificate
    {
        if (!self::isEligibleForCertificate($user, $course)) {
            return null;
        }

        try {
            // Create certificate
            $certificate = \App\Models\Certificate::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'organization_id' => $user->organizationProfile?->id,
                'issued_by_type' => $user->organizationProfile ? \App\Models\OrganizationProfile::class : \App\Models\User::class,
                'issued_by_id' => $user->organizationProfile ? $user->organizationProfile->id : 1, // Admin ID
                'title' => "Certificate of Completion - {$course->title}",
                'description' => "This certifies that {$user->name} has successfully completed the course '{$course->title}'.",
                'issue_date' => now(),
                'expiry_date' => now()->addYears(2),
                'status' => 'active',
                'is_public' => true,
            ]);

            // Generate certificate number
            $prefix = 'OLCERT';
            $year = date('Y');
            $sequence = str_pad(\App\Models\Certificate::whereYear('issue_date', $year)->count() + 1, 6, '0', STR_PAD_LEFT);
            $certificate->certificate_number = "{$prefix}-{$year}-{$sequence}";
            
            // Generate verification URL
            $hash = hash('sha256', $certificate->certificate_number . $user->id . $course->id . config('app.key'));
            $certificate->verification_url = route('certificates.verify', $hash);
            
            $certificate->save();

            return $certificate;

        } catch (\Exception $e) {
            \Log::error('Failed to generate certificate: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Simple console command helper
     */
    public static function checkCertificateFromConsole(int $userId, int $courseId): void
    {
        $user = \App\Models\User::find($userId);
        $course = \App\Models\Course::find($courseId);

        if (!$user || !$course) {
            echo "User or Course not found!\n";
            return;
        }

        echo "Checking certificate eligibility for:\n";
        echo "User: {$user->name} (ID: {$user->id})\n";
        echo "Course: {$course->title} (ID: {$course->id})\n";
        echo "Course Status: {$course->status}\n";
        echo "Progress: {$course->progress_percentage}%\n\n";

        $details = self::getEligibilityDetails($user, $course);

        if ($details['eligible']) {
            echo "✅ ELIGIBLE FOR CERTIFICATE!\n";
            echo "Generating certificate...\n";
            
            $certificate = self::generateCertificateIfEligible($user, $course);
            if ($certificate) {
                echo "✅ Certificate generated successfully!\n";
                echo "Certificate Number: {$certificate->certificate_number}\n";
                echo "Verification URL: {$certificate->verification_url}\n";
            } else {
                echo "❌ Failed to generate certificate.\n";
            }
        } else {
            echo "❌ NOT ELIGIBLE FOR CERTIFICATE\n";
            echo "Missing requirements:\n";
            foreach ($details['missing_requirements'] as $requirement) {
                echo "- {$requirement}\n";
            }
        }
    }
}