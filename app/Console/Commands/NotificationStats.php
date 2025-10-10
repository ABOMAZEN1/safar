<?php

namespace App\Console\Commands;

use App\Models\AppNotification;
use Illuminate\Console\Command;

class NotificationStats extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'notifications:stats';

    /**
     * The console command description.
     */
    protected $description = 'Display notification statistics';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('📊 Notification Statistics');
        $this->newLine();

        // Overall statistics
        $total = AppNotification::count();
        $sent = AppNotification::where('status', 'sent')->count();
        $scheduled = AppNotification::where('status', 'scheduled')->count();
        $draft = AppNotification::where('status', 'draft')->count();
        $failed = AppNotification::where('status', 'failed')->count();

        $this->table(
            ['Metric', 'Count', 'Percentage'],
            [
                ['Total Notifications', $total, '100%'],
                ['Sent', $sent, $total > 0 ? round(($sent / $total) * 100, 2) . '%' : '0%'],
                ['Scheduled', $scheduled, $total > 0 ? round(($scheduled / $total) * 100, 2) . '%' : '0%'],
                ['Draft', $draft, $total > 0 ? round(($draft / $total) * 100, 2) . '%' : '0%'],
                ['Failed', $failed, $total > 0 ? round(($failed / $total) * 100, 2) . '%' : '0%'],
            ]
        );

        $this->newLine();

        // Target type statistics
        $this->info('🎯 Target Type Breakdown');
        $targetStats = AppNotification::selectRaw('target_type, COUNT(*) as count')
            ->groupBy('target_type')
            ->get();

        $targetTableData = [];
        foreach ($targetStats as $stat) {
            $typeName = match ($stat->target_type) {
                'all' => 'جميع المستخدمين',
                'specific' => 'مستخدمين محددين',
                'segment' => 'شريحة من المستخدمين',
                default => $stat->target_type,
            };
            $targetTableData[] = [$typeName, $stat->count];
        }

        if (!empty($targetTableData)) {
            $this->table(['Target Type', 'Count'], $targetTableData);
        }

        $this->newLine();

        // Recent activity
        $this->info('📅 Recent Activity (Last 7 days)');
        $recentCount = AppNotification::where('created_at', '>=', now()->subDays(7))->count();
        $this->line("Notifications created in last 7 days: {$recentCount}");

        // Success rate
        if ($total > 0) {
            $successRate = round((($sent + $draft) / $total) * 100, 2);
            $this->line("Success rate: {$successRate}%");
        }

        return Command::SUCCESS;
    }
}
