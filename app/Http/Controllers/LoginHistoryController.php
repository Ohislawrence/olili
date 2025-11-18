<?php
// app/Http/Controllers/LoginHistoryController.php

namespace App\Http\Controllers;

use App\Services\LoginTrackerService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LoginHistoryController extends Controller
{
    public function __construct(
        private LoginTrackerService $loginTracker
    ) {}

    public function index(Request $request)
    {

        $user = $request->user();

        $loginHistory = $user->loginHistories()
            ->latest('login_at')
            ->paginate(20);

        $stats = $this->loginTracker->getUserLoginStats($user);
        $suspiciousActivities = $this->loginTracker->getSuspiciousActivity($user);

        return Inertia::render('LoginHistory', [
            'loginHistory' => $loginHistory,
            'stats' => $stats,
            'suspiciousActivities' => $suspiciousActivities,
        ]);
    }
}
