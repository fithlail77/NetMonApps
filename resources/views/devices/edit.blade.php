@extends('layouts.app')

@section('content')
<div class="max-w-2xl">
    <div class="flex items-center mb-6">
        <a href="{{ route('devices.index') }}" class="text-gray-400 hover:text-white mr-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h2 class="text-xl font-bold">Edit Device</h2>
    </div>

    <div class="bg-gray-900 rounded-xl border border-gray-800 p-6">
        <form method="POST" action="{{ route('devices.update', $device) }}">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-300 mb-1">Device Name *</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $device->name) }}" required
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="ip_address" class="block text-sm font-medium text-gray-300 mb-1">IP Address *</label>
                        <input type="text" id="ip_address" name="ip_address" value="{{ old('ip_address', $device->ip_address) }}" required
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('ip_address') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="device_type_id" class="block text-sm font-medium text-gray-300 mb-1">Device Type *</label>
                        <select id="device_type_id" name="device_type_id" required
                                class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach($deviceTypes as $type)
                                <option value="{{ $type->id }}" {{ old('device_type_id', $device->device_type_id) == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                            @endforeach
                        </select>
                        @error('device_type_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="snmp_community" class="block text-sm font-medium text-gray-300 mb-1">SNMP Community</label>
                        <input type="text" id="snmp_community" name="snmp_community" value="{{ old('snmp_community', $device->snmp_community) }}"
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('snmp_community') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="snmp_version" class="block text-sm font-medium text-gray-300 mb-1">SNMP Version</label>
                        <select id="snmp_version" name="snmp_version"
                                class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="v2c" {{ old('snmp_version', $device->snmp_version) === 'v2c' ? 'selected' : '' }}>v2c</option>
                            <option value="v1" {{ old('snmp_version', $device->snmp_version) === 'v1' ? 'selected' : '' }}>v1</option>
                            <option value="v3" {{ old('snmp_version', $device->snmp_version) === 'v3' ? 'selected' : '' }}>v3</option>
                        </select>
                        @error('snmp_version') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label for="location" class="block text-sm font-medium text-gray-300 mb-1">Location</label>
                    <input type="text" id="location" name="location" value="{{ old('location', $device->location) }}"
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('location') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-300 mb-1">Description</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $device->description) }}</textarea>
                    @error('description') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 mt-6 pt-6 border-t border-gray-800">
                <a href="{{ route('devices.index') }}" class="px-4 py-2 text-gray-400 hover:text-white text-sm">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                    Update Device
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
