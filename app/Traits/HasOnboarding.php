<?php
namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait HasOnboarding
{
    /**
     * Complete onboarding for user
     */
    public function completeOnboarding()
    {
        $this->forceFill([
            'onboarding_completed_at' => now(),
            'onboarding_seen_at' => now(),
        ])->save();
    }

    /**
     * Skip onboarding
     */
    public function skipOnboarding()
    {
        $this->forceFill([
            'onboarding_skipped_at' => now(),
            'onboarding_seen_at' => now(),
        ])->save();
    }

    /**
     * Reset onboarding to show again
     */
    public function resetOnboarding()
    {
        $this->forceFill([
            'onboarding_completed_at' => null,
            'onboarding_skipped_at' => null,
            'onboarding_seen_at' => null,
        ])->save();
    }

    /**
     * Check if user should see onboarding
     */
    public function shouldSeeOnboarding()
    {
        // Show if never completed or skipped
        return is_null($this->onboarding_completed_at) && 
               is_null($this->onboarding_skipped_at) &&
               is_null($this->onboarding_seen_at);
    }

    /**
     * Get onboarding status
     */
    public function getOnboardingStatusAttribute()
    {
        if ($this->onboarding_completed_at) {
            return 'completed';
        }

        if ($this->onboarding_skipped_at) {
            return 'skipped';
        }

        return 'pending';
    }
}