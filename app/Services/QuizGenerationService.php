<?php
// app/Services/QuizGenerationService.php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseOutline;
use App\Models\Quiz;
use App\Services\Ai\BaseAiService;
use Illuminate\Support\Facades\Log;

class QuizGenerationService
{
    protected $aiService;

    public function __construct(BaseAiService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Generate general course quiz using two-step approach
     */
    public function generateGeneralCourseQuiz(Course $course, array $config = [], ?CourseOutline $topic = null): Quiz
    {
        $questionCount = $config['question_count'] ?? 15;
        $difficulty = $config['difficulty'] ?? 'mixed';
        $timeLimitMinutes = $config['time_limit'] ?? ceil($questionCount * 1.5);

        // Step 1: Get all topics for the course
        $topics = CourseOutline::whereHas('module', function($query) use ($course) {
            $query->where('course_id', $course->id);
        })->with('module')->get();

        if ($topics->isEmpty()) {
            throw new \Exception("No topics found for this course");
        }

        Log::info('Starting two-step quiz generation', [
            'course_id' => $course->id,
            'topic_count' => $topics->count(),
            'total_questions' => $questionCount
        ]);

        // Step 2: Generate quiz plan (distribution)
        $quizPlan = $this->generateQuizPlan($course, $topics, $questionCount, $difficulty, $timeLimitMinutes);

        // Step 3: Generate questions for each topic based on plan
        $allQuestions = $this->generateQuestionsByPlan($course, $topics, $quizPlan);

        // Step 4: Create the quiz
        return $this->createQuizFromPlan($course, $quizPlan, $allQuestions, $config, $topic);
    }

    /**
     * Step 1: Generate quiz distribution plan
     */
    protected function generateQuizPlan(Course $course, $topics, int $questionCount, string $difficulty, int $timeLimitMinutes): array
    {
        $formattedTopics = $this->formatTopicsForPlanPrompt($topics);

        $prompt = "
        IMPORTANT: Return ONLY valid JSON. No explanations, no markdown.

        Create a quiz generation plan for the following course.

        COURSE:
        - Title: {$course->title}
        - Level: {$course->level}
        - Subject: {$course->subject}
        - Difficulty Target: {$difficulty}
        - Total Questions Required: {$questionCount}

        TOPICS IN COURSE:
        {$formattedTopics}

        TASK:
        Generate a quiz plan that distributes {$questionCount} questions fairly across all topics.

        RULES:
        1. Each topic must receive at least 1 question.
        2. Distribute questions proportionally if there are many topics.
        3. Difficulty levels must be balanced across the whole quiz.
        4. Supported question types: multiple_choice, true_false, fill_in_blank.
        5. Do NOT generate actual questions.
        6. Return ONLY the JSON structure below.

        REQUIRED JSON STRUCTURE:
        {
          \"quiz_title\": \"Course Review Quiz: {$course->title}\",
          \"question_distribution\": [
            {
              \"topic\": \"Topic name\",
              \"question_count\": 3,
              \"difficulty_breakdown\": {
                \"easy\": 1,
                \"medium\": 1,
                \"hard\": 1
              },
              \"question_types\": [\"multiple_choice\", \"true_false\"]
            }
          ],
          \"total_questions\": {$questionCount},
          \"time_limit_minutes\": {$timeLimitMinutes}
        }

        If anything outside valid JSON is returned, the response is invalid.
        ";

        $messages = [
            [
                'role' => 'system',
                'content' => "You are a quiz planning expert. Create fair question distributions across topics."
            ],
            [
                'role' => 'user',
                'content' => $prompt
            ]
        ];

