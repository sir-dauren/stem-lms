@props(['course'])

@php
    $levels = ['beginner' => 'Начальный', 'intermediate' => 'Средний', 'advanced' => 'Продвинутый'];
@endphp

<a href="{{ route('courses.show', $course) }}"
   class="group flex flex-col overflow-hidden rounded-2xl border border-paper-line bg-white transition-all duration-200 hover:-translate-y-1 hover:shadow-xl">
    <div class="relative aspect-[16/10] overflow-hidden bg-ink-900">
        @if($course->thumbnail)
            <img src="{{ \Illuminate\Support\Facades\Storage::url($course->thumbnail) }}" alt="{{ $course->title }}"
                 class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
        @else
            <div class="flex h-full w-full items-center justify-center bg-aurora">
                <span class="font-display text-3xl font-extrabold text-signal-300/80">{{ mb_strtoupper(mb_substr($course->title, 0, 2)) }}</span>
            </div>
        @endif
        @if($course->is_featured)
            <span class="absolute left-3 top-3 chip bg-signal-300 text-ink-950">★ Топ</span>
        @endif
    </div>

    <div class="flex flex-1 flex-col p-5">
        <div class="mb-2 flex items-center gap-2 text-xs font-semibold">
            @if($course->category)
                <span class="text-cyan-500">{{ $course->category->name }}</span>
            @endif
            <span class="text-ink-400">·</span>
            <span class="text-ink-500">{{ $levels[$course->level] ?? $course->level }}</span>
        </div>

        <h3 class="font-display text-lg font-bold leading-snug text-ink-950 group-hover:text-ink-700">{{ $course->title }}</h3>
        @if($course->subtitle)
            <p class="mt-1.5 line-clamp-2 text-sm text-ink-600">{{ $course->subtitle }}</p>
        @endif

        <div class="mt-auto flex items-center gap-4 pt-4 text-xs font-semibold text-ink-500">
            <span>{{ $course->lessons_count ?? $course->lessons()->count() }} уроков</span>
            @isset($course->enrollments_count)
                <span>{{ $course->enrollments_count }} учеников</span>
            @endisset
            @if($course->duration_minutes)
                <span class="ml-auto">{{ minutes_to_label($course->duration_minutes) }}</span>
            @endif
        </div>
    </div>
</a>
