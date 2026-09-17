<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TopologyEdge;
use App\Models\TopologyNode;

class TopologyApiController extends Controller
{
    public function index()
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

        $edges = TopologyEdge::all()->map(function ($edge) {
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
}
