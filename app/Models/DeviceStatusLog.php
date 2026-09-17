<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceStatusLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'status',
        'changed_at',
    ];

    protected function casts(): array
    {
        return [
            'changed_at' => 'datetime',
        ];
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function scopeForDevice($query, int $deviceId)
    {
        return $query->where('device_id', $deviceId);
    }

    public function scopeBetween($query, string $from, string $to)
    {
        return $query->whereBetween('changed_at', [$from, $to]);
    }
}
