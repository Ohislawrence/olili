<?php
// app/Http/Controllers/Admin/CourseOutlineController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateGeneralQuizJob;
use App\Models\Course;
use App\Models\CourseOutline;
use App\Models\Module;
use App\Services\ContentGenerationService;
use App\Services\QuizGenerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CourseOutlineController extends Controller
{
    protected $contentGenerationService;
    protected $quizGenerationService;

    public function __construct(ContentGenerationService $contentGenerationService, QuizGenerationService $quizGenerationService)
    {
        $this->contentGenerationService = $contentGenerationService;
        $this->quizGenerationService = $quizGenerationService;
    }

    /**
     * Generate content for a specific topic
     */
    public function generateContent(Course $course, Module $module, CourseOutline $topic)
    {
        // Verify the topic belongs to the module and course
        if ($topic->module_id !== $module->id || $module->course_id !== $course->id) {
            return redirect()->back()
                ->with('error', 'Topic does not belong to this course.');
        }

        try {
            // Generate content based on topic type
            $contentType = 'text'; // Default content type
            if ($topic->type === 'lesson' || $topic->type === 'topic') {
                $contentType = 'text';
            } elseif ($topic->type === 'video') {
                $contentType = 'video_script';
            } elseif ($topic->type === 'quiz') {
                return redirect()->back()
                    ->with('error', 'Use quiz generation for quiz topics.');
            }

            $content = $this->contentGenerationService->generateContentForOutline($topic, $contentType);

            // Update topic to mark content as generated
            $topic->update([
                'needs_content_generation' => false,
                'content_generated_at' => now(),
            ]);

            Log::info('Content generated for topic', [
                'topic_id' => $topic->id,
                'course_id' => $course->id,
                'content_type' => $contentType
            ]);

            return redirect()->back()
                ->with('success', 'Content generated successfully!')
                ->with('content_preview', $content);

        } catch (\Exception $e) {
            Log::error('Content generation failed', [
                'topic_id' => $topic->id,
                'course_id' => $course->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to generate content: ' . $e->getMessage());
        }
    }

    /**
     * Generate quiz for a topic
     */
    public function generateQuiz(Course $course, Module $module, CourseOutline $topic)
    {
        // Verify the topic belongs to the module and course
        if ($topic->module_id !== $module->id || $module->course_id !== $course->id) {
            return redirect()->back()
                ->with('error', 'Topic does not belong to this course.');
        }

        // Check if topic is quiz-enabled
        if (!$topic->has_quiz && $topic->type !== 'quiz') {
            return redirect()->back()
                ->with('error', 'This topic does not have quiz generation enabled.');
        }

        try {

            // Determine number of questions based on topic duration
            $questionCount = min(10, max(3, ceil($topic->estimated_duration_minutes / 30)));

            $quiz = $this->contentGenerationService->generateQuizForOutline($topic, $questionCount);

            // Update topic to mark quiz as generated
            $topic->update([
                'needs_quiz_generation' => false,
                'quiz_generated_at' => now(),
            ]);


            Log::info('Quiz generated for topic', [
                'topic_id' => $topic->id,
                'course_id' => $course->id,
                'question_count' => $questionCount
            ]);

            return redirect()->back()
                ->with('success', "Quiz generated with {$questionCount} questions!")
                ->with('quiz_id', $quiz->id ?? null);

        } catch (\Exception $e) {
            Log::error('Quiz generation failed', [
                'topic_id' => $topic->id,
                'course_id' => $course->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to generate quiz: ' . $e->getMessage());
        }
    }

    public function generateContentForGeneralQuiz(Course $course, Module $module, CourseOutline $topic)
    {
        // For general course quiz, we don't need module/topic validation
        // since it's for the entire course

        try {
            // Determine number of questions based on course content
            $totalTopics = CourseOutline::whereHas('module', function($query) use ($course) {
                $query->where('course_id', $course->id);
            })->count();

            $questionCount = min(30, max(10, ceil($totalTopics * 1.5)));

            $config = [
                'question_count' => $questionCount,
                'time_limit' => ceil($questionCount * 1.5),
                'max_attempts' => 3,
                'passing_score' => 70,
                'difficulty' => 'mixed',
            ];

            // Use the new general quiz generation method
            //$quiz = $this->quizGenerationService->generateGeneralCourseQuiz($course, $config);
            GenerateGeneralQuizJob::dispatch($course, $config, $topic);


            return redirect()->back()
                ->with('success', 'Quiz generation started in background. You will be notified when complete.')
                ->with('job_started', true);

        } catch (\Exception $e) {
            Log::error('General quiz generation failed', [
                'course_id' => $course->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to generate course review quiz: ' . $e->getMessage());
        }
    }


    /**
     * Preview generated content
     */
    public function previewContent(Course $course, Module $module, CourseOutline $topic)
    {
        // Verify the topic belongs to the module and course
        if ($topic->module_id !== $module->id || $module->course_id !== $course->id) {
            abort(403, 'Topic does not belong to this course.');
        }

        // Get the latest generated content for this topic
        $content = $topic->contents()->latest()->first();

        if (!$content) {
            return response()->json([
                'error' => 'No content generated yet for this topic.'
            ], 404);
        }

        return response()->json([
            'content' => $content->content,
            'content_type' => $content->content_type,
            'generated_at' => $content->created_at->format('Y-m-d H:i:s'),
            'read_time' => $this->calculateReadTime($content->content)
        ]);
    }

    private function calculateReadTime(string $content): string
    {
        $wordCount = str_word_count(strip_tags($content));
        $minutes = ceil($wordCount / 200); // 200 words per minute
        return $minutes . ' min read';
    }
}
