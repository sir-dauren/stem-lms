@extends('layouts.app')
@section('title', 'Результат теста')

@php($quiz = $attempt->quiz)
@php($review = $attempt->answers ?? [])

@section('content')
<div class="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8">
    {{-- Score banner --}}
    <div class="overflow-hidden rounded-3xl {{ $attempt->passed ? 'bg-blueprint' : 'bg-aurora' }} p-10 text-center text-white">
        <div class="text-5xl">{{ $attempt->passed ? '🎉' : '📚' }}</div>
        <h1 class="mt-4 font-display text-3xl font-extrabold">{{ $attempt->passed ? 'Тест пройден!' : 'Почти получилось' }}</h1>
        <p class="mt-2 text-white/70">{{ $quiz->title }}</p>
        <div class="mt-6 inline-flex items-baseline gap-2">
            <span class="font-display text-6xl font-extrabold {{ $attempt->passed ? 'text-signal-300' : 'text-coral-400' }}">{{ $attempt->score }}%</span>
        </div>
        <p class="mt-2 text-sm text-white/60">Проходной балл: {{ $quiz->passing_score }}%</p>
        <div class="mt-6 flex flex-wrap justify-center gap-3">
            <a href="{{ route('quizzes.show', $quiz) }}" class="btn btn-signal">Пройти заново</a>
            <a href="{{ route('quizzes.index') }}" class="btn btn-outline-light">Другие тесты</a>
        </div>
    </div>

    {{-- Review --}}
    <h2 class="mt-10 font-display text-2xl font-extrabold">Разбор ответов</h2>
    <div class="mt-5 space-y-4">
        @foreach($quiz->questions as $question)
            @php($r = $review[$question->id] ?? null)
            @php($isCorrect = $r['is_correct'] ?? false)
            <div class="card p-6">
                <div class="flex items-start gap-3">
                    <span class="grid h-6 w-6 shrink-0 place-items-center rounded-full text-xs font-bold {{ $isCorrect ? 'bg-signal-300 text-ink-950' : 'bg-coral-400 text-white' }}">{{ $isCorrect ? '✓' : '✕' }}</span>
                    <div class="flex-1">
                        <p class="font-display font-bold">{{ $loop->iteration }}. {{ $question->question }}</p>
                        <div class="mt-3 space-y-2">
                            @foreach($question->options as $option)
                                @php($given = in_array($option->id, $r['given'] ?? []))
                                @php($correct = $option->is_correct)
                                <div class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm
                                    {{ $correct ? 'bg-signal-300/15 text-signal-700' : ($given ? 'bg-coral-400/10 text-coral-600' : 'text-ink-600') }}">
                                    <span>
                                        @if($correct) ✓
                                        @elseif($given) ✕
                                        @else ◦ @endif
                                    </span>
                                    <span>{{ $option->text }}</span>
                                    @if($given && !$correct)<span class="ml-auto text-xs">ваш ответ</span>@endif
                                    @if($correct)<span class="ml-auto text-xs font-semibold">верно</span>@endif
                                </div>
                            @endforeach
                        </div>
                        @if($question->explanation)
                            <p class="mt-3 rounded-xl bg-paper px-4 py-3 text-sm text-ink-600"><span class="font-semibold">Пояснение:</span> {{ $question->explanation }}</p>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
