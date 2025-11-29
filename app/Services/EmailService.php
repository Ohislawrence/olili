<?php
// app/Services/EmailService.php
namespace App\Services;

use App\Models\User;
use App\Notifications\AdminBroadcastEmail;
use App\Notifications\UserDirectEmail;
use Illuminate\Support\Facades\Notification;

class EmailService
{
    public function sendToRole($role, $subject, $message, $fromEmail = null, $fromName = null)
    {
        $users = User::role($role)->where('is_active', true)->get();
        
        Notification::send($users, new AdminBroadcastEmail(
            $subject, 
            $message, 
            $fromEmail, 
            $fromName
        ));

        return $users->count();
    }

    public function sendToUser($userId, $subject, $message, $fromEmail = null, $fromName = null)
    {
        $user = User::findOrFail($userId);
        
        $user->notify(new UserDirectEmail(
            $subject, 
            $message, 
            $fromEmail, 
            $fromName
        ));

        return $user;
    }

    public function sendToMultipleUsers($userIds, $subject, $message, $fromEmail = null, $fromName = null)
    {
        $users = User::whereIn('id', $userIds)->where('is_active', true)->get();
        
        Notification::send($users, new UserDirectEmail(
            $subject, 
            $message, 
            $fromEmail, 
            $fromName
        ));

        return $users->count();
    }

    public function getRoles()
    {
        return ['admin', 'student', 'tutor', 'organization'];
    }
}