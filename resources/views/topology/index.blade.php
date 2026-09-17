@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold">Network Topology</h2>
        <div class="flex items-center space-x-3">
            <button onclick="openAddNodeModal()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition">
                + Add Device
            </button>
            <button onclick="openAddEdgeModal()" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white text-sm rounded-lg border border-gray-700 transition">
                + Connect
            </button>
            <button onclick="togglePhysics()" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white text-sm rounded-lg border border-gray-700 transition">
                Toggle Physics
            </button>
            <button onclick="fitNetwork()" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white text-sm rounded-lg border border-gray-700 transition">
                Fit View
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-900/50 border border-green-700 text-green-300 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Topology Container --}}
    <div class="bg-gray-900 rounded-xl border border-gray-800 overflow-hidden">
        <div id="topology" style="height: 600px; background: #0a0a1a;"></div>
    </div>

    {{-- Legend --}}
    <div class="bg-gray-900 rounded-xl border border-gray-800 p-4">
        <div class="flex items-center space-x-6 text-sm">
            <span class="text-gray-400">Legend:</span>
            <div class="flex items-center space-x-2">
                <span class="w-4 h-4 rounded-full bg-green-500"></span>
                <span class="text-gray-300">Online</span>
            </div>
            <div class="flex items-center space-x-2">
                <span class="w-4 h-4 rounded-full bg-yellow-500"></span>
                <span class="text-gray-300">Warning</span>
            </div>
            <div class="flex items-center space-x-2">
                <span class="w-4 h-4 rounded-full bg-red-500"></span>
                <span class="text-gray-300">Down</span>
            </div>
            <div class="flex items-center space-x-2">
                <span class="w-4 h-4 rounded-full bg-gray-500"></span>
                <span class="text-gray-300">Unknown</span>
            </div>
        </div>
    </div>
</div>

{{-- Add Node Modal --}}
<div id="addNodeModal" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 hidden">
    <div class="bg-gray-900 rounded-xl border border-gray-700 w-full max-w-md p-6">
        <h3 class="text-lg font-semibold mb-4">Add Device to Topology</h3>
        <form method="POST" action="{{ route('topology.node.store') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Device</label>
                    <select name="device_id" required
                            class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select device...</option>
                        @foreach(\App\Models\Device::all() as $device)
                            <option value="{{ $device->id }}">{{ $device->name }} ({{ $device->ip_address }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Position X</label>
                        <input type="number" name="x_position" value="0" step="any"
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Position Y</label>
                        <input type="number" name="y_position" value="0" step="any"
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="closeAddNodeModal()" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white text-sm rounded-lg border border-gray-700 transition">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition">Add</button>
            </div>
        </form>
    </div>
</div>

{{-- Add Edge Modal --}}
<div id="addEdgeModal" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 hidden">
    <div class="bg-gray-900 rounded-xl border border-gray-700 w-full max-w-md p-6">
        <h3 class="text-lg font-semibold mb-4">Connect Two Devices</h3>
        <form method="POST" action="{{ route('topology.edge.store') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Source Device</label>
                    <select name="source_node_id" required id="edgeSource"
                            class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select source...</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Target Device</label>
                    <select name="target_node_id" required id="edgeTarget"
                            class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select target...</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Label (optional)</label>
                    <input type="text" name="label" placeholder="e.g. WAN Link"
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="closeAddEdgeModal()" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white text-sm rounded-lg border border-gray-700 transition">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition">Connect</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('head_scripts')
<script src="https://unpkg.com/vis-network@9.1.6/standalone/umd/vis-network.min.js"></script>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusColors = {
        up: '#22c55e',
        warning: '#eab308',
        down: '#ef4444',
        unknown: '#6b7280'
    };

    let network;
    let physicsEnabled = false;
    let topologyNodes = [];

    function openAddNodeModal() {
        document.getElementById('addNodeModal').classList.remove('hidden');
    }

    function closeAddNodeModal() {
        document.getElementById('addNodeModal').classList.add('hidden');
    }

    function openAddEdgeModal() {
        const sourceSelect = document.getElementById('edgeSource');
        const targetSelect = document.getElementById('edgeTarget');

        sourceSelect.innerHTML = '<option value="">Select source...</option>';
        targetSelect.innerHTML = '<option value="">Select target...</option>';

        topologyNodes.forEach(node => {
            const opt1 = new Option(node.label.split('\n')[0], node.id);
            const opt2 = new Option(node.label.split('\n')[0], node.id);
            sourceSelect.appendChild(opt1);
            targetSelect.appendChild(opt2.cloneNode(true));
        });

        document.getElementById('addEdgeModal').classList.remove('hidden');
    }

    function closeAddEdgeModal() {
        document.getElementById('addEdgeModal').classList.add('hidden');
    }

    function togglePhysics() {
        physicsEnabled = !physicsEnabled;
        if (network) network.setOptions({ physics: { enabled: physicsEnabled } });
    }

    function fitNetwork() {
        if (network) network.fit();
    }

    async function loadTopology() {
        try {
            const response = await fetch('{{ route("topology.data") }}');
            const data = await response.json();

            topologyNodes = data.nodes;

            const nodes = new vis.DataSet(data.nodes.map(node => ({
                id: node.id,
                label: `${node.label}\n${node.ip}`,
                color: {
                    background: statusColors[node.status] || statusColors.unknown,
                    border: statusColors[node.status] || statusColors.unknown,
                    highlight: { background: statusColors[node.status] || statusColors.unknown, border: '#fff' }
                },
                font: { color: '#fff', size: 12 },
                shape: 'dot',
                size: 20,
                x: node.x || 0,
                y: node.y || 0
            })));

            const edges = new vis.DataSet(data.edges.map(edge => ({
                id: edge.id,
                from: edge.from,
                to: edge.to,
                label: edge.label || '',
                color: { color: statusColors[edge.status] || '#6b7280' },
                font: { color: '#9ca3af', size: 10 },
                arrows: 'to'
            })));

            const container = document.getElementById('topology');
            const options = {
                physics: { enabled: physicsEnabled },
                edges: {
                    smooth: { type: 'continuous' },
                    color: { inherit: true }
                },
                nodes: {
                    borderWidth: 2
                },
                interaction: {
                    hover: true,
                    tooltipDelay: 200
                }
            };

            network = new vis.Network(container, { nodes, edges }, options);

            network.on('click', function(params) {
                if (params.nodes.length > 0) {
                    const nodeId = params.nodes[0];
                    window.location.href = `/devices/${nodeId}`;
                }
            });
        } catch (error) {
            console.error('Failed to load topology:', error);
        }
    }

    function initVis() {
        if (typeof vis !== 'undefined') {
            loadTopology();
        } else {
            console.error('vis-network not loaded');
        }
    }

    if (typeof vis !== 'undefined') {
        initVis();
    } else {
        window.addEventListener('load', initVis);
    }

    window.openAddNodeModal = openAddNodeModal;
    window.closeAddNodeModal = closeAddNodeModal;
    window.openAddEdgeModal = openAddEdgeModal;
    window.closeAddEdgeModal = closeAddEdgeModal;
    window.togglePhysics = togglePhysics;
    window.fitNetwork = fitNetwork;
});
</script>
@endpush
