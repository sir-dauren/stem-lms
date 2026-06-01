@extends('layouts.app')
@section('title', 'О платформе')

@section('content')
<section class="bg-aurora text-white">
    <div class="relative mx-auto max-w-4xl px-4 py-20 sm:px-6 lg:px-8">
        <div class="absolute inset-0 bg-blueprint opacity-40"></div>
        <div class="relative">
            <span class="chip border border-signal-300/30 bg-signal-300/10 text-signal-300">О платформе</span>
            <h1 class="mt-6 font-display text-4xl font-extrabold sm:text-5xl">{{ $brandName }}</h1>
            <p class="mt-5 max-w-2xl text-lg leading-relaxed text-white/70">
                {{ setting('tagline', 'Учись строить будущее — наука, технологии, инженерия, математика.') }}
            </p>
        </div>
    </div>
</section>

<section class="mx-auto max-w-4xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="prose-lesson">
        <p class="text-lg">{{ $brandName }} — это современная образовательная платформа, посвящённая дисциплинам STEM: науке, технологиям, инженерии и математике. Мы помогаем учиться в удобном темпе и подтверждать знания именными сертификатами.</p>
    </div>

    <div class="mt-12 grid gap-6 sm:grid-cols-3">
        @foreach([
            ['🎓','Структурированные курсы','Видео, статьи и материалы для скачивания — всё в одном месте.'],
            ['🧠','Проверка знаний','Тесты после курсов и отдельные тесты для самопроверки.'],
            ['📜','Сертификаты','Именной сертификат за каждый завершённый курс с возможностью проверки.'],
        ] as [$icon,$title,$text])
            <div class="card p-6">
                <div class="text-3xl">{{ $icon }}</div>
                <h3 class="mt-4 font-display text-lg font-bold">{{ $title }}</h3>
                <p class="mt-2 text-sm text-ink-600">{{ $text }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-12 rounded-3xl bg-blueprint p-10 text-center text-white">
        <h2 class="font-display text-2xl font-extrabold">Начните учиться сегодня</h2>
        <p class="mt-3 text-white/70">Тысячи материалов по STEM ждут вас.</p>
        <a href="{{ route('courses.index') }}" class="btn btn-signal mt-6 px-7 py-3">Открыть каталог</a>
    </div>
</section>
@endsection
