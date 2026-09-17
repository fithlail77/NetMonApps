<?php

namespace App\Console\Commands;

use App\Services\Alerting\AlertService;
use Illuminate\Console\Command;

class AlertsCleanup extends Command
{
    protected $signature = 'alerts:cleanup {--days=30 : Number of days to keep resolved alerts}';
    protected $description = 'Clean up old resolved alerts';

    public function handle(AlertService $alertService): int
    {
        $days = (int) $this->option('days');

        $this->info("Cleaning up resolved alerts older than {$days} days...");

        $deleted = $alertService->cleanupOldAlerts($days);

        $this->info("Deleted {$deleted} old alerts.");

        return self::SUCCESS;
    }
}
