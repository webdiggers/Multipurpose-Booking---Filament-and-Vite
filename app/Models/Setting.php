<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type'];

    protected $casts = [
        'value' => 'string',
    ];

    /**
     * Get a setting value by key
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = self::where('key', $key)->first();
        
        if (!$setting || $setting->value === null) {
            return $default;
        }

        return match ($setting->type) {
            'json' => json_decode($setting->value, true) ?: $default,
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            default => $setting->value,
        };
    }

    /**
     * Set a setting value
     */
    public static function set(string $key, mixed $value, string $type = 'text'): void
    {
        $valueToStore = match ($type) {
            'json' => json_encode($value),
            'boolean' => $value ? '1' : '0',
            default => (string) $value,
        };

        self::updateOrCreate(
            ['key' => $key],
            ['value' => $valueToStore, 'type' => $type]
        );
    }
}
