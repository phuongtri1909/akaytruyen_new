<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use Illuminate\Console\Command;
use Carbon\Carbon;

class ClearOldActivityLogs extends Command
{
    protected $signature = 'activity-logs:clear {--days=7 : Number of days to keep logs}';

    protected $description = 'Clear activity logs older than specified days (default: 7 days)';

    public function handle()
    {
        $days = (int) $this->option('days');
        $cutoffDate = Carbon::now()->subDays($days);

        $this->info("Clearing activity logs older than {$days} days (before {$cutoffDate->format('Y-m-d H:i:s')})...");

        $logsToDelete = ActivityLog::where('created_at', '<', $cutoffDate)->count();

        if ($logsToDelete === 0) {
            $this->info('No old activity logs found to delete.');
            return 0;
        }

        $deletedCount = ActivityLog::where('created_at', '<', $cutoffDate)->delete();

        $this->info("Successfully deleted {$deletedCount} old activity logs.");

        $remainingCount = ActivityLog::count();
        $this->info("Remaining activity logs: {$remainingCount}");

        return 0;
    }
}
