<?php

namespace App\Console\Commands;

use App\Models\Device;
use App\Services\Monitoring\SnmpService;
use Illuminate\Console\Command;

class MonitoringSnmp extends Command
{
    protected $signature = 'monitoring:snmp';
    protected $description = 'Poll SNMP metrics for devices with SNMP configured';

    public function handle(SnmpService $snmp): int
    {
        $this->info('Starting SNMP polling...');

        $devices = Device::whereNotNull('snmp_community')
            ->where('status', 'up')
            ->get();

        $this->info("Found " . $devices->count() . " devices with SNMP configured.");

        foreach ($devices as $device) {
            try {
                $cpu = $snmp->getCpuUsage($device->ip_address, $device->snmp_community, $device->snmp_version);
                $memory = $snmp->getMemoryUsage($device->ip_address, $device->snmp_community, $device->snmp_version);

                $metrics = [];
                if ($cpu !== null) {
                    $metrics['cpu_usage'] = $cpu;
                }
                if ($memory !== null) {
                    $metrics['memory_usage'] = $memory;
                }

                if (! empty($metrics)) {
                    $this->storeMetrics($device, $metrics);
                    $this->info("  ✓ {$device->name}: CPU={$cpu}%, Memory={$memory}%");
                } else {
                    $this->warn("  ✗ {$device->name}: No SNMP data");
                }
            } catch (\Exception $e) {
                $this->error("  ✗ {$device->name}: {$e->getMessage()}");
            }
        }

        $this->info('SNMP polling complete.');
        return self::SUCCESS;
    }

    private function storeMetrics(Device $device, array $metrics): void
    {
        $now = now();
        foreach ($metrics as $type => $value) {
            \App\Models\DeviceMetric::create([
                'device_id' => $device->id,
                'metric_type' => $type,
                'value' => $value,
                'unit' => '%',
                'recorded_at' => $now,
            ]);
        }
    }
}
