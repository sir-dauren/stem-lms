<?php

use App\Models\Setting;

if (! function_exists('setting')) {
    function setting(string $key, $default = null)
    {
        return Setting::get($key, $default);
    }
}

if (! function_exists('initials')) {
    function initials(?string $name): string
    {
        $parts = preg_split('/\s+/', trim((string) $name));
        $a = mb_substr($parts[0] ?? '', 0, 1);
        $b = mb_substr($parts[1] ?? '', 0, 1);
        return mb_strtoupper($a.$b) ?: 'U';
    }
}

if (! function_exists('minutes_to_label')) {
    function minutes_to_label(int $minutes): string
    {
        $h = intdiv($minutes, 60);
        $m = $minutes % 60;
        $hu = __('app.unit_hours');
        $mu = __('app.unit_minutes');
        if ($h && $m) return "{$h} {$hu} {$m} {$mu}";
        if ($h) return "{$h} {$hu}";
        return "{$m} {$mu}";
    }
}

if (! function_exists('locales')) {
    /** All locales available on the platform. */
    function locales(): array
    {
        return config('locales.available', []);
    }
}

if (! function_exists('current_locale')) {
    function current_locale(): string
    {
        return app()->getLocale();
    }
}

if (! function_exists('locale_label')) {
    function locale_label(?string $code = null): string
    {
        $code = $code ?: app()->getLocale();
        return config("locales.available.$code.native", strtoupper($code));
    }
}

if (! function_exists('locale_flag')) {
    function locale_flag(?string $code = null): string
    {
        $code = $code ?: app()->getLocale();
        return config("locales.available.$code.flag", '🌐');
    }
}
