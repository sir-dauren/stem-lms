<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Setting keys whose value is stored as a JSON object of per-locale strings.
     */
    public const TRANSLATABLE = ['tagline', 'footer_note'];

    /**
     * Get a setting value. Translatable settings resolve to the current locale
     * with a fallback to the default locale.
     */
    public static function get(string $key, $default = null)
    {
        $all = Cache::rememberForever('settings.all', fn () => static::pluck('value', 'key')->all());
        $raw = $all[$key] ?? null;

        if ($raw === null) {
            return $default;
        }

        if (in_array($key, self::TRANSLATABLE, true)) {
            return self::localize($raw, $default);
        }

        return $raw;
    }

    /**
     * Get the raw stored value (untouched), useful for admin editing.
     */
    public static function raw(string $key, $default = null)
    {
        $all = Cache::rememberForever('settings.all', fn () => static::pluck('value', 'key')->all());
        return $all[$key] ?? $default;
    }

    /**
     * Decode a stored translatable value into a localized string.
     */
    public static function localize(?string $raw, $default = null)
    {
        if ($raw === null || $raw === '') {
            return $default;
        }

        $decoded = json_decode($raw, true);
        if (! is_array($decoded)) {
            return $raw; // plain string stored for a translatable key
        }

        $locale   = app()->getLocale();
        $fallback = config('app.fallback_locale');

        return $decoded[$locale]
            ?? $decoded[$fallback]
            ?? collect($decoded)->first(fn ($v) => filled($v))
            ?? $default;
    }

    /**
     * Store a setting. Arrays (per-locale) are JSON-encoded automatically.
     */
    public static function put(string $key, $value): void
    {
        if (is_array($value)) {
            $value = json_encode($value, JSON_UNESCAPED_UNICODE);
        }

        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget('settings.all');
    }
}
