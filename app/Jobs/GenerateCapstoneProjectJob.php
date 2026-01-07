<?php
// app/Jobs/GenerateCapstoneProjectJob.php

namespace App\Jobs;

use App\Models\Course;
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

class GenerateCapstoneProjectJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 300; // 5 minutes
    public $backoff = [30, 60, 120];

    protected $course;
    protected $topic;

    public function __construct(Course $course, CourseOutline $topic = null)
    {
        $this->course = $course->withoutRelations();
        $this->topic = $topic ? $topic->withoutRelations() : null;
    }

    public function handle(ContentGenerationService $contentGenerationService)
    {
        try {
            Log::info('Starting capstone project generation job', [
                'course_id' => $this->course->id,
                'course_title' => $this->course->title,
                'topic_id' => $this->topic?->id,
                'job_id' => $this->job->getJobId()
            ]);

            // Reload course with relations
            $course = Course::with(['modules.topics'])->findOrFail($this->course->id);

            // Reload topic if provided
            $topic = $this->topic ? CourseOutline::find($this->topic->id) : null;

            DB::beginTransaction();

            // Generate capstone project using the service
            $result = $contentGenerationService->generateCapstoneProject($course, $topic);

            // Update course status
            $course->update([
                'capstone_generated_at' => now(),
                'capstone_generation_status' => 'completed',
            ]);

            // Update topic if it was a capstone topic
            if ($topic) {
                $topic->update([
                    'needs_content_generation' => false,
                    'content_generated_at' => now(),
                ]);
            }

            DB::commit();

            Log::info('Capstone project generation job completed', [
                'course_id' => $course->id,
                'capstone_project_id' => $result['capstone_project']->id,
                'success' => $result['success'] ?? false
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Capstone project generation job failed', [
                'course_id' => $this->course->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'job_id' => $this->job->getJobId()
            ]);

            // Update course status
            $course = Course::find($this->course->id);
            if ($course) {
                $course->update([
                    'capstone_generation_status' => 'failed',
                    'capstone_generation_error' => $e->getMessage(),
                ]);
            }

            throw $e;
        }
    }

    public function failed(\Exception $exception)
{
    $courseId = $this->course?->id ?? 'unknown';
    $courseTitle = $this->course?->title ?? 'Unknown Course';

    Log::error('Capstone project generation job failed permanently', [
        'course_id' => $courseId,
        'course_title' => $courseTitle,
        'error' => $exception->getMessage(),
        'job_id' => $this->job->getJobId(),
        'attempts' => $this->attempts()
    ]);

    // Only update if course exists
    if ($this->course?->id) {
        $course = Course::find($this->course->id);
        if ($course) {
            $course->update([
                'capstone_generation_status' => 'failed',
                'capstone_generation_error' => 'Permanent failure: ' . $exception->getMessage(),
            ]);
        }
    }
}
}
