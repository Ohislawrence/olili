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
use App\Helpers\CertificateHelper;
use App\Models\Course;

class TestingController extends Controller
{
    public function test(){
        $user = User::find(4);
        $course = Course::find(35);

        $details = CertificateHelper::getEligibilityDetails($user, $course);

        dd($details);
    }
}
