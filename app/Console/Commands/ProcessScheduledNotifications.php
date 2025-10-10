<?php

namespace App\Console\Commands;

use App\Jobs\SendNotificationJob;
use App\Models\AppNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessScheduledNotifications extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'notifications:process-scheduled';

    /**
     * The console command description.
     */
    protected $description = 'Process scheduled notifications and send them when the scheduled time arrives';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            $this->info('Processing scheduled notifications...');

            // Find notifications that are scheduled and their time has arrived
            $notifications = AppNotification::where('status', 'scheduled')
                ->where('scheduled_at', '<=', now())
                ->limit(50)
                ->get();

            if ($notifications->isEmpty()) {
                $this->info('No scheduled notifications to process.');
                return Command::SUCCESS;
            }

            $processedCount = 0;
            foreach ($notifications as $notification) {
                try {
                    dispatch(new SendNotificationJob($notification));
                    $processedCount++;
                    $this->line("Queued notification: {$notification->title}");
                } catch (\Exception $e) {
                    $this->error("Failed to queue notification {$notification->id}: " . $e->getMessage());
                    Log::error("Failed to queue notification {$notification->id}", [
                        'error' => $e->getMessage(),
                        'notification' => $notification->toArray()
                    ]);
                }
            }

            $this->info("Successfully queued {$processedCount} notification(s) for sending.");
            
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('An error occurred while processing scheduled notifications: ' . $e->getMessage());
            Log::error('Scheduled notifications command failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return Command::FAILURE;
        }
    }
}
