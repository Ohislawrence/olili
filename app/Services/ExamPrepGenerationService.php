<?php

namespace App\Services;

use App\Models\ExamPrep;
use App\Models\ExamPrepQuestion;
use App\Models\ExamBoard;
use App\Models\Subject;
use App\Services\Ai\BaseAiService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ExamPrepGenerationService
{
    protected $aiService;
    protected $isDeepSeek;

    public function __construct(BaseAiService $aiService)
    {
        $this->aiService = $aiService;
        $this->isDeepSeek = $aiService->getProviderCode() === 'deepseek';
    }

    /**
     * Generate exam prep questions and description using AI
     */
    public function generateQuestions(ExamPrep $examPrep): array
    {
        Log::info('Starting exam prep question generation', [
            'exam_prep_id' => $examPrep->id,
            'exam_prep_name' => $examPrep->name,
            'total_questions' => $examPrep->total_questions
        ]);

        try {
            DB::beginTransaction();

            // Update status
            $examPrep->update([
                'content_generation_status' => 'processing',
                'content_generation_started_at' => now(),
                'ai_model_used' => $this->aiService->getProviderCode(),
            ]);

            // Clear existing questions if any
            $examPrep->questions()->delete();

            // Generate questions and description together
            $generatedData = $this->generateQuestionSetWithDescription($examPrep);

            $generatedQuestions = $generatedData['questions'];
            $generatedDescription = $generatedData['description'];

            // Insert questions
            foreach ($generatedQuestions as $index => $question) {
                ExamPrepQuestion::create([
                    'exam_prep_id' => $examPrep->id,
                    'question_text' => $question['question_text'],
                    'options' => $question['options'] ?? [],
                    'correct_answer' => $question['correct_answer'],
                    'explanation' => $question['explanation'] ?? null,
                    'question_type' => $question['question_type'] ?? 'multiple_choice',
                    'points' => $question['points'] ?? 1,
                    'difficulty' => $question['difficulty'],
                    'order' => $index + 1,
                    'metadata' => [
                        'topic' => $question['topic'] ?? $examPrep->subject?->name ?? 'General',
                        'concepts_tested' => $question['concepts_tested'] ?? [],
                        'estimated_time_seconds' => $question['estimated_time_seconds'] ?? 60,
                    ],
                ]);
            }

            DB::commit();

            // Update exam prep with generated description and generation summary
            $examPrep->update([
                'name' => $generatedData['name'],
                'description' => $generatedDescription,
                'content_generation_status' => 'completed',
                'content_generation_completed_at' => now(),
                'generation_summary' => [
                    'questions_generated' => count($generatedQuestions),
                    'difficulty_distribution' => $this->getDifficultyDistribution($examPrep),
                    'type_distribution' => $this->getQuestionTypeDistribution($examPrep),
                    'description_generated' => true,
                    'name_generated' => isset($generatedData['name']),
                    'generated_at' => now()->toDateTimeString(),
                ],
            ]);



            Log::info('Exam prep questions and description generated successfully', [
                'exam_prep_id' => $examPrep->id,
                'questions_generated' => count($generatedQuestions),
                'description_generated' => !empty($generatedDescription)
            ]);

            return [
                'success' => true,
                'question_count' => count($generatedQuestions),
                'questions' => $generatedQuestions,
                'description' => $generatedDescription,
                'name' => $generatedData['name'] ?? $examPrep->name
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to generate exam prep questions', [
                'exam_prep_id' => $examPrep->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $examPrep->update([
                'content_generation_status' => 'failed',
                'generation_summary' => [
                    'error' => $e->getMessage(),
                    'failed_at' => now()->toDateTimeString(),
                ]
            ]);

            throw new \Exception("Failed to generate exam prep questions: " . $e->getMessage());
        }
    }

    /**
     * Generate a complete set of questions with description in one API call
     */
    protected function generateQuestionSetWithDescription(ExamPrep $examPrep): array
{
    $distribution = $this->determineQuestionDistribution($examPrep);
    $prompt = $this->buildCompletePrompt($examPrep, $distribution);

    $messages = $this->formatMessagesForProvider(
        "You are an expert educational assessment designer and content creator. Create high-quality exam preparation materials including a compelling title, engaging description, and well-crafted questions.",
        $prompt
    );

    try {
        $response = $this->aiService->chat($messages, [
            'temperature' => 0.7,
            'max_tokens' => 4000,
        ], 'exam_prep_complete_generation');

        // Log the raw response for debugging
        Log::info('Raw AI response received', [
            'exam_prep_id' => $examPrep->id,
            'response_length' => strlen($response),
            'response_start' => substr($response, 0, 500),
            'response_end' => substr($response, -500)
        ]);

        // Check if response is empty
        if (empty(trim($response))) {
            Log::error('Empty response from AI service');
            return $this->fallbackGeneration($examPrep, $distribution);
        }

        // Clean and parse the response
        $cleanedContent = $this->cleanJsonResponse($response);

        // Verify we have valid JSON before decoding
        if (!$this->isValidJson($cleanedContent)) {
            Log::warning('Invalid JSON after cleaning', [
                'cleaned_preview' => substr($cleanedContent, 0, 500)
            ]);
            return $this->fallbackGeneration($examPrep, $distribution);
        }

        $generatedData = json_decode($cleanedContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::warning('JSON decode failed', [
                'error' => json_last_error_msg(),
                'cleaned_preview' => substr($cleanedContent, 0, 500)
            ]);
            return $this->fallbackGeneration($examPrep, $distribution);
        }

        // Validate and format the response
        return $this->validateCompleteResponse($generatedData, $examPrep, $distribution);

    } catch (\Exception $e) {
        Log::error('Failed to generate complete question set with description', [
            'exam_prep_id' => $examPrep->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return $this->fallbackGeneration($examPrep, $distribution);
    }
}

protected function isValidJson(string $string): bool
{
    if (empty($string)) {
        return false;
    }

    $firstChar = substr(trim($string), 0, 1);
    if (!in_array($firstChar, ['{', '['])) {
        return false;
    }

    json_decode($string);
    return json_last_error() === JSON_ERROR_NONE;
}


    /**
     * Build complete prompt that includes description and name generation
     */
    protected function buildCompletePrompt(ExamPrep $examPrep, array $distribution): string
    {
        $examBoard = ExamBoard::find($examPrep->exam_board_id);
        $subject = Subject::find($examPrep->subject_id);

        $totalQuestions = array_sum($distribution);

        $prompt = "Create a complete exam preparation package with the following specifications:

EXAM DETAILS:
- Current Name: {$examPrep->name}
- Subject: " . ($subject->name ?? 'General') . "
- Exam Board: " . ($examBoard->name ?? 'General') . "
- Total Questions: {$totalQuestions}
- Time Limit: {$examPrep->time_limit_minutes} minutes
- Passing Score: {$examPrep->passing_score}%
- Max Attempts: {$examPrep->max_attempts}

DIFFICULTY DISTRIBUTION:
- Easy Questions: {$distribution['easy']}
- Medium Questions: {$distribution['medium']}
- Hard Questions: {$distribution['hard']}

";
        if ($examPrep->course) {
            $prompt .= "COURSE CONTEXT:
- Course: {$examPrep->course->title}
- Course Level: {$examPrep->course->level}
- Course Description: " . ($examPrep->course->description ?? 'Not specified') . "

";
        }

        // Add question criteria if exists
        if (!empty($examPrep->question_criteria) && is_array($examPrep->question_criteria)) {
            $prompt .= "ADDITIONAL CRITERIA:\n";
            foreach ($examPrep->question_criteria as $key => $value) {
                if (!empty($value)) {
                    if (is_array($value)) {
                        $value = implode(', ', $value);
                    }
                    $prompt .= "- {$key}: {$value}\n";
                }
            }
            $prompt .= "\n";
        }

        $prompt .= "IMPORTANT: Return ONLY a valid JSON object with this exact structure:

{
  \"name\": \"An improved, SEO-friendly title for this exam prep (keep it concise)\",
  \"description\": \"A compelling 2-3 sentence description that motivates students and highlights key benefits. Include the exam board, subject, and number of questions.\",
  \"questions\": [
    {
      \"question_text\": \"The full question text with proper punctuation?\",
      \"options\": [\"Option A\", \"Option B\", \"Option C\", \"Option D\"],
      \"correct_answer\": \"The exact text of the correct option\",
      \"explanation\": \"Detailed explanation of why this answer is correct and why others are incorrect\",
      \"question_type\": \"multiple_choice\",
      \"points\": 1,
      \"difficulty\": \"easy|medium|hard\",
      \"topic\": \"Specific topic or concept covered\",
      \"concepts_tested\": [\"Concept1\", \"Concept2\"],
      \"estimated_time_seconds\": 60
    }
  ]
}

REQUIREMENTS:

1. NAME:
   - Improve the current name to be more SEO-friendly and descriptive
   - Include the exam board and subject if available
   - Keep it concise but descriptive (5-10 words)
   - Example: \"WAEC English Language: Complete Practice Test with Answers\"

2. DESCRIPTION:
   - 2-3 sentences (150-250 characters)
   - Highlight key benefits and what students will gain
   - Mention the exam board, subject, and number of questions
   - Be engaging, motivational, and professional
   - Include SEO keywords

3. QUESTIONS - BY DIFFICULTY:

EASY QUESTIONS ({$distribution['easy']} questions):
- Test basic recall and fundamental concepts
- Straightforward with clear, direct answers
- Distractors obviously incorrect to students with basic knowledge
- Focus on core definitions, simple facts

MEDIUM QUESTIONS ({$distribution['medium']} questions):
- Test application of concepts and understanding
- Require 2-3 steps of reasoning
- Plausible distractors with subtle errors
- Include scenarios requiring critical thinking

HARD QUESTIONS ({$distribution['hard']} questions):
- Test synthesis, evaluation, and complex problem-solving
- Multiple concepts or multi-step reasoning
- Highly plausible distractors testing common misconceptions
- Complex scenarios and edge cases

GENERAL GUIDELINES FOR ALL QUESTIONS:
- All questions must be accurate and factually correct
- Exactly one correct answer per question
- All options approximately the same length
- Avoid 'all of the above' or 'none of the above'
- Clear, educational explanations
- Questions appropriate for " . ($subject->name ?? 'the subject') . "
- No ambiguous wording
- Self-contained and understandable

Return ONLY the JSON object, no additional text.";

        return $prompt;
    }

    /**
 * Validate and format the complete response
 */
protected function validateCompleteResponse(array $data, ExamPrep $examPrep, array $distribution): array
{
    // Log the raw response for debugging
    Log::info('Validating AI response', [
        'exam_prep_id' => $examPrep->id,
        'response_keys' => array_keys($data),
        'has_name' => isset($data['name']),
        'has_description' => isset($data['description']),
        'has_questions' => isset($data['questions']),
        'questions_count' => isset($data['questions']) ? count($data['questions']) : 0
    ]);

    // More lenient name validation
    $name = isset($data['name']) && is_string($data['name']) && !empty(trim($data['name']))
        ? trim($data['name'])
        : $examPrep->name;

    // More lenient description validation
    $description = isset($data['description']) && is_string($data['description']) && !empty(trim($data['description']))
        ? trim($data['description'])
        : $this->generateFallbackDescription($examPrep);

    // Clean up description
    $description = preg_replace('/^["\']+|["\']+$/', '', $description);
    $description = preg_replace('/\s+/', ' ', $description);

    // Extract and validate questions with better error handling
    $questions = [];
    if (isset($data['questions']) && is_array($data['questions'])) {
        foreach ($data['questions'] as $index => $question) {
            $validatedQuestion = $this->validateSingleQuestion($question);
            if ($validatedQuestion) {
                $questions[] = $validatedQuestion;
            } else {
                Log::warning('Question validation failed', [
                    'exam_prep_id' => $examPrep->id,
                    'question_index' => $index,
                    'question' => $question
                ]);
            }
        }
    }

    // If we got at least some questions, use them
    if (count($questions) > 0) {
        Log::info('Successfully validated questions', [
            'exam_prep_id' => $examPrep->id,
            'validated_count' => count($questions)
        ]);

        // Balance questions to match distribution
        $questions = $this->balanceQuestionsByDifficulty($questions, $distribution);

        return [
            'name' => $name,
            'description' => $description,
            'questions' => $questions
        ];
    }

    // No valid questions, log and fallback
    Log::warning('No valid questions in AI response, using fallback', [
        'exam_prep_id' => $examPrep->id,
        'response_preview' => json_encode($data)
    ]);

    return $this->fallbackGeneration($examPrep, $distribution);
}

    /**
     * Validate a single question
     */
    protected function validateSingleQuestion(array $question): ?array
    {
        // Required fields
        if (empty($question['question_text']) ||
            empty($question['options']) ||
            empty($question['correct_answer']) ||
            empty($question['difficulty'])) {
            return null;
        }

        // Ensure options is an array
        $options = is_array($question['options']) ? $question['options'] : [];
        $options = array_map(function($option) {
            return is_string($option) ? $option : (string) $option;
        }, $options);
        $options = array_slice($options, 0, 4);

        // Need at least 2 options
        if (count($options) < 2) {
            return null;
        }

        // Ensure correct_answer is in options (or use first option)
        $correctAnswer = (string) $question['correct_answer'];
        if (!in_array($correctAnswer, $options)) {
            $correctAnswer = $options[0];
        }

        // Validate difficulty
        $difficulty = in_array($question['difficulty'], ['easy', 'medium', 'hard'])
            ? $question['difficulty']
            : 'medium';

        // Concepts tested
        $conceptsTested = isset($question['concepts_tested']) && is_array($question['concepts_tested'])
            ? array_map('strval', $question['concepts_tested'])
            : [];

        return [
            'question_text' => (string) $question['question_text'],
            'options' => $options,
            'correct_answer' => $correctAnswer,
            'explanation' => isset($question['explanation'])
                ? (string) $question['explanation']
                : "The correct answer is {$correctAnswer}.",
            'question_type' => 'multiple_choice',
            'points' => intval($question['points'] ?? 1),
            'difficulty' => $difficulty,
            'topic' => isset($question['topic']) ? (string) $question['topic'] : 'General',
            'concepts_tested' => $conceptsTested,
            'estimated_time_seconds' => intval($question['estimated_time_seconds'] ?? 60),
        ];
    }

    /**
     * Balance questions to match the required difficulty distribution
     */
    protected function balanceQuestionsByDifficulty(array $questions, array $distribution): array
    {
        $balanced = [];

        // Group questions by difficulty
        $byDifficulty = [
            'easy' => [],
            'medium' => [],
            'hard' => []
        ];

        foreach ($questions as $question) {
            $difficulty = $question['difficulty'];
            if (isset($byDifficulty[$difficulty])) {
                $byDifficulty[$difficulty][] = $question;
            }
        }

        // Take required number from each difficulty
        foreach (['easy', 'medium', 'hard'] as $difficulty) {
            $needed = $distribution[$difficulty] ?? 0;
            $available = $byDifficulty[$difficulty] ?? [];

            // Shuffle to get random selection
            shuffle($available);

            for ($i = 0; $i < $needed; $i++) {
                if (isset($available[$i])) {
                    $balanced[] = $available[$i];
                } else {
                    // Add fallback question if not enough
                    $balanced[] = $this->createFallbackQuestion($difficulty);
                }
            }
        }

        return $balanced;
    }

    /**
     * Fallback generation method when AI fails
     */
    protected function fallbackGeneration(ExamPrep $examPrep, array $distribution): array
    {
        Log::info('Using fallback generation method', [
            'exam_prep_id' => $examPrep->id
        ]);

        $allQuestions = [];

        // Generate questions for each difficulty
        foreach ($distribution as $difficulty => $count) {
            if ($count > 0) {
                $questions = $this->generateFallbackQuestions($examPrep, $difficulty, $count);
                $allQuestions = array_merge($allQuestions, $questions);
            }
        }

        // Shuffle if randomization is enabled
        if ($examPrep->randomize_questions) {
            shuffle($allQuestions);
        }

        return [
            'name' => $examPrep->name,
            'description' => $this->generateFallbackDescription($examPrep),
            'questions' => $allQuestions
        ];
    }

    /**
     * Generate fallback description
     */
    protected function generateFallbackDescription(ExamPrep $examPrep): string
    {
        $subject = Subject::find($examPrep->subject_id);
        $examBoard = ExamBoard::find($examPrep->exam_board_id);

        $subjectName = $subject->name ?? $examPrep->name;
        $examBoardName = $examBoard->name ?? '';

        $description = "Master your {$subjectName}";
        if ($examBoardName) {
            $description .= " {$examBoardName}";
        }
        $description .= " exam with this comprehensive practice test. Features {$examPrep->total_questions} carefully crafted questions, detailed explanations, and realistic exam scenarios to help you identify strengths and areas for improvement.";

        return $description;
    }

    /**
     * Format messages based on the AI provider
     */
    protected function formatMessagesForProvider(string $systemMessage, string $userMessage): array
    {
        if ($this->isDeepSeek) {
            return [
                [
                    'role' => 'user',
                    'content' => $systemMessage . "\n\n" . $userMessage
                ]
            ];
        }

        return [
            [
                'role' => 'system',
                'content' => $systemMessage
            ],
            [
                'role' => 'user',
                'content' => $userMessage
            ]
        ];
    }

    /**
     * Determine how many questions of each difficulty to generate
     */
    protected function determineQuestionDistribution(ExamPrep $examPrep): array
    {
        $distribution = $examPrep->question_distribution ?? [];

        if (!empty($distribution) && array_sum($distribution) > 0) {
            return [
                'easy' => intval($distribution['easy'] ?? 0),
                'medium' => intval($distribution['medium'] ?? 0),
                'hard' => intval($distribution['hard'] ?? 0),
            ];
        }

        $total = $examPrep->total_questions;
        return [
            'easy' => (int) round($total * 0.3),
            'medium' => (int) round($total * 0.5),
            'hard' => (int) round($total * 0.2),
        ];
    }

    /**
     * Clean JSON response from AI
     */
    protected function cleanJsonResponse(string $response): string
    {
        Log::debug('Raw AI response preview', [
            'response_start' => substr($response, 0, 200),
            'response_length' => strlen($response)
        ]);

        // Remove markdown code blocks and any surrounding text
        $response = preg_replace('/^[\s\S]*?```(?:json)?\s*/i', '', $response);
        $response = preg_replace('/```[\s\S]*?$/', '', $response);

        // Remove any non-JSON text before first { or [
        if (preg_match('/[{\[].*[}\]]/s', $response, $matches)) {
            $response = $matches[0];
        }

        // Fix common JSON issues
        $response = trim($response);

        // Remove BOM and invisible characters
        $response = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $response);

        // Fix trailing commas
        $response = preg_replace('/,\s*}/', '}', $response);
        $response = preg_replace('/,\s*]/', ']', $response);

        // Replace single quotes with double quotes for property names and string values
        $response = preg_replace('/(?<!\\)\'(.*?)(?<!\\)\'/', '"$1"', $response);

        // Fix unescaped quotes inside strings
        $response = preg_replace_callback('/:"(.*?)"(?=\s*[,}])/', function($matches) {
            $value = str_replace('"', '\\"', $matches[1]);
            return ':"' . $value . '"';
        }, $response);

        // Remove any whitespace between property names and colons
        $response = preg_replace('/"\s*:\s*/', '":', $response);

        Log::debug('Cleaned JSON preview', [
            'cleaned_start' => substr($response, 0, 200)
        ]);

        return $response;
    }

    /**
     * Generate fallback questions
     */
    protected function generateFallbackQuestions(ExamPrep $examPrep, string $difficulty, int $count): array
    {
        $questions = [];
        for ($i = 0; $i < $count; $i++) {
            $questions[] = $this->createFallbackQuestion($difficulty);
        }
        return $questions;
    }

    /**
     * Create a single fallback question
     */
    protected function createFallbackQuestion(string $difficulty): array
    {
        $difficultyText = ucfirst($difficulty);
        $questionNumber = rand(1, 100);

        return [
            'question_text' => "Sample {$difficultyText} Level Question #{$questionNumber}: This is a placeholder question. Please regenerate the exam prep to get AI-generated questions.",
            'options' => [
                "Correct answer placeholder",
                "Incorrect option 1",
                "Incorrect option 2",
                "Incorrect option 3"
            ],
            'correct_answer' => "Correct answer placeholder",
            'explanation' => "This is a placeholder explanation. When questions are properly generated, you'll receive detailed explanations for each answer.",
            'question_type' => 'multiple_choice',
            'points' => 1,
            'difficulty' => $difficulty,
            'topic' => 'General',
            'concepts_tested' => ['Sample concept'],
            'estimated_time_seconds' => 60,
        ];
    }

    /**
     * Helper method to get difficulty distribution
     */
    protected function getDifficultyDistribution(ExamPrep $examPrep): array
    {
        return [
            'easy' => $examPrep->questions()->where('difficulty', 'easy')->count(),
            'medium' => $examPrep->questions()->where('difficulty', 'medium')->count(),
            'hard' => $examPrep->questions()->where('difficulty', 'hard')->count(),
        ];
    }

    /**
     * Helper method to get question type distribution
     */
    protected function getQuestionTypeDistribution(ExamPrep $examPrep): array
    {
        return $examPrep->questions()
            ->select('question_type', \DB::raw('count(*) as total'))
            ->groupBy('question_type')
            ->pluck('total', 'question_type')
            ->toArray();
    }

    /**
     * Regenerate specific questions
     */
    public function regenerateQuestions(ExamPrep $examPrep, array $questionIds): array
    {
        // Implementation for regenerating specific questions
        return [];
    }
}
