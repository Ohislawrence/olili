<?php
// app/Services/CourseGenerationService.php

namespace App\Services;

use App\Models\Course;
use App\Models\StudentProfile;
use App\Models\CourseOutline;
use App\Models\Module;
use App\Services\Ai\BaseAiService;
use Illuminate\Support\Facades\DB;
use App\Services\Ai\AiServiceManager;
use Illuminate\Support\Facades\Log;

class CourseGenerationService
{
    protected $aiService;

    public function __construct(BaseAiService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function generateCourse(StudentProfile $studentProfile, array $courseData): Course
    {
        return DB::transaction(function () use ($studentProfile, $courseData) {
            // Validate required data
            $this->validateCourseData($courseData);

            // Create the course
            $course = Course::create([
                'student_profile_id' => $studentProfile->id,
                'exam_board_id' => $courseData['exam_board_id'] ?? null,
                'title' => $courseData['title'],
                'subject' => $courseData['subject'],
                'description' => $courseData['description'] ?? '',
                'level' => $studentProfile->target_level,
                'target_completion_date' => $studentProfile->target_completion_date,
                'learning_objectives' => $courseData['learning_objectives'] ?? [],
                'ai_model_used' => $this->aiService->getProviderCode(),
                'generation_parameters' => [
                    'student_level' => $studentProfile->current_level,
                    'target_level' => $studentProfile->target_level,
                    'learning_goals' => $studentProfile->learning_goals,
                    'weekly_hours' => $studentProfile->weekly_study_hours,
                ],
                'status' => 'draft',
            ]);

            // Generate course outline using AI
            $outline = $this->generateCourseOutline($course, $studentProfile);

            // Update course with estimated duration
            $course->update([
                'estimated_duration_hours' => $this->calculateTotalDuration($outline),
            ]);

            return $course->load('modules', 'modules.topics');
        });
    }

    protected function validateCourseData(array $courseData): void
    {
        if (empty($courseData['title']) || empty($courseData['subject'])) {
            throw new \InvalidArgumentException('Course title and subject are required.');
        }
    }

    protected function generateCourseOutline(Course $course, StudentProfile $studentProfile): array
    {
        $prompt = $this->buildCourseOutlinePrompt($course, $studentProfile);

        $messages = [
            [
                'role' => 'system',
                'content' => "You are an expert educational course designer. Create a comprehensive course outline based on the student's needs, level, and goals. Return ONLY valid JSON, no other text."
            ],
            [
                'role' => 'user',
                'content' => $prompt
            ]
        ];

        try {
            // Set a specific model for course generation
            $availableModels = $this->aiService->getAvailableModels();
            if (!empty($availableModels)) {
                $this->aiService->setModel($availableModels[0]);
            }

            Log::info('Generating course outline with AI', [
                'course_id' => $course->id,
                'model' => $this->aiService->getCurrentModel()
            ]);

            $response = $this->aiService->chat($messages, [
                'temperature' => 0.7,
                'max_tokens' => 4000,
            ], 'course_generation');

            Log::debug('AI Response received', ['response' => $response]);

            $outlineData = json_decode($response, true);

            // Validate the structure
           // if (json_last_error() !== JSON_ERROR_NONE || !$this->validateOutlineStructure($outlineData)) {
           //     Log::warning('AI returned invalid JSON, attempting text parsing');
           //     $outlineData = $this->parseOutlineFromText($response);
           // }

            return $this->saveCourseOutline($course, $outlineData);

        } catch (\Exception $e) {
            Log::error('Course outline generation failed', [
                'course_id' => $course->id,
                'error' => $e->getMessage()
            ]);
            throw new \Exception("Course outline generation failed: " . $e->getMessage());
        }
    }

    protected function validateOutlineStructure(array $outlineData): bool
    {
        // Basic validation for required structure
        if (!isset($outlineData['modules']) || !is_array($outlineData['modules'])) {
            return false;
        }

        foreach ($outlineData['modules'] as $module) {
            if (empty($module['title']) || !isset($module['topics']) || !is_array($module['topics'])) {
                return false;
            }
        }

        return true;
    }

    protected function buildCourseOutlinePrompt(Course $course, StudentProfile $studentProfile): string
    {
        $learningGoals = is_array($studentProfile->learning_goals)
            ? implode(', ', $studentProfile->learning_goals)
            : $studentProfile->learning_goals;

        return <<<PROMPT
        Create a detailed course outline in JSON format for the following course:

        COURSE INFORMATION:
        - Title: {$course->title}
        - Subject: {$course->subject}
        - Student Current Level: {$studentProfile->current_level}
        - Student Target Level: {$studentProfile->target_level}
        - Target Completion: {$studentProfile->target_completion_date->format('Y-m-d')}
        - Weekly Study Hours: {$studentProfile->weekly_study_hours}
        - Learning Goals: {$learningGoals}

        REQUIRED JSON STRUCTURE:
        {
            "course": {
                "title": "improve the title provided",
                "description": "improve the description if provided, add a good one if none was provided"
            },
            "modules": [
                {
                    "title": "Module Title",
                    "description": "Module description",
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
                            "has_quiz": true,
                        }
                    ]
                }
            ],
            "quizzes": [
                {
                    "title": "Quiz Title",
                    "description": "Quiz description",
                    "order": 2,
                    "estimated_duration_minutes": 30
                }
            ],
            "capstone_project": {
                "title": "Final Project Title",
                "description": "Project description",
                "requirements": ["requirement1", "requirement2"]
            }
        }

