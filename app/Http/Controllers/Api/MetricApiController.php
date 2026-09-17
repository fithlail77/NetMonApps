<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeviceMetric;
use Illuminate\Http\Request;

class MetricApiController extends Controller
{
    public function index(Request $request)
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
