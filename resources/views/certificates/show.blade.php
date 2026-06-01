@extends('layouts.app')
@section('title', 'Сертификат')

@section('content')
<div class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between">
        <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-cyan-500 hover:underline">← В кабинет</a>
        <a href="{{ route('certificates.download', $certificate) }}" class="btn btn-signal">↓ Скачать PDF</a>
    </div>

    {{-- Certificate visual --}}
    <div class="mt-6 overflow-hidden rounded-3xl border-4 border-ink-950 bg-blueprint p-2 shadow-2xl">
        <div class="relative rounded-2xl border border-signal-300/30 bg-ink-950 px-8 py-14 text-center text-white sm:px-16">
            <div class="absolute inset-0 bg-blueprint opacity-50"></div>
            <div class="relative">
                <div class="flex items-center justify-center gap-2.5">
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-signal-300 font-display text-lg font-extrabold text-ink-950">S</span>
                    <span class="font-display text-xl font-extrabold">{{ $brandName }}</span>
                </div>

                <p class="mt-10 text-sm font-semibold uppercase tracking-[0.3em] text-signal-300">Сертификат о прохождении курса</p>

                <p class="mt-8 text-sm text-white/50">Настоящим подтверждается, что</p>
                <h1 class="mt-3 font-display text-4xl font-extrabold text-white sm:text-5xl">{{ $certificate->recipient_name }}</h1>

                <p class="mt-8 text-sm text-white/50">успешно завершил(а) курс</p>
                <h2 class="mt-2 font-display text-2xl font-bold text-signal-300">«{{ $certificate->course_title }}»</h2>

                <div class="mt-12 flex items-end justify-between border-t border-white/10 pt-6 text-left">
                    <div>
                        <p class="font-display text-lg font-bold">{{ setting('certificate_signer', 'Dr. Ada Quantum') }}</p>
                        <p class="text-xs text-white/50">{{ setting('certificate_signer_title', 'Академический директор') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-white/50">№ {{ $certificate->certificate_number }}</p>
                        <p class="text-xs text-white/50">{{ $certificate->issued_at->format('d.m.Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <p class="mt-5 text-center text-sm text-ink-500">
        Подлинность сертификата можно проверить на странице
        <a href="{{ route('certificates.verify', ['code' => $certificate->certificate_number]) }}" class="font-semibold text-cyan-500 hover:underline">проверки</a>
        по номеру <span class="font-mono font-semibold">{{ $certificate->certificate_number }}</span>.
    </p>
</div>
@endsection
