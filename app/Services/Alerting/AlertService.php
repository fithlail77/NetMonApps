<?php

namespace App\Services\Alerting;

use App\Models\Alert;
use App\Models\AlertRule;
use App\Models\Device;
use App\Models\DeviceMetric;
use Illuminate\Support\Facades\Log;

class AlertService
{
    public function evaluateRules(): array
    {
        $activeRules = AlertRule::where('is_active', true)
            ->with('device')
            ->get();

        $triggeredAlerts = [];

        foreach ($activeRules as $rule) {
            $alert = $this->evaluateRule($rule);
            if ($alert) {
                $triggeredAlerts[] = $alert;
            }
        }

        return $triggeredAlerts;
    }

    public function evaluateRule(AlertRule $rule): ?Alert
    {
        $latestMetric = DeviceMetric::where('device_id', $rule->device_id)
            ->where('metric_type', $rule->metric_type)
            ->latest('recorded_at')
            ->first();

        if (! $latestMetric) {
            return null;
        }

        if (! $rule->evaluate($latestMetric->value)) {
            return null;
        }

        $recentAlert = Alert::where('alert_rule_id', $rule->id)
            ->where('device_id', $rule->device_id)
            ->whereIn('status', ['triggered', 'acknowledged'])
            ->where('triggered_at', '>=', now()->subHours(24))
            ->first();

        if ($recentAlert) {
            return null;
        }

        $alert = Alert::create([
            'alert_rule_id' => $rule->id,
            'device_id' => $rule->device_id,
            'message' => $this->buildAlertMessage($rule, $latestMetric),
            'severity' => $rule->severity,
            'status' => 'triggered',
            'triggered_at' => now(),
        ]);

        Log::warning("Alert triggered", [
            'rule' => $rule->name,
            'device' => $rule->device->name,
            'value' => $latestMetric->value,
            'threshold' => $rule->threshold,
        ]);

        event(new \App\Events\AlertTriggered($alert));

        return $alert;
    }

    public function checkDeviceDown(Device $device): ?Alert
    {
        $existingAlert = Alert::where('device_id', $device->id)
            ->where('status', 'triggered')
            ->where('message', 'like', '%down%')
            ->first();

        if ($existingAlert) {
            return null;
        }

        $alert = Alert::create([
            'alert_rule_id' => null,
            'device_id' => $device->id,
            'message' => "Device {$device->name} is down (no response to ping)",
            'severity' => 'critical',
            'status' => 'triggered',
            'triggered_at' => now(),
        ]);

        event(new \App\Events\AlertTriggered($alert));

        return $alert;
    }

    public function resolveDeviceAlerts(Device $device): void
    {
        Alert::where('device_id', $device->id)
            ->whereIn('status', ['triggered', 'acknowledged'])
            ->update([
                'status' => 'resolved',
                'resolved_at' => now(),
            ]);
    }

    private function buildAlertMessage(AlertRule $rule, DeviceMetric $metric): string
    {
        $conditionText = match ($rule->condition) {
            'greater_than' => 'exceeds',
            'less_than' => 'below',
            'equals' => 'equals',
            default => 'reached',
        };

        return "{$rule->name}: {$rule->device->name} {$rule->metric_type} {$conditionText} threshold ({$metric->value} {$metric->unit} / {$rule->threshold})";
    }

    public function cleanupOldAlerts(int $days = 30): int
    {
        return Alert::where('status', 'resolved')
            ->where('resolved_at', '<', now()->subDays($days))
            ->delete();
    }
}
