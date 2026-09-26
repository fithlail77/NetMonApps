<?php

namespace App\Services;

use App\Models\Device;
use App\Models\DeviceStatusLog;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SlaService
{
    public function getDailySla(?int $deviceId = null, ?Carbon $date = null): Collection
    {
        $date = $date ?? Carbon::today();
        $start = $date->copy()->startOfDay();
        $end = $date->copy()->endOfDay();

        return $this->calculateSla($deviceId, $start, $end, 'day');
    }

    public function getWeeklySla(?int $deviceId = null, ?Carbon $date = null): Collection
    {
        $date = $date ?? Carbon::today();
        $start = $date->copy()->startOfWeek();
        $end = $date->copy()->endOfWeek();

        return $this->calculateSla($deviceId, $start, $end, 'week');
    }

    public function getMonthlySla(?int $deviceId = null, ?Carbon $date = null): Collection
    {
        $date = $date ?? Carbon::today();
        $start = $date->copy()->startOfMonth();
        $end = $date->copy()->endOfMonth();

        return $this->calculateSla($deviceId, $start, $end, 'month');
    }

    public function getYearlySla(?int $deviceId = null, ?Carbon $date = null): Collection
    {
        $date = $date ?? Carbon::today();
        $start = $date->copy()->startOfYear();
        $end = $date->copy()->endOfYear();

        return $this->calculateSla($deviceId, $start, $end, 'year');
    }

    public function getTotalSla(?int $deviceId = null): Collection
    {
        $firstLog = DeviceStatusLog::query()
            ->when($deviceId, fn ($q) => $q->where('device_id', $deviceId))
            ->oldest('changed_at')
            ->first();

        $start = $firstLog ? $firstLog->changed_at : Carbon::now()->subYear();
        $end = Carbon::now();

        return $this->calculateSla($deviceId, $start, $end, 'total');
    }

    private function calculateSla(?int $deviceId, Carbon $start, Carbon $end, string $period): Collection
    {
        $devices = $deviceId
            ? Device::where('id', $deviceId)->get()
            : Device::all();

        $results = collect();

        foreach ($devices as $device) {
            $lastLogBeforeStart = DeviceStatusLog::forDevice($device->id)
                ->where('changed_at', '<', $start)
                ->latest('changed_at')
                ->first();

            $logs = DeviceStatusLog::forDevice($device->id)
                ->where('changed_at', '>=', $start)
                ->where('changed_at', '<=', $end)
                ->orderBy('changed_at')
                ->get();

            $allLogs = collect();
            if ($lastLogBeforeStart) {
                $allLogs->push($lastLogBeforeStart);
            }
            $allLogs = $allLogs->merge($logs)->sortBy('changed_at')->values();

            $totalSeconds = abs($end->diffInSeconds($start));
            $downtimeSeconds = max(0, $this->calculateDowntime($allLogs, $start, $end));
            $uptimeSeconds = max(0, $totalSeconds - $downtimeSeconds);

            $uptimePercent = $totalSeconds > 0
                ? min(100, round(($uptimeSeconds / $totalSeconds) * 100, 4))
                : 0;
            $downtimePercent = round(100 - $uptimePercent, 4);

            $results->push([
                'device_id' => $device->id,
                'device_name' => $device->name,
                'ip_address' => $device->ip_address,
                'device_type' => $device->deviceType->name ?? '-',
                'period' => $period,
                'start_date' => $start->format('Y-m-d H:i:s'),
                'end_date' => $end->format('Y-m-d H:i:s'),
                'total_hours' => round($totalSeconds / 3600, 2),
                'uptime_hours' => round($uptimeSeconds / 3600, 2),
                'downtime_hours' => round($downtimeSeconds / 3600, 2),
                'uptime_percent' => $uptimePercent,
                'downtime_percent' => $downtimePercent,
                'sla_target' => 99.99,
                'sla_met' => $uptimePercent >= 99.99,
            ]);
        }

        return $results;
    }

    private function calculateDowntime(Collection $logs, Carbon $start, Carbon $end): float
    {
        if ($logs->isEmpty()) {
            return 0;
        }

        $downtimeSeconds = 0;
        $periodStart = $start->copy();

        foreach ($logs as $log) {
            if ($log->changed_at->lte($periodStart)) {
                continue;
            }

            if ($log->changed_at->gte($end)) {
                if ($this->isDownStatus($logs->where('changed_at', '<=', $periodStart)->last()?->status ?? 'up')) {
                    $downtimeSeconds += abs($end->diffInSeconds($periodStart));
                }
                break;
            }

            if ($this->isDownStatus($logs->where('changed_at', '<=', $periodStart)->last()?->status ?? 'up')) {
                $downtimeSeconds += abs($log->changed_at->diffInSeconds($periodStart));
            }

            $periodStart = $log->changed_at->copy();
        }

        if ($this->isDownStatus($logs->last()->status ?? 'up') && $periodStart->lt($end)) {
            $downtimeSeconds += abs($end->diffInSeconds($periodStart));
        }

        return $downtimeSeconds;
    }

    private function isDownStatus(?string $status): bool
    {
        return in_array($status, ['down', 'unknown']);
    }
}
