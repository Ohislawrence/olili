<?php
// app/Jobs/GenerateExamPrepQuestions.php

namespace App\Jobs;

use App\Models\ExamPrep;
use App\Services\ExamPrepGenerationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateExamPrepQuestions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 minutes
    public $tries = 3;

    protected $examPrep;

    /**
     * Create a new job instance.
     */
    public function __construct(ExamPrep $examPrep)
    {
        $this->examPrep = $examPrep;
    }

    /**
     * Execute the job.
     */
    public function handle(ExamPrepGenerationService $generationService): void
    {
        Log::info('Processing exam prep generation job', [
            'exam_prep_id' => $this->examPrep->id,
            'attempt' => $this->attempts()
        ]);

        try {
            $result = $generationService->generateQuestions($this->examPrep);

            Log::info('Exam prep generation completed', [
                'exam_prep_id' => $this->examPrep->id,
                'questions_generated' => $result['question_count'] ?? 0
            ]);

        } catch (\Exception $e) {
            Log::error('Exam prep generation job failed', [
                'exam_prep_id' => $this->examPrep->id,
                'error' => $e->getMessage()
            ]);

            $this->release(300); // Release back to queue in 5 minutes
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Exam prep generation job failed permanently', [
            'exam_prep_id' => $this->examPrep->id,
            'error' => $exception->getMessage()
        ]);

        $this->examPrep->update([
            'content_generation_status' => 'failed',
            'generation_summary' => [
                'error' => $exception->getMessage(),
                'failed_at' => now()->toDateTimeString(),
                'attempts' => $this->attempts()
            ]
        ]);
    }
}
