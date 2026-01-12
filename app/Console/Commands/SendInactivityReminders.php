<?php

namespace App\Console\Commands;

use App\Services\ReminderService;
use Illuminate\Console\Command;

class SendInactivityReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:inactivity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send inactivity reminders for students';

    /**
     * Execute the console command.
     */
    public function handle(ReminderService $reminderService)
    {
        $this->info('Starting inactivity reminder sending process...');

        try {
            $reminderService->sendInactivityReminders();
            $this->info('Inactivity reminders sent successfully.');
        } catch (\Exception $e) {
            $this->error('Error sending inactivity reminders: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
