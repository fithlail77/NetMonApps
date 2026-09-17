<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AlertRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'device_id',
        'metric_type',
        'condition',
        'threshold',
        'severity',
        'is_active',
        'notify_email',
        'notify_telegram',
    ];

    protected function casts(): array
    {
        return [
            'threshold' => 'float',
            'is_active' => 'boolean',
            'notify_email' => 'boolean',
            'notify_telegram' => 'boolean',
        ];
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class);
    }

    public function evaluate(float $value): bool
    {
        return match ($this->condition) {
            'greater_than' => $value > $this->threshold,
            'less_than' => $value < $this->threshold,
            'equals' => abs($value - $this->threshold) < 0.001,
            default => false,
        };
    }
}
