<?php
// app/Http/Controllers/Api/Student/CourseContentController.php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseOutline;
use App\Models\Quiz;
use App\Services\ContentGenerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseContentController extends Controller
{
    protected $contentGenerationService;

    public function __construct(ContentGenerationService $contentGenerationService)
    {
        $this->contentGenerationService = $contentGenerationService;
    }

    public function getTopicContent(Course $course, CourseOutline $topic)
    {
        // Verify the topic belongs to the course
        if ($topic->module->course_id !== $course->id) {
            return response()->json([
                'error' => 'Topic does not belong to this course'
            ], 403);
        }

        // Verify the user has access to this course
        if (!$this->userHasAccess($course)) {
            return response()->json([
                'error' => 'You do not have access to this course'
            ], 403);
        }

        // Load topic with relationships
        $topic->load([
            'contents',
            'quiz' => function($query) {
                $query->with(['attempts' => function($q) {
                    $q->where('user_id', Auth::id())
                      ->orderBy('created_at', 'desc');
                }]);
            },
            'module',
            'projectSubmissions' => function($query) {
                $query->where('user_id', Auth::id())
                      ->orderBy('created_at', 'desc');
            }
        ]);

        return response()->json([
            'data' => $topic,
            'success' => true
        ]);
    }

    public function getQuizDetails(Course $course, CourseOutline $topic)
    {
        if ($topic->module->course_id !== $course->id) {
            return response()->json(['error' => 'Invalid topic'], 403);
        }

        if (!$this->userHasAccess($course)) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $quiz = $topic->quiz()->with(['attempts' => function($q) {
            $q->where('user_id', Auth::id())
              ->orderBy('created_at', 'desc');
        }])->first();

        if (!$quiz) {
            return response()->json([
                'data' => null,
                'message' => 'No quiz found for this topic'
            ]);
        }

        return response()->json([
            'data' => $quiz,
            'success' => true
        ]);
    }

    public function getProjectDetails(Course $course, CourseOutline $topic)
    {
        if ($topic->module->course_id !== $course->id) {
            return response()->json(['error' => 'Invalid topic'], 403);
        }

        if (!$this->userHasAccess($course)) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $projectData = [
            'requirements' => $topic->key_concepts ?? [],
            'evaluation_criteria' => $course->capstoneProject?->evaluation_criteria ?? [],
            'submissions' => $topic->projectSubmissions()->where('user_id', Auth::id())->get(),
            'capstone_project' => $course->capstoneProject
        ];

        return response()->json([
            'data' => $projectData,
            'success' => true
        ]);
    }

    public function generateContent(Course $course, CourseOutline $topic, Request $request)
    {
        if ($topic->module->course_id !== $course->id) {
            return response()->json(['error' => 'Invalid topic'], 403);
        }

        if (!$this->userHasAccess($course)) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        try {
            $content = $this->contentGenerationService->generateContentForOutline($topic, 'text');

            // Reload topic with new content
            $topic->load('contents');

            return response()->json([
                'data' => $topic,
                'message' => 'Content generated successfully',
                'success' => true
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to generate content: ' . $e->getMessage(),
                'success' => false
            ], 500);
        }
    }

    public function generateQuiz(Course $course, CourseOutline $topic, Request $request)
    {
        if ($topic->module->course_id !== $course->id) {
            return response()->json(['error' => 'Invalid topic'], 403);
        }

        if (!$this->userHasAccess($course)) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        try {
            $quiz = $this->contentGenerationService->generateQuizForOutline($topic, 5);

            return response()->json([
                'data' => $quiz,
                'message' => 'Quiz generated successfully',
                'success' => true
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to generate quiz: ' . $e->getMessage(),
                'success' => false
            ], 500);
        }
    }

    private function userHasAccess(Course $course): bool
    {
        // Check if user is enrolled in the course
        // Adjust this based on your enrollment logic
        return Auth::check() && (
            $course->created_by_user_id === Auth::id() ||
            $course->students()->where('user_id', Auth::id())->exists()
        );
    }
}
