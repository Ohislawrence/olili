<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use App\Models\CourseEnrollment;
use App\Notifications\CourseEnrollmentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Illuminate\Http\RedirectResponse; // Add this

class MassEnrollmentController extends Controller
{
    /**
     * Display the mass enrollment page
     */
    public function index(Request $request)
    {
        $courses = Course::whereIn('status', ['active', 'published'])
            ->with('examBoard')
            ->orderBy('title')
            ->get();

        $students = User::role('student')
            ->with('studentProfile')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $filters = $request->only(['course_id', 'search']);

        return Inertia::render('Admin/Courses/MassEnrollment/Index', [
            'courses' => $courses,
            'students' => $students,
            'filters' => $filters,
            'selected_course' => $request->course_id
                ? Course::with('examBoard')->find($request->course_id)
                : null,
        ]);
    }

    /**
     * Process mass enrollment
     */
    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:users,id',
            'send_notifications' => 'boolean',
            'notification_message' => 'nullable|string|max:500',
        ]);

        $course = Course::findOrFail($request->course_id);
        $enrolledCount = 0;
        $skippedCount = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            foreach ($request->student_ids as $studentId) {
                try {
                    $student = User::findOrFail($studentId);

                    // Check if student is already enrolled (not dropped)
                    $existingEnrollment = CourseEnrollment::where('course_id', $course->id)
                        ->where('user_id', $student->id)
                        ->where('status', '!=', 'dropped')
                        ->first();

                    if ($existingEnrollment) {
                        $skippedCount++;
                        $errors[] = "Student {$student->name} is already enrolled in this course.";
                        continue;
                    }

                    // Check if course has capacity
                    if ($course->enrollment_limit &&
                        $course->current_enrollment >= $course->enrollment_limit) {
                        $skippedCount++;
                        $errors[] = "Course enrollment limit reached. Could not enroll {$student->name}.";
                        continue;
                    }

                    // Check if student previously dropped the course
                    $droppedEnrollment = CourseEnrollment::where('course_id', $course->id)
                        ->where('user_id', $student->id)
                        ->where('status', 'dropped')
                        ->first();

                    if ($droppedEnrollment) {
                        // Reactivate the dropped enrollment
                        $droppedEnrollment->update([
                            'status' => 'enrolled',
                            'dropped_at' => null,
                            'paused_at' => null,
                        ]);
                    } else {
                        // Create new enrollment
                        $enrollment = $course->enrollments()->create([
                            'user_id' => $student->id,
                            'student_profile_id' => $student->studentProfile?->id,
                            'status' => 'enrolled',
                            'enrolled_at' => now(),
                            'total_modules' => $course->modules()->count(),
                        ]);

                        // Send notification if enabled
                        if ($request->boolean('send_notifications')) {
                            $student->notify(new CourseEnrollmentNotification(
                                $course,
                                $enrollment,
                                $request->notification_message
                            ));
                        }

                        // Create database notification
                        $student->notifications()->create([
                            'type' => 'App\Notifications\CourseEnrollmentNotification',
                            'data' => json_encode([
                                'course_id' => $course->id,
                                'course_title' => $course->title,
                                'message' => $request->notification_message ?? "You have been enrolled in the course '{$course->title}'",
                                'enrollment_id' => $enrollment->id,
                                'action_url' => route('student.courses.learn', $course->id),
                            ]),
                            'read_at' => null,
                        ]);

                        // Record activity log
                        activity()
                            ->performedOn($enrollment)
                            ->causedBy(auth()->user())
                            ->withProperties([
                                'course_id' => $course->id,
                                'student_id' => $student->id,
                                'enrollment_type' => 'mass_enrollment',
                            ])
                            ->log('Enrolled student in course via mass enrollment');
                    }

                    $enrolledCount++;

                } catch (\Exception $e) {
                    $skippedCount++;
                    $errors[] = "Failed to enroll {$student->name}: " . $e->getMessage();
                    Log::error('Mass enrollment error for student ' . $studentId, [
                        'error' => $e->getMessage(),
                        'course_id' => $course->id,
                    ]);
                    continue;
                }
            }

            // Update course enrollment count
            if ($enrolledCount > 0) {
                $course->increment('current_enrollment', $enrolledCount);
            }

            DB::commit();

            // FIXED: Use back() instead of redirect()->route() for Inertia
            return back()->with([
                'success' => "Successfully enrolled {$enrolledCount} student(s). {$skippedCount} skipped.",
                'errors' => $errors,
                'details' => [
                    'enrolled' => $enrolledCount,
                    'skipped' => $skippedCount,
                    'total' => count($request->student_ids),
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Mass enrollment transaction failed', [
                'error' => $e->getMessage(),
                'course_id' => $request->course_id,
            ]);

            return back()->with([
                'error' => 'Failed to process mass enrollment: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Get eligible students for a course
     */
    public function getEligibleStudents(Request $request, Course $course)
    {
        $request->validate([
            'search' => 'nullable|string|max:255',
        ]);

        $query = User::role('student')
            ->where('is_active', true)
            ->with('studentProfile')
            ->whereDoesntHave('courseEnrollments', function ($q) use ($course) {
                $q->where('course_id', $course->id)
                  ->where('status', '!=', 'dropped');
            });

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        $students = $query->orderBy('name')->paginate(20);

        return response()->json([
            'students' => $students,
            'course_capacity' => [
                'current' => $course->current_enrollment,
                'limit' => $course->enrollment_limit,
                'available' => $course->enrollment_limit
                    ? max(0, $course->enrollment_limit - $course->current_enrollment)
                    : null,
            ],
        ]);
    }

    /**
     * Get enrollment statistics for a course
     */
    public function getEnrollmentStats(Course $course)
    {
        $totalStudents = User::role('student')->where('is_active', true)->count();
        $enrolledStudents = $course->enrollments()->count();

        $eligibleByLevel = User::role('student')
            ->where('is_active', true)
            ->whereDoesntHave('courseEnrollments', function ($q) use ($course) {
                $q->where('course_id', $course->id)
                  ->where('status', '!=', 'dropped');
            })
            ->whereHas('studentProfile', function ($q) use ($course) {
                $q->where('education_level', $course->level);
            })
            ->count();

        return response()->json([
            'total_students' => $totalStudents,
            'enrolled_students' => $enrolledStudents,
            'eligible_by_level' => $eligibleByLevel,
            'course_capacity' => [
                'current' => $course->current_enrollment,
                'limit' => $course->enrollment_limit,
                'available' => $course->enrollment_limit
                    ? max(0, $course->enrollment_limit - $course->current_enrollment)
                    : null,
            ],
        ]);
    }

    /**
     * Upload CSV file for mass enrollment
     */
    public function uploadCsv(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $course = Course::findOrFail($request->course_id);
        $path = $request->file('csv_file')->store('temp/csv');
        $fullPath = storage_path('app/' . $path);

        $studentEmails = [];
        $validStudents = [];
        $invalidEntries = [];

        if (($handle = fopen($fullPath, 'r')) !== false) {
            $row = 0;
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                $row++;
                if ($row === 1) continue; // Skip header

                $email = trim($data[0]);
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $invalidEntries[] = "Row {$row}: Invalid email format '{$email}'";
                    continue;
                }

                $studentEmails[] = $email;
            }
            fclose($handle);
        }

        // Find students by email
        $students = User::role('student')
            ->whereIn('email', $studentEmails)
            ->where('is_active', true)
            ->get()
            ->keyBy('email');

        // Check each email
        foreach ($studentEmails as $email) {
            if (!isset($students[$email])) {
                $invalidEntries[] = "Email not found or not a student: '{$email}'";
                continue;
            }

            $student = $students[$email];

            // Check if already enrolled
            $existing = CourseEnrollment::where('course_id', $course->id)
                ->where('user_id', $student->id)
                ->where('status', '!=', 'dropped')
                ->exists();

            if ($existing) {
                $invalidEntries[] = "{$email}: Already enrolled in this course";
                continue;
            }

            $validStudents[] = [
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
            ];
        }

        // Clean up file
        unlink($fullPath);

        return response()->json([
            'valid_students' => $validStudents,
            'invalid_entries' => $invalidEntries,
            'total_processed' => count($studentEmails),
            'valid_count' => count($validStudents),
            'invalid_count' => count($invalidEntries),
        ]);
    }
}
