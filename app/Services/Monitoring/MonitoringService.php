<?php

namespace App\Services\Monitoring;

use App\Events\DeviceDown;
use App\Events\DeviceUp;
use App\Models\Device;
use App\Models\DeviceMetric;
use App\Models\DeviceStatusLog;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class MonitoringService
{
    public const DEFAULT_DOWN_CHECKS = 3;

    public const DEFAULT_UP_CHECKS = 2;

    private PingService $pingService;

    private SnmpService $snmpService;

    public function __construct(PingService $pingService, SnmpService $snmpService)
    {
        $this->pingService = $pingService;
        $this->snmpService = $snmpService;
    }

    public static function downThreshold(): int
    {
        return max(1, (int) Setting::getValue('debounce_down_checks', (string) self::DEFAULT_DOWN_CHECKS));
    }

    public static function upThreshold(): int
    {
        return max(1, (int) Setting::getValue('debounce_up_checks', (string) self::DEFAULT_UP_CHECKS));
    }

    public function checkDevice(Device $device): array
    {
        $hasLogs = DeviceStatusLog::where('device_id', $device->id)->exists();

        if (! $hasLogs) {
            DeviceStatusLog::create([
                'device_id' => $device->id,
                'status' => $device->status,
                'changed_at' => $device->created_at ?? now(),
            ]);
        }

        $pingResult = $this->pingService->ping($device->ip_address);

        $metrics = [
            'latency' => $pingResult['latency'],
            'packet_loss' => $pingResult['packet_loss'],
        ];

        if ($pingResult['status'] === 'up' && $device->snmp_community) {
            $cpu = $this->snmpService->getCpuUsage(
                $device->ip_address,
                $device->snmp_community,
                $device->snmp_version
            );
            $memory = $this->snmpService->getMemoryUsage(
                $device->ip_address,
                $device->snmp_community,
                $device->snmp_version
            );

            if ($cpu !== null) {
                $metrics['cpu_usage'] = $cpu;
            }
            if ($memory !== null) {
                $metrics['memory_usage'] = $memory;
            }
        }

        $this->storeMetrics($device, $metrics);

        $pingOk = $pingResult['status'] === 'up';
        $previousStatus = $device->status;
        $newStatus = $previousStatus;

        if ($pingOk) {
            $device->consecutive_successes = min(255, $device->consecutive_successes + 1);
            $device->consecutive_failures = 0;
        } else {
            $device->consecutive_failures = min(255, $device->consecutive_failures + 1);
            $device->consecutive_successes = 0;
        }

        if ($pingOk && $device->consecutive_successes >= self::upThreshold()) {
            $newStatus = 'up';

            if (isset($metrics['cpu_usage']) && $metrics['cpu_usage'] > 90) {
                $newStatus = 'warning';
            }
        } elseif (! $pingOk && $device->consecutive_failures >= self::downThreshold()) {
            $newStatus = 'down';
        }

        $device->update([
            'status' => $newStatus,
            'consecutive_failures' => $device->consecutive_failures,
            'consecutive_successes' => $device->consecutive_successes,
            'last_seen_at' => $pingOk ? now() : $device->last_seen_at,
        ]);

        if ($previousStatus !== $newStatus) {
            DeviceStatusLog::create([
                'device_id' => $device->id,
                'status' => $newStatus,
                'changed_at' => now(),
            ]);
            $this->handleStatusChange($device, $previousStatus, $newStatus);
        }

        return [
            'device_id' => $device->id,
            'status' => $newStatus,
            'metrics' => $metrics,
            'previous_status' => $previousStatus,
        ];
    }

    public function checkAllDevices(): array
    {
        $devices = Device::all();
        $results = [];

        foreach ($devices as $device) {
            try {
                $results[] = $this->checkDevice($device);
            } catch (\Throwable $e) {
                Log::error("Failed to check device {$device->name}", [
                    'error' => $e->getMessage(),
                    'exception' => $e::class,
                ]);
            }
        }

        return $results;
    }

    private function storeMetrics(Device $device, array $metrics): void
    {
        $now = now();

        foreach ($metrics as $type => $value) {
            if ($value !== null) {
                DeviceMetric::create([
                    'device_id' => $device->id,
                    'metric_type' => $type,
                    'value' => $value,
                    'unit' => $this->getUnit($type),
                    'recorded_at' => $now,
                ]);
            }
        }
    }

    private function getUnit(string $type): string
    {
        return match ($type) {
            'latency' => 'ms',
            'packet_loss' => '%',
            'cpu_usage' => '%',
            'memory_usage' => '%',
            default => '',
        };
    }

    private function handleStatusChange(Device $device, string $oldStatus, string $newStatus): void
    {
        Log::info("Device status changed: {$device->name}", [
            'from' => $oldStatus,
            'to' => $newStatus,
        ]);

        if ($oldStatus !== 'down' && $newStatus === 'down') {
            event(new DeviceDown($device));
        } elseif ($oldStatus === 'down' && $newStatus !== 'down') {
            event(new DeviceUp($device));
        }
    }
}
