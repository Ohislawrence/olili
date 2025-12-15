<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\NotificationLog;
use App\Models\SystemNotification;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $query = SystemNotification::with(['sender'])
            ->latest();

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                ->orWhere('message', 'like', "%{$request->search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        $notifications = $query->paginate(20);

        // Calculate stats
        $stats = [
            'total' => SystemNotification::count(),
            'draft' => SystemNotification::where('status', SystemNotification::STATUS_DRAFT)->count(),
            'scheduled' => SystemNotification::where('status', SystemNotification::STATUS_SCHEDULED)->count(),
            'sending' => SystemNotification::where('status', SystemNotification::STATUS_SENDING)->count(),
            'sent' => SystemNotification::where('status', SystemNotification::STATUS_SENT)->count(),
            'failed' => SystemNotification::where('status', SystemNotification::STATUS_FAILED)->count(),
        ];

        return Inertia::render('Admin/Notifications/Index', [
            'notifications' => $notifications,
            'filters' => $request->only(['search', 'status', 'type']),
            'stats' => $stats,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Notifications/Create', [
            'roles' => Role::all()->pluck('name'),
            'users' => User::select('id', 'name', 'email')->get(),
            'notificationtypes' => [
                ['value' => 'info', 'label' => 'Information'],
                ['value' => 'success', 'label' => 'Success'],
                ['value' => 'warning', 'label' => 'Warning'],
                ['value' => 'alert', 'label' => 'Alert'],
                ['value' => 'system', 'label' => 'System'],
            ],
            'userselectionoptions' => [
                ['value' => 'all', 'label' => 'All Users'],
                ['value' => 'roles', 'label' => 'Specific Roles'],
                ['value' => 'specific', 'label' => 'Specific Users'],
            ],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:info,success,warning,alert,system',
            'send_email' => 'boolean',
            'scheduled_at' => 'nullable|date|after:now',
            'user_selection' => 'required|in:all,roles,specific',
            'user_ids' => 'required_if:user_selection,specific|array',
            'user_ids.*' => 'exists:users,id',
            'role_names' => 'required_if:user_selection,roles|array',
            'role_names.*' => 'exists:roles,name',
        ]);

        $notification = $this->notificationService->createNotification([
            'title' => $request->title,
            'message' => $request->message,
            'type' => $request->type,
            'send_email' => $request->send_email ?? false,
            'scheduled_at' => $request->scheduled_at,
            'user_selection' => $request->user_selection,
            'user_ids' => $request->user_ids,
            'role_names' => $request->role_names,
        ]);

        // Send immediately if not scheduled
        if (!$request->scheduled_at) {
            $this->notificationService->sendNotification($notification);
        }

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notification created successfully.');
    }

    public function show(SystemNotification $notification)
    {
        $notification->load(['sender', 'notificationLogs.user']);

        $stats = $this->notificationService->getNotificationStats($notification);

        return Inertia::render('Admin/Notifications/Show', [
            'notification' => $notification,
            'stats' => $stats,
            'logs' => $notification->notificationLogs()->with('user')->paginate(20),
        ]);
    }

    public function edit(SystemNotification $notification)
    {
        if ($notification->status !== SystemNotification::STATUS_DRAFT) {
            return redirect()->back()
                ->with('error', 'Only draft notifications can be edited.');
        }

        return Inertia::render('Admin/Notifications/Edit', [
            'notification' => $notification,
            'roles' => Role::all()->pluck('name'),
            'users' => User::select('id', 'name', 'email')->get(),
            'notification_types' => [
                ['value' => 'info', 'label' => 'Information'],
                ['value' => 'success', 'label' => 'Success'],
                ['value' => 'warning', 'label' => 'Warning'],
                ['value' => 'alert', 'label' => 'Alert'],
                ['value' => 'system', 'label' => 'System'],
            ],
            'user_selection_options' => [
                ['value' => 'all', 'label' => 'All Users'],
                ['value' => 'roles', 'label' => 'Specific Roles'],
                ['value' => 'specific', 'label' => 'Specific Users'],
            ],
        ]);
    }

    public function update(Request $request, SystemNotification $notification)
    {
        if ($notification->status !== SystemNotification::STATUS_DRAFT) {
            return redirect()->back()
                ->with('error', 'Only draft notifications can be edited.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:info,success,warning,alert,system',
            'send_email' => 'boolean',
            'scheduled_at' => 'nullable|date|after:now',
            'user_selection' => 'required|in:all,roles,specific',
            'user_ids' => 'required_if:user_selection,specific|array',
            'user_ids.*' => 'exists:users,id',
            'role_names' => 'required_if:user_selection,roles|array',
            'role_names.*' => 'exists:roles,name',
        ]);

        $notification->update([
            'title' => $request->title,
            'message' => $request->message,
            'type' => $request->type,
            'send_email' => $request->send_email ?? false,
            'scheduled_at' => $request->scheduled_at,
            'user_selection' => $request->user_selection,
            'user_ids' => $request->user_ids,
            'role_names' => $request->role_names,
            'status' => $request->scheduled_at ? SystemNotification::STATUS_SCHEDULED : SystemNotification::STATUS_DRAFT,
        ]);

        return redirect()->route('admin.notifications.show', $notification)
            ->with('success', 'Notification updated successfully.');
    }

    public function sendNow(SystemNotification $notification)
    {
        if ($notification->status === SystemNotification::STATUS_SENT) {
            return redirect()->back()
                ->with('error', 'Notification has already been sent.');
        }

        $this->notificationService->sendNotification($notification);

        return redirect()->back()
            ->with('success', 'Notification sent successfully.');
    }

    public function destroy(SystemNotification $notification)
    {
        if ($notification->status === SystemNotification::STATUS_SENT) {
            return redirect()->back()
                ->with('error', 'Sent notifications cannot be deleted.');
        }

        $notification->delete();

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notification deleted successfully.');
    }

    public function preview(SystemNotification $notification)
    {
        return Inertia::render('Admin/Notifications/Preview', [
            'notification' => $notification,
        ]);
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:send_now,delete',
            'notifications' => 'required|array',
            'notifications.*' => 'exists:system_notifications,id',
        ]);

        $notifications = SystemNotification::whereIn('id', $request->notifications)->get();

        switch ($request->action) {
            case 'send_now':
                foreach ($notifications as $notification) {
                    if ($notification->status !== SystemNotification::STATUS_SENT) {
                        $this->notificationService->sendNotification($notification);
                    }
                }
                $message = 'Selected notifications sent successfully.';
                break;

            case 'delete':
                $notifications->each(function ($notification) {
                    if ($notification->status !== SystemNotification::STATUS_SENT) {
                        $notification->delete();
                    }
                });
                $message = 'Selected notifications deleted successfully.';
                break;
        }

        return redirect()->back()->with('success', $message);
    }

    public function resendFailed(SystemNotification $notification)
    {
        if ($notification->status !== SystemNotification::STATUS_SENT) {
            return redirect()->back()
                ->with('error', 'Only sent notifications can have failed items resent.');
        }

        $failedLogs = $notification->notificationLogs()
            ->where('status', NotificationLog::STATUS_FAILED)
            ->get();

        if ($failedLogs->isEmpty()) {
            return redirect()->back()
                ->with('info', 'No failed deliveries to resend.');
        }

        // Resend each failed notification
        foreach ($failedLogs as $log) {
            try {
                $user = $log->user;

                if ($notification->send_email) {
                    $user->notify(new \App\Notifications\SystemNotification(
                        $notification->title,
                        $notification->message,
                        $notification->type
                    ));
                } else {
                    // Send only database notification
                    $user->notify(new \App\Notifications\SystemNotification(
                        $notification->title,
                        $notification->message,
                        $notification->type
                    ));
                }

                $log->update([
                    'status' => NotificationLog::STATUS_SENT,
                    'sent_at' => now(),
                ]);
            } catch (\Exception $e) {
                $log->update([
                    'error_message' => $e->getMessage(),
                ]);
            }
        }

        return redirect()->back()
            ->with('success', 'Failed notifications resent successfully.');
    }
}
