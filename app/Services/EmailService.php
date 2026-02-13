<?php
// app/Services/EmailService.php
namespace App\Services;

use App\Models\User;
use App\Notifications\AdminBroadcastEmail;
use App\Notifications\UserDirectEmail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class EmailService
{
    /**
     * Send email to users with a specific role
     */
    public function sendToRole($role, $subject, $message, $fromEmail = null, $fromName = null)
    {
        $users = User::role($role)->where('is_active', true)->get();

        if ($users->isEmpty()) {
            return 0;
        }

        Notification::send($users, new AdminBroadcastEmail(
            $subject,
            $message,
            $fromEmail,
            $fromName
        ));

        return $users->count();
    }

    /**
     * Send email to a single user
     */
    public function sendToUser($userId, $subject, $message, $fromEmail = null, $fromName = null)
    {
        $user = User::findOrFail($userId);

        $user->notify(new UserDirectEmail(
            $subject,
            $message,
            $fromEmail,
            $fromName
        ));

        return 1; // Return count for consistency
    }

    /**
     * Send email to multiple specific users
     */
    public function sendToMultipleUsers($userIds, $subject, $message, $fromEmail = null, $fromName = null)
    {
        $users = User::whereIn('id', $userIds)->where('is_active', true)->get();

        if ($users->isEmpty()) {
            return 0;
        }

        Notification::send($users, new UserDirectEmail(
            $subject,
            $message,
            $fromEmail,
            $fromName
        ));

        return $users->count();
    }

    /**
     * Send email to a collection of users (new method for segments)
     */
    public function sendToUserCollection($users, $subject, $message, $fromEmail = null, $fromName = null)
    {
        if ($users->isEmpty()) {
            return 0;
        }

        // Filter out inactive users
        $activeUsers = $users->filter(function($user) {
            return $user->is_active ?? true;
        });

        if ($activeUsers->isEmpty()) {
            return 0;
        }

        // Use AdminBroadcastEmail for bulk emails to avoid too many notifications
        Notification::send($activeUsers, new AdminBroadcastEmail(
            $subject,
            $message,
            $fromEmail,
            $fromName
        ));

        return $activeUsers->count();
    }

    /**
     * Get available roles for dropdown
     */
    public function getRoles()
    {
        return ['admin', 'student', 'tutor', 'organization'];
    }

    /**
     * Get available segment types with descriptions (new method)
     */
    public function getSegmentTypes()
    {
        return [
            'no_course_enrollment' => [
                'label' => 'Never Enrolled in Any Course',
                'description' => 'Users who have never enrolled in any course',
                'requires' => []
            ],
            'no_course_completion' => [
                'label' => 'Enrolled But Never Completed Any Course',
                'description' => 'Users who enrolled but never completed any course',
                'requires' => []
            ],
            'no_exam_attempt' => [
                'label' => 'Never Attempted Exam Prep',
                'description' => 'Users who have never attempted exam prep questions',
                'requires' => ['exam_prep_id']
            ],
            'incomplete_courses' => [
                'label' => 'Started But Not Completed Specific Course',
                'description' => 'Users who started but haven\'t completed a specific course',
                'requires' => ['course_id']
            ],
            'recent_inactive' => [
                'label' => 'Inactive Users (No Activity)',
                'description' => 'Users who haven\'t accessed the platform recently',
                'requires' => ['days_inactive']
            ],
            'high_achievers' => [
                'label' => 'High Achievers',
                'description' => 'Users who scored above a certain percentage on exam preps',
                'requires' => ['min_score']
            ],
            'struggling_students' => [
                'label' => 'Struggling Students',
                'description' => 'Users who scored below a certain percentage on exam preps',
                'requires' => ['max_score']
            ],
        ];
    }

    /**
     * Validate that a segment type exists
     */
    public function isValidSegmentType($segmentType)
    {
        return array_key_exists($segmentType, $this->getSegmentTypes());
    }

    /**
     * Get required fields for a segment type
     */
    public function getSegmentRequirements($segmentType)
    {
        if (!$this->isValidSegmentType($segmentType)) {
            return [];
        }

        return $this->getSegmentTypes()[$segmentType]['requires'];
    }
}
