<?php
// app/Console/Commands/TestNotifications.php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Mail\InactivityReminderEmail;
use App\Notifications\CourseDueSoonNotification;
use App\Notifications\CourseOverdueNotification;
use App\Notifications\InactiveStudentNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class TestNotifications extends Command
{
    protected $signature = 'notifications:test
                            {--user= : User ID to test with}
                            {--all : Test all notification types}
                            {--email : Test email notifications}
                            {--push : Test push notifications}
                            {--database : Test database notifications}
                            {--fake : Use Mail fake (for testing only)}';

    protected $description = 'Test all notification systems';

    public function handle()
    {
        $userId = $this->option('user') ?? User::first()->id;
        $user = User::find($userId);

        if (!$user) {
            $this->error("User not found!");
            return Command::FAILURE;
        }

        $this->info("Testing notifications for: {$user->name} ({$user->email})");

        // Get a course the user is enrolled in
        $enrollment = $user->enrollments()->first();
        $course = $enrollment?->course ?? Course::first();

        if (!$course) {
            $this->error("No course found!");
            return Command::FAILURE;
        }

        $testAll = $this->option('all') ||
                  (!$this->option('email') && !$this->option('push') && !$this->option('database'));

        if ($testAll || $this->option('email')) {
            $this->testEmailNotifications($user, $course, $enrollment);
        }

        if ($testAll || $this->option('database')) {
            $this->testDatabaseNotifications($user, $course);
        }

        if ($testAll || $this->option('push')) {
            $this->testPushNotifications($user, $course);
        }

        $this->info("\n✅ Notification test completed!");
        $this->info("Check:");
        $this->info("- Storage/logs/laravel.log for email attempts");
        $this->info("- Database notifications table");
        $this->info("- Mailtrap/Mailhog if configured");

        return Command::SUCCESS;
    }

    private function testEmailNotifications(User $user, Course $course, ?CourseEnrollment $enrollment): void
    {
        $this->info("\n📧 Testing EMAIL notifications...");
        $this->info("   Using mail driver: " . config('mail.default'));

        // Don't use Mail::fake() in console commands - it causes the error
        // Only use Mail::fake() in actual PHPUnit tests

        try {
            // 1. Test Inactivity Reminder Email (REAL send)
            $this->info("  1. Testing InactivityReminderEmail...");

            // Actually send the email (will use your configured mail driver)
            Mail::to($user->email)->send(new InactivityReminderEmail(
                $user,
                $course,
                3, // days inactive
                45.5 // progress percentage
            ));

            $this->info("     ✅ InactivityReminderEmail sent to: {$user->email}");
            $this->info("     If using log driver, check storage/logs/laravel.log");
            $this->info("     If using smtp, check your mail server");

        } catch (\Exception $e) {
            $this->error("     ❌ InactivityReminderEmail failed: " . $e->getMessage());
            Log::error('Email test failed', ['error' => $e->getMessage()]);
        }

        try {
            // 2. Test Course Due Soon Notification
            $this->info("  2. Testing CourseDueSoonNotification...");
            $user->notify(new CourseDueSoonNotification($course, 3));
            $this->info("     ✅ CourseDueSoonNotification sent!");

        } catch (\Exception $e) {
            $this->error("     ❌ CourseDueSoonNotification failed: " . $e->getMessage());
        }

        try {
            // 3. Test Course Overdue Notification
            $this->info("  3. Testing CourseOverdueNotification...");
            $user->notify(new CourseOverdueNotification($course, 5));
            $this->info("     ✅ CourseOverdueNotification sent!");

        } catch (\Exception $e) {
            $this->error("     ❌ CourseOverdueNotification failed: " . $e->getMessage());
        }

        try {
            // 4. Test Inactive Student Notification
            $this->info("  4. Testing InactiveStudentNotification...");
            $user->notify(new InactiveStudentNotification($course, 7));
            $this->info("     ✅ InactiveStudentNotification sent!");

        } catch (\Exception $e) {
            $this->error("     ❌ InactiveStudentNotification failed: " . $e->getMessage());
        }
    }

    private function testDatabaseNotifications(User $user, Course $course): void
    {
        $this->info("\n💾 Testing DATABASE notifications...");

        try {
            // Send all notification types and check they're stored
            $initialCount = $user->notifications()->count();

            $user->notify(new CourseDueSoonNotification($course, 3));
            $user->notify(new CourseOverdueNotification($course, 5));
            $user->notify(new InactiveStudentNotification($course, 7));

            $newCount = $user->notifications()->count();
            $sentCount = $newCount - $initialCount;

            $this->info("     ✅ {$sentCount} notifications stored in database");

            // Show notification details
            if ($sentCount > 0) {
                $this->info("\n     Latest notifications:");
                $notifications = $user->notifications()->latest()->take($sentCount)->get();

                foreach ($notifications as $notification) {
                    $type = $notification->data['type'] ?? 'unknown';
                    $message = $notification->data['message'] ?? 'No message';
                    $this->info("     - [{$type}] {$message}");
                }
            }

        } catch (\Exception $e) {
            $this->error("     ❌ Database test failed: " . $e->getMessage());
        }
    }

    private function testPushNotifications(User $user, Course $course): void
    {
        $this->info("\n📱 Testing PUSH notifications...");

        try {
            // Check if user has push subscriptions
            if (method_exists($user, 'hasPushSubscription') && $user->hasPushSubscription()) {
                $this->info("     User has push subscriptions: " . $user->getPushSubscriptionCount());

                // Try to send push notification
                $user->notify(new CourseDueSoonNotification($course, 3));
                $this->info("     ✅ Push notification attempt sent");
            } else {
                $this->warn("     ⚠️ User has no push subscriptions (this is expected for testing)");
                $this->info("     You can test push by:");
                $this->info("     1. Creating a push subscription for user {$user->id}");
                $this->info("     2. Running: php artisan tinker");
                $this->info("        >>> \$user = User::find({$user->id});");
                $this->info("        >>> \$user->notify(new CourseDueSoonNotification(Course::first(), 3));");
            }

        } catch (\Exception $e) {
            $this->error("     ❌ Push test failed: " . $e->getMessage());
        }
    }
}
