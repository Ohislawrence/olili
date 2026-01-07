<?php
// app/Jobs/GenerateCourseQuizzesJob.php

namespace App\Jobs;

use App\Models\CourseOutline;
use App\Services\ContentGenerationService;
use Illuminate\Bus\Queueable;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GenerateCourseQuizzesJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 300; // 5 minutes
    public $backoff = [30, 60, 120];
    public $maxExceptions = 3;

    protected $topic;
    protected $questionCount;

    public function __construct(CourseOutline $topic, int $questionCount = null)
    {
        $this->topic = $topic->withoutRelations();
        $this->questionCount = $questionCount;
    }

    public function handle(ContentGenerationService $contentGenerationService)
    {
        try {
            $topic = CourseOutline::find($this->topic->id);

            if (!$topic) {
                Log::warning('Topic not found, skipping quiz generation', [
                    'topic_id' => $this->topic->id,
                    'job_id' => $this->job->getJobId()
                ]);

                // Release the job back to the queue with delay
                // Or just return to mark as completed
                $this->delete(); // Remove from queue
                return;
            }

            Log::info('Starting quiz generation job', [
                'topic_id' => $topic->id, // Use the reloaded topic
                'topic_title' => $topic->title,
                'question_count' => $this->questionCount,
                'job_id' => $this->job->getJobId(),
                'queue' => $this->queue
            ]);


            // Reload topic with relationships
            $topic = CourseOutline::with(['module.course', 'quiz'])
                ->findOrFail($this->topic->id);

            // Check if topic supports quizzes
            if (!$topic->has_quiz) {
                Log::warning('Topic does not support quizzes, skipping', [
                    'topic_id' => $topic->id,
                    'topic_title' => $topic->title
                ]);
                return;
            }

            // Check if quiz already exists
            if ($topic->quiz && $topic->quiz->is_active && !empty($topic->quiz->questions)) {
                Log::info('Quiz already exists and is active for topic, skipping', [
                    'topic_id' => $topic->id,
                    'quiz_id' => $topic->quiz->id,
                    'existing_questions' => $topic->quiz->question_count
                ]);

                // Update topic status even if quiz exists
                $topic->update([
                    'needs_quiz_generation' => false,
                    'quiz_generated_at' => now(),
                ]);

                return;
            }

            DB::beginTransaction();

            // Determine question count if not specified
            $questionCount = $this->questionCount ?? $this->calculateQuestionCount($topic);

            Log::debug('Generating quiz with question count', [
                'topic_id' => $topic->id,
                'question_count' => $questionCount
            ]);

            // Generate quiz using the content generation service
            // This method already creates and saves the Quiz model
            $quiz = $contentGenerationService->generateQuizForOutline($topic, $questionCount);

            if (!$quiz) {
                throw new \Exception('Quiz generation service returned null');
            }

            // Update topic status
            $topic->update([
                'needs_quiz_generation' => false,
                'quiz_generated_at' => now(),
                'last_quiz_generation_attempt' => now(),
                'generation_errors' => null, // Clear any previous errors
            ]);

            // Update module if all topics have quizzes
            $this->updateModuleQuizStatus($topic);

            DB::commit();

            Log::info('Quiz generation job completed successfully', [
                'topic_id' => $topic->id,
                'quiz_id' => $quiz->id,
                'question_count' => $quiz->question_count,
                'time_limit' => $quiz->time_limit_minutes,
                'passing_score' => $quiz->passing_score
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Quiz generation job failed', [
                'topic_id' => $this->topic->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'job_id' => $this->job->getJobId()
            ]);

            // Update topic with error information
            if (isset($topic)) {
                $topic->update([
                    'last_quiz_generation_attempt' => now(),
                    'generation_errors' => array_merge(
                        $topic->generation_errors ?? [],
                        [
                            [
                                'timestamp' => now(),
                                'error' => 'Quiz generation failed: ' . $e->getMessage(),
                                'job_id' => $this->job->getJobId(),
                                'attempt' => $this->attempts()
                            ]
                        ]
                    ),
                ]);
            }

            throw $e; // Re-throw for Laravel's retry mechanism
        }
    }

    public function failed(\Exception $exception)
    {
        // Check if topic exists before accessing properties
        $topicId = $this->topic?->id ?? 'unknown';
        $topicTitle = $this->topic?->title ?? 'Unknown Topic';

        Log::error('Quiz generation job failed permanently', [
            'topic_id' => $topicId,
            'topic_title' => $topicTitle,
            'error' => $exception->getMessage(),
            'job_id' => $this->job->getJobId(),
            'attempts' => $this->attempts(),
            'queue' => $this->queue
        ]);

        // Only update if topic exists
        if ($this->topic?->id) {
            $topic = CourseOutline::find($this->topic->id);
            if ($topic) {
                $topic->update([
                    'generation_errors' => array_merge(
                        $topic->generation_errors ?? [],
                        [
                            [
                                'timestamp' => now(),
                                'error' => 'Quiz permanent failure: ' . $exception->getMessage(),
                                'job_id' => $this->job->getJobId(),
                                'status' => 'failed'
                            ]
                        ]
                    ),
                    'needs_quiz_generation' => true, // Keep it as pending for manual retry
                ]);

                // Update course statistics if possible
                if ($topic->module) {
                    $course = $topic->module->course;
                    if ($course) {
                        $failedCount = ($course->failed_quiz_generation_count ?? 0) + 1;
                        $course->update([
                            'failed_quiz_generation_count' => $failedCount,
                            'last_quiz_generation_failure_at' => now(),
                        ]);
                    }
                }
            }
        } else {
            Log::warning('Topic not found for failed job - unable to update status', [
                'job_id' => $this->job->getJobId()
            ]);
        }
    }

    /**
     * Calculate appropriate number of questions based on topic duration
     */
    private function calculateQuestionCount(CourseOutline $topic): int
    {
        $duration = $topic->estimated_duration_minutes ?? 60;

        // Calculate questions based on duration:
        // - 1 question per 15 minutes for short topics (min 3)
        // - 1 question per 20 minutes for medium topics
        // - 1 question per 30 minutes for long topics (max 20)

        if ($duration <= 45) {
            // Short topic: 3-5 questions
            return max(3, min(5, ceil($duration / 15)));
        } elseif ($duration <= 120) {
            // Medium topic: 5-10 questions
            return max(5, min(10, ceil($duration / 20)));
        } else {
            // Long topic: 10-20 questions
            return max(10, min(20, ceil($duration / 30)));
        }
    }

    /**
     * Update module quiz status
     */
    private function updateModuleQuizStatus(CourseOutline $topic): void
    {
        $module = $topic->module;

        if ($module) {
        $pendingQuizzes = $module->topics()
            ->where('course_outlines.has_quiz', true) // Specify table
            ->where('course_outlines.needs_quiz_generation', true) // Specify table
            ->count();

            if ($pendingQuizzes === 0) {
                $module->update([
                    'quiz_generation_completed_at' => now(),
                ]);

                Log::debug('Module quiz generation completed', [
                    'module_id' => $module->id,
                    'module_title' => $module->title
                ]);

                // Update course quiz statistics
                $this->updateCourseQuizStats($module->course);
            }
        }
    }

    /**
     * Update course quiz statistics
     */
    private function updateCourseQuizStats($course): void
    {
        if (!$course) return;

        $totalQuizTopics = $course->topics()
        ->where('course_outlines.has_quiz', true) // Specify table
        ->count();

        $generatedQuizTopics = $course->topics()
            ->where('course_outlines.has_quiz', true) // Specify table
            ->where('course_outlines.needs_quiz_generation', false) // Specify table
            ->count();

        $progress = $totalQuizTopics > 0 ? ($generatedQuizTopics / $totalQuizTopics) * 100 : 0;

        $course->update([
            'quiz_generation_progress' => $progress,
            'pending_quiz_count' => $totalQuizTopics - $generatedQuizTopics,
            'last_quiz_update_at' => now(),
        ]);

        // Check if all quizzes are generated
        if ($progress >= 100) {
            $course->update([
                'quiz_generation_completed_at' => now(),
            ]);

            Log::info('Course quiz generation fully completed', [
                'course_id' => $course->id,
                'course_title' => $course->title,
                'total_quizzes' => $totalQuizTopics
            ]);
        }
    }

    /**
     * Get the middleware the job should pass through
     */
    public function middleware(): array
    {
        return [
            // Add rate limiting if needed
        ];
    }

    /**
     * Determine the time at which the job should timeout
     */
    public function retryUntil()
    {
        return now()->addMinutes(30);
    }
}