        try {
            $response = $this->aiService->chat($messages, [
                'temperature' => 0.3,
                'max_tokens' => 1500,
                'timeout' => 30,
            ], 'quiz_plan_generation');

            $cleanedContent = $this->cleanJsonResponse($response);
            $quizPlan = json_decode($cleanedContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("Invalid JSON in quiz plan response");
            }

            // Validate plan structure
            if (empty($quizPlan['question_distribution']) || !is_array($quizPlan['question_distribution'])) {
                throw new \Exception("Quiz plan missing question distribution");
            }

            Log::info('Quiz plan generated successfully', [
                'course_id' => $course->id,
                'distribution_count' => count($quizPlan['question_distribution']),
                'total_questions_planned' => $quizPlan['total_questions'] ?? 0
            ]);

            return $quizPlan;

        } catch (\Exception $e) {
            Log::error('Quiz plan generation failed', [
                'course_id' => $course->id,
                'error' => $e->getMessage()
            ]);

            // Fallback to simple distribution plan
            return $this->createFallbackQuizPlan($course, $topics, $questionCount, $timeLimitMinutes);
        }
    }

    /**
     * Step 2: Generate questions for each topic based on the plan
     */
    protected function generateQuestionsByPlan(Course $course, $topics, array $quizPlan): array
    {
        $allQuestions = [];
        $topicMap = $this->createTopicMap($topics);

        foreach ($quizPlan['question_distribution'] as $distribution) {
            $topicTitle = $distribution['topic'];

            // Find the matching topic
            $topic = $this->findMatchingTopic($topicTitle, $topicMap);

            if (!$topic) {
                Log::warning("Topic not found in course: {$topicTitle}", [
                    'course_id' => $course->id,
                    'available_topics' => array_keys($topicMap)
                ]);
                continue;
            }

            try {
                $topicQuestions = $this->generateQuestionsForTopic(
                    $course,
                    $topic,
                    $distribution['question_count'],
                    $distribution['difficulty_breakdown'],
                    $distribution['question_types'] ?? ['multiple_choice']
                );

                $allQuestions = array_merge($allQuestions, $topicQuestions);

                Log::debug("Generated questions for topic", [
                    'topic' => $topicTitle,
                    'questions_generated' => count($topicQuestions),
                    'total_questions' => count($allQuestions)
                ]);

                // Small delay between topic generations
                usleep(500000); // 0.5 seconds

            } catch (\Exception $e) {
                Log::error("Failed to generate questions for topic: {$topicTitle}", [
                    'course_id' => $course->id,
                    'error' => $e->getMessage()
                ]);

                // Generate fallback questions for this topic
                $fallbackQuestions = $this->generateFallbackQuestionsForTopic(
                    $course,
                    $topic,
                    $distribution['question_count']
                );

                $allQuestions = array_merge($allQuestions, $fallbackQuestions);
            }
        }

        return $allQuestions;
    }

    /**
     * Generate questions for a single topic
     */
    protected function generateQuestionsForTopic(Course $course, CourseOutline $topic, int $questionCount, array $difficultyBreakdown, array $questionTypes): array
    {
        $prompt = "
        IMPORTANT: Return ONLY valid JSON. No explanations, no markdown.

        Generate quiz questions for the topic below.

        COURSE:
        - Title: {$course->title}
        - Level: {$course->level}
        - Subject: {$course->subject}

        TOPIC:
        - Topic Title: {$topic->title}
        - Module: " . ($topic->module->title ?? 'N/A') . "
        - Key Concepts: " . $this->formatArrayForPrompt($topic->key_concepts ?? []) . "
        - Learning Objectives: " . $this->formatArrayForPrompt($topic->learning_objectives ?? []) . "

        QUESTION REQUIREMENTS:
        - Number of questions: {$questionCount}
        - Difficulty distribution:
          " . $this->formatDifficultyBreakdown($difficultyBreakdown) . "
        - Allowed question types:
          " . implode(', ', $questionTypes) . "

        RULES:
        1. Questions must match the provided difficulty levels.
        2. Questions must test understanding, not memorization.
        3. Each question must relate clearly to this topic.
        4. Explanations must be concise and educational.
        5. Do NOT reference other topics.
        6. Return ONLY the JSON structure below.

        REQUIRED JSON STRUCTURE:
        {
          \"topic\": \"{$topic->title}\",
          \"questions\": [
            {
              \"question\": \"Question text\",
              \"type\": \"multiple_choice\",
              \"options\": [\"Option A\", \"Option B\", \"Option C\", \"Option D\"],
              \"correct_answer\": \"Option A\",
              \"explanation\": \"Why this is correct\",
              \"difficulty\": \"easy|medium|hard\",
              \"points\": 1
            }
          ]
        }

        If anything outside valid JSON is returned, the response is invalid.
        ";

        $messages = [
            [
                'role' => 'system',
                'content' => "You are an expert quiz creator. Generate educational questions that test understanding."
            ],
            [
                'role' => 'user',
                'content' => $prompt
            ]
        ];

        $response = $this->aiService->chat($messages, [
            'temperature' => 0.4,
            'max_tokens' => 2000,
            'timeout' => 20, // Shorter timeout per topic
        ], 'topic_questions_generation');

        $cleanedContent = $this->cleanJsonResponse($response);
        $result = json_decode($cleanedContent, true);

        if (json_last_error() !== JSON_ERROR_NONE || empty($result['questions'])) {
            throw new \Exception("Failed to parse questions for topic");
        }

        // Validate each question has required fields
        foreach ($result['questions'] as &$question) {
            $question = $this->validateQuestionStructure($question);
            $question['topic_id'] = $topic->id;
        }

        return $result['questions'];
    }

    /**
     * Create final quiz from plan and questions
     */
    protected function createQuizFromPlan(Course $course, array $quizPlan, array $questions, array $config, CourseOutline $topic): Quiz
    {
        if (empty($questions)) {
            throw new \Exception("No questions generated");
        }

        return Quiz::create([
            'course_outline_id' => $topic->id,
            'title' => "General Quiz: {$topic->title}",
            'description' => "Comprehensive quiz covering all topics in {$course->title}",
            'time_limit_minutes' => $config['time_limit'] ?? 30,
            'max_attempts' => $config['max_attempts'] ?? 3,
            'passing_score' => $config['passing_score'] ?? 70.0,
            'questions' => $questions,
            'ai_model_used' => $this->aiService->getProviderCode(),
            'is_active' => true,
        ]);
    }

    // Helper methods

    protected function formatTopicsForPlanPrompt($topics): string
    {
        $formatted = "";
        foreach ($topics as $index => $topic) {
            $formatted .= ($index + 1) . ". " . $topic->title . "\n";
        }
        return $formatted;
    }

    protected function formatDifficultyBreakdown(array $breakdown): string
    {
        $result = [];
        foreach ($breakdown as $difficulty => $count) {
            if ($count > 0) {
                $result[] = "{$difficulty}: {$count}";
            }
        }
        return implode("\n  ", $result);
    }

    protected function createTopicMap($topics): array
    {
        $map = [];
        foreach ($topics as $topic) {
            $map[strtolower(trim($topic->title))] = $topic;
        }
        return $map;
    }

    protected function findMatchingTopic(string $searchTitle, array $topicMap): ?CourseOutline
    {
        $searchKey = strtolower(trim($searchTitle));

        // Exact match
        if (isset($topicMap[$searchKey])) {
            return $topicMap[$searchKey];
        }

        // Fuzzy match
        foreach ($topicMap as $key => $topic) {
            if (str_contains($key, $searchKey) || str_contains($searchKey, $key)) {
                return $topic;
            }
        }

        return null;
    }

    protected function validateQuestionStructure(array $question): array
    {
        $defaults = [
            'type' => 'multiple_choice',
            'options' => ['Option A', 'Option B', 'Option C', 'Option D'],
            'correct_answer' => 'Option A',
            'explanation' => 'This is the correct answer based on the course material.',
            'difficulty' => 'medium',
            'points' => 1,
        ];

        $question = array_merge($defaults, $question);

        // Ensure options array has exactly 4 items for multiple choice
        if ($question['type'] === 'multiple_choice' && count($question['options']) < 4) {
            $question['options'] = array_pad($question['options'], 4, 'Option ' . chr(65 + count($question['options'])));
        }

        return $question;
    }

    // Fallback methods

    protected function createFallbackQuizPlan(Course $course, $topics, int $questionCount, int $timeLimitMinutes): array
    {
        $distribution = [];
        $questionsPerTopic = max(1, floor($questionCount / count($topics)));
        $remainingQuestions = $questionCount - ($questionsPerTopic * count($topics));

        foreach ($topics as $index => $topic) {
            $topicQuestionCount = $questionsPerTopic;
            if ($index < $remainingQuestions) {
                $topicQuestionCount++;
            }

            $distribution[] = [
                'topic' => $topic->title,
                'question_count' => $topicQuestionCount,
                'difficulty_breakdown' => [
                    'easy' => max(1, floor($topicQuestionCount * 0.3)),
                    'medium' => max(1, floor($topicQuestionCount * 0.5)),
                    'hard' => max(1, floor($topicQuestionCount * 0.2)),
                ],
                'question_types' => ['multiple_choice', 'true_false']
            ];
        }

        return [
            'quiz_title' => "Course Review Quiz: {$course->title}",
            'question_distribution' => $distribution,
            'total_questions' => $questionCount,
            'time_limit_minutes' => $timeLimitMinutes
        ];
    }

    protected function generateFallbackQuestionsForTopic(Course $course, CourseOutline $topic, int $questionCount): array
    {
        $questions = [];
        $keyConcepts = is_array($topic->key_concepts) ? $topic->key_concepts : [$topic->key_concepts ?? 'key concepts'];

        for ($i = 0; $i < $questionCount; $i++) {
            $concept = $keyConcepts[$i % count($keyConcepts)] ?? 'the topic';

            $questions[] = [
                'question' => "What is an important aspect of '{$concept}' in {$topic->title}?",
                'type' => 'multiple_choice',
                'options' => [
                    "Understanding its fundamental principles",
                    "Applying it in practical scenarios",
                    "Analyzing its components",
                    "Evaluating its effectiveness"
                ],
                'correct_answer' => "Understanding its fundamental principles",
                'explanation' => "Understanding fundamental principles is key to mastering any concept.",
                'difficulty' => ['easy', 'medium', 'hard'][$i % 3],
                'points' => 1,
                'topic_id' => $topic->id
            ];
        }

        return $questions;
    }

    protected function formatArrayForPrompt(array $items): string
    {
        if (empty($items)) {
            return "None specified";
        }

        if (is_array($items)) {
            return implode(', ', array_filter($items));
        }

        return (string) $items;
    }

    protected function cleanJsonResponse(string $response): string
    {
        // Remove markdown code blocks
        $response = preg_replace('/```json\s*/', '', $response);
        $response = preg_replace('/```\s*$/', '', $response);

        // Extract JSON between first { and last }
        $startPos = strpos($response, '{');
        $endPos = strrpos($response, '}');

        if ($startPos !== false && $endPos !== false && $endPos > $startPos) {
            $response = substr($response, $startPos, $endPos - $startPos + 1);
        }

        // Fix common JSON issues
        $response = trim($response);
        $response = preg_replace('/,\s*}/', '}', $response);
        $response = preg_replace('/,\s*]/', ']', $response);

        return $response;
    }

    // Keep the existing single-topic quiz generation for backward compatibility
    public function generateQuiz(CourseOutline $outline, array $config = []): Quiz
    {
        // ... existing code ...
    }
}
