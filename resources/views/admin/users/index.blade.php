@extends('layouts.admin')
@section('title', 'Пользователи')
@section('heading', 'Пользователи')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-4">
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.users.index') }}" class="chip {{ !request('filter') ? 'bg-ink-950 text-white' : 'bg-white text-ink-600' }} px-4 py-2">Все</a>
        <a href="{{ route('admin.users.index', ['filter' => 'blocked']) }}" class="chip {{ request('filter')==='blocked' ? 'bg-ink-950 text-white' : 'bg-white text-ink-600' }} px-4 py-2">Заблокированные</a>
        <a href="{{ route('admin.users.index', ['filter' => 'admins']) }}" class="chip {{ request('filter')==='admins' ? 'bg-ink-950 text-white' : 'bg-white text-ink-600' }} px-4 py-2">Администраторы</a>
    </div>
    <form method="GET" class="flex gap-2">
        @if(request('filter'))<input type="hidden" name="filter" value="{{ request('filter') }}">@endif
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Имя или email…" class="input">
        <button class="btn btn-ghost">Найти</button>
    </form>
</div>

<div class="card mt-6 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="border-b border-paper-line bg-paper text-left text-xs font-bold uppercase tracking-wider text-ink-500">
            <tr>
                <th class="px-5 py-3">Пользователь</th>
                <th class="px-5 py-3 text-center">Курсы</th>
                <th class="px-5 py-3 text-center">Серт.</th>
                <th class="px-5 py-3 text-center">Коммент.</th>
                <th class="px-5 py-3 text-center">Роль</th>
                <th class="px-5 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-paper-line">
            @forelse($users as $user)
                <tr class="hover:bg-paper">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <span class="grid h-9 w-9 place-items-center rounded-full bg-ink-950 text-xs font-bold text-signal-300">{{ $user->initials }}</span>
                            <div>
                                <a href="{{ route('admin.users.show', $user) }}" class="font-semibold hover:text-cyan-500">{{ $user->name }}</a>
                                @if($user->is_blocked)<span class="chip bg-coral-400/15 text-coral-500">заблокирован</span>@endif
                                <p class="text-xs text-ink-500">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3 text-center">{{ $user->enrollments_count }}</td>
                    <td class="px-5 py-3 text-center">{{ $user->certificates_count }}</td>
                    <td class="px-5 py-3 text-center">{{ $user->comments_count }}</td>
                    <td class="px-5 py-3 text-center">
                        @if($user->isAdmin())<span class="chip bg-signal-300/20 text-signal-600">admin</span>@else<span class="text-ink-500">user</span>@endif
                    </td>
                    <td class="px-5 py-3 text-right">
                        <a href="{{ route('admin.users.show', $user) }}" class="text-xs font-semibold text-cyan-500">профиль</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-5 py-12 text-center text-ink-500">Пользователи не найдены.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $users->links() }}</div>
@endsection
