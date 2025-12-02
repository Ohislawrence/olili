<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class OnboardingController extends Controller
{
    /**
     * Complete onboarding
     */
    public function complete(Request $request)
    {
        $request->validate([
            'restart' => 'sometimes|boolean',
        ]);
        
        $user = $request->user();
        
        if ($request->boolean('restart')) {
            $user->resetOnboarding();
            
            // Return Inertia response with a flash message
            return redirect()->back()->with([
                'success' => 'Onboarding restarted! The tour will begin on your next page refresh.',
                'restart_onboarding' => true, // Flag to trigger restart
            ]);
        }
        
        $user->completeOnboarding();
        
        return redirect()->back()->with('success', 'Onboarding completed successfully!');
    }

    /**
     * Skip onboarding
     */
    public function skip(Request $request)
    {
        $user = $request->user();
        $user->skipOnboarding();
        
        return redirect()->back()->with('info', 'Onboarding skipped. You can restart it anytime.');
    }

    /**
     * Restart onboarding
     */
    public function restart(Request $request)
    {
        $user = $request->user();
        $user->resetOnboarding();
        
        // Store a session flag to show onboarding
        session(['force_show_onboarding' => true]);
        
        return redirect()->back()->with([
            'success' => 'Onboarding tour restarted!',
            'show_onboarding' => true, // Pass to Inertia props
        ]);
    }

    /**
     * Get onboarding status
     */
    public function status(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'status' => $user->onboarding_status,
            'completed_at' => $user->onboarding_completed_at,
            'should_see' => $user->shouldSeeOnboarding(),
        ]);
    }
}