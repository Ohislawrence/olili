<?php
// app/Http/Controllers/Student/NotificationController.php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Inertia\Inertia;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $student = auth()->user();

        $notifications = $student->notifications()
            ->latest()
            ->paginate(20)
            ->through(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'data' => $notification->data,
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at->toISOString(),
                    'updated_at' => $notification->updated_at->toISOString(),
                ];
            });

        return Inertia::render('Student/Notifications/Index', [
            'notifications' => $notifications,
            'filters' => $request->only(['search']),
        ]);
    }

    public function markAsRead(DatabaseNotification $notification)
    {
        //$this->authorize('update', $notification);

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read.',
        ]);
    }

    public function markAllRead()
    {
        $student = auth()->user();

        $student->unreadNotifications->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read.',
        ]);
    }

    public function destroy(DatabaseNotification $notification)
    {
        //$this->authorize('delete', $notification);

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted successfully.',
        ]);
    }

    public function clearAll()
    {
        $student = auth()->user();

        $student->notifications()->delete();

        return response()->json([
            'success' => true,
            'message' => 'All notifications cleared successfully.',
        ]);
    }

    public function getUnreadCount()
    {
        $student = auth()->user();

        return response()->json([
            'count' => $student->unreadNotifications->count(),
        ]);
    }
}
