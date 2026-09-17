<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;

class DeviceApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Device::with('deviceType');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('device_type_id')) {
            $query->where('device_type_id', $request->device_type_id);
        }

        $devices = $query->latest()->paginate(20);

        return response()->json($devices);
    }

    public function show(Device $device)
    {
        $device->load('deviceType');

        $latestMetrics = $device->metrics()
            ->latest('recorded_at')
            ->limit(100)
            ->get()
            ->groupBy('metric_type');

        return response()->json([
            'device' => $device,
            'metrics' => $latestMetrics,
        ]);
    }

    public function metrics(Request $request, Device $device)
    {
        $metricType = $request->metric_type ?? 'latency';
        $hours = $request->hours ?? 24;

        $metrics = $device->metrics()
            ->forType($metricType)
            ->recent($hours)
            ->orderBy('recorded_at', 'asc')
            ->get();

        return response()->json([
            'labels' => $metrics->pluck('recorded_at')->map(fn($d) => $d->format('H:i'))->toArray(),
            'values' => $metrics->pluck('value')->toArray(),
        ]);
    }
}
