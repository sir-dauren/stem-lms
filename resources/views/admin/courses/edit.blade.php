@extends('layouts.admin')
@section('title', 'Редактирование курса')
@section('heading', 'Редактирование: '.$course->title)

@section('content')
<div x-data="{ tab: 'details' }">
    {{-- Tab bar --}}
    <div class="flex flex-wrap gap-1.5 rounded-2xl border border-paper-line bg-white p-1.5">
        @foreach(['details' => 'Детали', 'curriculum' => 'Программа', 'materials' => 'Материалы', 'tests' => 'Тесты'] as $key => $label)
            <button @click="tab = '{{ $key }}'"
                    :class="tab === '{{ $key }}' ? 'bg-ink-950 text-white' : 'text-ink-600 hover:bg-paper'"
                    class="rounded-xl px-4 py-2 text-sm font-semibold transition">{{ $label }}</button>
        @endforeach
        <a href="{{ route('courses.show', $course) }}" target="_blank" class="ml-auto rounded-xl px-4 py-2 text-sm font-semibold text-cyan-500 hover:bg-paper">Открыть на сайте ↗</a>
    </div>

    {{-- DETAILS --}}
    <div x-show="tab === 'details'" class="mt-6">
        <form method="POST" action="{{ route('admin.courses.update', $course) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            @include('admin.courses._fields')
            <div class="mt-6 flex justify-end gap-3">
                <button class="btn btn-signal">Сохранить изменения</button>
            </div>
        </form>
    </div>

    {{-- CURRICULUM --}}
    <div x-show="tab === 'curriculum'" x-cloak class="mt-6 space-y-6">
        {{-- Add section --}}
        <div class="card p-5">
            <form method="POST" action="{{ route('admin.sections.store', $course) }}" class="flex flex-col gap-3">
                @csrf
                <x-i18n-field name="title" label="Новый раздел" :required="true" placeholder="Название раздела" />
                <button class="btn btn-ink self-start">+ Добавить раздел</button>
            </form>
        </div>

        @forelse($course->sections as $section)
            <div class="card overflow-hidden" x-data="{ addLesson: false, editSection: false }">
                <div class="flex items-center justify-between gap-3 border-b border-paper-line bg-paper px-5 py-3">
                    <h3 class="font-display font-bold">{{ $loop->iteration }}. {{ $section->title }}</h3>
                    <div class="flex items-center gap-1.5">
                        <button @click="editSection = !editSection" class="rounded-lg px-2.5 py-1.5 text-xs font-semibold text-cyan-500 hover:bg-white">✎ Раздел</button>
                        <button @click="addLesson = !addLesson" class="rounded-lg px-2.5 py-1.5 text-xs font-semibold text-ink-700 hover:bg-white">+ Урок</button>
                        <form method="POST" action="{{ route('admin.sections.destroy', $section) }}" onsubmit="return confirm('Удалить раздел со всеми уроками?')">
                            @csrf @method('DELETE')
                            <button class="rounded-lg px-2.5 py-1.5 text-xs font-semibold text-coral-500 hover:bg-white">🗑</button>
                        </form>
                    </div>
                </div>

                {{-- Edit section --}}
                <div x-show="editSection" x-cloak class="border-b border-paper-line bg-cyan-400/5 p-4">
                    <form method="POST" action="{{ route('admin.sections.update', $section) }}" class="space-y-3">
                        @csrf @method('PUT')
                        <x-i18n-field name="title" label="Название раздела" :required="true" :translations="$section->getTranslations('title')" />
                        <button class="btn btn-ink">OK</button>
                    </form>
                </div>

                {{-- Lessons --}}
                <ul class="divide-y divide-paper-line">
                    @forelse($section->lessons as $lesson)
                        <li x-data="{ editLesson: false }">
                            <div class="flex items-center gap-3 px-5 py-3">
                                <span class="text-ink-400">
                                    @if($lesson->type === 'video') ▶ @elseif($lesson->type === 'quiz') ✎ @else ☰ @endif
                                </span>
                                <span class="flex-1 text-sm font-medium">{{ $lesson->title }}</span>
                                @if($lesson->is_preview)<span class="chip bg-signal-300/20 text-signal-600">превью</span>@endif
                                <span class="text-xs text-ink-400">{{ $lesson->materials->count() }} мат.</span>
                                <button @click="editLesson = !editLesson" class="rounded-lg px-2 py-1 text-xs font-semibold text-cyan-500 hover:bg-paper">✎</button>
                                <form method="POST" action="{{ route('admin.lessons.destroy', $lesson) }}" onsubmit="return confirm('Удалить урок?')">
                                    @csrf @method('DELETE')
                                    <button class="rounded-lg px-2 py-1 text-xs font-semibold text-coral-500 hover:bg-paper">🗑</button>
                                </form>
                            </div>
                            {{-- Edit lesson --}}
                            <div x-show="editLesson" x-cloak class="bg-paper p-4">
                                @include('admin.courses._lesson_form', ['action' => route('admin.lessons.update', $lesson), 'method' => 'PUT', 'lesson' => $lesson, 'course' => $course])
                            </div>
                        </li>
                    @empty
                        <li class="px-5 py-4 text-sm text-ink-400">В разделе пока нет уроков.</li>
                    @endforelse
                </ul>

                {{-- Add lesson --}}
                <div x-show="addLesson" x-cloak class="border-t border-paper-line bg-paper p-4">
                    @include('admin.courses._lesson_form', ['action' => route('admin.lessons.store', $section), 'method' => 'POST', 'lesson' => null, 'course' => $course])
                </div>
            </div>
        @empty
            <div class="card grid place-items-center p-12 text-center text-ink-500">Добавьте первый раздел, чтобы начать.</div>
        @endforelse
    </div>

    {{-- MATERIALS --}}
    <div x-show="tab === 'materials'" x-cloak class="mt-6 space-y-6">
        <div class="card p-5">
            <h3 class="font-display font-bold">Загрузить материалы</h3>
            <p class="mt-1 text-sm text-ink-500">PDF, Word, Excel, PowerPoint, изображения, архивы и др. (до 50 МБ на файл)</p>
            <form method="POST" action="{{ route('admin.materials.store', $course) }}" enctype="multipart/form-data" class="mt-4 space-y-3">
                @csrf
                <input type="file" name="files[]" multiple required class="input"
                       accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,.ppt,.pptx,.txt,.zip,.rar,.7z,.png,.jpg,.jpeg,.gif,.webp,.mp4">
                <div class="grid gap-3 sm:grid-cols-2">
                    <select name="lesson_id" class="input">
                        <option value="">Привязать к курсу (общие)</option>
                        @foreach($course->sections as $section)
                            <optgroup label="{{ $section->title }}">
                                @foreach($section->lessons as $lesson)
                                    <option value="{{ $lesson->id }}">{{ $lesson->title }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    <button class="btn btn-signal">Загрузить</button>
                </div>
            </form>
        </div>

        <div class="card p-5">
            <h3 class="font-display font-bold">Загруженные материалы ({{ $course->materials->count() }})</h3>
            <div class="mt-4 space-y-2">
                @forelse($course->materials as $material)
                    <div class="flex items-center gap-3 rounded-xl border border-paper-line p-3">
                        <span class="text-2xl">{{ $material->icon }}</span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold">{{ $material->title }}</p>
                            <p class="text-xs text-ink-500">{{ strtoupper($material->extension) }} · {{ $material->human_size }}
                                @if($material->lesson_id) · {{ $material->lesson?->title }} @else · общий @endif
                            </p>
                        </div>
                        <a href="{{ \Illuminate\Support\Facades\Storage::url($material->file_path) }}" target="_blank" class="text-xs font-semibold text-cyan-500">просмотр</a>
                        <form method="POST" action="{{ route('admin.materials.destroy', $material) }}" onsubmit="return confirm('Удалить материал?')">
                            @csrf @method('DELETE')
                            <button class="text-xs font-semibold text-coral-500">удалить</button>
                        </form>
                    </div>
                @empty
                    <p class="text-sm text-ink-400">Материалов пока нет.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- TESTS --}}
    <div x-show="tab === 'tests'" x-cloak class="mt-6">
        <div class="card p-5">
            <div class="flex items-center justify-between">
                <h3 class="font-display font-bold">Тесты курса</h3>
                <a href="{{ route('admin.quizzes.create') }}" class="btn btn-ghost">+ Новый тест</a>
            </div>
            <div class="mt-4 space-y-2">
                @forelse($course->quizzes as $quiz)
                    <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="flex items-center justify-between rounded-xl border border-paper-line p-3 hover:bg-paper">
                        <span class="text-sm font-semibold">{{ $quiz->title }}</span>
                        <span class="text-xs text-ink-500">{{ $quiz->questions->count() }} вопросов · {{ $quiz->is_published ? 'опубликован' : 'черновик' }}</span>
                    </a>
                @empty
                    <p class="text-sm text-ink-400">К этому курсу не привязаны тесты. Создайте тест и выберите этот курс в его настройках.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
