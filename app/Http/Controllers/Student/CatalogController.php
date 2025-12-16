<?php


namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\ExamBoard;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CatalogController extends Controller
{
    public function index(Request $request)
{
    $student = auth()->user();

    // Get all public courses available for enrollment
    $query = Course::with(['examBoard', 'creator'])
        ->where('is_public', true)
        ->where('visibility', 'public')
        ->where('status', 'active')
        ->latest();

    // Get IDs of courses the student is already enrolled in
    $enrolledCourseIds = $student->courses()
        ->whereNotNull('original_course_id')
        ->pluck('original_course_id')
        ->toArray();

    // Filter by subject
    if ($request->has('subject') && $request->subject) {
        $query->where('subject', $request->subject);
    }

    // Filter by level
    if ($request->has('level') && $request->level) {
        $query->where('level', $request->level);
    }

    // Filter by exam board
    if ($request->has('exam_board_id') && $request->exam_board_id) {
        $query->where('exam_board_id', $request->exam_board_id);
    }

    // Search
    if ($request->has('search') && $request->search) {
        $query->where(function ($q) use ($request) {
            $q->where('title', 'like', "%{$request->search}%")
              ->orWhere('subject', 'like', "%{$request->search}%")
              ->orWhere('description', 'like', "%{$request->search}%");
        });
    }

    $courses = $query->paginate(12);

    // Get enrolled course details for each public course
    $enrolledCourses = [];
    if (!empty($enrolledCourseIds)) {
        $enrolledCourses = $student->courses()
            ->whereIn('original_course_id', $enrolledCourseIds)
            ->get(['courses.id', 'courses.original_course_id', 'courses.progress_percentage', 'courses.status']) // Specify table name
            ->keyBy('original_course_id');
    }

    // Get unique subjects for filters
    $subjects = Course::where('is_public', true)
        ->where('visibility', 'public')
        ->where('status', 'active')
        ->distinct('subject')
        ->pluck('subject')
        ->sort()
        ->values();

    // Get unique levels for filters
    $levels = Course::where('is_public', true)
        ->where('visibility', 'public')
        ->where('status', 'active')
        ->distinct('level')
        ->pluck('level')
        ->mapWithKeys(function ($level) {
            return [$level => ucfirst($level)];
        })
        ->sort();

    // Get active exam boards
    $examBoards = ExamBoard::active()->get();

    return Inertia::render('Student/Catalog/Index', [
        'courses' => $courses,
        'enrolled_course_ids' => $enrolledCourseIds,
        'enrolled_courses' => $enrolledCourses, // Add enrolled course details
        'filters' => $request->only(['search', 'subject', 'level', 'exam_board_id']),
        'subjects' => $subjects,
        'levels' => $levels,
        'exam_boards' => $examBoards,
        'total_courses' => Course::where('is_public', true)
            ->where('visibility', 'public')
            ->where('status', 'active')
            ->count(),
    ]);
}

    public function show(Course $course)
    {
        // Ensure course is public and accessible
        if (!$course->isPublic() || $course->status !== 'active') {
            abort(404);
        }

        $student = auth()->user();

        // Check if student is already enrolled
        $isEnrolled = $student->courses()
            ->where('original_course_id', $course->id)
            ->exists();

        // Get the enrolled course if exists
        $enrolledCourse = null;
        if ($isEnrolled) {
            $enrolledCourse = $student->courses()
                ->where('original_course_id', $course->id)
                ->first();
        }

        $course->load([
            'examBoard',
            'creator',
            'modules' => function ($query) {
                $query->orderBy('order')
                      ->with(['topics' => function ($query) {
                          $query->orderBy('order');
                      }]);
            },
        ]);

        // Calculate stats
        $moduleCount = $course->modules->count();
        $topicCount = $course->modules->sum(function ($module) {
            return $module->topics->count();
        });

        return Inertia::render('Student/Catalog/Show', [
            'course' => $course,
            'is_enrolled' => $isEnrolled,
            'enrolled_course' => $enrolledCourse,
            'can_enroll' => $course->canEnroll($student),
            'module_count' => $moduleCount,
            'topic_count' => $topicCount,
            'enrollment_stats' => [
                'total' => $course->current_enrollment,
                'limit' => $course->enrollment_limit,
                'available' => $course->enrollment_limit
                    ? $course->enrollment_limit - $course->current_enrollment
                    : null,
            ],
        ]);
    }

    public function preview(Course $course)
    {
        // Ensure course is public and accessible (preview for unauthenticated users too)
        if (!$course->isPublic() || $course->status !== 'active') {
            abort(404);
        }

        $isEnrolled = false;
        $canEnroll = false;

        if (auth()->check()) {
            $student = auth()->user();
            $isEnrolled = $student->courses()
                ->where('original_course_id', $course->id)
                ->exists();
            $canEnroll = $course->canEnroll($student);
        }

        $course->load([
            'examBoard',
            'creator',
            'modules' => function ($query) {
                $query->orderBy('order')
                      ->limit(3) // Limit for preview
                      ->with(['topics' => function ($query) {
                          $query->orderBy('order')->limit(2);
                      }]);
            },
        ]);

        return Inertia::render('Student/Catalog/Preview', [
            'course' => $course,
            'is_enrolled' => $isEnrolled,
            'can_enroll' => $canEnroll,
            'is_authenticated' => auth()->check(),
        ]);
    }

    public function enroll(Request $request, Course $course)
    {
        $student = auth()->user();

        if (!$course->canEnroll($student)) {
            return redirect()->back()
                ->with('error', 'Unable to enroll in this course. It may be full or you may already be enrolled.');
        }

        try {
            if ($course->enrollStudent($student)) {
                // Get the cloned course
                $clonedCourse = $student->courses()
                    ->where('original_course_id', $course->id)
                    ->first();

                return redirect()->route('student.courses.show', $clonedCourse->id)
                    ->with('success', 'Successfully enrolled in the course! You can now start learning.');
            }

            return redirect()->back()
                ->with('error', 'Failed to enroll in the course. Please try again.');

        } catch (\Exception $e) {
            \Log::error('Course enrollment failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'An error occurred while enrolling. Please try again.');
        }
    }

    public function myEnrolledCourses(Request $request)
    {
        $student = auth()->user();

        $query = $student->courses()
            ->whereNotNull('original_course_id')
            ->with(['originalCourse' => function ($query) {
                $query->with('creator');
            }])
            ->latest();

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('subject', 'like', "%{$request->search}%");
            });
        }

        $courses = $query->paginate(12);

        return Inertia::render('Student/Catalog/MyCourses', [
            'courses' => $courses,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function unenroll(Request $request, Course $course)
    {
        $student = auth()->user();

        // Check if this is the original course or cloned course
        if ($course->created_by === 'admin') {
            // Original public course - find and delete the cloned course
            $clonedCourse = $student->courses()
                ->where('original_course_id', $course->id)
                ->first();

            if (!$clonedCourse) {
                return redirect()->back()
                    ->with('error', 'You are not enrolled in this course.');
            }

            $courseToDelete = $clonedCourse;
        } else {
            // Cloned course - check if it belongs to student
            if ($course->student_profile_id !== $student->studentProfile->id) {
                abort(403);
            }

            $courseToDelete = $course;
        }

        if (!confirm('Are you sure you want to unenroll from this course? All your progress will be lost.')) {
            return redirect()->back();
        }

        try {
            // Delete the enrollment record
            CourseEnrollment::where('course_id', $course->id)
                ->where('user_id', $student->id)
                ->delete();

            // Decrement enrollment count on original course
            if ($courseToDelete->original_course_id) {
                Course::where('id', $courseToDelete->original_course_id)
                    ->decrement('current_enrollment');
            }

            // Delete the cloned course
            $courseToDelete->delete();

            return redirect()->route('student.catalog.index')
                ->with('success', 'Successfully unenrolled from the course.');

        } catch (\Exception $e) {
            \Log::error('Course unenrollment failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to unenroll from the course. Please try again.');
        }
    }
}
