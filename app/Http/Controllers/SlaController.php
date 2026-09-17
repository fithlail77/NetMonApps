<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Services\SlaService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SlaController extends Controller
{
    public function __construct(private SlaService $slaService)
    {
    }

    public function index(Request $request)
    {
        $period = $request->get('period', 'daily');
        $deviceId = $request->get('device_id');
        $date = $request->get('date') ? Carbon::parse($request->get('date')) : Carbon::today();

        $slaData = match ($period) {
            'daily' => $this->slaService->getDailySla($deviceId, $date),
            'weekly' => $this->slaService->getWeeklySla($deviceId, $date),
            'monthly' => $this->slaService->getMonthlySla($deviceId, $date),
            'yearly' => $this->slaService->getYearlySla($deviceId, $date),
            'total' => $this->slaService->getTotalSla($deviceId),
            default => $this->slaService->getDailySla($deviceId, $date),
        };

        $devices = Device::orderBy('name')->get();

        $summary = [
            'total_devices' => $slaData->count(),
            'avg_uptime' => $slaData->avg('uptime_percent'),
            'devices_meeting_sla' => $slaData->where('sla_met', true)->count(),
            'devices_breaching_sla' => $slaData->where('sla_met', false)->count(),
            'total_downtime_hours' => $slaData->sum('downtime_hours'),
        ];

        return view('sla.index', compact('slaData', 'devices', 'period', 'date', 'summary'));
    }

    public function export(Request $request)
    {
        $period = $request->get('period', 'daily');
        $deviceId = $request->get('device_id');
        $date = $request->get('date') ? Carbon::parse($request->get('date')) : Carbon::today();

        $slaData = match ($period) {
            'daily' => $this->slaService->getDailySla($deviceId, $date),
            'weekly' => $this->slaService->getWeeklySla($deviceId, $date),
            'monthly' => $this->slaService->getMonthlySla($deviceId, $date),
            'yearly' => $this->slaService->getYearlySla($deviceId, $date),
            'total' => $this->slaService->getTotalSla($deviceId),
            default => $this->slaService->getDailySla($deviceId, $date),
        };

        $filename = "sla_report_{$period}_" . Carbon::now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($slaData) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Device Name',
                'IP Address',
                'Device Type',
                'Period',
                'Start Date',
                'End Date',
                'Total Hours',
                'Uptime Hours',
                'Downtime Hours',
                'Uptime %',
                'Downtime %',
                'SLA Target %',
                'SLA Met',
            ]);

            foreach ($slaData as $row) {
                fputcsv($file, [
                    $row['device_name'],
                    $row['ip_address'],
                    $row['device_type'],
                    $row['period'],
                    $row['start_date'],
                    $row['end_date'],
                    $row['total_hours'],
                    $row['uptime_hours'],
                    $row['downtime_hours'],
                    $row['uptime_percent'] . '%',
                    $row['downtime_percent'] . '%',
                    $row['sla_target'] . '%',
                    $row['sla_met'] ? 'Yes' : 'No',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
