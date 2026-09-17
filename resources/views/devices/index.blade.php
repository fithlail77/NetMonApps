@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold">Devices</h2>
        <a href="{{ route('devices.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Device
        </a>
    </div>

    {{-- Filters --}}
    <div class="bg-gray-900 rounded-xl border border-gray-800 p-4">
        <form method="GET" class="flex flex-wrap gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search devices..."
                   class="bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-64">
            <select name="status" class="bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Status</option>
                <option value="up" {{ request('status') === 'up' ? 'selected' : '' }}>Online</option>
                <option value="warning" {{ request('status') === 'warning' ? 'selected' : '' }}>Warning</option>
                <option value="down" {{ request('status') === 'down' ? 'selected' : '' }}>Down</option>
                <option value="unknown" {{ request('status') === 'unknown' ? 'selected' : '' }}>Unknown</option>
            </select>
            <select name="device_type_id" class="bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Types</option>
                @foreach($deviceTypes as $type)
                    <option value="{{ $type->id }}" {{ request('device_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-gray-700 hover:bg-gray-600 text-white text-sm px-4 py-2 rounded-lg">Filter</button>
            <a href="{{ route('devices.index') }}" class="text-gray-400 hover:text-white text-sm px-4 py-2">Reset</a>
        </form>
    </div>

    {{-- Devices Table --}}
    <div class="bg-gray-900 rounded-xl border border-gray-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-800/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">IP Address</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Last Seen</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    @forelse($devices as $device)
                        <tr class="hover:bg-gray-800/50">
                            <td class="px-6 py-4">
                                @if($device->status === 'up')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-900/50 text-green-400 border border-green-700">
                                        <span class="w-2 h-2 bg-green-400 rounded-full mr-1.5"></span> Online
                                    </span>
                                @elseif($device->status === 'warning')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-900/50 text-yellow-400 border border-yellow-700">
                                        <span class="w-2 h-2 bg-yellow-400 rounded-full mr-1.5"></span> Warning
                                    </span>
                                @elseif($device->status === 'down')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-900/50 text-red-400 border border-red-700">
                                        <span class="w-2 h-2 bg-red-400 rounded-full mr-1.5"></span> Down
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-900/50 text-gray-400 border border-gray-700">
                                        <span class="w-2 h-2 bg-gray-400 rounded-full mr-1.5"></span> Unknown
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('devices.show', $device) }}" class="text-sm font-medium text-white hover:text-blue-400">{{ $device->name }}</a>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-300 font-mono">{{ $device->ip_address }}</td>
                            <td class="px-6 py-4 text-sm text-gray-300">{{ $device->deviceType->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-300">{{ $device->location ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-400">{{ $device->last_seen_at ? $device->last_seen_at->diffForHumans() : 'Never' }}</td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('devices.show', $device) }}" class="text-blue-400 hover:text-blue-300">View</a>
                                    <a href="{{ route('devices.edit', $device) }}" class="text-yellow-400 hover:text-yellow-300">Edit</a>
                                    <form method="POST" action="{{ route('devices.destroy', $device) }}" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-300">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                                </svg>
                                <p>No devices found</p>
                                <a href="{{ route('devices.create') }}" class="mt-2 inline-block text-blue-400 hover:text-blue-300">Add your first device</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($devices->hasPages())
            <div class="px-6 py-4 border-t border-gray-800">
                {{ $devices->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
