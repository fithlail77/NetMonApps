<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\DeviceMetric;
use Illuminate\Http\Request;

class MetricController extends Controller
{
    public function index(Request $request)
    {
        $deviceId = $request->input('device_id');
        $metricType = $request->input('metric_type', 'latency');
        $hours = $request->input('hours', 24);

        $query = DeviceMetric::with('device');

        if ($deviceId) {
            $query->where('device_id', $deviceId);
        }

        $metrics = $query->forType($metricType)
            ->recent($hours)
            ->orderBy('recorded_at', 'desc')
            ->limit(500)
            ->get();

        $devices = Device::orderBy('name')->get();

        return view('metrics.index', compact('metrics', 'devices', 'metricType', 'hours', 'deviceId'));
    }

    public function apiData(Request $request)
    {
        $deviceId = $request->device_id;
        $metricType = $request->metric_type ?? 'latency';
        $hours = $request->hours ?? 24;

        $query = DeviceMetric::query();

        if ($deviceId) {
            $query->where('device_id', $deviceId);
        }

        $metrics = $query->forType($metricType)
            ->recent($hours)
            ->orderBy('recorded_at', 'asc')
            ->get();

        return response()->json([
            'labels' => $metrics->pluck('recorded_at')->map(fn($d) => $d->format('H:i'))->toArray(),
            'values' => $metrics->pluck('value')->toArray(),
        ]);
    }
}
