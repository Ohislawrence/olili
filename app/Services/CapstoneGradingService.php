<?php
// app/Services/CapstoneGradingService.php

namespace App\Services;

use App\Models\CapstoneProject;
use App\Services\Ai\BaseAiService;
use Illuminate\Support\Facades\Log;

class CapstoneGradingService
{
    protected $aiService;

    public function __construct(BaseAiService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function gradeProject(CapstoneProject $project): void
    {
        $prompt = $this->buildGradingPrompt($project);

        $messages = [
            [
                'role' => 'system',
                'content' => "You are an expert educational assessor. Evaluate student projects fairly and provide constructive feedback based on the course curriculum and learning objectives."
            ],
            [
                'role' => 'user',
                'content' => $prompt
            ]
        ];

        try {
            $response = $this->aiService->chat($messages, [
                'temperature' => 0.2,
                'max_tokens' => 2000,
            ], 'project_grading');

            $gradingData = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::warning('AI returned invalid JSON for project grading, using fallback parsing', [
                    'project_id' => $project->id,
                    'response' => $response
                ]);
                $gradingData = $this->parseGradingFromText($response);
            }

            $project->gradeByAI(
                $gradingData['score'] ?? 0,
                $gradingData['feedback'] ?? []
            );

            Log::info('Project graded successfully', [
                'project_id' => $project->id,
                'score' => $gradingData['score'] ?? 0
            ]);

        } catch (\Exception $e) {
            Log::error('Project grading failed', [
                'project_id' => $project->id,
                'error' => $e->getMessage()
            ]);
            throw new \Exception("Project grading failed: " . $e->getMessage());
        }
    }

    protected function buildGradingPrompt(CapstoneProject $project): string
    {
        $course = $project->course;

        // Get all modules and topics for comprehensive course context
        $modules = $course->modules()->with('topics')->orderBy('order')->get();

        $courseStructure = $this->formatCourseStructure($modules);
        $learningObjectives = $this->extractLearningObjectives($modules);

        return "
        Evaluate the following student capstone project submission:

        COURSE INFORMATION:
        - Title: {$course->title}
        - Subject: {$course->subject}
        - Level: {$course->level}
        - Student Target Level: {$course->level}

        COURSE STRUCTURE AND CONTENT:
        {$courseStructure}

        COURSE LEARNING OBJECTIVES:
        " . json_encode($learningObjectives) . "

        PROJECT DETAILS:
        - Title: {$project->title}
        - Description: {$project->description}

        PROJECT REQUIREMENTS:
        " . json_encode($project->requirements) . "

        EVALUATION CRITERIA:
        " . json_encode($project->evaluation_criteria) . "

        STUDENT SUBMISSION:
        {$project->student_submission}

        SUBMISSION FILES: " . json_encode($project->submission_files ?? []) . "

        Please provide evaluation in JSON format:
        {
            \"score\": 85.5,
            \"feedback\": {
                \"strengths\": [\"List 3-5 key strengths aligned with course objectives\"],
                \"weaknesses\": [\"List 3-5 areas for improvement with specific references\"],
                \"suggestions\": [\"Provide 3-5 actionable suggestions for enhancement\"],
                \"overall_feedback\": \"Comprehensive assessment of how well the project demonstrates mastery of course concepts\"
            },
            \"breakdown\": [
                {
                    \"criteria\": \"Understanding of Concepts\",
                    \"score\": 90,
                    \"comments\": \"Specific comments on conceptual understanding with examples from submission\"
                },
                {
                    \"criteria\": \"Practical Application\",
                    \"score\": 85,
                    \"comments\": \"Evaluation of how well concepts were applied practically\"
                },
                {
                    \"criteria\": \"Creativity and Innovation\",
                    \"score\": 80,
                    \"comments\": \"Assessment of creative thinking and innovative approaches\"
                },
                {
                    \"criteria\": \"Documentation and Presentation\",
                    \"score\": 75,
                    \"comments\": \"Evaluation of documentation quality and presentation clarity\"
                }
            ],
            \"alignment_with_course\": {
                \"covered_topics\": [\"List specific course topics that were effectively addressed\"],
                \"missed_opportunities\": [\"List areas where more course concepts could have been applied\"],
                \"mastery_demonstration\": \"Assessment of how well the project demonstrates course mastery\"
            }
        }

        GRADING INSTRUCTIONS:
        1. Evaluate based on alignment with course curriculum and learning objectives
        2. Consider the student's target level and course difficulty
        3. Provide specific examples from the submission in your feedback
        4. Focus on constructive, actionable feedback
        5. Score should reflect mastery of the course material (0-100 scale)
        6. Reference specific modules and topics from the course structure

        Be fair, constructive, and specific in your evaluation. Provide actionable feedback that helps the student improve.
        ";
    }

    protected function formatCourseStructure($modules): string
    {
        $structure = "Course Modules and Topics:\n\n";

        foreach ($modules as $module) {
            $structure .= "MODULE: {$module->title}\n";
            $structure .= "Description: {$module->description}\n";
            $structure .= "Learning Objectives: " . json_encode($module->learning_objectives ?? []) . "\n";
            $structure .= "Topics:\n";

            foreach ($module->topics as $topic) {
                $structure .= "  - {$topic->title}\n";
                $structure .= "    Key Concepts: " . json_encode($topic->key_concepts ?? []) . "\n";
                $structure .= "    Learning Objectives: " . json_encode($topic->learning_objectives ?? []) . "\n";
            }
            $structure .= "\n";
        }

        return $structure;
    }

    protected function extractLearningObjectives($modules): array
    {
        $objectives = [];

        foreach ($modules as $module) {
            if (!empty($module->learning_objectives)) {
                $objectives = array_merge($objectives, $module->learning_objectives);
            }

            foreach ($module->topics as $topic) {
                if (!empty($topic->learning_objectives)) {
                    $objectives = array_merge($objectives, $topic->learning_objectives);
                }
            }
        }

        return array_unique($objectives);
    }

    protected function parseGradingFromText(string $text): array
    {
        // Fallback parsing for grading data
        $lines = explode("\n", $text);
        $score = 0;
        $feedback = [
            'strengths' => [],
            'weaknesses' => [],
            'suggestions' => [],
            'overall_feedback' => $text
        ];

        foreach ($lines as $line) {
            if (preg_match('/score.*?(\d+(?:\.\d+)?)/i', $line, $matches)) {
                $score = floatval($matches[1]);
                break;
            }
        }

        // Simple text analysis for strengths/weaknesses
        if (stripos($text, 'excellent') !== false || stripos($text, 'good') !== false || stripos($text, 'well') !== false) {
            $feedback['strengths'][] = "Good understanding demonstrated in key areas";
        }

        if (stripos($text, 'improve') !== false || stripos($text, 'better') !== false || stripos($text, 'weak') !== false) {
            $feedback['weaknesses'][] = "Areas identified for improvement in the submission";
        }

        return [
            'score' => $score,
            'feedback' => $feedback
        ];
    }
}
