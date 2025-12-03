<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use App\Models\CourseCreationReminder;
use App\Models\Quiz;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TestingController extends Controller
{
    public function test()
    {
        //$subscription = auth()->user()->current_subscription;
       // dd($subscription->hasFeature('unlimited_course_creation'));
        $user = auth()->user();
        $courses = $user->courses()->active()->get();

        return Inertia::render('Frontpages/Community/Create', [
            'courses' => $courses,
        ]);
    }
}