        CRITICAL INSTRUCTIONS:
        1. Return ONLY valid, complete JSON - no additional text, no markdown code blocks
        2. Ensure all JSON brackets and braces are properly closed
        3. Create 3 modules that progress from basic to advanced concepts
        4. Each module should have 3 topics
        5. Ensure total duration matches the available study time
        6. Include practical exercises and assessments
        7. Align with the student's learning goals
        8. Double-check that your JSON is complete before returning

        IMPORTANT: Your response must be valid JSON that can be parsed by json_decode(). Do not truncate the response.

        PROMPT;
    }

    protected function saveCourseOutline(Course $course, array $outlineData): array
    {
        $savedItems = [];
        $moduleOrder = 1;

        try {
            // Update course with improved title and description
            $course->update([
                'title' => $outlineData['course']['title'] ?? $course->title,
                'description' => $outlineData['course']['description'] ?? $course->description
            ]);

            // Save modules and their topics
            foreach ($outlineData['modules'] ?? [] as $moduleData) {
                $module = Module::create([
                    'course_id' => $course->id,
                    'title' => $moduleData['title'],
                    'description' => $moduleData['description'] ?? '',
                    'order' => $moduleOrder++,
                    'estimated_duration_minutes' => 0, // Will be calculated from topics
                    'learning_objectives' => $moduleData['learning_objectives'] ?? [],
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
                        'has_project' =>  false,
                    ]);

                    $savedItems[] = $topic;
                }

                // Update module with total duration
                $module->update(['estimated_duration_minutes' => $moduleDuration]);
                $savedItems[] = $module;
            }

            // Save standalone quizzes as topics

            if (isset($outlineData['quizzes'])) {
                // Find the last module to attach the quiz to, or create a dedicated module
                $lastModule = $course->modules()->orderBy('order', 'desc')->first();
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
                        'description' =>  $quizData['description'] ?? '',
                        'order' => $lastModule->topics()->count() + 1,
                        'type' => 'quiz',
                        'estimated_duration_minutes' => $quizData['estimated_duration_minutes'] ?? 30,
                        'learning_objectives' => null,
                        'key_concepts' => null,
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

    protected function parseOutlineFromText(string $text): array
    {
        // Fallback parsing if JSON parsing fails
        $lines = explode("\n", $text);
        $modules = [];
        $currentModule = null;
        $moduleOrder = 1;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            if (preg_match('/^#+\s*(Module|Chapter)\s*\d+:\s*(.+)$/i', $line, $matches)) {
                if ($currentModule) {
                    $modules[] = $currentModule;
                }
                $currentModule = [
                    'title' => $matches[2],
                    'description' => '',
                    'order' => $moduleOrder++,
                    'topics' => []
                ];
            } elseif (preg_match('/^[-*]\s*(.+)$/', $line, $matches) && $currentModule) {
                $currentModule['topics'][] = [
                    'title' => $matches[1],
                    'description' => '',
                    'estimated_duration_minutes' => 45,
                    'has_quiz' => false,
                    'has_project' => false
                ];
            }
        }

        if ($currentModule) {
            $modules[] = $currentModule;
        }

        return [
            'course' => [
                'title' => 'Course Title',
                'description' => 'Course Description'
            ],
            'modules' => $modules
        ];
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
}
