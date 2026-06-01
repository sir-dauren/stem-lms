@extends('layouts.app')
@section('title', 'Проверка сертификата')

@section('content')
<div class="mx-auto max-w-2xl px-4 py-16 sm:px-6 lg:px-8">
    <h1 class="font-display text-3xl font-extrabold">Проверка сертификата</h1>
    <p class="mt-2 text-ink-600">Введите номер сертификата, чтобы убедиться в его подлинности.</p>

    <form method="GET" action="{{ route('certificates.verify') }}" class="mt-6 flex gap-3">
        <input name="code" value="{{ $code }}" placeholder="Например: STM-XXXX-2026-XXXX" class="input font-mono">
        <button class="btn btn-ink">Проверить</button>
    </form>

    @if($code)
        <div class="mt-8">
            @if($certificate)
                <div class="rounded-3xl border border-signal-400/40 bg-signal-50 p-8">
                    <div class="flex items-center gap-2 text-signal-600">
                        <span class="grid h-8 w-8 place-items-center rounded-full bg-signal-300 text-ink-950">✓</span>
                        <span class="font-display text-lg font-bold">Сертификат действителен</span>
                    </div>
                    <dl class="mt-6 space-y-3 text-sm">
                        <div class="flex justify-between border-b border-signal-400/20 pb-3"><dt class="text-ink-500">Получатель</dt><dd class="font-bold">{{ $certificate->recipient_name }}</dd></div>
                        <div class="flex justify-between border-b border-signal-400/20 pb-3"><dt class="text-ink-500">Курс</dt><dd class="font-bold text-right">{{ $certificate->course_title }}</dd></div>
                        <div class="flex justify-between border-b border-signal-400/20 pb-3"><dt class="text-ink-500">Номер</dt><dd class="font-mono font-semibold">{{ $certificate->certificate_number }}</dd></div>
                        <div class="flex justify-between"><dt class="text-ink-500">Дата выдачи</dt><dd class="font-semibold">{{ $certificate->issued_at->format('d.m.Y') }}</dd></div>
                    </dl>
                </div>
            @else
                <div class="rounded-3xl border border-coral-400/40 bg-coral-400/10 p-8 text-center">
                    <div class="text-3xl">✕</div>
                    <p class="mt-3 font-display text-lg font-bold">Сертификат не найден</p>
                    <p class="mt-1 text-sm text-ink-600">Проверьте правильность номера и попробуйте снова.</p>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
