@extends('layouts.app')
@section('title', $brandName)
@section('subtitle', 'Платформа STEM-образования')

@section('content')
{{-- HERO --}}
<section class="relative overflow-hidden bg-aurora text-white">
    <div class="absolute inset-0 bg-blueprint opacity-40"></div>
    <div class="relative mx-auto grid max-w-7xl gap-12 px-4 py-20 sm:px-6 lg:grid-cols-2 lg:py-28 lg:px-8">
        <div class="reveal">
            <span class="chip border border-signal-300/30 bg-signal-300/10 text-signal-300">Наука · Технологии · Инженерия · Математика</span>
            <h1 class="mt-6 font-display text-4xl font-extrabold leading-[1.05] sm:text-5xl lg:text-6xl">
                Учись строить <span class="text-signal-300">будущее</span>
            </h1>
            <p class="mt-5 max-w-md text-lg leading-relaxed text-white/70">
                {{ setting('tagline', 'Современные курсы, практические материалы, тесты для проверки знаний и именной сертификат за каждый пройденный курс.') }}
            </p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('courses.index') }}" class="btn btn-signal px-7 py-3 text-base">Выбрать курс</a>
                <a href="{{ route('quizzes.index') }}" class="btn btn-outline-light px-7 py-3 text-base">Проверить знания</a>
            </div>
        </div>

        <div class="relative hidden lg:block">
            <div class="grid grid-cols-2 gap-4">
                @foreach($newest->take(4) as $c)
                    <a href="{{ route('courses.show', $c) }}" class="reveal rounded-2xl border border-white/10 bg-white/5 p-5 backdrop-blur transition hover:bg-white/10" style="animation-delay: {{ $loop->index * 0.08 }}s">
                        <div class="grid h-10 w-10 place-items-center rounded-xl bg-signal-300/15 font-display font-bold text-signal-300">{{ mb_substr($c->title, 0, 1) }}</div>
                        <p class="mt-3 line-clamp-2 text-sm font-semibold">{{ $c->title }}</p>
                        <p class="mt-1 text-xs text-white/50">{{ $c->lessons_count }} уроков</p>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Stats bar --}}
    <div class="relative border-t border-white/10">
        <div class="mx-auto grid max-w-7xl grid-cols-2 divide-x divide-white/10 px-4 sm:px-6 md:grid-cols-4 lg:px-8">
            @foreach([['courses','Курсов'],['learners','Учеников'],['certs','Сертификатов'],['quizzes','Тестов']] as [$k,$label])
                <div class="px-4 py-7 text-center">
                    <div class="font-display text-3xl font-extrabold text-signal-300">{{ number_format($stats[$k], 0, '.', ' ') }}</div>
                    <div class="mt-1 text-xs font-semibold uppercase tracking-wider text-white/50">{{ $label }}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CATEGORIES --}}
@if($categories->isNotEmpty())
<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="flex items-end justify-between">
        <h2 class="font-display text-2xl font-extrabold sm:text-3xl">Направления обучения</h2>
        <a href="{{ route('courses.index') }}" class="text-sm font-semibold text-cyan-500 hover:underline">Все курсы →</a>
    </div>
    <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach($categories as $cat)
            <a href="{{ route('courses.index', ['category' => $cat->slug]) }}"
               class="group rounded-2xl border border-paper-line bg-white p-6 transition hover:-translate-y-1 hover:shadow-lg"
               style="border-top: 3px solid {{ $cat->color ?: '#c2e72f' }}">
                <div class="text-3xl">{{ $cat->icon ?: '🧪' }}</div>
                <h3 class="mt-4 font-display text-lg font-bold">{{ $cat->name }}</h3>
                <p class="mt-1 text-sm text-ink-500">{{ $cat->courses_count }} курсов</p>
            </a>
        @endforeach
    </div>
</section>
@endif

{{-- FEATURED --}}
@if($featured->isNotEmpty())
<section class="bg-white py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <h2 class="font-display text-2xl font-extrabold sm:text-3xl">Рекомендуемые курсы</h2>
        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($featured as $course)
                <x-course-card :course="$course" />
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- NEWEST --}}
@if($newest->isNotEmpty())
<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="flex items-end justify-between">
        <h2 class="font-display text-2xl font-extrabold sm:text-3xl">Новые поступления</h2>
        <a href="{{ route('courses.index', ['sort' => 'newest']) }}" class="text-sm font-semibold text-cyan-500 hover:underline">Смотреть все →</a>
    </div>
    <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        @foreach($newest as $course)
            <x-course-card :course="$course" />
        @endforeach
    </div>
</section>
@endif

{{-- CTA --}}
<section class="mx-auto max-w-7xl px-4 pb-20 sm:px-6 lg:px-8">
    <div class="overflow-hidden rounded-3xl bg-blueprint px-8 py-14 text-center text-white sm:px-16">
        <h2 class="font-display text-3xl font-extrabold sm:text-4xl">Готовы прокачать навыки?</h2>
        <p class="mx-auto mt-4 max-w-xl text-white/70">Зарегистрируйтесь бесплатно, проходите курсы в своём темпе и получайте именные сертификаты.</p>
        @guest
            <a href="{{ route('register') }}" class="btn btn-signal mt-8 px-8 py-3 text-base">Создать аккаунт</a>
        @else
            <a href="{{ route('courses.index') }}" class="btn btn-signal mt-8 px-8 py-3 text-base">К каталогу курсов</a>
        @endguest
    </div>
</section>
@endsection
