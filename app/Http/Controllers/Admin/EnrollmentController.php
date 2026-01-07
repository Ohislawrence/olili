<?php
// app/Http/Controllers/Admin/EnrollmentController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EnrollmentController extends Controller
{
    /**
     * Display a listing of enrollments for a course
     */
    public function index(Course $course, Request $request)
    {
        $enrollments = $course->enrollments()
            ->with(['user', 'studentProfile'])
            ->when($request->search, function ($query, $search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(20)
            ->withQueryString(); // This preserves filter parameters in pagination links

        return Inertia::render('Admin/Courses/Enrollments/Index', [
            'course' => $course,
            'enrollments' => $enrollments,
            'statuses' => $this->getEnrollmentStatuses(),
            'filters' => $request->only(['search', 'status']), // Pass filters to Vue
        ]);
    }

    /**
     * Show the form for creating a new enrollment
     */
    public function create(Course $course)
    {
        $availableStudents = User::whereHas('studentProfile')
            ->whereDoesntHave('courseEnrollments', function ($query) use ($course) {
                $query->where('course_id', $course->id);
            })
            ->with('studentProfile')
            ->get();

        return Inertia::render('Admin/Courses/Enrollments/Create', [
            'course' => $course,
            'availableStudents' => $availableStudents,
        ]);
    }

    /**
     * Store a newly created enrollment
     */
    public function store(Request $request, Course $course)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:enrolled,active,paused',
        ]);

        $user = User::findOrFail($request->user_id);

        // Check if already enrolled
        $existingEnrollment = CourseEnrollment::where('course_id', $course->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingEnrollment) {
            return redirect()->back()
                ->with('error', 'Student is already enrolled in this course.');
        }

        // Create enrollment
        $enrollment = CourseEnrollment::create([
            'course_id' => $course->id,
            'user_id' => $user->id,
            'student_profile_id' => $user->studentProfile->id,
            'status' => $request->status,
            'enrolled_at' => now(),
        ]);

        // Increment course enrollment count
        $course->increment('current_enrollment');

        return redirect()->route('admin.courses.enrollments.index', $course->id)
            ->with('success', 'Student enrolled successfully!');
    }

    /**
     * Display the specified enrollment
     */
    public function show(Course $course, CourseEnrollment $enrollment)
    {
        $enrollment->load(['user', 'studentProfile', 'course.modules.topics']);

        // Calculate progress
        $progress = $this->calculateEnrollmentProgress($enrollment);

        return Inertia::render('Admin/Courses/Enrollments/Show', [
            'course' => $course,
            'enrollment' => $enrollment,
            'progress' => $progress,
            'recentActivity' => $this->getRecentActivity($enrollment),
        ]);
    }

    /**
     * Show the form for editing the specified enrollment
     */
    public function edit(Course $course, CourseEnrollment $enrollment)
    {
        $enrollment->load('user');

        return Inertia::render('Admin/Courses/Enrollments/Edit', [
            'course' => $course,
            'enrollment' => $enrollment,
            'statuses' => $this->getEnrollmentStatuses(),
        ]);
    }

    /**
     * Update the specified enrollment
     */
    public function update(Request $request, Course $course, CourseEnrollment $enrollment)
    {
        $request->validate([
            'status' => 'required|in:enrolled,active,paused,completed,dropped',
            'progress_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        $oldStatus = $enrollment->status;
        $newStatus = $request->status;

        $enrollment->update([
            'status' => $newStatus,
            'progress_percentage' => $request->progress_percentage ?? $enrollment->progress_percentage,
        ]);

        // Handle status-specific actions
        if ($newStatus === 'active' && !$enrollment->started_at) {
            $enrollment->update(['started_at' => now()]);
        } elseif ($newStatus === 'completed') {
            $enrollment->update(['completed_at' => now()]);
        }

        return redirect()->route('admin.courses.enrollments.index', $course->id)
            ->with('success', 'Enrollment updated successfully!');
    }

    /**
     * Remove the specified enrollment
     */
    public function destroy(Course $course, CourseEnrollment $enrollment)
    {
        $enrollment->delete();

        // Decrement course enrollment count
        $course->decrement('current_enrollment');

        return redirect()->route('admin.courses.enrollments.index', $course->id)
            ->with('success', 'Enrollment removed successfully!');
    }

    /**
     * Bulk update enrollments
     */
    public function bulkUpdate(Request $request, Course $course)
    {
        $request->validate([
            'enrollment_ids' => 'required|array',
            'enrollment_ids.*' => 'exists:course_enrollments,id',
            'status' => 'required|in:active,paused,completed,dropped',
        ]);

        CourseEnrollment::whereIn('id', $request->enrollment_ids)
            ->update([
                'status' => $request->status,
                'updated_at' => now(),
            ]);

        // Update completion dates if marked as completed
        if ($request->status === 'completed') {
            CourseEnrollment::whereIn('id', $request->enrollment_ids)
                ->whereNull('completed_at')
                ->update(['completed_at' => now()]);
        }

        return redirect()->back()
            ->with('success', count($request->enrollment_ids) . ' enrollments updated successfully!');
    }

    /**
     * Import enrollments from CSV
     */
    public function import(Request $request, Course $course)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $path = $request->file('csv_file')->getRealPath();
        $data = array_map('str_getcsv', file($path));

        $headers = array_shift($data);
        $enrolled = 0;
        $errors = [];

        foreach ($data as $row) {
            $row = array_combine($headers, $row);

            try {
                $user = User::where('email', $row['email'])->first();

                if (!$user) {
                    $errors[] = "User with email {$row['email']} not found.";
                    continue;
                }

                if (!$user->studentProfile) {
                    $errors[] = "User {$user->email} is not a student.";
                    continue;
                }

                // Check if already enrolled
                $existing = CourseEnrollment::where('course_id', $course->id)
                    ->where('user_id', $user->id)
                    ->exists();

                if ($existing) {
                    $errors[] = "Student {$user->email} is already enrolled.";
                    continue;
                }

                // Create enrollment
                CourseEnrollment::create([
                    'course_id' => $course->id,
                    'user_id' => $user->id,
                    'student_profile_id' => $user->studentProfile->id,
                    'status' => $row['status'] ?? 'enrolled',
                    'enrolled_at' => now(),
                ]);

                $enrolled++;
            } catch (\Exception $e) {
                $errors[] = "Error processing {$row['email']}: " . $e->getMessage();
            }
        }

        // Update course enrollment count
        if ($enrolled > 0) {
            $course->increment('current_enrollment', $enrolled);
        }

        return redirect()->back()
            ->with('success', "Successfully enrolled {$enrolled} students.")
            ->with('errors', $errors);
    }

    /**
     * Export enrollments to CSV
     */
    public function export(Course $course)
    {
        $enrollments = $course->enrollments()
            ->with(['user', 'studentProfile'])
            ->get();

        $csv = \League\Csv\Writer::createFromString('');
        $csv->insertOne(['Name', 'Email', 'Status', 'Progress', 'Enrolled At', 'Started At', 'Completed At']);

        foreach ($enrollments as $enrollment) {
            $csv->insertOne([
                $enrollment->user->name,
                $enrollment->user->email,
                $enrollment->status,
                $enrollment->progress_percentage . '%',
                $enrollment->enrolled_at?->format('Y-m-d'),
                $enrollment->started_at?->format('Y-m-d'),
                $enrollment->completed_at?->format('Y-m-d'),
            ]);
        }

        return response($csv->toString(), 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="enrollments_' . $course->slug . '_' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    /**
     * Calculate enrollment progress
     */
    private function calculateEnrollmentProgress(CourseEnrollment $enrollment)
    {
        $course = $enrollment->course;
        $userId = $enrollment->user_id;

        $totalTopics = 0;
        $completedTopics = 0;
        $totalModules = $course->modules->count();
        $completedModules = 0;

        foreach ($course->modules as $module) {
            $moduleTopics = $module->topics->count();
            $totalTopics += $moduleTopics;

            $moduleCompletedTopics = 0;
            foreach ($module->topics as $topic) {
                if ($topic->isCompletedForUser($userId)) {
                    $moduleCompletedTopics++;
                }
            }

            $completedTopics += $moduleCompletedTopics;

            if ($moduleCompletedTopics === $moduleTopics && $moduleTopics > 0) {
                $completedModules++;
            }
        }

        $overallCompletion = $totalTopics > 0 ? ($completedTopics / $totalTopics) * 100 : 0;

        return [
            'total_modules' => $totalModules,
            'completed_modules' => $completedModules,
            'total_topics' => $totalTopics,
            'completed_topics' => $completedTopics,
            'overall_completion_percentage' => $overallCompletion,
            'module_completion_percentage' => $totalModules > 0 ? ($completedModules / $totalModules) * 100 : 0,
            'topic_completion_percentage' => $totalTopics > 0 ? ($completedTopics / $totalTopics) * 100 : 0,
        ];
    }

    /**
     * Get recent activity for enrollment
     */
    private function getRecentActivity(CourseEnrollment $enrollment)
    {
        return \App\Models\ProgressTracking::where('user_id', $enrollment->user_id)
            ->where('course_id', $enrollment->course_id)
            ->with('courseOutline.module')
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'activity_type' => $activity->activity_type,
                    'topic_title' => $activity->courseOutline->title ?? 'Unknown Topic',
                    'module_title' => $activity->courseOutline->module->title ?? 'Unknown Module',
                    'time_spent_minutes' => $activity->time_spent_minutes,
                    'is_completed' => $activity->is_completed,
                    'completion_score' => $activity->completion_score,
                    'created_at' => $activity->created_at->diffForHumans(),
                ];
            });
    }

    /**
     * Get available enrollment statuses
     */
    private function getEnrollmentStatuses()
    {
        return [
            'enrolled' => [
                'name' => 'Enrolled',
                'description' => 'Student is enrolled but hasn\'t started',
                'color' => 'bg-blue-100 text-blue-800',
            ],
            'active' => [
                'name' => 'Active',
                'description' => 'Student is actively learning',
                'color' => 'bg-green-100 text-green-800',
            ],
            'paused' => [
                'name' => 'Paused',
                'description' => 'Student has paused learning',
                'color' => 'bg-amber-100 text-amber-800',
            ],
            'completed' => [
                'name' => 'Completed',
                'description' => 'Student has completed the course',
                'color' => 'bg-teal-100 text-teal-800',
            ],
            'dropped' => [
                'name' => 'Dropped',
                'description' => 'Student has dropped the course',
                'color' => 'bg-red-100 text-red-800',
            ],
        ];
    }
}
