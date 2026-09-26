@forelse($devices as $device)
    <tr class="hover:bg-gray-800/50 cursor-pointer" onclick="window.location='{{ route('devices.show', $device) }}'">
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
        <td class="px-6 py-4 text-sm font-medium text-white">{{ $device->name }}</td>
        <td class="px-6 py-4 text-sm text-gray-300 font-mono">{{ $device->ip_address }}</td>
        <td class="px-6 py-4 text-sm text-gray-300">{{ $device->deviceType->name ?? '-' }}</td>
        <td class="px-6 py-4 text-sm text-gray-300">{{ $device->location ?? '-' }}</td>
        <td class="px-6 py-4 text-sm text-gray-300 font-mono">
            @if($device->latency !== null)
                {{ number_format($device->latency, 1) }} ms
            @else
                <span class="text-gray-500">-</span>
            @endif
        </td>
        <td class="px-6 py-4 text-sm text-gray-400">{{ $device->last_seen_at ? $device->last_seen_at->diffForHumans() : 'Never' }}</td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <p>No devices found</p>
            <a href="{{ route('devices.create') }}" class="mt-2 inline-block text-blue-400 hover:text-blue-300">Add your first device</a>
        </td>
    </tr>
@endforelse
