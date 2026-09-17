<?php

namespace App\Console\Commands;

use App\Services\Monitoring\MonitoringService;
use Illuminate\Console\Command;

class MonitoringPing extends Command
{
    protected $signature = 'monitoring:ping';
    protected $description = 'Ping all devices and update status';

    public function handle(MonitoringService $monitoring): int
    {
        $this->info('Starting ping check for all devices...');

        $results = $monitoring->checkAllDevices();

        $up = collect($results)->where('status', 'up')->count();
        $down = collect($results)->where('status', 'down')->count();
        $warning = collect($results)->where('status', 'warning')->count();

        $this->info("Check complete:");
        $this->info("  Online: {$up}");
        $this->info("  Down: {$down}");
        $this->info("  Warning: {$warning}");
        $this->info("  Total: " . count($results));

        return self::SUCCESS;
    }
}
