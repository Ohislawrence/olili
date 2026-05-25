<?php

namespace App\Console\Commands;

use App\Services\ReminderService;
use Illuminate\Console\Command;

class SendAllReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send all course reminders';

    /**
     * Execute the console command.
     */
    public function handle(ReminderService $reminderService)
    {
        $this->info('Starting all reminder sending process...');

        try {
            $reminderService->sendAllReminders();
            $this->info('All reminders sent successfully.');
        } catch (\Exception $e) {
            $this->error('Error sending reminders: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
