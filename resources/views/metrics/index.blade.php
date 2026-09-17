@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold">Performance Metrics</h2>
    </div>

    {{-- Filters --}}
    <div class="bg-gray-900 rounded-xl border border-gray-800 p-4">
        <form method="GET" class="flex flex-wrap gap-4">
            <select name="device_id" class="bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Devices</option>
                @foreach($devices as $device)
                    <option value="{{ $device->id }}" {{ $deviceId == $device->id ? 'selected' : '' }}>{{ $device->name }}</option>
                @endforeach
            </select>
            <select name="metric_type" class="bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="latency" {{ $metricType === 'latency' ? 'selected' : '' }}>Latency</option>
                <option value="packet_loss" {{ $metricType === 'packet_loss' ? 'selected' : '' }}>Packet Loss</option>
                <option value="cpu_usage" {{ $metricType === 'cpu_usage' ? 'selected' : '' }}>CPU Usage</option>
                <option value="memory_usage" {{ $metricType === 'memory_usage' ? 'selected' : '' }}>Memory Usage</option>
            </select>
            <select name="hours" class="bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="1" {{ $hours == 1 ? 'selected' : '' }}>1 Hour</option>
                <option value="6" {{ $hours == 6 ? 'selected' : '' }}>6 Hours</option>
                <option value="24" {{ $hours == 24 ? 'selected' : '' }}>24 Hours</option>
                <option value="72" {{ $hours == 72 ? 'selected' : '' }}>3 Days</option>
                <option value="168" {{ $hours == 168 ? 'selected' : '' }}>7 Days</option>
            </select>
            <button type="submit" class="bg-gray-700 hover:bg-gray-600 text-white text-sm px-4 py-2 rounded-lg">Apply</button>
        </form>
    </div>

    {{-- Chart --}}
    <div class="bg-gray-900 rounded-xl border border-gray-800 p-6">
        @if($metrics->isEmpty())
            <div class="flex flex-col items-center justify-center h-64 text-gray-400">
                <svg class="w-12 h-12 mb-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <p class="text-sm">No {{ $metricType }} data available for this device.</p>
                <p class="text-xs text-gray-500 mt-1">Data will appear once monitoring is active.</p>
            </div>
        @else
            <canvas id="metricsChart" height="300"></canvas>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const labels = @json($metrics->pluck('recorded_at')->reverse()->values()->map(fn($d) => $d->format('H:i'))->toArray());
        const values = @json($metrics->pluck('value')->reverse()->values()->toArray());

        console.log('Metrics labels:', labels);
        console.log('Metrics values:', values);
        console.log('Chart available:', typeof Chart !== 'undefined');

        const metricLabels = {
            latency: 'Latency (ms)',
            packet_loss: 'Packet Loss (%)',
            cpu_usage: 'CPU Usage (%)',
            memory_usage: 'Memory Usage (%)'
        };

        const metricColors = {
            latency: '#3B82F6',
            packet_loss: '#EF4444',
            cpu_usage: '#8B5CF6',
            memory_usage: '#F59E0B'
        };

        function initChart() {
            if (labels.length === 0 || values.length === 0) {
                return;
            }

            const ctx = document.getElementById('metricsChart');
            if (!ctx) {
                console.error('Canvas not found');
                return;
            }
            if (typeof Chart === 'undefined') {
                console.error('Chart.js not loaded');
                return;
            }

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: metricLabels['{{ $metricType }}'] || 'Value',
                        data: values,
                        borderColor: metricColors['{{ $metricType }}'] || '#3B82F6',
                        backgroundColor: (metricColors['{{ $metricType }}'] || '#3B82F6') + '20',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { labels: { color: '#9CA3AF' } }
                    },
                    scales: {
                        x: { grid: { color: '#374151' }, ticks: { color: '#9CA3AF' } },
                        y: { grid: { color: '#374151' }, ticks: { color: '#9CA3AF' } }
                    }
                }
            });
        }

        if (typeof Chart !== 'undefined') {
            initChart();
        } else {
            window.addEventListener('load', initChart);
        }
    });
</script>
@endpush
@endsection
