<?php
// app/Console/Commands/CleanupExpiredCourseShares.php

namespace App\Console\Commands;

use App\Models\CourseShare;
use Illuminate\Console\Command;

class CleanupExpiredCourseShares extends Command
{
    protected $signature = 'course-shares:cleanup';
    protected $description = 'Clean up expired course shares';

    public function handle()
    {
        $expiredShares = CourseShare::where('status', 'pending')
            ->where('expires_at', '<', now())
            ->update(['status' => 'expired']);

        $this->info("Marked {$expiredShares} expired course shares.");

        return Command::SUCCESS;
    }
}
