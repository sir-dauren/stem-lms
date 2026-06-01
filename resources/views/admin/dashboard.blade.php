@extends('layouts.admin')
@section('title', 'Обзор')
@section('heading', 'Обзор платформы')

@section('content')
{{-- Stat cards --}}
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    @foreach([
        ['courses','Всего курсов','📚','ink'],
        ['published','Опубликовано','✅','signal'],
        ['users','Учеников','👥','cyan'],
        ['enrollments','Записей','📝','ink'],
        ['completions','Завершений','🎓','signal'],
        ['certs','Сертификатов','📜','cyan'],
        ['comments','Комментариев','💬','ink'],
        ['blocked','Заблокировано','🚫','coral'],
    ] as [$k,$label,$icon,$tone])
        <div class="card p-5">
            <div class="flex items-center justify-between">
                <span class="text-2xl">{{ $icon }}</span>
            </div>
            <div class="mt-3 font-display text-3xl font-extrabold">{{ number_format($stats[$k], 0, '.', ' ') }}</div>
            <div class="text-xs font-semibold uppercase tracking-wider text-ink-500">{{ $label }}</div>
        </div>
    @endforeach
</div>

<div class="mt-6 grid gap-6 lg:grid-cols-[1.6fr_1fr]">
    {{-- Enrollment trend --}}
    <div class="card p-6">
        <h2 class="font-display text-lg font-bold">Записи за 14 дней</h2>
        @php($max = max($days->max('value'), 1))
        <div class="mt-6 flex h-48 items-end gap-1.5">
            @foreach($days as $d)
                <div class="group flex flex-1 flex-col items-center justify-end gap-1.5">
                    <span class="text-[10px] font-bold text-ink-500 opacity-0 group-hover:opacity-100">{{ $d['value'] }}</span>
                    <div class="w-full rounded-t-lg bg-signal-400 transition-all hover:bg-signal-500" style="height: {{ max($d['value'] / $max * 100, 3) }}%"></div>
                    <span class="text-[10px] text-ink-400">{{ $d['label'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Top courses --}}
    <div class="card p-6">
        <h2 class="font-display text-lg font-bold">Топ курсов</h2>
        <div class="mt-4 space-y-3">
            @forelse($topCourses as $course)
                <a href="{{ route('admin.courses.edit', $course) }}" class="flex items-center justify-between gap-3 rounded-xl border border-paper-line p-3 transition hover:bg-paper">
                    <span class="line-clamp-1 text-sm font-semibold">{{ $course->title }}</span>
                    <span class="shrink-0 text-xs font-bold text-cyan-500">{{ $course->enrollments_count }} 👥</span>
                </a>
            @empty
                <p class="text-sm text-ink-500">Пока нет курсов.</p>
            @endforelse
        </div>
    </div>
</div>

<div class="mt-6 grid gap-6 lg:grid-cols-2">
    {{-- Recent users --}}
    <div class="card p-6">
        <h2 class="font-display text-lg font-bold">Новые пользователи</h2>
        <div class="mt-4 space-y-2">
            @forelse($recentUsers as $u)
                <a href="{{ route('admin.users.show', $u) }}" class="flex items-center gap-3 rounded-xl p-2 transition hover:bg-paper">
                    <span class="grid h-9 w-9 place-items-center rounded-full bg-ink-950 text-xs font-bold text-signal-300">{{ $u->initials }}</span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold">{{ $u->name }}</p>
                        <p class="truncate text-xs text-ink-500">{{ $u->email }}</p>
                    </div>
                    <span class="text-xs text-ink-400">{{ $u->created_at->diffForHumans() }}</span>
                </a>
            @empty
                <p class="text-sm text-ink-500">Пока нет пользователей.</p>
            @endforelse
        </div>
    </div>

    {{-- Recent comments --}}
    <div class="card p-6">
        <h2 class="font-display text-lg font-bold">Последние комментарии</h2>
        <div class="mt-4 space-y-2">
            @forelse($recentComments as $c)
                <div class="rounded-xl border border-paper-line p-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-semibold">{{ $c->user?->name }}</span>
                        <a href="{{ route('admin.comments.index') }}" class="text-xs text-cyan-500">модерация →</a>
                    </div>
                    <p class="mt-1 line-clamp-2 text-sm text-ink-600">{{ $c->body }}</p>
                    <p class="mt-1 text-xs text-ink-400">{{ $c->course?->title }}</p>
                </div>
            @empty
                <p class="text-sm text-ink-500">Пока нет комментариев.</p>
            @endforelse
        </div>
    </div>
</div>

<div class="mt-6 flex flex-wrap gap-3">
    <a href="{{ route('admin.courses.create') }}" class="btn btn-signal">+ Новый курс</a>
    <a href="{{ route('admin.quizzes.create') }}" class="btn btn-ghost">+ Новый тест</a>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-ghost">Управление категориями</a>
</div>
@endsection
