<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'ip_address',
        'device_type_id',
        'snmp_community',
        'snmp_version',
        'snmp_port',
        'location',
        'description',
        'status',
        'last_seen_at',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'last_seen_at' => 'datetime',
            'snmp_port' => 'integer',
        ];
    }

    public function deviceType(): BelongsTo
    {
        return $this->belongsTo(DeviceType::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function metrics(): HasMany
    {
        return $this->hasMany(DeviceMetric::class);
    }

    public function alertRules(): HasMany
    {
        return $this->hasMany(AlertRule::class);
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class);
    }

    public function topologyNode()
    {
        return $this->hasOne(TopologyNode::class);
    }

    public function isUp(): bool
    {
        return $this->status === 'up';
    }

    public function isDown(): bool
    {
        return $this->status === 'down';
    }

    public function markAsUp(): void
    {
        $this->update(['status' => 'up', 'last_seen_at' => now()]);
    }

    public function markAsDown(): void
    {
        $this->update(['status' => 'down']);
    }

    public function markAsWarning(): void
    {
        $this->update(['status' => 'warning']);
    }
}
