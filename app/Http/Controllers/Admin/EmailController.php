<?php
// app/Http/Controllers/Admin/EmailController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommunityPost;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Inertia\Inertia;

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
        ]);
    }

    public function send(Request $request)
    {
        $request->validate([
            'type' => 'required|in:role,user,multiple',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'from_email' => 'nullable|email',
            'from_name' => 'nullable|string|max:255',
        ]);

        try {
            $count = 0;

            switch ($request->type) {
                case 'role':
                    $request->validate(['role' => 'required|in:' . implode(',', $this->emailService->getRoles())]);
                    $count = $this->emailService->sendToRole(
                        $request->role,
                        $request->subject,
                        $request->message,
                        $request->from_email,
                        $request->from_name
                    );
                    break;

                case 'user':
                    $request->validate(['user_id' => 'required|exists:users,id']);
                    $this->emailService->sendToUser(
                        $request->user_id,
                        $request->subject,
                        $request->message,
                        $request->from_email,
                        $request->from_name
                    );
                    $count = 1;
                    break;

                case 'multiple':
                    $request->validate(['user_ids' => 'required|array', 'user_ids.*' => 'exists:users,id']);
                    $count = $this->emailService->sendToMultipleUsers(
                        $request->user_ids,
                        $request->subject,
                        $request->message,
                        $request->from_email,
                        $request->from_name
                    );
                    break;
            }

            return redirect()->back()->with('success', "Email sent successfully to {$count} recipients.");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }


    public function indexOne()
    {


        return Inertia::render('Admin/Community/Index');
    }
}
