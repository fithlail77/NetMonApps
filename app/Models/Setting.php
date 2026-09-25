<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value', 'group'];

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $cacheKey = "setting_{$key}";

        $cached = Cache::get($cacheKey);

        if ($cached !== null && ! is_string($cached)) {
            Cache::forget($cacheKey);
            $cached = null;
        }

        if ($cached === null) {
            $cached = Cache::remember($cacheKey, 3600, function () use ($key) {
                return static::where('key', $key)->value('value');
            });
        }

        return $cached ?? $default;
    }

    public static function setValue(string $key, mixed $value, string $group = 'general'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );

        Cache::forget("setting_{$key}");
    }

    public static function getGroup(string $group): array
    {
        return static::where('group', $group)
            ->pluck('value', 'key')
            ->toArray();
    }

    public static function getTelegramConfig(): array
    {
        return [
            'bot_token' => static::getValue('telegram_bot_token', config('services.telegram.bot_token', '')),
            'chat_id' => static::getValue('telegram_chat_id', config('services.telegram.chat_id', '')),
        ];
    }
}
