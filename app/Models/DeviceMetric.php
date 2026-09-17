<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'metric_type',
        'value',
        'unit',
        'recorded_at',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'float',
            'recorded_at' => 'datetime',
        ];
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function scopeForType($query, string $type)
    {
        return $query->where('metric_type', $type);
    }

    public function scopeRecent($query, int|string $hours = 24)
    {
        return $query->where('recorded_at', '>=', now()->subHours((int) $hours));
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('recorded_at', 'desc');
    }
}
