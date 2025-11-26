<?php
// app/Http/Controllers/Student/FlashcardController.php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseOutline;
use App\Models\Flashcard;
use App\Models\FlashcardSet;
use App\Services\FlashcardService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class FlashcardController extends Controller
{
    protected $flashcardService;

    public function __construct()
    {
        $this->flashcardService = new FlashcardService();
    }

    public function index(Request $request)
    {
        $student = auth()->user();

        // FIXED: Specify the table for id column
        $courses = $student->courses()
            ->where('status', 'active')
            ->with(['modules.topics'])
            ->get(['courses.id', 'courses.title', 'courses.subject']); // Explicitly select columns

        $flashcardSets = FlashcardSet::with(['course', 'flashcards'])
            ->where('user_id', $student->id)
            ->latest()
            ->paginate(12);

        // Calculate due counts for each set
        foreach ($flashcardSets as $set) {
            $set->due_flashcards_count = $set->flashcards()
                ->where(function($query) {
                    $query->where('next_review_date', '<=', now())
                          ->orWhereNull('next_review_date');
                })
                ->count();
        }

        return Inertia::render('Student/Flashcards/Index', [
            'courses' => $courses,
            'flashcard_sets' => $flashcardSets,
            'filters' => $request->only(['course_id', 'search']),
        ]);
    }

    public function create(Request $request)
    {
        $student = auth()->user();

        $courseId = $request->get('course_id');
        $outlineId = $request->get('outline_id');

        $course = null;
        $outline = null;
        $availableOutlines = collect();

        if ($courseId) {
            // FIXED: Specify table for id column
            $course = $student->courses()
                ->where('courses.id', $courseId)
                ->where('status', 'active')
                ->firstOrFail();

            $availableOutlines = $course->modules()
                ->with(['topics' => function ($query) {
                    $query->orderBy('order');
                }])
                ->orderBy('order')
                ->get()
                ->flatMap(function ($module) {
                    return $module->topics->map(function ($topic) use ($module) {
                        return [
                            'id' => $topic->id,
                            'title' => $topic->title,
                            'description' => $topic->description,
                            'module_title' => $module->title,
                            'full_title' => "{$module->title} → {$topic->title}",
                        ];
                    });
                });
        }

        // FIXED: Specify table for id column
        $courses = $student->courses()
            ->where('status', 'active')
            ->get(['courses.id', 'courses.title']);

        return Inertia::render('Student/Flashcards/Create', [
            'courses' => $courses,
            'selected_course' => $course,
            'selected_outline' => $outline,
            'available_outlines' => $availableOutlines,
        ]);
    }

    public function store(Request $request)
    {
        $student = auth()->user();

        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'course_outline_id' => 'nullable|exists:course_outlines,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'number_of_cards' => 'required|integer|min:1|max:50',
            'difficulty_level' => 'required|in:easy,medium,hard',
            'include_explanations' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            // Verify course access - FIXED: Specify table for id column
            $course = $student->courses()
                ->where('courses.id', $request->course_id)
                ->firstOrFail();

            $outline = null;
            if ($request->course_outline_id) {
                $outline = CourseOutline::where('id', $request->course_outline_id)
                    ->whereHas('module.course', function ($query) use ($request) {
                        $query->where('id', $request->course_id);
                    })
                    ->with('module')
                    ->firstOrFail();
            }

            // Create flashcard set using service
            $flashcardSet = $this->flashcardService->createFlashcardSet(
                $student->id,
                $request->course_id,
                $request->title,
                $request->description
            );

            // Generate flashcards using service
            $this->flashcardService->generateFlashcards(
                $flashcardSet,
                $course,
                $outline,
                $request->number_of_cards,
                $request->difficulty_level,
                $request->include_explanations ?? false
            );

            DB::commit();

            return redirect()->route('student.flashcards.show', $flashcardSet->id)
                ->with('success', "Flashcard set created with {$request->number_of_cards} cards!");

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Failed to create flashcard set', [
                'user_id' => $student->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to create flashcard set: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(FlashcardSet $flashcardSet)
    {
        $student = auth()->user();

        if ($flashcardSet->user_id !== $student->id) {
            abort(403, 'Unauthorized access to this flashcard set.');
        }

        $flashcardSet->load(['course', 'flashcards' => function ($query) {
            $query->orderBy('created_at');
        }]);

        return Inertia::render('Student/Flashcards/Show', [
            'flashcard_set' => $flashcardSet,
        ]);
    }

    public function study(FlashcardSet $flashcardSet)
    {
        $student = auth()->user();

        if ($flashcardSet->user_id !== $student->id) {
            abort(403, 'Unauthorized access to this flashcard set.');
        }

        $dueFlashcards = $flashcardSet->flashcards()
            ->where(function($query) {
                $query->where('next_review_date', '<=', now())
                      ->orWhereNull('next_review_date');
            })
            ->orderBy('next_review_date')
            ->get();

        $allFlashcards = $flashcardSet->flashcards()
            ->orderBy('created_at')
            ->get();

        return Inertia::render('Student/Flashcards/Study', [
            'flashcard_set' => $flashcardSet,
            'due_flashcards' => $dueFlashcards,
            'all_flashcards' => $allFlashcards,
            'study_mode' => request('mode', 'due'), // due or all
        ]);
    }

    public function updateProgress(Flashcard $flashcard, Request $request)
    {
        $student = auth()->user();

        if ($flashcard->user_id !== $student->id) {
            abort(403, 'Unauthorized access to this flashcard.');
        }

        $request->validate([
            'quality' => 'required|integer|min:0|max:5',
        ]);

        try {
            $updatedFlashcard = $this->flashcardService->updateFlashcardProgress($flashcard, $request->quality);

            return response()->json([
                'success' => true,
                'next_review_date' => $updatedFlashcard->next_review_date->format('Y-m-d H:i:s'),
                'interval' => $updatedFlashcard->interval,
                'repetitions' => $updatedFlashcard->repetitions,
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to update flashcard progress', [
                'flashcard_id' => $flashcard->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to update progress',
            ], 500);
        }
    }

    public function destroy(FlashcardSet $flashcardSet)
    {
        $student = auth()->user();

        if ($flashcardSet->user_id !== $student->id) {
            abort(403, 'Unauthorized access to this flashcard set.');
        }

        try {
            $flashcardSet->delete();

            return redirect()->route('student.flashcards.index')
                ->with('success', 'Flashcard set deleted successfully.');

        } catch (\Exception $e) {
            \Log::error('Failed to delete flashcard set', [
                'flashcard_set_id' => $flashcardSet->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to delete flashcard set.');
        }
    }

    public function getCourseOutlines(Course $course)
    {
        $student = auth()->user();

        // Verify course access - FIXED: Specify table for id column
        $course = $student->courses()
            ->where('courses.id', $course->id)
            ->firstOrFail();

        $outlines = $course->modules()
            ->with(['topics' => function ($query) {
                $query->orderBy('order');
            }])
            ->orderBy('order')
            ->get()
            ->flatMap(function ($module) {
                return $module->topics->map(function ($topic) use ($module) {
                    return [
                        'id' => $topic->id,
                        'title' => $topic->title,
                        'description' => $topic->description,
                        'module_title' => $module->title,
                        'full_title' => "{$module->title} → {$topic->title}",
                        'learning_objectives' => $topic->learning_objectives ?? [],
                        'key_concepts' => $topic->key_concepts ?? [],
                    ];
                });
            });

        return response()->json([
            'outlines' => $outlines,
        ]);
    }

    public function resetProgress(FlashcardSet $flashcardSet)
    {
        $student = auth()->user();

        if ($flashcardSet->user_id !== $student->id) {
            abort(403, 'Unauthorized access to this flashcard set.');
        }

        try {
            $this->flashcardService->resetFlashcardProgress($flashcardSet);

            return redirect()->back()
                ->with('success', 'Flashcard progress reset successfully.');

        } catch (\Exception $e) {
            \Log::error('Failed to reset flashcard progress', [
                'flashcard_set_id' => $flashcardSet->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to reset progress.');
        }
    }
}
