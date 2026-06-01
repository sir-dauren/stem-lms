@extends('layouts.app')
@section('title', 'Проверка знаний')

@section('content')
<div class="border-b border-paper-line bg-white">
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <h1 class="font-display text-3xl font-extrabold sm:text-4xl">Проверьте свои знания</h1>
        <p class="mt-2 text-ink-600">Тесты для самопроверки по дисциплинам STEM. Для прохождения нужно войти в аккаунт.</p>
    </div>
</div>

<div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
    @if($quizzes->isEmpty())
        <div class="card grid place-items-center p-16 text-center">
            <div class="text-4xl">🧪</div>
            <p class="mt-4 font-display text-lg font-bold">Тесты скоро появятся</p>
        </div>
    @else
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($quizzes as $quiz)
                <a href="{{ auth()->check() ? route('quizzes.show', $quiz) : route('login') }}"
                   class="group flex flex-col rounded-2xl border border-paper-line bg-white p-6 transition hover:-translate-y-1 hover:shadow-lg">
                    <div class="flex items-center justify-between">
                        <span class="text-3xl">✎</span>
                        <span class="chip bg-cyan-400/10 text-cyan-600">{{ $quiz->questions_count }} вопросов</span>
                    </div>
                    <h3 class="mt-4 font-display text-lg font-bold">{{ $quiz->title }}</h3>
                    @if($quiz->category)
                        <p class="mt-1 text-sm text-cyan-500">{{ $quiz->category->name }}</p>
                    @endif
                    @if($quiz->description)
                        <p class="mt-2 line-clamp-2 text-sm text-ink-600">{{ $quiz->description }}</p>
                    @endif
                    <div class="mt-auto flex items-center gap-4 pt-4 text-xs font-semibold text-ink-500">
                        <span>Проходной балл: {{ $quiz->passing_score }}%</span>
                        @if($quiz->time_limit_minutes)<span>⏱ {{ $quiz->time_limit_minutes }} мин</span>@endif
                    </div>
                </a>
            @endforeach
        </div>
        <div class="mt-10">{{ $quizzes->links() }}</div>
    @endif
</div>
@endsection
