<?php
// app/Services/QuizGenerationService.php

namespace App\Services;

use App\Models\CourseOutline;
use App\Models\Quiz;
use App\Services\Ai\BaseAiService;

class QuizGenerationService
{
    protected $aiService;

    public function __construct(BaseAiService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function generateQuiz(CourseOutline $outline, array $config = []): Quiz
    {
        $questionCount = $config['question_count'] ?? 10;
        $difficulty = $config['difficulty'] ?? 'medium';

        $questions = $this->generateQuizQuestions($outline, $questionCount, $difficulty);

        return Quiz::create([
            'course_outline_id' => $outline->id,
            'title' => "Quiz: {$outline->title}",
            'description' => "Assessment for {$outline->title}",
            'time_limit_minutes' => $config['time_limit'] ?? 30,
            'max_attempts' => $config['max_attempts'] ?? 3,
            'passing_score' => $config['passing_score'] ?? 70.0,
            'questions' => $questions,
            'ai_model_used' => $this->aiService->getProviderCode(),
        ]);
    }

    protected function generateQuizQuestions(CourseOutline $outline, int $questionCount, string $difficulty): array
    {
        $prompt = $this->buildQuizGenerationPrompt($outline, $questionCount, $difficulty);

        $messages = [
            [
                'role' => 'system',
                'content' => "You are an expert educational assessment designer. Create valid, fair quiz questions that accurately assess understanding."
            ],
            [
                'role' => 'user',
                'content' => $prompt
            ]
        ];

        $response = $this->aiService->chat($messages, [
            'temperature' => 0.3,
            'max_tokens' => 3000,
        ], 'quiz_generation');

        $questions = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Failed to parse quiz questions from AI response");
        }

        return $questions['questions'] ?? [];
    }

    protected function buildQuizGenerationPrompt(CourseOutline $outline, int $questionCount, string $difficulty): string
    {
        $course = $outline->course;

        return "
        Create a quiz with {$questionCount} questions for the following educational topic:

        COURSE: {$course->title}
        TOPIC: {$outline->title}
        DIFFICULTY LEVEL: {$difficulty}
        STUDENT LEVEL: {$course->level}

        KEY CONCEPTS:
        " . implode("\n", $outline->key_concepts ?? []) . "

        LEARNING OBJECTIVES:
        " . implode("\n", $outline->learning_objectives ?? []) . "

        REQUIREMENTS:
        - Include a mix of question types (multiple_choice, true_false, fill_in_blank)
        - Questions should test different cognitive levels (remember, understand, apply, analyze)
        - Provide clear, unambiguous questions
        - Include exactly 4 options for multiple choice questions
        - Provide detailed explanations for correct answers
        - Ensure questions are appropriate for the difficulty level

        FORMAT:
        Return a JSON object with this structure:
        {
            \"questions\": [
                {
                    \"question\": \"Question text here\",
                    \"type\": \"multiple_choice|true_false|fill_in_blank\",
                    \"options\": [\"Option A\", \"Option B\", \"Option C\", \"Option D\"], // for multiple_choice only
                    \"correct_answer\": \"Correct answer or option\",
                    \"explanation\": \"Detailed explanation of why this is correct\",
                    \"points\": 1,
                    \"difficulty\": \"easy|medium|hard\"
                }
            ]
        }
        ";
    }
}
