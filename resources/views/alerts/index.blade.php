@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold">Alerts</h2>
    </div>

    {{-- Filters --}}
    <div class="bg-gray-900 rounded-xl border border-gray-800 p-4">
        <form method="GET" class="flex flex-wrap gap-4">
            <select name="status" class="bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Active Alerts</option>
                <option value="triggered" {{ request('status') === 'triggered' ? 'selected' : '' }}>Triggered</option>
                <option value="acknowledged" {{ request('status') === 'acknowledged' ? 'selected' : '' }}>Acknowledged</option>
                <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
            </select>
            <select name="severity" class="bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Severity</option>
                <option value="critical" {{ request('severity') === 'critical' ? 'selected' : '' }}>Critical</option>
                <option value="warning" {{ request('severity') === 'warning' ? 'selected' : '' }}>Warning</option>
                <option value="info" {{ request('severity') === 'info' ? 'selected' : '' }}>Info</option>
            </select>
            <button type="submit" class="bg-gray-700 hover:bg-gray-600 text-white text-sm px-4 py-2 rounded-lg">Filter</button>
        </form>
    </div>

    {{-- Alerts List --}}
    <div class="bg-gray-900 rounded-xl border border-gray-800 divide-y divide-gray-800">
        @forelse($alerts as $alert)
            <div class="px-6 py-4">
                <div class="flex items-start justify-between">
                    <div class="flex items-start space-x-3">
                        @if($alert->severity === 'critical')
                            <span class="w-3 h-3 bg-red-500 rounded-full mt-1.5 animate-pulse flex-shrink-0"></span>
                        @elseif($alert->severity === 'warning')
                            <span class="w-3 h-3 bg-yellow-500 rounded-full mt-1.5 flex-shrink-0"></span>
                        @else
                            <span class="w-3 h-3 bg-blue-500 rounded-full mt-1.5 flex-shrink-0"></span>
                        @endif
                        <div>
                            <div class="flex items-center space-x-2">
                                <p class="text-sm font-medium text-white">{{ $alert->device->name ?? 'Unknown Device' }}</p>
                                <span class="text-xs text-gray-500">{{ $alert->device->ip_address ?? '' }}</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    {{ $alert->severity === 'critical' ? 'bg-red-900/50 text-red-400' : ($alert->severity === 'warning' ? 'bg-yellow-900/50 text-yellow-400' : 'bg-blue-900/50 text-blue-400') }}">
                                    {{ ucfirst($alert->severity) }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-300 mt-1">{{ $alert->message }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $alert->triggered_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        @if($alert->status === 'triggered')
                            <form method="POST" action="{{ route('alerts.acknowledge', $alert) }}">
                                @csrf
                                <button type="submit" class="text-xs bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-1 rounded-lg transition">Acknowledge</button>
                            </form>
                        @endif
                        @if(in_array($alert->status, ['triggered', 'acknowledged']))
                            <form method="POST" action="{{ route('alerts.resolve', $alert) }}">
                                @csrf
                                <button type="submit" class="text-xs bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg transition">Resolve</button>
                            </form>
                        @endif
                        <span class="text-xs text-gray-500 px-2 py-1 rounded bg-gray-800">{{ ucfirst($alert->status) }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="px-6 py-12 text-center text-gray-500">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <p>No alerts</p>
            </div>
        @endforelse
    </div>

    @if($alerts->hasPages())
        <div class="bg-gray-900 rounded-xl border border-gray-800 px-6 py-4">
            {{ $alerts->links() }}
        </div>
    @endif
</div>
@endsection
