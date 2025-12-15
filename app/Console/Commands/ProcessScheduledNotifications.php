<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationService;

class ProcessScheduledNotifications extends Command
{
    protected $signature = 'notifications:process-scheduled';
    protected $description = 'Process scheduled notifications';

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    public function handle()
    {
        $this->info('Processing scheduled notifications...');

        try {
            $this->notificationService->processScheduledNotifications();
            $this->info('Scheduled notifications processed successfully.');
        } catch (\Exception $e) {
            $this->error('Error processing notifications: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
