<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Device;
use App\Models\DeviceMetric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $totalDevices = Device::count();
        $devicesUp = Device::where('status', 'up')->count();
        $devicesDown = Device::where('status', 'down')->count();
        $devicesWarning = Device::where('status', 'warning')->count();

        $recentAlerts = Alert::with(['device', 'alertRule'])
            ->active()
            ->latest('triggered_at')
            ->limit(10)
            ->get();

        $deviceStatus = Device::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $recentMetrics = DeviceMetric::with('device')
            ->latest('recorded_at')
            ->limit(50)
            ->get()
            ->groupBy('device_id')
            ->map(function ($metrics) {
                return $metrics->first();
            });

        $devices = Device::with('deviceType')
            ->select('devices.*')
            ->selectRaw('(SELECT value FROM device_metrics WHERE device_id = devices.id AND metric_type = ? ORDER BY recorded_at DESC LIMIT 1) as latency', ['latency'])
            ->latest()
            ->paginate(10);

        return view('dashboard.index', compact(
            'totalDevices',
            'devicesUp',
            'devicesDown',
            'devicesWarning',
            'recentAlerts',
            'deviceStatus',
            'recentMetrics',
            'devices'
        ));
    }
}
