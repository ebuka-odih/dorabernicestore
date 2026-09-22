<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Setting extends Model
{
    public $incrementing = false;

    protected $primaryKey = 'key';

    protected $keyType = 'string';

    protected $fillable = ['key', 'value'];

    /**
     * Read a setting, falling back to $default when the row (or table) is missing.
     */
    public static function get(string $key, ?string $default = null): ?string
    {
        try {
            if (! Schema::hasTable('settings')) {
                return $default;
            }

            $row = static::query()->where('key', $key)->first();

            return $row?->value ?? $default;
        } catch (\Exception) {
            return $default;
        }
    }

    /**
     * Persist a setting value (null/empty string clears it back to the fallback).
     */
    public static function set(string $key, ?string $value): void
    {
        $value = $value !== null && trim($value) === '' ? null : $value;

        static::query()->updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
