<?php
// app/Services/ChatService.php

namespace App\Services;

use App\Models\ChatSession;
use App\Models\ChatMessage;
use App\Models\CourseOutline;
use App\Models\Module;
use App\Services\Ai\BaseAiService;
use Illuminate\Support\Facades\Log;

class ChatService
{
    protected $aiService;

    public function __construct(BaseAiService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function createSession($userId, $courseId = null, $outlineId = null): ChatSession
    {
        $topicContext = null;
        $contextParameters = [];

        if ($outlineId) {
            $outline = CourseOutline::with(['module.course', 'module'])->find($outlineId);

            if ($outline) {
                $topicContext = $outline->title;
                $contextParameters = [
                    'learning_objectives' => $outline->learning_objectives ?? [],
                    'key_concepts' => $outline->key_concepts ?? [],
                    'resources' => $outline->resources ?? [],
                    'topic_description' => $outline->description,
                    'course_title' => $outline->module->course->title ?? 'Unknown Course',
                    'module_title' => $outline->module->title ?? 'Unknown Module',
                    'module_description' => $outline->module->description ?? '',
                    'module_learning_objectives' => $outline->module->learning_objectives ?? [],
                ];
            }
        }

        return ChatSession::create([
            'user_id' => $userId,
            'course_id' => $courseId,
            'course_outline_id' => $outlineId,
            'topic_context' => $topicContext,
            'context_parameters' => $contextParameters,
            'is_active' => true,
            'last_activity_at' => now(),
        ]);
    }

    public function sendMessage(ChatSession $session, string $message): ChatMessage
    {
        // Add user message
        $userMessage = $session->addMessage('user', $message);

        // Generate AI response
        $aiResponse = $this->generateAiResponse($session, $message);

        // Add AI message
        $aiMessage = $session->addMessage('ai', $aiResponse['content'], $aiResponse['metadata'], $this->aiService->getProviderCode());

        // Update session activity
        $session->update(['last_activity_at' => now()]);

        return $aiMessage;
    }

    protected function generateAiResponse(ChatSession $session, string $userMessage): array
    {
        $messages = $this->buildMessageHistory($session, $userMessage);

        try {
            $response = $this->aiService->chat($messages, [
                'temperature' => 0.7,
                'max_tokens' => 1000,
            ], 'chat');

            $isRelated = $this->checkTopicRelevance($userMessage, $session->topic_context, $session->context_parameters);

            return [
                'content' => $response,
                'metadata' => [
                    'is_related_to_topic' => $isRelated,
                    'topic_context' => $session->topic_context,
                    'module_context' => $session->context_parameters['module_title'] ?? null,
                    'ai_model_used' => $this->aiService->getProviderCode(),
                ]
            ];

        } catch (\Exception $e) {
            Log::error('AI response generation failed', [
                'session_id' => $session->id,
                'error' => $e->getMessage()
            ]);

            return [
                'content' => "I'm sorry, I'm having trouble responding right now. Please try again in a moment.",
                'metadata' => [
                    'is_related_to_topic' => true,
                    'topic_context' => $session->topic_context,
                    'error' => true
                ]
            ];
        }
    }

    protected function buildMessageHistory(ChatSession $session, string $userMessage): array
    {
        $messages = [
            [
                'role' => 'system',
                'content' => $this->buildSystemPrompt($session)
            ]
        ];

        // Add recent message history (last 10 messages)
        $recentMessages = $session->messages()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->reverse();

        foreach ($recentMessages as $chatMessage) {
            $messages[] = [
                'role' => $chatMessage->isFromUser() ? 'user' : 'assistant',
                'content' => $chatMessage->message
            ];
        }

        // Add current user message
        $messages[] = [
            'role' => 'user',
            'content' => $userMessage
        ];

        return $messages;
    }

    protected function buildSystemPrompt(ChatSession $session): string
    {
        $basePrompt = "You are an AI tutor helping a student with their learning. ";

        if ($session->topic_context) {
            $context = $session->context_parameters;

            $basePrompt .= "

            COURSE: {$context['course_title']}
            MODULE: {$context['module_title']}
            CURRENT TOPIC: {$session->topic_context}

            MODULE DESCRIPTION: {$context['module_description']}
            TOPIC DESCRIPTION: {$context['topic_description']}

            Please focus your responses specifically on this topic and related concepts within the module.
            If the student asks about unrelated topics, gently guide them back to the current subject.

            MODULE LEARNING OBJECTIVES:
            " . implode(', ', $context['module_learning_objectives'] ?? []) . "

            TOPIC LEARNING OBJECTIVES:
            " . implode(', ', $context['learning_objectives'] ?? []) . "

            KEY CONCEPTS TO FOCUS ON:
            " . implode(', ', $context['key_concepts'] ?? []) . "

            AVAILABLE RESOURCES:
            " . implode(', ', $context['resources'] ?? []) . "
            ";
        } else {
            $basePrompt .= "
            You are having a general conversation about learning. Provide helpful educational guidance.
            ";
        }

        $basePrompt .= "

        TUTORING GUIDELINES:
        - Provide clear, educational explanations tailored to the student's level
        - Use examples and analogies to illustrate concepts
        - Ask follow-up questions to check understanding
        - Be encouraging and supportive
        - Admit when you don't know something and suggest where to find information
        - Suggest additional resources when helpful
        - If the student seems stuck, break down concepts into smaller steps
        - Connect new concepts to what the student already knows
        - Provide practical applications of theoretical concepts
        ";

        return $basePrompt;
    }

    protected function checkTopicRelevance(string $message, ?string $topicContext, array $contextParameters): bool
    {
        if (!$topicContext) {
            return true; // No topic restriction
        }

        // Combine all relevant keywords from module and topic
        $relevantKeywords = [];

        // Add topic keywords
        if ($topicContext) {
            $relevantKeywords = array_merge($relevantKeywords, explode(' ', strtolower($topicContext)));
        }

        // Add module keywords
        if (isset($contextParameters['module_title'])) {
            $relevantKeywords = array_merge($relevantKeywords, explode(' ', strtolower($contextParameters['module_title'])));
        }

        // Add key concepts
        if (isset($contextParameters['key_concepts'])) {
            foreach ($contextParameters['key_concepts'] as $concept) {
                $relevantKeywords = array_merge($relevantKeywords, explode(' ', strtolower($concept)));
            }
        }

        $relevantKeywords = array_unique($relevantKeywords);
        $messageWords = explode(' ', strtolower($message));

        $matchingWords = array_intersect($relevantKeywords, $messageWords);

        return count($matchingWords) > 1 || similar_text(strtolower($message), strtolower($topicContext)) > 15;
    }

    public function getActiveSession($userId, $courseId = null): ?ChatSession
    {
        return ChatSession::where('user_id', $userId)
            ->when($courseId, function ($query) use ($courseId) {
                return $query->where('course_id', $courseId);
            })
            ->where('is_active', true)
            ->where('last_activity_at', '>', now()->subHours(2))
            ->first();
    }

    public function closeSession(ChatSession $session): void
    {
        $session->update(['is_active' => false]);

        Log::info('Chat session closed', [
            'session_id' => $session->id,
            'user_id' => $session->user_id,
            'message_count' => $session->messages()->count()
        ]);
    }

    /**
     * Get or create active session for user
     */
    public function getOrCreateSession($userId, $courseId = null, $outlineId = null): ChatSession
    {
        $activeSession = $this->getActiveSession($userId, $courseId);

        if ($activeSession) {
            // Update the existing session if outline ID is provided and different
            if ($outlineId && $activeSession->course_outline_id != $outlineId) {
                $activeSession->update([
                    'course_outline_id' => $outlineId,
                    'topic_context' => CourseOutline::find($outlineId)->title ?? null,
                    'context_parameters' => $this->buildContextParameters($outlineId),
                ]);
            }
            return $activeSession;
        }

        return $this->createSession($userId, $courseId, $outlineId);
    }

    /**
     * Build context parameters for a given outline ID
     */
    public function buildContextParameters($outlineId): array
    {
        if (!$outlineId) {
            return [];
        }

        $outline = CourseOutline::with(['module.course', 'module'])->find($outlineId);

        if (!$outline) {
            return [];
        }

        return [
            'learning_objectives' => $outline->learning_objectives ?? [],
            'key_concepts' => $outline->key_concepts ?? [],
            'resources' => $outline->resources ?? [],
            'topic_description' => $outline->description,
            'course_title' => $outline->module->course->title ?? 'Unknown Course',
            'module_title' => $outline->module->title ?? 'Unknown Module',
            'module_description' => $outline->module->description ?? '',
            'module_learning_objectives' => $outline->module->learning_objectives ?? [],
        ];
    }
}
