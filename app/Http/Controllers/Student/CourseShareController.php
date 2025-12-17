<?php
// app/Http/Controllers/Student/CourseShareController.php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseShare;
use App\Models\User;
use App\Notifications\CourseSharedNotification;
use App\Notifications\CourseSharedInvitationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Jobs\SendCourseSharedEmail;
use App\Jobs\SendCourseInvitationEmail;
use Inertia\Inertia;

class CourseShareController extends Controller
{
    public function share(Request $request, Course $course)
    {
        // Ensure user owns the course
        if ($course->student_profile_id !== auth()->user()->studentProfile->id) {
            abort(403, 'You can only share your own courses.');
        }

        $validated = $request->validate([
            'emails' => 'required|array|max:5',
            'emails.*' => 'required|email',
            'message' => 'nullable|string|max:500',
        ]);

        $emails = array_unique($validated['emails']);
        $sharer = auth()->user();
        $successCount = 0;
        $skippedCount = 0;

        DB::beginTransaction();
        try {
            foreach ($emails as $email) {
                // Check if already shared with this email
                $existingShare = CourseShare::where('course_id', $course->id)
                    ->where('recipient_email', $email)
                    ->where('status', 'pending')
                    ->where('expires_at', '>', now())
                    ->first();

                if ($existingShare) {
                    $skippedCount++;
                    continue;
                }

                // Create share record
                $share = CourseShare::create([
                    'course_id' => $course->id,
                    'sharer_id' => $sharer->id,
                    'recipient_email' => $email,
                    'token' => CourseShare::generateToken(),
                    'expires_at' => now()->addDays(7),
                ]);

                // Check if recipient is registered
                $recipient = User::where('email', $email)->first();

                if ($recipient) {
                    // Queue notification for registered user
                    SendCourseSharedEmail::dispatch($recipient, $sharer, $course, $share, $validated['message'] ?? null);
                } else {
                    // Queue invitation for non-registered user
                    SendCourseInvitationEmail::dispatch($email, $sharer, $course, $share, $validated['message'] ?? null);
                }

                $successCount++;
            }

            DB::commit();

            $message = '';
            if ($successCount > 0) {
                $message = "Course shared successfully with {$successCount} " . ($successCount === 1 ? 'recipient' : 'recipients');
            }
            if ($skippedCount > 0) {
                $message .= $message ? ' and ' : '';
                $message .= "{$skippedCount} " . ($skippedCount === 1 ? 'email was' : 'emails were') . " skipped (already shared)";
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Course sharing failed: ' . $e->getMessage());

            return back()->with('error', 'Failed to share course. Please try again.');
        }
    }


    public function accept(string $token)
    {
        $share = CourseShare::where('token', $token)
            ->with(['course', 'sharer'])
            ->firstOrFail();

        if (!$share->isPending()) {
            if ($share->isExpired()) {
                return redirect()->route('home')
                    ->with('error', 'This invitation has expired.');
            }

            if ($share->status === 'accepted') {
                return redirect()->route('home')
                    ->with('info', 'You have already accepted this course.');
            }

            if ($share->status === 'rejected') {
                return redirect()->route('home')
                    ->with('info', 'You have declined this course.');
            }
        }

        $user = auth()->user();

        // If user is not logged in, redirect to register with token
        if (!$user) {
            session(['course_share_token' => $token]);
            return redirect()->route('register')
                ->with('info', 'Please register or login to accept the shared course.');
        }

        // Verify email matches
        if (strtolower($user->email) !== strtolower($share->recipient_email)) {
            return redirect()->route('home')
                ->with('error', 'This course was shared with a different email address.');
        }

        DB::beginTransaction();
        try {
            // Clone the course for the user
            $clonedCourse = $share->course->cloneForUser($user, $share->sharer_id);

            // Mark share as accepted
            $share->markAsAccepted($user);

            DB::commit();

            return redirect()->route('student.courses.show', $clonedCourse->id)
                ->with('success', 'Course accepted successfully! You can now start learning.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Course acceptance failed: ' . $e->getMessage());

            return redirect()->route('home')
                ->with('error', 'Failed to accept the course. Please try again.');
        }
    }

    public function reject(string $token)
    {
        $share = CourseShare::where('token', $token)->firstOrFail();

        if (!$share->isPending()) {
            return redirect()->route('home')
                ->with('error', 'This invitation is no longer valid.');
        }

        $share->markAsRejected();

        return redirect()->route('home')
            ->with('info', 'You have declined the shared course.');
    }

    public function pendingShares()
    {
        $user = auth()->user();
        $shares = CourseShare::forUser($user)
            ->pending()
            ->with(['course', 'sharer'])
            ->latest()
            ->paginate(10);

        return view('student.courses.shared-pending', compact('shares'));
    }



    public function sharedCourses()
    {
        $user = auth()->user();

        // Get courses shared with this user that they've accepted
        $sharedCourses = CourseShare::where('recipient_email', $user->email)
            ->where('status', 'accepted')
            ->with(['course' => function($query) {
                $query->with(['modules', 'examBoard']);
            }, 'sharer'])
            ->latest('accepted_at')
            ->paginate(12);

        // Get pending shares (invitations not yet accepted)
        $pendingShares = CourseShare::where('recipient_email', $user->email)
            ->pending()
            ->with(['course', 'sharer'])
            ->latest()
            ->get();

        return Inertia::render('Student/Courses/Shared', [
            'sharedCourses' => $sharedCourses,
            'pendingShares' => $pendingShares,
            'meta' => [
                'title' => 'Shared Courses',
                'description' => 'Courses shared with you by other users',
            ]
        ]);
    }
}
