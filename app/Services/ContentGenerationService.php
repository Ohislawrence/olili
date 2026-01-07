<?php
// app/Services/ContentGenerationService.php

namespace App\Services;

use App\Models\CapstoneProject;
use App\Models\Course;
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
                'content' => "You are an expert educator and content creator. Create engaging, educational content that helps students learn effectively based on the course structure and learning objectives. The content should be appropriate for the course level and target audience."
            ],
            [
                'role' => 'user',
                'content' => $prompt
            ]
        ];

        try {
            $content = $this->aiService->chat($messages, [
                'temperature' => 0.8,
                'max_tokens' => 4000,
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
            $outline->load(['module.course', 'module']);
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

        $prompt = "
        Create educational content for the following topic:

        COURSE CONTEXT:
        - Course: {$course->title}
        - Subject: {$course->subject}
        - Level: {$course->level}
        - Course Description: " . ($course->description ?? 'No description') . "

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
        - Estimated Duration: " . ($outline->estimated_duration_minutes ?? 'Not specified') . " minutes

        CONTENT TYPE: {$contentType}
        ";

        if ($contentType === 'text') {
            $prompt .= "
            Please create comprehensive written content that:
            - Starts with an engaging introduction connecting to the module and course context
            - Explains concepts clearly with examples appropriate for {$course->level} level
            - Uses appropriate terminology for {$course->level} level
            - Includes practical examples and real-world applications
            - Connects back to the module and course learning objectives
            - Summarizes key takeaways
            - Suggests further reading or exercises based on available resources
            - Provides clear transitions between concepts
            - Is suitable for self-paced learning

            Format the content using Markdown for better readability with headings, bullet points, and examples.
            ";
        } elseif ($contentType === 'interactive') {
            $prompt .= "
            Create an interactive learning activity that:
            - Engages the student actively with the topic concepts
            - Reinforces the key concepts from both topic and module
            - Provides immediate feedback and explanations
            - Adapts to different learning styles
            - Connects to the broader module and course context
            - Includes clear instructions and learning objectives
            - Is suitable for {$course->level} level students
            ";
        } elseif ($contentType === 'video_script') {
            $prompt .= "
            Create a video script that:
            - Starts with a hook related to the module and course context
            - Clearly explains the topic concepts in an engaging way
            - Uses analogies and examples appropriate for {$course->level} level
            - Includes visual cues for what should be shown on screen
            - Summarizes key points at the end
            - Suggests practical applications
            - Keeps the content dynamic and engaging (5-10 minute duration)
            - Includes timings for different sections
            ";
        } elseif ($contentType === 'exercise') {
            $prompt .= "
            Create a practical exercise that:
            - Allows students to apply the topic concepts
            - Includes clear step-by-step instructions
            - Provides example solutions or expected outcomes
            - Connects to real-world applications
            - Includes varying difficulty levels to accommodate different student abilities
            - Aligns with {$course->level} level expectations
            - Relates to the course subject: {$course->subject}
            ";
        }

        $prompt .= "

        CONTENT CREATION GUIDELINES:
        - Align content with topic, module, and course learning objectives
        - Use language and examples appropriate for {$course->level} level
        - Ensure content builds on previous knowledge from the module and course
        - Include practical applications relevant to the course subject: {$course->subject}
        - Make content engaging, accessible, and suitable for self-study
        - Break down complex concepts into digestible parts
        - Consider the estimated duration for this topic: " . ($outline->estimated_duration_minutes ?? 'flexible') . " minutes
        - Content should be comprehensive but not overwhelming for {$course->level} level students
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

        if (is_array($items)) {
            return implode(', ', $items);
        }

        return (string) $items;
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
        // Load course and module data
        $outline->load(['module.course', 'module']);
        $module = $outline->module;
        $course = $outline->module->course ?? null;

        if (!$module) {
            throw new \Exception("Topic is not associated with a module");
        }

        $courseContext = $course ? "Course Level: {$course->level}" : "Course level not specified";

        return "
    IMPORTANT: You MUST return ONLY valid JSON. Do not include any explanatory text before or after the JSON.

    Create a quiz with exactly {$questionCount} questions for the following topic:

    COURSE CONTEXT:
    - Course Level: " . ($course->level ?? 'General') . "
    - Subject: " . ($course->subject ?? 'General') . "

    MODULE CONTEXT:
    - Module: {$module->title}
    - Module Learning Objectives: " . $this->formatArrayForPrompt($module->learning_objectives ?? []) . "

    TOPIC DETAILS:
    - Topic: {$outline->title}
    - Topic Description: " . ($outline->description ?? 'No description') . "
    - Key Concepts: " . $this->formatArrayForPrompt($outline->key_concepts ?? []) . "
    - Learning Objectives: " . $this->formatArrayForPrompt($outline->learning_objectives ?? []) . "
    - Topic Type: {$outline->type}
    - Estimated Duration: " . ($outline->estimated_duration_minutes ?? 'Not specified') . " minutes

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

    QUIZ DESIGN GUIDELINES:
    - Questions should be appropriate for " . ($course->level ?? 'general') . " level
    - Questions should test understanding of the key concepts listed
    - Include a mix of difficulty levels (easy, medium, hard)
    - Make sure explanations are clear and educational
    - Questions should relate to real-world applications where possible
    - Avoid trick questions - focus on genuine understanding

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

    /**
     * Generate project requirements for a project topic
     */
    public function generateProjectRequirements(CourseOutline $outline): array
    {
        $prompt = $this->buildProjectRequirementsPrompt($outline);

        $messages = [
            [
                'role' => 'system',
                'content' => "You are an expert project designer. Create clear, achievable project requirements that help students apply their knowledge in practical ways."
            ],
            [
                'role' => 'user',
                'content' => $prompt
            ]
        ];

        try {
            $requirementsContent = $this->aiService->chat($messages, [
                'temperature' => 0.7,
                'max_tokens' => 2000,
            ], 'project_generation');

            // Parse the requirements
            $requirements = $this->parseProjectRequirements($requirementsContent);

            // Update the outline with requirements
            $outline->update([
                'project_requirements' => $requirements
            ]);

            return $requirements;

        } catch (\Exception $e) {
            Log::error('Project requirements generation failed', [
                'outline_id' => $outline->id,
                'error' => $e->getMessage()
            ]);
            throw new \Exception("Project requirements generation failed: " . $e->getMessage());
        }
    }

    protected function buildProjectRequirementsPrompt(CourseOutline $outline): string
    {
        $outline->load(['module.course', 'module']);
        $module = $outline->module;
        $course = $outline->module->course ?? null;

        return "
        Create project requirements for the following topic:

        COURSE CONTEXT:
        - Course: " . ($course->title ?? 'General Course') . "
        - Subject: " . ($course->subject ?? 'General') . "
        - Level: " . ($course->level ?? 'Beginner') . "

        MODULE CONTEXT:
        - Module: {$module->title}
        - Module Description: " . ($module->description ?? 'No description') . "

        TOPIC DETAILS:
        - Topic: {$outline->title}
        - Topic Description: " . ($outline->description ?? 'No description') . "
        - Key Concepts: " . $this->formatArrayForPrompt($outline->key_concepts ?? []) . "
        - Learning Objectives: " . $this->formatArrayForPrompt($outline->learning_objectives ?? []) . "
        - Estimated Duration: " . ($outline->estimated_duration_minutes ?? 60) . " minutes

        Please create a set of clear, actionable project requirements that:
        1. Allow students to demonstrate understanding of the key concepts
        2. Are appropriate for the course level: " . ($course->level ?? 'Beginner') . "
        3. Can be completed within the estimated duration
        4. Include clear deliverables
        5. Provide opportunities for creativity and application
        6. Include clear success criteria

        Return the requirements as a numbered list.
        ";
    }

    protected function parseProjectRequirements(string $content): array
    {
        // Split by lines and filter out empty ones
        $lines = array_filter(explode("\n", $content), function($line) {
            return trim($line) !== '';
        });

        $requirements = [];

        foreach ($lines as $line) {
            $line = trim($line);
            // Check if line starts with a number or bullet point
            if (preg_match('/^(\d+[\.\)]|\*|\-)\s+(.+)$/', $line, $matches)) {
                $requirements[] = trim($matches[2]);
            } elseif (!empty($line) && !preg_match('/^(Requirements|Project|Deliverables):/i', $line)) {
                // Add non-empty lines that aren't headers
                $requirements[] = $line;
            }
        }

        // If no requirements found, create some defaults
        if (empty($requirements)) {
            $requirements = [
                "Apply the key concepts learned in this topic",
                "Create a practical example or implementation",
                "Document your approach and reasoning",
                "Submit your work for review"
            ];
        }

        return array_values(array_unique($requirements));
    }

    public function generateCapstoneProject(Course $course, CourseOutline $topic = null): array
    {
        try {
            Log::info('Generating capstone project', [
                'course_id' => $course->id,
                'course_title' => $course->title,
                'topic_id' => $topic?->id
            ]);

            $prompt = $this->buildCapstonePrompt($course, $topic);

            $messages = [
                [
                    'role' => 'system',
                    'content' => "You are an expert educational project designer. Create a comprehensive capstone project that tests students' mastery of the entire course."
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ];

            $response = $this->aiService->chat($messages, [
                'temperature' => 0.7,
                'max_tokens' => 2000,
            ], 'capstone_generation');

            // Clean and parse response
            $cleanedContent = $this->cleanJsonResponse($response);
            $projectData = json_decode($cleanedContent, true);

            // If JSON parsing fails, use fallback
            if (json_last_error() !== JSON_ERROR_NONE || empty($projectData)) {
                Log::warning('AI returned invalid JSON for capstone, using fallback', [
                    'json_error' => json_last_error_msg(),
                    'course_id' => $course->id
                ]);

                $projectData = $this->createFallbackCapstone($course);
            }

            // Create or update capstone project in database
            $capstoneProject = CapstoneProject::updateOrCreate(
                ['course_id' => $course->id],
                [
                    'title' => $projectData['title'] ?? 'Capstone Project: ' . $course->title,
                    'description' => $projectData['description'] ?? 'Final project to demonstrate course mastery',
                    'requirements' => $projectData['requirements'] ?? $this->defaultRequirements($course),
                    'evaluation_criteria' => $projectData['evaluation_criteria'] ?? $this->defaultEvaluationCriteria(),
                    'due_date' => $course->target_completion_date ?? now()->addWeeks(2),
                    'status' => 'assigned',
                    'ai_model_used' => $this->aiService->getProviderCode(),
                ]
            );

            Log::info('Capstone project generated successfully', [
                'course_id' => $course->id,
                'capstone_id' => $capstoneProject->id,
                'title' => $capstoneProject->title
            ]);

            return [
                'capstone_project' => $capstoneProject,
                'project_data' => $projectData,
                'success' => true
            ];

        } catch (\Exception $e) {
            Log::error('Capstone project generation failed', [
                'course_id' => $course->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw new \Exception("Capstone project generation failed: " . $e->getMessage());
        }
    }

    /**
     * Build prompt for capstone project generation
     */
    private function buildCapstonePrompt(Course $course, ?CourseOutline $topic = null): string
    {
        $courseInfo = [
            'Title' => $course->title,
            'Subject' => $course->subject,
            'Level' => $course->level,
            'Description' => $course->description,
            'Learning Objectives' => is_array($course->learning_objectives)
                ? implode(', ', $course->learning_objectives)
                : $course->learning_objectives,
        ];

        if ($topic) {
            $courseInfo['Topic'] = $topic->title;
            $courseInfo['Topic Description'] = $topic->description;
            $courseInfo['Topic Concepts'] = is_array($topic->key_concepts)
                ? implode(', ', $topic->key_concepts)
                : '';
        }

        $prompt = "Create a capstone project for the following course:\n\n";

        foreach ($courseInfo as $key => $value) {
            if (!empty($value)) {
                $prompt .= "{$key}: {$value}\n";
            }
        }

        $prompt .= "\nReturn ONLY valid JSON in this format:\n";
        $prompt .= '{
            "title": "Project Title",
            "description": "Brief project description (2-3 sentences)",
            "requirements": ["Requirement 1", "Requirement 2", "Requirement 3"],
            "evaluation_criteria": [
                {"criteria": "Criteria 1", "weight": 0.3},
                {"criteria": "Criteria 2", "weight": 0.3},
                {"criteria": "Criteria 3", "weight": 0.2},
                {"criteria": "Criteria 4", "weight": 0.2}
            ]
        }';

        $prompt .= "\n\nRequirements: 3-5 clear, measurable requirements.";
        $prompt .= "\nEvaluation Criteria: 4 criteria that total 1.0 weight.";
        $prompt .= "\nMake it challenging but achievable for the course level.";

        return $prompt;
    }

    /**
     * Create fallback capstone project if AI fails
     */
    private function createFallbackCapstone(Course $course): array
    {
        return [
            'title' => 'Capstone Project: ' . $course->title,
            'description' => 'Demonstrate your mastery of ' . $course->subject . ' by completing this comprehensive final project.',
            'requirements' => [
                'Apply all key concepts learned throughout the course',
                'Create a practical solution or analysis',
                'Document your process and methodology',
                'Present your findings effectively',
            ],
            'evaluation_criteria' => [
                ['criteria' => 'Concept Application', 'weight' => 0.3],
                ['criteria' => 'Technical Accuracy', 'weight' => 0.3],
                ['criteria' => 'Completeness', 'weight' => 0.2],
                ['criteria' => 'Presentation Quality', 'weight' => 0.2],
            ]
        ];
    }

    /**
     * Default requirements for capstone project
     */
    private function defaultRequirements(Course $course): array
    {
        return [
            'Demonstrate understanding of course concepts',
            'Apply knowledge to a real-world problem',
            'Create comprehensive documentation',
            'Include testing and validation',
            'Present findings in a clear format',
        ];
    }

    /**
     * Default evaluation criteria
     */
    private function defaultEvaluationCriteria(): array
    {
        return [
            ['criteria' => 'Concept Understanding', 'weight' => 0.3],
            ['criteria' => 'Practical Application', 'weight' => 0.3],
            ['criteria' => 'Completeness', 'weight' => 0.2],
            ['criteria' => 'Documentation & Presentation', 'weight' => 0.2],
        ];
    }
}
