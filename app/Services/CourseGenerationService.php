<?php
// app/Services/CourseGenerationService.php

namespace App\Services;

use App\Models\Course;
use App\Models\Module;
use App\Models\CourseOutline;
use App\Services\Ai\BaseAiService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Jobs\GenerateCourseContentJob;
use App\Jobs\GenerateCourseQuizzesJob;
use App\Models\User;

class CourseGenerationService
{
    protected $aiService;

    public function __construct(BaseAiService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Generate a course for admin/public use only
     */
    public function generateCourse(array $courseData): Course
    {
        DB::beginTransaction();

        try {
            // Validate required data
            $this->validateCourseData($courseData);

            // Get the admin user
            $adminUser = User::where('email', 'lawrenceohis@gmail.com')->first();
            $createdByUserId = $courseData['created_by_user_id'] ?? ($adminUser->id ?? auth()->id());

            // Create the base course
            $course = Course::create([
                'exam_board_id' => $courseData['exam_board_id'] ?? null,
                'title' => $courseData['title'],
                'slug' => Str::slug($courseData['title']),
                'subject' => $courseData['subject'],
                'description' => $courseData['description'] ?? '',
                'level' => $courseData['level'] ?? 'intermediate',
                'learning_objectives' => $courseData['learning_objectives'] ?? [],
                'prerequisites' => $courseData['prerequisites'] ?? [],
                'ai_model_used' => $this->aiService->getProviderCode(),
                'generation_parameters' => [
                    'target_level' => $courseData['level'] ?? 'intermediate',
                    'estimated_duration' => $courseData['estimated_duration_hours'] ?? 40,
                ],
                'status' => 'draft',
                'is_public' => $courseData['is_public'] ?? false,
                'visibility' => $courseData['visibility'] ?? 'private',
                'created_by_user_id' => $createdByUserId,
                'created_by' => $courseData['created_by'] ?? 'admin',
                'needs_content_generation' => true, // Flag to indicate content needs to be generated
            ]);

            // Generate course outline using AI
            $prompt = $this->buildCoursePrompt($courseData);

            $messages = [
                [
                    'role' => 'system',
                    'content' => "You are an expert educational course designer creating public courses for diverse students. Return ONLY valid JSON, no other text."
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ];

            Log::info('Generating admin course outline with AI', [
                'course_title' => $courseData['title'],
                'subject' => $courseData['subject']
            ]);

            // Use the existing AI service
            $response = $this->aiService->chat($messages, [
                'temperature' => 0.7,
                'max_tokens' => 4000,
            ], 'admin_course_generation');

            Log::debug('Admin AI Response received', [
                'response_length' => strlen($response),
                'response_preview' => substr($response, 0, 200)
            ]);

            // Clean and parse response - using the simple method that worked before
            $cleanResponse = $this->cleanJsonResponse($response);
            $structure = json_decode($cleanResponse, true);

            if (!$structure || !isset($structure['modules'])) {
                Log::error('Failed to parse AI response or missing modules', [
                    'json_error' => json_last_error_msg(),
                    'has_course' => isset($structure['course']),
                    'has_modules' => isset($structure['modules'])
                ]);
                throw new \Exception('Failed to generate valid course structure from AI');
            }

            Log::debug('Parsed structure successfully', [
                'module_count' => count($structure['modules']),
                'has_course' => isset($structure['course']),
                'structure_keys' => array_keys($structure)
            ]);

            // Update course with improved title/description
            if (isset($structure['course'])) {
                $course->update([
                    'title' => $structure['course']['title'] ?? $course->title,
                    'slug' => Str::slug($structure['course']['title']),
                    'description' => $structure['course']['description'] ?? $course->description
                ]);
            }

            // Save the course outline
            $savedItems = $this->saveCourseOutline($course, $structure);

            // Calculate and update total duration
            $totalMinutes = $this->calculateTotalDuration($savedItems);
            $course->update([
                'estimated_duration_hours' => $totalMinutes,
            ]);

            DB::commit();

            // Dispatch background jobs for content and quiz generation
            //$this->dispatchContentGenerationJobs($course);

            // Log the AI usage if needed
            /**
            if (class_exists('App\Models\AiUsageLog')) {
                \App\Models\AiUsageLog::create([
                    'user_id' => $createdByUserId,
                    'action' => 'generate_admin_course',
                    'model_used' => $this->aiService->getProviderCode(),
                    'tokens_used' => 1000,
                    'cost' => 0.02,
                    'ai_provider_id' => 2,
                ]);
            }
            */
            return $course->load('modules', 'modules.topics');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Admin course generation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function dispatchContentGenerationJobs(Course $course): void
    {
        // Dispatch content generation job
        GenerateCourseContentJob::dispatch($course)
            ->onQueue('course_generation')
            ->delay(now()->addSeconds(5)); // Small delay to ensure course is fully saved

        // Dispatch quiz generation job for topics with has_quiz = true
        GenerateCourseQuizzesJob::dispatch($course)
            ->onQueue('course_generation')
            ->delay(now()->addMinutes(3)); // Start after content generation

        Log::info('Background jobs dispatched for course content generation', [
            'course_id' => $course->id,
            'course_title' => $course->title
        ]);
    }

    protected function validateCourseData(array $courseData): void
    {
        if (empty($courseData['title']) || empty($courseData['subject'])) {
            throw new \InvalidArgumentException('Course title and subject are required.');
        }
    }

    private function buildCoursePrompt(array $courseData): string
    {
        $learningObjectives = isset($courseData['learning_objectives']) && is_array($courseData['learning_objectives'])
            ? implode(', ', $courseData['learning_objectives'])
            : ($courseData['learning_objectives'] ?? 'No specific objectives provided');

        $prerequisites = isset($courseData['prerequisites']) && is_array($courseData['prerequisites'])
            ? implode(', ', $courseData['prerequisites'])
            : ($courseData['prerequisites'] ?? 'No prerequisites required');

        $estimatedDuration = $courseData['estimated_duration_hours'] ?? 20;
        $description = $courseData['description'] ?? 'No description provided';
        $level = $courseData['level'] ?? 'intermediate';

        return <<<PROMPT
Create a course outline in JSON format for a PUBLIC course that will be available to multiple students:

COURSE INFORMATION:
- Title: {$courseData['title']}
- Subject: {$courseData['subject']}
- Target Level: {$level}
- Estimated Duration: {$estimatedDuration} hours
- Description: {$description}
- Learning Objectives: {$learningObjectives}
- Prerequisites: {$prerequisites}
- Course Type: public (for multiple students with diverse backgrounds)

REQUIRED JSON STRUCTURE:
{
    "course": {
        "title": "improve the title to be engaging for public enrollment",
        "description": "improve the description to attract diverse students and clearly state course benefits"
    },
    "modules": [
        {
            "title": "Module Title",
            "description": "Module description suitable for public course",
            "order": 1,
            "learning_objectives": ["objective1", "objective2"],
            "topics": [
                {
                    "title": "Topic Title",
                    "description": "Topic description",
                    "order": 1,
                    "estimated_duration_minutes": 60,
                    "learning_objectives": ["objective1", "objective2"],
                    "key_concepts": ["concept1", "concept2"],
                    "resources": ["resource1", "resource2"],
                    "has_quiz": true
                }
            ]
        }
    ],
    "quizzes": [
        {
            "title": "Quiz Title",
            "description": "Quiz description this should be one quiz that covers everything in this course",
            "order": 2,
            "estimated_duration_minutes": 30
        }
    ],
    "capstone_project": {
        "title": "Final Project Title",
        "description": "Project description suitable for public course evaluation",
        "requirements": ["requirement1", "requirement2"]
    }
}

CRITICAL INSTRUCTIONS FOR PUBLIC COURSE:
1. Return ONLY valid, complete JSON - no additional text, no markdown code blocks
2. Ensure all JSON brackets and braces are properly closed
3. Create between 2 to 5 modules that progress from basic to advanced concepts
4. Each module should have 2 to 6 topics (inclusive).
5. Total duration should be approximately {$estimatedDuration} hours
6. Make content suitable for diverse student backgrounds
7. Include practical exercises and assessments that work for all learners
8. Align with the provided learning objectives
9. Create content that is scalable and maintainable
10. Do NOT create module-level or topic-level quiz objects. Only set has_quiz flags and create ONE course-level quiz in the quizzes array.
11. Avoid assuming prior professional experience unless stated in prerequisites.
12. Capstone project must be achievable by a student who completes all modules.

SPECIFIC REQUIREMENTS:
- Topics should use "has_quiz": true/false (not "type" field)
- Include clear prerequisites and expectations
- Make assessment criteria transparent and fair
- Ensure topics build logically from basic to advanced
- "quizzes" should cover everything that has been learnt, so it can be 1 general quiz

IMPORTANT: Your response must be valid JSON that can be parsed by json_decode(). Do not truncate the response.

PROMPT;
    }

    protected function saveCourseOutline(Course $course, array $outlineData): array
    {
        $savedItems = [];
        $moduleOrder = 1;

        try {
            // Save modules and their topics
            foreach ($outlineData['modules'] ?? [] as $moduleData) {
                $module = Module::create([
                    'course_id' => $course->id,
                    'title' => $moduleData['title'],
                    'description' => $moduleData['description'] ?? '',
                    'order' => $moduleOrder++,
                    'estimated_duration_minutes' => 0,
                    'learning_objectives' => $moduleData['learning_objectives'] ?? [],
                    'needs_content_generation' => true, // Flag for content generation
                ]);

                $moduleDuration = 0;
                $topicOrder = 1;

                foreach ($moduleData['topics'] ?? [] as $topicData) {
                    $topicDuration = $topicData['estimated_duration_minutes'] ?? 60;
                    $moduleDuration += $topicDuration;

                    $topic = CourseOutline::create([
                        'module_id' => $module->id,
                        'title' => $topicData['title'],
                        'description' => $topicData['description'] ?? '',
                        'order' => $topicOrder++,
                        'type' => 'topic',
                        'estimated_duration_minutes' => $topicDuration,
                        'learning_objectives' => $topicData['learning_objectives'] ?? [],
                        'key_concepts' => $topicData['key_concepts'] ?? [],
                        'resources' => $topicData['resources'] ?? [],
                        'has_quiz' => $topicData['has_quiz'] ?? false,
                        'has_project' => false,
                        'needs_content_generation' => true, // Flag for content generation
                        'needs_quiz_generation' => $topicData['has_quiz'] ?? false, // Flag for quiz generation
                    ]);

                    $savedItems[] = $topic;
                }

                // Update module with total duration
                $module->update(['estimated_duration_minutes' => $moduleDuration]);
                $savedItems[] = $module;
            }

            // Save standalone quizzes
            if (isset($outlineData['quizzes'])) {
                foreach ($outlineData['quizzes'] ?? [] as $quizData) {
                    // Create a dedicated module for the quiz
                    $quizModule = Module::create([
                        'course_id' => $course->id,
                        'title' => $quizData['title'],
                        'description' => $quizData['description'] ?? '',
                        'order' => $moduleOrder++,
                        'estimated_duration_minutes' => $quizData['estimated_duration_minutes'] ?? 30,
                        'learning_objectives' => ['General knowledge test'],
                    ]);

                    $quizTopic = CourseOutline::create([
                        'module_id' => $quizModule->id,
                        'title' => $quizData['title'],
                        'description' => $quizData['description'] ?? '',
                        'order' => 1,
                        'type' => 'quiz',
                        'estimated_duration_minutes' => $quizData['estimated_duration_minutes'] ?? 30,
                        'has_project' => false,
                    ]);

                    $savedItems[] = $quizModule;
                    $savedItems[] = $quizTopic;
                }
            }

            // Save capstone project if exists
            if (isset($outlineData['capstone_project'])) {
                // Create a dedicated module for the capstone project
                $capstoneModule = Module::create([
                    'course_id' => $course->id,
                    'title' => 'Capstone Project',
                    'description' => 'Final project to demonstrate course mastery',
                    'order' => $moduleOrder++,
                    'estimated_duration_minutes' => 0,
                    'learning_objectives' => ['Apply all learned concepts to a real-world project'],
                ]);

                $capstoneTopic = CourseOutline::create([
                    'module_id' => $capstoneModule->id,
                    'title' => $outlineData['capstone_project']['title'],
                    'description' => $outlineData['capstone_project']['description'],
                    'order' => 1,
                    'type' => 'project',
                    'estimated_duration_minutes' => 300, // 5 hours default for project
                    'learning_objectives' => ['Demonstrate comprehensive understanding of course material'],
                    'key_concepts' => $outlineData['capstone_project']['requirements'] ?? [],
                    'has_project' => true,
                ]);

                $savedItems[] = $capstoneModule;
                $savedItems[] = $capstoneTopic;

                if (method_exists($course, 'capstoneProject')) {
                    $course->capstoneProject()->create([
                        'title' => $outlineData['capstone_project']['title'],
                        'description' => $outlineData['capstone_project']['description'],
                        'requirements' => $outlineData['capstone_project']['requirements'] ?? [],
                        'evaluation_criteria' => $this->generateEvaluationCriteria(),
                        'due_date' => $course->target_completion_date,
                    ]);
                }
            }

            return $savedItems;

        } catch (\Exception $e) {
            Log::error('Failed to save course outline', [
                'course_id' => $course->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    protected function calculateTotalDuration(array $outlineItems): int
    {
        $totalMinutes = 0;

        foreach ($outlineItems as $item) {
            if ($item instanceof Module) {
                $totalMinutes += $item->estimated_duration_minutes;
            } elseif ($item instanceof CourseOutline) {
                $totalMinutes += $item->estimated_duration_minutes;
            }
        }

        return ceil($totalMinutes / 60); // Convert to hours
    }

    private function cleanJsonResponse(string $response): string
    {
        // Remove markdown code blocks
        $response = preg_replace('/```json\s*/', '', $response);
        $response = preg_replace('/```\s*/', '', $response);

        // Remove any text before the first {
        $pos = strpos($response, '{');
        if ($pos !== false) {
            $response = substr($response, $pos);
        }

        // Remove any text after the last }
        $pos = strrpos($response, '}');
        if ($pos !== false) {
            $response = substr($response, 0, $pos + 1);
        }

        return trim($response);
    }

    protected function generateEvaluationCriteria(): array
    {
        return [
            ['criteria' => 'Understanding of Concepts', 'weight' => 0.3],
            ['criteria' => 'Practical Application', 'weight' => 0.3],
            ['criteria' => 'Creativity and Innovation', 'weight' => 0.2],
            ['criteria' => 'Documentation and Presentation', 'weight' => 0.2],
        ];
    }

    /**
     * Update an existing course with AI-generated content
     */
    public function updateCourseContent(Course $course, array $updateData): Course
    {
        DB::beginTransaction();

        try {
            // If modules should be regenerated
            if ($updateData['regenerate_outline'] ?? false) {
                // Delete existing modules and topics
                $course->modules()->delete();

                // Regenerate the outline
                $prompt = $this->buildCoursePrompt([
                    'title' => $course->title,
                    'subject' => $course->subject,
                    'description' => $course->description,
                    'level' => $course->level,
                    'learning_objectives' => $course->learning_objectives,
                    'prerequisites' => $course->prerequisites,
                    'estimated_duration_hours' => $course->estimated_duration_hours,
                ]);

                $messages = [
                    [
                        'role' => 'system',
                        'content' => "You are updating an existing course outline. Return ONLY valid JSON."
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ];

                $response = $this->aiService->chat($messages, [
                    'temperature' => 0.7,
                    'max_tokens' => 2500,
                ], 'course_update');

                $cleanResponse = $this->cleanJsonResponse($response);
                $structure = json_decode($cleanResponse, true);

                if ($structure && isset($structure['modules'])) {
                    $savedItems = $this->saveCourseOutline($course, $structure);

                    // Update total duration
                    $totalMinutes = $this->calculateTotalDuration($savedItems);
                    $course->update(['estimated_duration_hours' => $totalMinutes]);
                }
            }

            DB::commit();
            return $course->fresh()->load('modules', 'modules.topics');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Course update failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate additional content for a specific topic
     */
    public function generateTopicContent(CourseOutline $topic, string $contentType = 'lesson'): void
    {
        // Generate content based on topic information
        $prompt = "Generate {$contentType} content for the topic: {$topic->title}\nDescription: {$topic->description}\nKey Concepts: " .
                 implode(', ', $topic->key_concepts ?? []) .
                 "\nLearning Objectives: " . implode(', ', $topic->learning_objectives ?? []);

        $messages = [
            [
                'role' => 'system',
                'content' => "You are an educational content creator. Generate engaging and informative content."
            ],
            [
                'role' => 'user',
                'content' => $prompt
            ]
        ];

        try {
            $content = $this->aiService->chat($messages, [
                'temperature' => 0.8,
                'max_tokens' => 2000,
            ], 'topic_content_generation');

            // Save the generated content (you'll need to add a content field to CourseOutline or create a separate model)
            // For now, we'll log it
            Log::info('Generated content for topic', [
                'topic_id' => $topic->id,
                'content_length' => strlen($content),
                'content_preview' => substr($content, 0, 200)
            ]);

        } catch (\Exception $e) {
            Log::error('Topic content generation failed', [
                'topic_id' => $topic->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
