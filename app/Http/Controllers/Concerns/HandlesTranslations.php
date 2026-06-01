<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

trait HandlesTranslations
{
    /**
     * Build validation rules for a set of translatable fields.
     * The default locale is required for $requiredFields; other locales are optional.
     *
     * @param  array<int,string>  $requiredFields
     * @param  array<int,string>  $optionalFields
     * @return array<string,mixed>
     */
    protected function translationRules(array $requiredFields, array $optionalFields = []): array
    {
        $default = config('app.fallback_locale');
        $rules = [];

        foreach ($requiredFields as $field) {
            $rules[$field] = ['required', 'array'];
            $rules["{$field}.{$default}"] = ['required', 'string'];
        }
        foreach ($optionalFields as $field) {
            $rules[$field] = ['nullable', 'array'];
            $rules["{$field}.*"] = ['nullable', 'string'];
        }

        return $rules;
    }

    /**
     * Pull a translatable field from the request and drop empty locale values.
     *
     * @return array<string,string>
     */
    protected function translations(Request $request, string $field): array
    {
        return collect($request->input($field, []))
            ->map(fn ($v) => is_string($v) ? trim($v) : $v)
            ->filter(fn ($v) => filled($v))
            ->all();
    }

    /**
     * Custom validation message: default-locale value is required.
     *
     * @param  array<int,string>  $fields
     * @return array<string,string>
     */
    protected function translationMessages(array $fields): array
    {
        $default = config('app.fallback_locale');
        $messages = [];
        foreach ($fields as $field) {
            $messages["{$field}.{$default}.required"] = 'Заполните поле хотя бы на языке по умолчанию (RU).';
        }
        return $messages;
    }
}
