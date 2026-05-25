<?php
// app/Http/Controllers/Admin/EmailController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommunityPost;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Course;
use App\Models\ExamPrep;

class EmailController extends Controller
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function index()
    {
        return Inertia::render('Admin/Email/Index', [
            'roles' => $this->emailService->getRoles(),
            'courses' => Course::select('id', 'title')->get(),
            'examPreps' => ExamPrep::select('id', 'name')->get(),
        ]);
    }

    public function send(Request $request)
    {

        /**
        $request->validate([
            'type' => 'required|in:role,user,multiple,segment',
            'segment_type' => 'required_if:type,segment|in:no_course_enrollment,no_course_completion,no_exam_attempt,incomplete_courses,recent_inactive,high_achievers,struggling_students',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'from_email' => 'nullable|email',
            'from_name' => 'nullable|string|max:255',
            'course_id' => 'required_if:segment_type,incomplete_courses|exists:courses,id',
            'exam_prep_id' => 'required_if:segment_type,no_exam_attempt|exists:exam_preps,id',
            'days_inactive' => 'required_if:segment_type,recent_inactive|integer|min:1|max:365',
            'min_score' => 'required_if:segment_type,high_achievers|integer|min:0|max:100',
            'max_score' => 'required_if:segment_type,struggling_students|integer|min:0|max:100',
        ]);
    */


        try {
            $count = 0;
            $query = User::query();

            switch ($request->type) {
                case 'role':
                    $request->validate(['role' => 'required|in:' . implode(',', $this->emailService->getRoles())]);
                    $query->whereHas('roles', function($q) use ($request) {
                        $q->where('name', $request->role);
                    });
                    $users = $query->get();
                    $count = $this->emailService->sendToUserCollection($users, $request->subject, $request->message, $request->from_email, $request->from_name);
                    break;

                case 'user':
                    $request->validate(['user_id' => 'required|exists:users,id']);
                    $this->emailService->sendToUser($request->user_id, $request->subject, $request->message, $request->from_email, $request->from_name);
                    $count = 1;
                    break;

                case 'multiple':
                    $request->validate(['user_ids' => 'required|array', 'user_ids.*' => 'exists:users,id']);
                    $users = User::whereIn('id', $request->user_ids)->get();
                    $count = $this->emailService->sendToUserCollection($users, $request->subject, $request->message, $request->from_email, $request->from_name);
                    break;

                case 'segment':
                    $users = $this->getSegmentUsers($request);
                    $count = $this->emailService->sendToUserCollection($users, $request->subject, $request->message, $request->from_email, $request->from_name);
                    break;
            }

            return redirect()->back()->with('message', "Email sent successfully to {$count} recipients.");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }

    private function getSegmentUsers(Request $request)
    {
        switch ($request->segment_type) {
            case 'no_course_enrollment':
                return User::whereDoesntHave('courseEnrollments')->get();

            case 'no_course_completion':
                return User::whereHas('courseEnrollments')
                    ->whereDoesntHave('courseEnrollments', function($q) {
                        $q->where('status', 'completed');
                    })->get();

            case 'no_exam_attempt':
                return User::whereDoesntHave('examPrepAttempts', function($q) use ($request) {
                    if ($request->exam_prep_id) {
                        $q->where('exam_prep_id', $request->exam_prep_id);
                    }
                })->get();

            case 'incomplete_courses':
                return User::whereHas('courseEnrollments', function($q) use ($request) {
                    $q->where('course_id', $request->course_id)
                      ->where('status', '!=', 'completed')
                      ->whereNotNull('started_at');
                })->get();

            case 'recent_inactive':
                $days = $request->days_inactive ?? 30;
                return User::where('last_activity_at', '<', now()->subDays($days))
                    ->orWhereNull('last_activity_at')
                    ->get();

            case 'high_achievers':
                return User::whereHas('examPrepAttempts', function($q) use ($request) {
                    $q->where('percentage', '>=', $request->min_score ?? 80);
                })->get();

            case 'struggling_students':
                return User::whereHas('examPrepAttempts', function($q) use ($request) {
                    $q->where('percentage', '<=', $request->max_score ?? 50);
                })->get();

            default:
                return collect([]);
        }
    }

    public function getSegmentCount(Request $request)
{
    try {
        // Base validation rules
        $rules = [
            'segment_type' => 'required|in:no_course_enrollment,no_course_completion,no_exam_attempt,incomplete_courses,recent_inactive,high_achievers,struggling_students',
        ];

        // Add conditional validation rules based on segment_type
        switch ($request->segment_type) {
            case 'incomplete_courses':
                $rules['course_id'] = 'required|exists:courses,id';
                break;

            case 'no_exam_attempt':
                $rules['exam_prep_id'] = 'nullable|exists:exam_preps,id';
                break;

            case 'recent_inactive':
                $rules['days_inactive'] = 'required|integer|min:1|max:365';
                break;

            case 'high_achievers':
                $rules['min_score'] = 'required|integer|min:0|max:100';
                break;

            case 'struggling_students':
                $rules['max_score'] = 'required|integer|min:0|max:100';
                break;

            // For no_course_enrollment and no_course_completion, no additional fields needed
        }

        $validated = $request->validate($rules);

        $count = $this->getSegmentUsers($request)->count();

        return response()->json([
            'success' => true,
            'count' => $count
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'errors' => $e->errors(),
            'message' => 'Validation failed'
        ], 422);
    } catch (\Exception $e) {
        Log::error('Failed to get segment count: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Failed to get segment count: ' . $e->getMessage()
        ], 500);
    }
}
}
