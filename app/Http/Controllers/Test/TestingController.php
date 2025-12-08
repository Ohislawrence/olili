<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use App\Models\CourseCreationReminder;
use App\Models\Quiz;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Notifications\CourseCreationReminder as NotificationsCourseCreationReminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TestingController extends Controller
{
    public function test()
    {
        $user = User::where('id', 2)->first();

    // Make sure we have a User object
    if (!$user) {
        dd('User not found');
    }

    // PASS USER OBJECT
    $notification = new \App\Notifications\CourseCreationReminder($user, 1);

    // Get the mail message
    $mailMessage = $notification->toMail($user);

    // Render the view to check
    $view = $mailMessage->view;
    $data = $mailMessage->viewData;

    dd([
        'user_in_data' => isset($data['user']),
        'user_class' => get_class($data['user'] ?? null),
        'view_data' => $data,
        'rendered' => $mailMessage->render(),
    ]);
    }
}
