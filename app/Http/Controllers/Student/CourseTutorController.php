<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\ChatSession;
use App\Services\ChatService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class CourseTutorController extends Controller
{
    protected $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    /**
     * Get or create active chat session for a course
     */
    public function getCourseSession(Course $course): JsonResponse
    {
        try {
            $student = auth()->user();

            // Verify the course belongs to the student
            if (!$student->courses()->where('courses.id', $course->id)->exists()) {
                return response()->json([
                    'error' => 'Course not found or access denied.',
                ], 404);
            }

            // Get or create active session for this course
            $session = $this->chatService->getOrCreateSession(
                $student->id,
                $course->id,
                null // No specific topic initially
            );

            // Load messages for the session
            $session->load(['messages' => function ($query) {
                $query->orderBy('created_at', 'asc')->limit(50);
            }]);

            return response()->json([
                'success' => true,
                'session' => $session,
                'messages' => $session->messages,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get course chat session', [
                'course_id' => $course->id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to load chat session.',
            ], 500);
        }
    }

    /**
     * Send a message in course chat session
     */
    public function sendMessage(Course $course, Request $request): JsonResponse
    {
        try {
            $request->validate([
                'message' => 'required|string|max:1000',
                'outline_id' => 'nullable|exists:course_outlines,id',
            ]);

            $student = auth()->user();

            // Verify course access
            if (!$student->courses()->where('courses.id', $course->id)->exists()) {
                return response()->json([
                    'error' => 'Course not found or access denied.',
                ], 404);
            }

            // Get or create session
            $session = $this->chatService->getOrCreateSession(
                $student->id,
                $course->id,
                $request->outline_id
            );

            // Send message
            $aiMessage = $this->chatService->sendMessage($session, $request->message);

            // Return the AI response
            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $aiMessage->id,
                    'sender_type' => 'ai',
                    'message' => $aiMessage->message,
                    'metadata' => $aiMessage->metadata,
                    'created_at' => $aiMessage->created_at,
                    'is_related_to_topic' => $aiMessage->metadata['is_related_to_topic'] ?? true,
                ],
                'session_id' => $session->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send course chat message', [
                'course_id' => $course->id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to send message. Please try again.',
            ], 500);
        }
    }

    /**
     * Update chat context (change topic)
     */
    public function updateContext(Course $course, Request $request): JsonResponse
    {
        try {
            $request->validate([
                'outline_id' => 'nullable|exists:course_outlines,id',
                'session_id' => 'required|exists:chat_sessions,id',
            ]);

            $student = auth()->user();

            // Verify session belongs to user and course
            $session = ChatSession::where('id', $request->session_id)
                ->where('user_id', $student->id)
                ->where('course_id', $course->id)
                ->first();

            if (!$session) {
                return response()->json([
                    'success' => false,
                    'error' => 'Chat session not found.',
                ], 404);
            }
            Log::error('Failed to update chat context', [
                'course_id' => $course->id,
                'outline_id' => $request->outline_id,
                ]);

            // Update context
            if ($request->outline_id) {
                $outline = $course->topics()->where('course_outlines.id', $request->outline_id)->first();
                if (!$outline) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Topic not found in this course.',
                    ], 404);
                }

                $contextParameters = $this->chatService->buildContextParameters($request->outline_id);

                $session->update([
                    'course_outline_id' => $request->outline_id,
                    'topic_context' => $outline->title,
                    'context_parameters' => $contextParameters,
                ]);
            } else {
                // Clear context (general course chat)
                $session->update([
                    'course_outline_id' => null,
                    'topic_context' => null,
                    'context_parameters' => [],
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Chat context updated successfully.',
                'new_context' => $session->topic_context,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update chat context', [
                'course_id' => $course->id,
                'session_id' => $request->session_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to update context.',
            ], 500);
        }
    }

    /**
     * Get available topics for a course
     */
    public function getCourseTopics(Course $course): JsonResponse
    {
        try {
            $student = auth()->user();

            // Verify course access
            if (!$student->courses()->where('courses.id', $course->id)->exists()) {
                return response()->json([
                    'error' => 'Course not found or access denied.',
                ], 404);
            }

            $topics = $course->modules()
                ->with(['topics' => function ($query) {
                    $query->orderBy('order');
                }])
                ->orderBy('order')
                ->get()
                ->flatMap(function ($module) {
                    return $module->topics->map(function ($topic) use ($module) {
                        return [
                            'id' => $topic->id,
                            'title' => $topic->title,
                            'order' => $topic->order,
                            'module_title' => $module->title,
                            'module_id' => $module->id,
                            'full_title' => "{$module->title} → {$topic->title}",
                            'description' => $topic->description,
                        ];
                    });
                });

            return response()->json([
                'success' => true,
                'topics' => $topics,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get course topics', [
                'course_id' => $course->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to load topics.',
            ], 500);
        }
    }

    /**
     * Get chat session messages
     */
    public function getMessages(Course $course, ChatSession $chatSession): JsonResponse
    {
        try {
            $student = auth()->user();

            // Verify session belongs to user and course
            if ($chatSession->user_id !== $student->id || $chatSession->course_id !== $course->id) {
                return response()->json([
                    'success' => false,
                    'error' => 'Chat session not found.',
                ], 404);
            }

            $messages = $chatSession->messages()
                ->orderBy('created_at', 'asc')
                ->limit(50)
                ->get();

            return response()->json([
                'success' => true,
                'messages' => $messages,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get chat messages', [
                'course_id' => $course->id,
                'session_id' => $chatSession->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to load messages.',
            ], 500);
        }
    }

    /**
     * Close course chat session
     */
    public function closeSession(Course $course, ChatSession $chatSession): JsonResponse
    {
        try {
            $student = auth()->user();

            // Verify session belongs to user and course
            if ($chatSession->user_id !== $student->id || $chatSession->course_id !== $course->id) {
                return response()->json([
                    'success' => false,
                    'error' => 'Chat session not found.',
                ], 404);
            }

            $this->chatService->closeSession($chatSession);

            return response()->json([
                'success' => true,
                'message' => 'Chat session closed.',
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to close chat session', [
                'course_id' => $course->id,
                'session_id' => $chatSession->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to close session.',
            ], 500);
        }
    }
}
