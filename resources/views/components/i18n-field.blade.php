@props([
    'name',
    'label' => null,
    'type' => 'text',
    'translations' => [],
    'required' => false,
    'rows' => 4,
    'placeholder' => '',
    'hint' => null,
])

@php
    $default = config('app.fallback_locale');
    $trans = is_array($translations) ? $translations : [];
@endphp

<div x-data="{ loc: '{{ $default }}' }" class="space-y-1.5">
    @if($label)
        <label class="label">{{ $label }} @if($required)<span class="text-coral-500">*</span>@endif</label>
    @endif

    <div class="flex gap-1">
        @foreach(config('locales.available') as $code => $meta)
            <button type="button" @click="loc='{{ $code }}'"
                :class="loc==='{{ $code }}' ? 'bg-ink-950 text-signal-300' : 'bg-paper text-ink-500 hover:bg-paper-line'"
                class="rounded-lg px-2.5 py-1 text-xs font-bold uppercase transition">{{ $meta['flag'] }} {{ $code }}</button>
        @endforeach
    </div>

    @foreach(config('locales.available') as $code => $meta)
        <div x-show="loc==='{{ $code }}'" @if(!$loop->first) x-cloak @endif>
            @if($type === 'textarea')
                <textarea name="{{ $name }}[{{ $code }}]" rows="{{ $rows }}"
                    placeholder="{{ $placeholder }}" class="input">{{ old($name.'.'.$code, $trans[$code] ?? '') }}</textarea>
            @else
                <input type="text" name="{{ $name }}[{{ $code }}]"
                    value="{{ old($name.'.'.$code, $trans[$code] ?? '') }}"
                    placeholder="{{ $placeholder }}" class="input">
            @endif
        </div>
    @endforeach

    @if($hint)<p class="text-xs text-ink-400">{{ $hint }}</p>@endif
</div>
