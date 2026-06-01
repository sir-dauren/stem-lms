<!DOCTYPE html>
<html lang="ru" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Админка') — {{ $brandName }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Sora:wght@500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-paper">
<div x-data="{ nav: false }" class="lg:grid lg:grid-cols-[260px_1fr]">
    {{-- Sidebar --}}
    <aside x-show="nav || window.innerWidth >= 1024" x-cloak
           class="fixed inset-y-0 left-0 z-40 w-64 overflow-y-auto bg-ink-950 thin-scroll lg:static lg:block">
        <div class="bg-blueprint min-h-full p-5">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                <span class="grid h-9 w-9 place-items-center rounded-xl bg-signal-300 font-display text-lg font-extrabold text-ink-950">S</span>
                <span class="font-display text-lg font-extrabold text-white">{{ $brandName }}</span>
            </a>
            <p class="mt-1 text-xs font-semibold uppercase tracking-wider text-signal-300/70">Панель управления</p>

            @php
                $nav = [
                    ['admin.dashboard', '📊 Обзор', 'dashboard'],
                    ['admin.courses.index', '📚 Курсы', 'courses'],
                    ['admin.categories.index', '🗂 Категории', 'categories'],
                    ['admin.quizzes.index', '✎ Тесты', 'quizzes'],
                    ['admin.comments.index', '💬 Комментарии', 'comments'],
                    ['admin.users.index', '👥 Пользователи', 'users'],
                    ['admin.menus.index', '🧭 Меню', 'menus'],
                    ['admin.settings.edit', '⚙ Настройки', 'settings'],
                ];
            @endphp
            <nav class="mt-7 space-y-1">
                @foreach($nav as [$route, $label, $key])
                    @php($active = request()->routeIs('admin.'.$key.'*'))
                    <a href="{{ route($route) }}"
                       class="block rounded-xl px-4 py-2.5 text-sm font-semibold transition {{ $active ? 'bg-signal-300 text-ink-950' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </nav>

            <div class="mt-8 border-t border-white/10 pt-5">
                <a href="{{ route('dashboard') }}" class="block rounded-xl px-4 py-2.5 text-sm font-semibold text-white/70 hover:bg-white/10 hover:text-white">← На сайт</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="mt-1 block w-full rounded-xl px-4 py-2.5 text-left text-sm font-semibold text-coral-400 hover:bg-white/10">Выйти</button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Main --}}
    <div class="min-h-screen">
        <header class="sticky top-0 z-30 flex items-center justify-between border-b border-paper-line bg-white/90 px-4 py-3 backdrop-blur lg:px-8">
            <button @click="nav = !nav" class="grid h-10 w-10 place-items-center rounded-xl border border-paper-line lg:hidden">☰</button>
            <h1 class="font-display text-lg font-bold">@yield('heading', 'Панель управления')</h1>
            <div class="flex items-center gap-2 text-sm">
                <span class="grid h-9 w-9 place-items-center rounded-full bg-ink-950 text-xs font-bold text-signal-300">{{ auth()->user()->initials }}</span>
            </div>
        </header>

        <main class="p-4 lg:p-8">
            @if(session('status'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                     class="mb-6 flex items-center justify-between rounded-2xl border border-cyan-400/40 bg-cyan-400/10 px-5 py-3.5 text-sm font-semibold">
                    <span>{{ session('status') }}</span>
                    <button @click="show = false">✕</button>
                </div>
            @endif
            @if($errors->any())
                <div class="mb-6 rounded-2xl border border-coral-400/40 bg-coral-400/10 px-5 py-3.5 text-sm">
                    @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>
@stack('scripts')
</body>
</html>
