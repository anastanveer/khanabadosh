<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'key',
        'value',
    ];

    public static function getValue(string $key, $default = null)
    {
        $record = static::query()->where('key', $key)->first();

        return $record?->value ?? $default;
    }

    public static function setValue(string $key, $value): void
    {
        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => (string) $value]
        );
    }
}
