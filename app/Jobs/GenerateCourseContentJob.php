<?php
// app/Jobs/GenerateCourseContentJob.php

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
use App\Models\GeneratedContent;

class GenerateCourseContentJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 300; // 5 minutes
    public $backoff = [30, 60, 120];
    public $maxExceptions = 3;

    protected $topic;
    protected $contentType;

    public function __construct(CourseOutline $topic, string $contentType = 'text')
    {
        $this->topic = $topic->withoutRelations();
        $this->contentType = $contentType;
    }

    public function handle(ContentGenerationService $contentGenerationService)
    {
        // Check if this job belongs to a batch
        if ($this->batch() && $this->batch()->cancelled()) {
            Log::info('Job cancelled due to batch cancellation', [
                'topic_id' => $this->topic->id,
                'job_id' => $this->job->getJobId()
            ]);
            return;
        }
        try {
            Log::info('Starting content generation job', [
                'topic_id' => $this->topic->id,
                'topic_title' => $this->topic->title,
                'content_type' => $this->contentType,
                'job_id' => $this->job->getJobId(),
                'queue' => $this->queue
            ]);

            // Reload topic with relationships
            $topic = CourseOutline::with(['module.course', 'contents'])
                ->findOrFail($this->topic->id);

            DB::beginTransaction();

            // Determine content type if not specified
            $contentType = $this->contentType ?? $this->determineContentType($topic);

            Log::debug('Content type determined', [
                'topic_id' => $topic->id,
                'content_type' => $contentType
            ]);

            // Generate content
            $content = $contentGenerationService->generateContentForOutline($topic, $contentType);

            if (empty($content)) {
                throw new \Exception('Content generation returned empty result');
            }

            // Create content record
            $generatedContent = GeneratedContent::create([
                'course_outline_id' => $topic->id,
                'content' => $content,
                'content_type' => $contentType,
                'generated_at' => now(),
                'status' => 'generated',
                'word_count' => str_word_count(strip_tags($content)),
                'read_time_minutes' => ceil(str_word_count(strip_tags($content)) / 200), // 200 words per minute
            ]);

            // Update topic status
            $topic->update([
                'needs_content_generation' => false,
                'content_generated_at' => now(),
                'last_content_generation_attempt' => now(),
                'generation_errors' => null, // Clear any previous errors
            ]);

            // Update module if all topics have content
            $this->updateModuleStatus($topic);

            DB::commit();

            Log::info('Content generation job completed successfully', [
                'topic_id' => $topic->id,
                'content_id' => $generatedContent->id,
                'content_type' => $contentType,
                'word_count' => $generatedContent->word_count,
                'read_time_minutes' => $generatedContent->read_time_minutes
            ]);

            // Dispatch quiz generation if needed (with delay)
            if ($topic->has_quiz && $topic->needs_quiz_generation) {
                GenerateCourseQuizzesJob::dispatch($topic)
                    ->onQueue('course_generation')
                    ->delay(now()->addSeconds(30)); // Wait 30 seconds

                Log::debug('Quiz generation job dispatched', [
                    'topic_id' => $topic->id
                ]);
            }

            /**
            if ($topic->type === 'quiz' && !$topic->has_quiz && !$topic->needs_quiz_generation) {
                GenerateGeneralQuizJob::dispatch($course, $config, $topic)
                    ->onQueue('course_generation')
                    ->delay(now()->addSeconds(30)); // Wait 30 seconds

                Log::debug('General Quiz generation job dispatched', [
                    'topic_id' => $topic->id
                ]);
            }
                 */

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Content generation job failed', [
                'topic_id' => $this->topic->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'job_id' => $this->job->getJobId()
            ]);

            // Update topic with error information
            if (isset($topic)) {
                $topic->update([
                    'last_content_generation_attempt' => now(),
                    'generation_errors' => array_merge(
                        $topic->generation_errors ?? [],
                        [
                            [
                                'timestamp' => now(),
                                'error' => $e->getMessage(),
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

    Log::error('Content generation job failed permanently', [
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
                            'error' => 'Permanent failure: ' . $exception->getMessage(),
                            'job_id' => $this->job->getJobId(),
                            'status' => 'failed'
                        ]
                    ]
                ),
                'needs_content_generation' => true, // Keep it as pending for manual retry
            ]);

            // Update course statistics if possible
            if ($topic && $topic->module) {
                $course = $topic->module->course;
                if ($course) {
                    $failedCount = ($course->failed_generation_count ?? 0) + 1;
                    $course->update([
                        'failed_generation_count' => $failedCount,
                        'last_generation_failure_at' => now(),
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
     * Determine appropriate content type based on topic
     */
    private function determineContentType(CourseOutline $topic): string
    {
        // Map topic types to content types
        $typeMap = [
            'video' => 'video_script',
            'quiz' => 'quiz_content',
            'project' => 'project_brief',
            'exercise' => 'interactive_exercise',
            'interactive' => 'interactive_exercise',
            'lesson' => 'text',
            'topic' => 'text',
            'text' => 'text',
        ];

        $topicType = strtolower($topic->type ?? 'topic');

        if ($topic->has_project) {
            return 'project_brief';
        }

        if ($topic->has_quiz && $topic->type === 'quiz') {
            return 'quiz_content';
        }

        return $typeMap[$topicType] ?? 'text';
    }

    /**
     * Update module status when all topics have content
     */
    private function updateModuleStatus(CourseOutline $topic): void
{
    $module = $topic->module;

    if ($module) {
        $pendingTopics = $module->topics()
            ->where('course_outlines.needs_content_generation', true) // Specify table
            ->count();

        if ($pendingTopics === 0) {
            $module->update([
                'needs_content_generation' => false,
                'content_completed_at' => now(),
            ]);

            Log::debug('Module content completed', [
                'module_id' => $module->id,
                'module_title' => $module->title
            ]);

            // Update course statistics
            $this->updateCourseStats($module->course);
        }
    }
}


    /**
     * Update course generation statistics
     */
    private function updateCourseStats($course): void
    {
        if (!$course) return;

        $totalTopics = $course->topics()->count();
        $generatedTopics = $course->topics()
            ->where('course_outlines.needs_content_generation', false) // Specify table
            ->count();

        $progress = $totalTopics > 0 ? ($generatedTopics / $totalTopics) * 100 : 0;

        $course->update([
            'content_generation_progress' => $progress,
            'pending_content_count' => $totalTopics - $generatedTopics,
            'last_content_update_at' => now(),
        ]);

        // Check if all content is generated
        if ($progress >= 100) {
            $course->update([
                'content_generation_completed_at' => now(),
                'status' => $course->status === 'draft' ? 'ready_for_review' : $course->status,
            ]);

            Log::info('Course content generation fully completed', [
                'course_id' => $course->id,
                'course_title' => $course->title
            ]);
        }
    }

    /**
     * Calculate read time in minutes
     */
    private function calculateReadTime(string $content): int
    {
        $wordCount = str_word_count(strip_tags($content));
        return max(1, ceil($wordCount / 200)); // At least 1 minute
    }

    /**
     * Get the middleware the job should pass through
     */
    public function middleware(): array
    {
        return [
            // You can add rate limiting middleware here if needed
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
