@extends('layouts.app')
@section('title', 'Личный кабинет')

@php
    $levels = ['beginner' => 'Начальный', 'intermediate' => 'Средний', 'advanced' => 'Продвинутый'];
@endphp

@section('content')
<section class="bg-blueprint text-white">
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="grid h-16 w-16 place-items-center rounded-2xl bg-signal-300 font-display text-2xl font-extrabold text-ink-950">{{ $user->initials }}</div>
                <div>
                    <h1 class="font-display text-2xl font-extrabold sm:text-3xl">Привет, {{ $user->first_name }}!</h1>
                    <p class="text-white/60">{{ $user->headline ?: 'Продолжайте учиться 🚀' }}</p>
                </div>
            </div>
            <a href="{{ route('profile.edit') }}" class="btn btn-outline-light">Редактировать профиль</a>
        </div>

        <div class="mt-10 grid grid-cols-2 gap-4 sm:grid-cols-4">
            @foreach([['enrolled','Курсов','📚'],['completed','Пройдено','✅'],['skills','Навыков','🧠'],['certs','Сертификатов','📜']] as [$k,$label,$icon])
                <div class="rounded-2xl border border-white/10 bg-white/5 p-5 backdrop-blur">
                    <div class="text-2xl">{{ $icon }}</div>
                    <div class="mt-2 font-display text-3xl font-extrabold text-signal-300">{{ $stats[$k] }}</div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-white/50">{{ $label }}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<div class="mx-auto max-w-7xl gap-10 px-4 py-12 sm:px-6 lg:grid lg:grid-cols-[1fr_340px] lg:px-8">
    <div class="space-y-12">
        {{-- In progress --}}
        <section>
            <h2 class="font-display text-2xl font-extrabold">Продолжить обучение</h2>
            @if($inProgress->isEmpty())
                <div class="card mt-5 grid place-items-center p-12 text-center">
                    <div class="text-4xl">🎯</div>
                    <p class="mt-3 font-semibold">Вы ещё не записаны на курсы</p>
                    <a href="{{ route('courses.index') }}" class="btn btn-signal mt-4">Выбрать курс</a>
                </div>
            @else
                <div class="mt-5 space-y-4">
                    @foreach($inProgress as $enrollment)
                        @php($c = $enrollment->course)
                        @if($c)
                        <div class="flex flex-col gap-4 rounded-2xl border border-paper-line bg-white p-4 sm:flex-row sm:items-center">
                            <div class="grid h-20 w-full shrink-0 place-items-center overflow-hidden rounded-xl bg-aurora sm:w-28">
                                @if($c->thumbnail)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($c->thumbnail) }}" class="h-full w-full object-cover" alt="">
                                @else
                                    <span class="font-display text-xl font-bold text-signal-300">{{ mb_substr($c->title,0,1) }}</span>
                                @endif
                            </div>
                            <div class="flex-1">
                                <p class="text-xs font-semibold text-cyan-500">{{ $c->category?->name }}</p>
                                <h3 class="font-display font-bold">{{ $c->title }}</h3>
                                <div class="mt-2 flex items-center gap-3">
                                    <div class="h-2 flex-1 overflow-hidden rounded-full bg-paper-line">
                                        <div class="h-full rounded-full bg-signal-400" style="width: {{ $enrollment->progress }}%"></div>
                                    </div>
                                    <span class="text-xs font-semibold text-ink-500">{{ $enrollment->progress }}%</span>
                                </div>
                            </div>
                            @php($first = $c->lessons()->orderBy('sort_order')->first())
                            @if($first)
                                <a href="{{ route('learn.lesson', [$c, $first]) }}" class="btn btn-ink shrink-0">Продолжить</a>
                            @endif
                        </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </section>

        {{-- Completed --}}
        @if($completed->isNotEmpty())
            <section>
                <h2 class="font-display text-2xl font-extrabold">Завершённые курсы</h2>
                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    @foreach($completed as $enrollment)
                        @php($c = $enrollment->course)
                        @if($c)
                        <div class="rounded-2xl border border-paper-line bg-white p-5">
                            <div class="flex items-start justify-between">
                                <div>
                                    <p class="text-xs font-semibold text-cyan-500">{{ $c->category?->name }}</p>
                                    <h3 class="font-display font-bold">{{ $c->title }}</h3>
                                </div>
                                <span class="chip bg-signal-300/20 text-signal-600">✓ 100%</span>
                            </div>
                            <p class="mt-2 text-xs text-ink-500">Завершён {{ $enrollment->completed_at?->format('d.m.Y') }}</p>
                        </div>
                        @endif
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Skills --}}
        @if($skills->isNotEmpty())
            <section>
                <h2 class="font-display text-2xl font-extrabold">Приобретённые навыки</h2>
                <div class="mt-5 flex flex-wrap gap-2.5">
                    @foreach($skills as $skill)
                        <span class="chip bg-cyan-400/10 px-4 py-2 text-cyan-600">🧠 {{ $skill->name }}</span>
                    @endforeach
                </div>
            </section>
        @endif
    </div>

    {{-- Right column --}}
    <aside class="mt-12 space-y-10 lg:mt-0">
        {{-- Certificates --}}
        <section>
            <h2 class="font-display text-lg font-bold">Мои сертификаты</h2>
            @if($certificates->isEmpty())
                <p class="mt-3 rounded-2xl border border-paper-line bg-white px-4 py-5 text-sm text-ink-500">Завершите курс, чтобы получить первый сертификат.</p>
            @else
                <div class="mt-4 space-y-3">
                    @foreach($certificates as $cert)
                        <a href="{{ route('certificates.show', $cert) }}" class="block rounded-2xl border border-paper-line bg-white p-4 transition hover:shadow-md">
                            <div class="flex items-center gap-2 text-signal-600">📜 <span class="text-xs font-semibold uppercase tracking-wider">Сертификат</span></div>
                            <p class="mt-1.5 font-display font-bold leading-snug">{{ $cert->course_title }}</p>
                            <p class="mt-1 text-xs text-ink-500">№ {{ $cert->certificate_number }} · {{ $cert->issued_at->format('d.m.Y') }}</p>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- Recommendations --}}
        @if($recommendations->isNotEmpty())
            <section>
                <h2 class="font-display text-lg font-bold">Рекомендуем вам</h2>
                <div class="mt-4 space-y-3">
                    @foreach($recommendations as $rc)
                        <a href="{{ route('courses.show', $rc) }}" class="flex gap-3 rounded-2xl border border-paper-line bg-white p-3 transition hover:shadow-md">
                            <div class="grid h-14 w-14 shrink-0 place-items-center overflow-hidden rounded-xl bg-aurora">
                                @if($rc->thumbnail)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($rc->thumbnail) }}" class="h-full w-full object-cover" alt="">
                                @else
                                    <span class="font-display font-bold text-signal-300">{{ mb_substr($rc->title,0,1) }}</span>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="line-clamp-2 text-sm font-semibold">{{ $rc->title }}</p>
                                <p class="mt-1 text-xs text-ink-500">{{ $rc->lessons_count }} уроков · {{ $rc->enrollments_count }} учеников</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </aside>
</div>
@endsection
