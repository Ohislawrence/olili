<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use App\Models\CourseOutline;
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
        $student = auth()->user();
        $course = $student->courses()
                ->where('courses.id', 24)
                ->where('status', 'active')
                ->firstOrFail();
       $availableOutlines = $course->modules()
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
                            'description' => $topic->description,
                            'module_title' => $module->title,
                            'full_title' => "{$module->title} → {$topic->title}",
                        ];
                    });
                });
        dd($availableOutlines );

    }
}
