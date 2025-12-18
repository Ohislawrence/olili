<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\ExamBoard;
use App\Models\User;
use App\Services\CourseGenerationService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class CoursesController extends Controller
{
    protected $courseGenerationService;

    public function __construct(CourseGenerationService $courseGenerationService)
    {
        $this->courseGenerationService = $courseGenerationService;
        //$this->middleware('role:admin');
    }

    public function index(Request $request)
    {
        $query = Course::with(['examBoard', 'creator'])
            ->createdByAdmin()
            ->latest();

        // Filters
        if ($request->has('visibility') && $request->visibility) {
            $query->where('visibility', $request->visibility);
        }

        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('subject', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        $courses = $query->paginate(15);

        return Inertia::render('Admin/Catalog/Index', [
            'courses' => $courses,
            'filters' => $request->only(['search', 'visibility']),
            'visibilityOptions' => [
                'public' => 'Public',
                'private' => 'Private',
                'unlisted' => 'Unlisted'
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Catalog/Create', [
            'exam_boards' => ExamBoard::active()->get(),
            'subjects' => $this->getPopularSubjects(),
            'levels' => $this->getLevels(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'exam_board_id' => 'nullable|exists:exam_boards,id',
            'level' => 'required|string|in:beginner,intermediate,advanced,expert',
            'visibility' => 'required|string|in:public,private,unlisted',
            'enrollment_limit' => 'nullable|integer|min:1',
            'target_completion_date' => 'nullable|date|after:today',
            'estimated_duration_hours' => 'nullable|integer|min:1',
            'thumbnail' => 'nullable|image|max:2048',
            'learning_objectives' => 'nullable|array',
            'learning_objectives.*' => 'string|max:500',
            'prerequisites' => 'nullable|array',
            'prerequisites.*' => 'string|max:255',
        ]);

        try {
            // Handle thumbnail upload
            $thumbnailUrl = null;
            if ($request->hasFile('thumbnail')) {
                $path = $request->file('thumbnail')->store('course-thumbnails', 'public');
                $thumbnailUrl = Storage::url($path);
            }

            // Determine if course is public based on visibility
            $isPublic = $validated['visibility'] === 'public';

            $courseData = [
                'title' => $validated['title'],
                'slug' => Str::slug($validated['title']),
                'subject' => $validated['subject'],
                'description' => $validated['description'],
                'exam_board_id' => $validated['exam_board_id'] ?? null,
                'level' => $validated['level'],
                'created_by' => 'admin',
                'created_by_user_id' => auth()->id(),
                'is_public' => $isPublic,
                'visibility' => $validated['visibility'],
                'enrollment_limit' => $validated['enrollment_limit'] ?? null,
                'target_completion_date' => $validated['target_completion_date'] ?? null,
                'estimated_duration_hours' => $validated['estimated_duration_hours'] ?? null,
                'thumbnail_url' => $thumbnailUrl ?? null,
                'learning_objectives' => $validated['learning_objectives'] ?? [],
                'prerequisites' => $validated['prerequisites'] ?? [],
                'status' => 'active',
                'start_date' => now(),
            ];

            // Use course generation service to create the course structure
            $course = $this->courseGenerationService->generateAdminCourse($courseData);

            return redirect()->route('admin.courses.show', $course->id)
                ->with('success', 'Course created successfully!');

        } catch (\Exception $e) {
            \Log::error('Admin course creation failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to create course: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Course $course)
    {
        // Ensure it's an admin course
        if ($course->created_by !== 'admin') {
            abort(404);
        }

        $course->load([
            'examBoard',
            'modules' => function ($query) {
                $query->orderBy('order')
                      ->with(['topics' => function ($query) {
                          $query->orderBy('order');
                      }]);
            },
            'enrollments' => function ($query) {
                $query->with('user')->latest();
            },
            'creator',
        ]);

        $enrollmentStats = [
            'total' => $course->current_enrollment,
            'active' => $course->enrollments()->active()->count(),
            'completed' => $course->enrollments()->completed()->count(),
        ];

        return Inertia::render('Admin/Catalog/Show', [
            'course' => $course,
            'enrollment_stats' => $enrollmentStats,
            'recent_enrollments' => $course->enrollments()->with('user')->latest()->limit(10)->get(),
        ]);
    }

    public function edit(Course $course)
    {
        if ($course->created_by !== 'admin') {
            abort(404);
        }

        return Inertia::render('Admin/Catalog/Edit', [
            'course' => $course->load('examBoard'),
            'exam_boards' => ExamBoard::active()->get(),
            'subjects' => $this->getPopularSubjects(),
            'levels' => $this->getLevels(),
            'visibilityOptions' => [
                'public' => 'Public',
                'private' => 'Private',
                'unlisted' => 'Unlisted'
            ],
        ]);
    }

    public function update(Request $request, Course $course)
    {
        if ($course->created_by !== 'admin') {
            abort(404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'exam_board_id' => 'nullable|exists:exam_boards,id',
            'level' => 'required|string|in:beginner,intermediate,advanced,expert',
            'visibility' => 'required|string|in:public,private,unlisted',
            'enrollment_limit' => 'nullable|integer|min:1',
            'target_completion_date' => 'nullable|date|after:today',
            'estimated_duration_hours' => 'nullable|integer|min:1',
            'thumbnail' => 'nullable|image|max:2048',
            'learning_objectives' => 'nullable|array',
            'learning_objectives.*' => 'string|max:500',
            'prerequisites' => 'nullable|array',
            'prerequisites.*' => 'string|max:255',
            'status' => 'required|string|in:active,paused,archived',
        ]);

        try {
            // Handle thumbnail update
            if ($request->hasFile('thumbnail')) {
                // Delete old thumbnail if exists
                if ($course->thumbnail_url) {
                    $oldPath = str_replace('/storage/', '', parse_url($course->thumbnail_url, PHP_URL_PATH));
                    Storage::disk('public')->delete($oldPath);
                }

                $path = $request->file('thumbnail')->store('course-thumbnails', 'public');
                $validated['thumbnail_url'] = Storage::url($path);
            }

            $validated['is_public'] = $validated['visibility'] === 'public';
            unset($validated['thumbnail']); // Remove the file object

            $course->update($validated);

            return redirect()->route('admin.courses.show', $course->id)
                ->with('success', 'Course updated successfully!');

        } catch (\Exception $e) {
            \Log::error('Admin course update failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update course: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Course $course)
    {
        if ($course->created_by !== 'admin') {
            abort(404);
        }

        // Check if there are active enrollments
        if ($course->current_enrollment > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete course with active enrollments. Please archive it instead.');
        }

        try {
            // Delete thumbnail if exists
            if ($course->thumbnail_url) {
                $path = str_replace('/storage/', '', parse_url($course->thumbnail_url, PHP_URL_PATH));
                Storage::disk('public')->delete($path);
            }

            $course->delete();

            return redirect()->route('admin.courses.index')
                ->with('success', 'Course deleted successfully!');

        } catch (\Exception $e) {
            \Log::error('Admin course deletion failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to delete course: ' . $e->getMessage());
        }
    }

    public function enrollments(Course $course)
    {
        if ($course->created_by !== 'admin') {
            abort(404);
        }

        $enrollments = $course->enrollments()
            ->with('user')
            ->latest()
            ->paginate(20);

        return Inertia::render('Admin/Catalog/Enrollments', [
            'course' => $course->load('examBoard'),
            'enrollments' => $enrollments,
        ]);
    }

    public function toggleEnrollment(Request $request, Course $course)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'action' => 'required|in:enroll,unenroll',
        ]);

        $user = \App\Models\User::findOrFail($request->user_id);

        if ($request->action === 'enroll') {
            if ($course->enrollStudent($user)) {
                return response()->json(['success' => true, 'message' => 'Student enrolled successfully']);
            }
            return response()->json(['success' => false, 'message' => 'Unable to enroll student'], 422);
        } else {
            // Unenroll logic
            $enrollment = $course->enrollments()->where('user_id', $user->id)->first();
            if ($enrollment) {
                $enrollment->delete();
                $course->decrement('current_enrollment');
                return response()->json(['success' => true, 'message' => 'Student unenrolled successfully']);
            }
            return response()->json(['success' => false, 'message' => 'Student not enrolled'], 422);
        }
    }

    private function getPopularSubjects()
    {
        return [
            'Mathematics',
            'Physics',
            'Chemistry',
            'Biology',
            'English Language',
            'Literature',
            'History',
            'Geography',
            'Economics',
            'Accounting',
            'Business Studies',
            'Computer Science',
            'Programming',
            'Data Science',
            'Artificial Intelligence',
            'Web Development',
            'Mobile Development',
            'Graphic Design',
            'Music',
            'Art',
        ];
    }

    private function getLevels()
    {
        return [
            'beginner' => 'Beginner',
            'intermediate' => 'Intermediate',
            'advanced' => 'Advanced',
            'expert' => 'Expert',
        ];
    }

    public function search(Request $request)
{
    $request->validate([
        'search' => 'required|string|min:2',
        'exclude_enrolled' => 'boolean',
        'course_id' => 'exists:courses,id',
    ]);

    $query = User::role('student')
        ->where(function ($q) use ($request) {
            $q->where('name', 'like', "%{$request->search}%")
              ->orWhere('email', 'like', "%{$request->search}%");
        });

    if ($request->exclude_enrolled && $request->course_id) {
        $query->whereDoesntHave('enrollments', function ($q) use ($request) {
            $q->where('course_id', $request->course_id);
        });
    }

    $students = $query->limit(10)->get(['id', 'name', 'email']);

    return response()->json(['students' => $students]);
}

// Bulk enrollment
public function bulkEnroll(Request $request, Course $course)
{
    $request->validate([
        'emails' => 'required|array',
        'emails.*' => 'email',
        'status' => 'string|in:active,pending',
    ]);

    $enrolledCount = 0;
    $failedEmails = [];

    foreach ($request->emails as $email) {
        $user = User::where('email', $email)->role('student')->first();

        if ($user && $course->enrollStudent($user)) {
            $enrolledCount++;
        } else {
            $failedEmails[] = $email;
        }
    }

    return response()->json([
        'success' => true,
        'enrolled' => $enrolledCount,
        'failed' => $failedEmails,
    ]);
}

public function regenerate(Course $course)
{
    if ($course->created_by !== 'admin') {
        abort(404);
    }

    if ($course->current_enrollment > 0) {
        return back()->with('error', 'Cannot regenerate course with active enrollments.');
    }

    try {
        // Delete existing modules and topics
        $course->modules()->each(function ($module) {
            $module->topics()->delete();
            $module->delete();
        });

        // Regenerate course using AI
        $courseData = [
            'title' => $course->title,
            'slug' => Str::slug($course->title),
            'subject' => $course->subject,
            'description' => $course->description,
            'level' => $course->level,
            'estimated_duration_hours' => $course->estimated_duration_hours,
            'learning_objectives' => $course->learning_objectives,
            'prerequisites' => $course->prerequisites,
            'exam_board_id' => $course->exam_board_id,
        ];

        $courseGenerationService = app(CourseGenerationService::class);
        $courseGenerationService->generateAdminCourse($courseData);

        return back()->with('success', 'Course content regenerated successfully!');
    } catch (\Exception $e) {
        \Log::error('Course regeneration failed: ' . $e->getMessage());
        return back()->with('error', 'Failed to regenerate course: ' . $e->getMessage());
    }
}
}
