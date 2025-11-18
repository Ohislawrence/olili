<?php
// app/Http/Middleware/TrackActiveSession.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\LoginTrackerService;

class TrackActiveSession
{
    protected $loginTracker;

    public function __construct(LoginTrackerService $loginTracker)
    {
        $this->loginTracker = $loginTracker;
    }

    public function handle(Request $request, Closure $next)
    {
        // Update last activity timestamp for the user
        if ($request->user()) {
            $request->user()->update([
                'last_activity_at' => now(),
            ]);
        }

        return $next($request);
    }
}
