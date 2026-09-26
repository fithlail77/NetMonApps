@extends('layouts.app')

@section('content')
<div class="space-y-6">
    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-gray-900 rounded-xl p-6 border border-gray-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400">Total Devices</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ $totalDevices }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-600/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gray-900 rounded-xl p-6 border border-gray-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400">Online</p>
                    <p class="text-3xl font-bold text-green-400 mt-1">{{ $devicesUp }}</p>
                </div>
                <div class="w-12 h-12 bg-green-600/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gray-900 rounded-xl p-6 border border-gray-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400">Warning</p>
                    <p class="text-3xl font-bold text-yellow-400 mt-1">{{ $devicesWarning }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-600/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gray-900 rounded-xl p-6 border border-gray-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400">Down</p>
                    <p class="text-3xl font-bold text-red-400 mt-1">{{ $devicesDown }}</p>
                </div>
                <div class="w-12 h-12 bg-red-600/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Recent Alerts --}}
        <div class="lg:col-span-2 bg-gray-900 rounded-xl border border-gray-800">
            <div class="px-6 py-4 border-b border-gray-800 flex items-center justify-between">
                <h2 class="text-lg font-semibold">Recent Alerts</h2>
                <a href="{{ route('alerts.index') }}" class="text-sm text-blue-400 hover:text-blue-300">View All</a>
            </div>
            <div class="divide-y divide-gray-800">
                @forelse($recentAlerts as $alert)
                    <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-800/50">
                        <div class="flex items-center space-x-3">
                            @if($alert->severity === 'critical')
                                <span class="w-3 h-3 bg-red-500 rounded-full animate-pulse"></span>
                            @elseif($alert->severity === 'warning')
                                <span class="w-3 h-3 bg-yellow-500 rounded-full"></span>
                            @else
                                <span class="w-3 h-3 bg-blue-500 rounded-full"></span>
                            @endif
                            <div>
                                <p class="text-sm font-medium text-white">{{ $alert->device->name ?? 'Unknown' }}</p>
                                <p class="text-xs text-gray-400">{{ $alert->message }}</p>
                            </div>
                        </div>
                        <span class="text-xs text-gray-500">{{ $alert->triggered_at->diffForHumans() }}</span>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <p>No recent alerts</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Device Status --}}
        <div class="bg-gray-900 rounded-xl border border-gray-800">
            <div class="px-6 py-4 border-b border-gray-800">
                <h2 class="text-lg font-semibold">Device Status</h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                            <span class="text-sm text-gray-300">Online</span>
                        </div>
                        <span class="text-sm font-medium text-white">{{ $devicesUp ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <span class="w-3 h-3 bg-yellow-500 rounded-full"></span>
                            <span class="text-sm text-gray-300">Warning</span>
                        </div>
                        <span class="text-sm font-medium text-white">{{ $devicesWarning ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <span class="w-3 h-3 bg-red-500 rounded-full"></span>
                            <span class="text-sm text-gray-300">Down</span>
                        </div>
                        <span class="text-sm font-medium text-white">{{ $devicesDown ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <span class="w-3 h-3 bg-gray-500 rounded-full"></span>
                            <span class="text-sm text-gray-300">Unknown</span>
                        </div>
                        <span class="text-sm font-medium text-white">{{ $totalDevices - $devicesUp - $devicesDown - $devicesWarning }}</span>
                    </div>
                </div>

                {{-- Status Bar --}}
                @if($totalDevices > 0)
                    <div class="mt-6 flex h-3 rounded-full overflow-hidden bg-gray-800">
                        @if($devicesUp > 0)
                            <div class="bg-green-500" style="width: {{ ($devicesUp / $totalDevices) * 100 }}%"></div>
                        @endif
                        @if($devicesWarning > 0)
                            <div class="bg-yellow-500" style="width: {{ ($devicesWarning / $totalDevices) * 100 }}%"></div>
                        @endif
                        @if($devicesDown > 0)
                            <div class="bg-red-500" style="width: {{ ($devicesDown / $totalDevices) * 100 }}%"></div>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 mt-2 text-center">
                        {{ round(($devicesUp / $totalDevices) * 100, 1) }}% uptime
                    </p>
                @endif
            </div>
        </div>
    </div>

    {{-- Device List Table --}}
    <div class="bg-gray-900 rounded-xl border border-gray-800">
        <div class="px-6 py-4 border-b border-gray-800 flex items-center justify-between">
            <h2 class="text-lg font-semibold">Device List</h2>
            <a href="{{ route('devices.index') }}" class="text-sm text-blue-400 hover:text-blue-300">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-800/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">IP Address</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Latency</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Last Seen</th>
                    </tr>
                </thead>
                <tbody id="device-list-body" class="divide-y divide-gray-800">
                    @include('dashboard._device-list', ['devices' => $devices])
                </tbody>
            </table>
        </div>
        @if($devices->hasPages())
            <div class="px-6 py-4 border-t border-gray-800">
                {{ $devices->links() }}
            </div>
        @endif
    </div>

    {{-- Quick Actions --}}
    <div class="bg-gray-900 rounded-xl border border-gray-800 p-6">
        <h2 class="text-lg font-semibold mb-4">Quick Actions</h2>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('devices.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Device
            </a>
            <a href="{{ route('topology.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition border border-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                View Topology
            </a>
            <a href="{{ route('settings.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition border border-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Settings
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const url = @json(route('dashboard.devices'));
        const intervalMs = 90000;

        setInterval(async function () {
            if (document.hidden) {
                return;
            }

            try {
                const res = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                });

                if (!res.ok) {
                    return;
                }

                const html = await res.text();
                const tbody = document.getElementById('device-list-body');

                if (tbody) {
                    tbody.innerHTML = html;
                }
            } catch (e) {
                console.error('Device list refresh failed', e);
            }
        }, intervalMs);
    });
</script>
@endpush
@endsection
