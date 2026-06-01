@extends('layouts.app')
@section('title', $quiz->title)

@section('content')
<div class="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8">
    <a href="{{ route('quizzes.index') }}" class="text-sm font-semibold text-cyan-500 hover:underline">← Все тесты</a>

    <div class="mt-4">
        <h1 class="font-display text-3xl font-extrabold">{{ $quiz->title }}</h1>
        @if($quiz->description)<p class="mt-2 text-ink-600">{{ $quiz->description }}</p>@endif
        <div class="mt-4 flex flex-wrap gap-2 text-sm">
            <span class="chip bg-paper text-ink-600">{{ $quiz->questions->count() }} вопросов</span>
            <span class="chip bg-paper text-ink-600">Проходной балл: {{ $quiz->passing_score }}%</span>
            @if($quiz->time_limit_minutes)<span class="chip bg-paper text-ink-600">⏱ {{ $quiz->time_limit_minutes }} мин</span>@endif
        </div>

        @if($lastAttempt)
            <div class="mt-4 rounded-2xl border border-paper-line bg-white px-5 py-4">
                <p class="text-sm">Прошлая попытка: <span class="font-bold {{ $lastAttempt->passed ? 'text-signal-600' : 'text-coral-500' }}">{{ $lastAttempt->score }}%</span> — {{ $lastAttempt->passed ? 'пройдено' : 'не пройдено' }}.
                <a href="{{ route('quizzes.result', $lastAttempt) }}" class="font-semibold text-cyan-500 hover:underline">Посмотреть результат</a></p>
            </div>
        @endif
    </div>

    @if($quiz->questions->isEmpty())
        <p class="mt-8 card p-8 text-center text-ink-500">В этом тесте пока нет вопросов.</p>
    @else
        <form method="POST" action="{{ route('quizzes.submit', $quiz) }}" class="mt-8" x-data="{ answered: {} }">
            @csrf
            <div class="space-y-6">
                @foreach($quiz->questions as $question)
                    <div class="card p-6">
                        <p class="font-display font-bold">{{ $loop->iteration }}. {{ $question->question }}</p>
                        @if($question->type === 'multiple')
                            <p class="mt-1 text-xs font-semibold text-cyan-500">Выберите один или несколько вариантов</p>
                        @endif
                        <div class="mt-4 space-y-2.5">
                            @foreach($question->options as $option)
                                <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-paper-line bg-paper px-4 py-3 transition hover:border-cyan-400 has-[:checked]:border-cyan-500 has-[:checked]:bg-cyan-400/5">
                                    <input type="{{ $question->type === 'multiple' ? 'checkbox' : 'radio' }}"
                                           name="answers[{{ $question->id }}]{{ $question->type === 'multiple' ? '[]' : '' }}"
                                           value="{{ $option->id }}"
                                           class="h-4 w-4 accent-cyan-500">
                                    <span class="text-ink-800">{{ $option->text }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 flex justify-end">
                <button class="btn btn-signal px-8 py-3 text-base">Завершить тест</button>
            </div>
        </form>
    @endif
</div>
@endsection
