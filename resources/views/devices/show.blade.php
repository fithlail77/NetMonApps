@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <a href="{{ route('devices.index') }}" class="text-gray-400 hover:text-white mr-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-bold">{{ $device->name }}</h2>
                <p class="text-sm text-gray-400">{{ $device->ip_address }} &middot; {{ $device->deviceType->name ?? 'Unknown' }}</p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            @if($device->status === 'up')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-900/50 text-green-400 border border-green-700">
                    <span class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></span> Online
                </span>
            @elseif($device->status === 'warning')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-900/50 text-yellow-400 border border-yellow-700">
                    <span class="w-2 h-2 bg-yellow-400 rounded-full mr-2"></span> Warning
                </span>
            @elseif($device->status === 'down')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-900/50 text-red-400 border border-red-700">
                    <span class="w-2 h-2 bg-red-400 rounded-full mr-2 animate-pulse"></span> Down
                </span>
            @else
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-900/50 text-gray-400 border border-gray-700">
                    <span class="w-2 h-2 bg-gray-400 rounded-full mr-2"></span> Unknown
                </span>
            @endif
            <a href="{{ route('devices.edit', $device) }}" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition border border-gray-700">Edit</a>
        </div>
    </div>

    {{-- Device Info --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-gray-900 rounded-xl border border-gray-800 p-4">
            <p class="text-sm text-gray-400">IP Address</p>
            <p class="text-lg font-mono text-white mt-1">{{ $device->ip_address }}</p>
        </div>
        <div class="bg-gray-900 rounded-xl border border-gray-800 p-4">
            <p class="text-sm text-gray-400">Location</p>
            <p class="text-lg text-white mt-1">{{ $device->location ?? 'Not set' }}</p>
        </div>
        <div class="bg-gray-900 rounded-xl border border-gray-800 p-4">
            <p class="text-sm text-gray-400">Last Seen</p>
            <p class="text-lg text-white mt-1">{{ $device->last_seen_at ? $device->last_seen_at->diffForHumans() : 'Never' }}</p>
        </div>
    </div>

    {{-- Metrics Charts --}}
    <div class="bg-gray-900 rounded-xl border border-gray-800 p-6">
        <h3 class="text-lg font-semibold mb-4">Performance Metrics (24h)</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-400 mb-2">Latency (ms)</p>
                <canvas id="latencyChart" height="150"></canvas>
            </div>
            <div>
                <p class="text-sm text-gray-400 mb-2">Packet Loss (%)</p>
                <canvas id="packetLossChart" height="150"></canvas>
            </div>
        </div>
    </div>

    {{-- Active Alerts --}}
    @if($device->alerts->isNotEmpty())
        <div class="bg-gray-900 rounded-xl border border-gray-800">
            <div class="px-6 py-4 border-b border-gray-800">
                <h3 class="text-lg font-semibold">Active Alerts</h3>
            </div>
            <div class="divide-y divide-gray-800">
                @foreach($device->alerts as $alert)
                    <div class="px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            @if($alert->severity === 'critical')
                                <span class="w-3 h-3 bg-red-500 rounded-full animate-pulse"></span>
                            @elseif($alert->severity === 'warning')
                                <span class="w-3 h-3 bg-yellow-500 rounded-full"></span>
                            @else
                                <span class="w-3 h-3 bg-blue-500 rounded-full"></span>
                            @endif
                            <div>
                                <p class="text-sm text-white">{{ $alert->message }}</p>
                                <p class="text-xs text-gray-400">{{ $alert->triggered_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <span class="text-xs text-gray-500">{{ ucfirst($alert->status) }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const latencyData = @json($recentMetrics->get('latency', collect())->pluck('value')->values()->toArray());
        const packetLossData = @json($recentMetrics->get('packet_loss', collect())->pluck('value')->values()->toArray());
        const labels = @json($recentMetrics->get('latency', collect())->pluck('recorded_at')->values()->map(fn($d) => $d->format('H:i'))->toArray());

        function initCharts() {
            if (typeof Chart === 'undefined') {
                console.error('Chart.js not loaded');
                return;
            }

            new Chart(document.getElementById('latencyChart'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Latency',
                        data: latencyData,
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { color: '#374151' }, ticks: { color: '#9CA3AF' } },
                        y: { grid: { color: '#374151' }, ticks: { color: '#9CA3AF' } }
                    }
                }
            });

            new Chart(document.getElementById('packetLossChart'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Packet Loss',
                        data: packetLossData,
                        borderColor: '#EF4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { color: '#374151' }, ticks: { color: '#9CA3AF' } },
                        y: { grid: { color: '#374151' }, ticks: { color: '#9CA3AF' }, min: 0, max: 100 }
                    }
                }
            });
        }

        if (typeof Chart !== 'undefined') {
            initCharts();
        } else {
            window.addEventListener('load', initCharts);
        }
    });
</script>
@endpush
@endsection
