<?php
// app/Services/ContentGenerationService.php

namespace App\Services;

use App\Models\CourseOutline;
use App\Models\CourseContent;
use App\Models\Quiz;
use App\Services\Ai\BaseAiService;
use Illuminate\Support\Facades\Log;

class ContentGenerationService
{
    protected $aiService;

    public function __construct(BaseAiService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function generateContentForOutline(CourseOutline $outline, string $contentType = 'text'): CourseContent
    {
        $prompt = $this->buildContentPrompt($outline, $contentType);

        $messages = [
            [
                'role' => 'system',
                'content' => "You are an expert educator and content creator. Create engaging, educational content that helps students learn effectively based on the course structure and learning objectives. "
            ],
            [
                'role' => 'user',
                'content' => $prompt
            ]
        ];

        try {
            $content = $this->aiService->chat($messages, [
                'temperature' => 0.8,
                'max_tokens' => 3000,
            ], 'content_generation');

            return CourseContent::create([
                'course_outline_id' => $outline->id,
                'content' => $content,
                'content_type' => $contentType,
                'order' => $this->getNextContentOrder($outline),
                'ai_model_used' => $this->aiService->getProviderCode(),
                'generation_prompt' => ['prompt' => $prompt, 'content_type' => $contentType],
                'token_count' => 0, // Would be updated from usage log
            ]);

        } catch (\Exception $e) {
            Log::error('Content generation failed', [
                'outline_id' => $outline->id,
                'content_type' => $contentType,
                'error' => $e->getMessage()
            ]);
            throw new \Exception("Content generation failed: " . $e->getMessage());
        }
    }

    protected function buildContentPrompt(CourseOutline $outline, string $contentType): string
    {
        // Load the full hierarchy with error handling
        try {
            $outline->load(['module.course.studentProfile', 'module']);
        } catch (\Exception $e) {
            Log::error('Failed to load course hierarchy', [
                'outline_id' => $outline->id,
                'error' => $e->getMessage()
            ]);
            throw new \Exception("Failed to load course structure: " . $e->getMessage());
        }

        // Check if all relationships are loaded properly
        if (!$outline->module) {
            throw new \Exception("Topic is not associated with a module");
        }

        if (!$outline->module->course) {
            throw new \Exception("Module is not associated with a course");
        }

        $course = $outline->module->course;
        $module = $outline->module;

        // Student profile might be optional
        $studentProfile = $course->studentProfile;

        $prompt = "
        Create educational content for the following topic:

        COURSE CONTEXT:
        - Course: {$course->title}
        - Subject: {$course->subject}
        - Level: {$course->level}

        MODULE CONTEXT:
        - Module: {$module->title}
        - Module Description: " . ($module->description ?? 'No description') . "
        - Module Learning Objectives: " . $this->formatArrayForPrompt($module->learning_objectives ?? []) . "

        TOPIC DETAILS:
        - Topic: {$outline->title}
        - Topic Description: " . ($outline->description ?? 'No description') . "
        - Topic Learning Objectives: " . $this->formatArrayForPrompt($outline->learning_objectives ?? []) . "
        - Key Concepts: " . $this->formatArrayForPrompt($outline->key_concepts ?? []) . "
        - Resources: " . $this->formatArrayForPrompt($outline->resources ?? []) . "

        STUDENT CONTEXT:";

        if ($studentProfile) {
            $prompt .= "
            - Current Level: {$studentProfile->current_level}
            - Target Level: {$studentProfile->target_level}
            - Learning Goals: " . $this->formatLearningGoals($studentProfile->learning_goals) . "";
        } else {
            $prompt .= "
            - Level: {$course->level} (course level)";
        }

        $prompt .= "

        CONTENT TYPE: {$contentType}
        ";

        if ($contentType === 'text') {
            $studentLevel = $studentProfile->current_level ?? $course->level;
            $targetLevel = $studentProfile->target_level ?? $course->level;

            $prompt .= "
            Please create comprehensive written content that:
            - Starts with an engaging introduction connecting to the module context
            - Explains concepts clearly with examples appropriate for the student's level
            - Uses appropriate terminology for {$studentLevel} level
            - Includes practical examples and real-world applications
            - Connects back to the module learning objectives
            - Summarizes key takeaways
            - Suggests further reading or exercises based on available resources
            - Provides clear transitions between concepts

            Format the content using Markdown for better readability with headings, bullet points, and examples.
            ";
        } elseif ($contentType === 'interactive') {
            $prompt .= "
            Create an interactive learning activity that:
            - Engages the student actively with the topic concepts
            - Reinforces the key concepts from both topic and module
            - Provides immediate feedback and explanations
            - Adapts to different learning styles
            - Connects to the broader module context
            - Includes clear instructions and learning objectives
            ";
        } elseif ($contentType === 'video_script') {
            $prompt .= "
            Create a video script that:
            - Starts with a hook related to the module context
            - Clearly explains the topic concepts in an engaging way
            - Uses analogies and examples appropriate for the student's level
            - Includes visual cues for what should be shown on screen
            - Summarizes key points at the end
            - Suggests practical applications
            - Keeps the content dynamic and engaging (5-10 minute duration)
            ";
        } elseif ($contentType === 'exercise') {
            $prompt .= "
            Create a practical exercise that:
            - Allows students to apply the topic concepts
            - Includes clear step-by-step instructions
            - Provides example solutions or expected outcomes
            - Connects to real-world applications
            - Includes varying difficulty levels to accommodate different student abilities
            ";
        }

        $studentLevel = $studentProfile->current_level ?? $course->level;
        $targetLevel = $studentProfile->target_level ?? $course->level;
        $learningGoals = $studentProfile ? $this->formatLearningGoals($studentProfile->learning_goals) : 'General learning';

        $prompt .= "

        CONTENT CREATION GUIDELINES:
        - Align content with both topic and module learning objectives
        - Use language and examples appropriate for {$studentLevel} level moving toward {$targetLevel}
        - Ensure content builds on previous knowledge from the module
        - Include practical applications that relate to student goals: {$learningGoals}
        - Make content engaging and accessible
        - Break down complex concepts into digestible parts
        ";

        return $prompt;
    }

    /**
     * Format array for prompt (safer than json_encode)
     */
    protected function formatArrayForPrompt(array $items): string
    {
        if (empty($items)) {
            return "None specified";
        }

        return implode(', ', $items);
    }

    /**
     * Format learning goals for prompt
     */
    protected function formatLearningGoals($learningGoals): string
    {
        if (is_array($learningGoals)) {
            return implode(', ', $learningGoals);
        }

        return $learningGoals ?? 'General knowledge acquisition';
    }

    protected function cleanJsonResponse(string $response): string
    {
        // Remove markdown code blocks if present
        $response = preg_replace('/```json\s*/', '', $response);
        $response = preg_replace('/```\s*$/', '', $response);

        // Remove any text before the first { and after the last }
        $startPos = strpos($response, '{');
        $endPos = strrpos($response, '}');

        if ($startPos !== false && $endPos !== false && $endPos > $startPos) {
            $response = substr($response, $startPos, $endPos - $startPos + 1);
        }

        // Fix common JSON issues
        $response = trim($response);

        // Remove any trailing commas that might break JSON
        $response = preg_replace('/,\s*}/', '}', $response);
        $response = preg_replace('/,\s*]/', ']', $response);

        // Fix unescaped quotes
        $response = preg_replace('/(\w)"(\w)/', '$1\"$2', $response);

        return $response;
    }

    public function generateQuizForOutline(CourseOutline $outline, int $questionCount = 5): Quiz
    {
        $prompt = $this->buildQuizPrompt($outline, $questionCount);

        $messages = [
            [
                'role' => 'system',
                'content' => "You are an expert educational assessment designer. Create fair, comprehensive quizzes that accurately test understanding of both topic-specific and module-level concepts."
            ],
            [
                'role' => 'user',
                'content' => $prompt
            ]
        ];

        try {
            $quizContent = $this->aiService->chat($messages, [
                'temperature' => 0.5,
                'max_tokens' => 3000,
            ], 'quiz_generation');

            // Log the raw AI response
            Log::debug('AI Response Raw', [
                'response_length' => strlen($quizContent),
                'response_preview' => substr($quizContent, 0, 500) . '...',
            ]);

            // Clean the response before JSON decoding
            $cleanedContent = $this->cleanJsonResponse($quizContent);
            $quizData = json_decode($cleanedContent, true);

            // Log JSON decoding result
            Log::debug('JSON Decoding Result', [
                'json_last_error' => json_last_error(),
                'json_last_error_msg' => json_last_error_msg(),
                'decoded_data' => $quizData,
                'is_array' => is_array($quizData),
            ]);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::warning('AI returned invalid JSON for quiz, attempting fallback parsing', [
                    'json_error' => json_last_error_msg(),
                    'raw_response_preview' => substr($quizContent, 0, 200)
                ]);

                // Use fallback parsing
                $quizData = $this->parseQuestionsFromText($quizContent);
            }

            // Validate the structure
            if (empty($quizData) || !isset($quizData['questions']) || !is_array($quizData['questions'])) {
                Log::error('Quiz structure is invalid after parsing', [
                    'has_questions_key' => isset($quizData['questions']),
                    'questions_count' => isset($quizData['questions']) ? count($quizData['questions']) : 0,
                    'quiz_data_keys' => array_keys($quizData ?? [])
                ]);
                throw new \Exception("Generated quiz has invalid structure - no questions found");
            }

            // Ensure we have the expected number of questions
            if (count($quizData['questions']) < $questionCount) {
                Log::warning('AI generated fewer questions than requested', [
                    'requested' => $questionCount,
                    'generated' => count($quizData['questions'])
                ]);
            }

            // Create the quiz in database
            return Quiz::create([
                'course_outline_id' => $outline->id,
                'title' => $quizData['quiz_title'] ?? "Quiz: {$outline->title}",
                'description' => $quizData['description'] ?? "Assessment for {$outline->title} covering key concepts",
                'time_limit_minutes' => $quizData['time_limit_minutes'] ?? ceil($questionCount * 1.5),
                'max_attempts' => 3, // Default value
                'passing_score' => 70,
                'total_points' => $quizData['total_points'] ?? $questionCount,
                'questions' => $quizData['questions'],
                'ai_model_used' => $this->aiService->getProviderCode(),
                'question_count' => count($quizData['questions']),
                'is_active' => true,
            ]);

        } catch (\Exception $e) {
            Log::error('Quiz generation failed', [
                'outline_id' => $outline->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception("Quiz generation failed: " . $e->getMessage());
        }
    }

    protected function buildQuizPrompt(CourseOutline $outline, int $questionCount): string
    {
        // Your existing loading logic...
        $outline->load(['module']);
        $module = $outline->module;

        if (!$module) {
            throw new \Exception("Topic is not associated with a module");
        }

        return "
    IMPORTANT: You MUST return ONLY valid JSON. Do not include any explanatory text before or after the JSON.

    Create a quiz with exactly {$questionCount} questions for the following topic:

    COURSE CONTEXT:
    - Module: {$module->title}
    - Module Learning Objectives: " . $this->formatArrayForPrompt($module->learning_objectives ?? []) . "

    TOPIC DETAILS:
    - Topic: {$outline->title}
    - Topic Description: " . ($outline->description ?? 'No description') . "
    - Key Concepts: " . $this->formatArrayForPrompt($outline->key_concepts ?? []) . "
    - Learning Objectives: " . $this->formatArrayForPrompt($outline->learning_objectives ?? []) . "

    CRITICAL: Return ONLY the following JSON structure, nothing else:

    {
        \"quiz_title\": \"Quiz: {$outline->title}\",
        \"description\": \"Assessment for {$outline->title} covering key concepts and learning objectives\",
        \"questions\": [
            {
                \"question\": \"Question text here\",
                \"type\": \"multiple_choice\",
                \"options\": [\"Option A\", \"Option B\", \"Option C\", \"Option D\"],
                \"correct_answer\": \"Option A\",
                \"explanation\": \"Explanation of why this is correct\",
                \"points\": 1,
                \"difficulty\": \"easy\",
                \"concepts_tested\": [\"concept1\"]
            }
        ],
        \"total_points\": {$questionCount},
        \"passing_score\": " . ceil(70) . ",
        \"time_limit_minutes\": " . ceil($questionCount * 1.5) . "
    }

    RULES:
    - Return ONLY valid JSON, no other text
    - Ensure all strings are properly quoted
    - Include exactly {$questionCount} questions in the array
    - Make sure all brackets and braces are properly closed
    ";
    }

    protected function parseQuestionsFromText(string $text): array
    {
        Log::info('Attempting fallback parsing for quiz content');

        $lines = explode("\n", $text);
        $questions = [];
        $currentQuestion = null;
        $questionNumber = 1;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Detect question
            if (preg_match('/^(\d+\.|\?|Q:|Question:)\s*(.+[?])$/i', $line, $matches)) {
                if ($currentQuestion && !empty($currentQuestion['question'])) {
                    $questions[] = $currentQuestion;
                    $questionNumber++;
                }

                $questionText = end($matches);
                $currentQuestion = [
                    'question' => $questionText,
                    'type' => 'multiple_choice',
                    'options' => [],
                    'correct_answer' => '',
                    'explanation' => 'Review the key concepts to understand this topic better.',
                    'points' => 1,
                    'difficulty' => 'medium',
                    'concepts_tested' => []
                ];
            }
            // Detect options (A), B), etc.
            elseif (preg_match('/^([A-D])[\.\)]\s*(.+)$/i', $line, $matches) && $currentQuestion) {
                $optionText = $matches[2];
                $currentQuestion['options'][] = $optionText;

                // Check if this might be the correct answer (often indicated with * or ✓)
                if (preg_match('/(\*|✓|correct)/i', $optionText)) {
                    $currentQuestion['correct_answer'] = $optionText;
                    // Clean the option text
                    $currentQuestion['options'][count($currentQuestion['options'])-1] =
                        preg_replace('/(\*|✓|\s*\(correct\))/i', '', $optionText);
                }
            }
            // Detect answer line
            elseif (preg_match('/^(Answer|Correct):\s*(.+)$/i', $line, $matches) && $currentQuestion) {
                $currentQuestion['correct_answer'] = trim($matches[2]);
            }
            // Detect explanation
            elseif (preg_match('/^(Explanation|Why):\s*(.+)$/i', $line, $matches) && $currentQuestion) {
                $currentQuestion['explanation'] = trim($matches[2]);
            }
        }

        // Add the last question
        if ($currentQuestion && !empty($currentQuestion['question']) && !empty($currentQuestion['options'])) {
            // If no correct answer was specified, use the first option as default
            if (empty($currentQuestion['correct_answer']) && !empty($currentQuestion['options'])) {
                $currentQuestion['correct_answer'] = $currentQuestion['options'][0];
            }
            $questions[] = $currentQuestion;
        }

        Log::info('Fallback parsing completed', [
            'questions_found' => count($questions),
            'sample_question' => $questions[0]['question'] ?? 'None'
        ]);

        return [
            'quiz_title' => 'Topic Assessment',
            'description' => 'Comprehensive quiz covering key concepts',
            'questions' => $questions,
            'total_points' => count($questions),
            'passing_score' => 70,
            'time_limit_minutes' => ceil(count($questions) * 1.5)
        ];
    }

    /**
     * Get the next order number for content in this outline
     */
    protected function getNextContentOrder(CourseOutline $outline): int
    {
        $lastContent = $outline->contents()->orderBy('order', 'desc')->first();
        return $lastContent ? $lastContent->order + 1 : 1;
    }

    /**
     * Generate multiple content types for an outline
     */
    public function generateMultipleContentTypes(CourseOutline $outline, array $contentTypes = ['text', 'exercise']): array
    {
        $generatedContent = [];

        foreach ($contentTypes as $contentType) {
            try {
                $content = $this->generateContentForOutline($outline, $contentType);
                $generatedContent[] = $content;
            } catch (\Exception $e) {
                Log::error("Failed to generate {$contentType} content", [
                    'outline_id' => $outline->id,
                    'error' => $e->getMessage()
                ]);
                // Continue with other content types even if one fails
            }
        }

        return $generatedContent;
    }
}
