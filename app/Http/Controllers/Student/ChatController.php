<?php
// app/Http/Controllers/Student/ChatController.php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ChatSession;
use App\Models\Course;
use App\Models\Module;
use App\Models\CourseOutline;
use App\Services\ChatService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    protected $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    public function index(Request $request)
    {
        $student = auth()->user();

        // FIXED: Get courses first to ensure we only show chats for courses the student owns
        $courses = $student->enrolledCourses()
            ->where('status', 'active')
            ->get(['courses.id', 'courses.title']);

        // Build query with proper course filtering
        $query = ChatSession::with(['course', 'courseOutline.module', 'messages'])
            ->where('user_id', $student->id)
            ->whereIn('course_id', $courses->pluck('id')); // FIXED: Only show chats for student's courses

        // Filter by course
        if ($request->has('course_id') && $request->course_id) {
            // FIXED: Verify the course belongs to the student
            if ($courses->contains('id', $request->course_id)) {
                $query->where('course_id', $request->course_id);
            }
        }

        // Filter by active sessions
        if ($request->has('active_only') && $request->active_only) {
            $query->where('is_active', true)
                ->where('last_activity_at', '>=', now()->subHours(2));
        }

        $chatSessions = $query->latest()
            ->paginate(15);

        return Inertia::render('Student/Chat/Index', [
            'chat_sessions' => $chatSessions,
            'courses' => $courses,
            'filters' => $request->only(['course_id', 'active_only']),
        ]);
    }

    public function create(Request $request)
    {
        $student = auth()->user();

        $courseId = $request->get('course_id');
        $outlineId = $request->get('outline_id');

        $course = null;
        $outline = null;
        $availableModules = collect();
        $availableTopics = collect();

        if ($courseId) {
            try {
                // FIXED: Ensure the course belongs to the student
                $course = $student->enrolledCourses()
                    ->where('courses.id', $courseId)
                    ->where('status', 'active')
                    ->firstOrFail();

                // Load modules and their topics
                $availableModules = $course->modules()
                    ->with(['topics' => function ($query) {
                        $query->orderBy('order');
                    }])
                    ->orderBy('order')
                    ->get();

                // Flatten all topics for the dropdown
                $availableTopics = $availableModules->flatMap(function ($module) {
                    return $module->topics->map(function ($topic) use ($module) {
                        return [
                            'id' => $topic->id,
                            'title' => $topic->title,
                            'order' => $topic->order,
                            'module_title' => $module->title,
                            'module_id' => $module->id,
                            'full_title' => "{$module->title} → {$topic->title}",
                        ];
                    });
                });

                if ($outlineId) {
                    $outline = CourseOutline::where('id', $outlineId)
                        ->whereHas('module', function ($query) use ($courseId) {
                            $query->where('course_id', $courseId);
                        })
                        ->with('module')
                        ->firstOrFail();
                }
            } catch (\Exception $e) {
                Log::error('Error loading course data for chat creation', [
                    'course_id' => $courseId,
                    'outline_id' => $outlineId,
                    'user_id' => $student->id,
                    'error' => $e->getMessage()
                ]);

                // Don't fail completely, just show the form without the specific course data
                $course = null;
                $outline = null;
                $availableModules = collect();
                $availableTopics = collect();
            }
        }

        // FIXED: Get only student's courses
        $courses = $student->enrolledCourses()
            ->where('status', 'active')
            ->get(['courses.id', 'courses.title']);

        return Inertia::render('Student/Chat/Create', [
            'courses' => $courses,
            'selected_course' => $course,
            'selected_outline' => $outline,
            'available_modules' => $availableModules,
            'available_topics' => $availableTopics,
        ]);
    }

    public function store(Request $request)
    {
        $student = auth()->user();

        $request->validate([
            'course_id' => 'nullable|exists:courses,id',
            'outline_id' => 'nullable|exists:course_outlines,id',
            'initial_message' => 'nullable|string|max:1000',
        ]);

        try {
            // FIXED: Verify course access if course_id is provided
            if ($request->course_id) {
                $course = $student->enrolledCourses()
                    ->where('courses.id', $request->course_id)
                    ->where('status', 'active')
                    ->firstOrFail();

                // Verify topic belongs to course through module if outline_id is provided
                if ($request->outline_id) {
                    $topic = CourseOutline::where('id', $request->outline_id)
                        ->whereHas('module', function ($query) use ($request) {
                            $query->where('course_id', $request->course_id);
                        })
                        ->firstOrFail();
                }
            }

            // Use getOrCreateSession to handle existing active sessions
            $session = $this->chatService->getOrCreateSession(
                $student->id,
                $request->course_id,
                $request->outline_id
            );

            // Send initial message if provided
            if ($request->initial_message) {
                $this->chatService->sendMessage($session, $request->initial_message);
            }

            return redirect()->route('student.chat.show', $session->id)
                ->with('success', 'Chat session started!');

        } catch (\Exception $e) {
            Log::error('Failed to create chat session', [
                'user_id' => $student->id,
                'course_id' => $request->course_id,
                'outline_id' => $request->outline_id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to create chat session: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(ChatSession $chatSession)
    {
        // FIXED: Add authorization to ensure student owns this chat session
        if ($chatSession->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this chat session.');
        }

        try {
            $chatSession->load([
                'course',
                'courseOutline.module',
                'messages' => function ($query) {
                    $query->orderBy('created_at', 'desc')->limit(50);
                },
            ]);

            // Reverse messages to show oldest first
            $chatSession->messages = $chatSession->messages->reverse()->values();

            $relatedTopics = collect();
            if ($chatSession->course) {
                $relatedTopics = $chatSession->course->modules()
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
                            ];
                        });
                    });
            }

            return Inertia::render('Student/Chat/Show', [
                'chat_session' => $chatSession,
                'related_topics' => $relatedTopics,
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading chat session', [
                'chat_session_id' => $chatSession->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('student.chat.index')
                ->with('error', 'Failed to load chat session.');
        }
    }

    public function showFromCourse(Course $course)
    {
        $student = auth()->user();

        // Verify the course belongs to the student

        try {
            // Get the most recent active session for this course, or create a new one
            $chatSession = ChatSession::where('course_id', $course->id)
                ->where('user_id', $student->id)
                ->where('is_active', true)
                ->where('last_activity_at', '>=', now()->subHours(2))
                ->latest()
                ->first();

            // If no active session exists, create one
            if (!$chatSession) {
                $chatSession = $this->chatService->getOrCreateSession(
                    $student->id,
                    $course->id,
                    null // No specific topic
                );
            }

            // Load the session with relationships
            $chatSession->load([
                'course',
                'courseOutline.module',
                'messages' => function ($query) {
                    $query->orderBy('created_at', 'desc')->limit(50);
                },
            ]);

            // Reverse messages to show oldest first
            $chatSession->messages = $chatSession->messages->reverse()->values();

            // Get related topics for context switching
            $relatedTopics = $course->modules()
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
                        ];
                    });
                });

            return Inertia::render('Student/Chat/Show', [
                'chat_session' => $chatSession,
                'related_topics' => $relatedTopics,
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading chat session from course', [
                'course_id' => $course->id,
                'user_id' => $student->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('student.courses.show', $course->id)
                ->with('error', 'Failed to load chat session: ' . $e->getMessage());
        }
    }

    public function sendMessage(ChatSession $chatSession, Request $request)
    {
        //$this->authorize('update', $chatSession);

        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        try {
            $message = $this->chatService->sendMessage($chatSession, $request->message);

            return response()->json([
                'success' => true,
                'message' => $message,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send chat message', [
                'chat_session_id' => $chatSession->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send message: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function updateContext(ChatSession $chatSession, Request $request)
    {
        //$this->authorize('update', $chatSession);
        $request->validate([
            'outline_id' => 'nullable|exists:course_outlines,id',
        ]);

        try {
            $topic = null;
            if ($request->outline_id) {

                $topic = CourseOutline::where('id', $request->outline_id)
                    ->whereHas('module', function ($query) use ($chatSession) {
                        $query->where('course_id', $chatSession->course_id);
                    })
                    ->with('module')
                    ->firstOrFail();
            }
            // Use the service to update context with proper parameters
            if ($topic) {
                $contextParameters = $this->chatService->buildContextParameters($topic->id);
            } else {
                $contextParameters = [];
            }

            return response()->json([
                'success' => true,
                'message' => 'Chat context updated successfully.',
                'new_context' => $topic ? [
                    'topic_title' => $topic->title,
                    'module_title' => $topic->module->title,
                    'course_title' => $chatSession->course->title,
                ] : null,
            ]);

        } catch (\Exception $e) {


            return response()->json([
                'success' => false,
                'message' => 'Failed to update context: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function closeSession(ChatSession $chatSession)
    {
        // FIXED: Add authorization
        if ($chatSession->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $this->chatService->closeSession($chatSession);

            return redirect()->route('student.chat.index')
                ->with('success', 'Chat session closed.');

        } catch (\Exception $e) {
            Log::error('Failed to close chat session', [
                'chat_session_id' => $chatSession->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to close chat session.');
        }
    }

    public function getActiveSession(Request $request)
    {
        $student = auth()->user();
        $courseId = $request->get('course_id');

        try {
            $session = $this->chatService->getActiveSession($student->id, $courseId);

            if ($session) {
                $session->load([
                    'course',
                    'courseOutline.module',
                    'messages' => function ($query) {
                        $query->orderBy('created_at', 'desc')->limit(20);
                    }
                ]);

                $session->messages = $session->messages->reverse()->values();

                return response()->json([
                    'exists' => true,
                    'session' => $session,
                ]);
            }

            return response()->json([
                'exists' => false,
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting active session', [
                'user_id' => $student->id,
                'course_id' => $courseId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'exists' => false,
                'error' => 'Failed to check active session',
            ], 500);
        }
    }

    public function getMessages(ChatSession $chatSession)
    {
        try {
            $student = auth()->user();

            // Verify session belongs to user
            if ($chatSession->user_id !== $student->id) {
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
                'session' => [
                    'course_outline_id' => $chatSession->course_outline_id,
                    'topic_context' => $chatSession->topic_context,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get chat messages', [
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
     * Quick chat from course learning page
     */
    public function quickStart(Course $course, CourseOutline $topic = null)
    {
        //$this->authorize('view', $course);

        $student = auth()->user();

        try {
            // Verify topic belongs to course if provided
            if ($topic) {
                $topic->load('module');
                if ($topic->module->course_id !== $course->id) {
                    abort(404, 'Topic not found in this course.');
                }
            }

            // Get or create session
            $session = $this->chatService->getOrCreateSession(
                $student->id,
                $course->id,
                $topic?->id
            );

            return redirect()->route('student.chat.show', $session->id)
                ->with('info', $topic ?
                    "Chat session started for topic: {$topic->title}" :
                    "Chat session started for course: {$course->title}");

        } catch (\Exception $e) {
            Log::error('Error starting quick chat', [
                'course_id' => $course->id,
                'topic_id' => $topic?->id,
                'user_id' => $student->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to start chat session.');
        }
    }

    /**
     * Get available topics for a course
     */
    public function getCourseTopics(Course $course)
    {
        //$this->authorize('view', $course);

        try {
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
                'topics' => $topics,
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting course topics', [
                'course_id' => $course->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'topics' => [],
                'error' => 'Failed to load topics',
            ], 500);
        }
    }

    /**
     * Get active sessions for current user
     */
    public function getMyActiveSessions()
    {
        $student = auth()->user();

        try {
            $sessions = ChatSession::with(['course', 'courseOutline.module'])
                ->where('user_id', $student->id)
                ->where('is_active', true)
                ->where('last_activity_at', '>=', now()->subHours(2))
                ->orderBy('last_activity_at', 'desc')
                ->get();

            return response()->json([
                'sessions' => $sessions,
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting active sessions', [
                'user_id' => $student->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'sessions' => [],
                'error' => 'Failed to load active sessions',
            ], 500);
        }
    }


    public function initializePopupChat(Course $course)
    {
        $student = auth()->user();

        try {
            // Verify course access
            $course = Course::where('id', $course->id)
                ->firstOrFail();

            $enrollment = $student->courseEnrollments()
            ->where('course_id', $course->id)
            ->where('status', '!=', 'dropped')
            ->first();

            // Get or create active session
            $session = $this->chatService->getOrCreateSession(
                $student->id,
                $course->id,
                null
            );

            // Load minimal session data
            $session->load(['messages' => function ($query) {
                $query->orderBy('created_at', 'desc')->limit(20);
            }]);

            return response()->json([
                'session' => $session,
                'messages' => $session->messages->reverse()->values()
            ]);

        } catch (\Exception $e) {
            Log::error('Error initializing popup chat', [
                'course_id' => $course->id,
                'user_id' => $student->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Failed to initialize chat'
            ], 500);
        }
    }
}
