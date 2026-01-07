<?php
// app/Http/Controllers/Student/CourseEnrollmentController.php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseEnrollment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CourseEnrollmentController extends Controller
{
    public function index()
    {
        $enrollments = CourseEnrollment::with('course', 'course.examBoard')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return Inertia::render('Student/Courses/Index', [
            'enrollments' => $enrollments,
        ]);
    }

    public function browse()
    {
        $courses = Course::availableForEnrollment()
            ->with('examBoard', 'creator')
            ->paginate(12);

        return Inertia::render('Student/Courses/Browse', [
            'courses' => $courses,
        ]);
    }

    public function show(Course $course)
    {
        $course->load('examBoard', 'creator', 'modules.topics');

        $isEnrolled = CourseEnrollment::where('course_id', $course->id)
            ->where('user_id', auth()->id())
            ->exists();

        return Inertia::render('Student/Courses/Show', [
            'course' => $course,
            'isEnrolled' => $isEnrolled,
            'canEnroll' => $course->canEnroll(auth()->user()),
        ]);
    }

    public function enroll(Request $request, Course $course)
    {
        $enrollment = $course->enrollStudent(auth()->user());

        if (!$enrollment) {
            return back()->with('error', 'Unable to enroll in this course.');
        }

        return redirect()->route('student.courses.learn', $course)
            ->with('success', 'Successfully enrolled in the course!');
    }

    public function learn(Course $course)
    {
        $enrollment = CourseEnrollment::where('course_id', $course->id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Mark as started if not already
        if (!$enrollment->started_at) {
            $enrollment->startCourse();
        }

        $course->load('modules.topics.contents', 'modules.topics.quiz');

        return Inertia::render('Student/Courses/Learn', [
            'course' => $course,
            'enrollment' => $enrollment,
        ]);
    }

    public function updateProgress(Request $request, Course $course)
    {
        $enrollment = CourseEnrollment::where('course_id', $course->id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $enrollment->updateProgress();

        return response()->json([
            'progress' => $enrollment->progress_percentage,
            'completed_modules' => $enrollment->completed_modules,
        ]);
    }
}
