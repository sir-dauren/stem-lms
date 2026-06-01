@extends('layouts.app')
@section('title', 'Каталог курсов')

@section('content')
<div class="border-b border-paper-line bg-white">
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <h1 class="font-display text-3xl font-extrabold sm:text-4xl">Каталог курсов</h1>
        <p class="mt-2 text-ink-600">Найдите курс по направлению, уровню или ключевому слову.</p>

        <form method="GET" action="{{ route('courses.index') }}" class="mt-6 flex flex-col gap-3 sm:flex-row">
            <div class="relative flex-1">
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Поиск курсов…"
                       class="input pl-11">
                <svg class="absolute left-3.5 top-3 h-5 w-5 text-ink-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m21 21-4.3-4.3"/></svg>
            </div>
            @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
            <select name="level" class="input sm:w-44" onchange="this.form.submit()">
                <option value="">Любой уровень</option>
                <option value="beginner" @selected(request('level')==='beginner')>Начальный</option>
                <option value="intermediate" @selected(request('level')==='intermediate')>Средний</option>
                <option value="advanced" @selected(request('level')==='advanced')>Продвинутый</option>
            </select>
            <select name="sort" class="input sm:w-44" onchange="this.form.submit()">
                <option value="newest" @selected(request('sort','newest')==='newest')>Сначала новые</option>
                <option value="popular" @selected(request('sort')==='popular')>Популярные</option>
                <option value="liked" @selected(request('sort')==='liked')>С лучшими оценками</option>
            </select>
            <button class="btn btn-ink">Найти</button>
        </form>
    </div>
</div>

<div class="mx-auto max-w-7xl gap-8 px-4 py-10 sm:px-6 lg:grid lg:grid-cols-[260px_1fr] lg:px-8">
    {{-- Sidebar --}}
    <aside class="mb-8 lg:mb-0">
        <div class="card p-5">
            <h3 class="font-display text-sm font-bold uppercase tracking-wider text-ink-500">Направления</h3>
            <ul class="mt-4 space-y-0.5">
                <li>
                    <a href="{{ route('courses.index', request()->except(['category','page'])) }}"
                       class="block rounded-lg px-3 py-1.5 text-sm transition {{ !request('category') ? 'bg-ink-950 font-bold text-signal-300' : 'text-ink-700 hover:bg-white' }}">
                        Все направления
                    </a>
                </li>
                @include('partials.category-tree', ['nodes' => $categories, 'depth' => 0])
            </ul>
        </div>
    </aside>

    {{-- Results --}}
    <div>
        <p class="mb-5 text-sm font-semibold text-ink-500">Найдено курсов: {{ $courses->total() }}</p>
        @if($courses->isEmpty())
            <div class="card grid place-items-center p-16 text-center">
                <div class="text-4xl">🔍</div>
                <p class="mt-4 font-display text-lg font-bold">Ничего не найдено</p>
                <p class="mt-1 text-sm text-ink-500">Попробуйте изменить фильтры или поисковый запрос.</p>
            </div>
        @else
            <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
                @foreach($courses as $course)
                    <x-course-card :course="$course" />
                @endforeach
            </div>
            <div class="mt-10">{{ $courses->links() }}</div>
        @endif
    </div>
</div>
@endsection
