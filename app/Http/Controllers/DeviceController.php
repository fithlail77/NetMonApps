<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\DeviceType;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index(Request $request)
    {
        $query = Device::with('deviceType');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('ip_address', 'ilike', "%{$search}%")
                  ->orWhere('location', 'ilike', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('device_type_id')) {
            $query->where('device_type_id', $request->device_type_id);
        }

        $devices = $query->latest()->paginate(15)->withQueryString();
        $deviceTypes = DeviceType::all();

        return view('devices.index', compact('devices', 'deviceTypes'));
    }

    public function create()
    {
        $deviceTypes = DeviceType::all();

        return view('devices.create', compact('deviceTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ip_address' => 'required|ip|unique:devices,ip_address',
            'device_type_id' => 'required|exists:device_types,id',
            'snmp_community' => 'nullable|string|max:255',
            'snmp_version' => 'nullable|in:v1,v2c,v3',
            'snmp_port' => 'nullable|integer|min:1|max:65535',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['updated_by'] = auth()->id();
        $validated['status'] = 'unknown';

        Device::create($validated);

        return redirect()->route('devices.index')->with('success', 'Device created successfully.');
    }

    public function show(Device $device)
    {
        $device->load('deviceType', 'metrics', 'alertRules', 'alerts');

        $recentMetrics = $device->metrics()
            ->latest('recorded_at')
            ->limit(100)
            ->get()
            ->groupBy('metric_type');

        return view('devices.show', compact('device', 'recentMetrics'));
    }

    public function edit(Device $device)
    {
        $deviceTypes = DeviceType::all();

        return view('devices.edit', compact('device', 'deviceTypes'));
    }

    public function update(Request $request, Device $device)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ip_address' => 'required|ip|unique:devices,ip_address,' . $device->id,
            'device_type_id' => 'required|exists:device_types,id',
            'snmp_community' => 'nullable|string|max:255',
            'snmp_version' => 'nullable|in:v1,v2c,v3',
            'snmp_port' => 'nullable|integer|min:1|max:65535',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $validated['updated_by'] = auth()->id();

        $device->update($validated);

        return redirect()->route('devices.index')->with('success', 'Device updated successfully.');
    }

    public function destroy(Device $device)
    {
        $device->delete();

        return redirect()->route('devices.index')->with('success', 'Device deleted successfully.');
    }
}
