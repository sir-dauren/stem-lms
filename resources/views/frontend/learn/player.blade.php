@extends('layouts.app')
@section('title', $lesson->title)
@section('subtitle', $course->title)

@php($completed = in_array($lesson->id, $completedIds))
@php($progress = $enrollment?->progress ?? 0)

@section('content')
<div x-data="{ sidebar: true }" class="mx-auto max-w-[1600px] lg:grid lg:grid-cols-[360px_1fr]">
    {{-- Sidebar curriculum --}}
    <aside x-show="sidebar" class="border-r border-paper-line bg-white lg:sticky lg:top-[57px] lg:h-[calc(100vh-57px)] lg:overflow-y-auto thin-scroll">
        <div class="border-b border-paper-line p-5">
            <a href="{{ route('courses.show', $course) }}" class="text-sm font-semibold text-cyan-500 hover:underline">← {{ $course->title }}</a>
            <div class="mt-4">
                <div class="flex items-center justify-between text-xs font-semibold text-ink-500">
                    <span>Прогресс</span><span>{{ $progress }}%</span>
                </div>
                <div class="mt-1.5 h-2 overflow-hidden rounded-full bg-paper-line">
                    <div class="h-full rounded-full bg-signal-400 transition-all" style="width: {{ $progress }}%"></div>
                </div>
            </div>
        </div>

        <nav class="p-3">
            @foreach($course->sections as $section)
                <div class="mb-2">
                    <p class="px-3 py-2 font-display text-sm font-bold text-ink-900">{{ $loop->iteration }}. {{ $section->title }}</p>
                    <ul class="space-y-0.5">
                        @foreach($section->lessons as $l)
                            @php($isCurrent = $l->id === $lesson->id)
                            @php($isDone = in_array($l->id, $completedIds))
                            <li>
                                <a href="{{ route('learn.lesson', [$course, $l]) }}"
                                   class="flex items-center gap-2.5 rounded-xl px-3 py-2 text-sm transition {{ $isCurrent ? 'bg-ink-950 text-white' : 'hover:bg-paper' }}">
                                    <span class="grid h-5 w-5 shrink-0 place-items-center rounded-full text-[10px] {{ $isDone ? 'bg-signal-400 text-ink-950' : ($isCurrent ? 'bg-white/20 text-white' : 'bg-paper-line text-ink-500') }}">
                                        {{ $isDone ? '✓' : ($l->type === 'video' ? '▶' : ($l->type === 'quiz' ? '✎' : '☰')) }}
                                    </span>
                                    <span class="flex-1 {{ $isCurrent ? 'font-semibold' : 'text-ink-700' }}">{{ $l->title }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach

            @if($course->quizzes->isNotEmpty())
                <div class="mt-3 border-t border-paper-line pt-3">
                    <p class="px-3 py-2 font-display text-sm font-bold">Тесты</p>
                    @foreach($course->quizzes as $quiz)
                        <a href="{{ route('quizzes.show', $quiz) }}" class="flex items-center gap-2.5 rounded-xl px-3 py-2 text-sm text-ink-700 hover:bg-paper">
                            <span class="grid h-5 w-5 place-items-center rounded-full bg-cyan-400/20 text-[10px] text-cyan-600">✎</span>
                            <span class="flex-1">{{ $quiz->title }}</span>
                        </a>
                    @endforeach
                </div>
            @endif
        </nav>
    </aside>

    {{-- Main content --}}
    <div class="min-h-[calc(100vh-57px)]">
        {{-- Video / media --}}
        @if($lesson->type === 'video')
            <div class="bg-ink-950">
                <div class="mx-auto max-w-5xl">
                    @if($lesson->embedUrl())
                        <div class="aspect-video">
                            <iframe src="{{ $lesson->embedUrl() }}" class="h-full w-full" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                    @elseif($lesson->video_path)
                        <video controls class="aspect-video w-full bg-black">
                            <source src="{{ \Illuminate\Support\Facades\Storage::url($lesson->video_path) }}">
                        </video>
                    @else
                        <div class="flex aspect-video items-center justify-center text-white/40">Видео не добавлено</div>
                    @endif
                </div>
            </div>
        @endif

        <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6">
            <div class="flex items-center gap-2 text-xs font-semibold text-ink-500">
                <button @click="sidebar = !sidebar" class="rounded-lg border border-paper-line bg-white px-2.5 py-1.5">☰ Программа</button>
                <span class="chip bg-paper text-ink-600">{{ $lesson->section->title }}</span>
            </div>

            <h1 class="mt-4 font-display text-3xl font-extrabold">{{ $lesson->title }}</h1>

            @if($lesson->type === 'article' || $lesson->content)
                <article class="prose-lesson mt-6 max-w-none">
                    {!! $lesson->content !!}
                </article>
            @endif

            {{-- Materials --}}
            @if($lesson->materials->isNotEmpty())
                <section class="mt-10">
                    <h2 class="font-display text-lg font-bold">Материалы урока</h2>
                    <div class="mt-3 space-y-2">
                        @foreach($lesson->materials as $material)
                            <a href="{{ \Illuminate\Support\Facades\Storage::url($material->file_path) }}" download="{{ $material->original_name }}"
                               class="flex items-center gap-3 rounded-2xl border border-paper-line bg-white px-4 py-3 transition hover:shadow-md">
                                <span class="text-2xl">{{ $material->icon }}</span>
                                <div class="flex-1 min-w-0">
                                    <p class="truncate font-semibold">{{ $material->title }}</p>
                                    <p class="text-xs text-ink-500">{{ strtoupper($material->extension) }} · {{ $material->human_size }}</p>
                                </div>
                                <span class="text-cyan-500">↓</span>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- Complete + nav --}}
            <div class="mt-12 flex flex-col items-center gap-4 border-t border-paper-line pt-8">
                @if($enrollment)
                <div x-data="completeLesson({{ $completed ? 'true' : 'false' }})" class="w-full">
                    <button @click="complete" :disabled="done"
                            class="btn w-full py-3 text-base"
                            :class="done ? 'bg-signal-100 text-signal-600' : 'btn-signal'">
                        <span x-show="!done">Отметить как пройденный</span>
                        <span x-show="done">✓ Урок пройден</span>
                    </button>
                    <p x-show="justFinished" x-cloak class="mt-3 rounded-2xl border border-signal-400/40 bg-signal-50 px-4 py-3 text-center text-sm font-semibold text-signal-600">
                        🎉 Курс пройден! Сертификат уже в личном кабинете.
                    </p>
                </div>
                @else
                <form method="POST" action="{{ route('enroll', $course) }}" class="w-full">
                    @csrf
                    <button class="btn btn-signal w-full py-3 text-base">Записаться, чтобы продолжить</button>
                </form>
                @endif

                <div class="flex w-full items-center justify-between gap-3">
                    @if($prev)
                        <a href="{{ route('learn.lesson', [$course, $prev]) }}" class="btn btn-ghost">← Назад</a>
                    @else
                        <span></span>
                    @endif
                    @if($next)
                        <a href="{{ route('learn.lesson', [$course, $next]) }}" class="btn btn-ink">Далее →</a>
                    @else
                        <a href="{{ route('dashboard') }}" class="btn btn-ink">Завершить →</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function completeLesson(alreadyDone) {
    return {
        done: alreadyDone,
        justFinished: false,
        complete() {
            if (this.done) return;
            fetch('{{ route('learn.complete', [$course, $lesson]) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json',
                },
            }).then(r => r.json()).then(d => {
                this.done = true;
                if (d.completed) this.justFinished = true;
                setTimeout(() => window.location.reload(), d.completed ? 2500 : 600);
            });
        }
    }
}
</script>
@endpush
@endsection
