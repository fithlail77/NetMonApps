<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TopologyNode extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'x_position',
        'y_position',
        'label',
    ];

    protected function casts(): array
    {
        return [
            'x_position' => 'float',
            'y_position' => 'float',
        ];
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
}
