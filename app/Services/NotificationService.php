<?php

namespace App\Services;

use App\Models\SystemNotification;
use App\Models\NotificationLog;
use App\Models\User;
use App\Notifications\SystemNotification as SystemNotificationNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification as LaravelNotification;
use Carbon\Carbon;

class NotificationService
{
    public function createNotification(array $data)
    {
        return DB::transaction(function () use ($data) {
            $notification = SystemNotification::create([
                'title' => $data['title'],
                'message' => $data['message'],
                'type' => $data['type'] ?? 'info',
                'send_email' => $data['send_email'] ?? false,
                'scheduled_at' => isset($data['scheduled_at']) ? Carbon::parse($data['scheduled_at']) : null,
                'user_selection' => $data['user_selection'],
                'user_ids' => $data['user_ids'] ?? null,
                'role_names' => $data['role_names'] ?? null,
                'status' => isset($data['scheduled_at']) ? SystemNotification::STATUS_SCHEDULED : SystemNotification::STATUS_DRAFT,
                'sender_id' => auth()->id(),
                'data' => $data['data'] ?? null,
            ]);

            return $notification;
        });
    }

    public function sendNotification(SystemNotification $notification)
    {
        DB::transaction(function () use ($notification) {
            $notification->update(['status' => SystemNotification::STATUS_SENDING]);

            $users = $this->getRecipients($notification);

            foreach ($users as $user) {
                NotificationLog::create([
                    'notification_id' => $notification->id,
                    'user_id' => $user->id,
                    'channel' => $notification->send_email ? NotificationLog::CHANNEL_BOTH : NotificationLog::CHANNEL_DATABASE,
                    'status' => NotificationLog::STATUS_PENDING,
                ]);
            }

            // Send notifications
            if ($notification->send_email) {
                LaravelNotification::send($users, new SystemNotificationNotification(
                    $notification->title,
                    $notification->message,
                    $notification->type
                ));
            } else {
                LaravelNotification::send($users, new SystemNotificationNotification(
                    $notification->title,
                    $notification->message,
                    $notification->type
                ));
            }

            $notification->update([
                'status' => SystemNotification::STATUS_SENT,
                'sent_at' => now(),
            ]);

            // Update logs
            NotificationLog::where('notification_id', $notification->id)
                ->update([
                    'status' => NotificationLog::STATUS_SENT,
                    'sent_at' => now(),
                ]);
        });
    }

    private function getRecipients(SystemNotification $notification)
    {
        $query = User::query();

        switch ($notification->user_selection) {
            case SystemNotification::SELECT_ALL:
                // All users
                break;

            case SystemNotification::SELECT_ROLES:
                $query->role($notification->role_names ?? []);
                break;

            case SystemNotification::SELECT_SPECIFIC:
                $query->whereIn('id', $notification->user_ids ?? []);
                break;
        }

        return $query->get();
    }

    public function processScheduledNotifications()
    {
        $notifications = SystemNotification::pending()->get();

        foreach ($notifications as $notification) {
            $this->sendNotification($notification);
        }
    }

    public function getNotificationStats(SystemNotification $notification)
    {
        $logs = $notification->notificationLogs;

        return [
            'total' => $logs->count(),
            'sent' => $logs->where('status', NotificationLog::STATUS_SENT)->count(),
            'failed' => $logs->where('status', NotificationLog::STATUS_FAILED)->count(),
            'pending' => $logs->where('status', NotificationLog::STATUS_PENDING)->count(),
            'email_sent' => $logs->where('channel', '!=', NotificationLog::CHANNEL_DATABASE)->count(),
        ];
    }

    public function resendFailed(SystemNotification $notification)
    {
        $failedLogs = $notification->notificationLogs()
            ->where('status', NotificationLog::STATUS_FAILED)
            ->get();

        foreach ($failedLogs as $log) {
            try {
                $user = $log->user;

                if ($notification->send_email) {
                    $user->notify(new SystemNotificationNotification(
                        $notification->title,
                        $notification->message,
                        $notification->type
                    ));
                } else {
                    $user->notify(new SystemNotificationNotification(
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
    }
}
