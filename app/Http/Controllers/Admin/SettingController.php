<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function edit()
    {
        // Plain (non-translatable) settings
        $settings = [
            'site_name'      => setting('site_name', 'STEMLY'),
            'support_email'  => setting('support_email', 'support@stemly.test'),
            'certificate_signer' => setting('certificate_signer', 'Dr. Ada Quantum'),
            'certificate_signer_title' => setting('certificate_signer_title', 'Академический директор, STEMLY'),
        ];

        // Translatable settings -> decode stored JSON into per-locale arrays for the form
        $translatable = [];
        foreach (Setting::TRANSLATABLE as $key) {
            $raw = Setting::raw($key);
            $decoded = $raw ? json_decode($raw, true) : null;
            $translatable[$key] = is_array($decoded) ? $decoded : ($raw ? [config('app.fallback_locale') => $raw] : []);
        }

        return view('admin.settings', compact('settings', 'translatable'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_name'      => ['required', 'string', 'max:80'],
            'support_email'  => ['nullable', 'email'],
            'certificate_signer' => ['nullable', 'string', 'max:80'],
            'certificate_signer_title' => ['nullable', 'string', 'max:120'],
            'tagline'        => ['nullable', 'array'],
            'tagline.*'      => ['nullable', 'string', 'max:200'],
            'footer_note'    => ['nullable', 'array'],
            'footer_note.*'  => ['nullable', 'string', 'max:200'],
        ]);

        // Plain settings
        foreach (['site_name', 'support_email', 'certificate_signer', 'certificate_signer_title'] as $key) {
            Setting::put($key, $request->input($key));
        }

        // Translatable settings: store cleaned per-locale arrays as JSON
        foreach (Setting::TRANSLATABLE as $key) {
            $values = collect($request->input($key, []))
                ->map(fn ($v) => is_string($v) ? trim($v) : $v)
                ->filter(fn ($v) => filled($v))
                ->all();
            Setting::put($key, $values);
        }

        return back()->with('status', 'Настройки сохранены.');
    }
}
