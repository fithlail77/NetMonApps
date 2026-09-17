<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\TopologyEdge;
use App\Models\TopologyNode;
use Illuminate\Http\Request;

class TopologyController extends Controller
{
    public function index()
    {
        return view('topology.index');
    }

    public function data()
    {
        $nodes = TopologyNode::with('device')->get()->map(function ($node) {
            return [
                'id' => $node->id,
                'label' => $node->label ?? $node->device->name,
                'x' => $node->x_position,
                'y' => $node->y_position,
                'status' => $node->device->status ?? 'unknown',
                'ip' => $node->device->ip_address,
                'type' => $node->device->deviceType->name ?? 'Unknown',
            ];
        });

        $edges = TopologyEdge::with(['sourceNode', 'targetNode'])->get()->map(function ($edge) {
            return [
                'id' => $edge->id,
                'from' => $edge->source_node_id,
                'to' => $edge->target_node_id,
                'label' => $edge->label,
                'status' => $edge->status,
            ];
        });

        return response()->json(['nodes' => $nodes, 'edges' => $edges]);
    }

    public function storeNode(Request $request)
    {
        $validated = $request->validate([
            'device_id' => 'required|exists:devices,id',
            'x_position' => 'required|numeric',
            'y_position' => 'required|numeric',
            'label' => 'nullable|string',
        ]);

        TopologyNode::create($validated);

        return redirect()->back()->with('success', 'Node added to topology.');
    }

    public function storeEdge(Request $request)
    {
        $validated = $request->validate([
            'source_node_id' => 'required|exists:topology_nodes,id',
            'target_node_id' => 'required|exists:topology_nodes,id',
            'label' => 'nullable|string',
        ]);

        $validated['status'] = 'unknown';

        TopologyEdge::create($validated);

        return redirect()->back()->with('success', 'Edge added to topology.');
    }

    public function updateNode(Request $request, TopologyNode $node)
    {
        $validated = $request->validate([
            'x_position' => 'required|numeric',
            'y_position' => 'required|numeric',
        ]);

        $node->update($validated);

        return response()->json(['success' => true]);
    }

    public function destroyNode(TopologyNode $node)
    {
        $node->delete();

        return redirect()->back()->with('success', 'Node removed from topology.');
    }

    public function destroyEdge(TopologyEdge $edge)
    {
        $edge->delete();

        return redirect()->back()->with('success', 'Edge removed from topology.');
    }
}
