<?php
// app/Http/Controllers/Student/ProfileController.php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentProfile;
use App\Models\ExamBoard;
use App\Models\Course;
use App\Services\ProgressTrackingService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class ProfileController extends Controller
{
    public function show()
    {
        $student = auth()->user();
        $student->load(['studentProfile.examBoard']);

        // Safely calculate total study hours
        $totalStudyMinutes = $student->progressTracking()->sum('time_spent_minutes') ?? 0;
        $totalStudyHours = $totalStudyMinutes / 60;

        // Fix: Specify table name for status column to avoid ambiguity
        $stats = [
            'total_courses' => $student->courseEnrollments()
                ->where('status', '!=', 'dropped') // Exclude dropped courses
                ->count(),
            'completed_courses' => $student->courseEnrollments()
                ->where('course_enrollments.status', 'completed') // Specify table
                ->count(),
            'active_courses' => $student->courseEnrollments()
                ->whereIn('course_enrollments.status', ['enrolled', 'active']) // Specify table
                ->count(),
            'total_study_hours' => round($totalStudyHours, 1),
        ];

        return Inertia::render('Student/Profile/Show', [
            'user' => $student,
            'student_profile' => $student->studentProfile,
            'stats' => $stats,
        ]);
    }

    public function edit()
    {
        $student = auth()->user();
        $student->load(['studentProfile.examBoard']);

        $examBoards = ExamBoard::active()->get();
        $levels = ['beginner', 'intermediate', 'advanced', 'expert'];
        $learningStyles = ['visual', 'auditory', 'reading_writing', 'kinesthetic'];

        return Inertia::render('Student/Profile/Edit', [
            'user' => $student,
            'student_profile' => $student->studentProfile,
            'exam_boards' => $examBoards,
            'levels' => $levels,
            'learning_styles' => $learningStyles,
        ]);
    }

    public function update(Request $request)
    {
        $student = auth()->user();
        $studentProfile = $student->studentProfile;

        $request->validate([
            // User fields
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $student->id,
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'bio' => 'nullable|string|max:1000',

            // Student profile fields
            'exam_board_id' => 'nullable|exists:exam_boards,id',
            'current_level' => 'required|in:beginner,intermediate,advanced,expert',
            'target_level' => 'required|in:beginner,intermediate,advanced,expert',
            'target_completion_date' => 'required|date|after:today',
            'weekly_study_hours' => 'required|integer|min:1|max:40',
            'current_grade' => 'nullable|numeric|between:0,100',
            'target_grade' => 'nullable|numeric|between:0,100',
            'learning_goals' => 'nullable|array',
            'learning_goals.*' => 'string|max:500',
            'learning_preferences' => 'nullable|array',
        ]);

        // Update user
        $student->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'bio' => $request->bio,
        ]);

        // Update student profile
        $studentProfile->update([
            'exam_board_id' => $request->exam_board_id,
            'current_level' => $request->current_level,
            'target_level' => $request->target_level,
            'target_completion_date' => $request->target_completion_date,
            'weekly_study_hours' => $request->weekly_study_hours,
            'current_grade' => $request->current_grade,
            'target_grade' => $request->target_grade,
            'learning_goals' => $request->learning_goals,
            'learning_preferences' => $request->learning_preferences,
        ]);

        return redirect()->route('student.profile.show')
            ->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $student = auth()->user();

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $student->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('student.profile.show')
            ->with('success', 'Password updated successfully!');
    }

    public function updateLearningGoals(Request $request)
    {
        $student = auth()->user();
        $studentProfile = $student->studentProfile;

        $request->validate([
            'learning_goals' => 'required|array|min:1',
            'learning_goals.*' => 'string|max:500',
        ]);

        $studentProfile->update([
            'learning_goals' => $request->learning_goals,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Learning goals updated successfully!',
            'learning_goals' => $studentProfile->learning_goals,
        ]);
    }

    public function getProgressStats()
    {
        $student = auth()->user();
        $progressService = app(\App\Services\ProgressTrackingService::class);

        $analytics = $progressService->getLearningAnalytics($student);

        return response()->json([
            'analytics' => $analytics,
        ]);
    }

    public function getCourseProgress()
    {
        $student = auth()->user();
        $progressService = app(ProgressTrackingService::class);

        // Fix: Get courses through enrollments and specify table for status
        $courses = $student->courseEnrollments()
            ->where('status', '!=', 'dropped') // Exclude dropped courses
            ->with('course')
            ->get()
            ->map(function ($enrollment) use ($progressService, $student) {
                $course = $enrollment->course;
                if (!$course) {
                    return null;
                }

                $progress = $progressService->calculateCourseProgress($course, $student->id);

                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'status' => $enrollment->status, // Use enrollment status, not course status
                    'progress_percentage' => $progress['overall_completion_percentage'],
                    'completed_modules' => $progress['completed_modules'],
                    'total_modules' => $progress['total_modules'],
                    'completed_topics' => $progress['completed_topics'],
                    'total_topics' => $progress['total_topics'],
                    'enrollment_id' => $enrollment->id,
                ];
            })
            ->filter() // Remove null values
            ->values(); // Reset array keys

        return response()->json([
            'courses' => $courses
        ]);
    }
}
