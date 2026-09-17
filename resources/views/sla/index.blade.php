@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold">SLA Reports</h2>
        <a href="{{ route('sla.export', ['period' => $period, 'device_id' => request('device_id'), 'date' => $date->format('Y-m-d')]) }}"
           class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Export CSV
        </a>
    </div>

    {{-- Summary Cards --}}
    <div class="flex flex-nowrap gap-4">
        <div class="flex-1 min-w-0 bg-gray-900 border border-gray-700/50 rounded-xl p-5 flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-sm">Total Devices</p>
                <p class="text-3xl font-bold text-white mt-1">{{ $summary['total_devices'] }}</p>
            </div>
            <div class="flex-shrink-0 w-11 h-11 bg-blue-900/50 border border-blue-700/50 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
        </div>
        <div class="flex-1 min-w-0 bg-gray-900 border border-gray-700/50 rounded-xl p-5 flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-sm">Avg Uptime</p>
                <p class="text-3xl font-bold text-green-400 mt-1">{{ number_format($summary['avg_uptime'], 2) }}%</p>
            </div>
            <div class="flex-shrink-0 w-11 h-11 bg-green-900/50 border border-green-700/50 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <div class="flex-1 min-w-0 bg-gray-900 border border-gray-700/50 rounded-xl p-5 flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-sm">SLA Met</p>
                <p class="text-3xl font-bold text-green-400 mt-1">{{ $summary['devices_meeting_sla'] }}</p>
            </div>
            <div class="flex-shrink-0 w-11 h-11 bg-green-900/50 border border-green-700/50 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
        </div>
        <div class="flex-1 min-w-0 bg-gray-900 border border-gray-700/50 rounded-xl p-5 flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-sm">SLA Breached</p>
                <p class="text-3xl font-bold text-red-400 mt-1">{{ $summary['devices_breaching_sla'] }}</p>
            </div>
            <div class="flex-shrink-0 w-11 h-11 bg-red-900/50 border border-red-700/50 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
        </div>
        <div class="flex-1 min-w-0 bg-gray-900 border border-gray-700/50 rounded-xl p-5 flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-sm">Total Downtime</p>
                <p class="text-3xl font-bold text-yellow-400 mt-1">{{ number_format($summary['total_downtime_hours'], 2) }}h</p>
            </div>
            <div class="flex-shrink-0 w-11 h-11 bg-yellow-900/50 border border-yellow-700/50 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Period Tabs --}}
    <div class="bg-gray-900 rounded-xl border border-gray-800 p-1">
        <nav class="flex space-x-1">
            @php
                $tabs = [
                    'daily' => 'Daily',
                    'weekly' => 'Weekly',
                    'monthly' => 'Monthly',
                    'yearly' => 'Yearly',
                    'total' => 'Total',
                ];
            @endphp
            @foreach($tabs as $key => $label)
                <a href="{{ route('sla.index', ['period' => $key, 'device_id' => request('device_id'), 'date' => $date->format('Y-m-d')]) }}"
                   class="flex-1 text-center px-4 py-2.5 text-sm font-medium rounded-lg transition {{ $period === $key ? 'bg-blue-600 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                    {{ $label }}
                </a>
            @endforeach
        </nav>
    </div>

    {{-- Filters --}}
    <div class="bg-gray-900 rounded-xl border border-gray-800 p-4">
        <form method="GET" class="flex flex-wrap gap-4">
            <input type="hidden" name="period" value="{{ $period }}">
            <select name="device_id" class="bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Devices</option>
                @foreach($devices as $device)
                    <option value="{{ $device->id }}" {{ request('device_id') == $device->id ? 'selected' : '' }}>{{ $device->name }}</option>
                @endforeach
            </select>
            @if($period !== 'total')
                <input type="date" name="date" value="{{ $date->format('Y-m-d') }}"
                       class="bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            @endif
            <button type="submit" class="bg-gray-700 hover:bg-gray-600 text-white text-sm px-4 py-2 rounded-lg">Filter</button>
            <a href="{{ route('sla.index', ['period' => $period]) }}" class="text-gray-400 hover:text-white text-sm px-4 py-2">Reset</a>
        </form>
    </div>

    {{-- SLA Table --}}
    <div class="bg-gray-900 rounded-xl border border-gray-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-800/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Device</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">IP Address</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Total Hours</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Uptime</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Downtime</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Uptime %</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">SLA (99.99%)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    @forelse($slaData as $row)
                        <tr class="hover:bg-gray-800/50">
                            <td class="px-6 py-4">
                                @if($row['sla_met'])
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-900/50 text-green-400 border border-green-700">
                                        <span class="w-2 h-2 bg-green-400 rounded-full mr-1.5"></span> Met
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-900/50 text-red-400 border border-red-700">
                                        <span class="w-2 h-2 bg-red-400 rounded-full mr-1.5"></span> Breached
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-white">{{ $row['device_name'] }}</td>
                            <td class="px-6 py-4 text-sm text-gray-300 font-mono">{{ $row['ip_address'] }}</td>
                            <td class="px-6 py-4 text-sm text-gray-300">{{ $row['device_type'] }}</td>
                            <td class="px-6 py-4 text-sm text-gray-300">{{ number_format($row['total_hours'], 2) }}h</td>
                            <td class="px-6 py-4 text-sm text-green-400">{{ number_format($row['uptime_hours'], 2) }}h</td>
                            <td class="px-6 py-4 text-sm text-red-400">{{ number_format($row['downtime_hours'], 2) }}h</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-16 bg-gray-700 rounded-full h-2 mr-2">
                                        <div class="h-2 rounded-full {{ $row['uptime_percent'] >= 99.99 ? 'bg-green-500' : 'bg-red-500' }}"
                                             style="width: {{ min($row['uptime_percent'], 100) }}%"></div>
                                    </div>
                                    <span class="text-sm {{ $row['uptime_percent'] >= 99.99 ? 'text-green-400' : 'text-red-400' }}">
                                        {{ number_format($row['uptime_percent'], 4) }}%
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($row['sla_met'])
                                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                <p>No SLA data available</p>
                                <p class="text-xs text-gray-600 mt-1">Data will appear once monitoring starts tracking status changes.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
