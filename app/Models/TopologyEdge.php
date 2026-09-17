<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TopologyEdge extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_node_id',
        'target_node_id',
        'label',
        'status',
    ];

    public function sourceNode(): BelongsTo
    {
        return $this->belongsTo(TopologyNode::class, 'source_node_id');
    }

    public function targetNode(): BelongsTo
    {
        return $this->belongsTo(TopologyNode::class, 'target_node_id');
    }
}
