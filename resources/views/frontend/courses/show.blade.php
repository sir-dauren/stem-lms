@extends('layouts.app')
@section('title', $course->title)
@section('subtitle', $course->subtitle ?: 'Курс')

@php
    $levels = ['beginner' => 'Начальный', 'intermediate' => 'Средний', 'advanced' => 'Продвинутый'];
@endphp

@section('content')
{{-- HERO --}}
<section class="bg-aurora text-white">
    <div class="relative mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="absolute inset-0 bg-blueprint opacity-30"></div>
        <div class="relative grid gap-10 lg:grid-cols-[1fr_380px]">
            <div>
                <nav class="flex flex-wrap items-center gap-2 text-sm text-white/60">
                    <a href="{{ route('courses.index') }}" class="hover:text-white">Курсы</a>
                    @if($course->category)
                        @foreach($course->category->breadcrumb() as $crumb)
                            <span>/</span>
                            <a href="{{ route('courses.index', ['category' => $crumb->slug]) }}" class="hover:text-white">{{ $crumb->name }}</a>
                        @endforeach
                    @endif
                </nav>

                <h1 class="mt-4 font-display text-3xl font-extrabold leading-tight sm:text-4xl">{{ $course->title }}</h1>
                @if($course->subtitle)
                    <p class="mt-3 max-w-2xl text-lg text-white/70">{{ $course->subtitle }}</p>
                @endif

                <div class="mt-5 flex flex-wrap items-center gap-3 text-sm">
                    <span class="chip bg-signal-300/15 text-signal-300">{{ $levels[$course->level] ?? $course->level }}</span>
                    <span class="chip bg-white/10 text-white/80">{{ $course->lessons_count }} уроков</span>
                    <span class="chip bg-white/10 text-white/80">{{ minutes_to_label($course->duration_minutes) }}</span>
                    <span class="chip bg-white/10 text-white/80">👥 {{ $course->enrollments_count }}</span>
                    <span class="chip bg-white/10 text-white/80">❤ {{ $course->likes_count }}</span>
                </div>

                @if($course->instructor_name)
                    <div class="mt-6 flex items-center gap-3">
                        <div class="grid h-11 w-11 place-items-center rounded-full bg-signal-300 font-display font-bold text-ink-950">{{ initials($course->instructor_name) }}</div>
                        <div>
                            <p class="font-semibold">{{ $course->instructor_name }}</p>
                            <p class="text-sm text-white/60">{{ $course->instructor_title }}</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Enroll card --}}
            <div class="lg:row-span-2">
                <div class="overflow-hidden rounded-2xl border border-white/10 bg-white text-ink-900 shadow-2xl">
                    <div class="aspect-video bg-ink-900">
                        @if($course->thumbnail)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($course->thumbnail) }}" class="h-full w-full object-cover" alt="">
                        @else
                            <div class="flex h-full items-center justify-center bg-aurora"><span class="font-display text-4xl font-extrabold text-signal-300/80">{{ mb_strtoupper(mb_substr($course->title,0,2)) }}</span></div>
                        @endif
                    </div>
                    <div class="p-6">
                        @auth
                            @php($firstLesson = $course->sections->flatMap->lessons->first())
                            @if($enrolled)
                                <a href="{{ $firstLesson ? route('learn.lesson', [$course, $firstLesson]) : route('courses.show', $course) }}" class="btn btn-signal w-full py-3 text-base">Продолжить обучение</a>
                            @else
                                <form method="POST" action="{{ route('enroll', $course) }}">
                                    @csrf
                                    <button class="btn btn-signal w-full py-3 text-base">Записаться бесплатно</button>
                                </form>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn btn-signal w-full py-3 text-base">Войти и записаться</a>
                            <p class="mt-2 text-center text-xs text-ink-500">Доступ к урокам и сертификату — только после входа.</p>
                        @endauth

                        <div x-data="likeBtn({{ $course->likes_count }}, {{ $liked ? 'true' : 'false' }})" class="mt-3">
                            @auth
                                <button @click="toggle" class="btn btn-ghost w-full" :class="liked && 'border-coral-400 text-coral-500'">
                                    <span x-text="liked ? '❤' : '🤍'"></span> <span x-text="count"></span> · Нравится
                                </button>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-ghost w-full">🤍 {{ $course->likes_count }} · Нравится</a>
                            @endauth
                        </div>

                        <dl class="mt-5 space-y-2.5 text-sm">
                            <div class="flex justify-between"><dt class="text-ink-500">Язык</dt><dd class="font-semibold">{{ $course->language ?: 'Русский' }}</dd></div>
                            <div class="flex justify-between"><dt class="text-ink-500">Уровень</dt><dd class="font-semibold">{{ $levels[$course->level] ?? $course->level }}</dd></div>
                            <div class="flex justify-between"><dt class="text-ink-500">Уроков</dt><dd class="font-semibold">{{ $course->lessons_count }}</dd></div>
                            <div class="flex justify-between"><dt class="text-ink-500">Сертификат</dt><dd class="font-semibold text-cyan-500">Да</dd></div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="mx-auto max-w-7xl gap-10 px-4 py-12 sm:px-6 lg:grid lg:grid-cols-[1fr_380px] lg:px-8">
    <div class="space-y-12">
        {{-- About --}}
        @if($course->description)
            <section>
                <h2 class="font-display text-2xl font-extrabold">О курсе</h2>
                <div class="prose-lesson mt-4 max-w-none">{!! nl2br(e($course->description)) !!}</div>
            </section>
        @endif

        {{-- Outcomes --}}
        @if(count($course->outcomes_list))
            <section>
                <h2 class="font-display text-2xl font-extrabold">Чему вы научитесь</h2>
                <ul class="mt-4 grid gap-3 sm:grid-cols-2">
                    @foreach($course->outcomes_list as $item)
                        <li class="flex gap-2.5"><span class="mt-0.5 text-signal-500">✓</span><span class="text-ink-700">{{ $item }}</span></li>
                    @endforeach
                </ul>
            </section>
        @endif

        {{-- Skills --}}
        @if($course->skills->isNotEmpty())
            <section>
                <h2 class="font-display text-2xl font-extrabold">Приобретаемые навыки</h2>
                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach($course->skills as $skill)
                        <span class="chip bg-cyan-400/10 text-cyan-600">{{ $skill->name }}</span>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Curriculum --}}
        <section>
            <h2 class="font-display text-2xl font-extrabold">Программа курса</h2>
            <p class="mt-1 text-sm text-ink-500">{{ $course->sections->count() }} разделов · {{ $course->lessons_count }} уроков</p>
            <div class="mt-5 space-y-3" x-data="{ open: 0 }">
                @foreach($course->sections as $section)
                    <div class="overflow-hidden rounded-2xl border border-paper-line bg-white">
                        <button @click="open === {{ $loop->index }} ? open = null : open = {{ $loop->index }}" class="flex w-full items-center justify-between gap-3 px-5 py-4 text-left">
                            <span class="font-display font-bold">{{ $loop->iteration }}. {{ $section->title }}</span>
                            <span class="flex items-center gap-3 text-sm text-ink-400">
                                <span>{{ $section->lessons->count() }} ур.</span>
                                <svg class="h-4 w-4 transition" :class="open === {{ $loop->index }} && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="m6 9 6 6 6-6"/></svg>
                            </span>
                        </button>
                        <div x-show="open === {{ $loop->index }}" x-collapse>
                            <ul class="divide-y divide-paper-line border-t border-paper-line">
                                @foreach($section->lessons as $lesson)
                                    <li class="flex items-center gap-3 px-5 py-3 text-sm">
                                        <span class="text-ink-400">
                                            @if($lesson->type === 'video') ▶
                                            @elseif($lesson->type === 'quiz') ✎
                                            @else ☰ @endif
                                        </span>
                                        <span class="flex-1 text-ink-700">{{ $lesson->title }}</span>
                                        @if($lesson->is_preview)
                                            <a href="{{ route('learn.lesson', [$course, $lesson]) }}" class="chip bg-signal-300/20 text-signal-600">Превью</a>
                                        @elseif($lesson->duration_minutes)
                                            <span class="text-ink-400">{{ $lesson->duration_minutes }} мин</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Requirements --}}
        @if(count($course->requirements_list))
            <section>
                <h2 class="font-display text-2xl font-extrabold">Требования</h2>
                <ul class="mt-4 space-y-2">
                    @foreach($course->requirements_list as $item)
                        <li class="flex gap-2.5"><span class="text-ink-400">•</span><span class="text-ink-700">{{ $item }}</span></li>
                    @endforeach
                </ul>
            </section>
        @endif

        {{-- Linked tests --}}
        @if($course->quizzes->isNotEmpty())
            <section>
                <h2 class="font-display text-2xl font-extrabold">Тесты по курсу</h2>
                <div class="mt-4 space-y-3">
                    @foreach($course->quizzes as $quiz)
                        <a href="{{ auth()->check() ? route('quizzes.show', $quiz) : route('login') }}" class="flex items-center justify-between rounded-2xl border border-paper-line bg-white px-5 py-4 transition hover:shadow-md">
                            <div>
                                <p class="font-display font-bold">{{ $quiz->title }}</p>
                                <p class="text-sm text-ink-500">Проходной балл: {{ $quiz->passing_score }}%</p>
                            </div>
                            <span class="text-cyan-500">→</span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Comments --}}
        <section id="comments">
            <h2 class="font-display text-2xl font-extrabold">Обсуждение ({{ $course->comments_count }})</h2>

            @auth
                <form method="POST" action="{{ route('comment.store', $course) }}" class="mt-5">
                    @csrf
                    <textarea name="body" rows="3" required placeholder="Поделитесь вопросом или отзывом…" class="input"></textarea>
                    <div class="mt-2 flex justify-end"><button class="btn btn-ink">Отправить</button></div>
                </form>
            @else
                <p class="mt-4 rounded-2xl border border-paper-line bg-white px-5 py-4 text-sm text-ink-600"><a href="{{ route('login') }}" class="font-semibold text-cyan-500">Войдите</a>, чтобы оставить комментарий.</p>
            @endauth

            <div class="mt-8 space-y-6">
                @forelse($course->comments as $comment)
                    @if(!$comment->is_hidden)
                        <div class="flex gap-3">
                            <div class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-ink-950 text-xs font-bold text-signal-300">{{ initials($comment->user->name) }}</div>
                            <div class="flex-1">
                                <div class="rounded-2xl border border-paper-line bg-white px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold">{{ $comment->user->name }}</span>
                                        @if($comment->is_staff)<span class="chip bg-signal-300/20 text-signal-600">Команда</span>@endif
                                        <span class="text-xs text-ink-400">{{ $comment->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="mt-1.5 text-ink-700">{{ $comment->body }}</p>
                                </div>
                                @foreach($comment->replies as $reply)
                                    @if(!$reply->is_hidden)
                                        <div class="ml-6 mt-3 flex gap-3">
                                            <div class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-ink-700 text-[10px] font-bold text-signal-300">{{ initials($reply->user->name) }}</div>
                                            <div class="flex-1 rounded-2xl border border-paper-line bg-paper px-4 py-3">
                                                <div class="flex items-center gap-2">
                                                    <span class="font-semibold">{{ $reply->user->name }}</span>
                                                    @if($reply->is_staff)<span class="chip bg-signal-300/20 text-signal-600">Команда</span>@endif
                                                    <span class="text-xs text-ink-400">{{ $reply->created_at->diffForHumans() }}</span>
                                                </div>
                                                <p class="mt-1.5 text-ink-700">{{ $reply->body }}</p>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                @empty
                    <p class="text-sm text-ink-500">Пока нет комментариев. Будьте первым!</p>
                @endforelse
            </div>
        </section>
    </div>

    {{-- Related --}}
    <aside class="mt-12 lg:mt-0">
        @if($related->isNotEmpty())
            <h3 class="font-display text-lg font-bold">Похожие курсы</h3>
            <div class="mt-4 space-y-4">
                @foreach($related as $rc)
                    <a href="{{ route('courses.show', $rc) }}" class="flex gap-3 rounded-2xl border border-paper-line bg-white p-3 transition hover:shadow-md">
                        <div class="grid h-16 w-16 shrink-0 place-items-center overflow-hidden rounded-xl bg-aurora">
                            @if($rc->thumbnail)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($rc->thumbnail) }}" class="h-full w-full object-cover" alt="">
                            @else
                                <span class="font-display font-bold text-signal-300">{{ mb_substr($rc->title,0,1) }}</span>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <p class="line-clamp-2 text-sm font-semibold">{{ $rc->title }}</p>
                            <p class="mt-1 text-xs text-ink-500">{{ $rc->lessons_count }} уроков</p>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </aside>
</div>

@push('scripts')
<script>
function likeBtn(initial, liked) {
    return {
        count: initial,
        liked: liked,
        toggle() {
            fetch('{{ route('like.toggle', $course) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json',
                },
            }).then(r => r.json()).then(d => { this.liked = d.liked; this.count = d.count; });
        }
    }
}
</script>
@endpush
@endsection
