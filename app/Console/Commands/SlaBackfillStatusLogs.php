<?php

namespace App\Console\Commands;

use App\Models\Device;
use App\Models\DeviceStatusLog;
use Illuminate\Console\Command;

class SlaBackfillStatusLogs extends Command
{
    protected $signature = 'sla:backfill';
    protected $description = 'Backfill initial status logs for devices without any';

    public function handle(): int
    {
        $devices = Device::all();
        $count = 0;

        foreach ($devices as $device) {
            $hasLogs = DeviceStatusLog::where('device_id', $device->id)->exists();

            if (!$hasLogs) {
                DeviceStatusLog::create([
                    'device_id' => $device->id,
                    'status' => $device->status,
                    'changed_at' => $device->created_at ?? now(),
                ]);
                $count++;
                $this->info("Backfilled: {$device->name}");
            }
        }

        $this->info("Done. Backfilled {$count} devices.");

        return Command::SUCCESS;
    }
}
