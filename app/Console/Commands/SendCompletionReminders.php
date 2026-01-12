<?php

namespace App\Console\Commands;

use App\Services\ReminderService;
use Illuminate\Console\Command;

class SendCompletionReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:completion';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send course completion reminders';

    /**
     * Execute the console command.
     */
    public function handle(ReminderService $reminderService)
    {
        $this->info('Starting completion reminder sending process...');

        try {
            $reminderService->sendCompletionReminders();
            $this->info('Completion reminders sent successfully.');
        } catch (\Exception $e) {
            $this->error('Error sending completion reminders: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
