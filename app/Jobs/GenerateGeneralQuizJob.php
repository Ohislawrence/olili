<?php

namespace App\Jobs;

use App\Models\Course;
use App\Models\CourseOutline;
use App\Services\QuizGenerationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateGeneralQuizJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes timeout for job
    public $tries = 3;

    protected $course;
    protected $config;
    protected $topic;

    public function __construct(Course $course, array $config = [], CourseOutline $topic)
    {
        $this->course = $course;
        $this->config = $config;
        $this->topic = $topic;
    }

    public function handle(QuizGenerationService $quizService)
    {
        try {
                Log::info('Starting quiz generation job', [
                'course_id' => $this->course->id,
                'job_id' => $this->job->getJobId()
            ]);

            $config = [
                'question_count' => $this->config['question_count'] ?? 15,
                'difficulty' => $this->config['difficulty'] ?? 'mixed',
            ];

            $quiz = $quizService->generateGeneralCourseQuiz($this->course, $this->config, $this->topic);

            // Store the result somewhere or send notification
            // You could cache it or store in database
            cache()->put("quiz_generated_{$this->course->id}", [
                'quiz_id' => $quiz->id,
                'title' => $quiz->title,
                'question_count' => count($quiz->questions),
                'generated_at' => now()->toDateTimeString(),
            ], 3600);

            Log::info('General quiz generated via job', [
                'course_id' => $this->course->id,
                'quiz_id' => $quiz->id,
                'job_id' => $this->job->getJobId()
            ]);

        } catch (\Exception $e) {
            Log::error('General quiz job failed', [
                'course_id' => $this->course->id,
                'error' => $e->getMessage()
            ]);
            throw $e; // Will trigger retry
        }
    }
}
