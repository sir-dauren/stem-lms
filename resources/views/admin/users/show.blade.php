@extends('layouts.admin')
@section('title', $user->name)
@section('heading', 'Профиль: '.$user->name)

@section('content')
<a href="{{ route('admin.users.index') }}" class="text-sm font-semibold text-cyan-500 hover:underline">← Все пользователи</a>

<div class="mt-4 grid gap-6 lg:grid-cols-[340px_1fr]">
    {{-- Profile card --}}
    <div class="space-y-6">
        <div class="card p-6 text-center">
            <span class="mx-auto grid h-20 w-20 place-items-center rounded-2xl bg-ink-950 text-2xl font-bold text-signal-300">{{ $user->initials }}</span>
            <h2 class="mt-4 font-display text-xl font-bold">{{ $user->name }}</h2>
            <p class="text-sm text-ink-500">{{ $user->email }}</p>
            <div class="mt-2 flex justify-center gap-2">
                @if($user->isAdmin())<span class="chip bg-signal-300/20 text-signal-600">Администратор</span>@endif
                @if($user->is_blocked)<span class="chip bg-coral-400/15 text-coral-500">Заблокирован</span>@endif
            </div>
            @if($user->headline)<p class="mt-3 text-sm text-ink-600">{{ $user->headline }}</p>@endif
            <p class="mt-3 text-xs text-ink-400">Регистрация: {{ $user->created_at->format('d.m.Y') }}</p>

            @if($user->is_blocked && $user->blocked_reason)
                <p class="mt-3 rounded-xl bg-coral-400/10 px-3 py-2 text-xs text-coral-600">Причина: {{ $user->blocked_reason }}</p>
            @endif
        </div>

        {{-- Actions --}}
        <div class="card p-6">
            <h3 class="font-display font-bold">Действия</h3>
            <div class="mt-4 space-y-3">
                @if($user->is_blocked)
                    <form method="POST" action="{{ route('admin.users.unblock', $user) }}">
                        @csrf
                        <button class="btn btn-signal w-full">Разблокировать</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.users.block', $user) }}" class="space-y-2">
                        @csrf
                        <input name="reason" placeholder="Причина блокировки" class="input">
                        <button class="btn w-full" style="background:#fb7185;color:#fff" onclick="return confirm('Заблокировать пользователя?')">Заблокировать</button>
                    </form>
                @endif

                <form method="POST" action="{{ route('admin.users.role', $user) }}">
                    @csrf
                    <button class="btn btn-ghost w-full" onclick="return confirm('Изменить роль пользователя?')">
                        {{ $user->isAdmin() ? 'Снять права администратора' : 'Назначить администратором' }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Activity --}}
    <div class="space-y-6">
        <div class="grid grid-cols-3 gap-4">
            <div class="card p-4 text-center"><div class="font-display text-2xl font-extrabold">{{ $user->enrollments->count() }}</div><div class="text-xs text-ink-500">Курсов</div></div>
            <div class="card p-4 text-center"><div class="font-display text-2xl font-extrabold">{{ $user->certificates->count() }}</div><div class="text-xs text-ink-500">Сертификатов</div></div>
            <div class="card p-4 text-center"><div class="font-display text-2xl font-extrabold">{{ $user->skills->count() }}</div><div class="text-xs text-ink-500">Навыков</div></div>
        </div>

        <div class="card p-6">
            <h3 class="font-display font-bold">Записи на курсы</h3>
            <div class="mt-4 space-y-2">
                @forelse($user->enrollments as $e)
                    <div class="flex items-center justify-between rounded-xl border border-paper-line p-3">
                        <span class="text-sm font-medium">{{ $e->course?->title }}</span>
                        <span class="text-xs font-semibold {{ $e->completed_at ? 'text-signal-600' : 'text-ink-500' }}">{{ $e->progress }}%{{ $e->completed_at ? ' · завершён' : '' }}</span>
                    </div>
                @empty
                    <p class="text-sm text-ink-400">Нет записей.</p>
                @endforelse
            </div>
        </div>

        <div class="card p-6">
            <h3 class="font-display font-bold">Сертификаты</h3>
            <div class="mt-4 space-y-2">
                @forelse($user->certificates as $cert)
                    <div class="flex items-center justify-between rounded-xl border border-paper-line p-3">
                        <span class="text-sm font-medium">{{ $cert->course_title }}</span>
                        <span class="font-mono text-xs text-ink-500">{{ $cert->certificate_number }}</span>
                    </div>
                @empty
                    <p class="text-sm text-ink-400">Нет сертификатов.</p>
                @endforelse
            </div>
        </div>

        @if($user->comments->isNotEmpty())
        <div class="card p-6">
            <h3 class="font-display font-bold">Последние комментарии</h3>
            <div class="mt-4 space-y-2">
                @foreach($user->comments->take(5) as $c)
                    <div class="rounded-xl border border-paper-line p-3">
                        <p class="text-sm text-ink-700">{{ $c->body }}</p>
                        <p class="mt-1 text-xs text-ink-400">{{ $c->course?->title }} · {{ $c->created_at->diffForHumans() }}</p>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
