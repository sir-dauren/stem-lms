@extends('layouts.admin')
@section('title', $quiz->exists ? 'Редактирование теста' : 'Новый тест')
@section('heading', $quiz->exists ? 'Тест: '.$quiz->title : 'Новый тест')

@section('content')
@php($default = config('app.fallback_locale'))

{{-- Quiz settings --}}
<form method="POST" action="{{ $quiz->exists ? route('admin.quizzes.update', $quiz) : route('admin.quizzes.store') }}">
    @csrf
    @if($quiz->exists) @method('PUT') @endif
    <div class="card p-6">
        <h2 class="font-display text-lg font-bold">Настройки теста</h2>
        <div class="mt-4 grid gap-4 lg:grid-cols-2">
            <div class="lg:col-span-2">
                <x-i18n-field name="title" label="Название" :required="true" :translations="$quiz->exists ? $quiz->getTranslations('title') : []" />
            </div>
            <div class="lg:col-span-2">
                <x-i18n-field name="description" label="Описание" type="textarea" :rows="2" :translations="$quiz->exists ? $quiz->getTranslations('description') : []" />
            </div>
            <div>
                <label class="label">Привязать к курсу</label>
                <select name="course_id" class="input">
                    <option value="">— Не привязан —</option>
                    @foreach($courses as $id => $title)
                        <option value="{{ $id }}" @selected(old('course_id', $quiz->course_id) == $id)>{{ $title }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">Категория</label>
                <select name="category_id" class="input">
                    <option value="">— Без категории —</option>
                    @foreach($categories as $id => $name)
                        <option value="{{ $id }}" @selected(old('category_id', $quiz->category_id) == $id)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">Проходной балл (%) *</label>
                <input type="number" name="passing_score" value="{{ old('passing_score', $quiz->passing_score ?? 70) }}" min="1" max="100" required class="input">
            </div>
            <div>
                <label class="label">Лимит времени (мин)</label>
                <input type="number" name="time_limit_minutes" value="{{ old('time_limit_minutes', $quiz->time_limit_minutes) }}" min="1" class="input">
            </div>
            <div class="flex items-center gap-6 lg:col-span-2">
                <label class="flex items-center gap-2 text-sm font-semibold">
                    <input type="checkbox" name="is_standalone" value="1" @checked(old('is_standalone', $quiz->is_standalone ?? true)) class="h-4 w-4 accent-cyan-500"> Доступен в разделе «Тесты» (самопроверка)
                </label>
                <label class="flex items-center gap-2 text-sm font-semibold">
                    <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $quiz->is_published ?? true)) class="h-4 w-4 accent-cyan-500"> Опубликован
                </label>
            </div>
        </div>
        <div class="mt-5 flex justify-end">
            <button class="btn btn-signal">{{ $quiz->exists ? 'Сохранить настройки' : 'Создать тест' }}</button>
        </div>
    </div>
</form>

@if($quiz->exists)
    {{-- Questions --}}
    <div class="card mt-6 p-6">
        <h2 class="font-display text-lg font-bold">Вопросы ({{ $quiz->questions->count() }})</h2>
        <div class="mt-4 space-y-3">
            @forelse($quiz->questions as $question)
                <div class="rounded-2xl border border-paper-line p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1">
                            <p class="font-semibold">{{ $loop->iteration }}. {{ $question->question }}
                                <span class="chip bg-paper text-ink-500">{{ $question->type === 'multiple' ? 'неск. ответов' : 'один ответ' }}</span>
                                <span class="chip bg-paper text-ink-500">{{ $question->points }} б.</span>
                            </p>
                            <ul class="mt-2 space-y-1 text-sm">
                                @foreach($question->options as $option)
                                    <li class="flex items-center gap-2 {{ $option->is_correct ? 'font-semibold text-signal-600' : 'text-ink-600' }}">
                                        <span>{{ $option->is_correct ? '✓' : '◦' }}</span> {{ $option->text }}
                                    </li>
                                @endforeach
                            </ul>
                            @if($question->explanation)<p class="mt-2 text-xs text-ink-500">💡 {{ $question->explanation }}</p>@endif
                        </div>
                        <form method="POST" action="{{ route('admin.questions.destroy', $question) }}" onsubmit="return confirm('Удалить вопрос?')">
                            @csrf @method('DELETE')
                            <button class="text-xs font-semibold text-coral-500">удалить</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-sm text-ink-400">Вопросов пока нет. Добавьте первый ниже.</p>
            @endforelse
        </div>
    </div>

    {{-- Add question --}}
    <div class="card mt-6 p-6" x-data="questionForm()">
        <h2 class="font-display text-lg font-bold">Новый вопрос</h2>
        <p class="mt-1 text-sm text-ink-500">Заполните хотя бы язык по умолчанию (RU). Остальные языки — по желанию.</p>
        <form method="POST" action="{{ route('admin.questions.store', $quiz) }}" class="mt-4 space-y-4">
            @csrf

            {{-- Question text per locale --}}
            <div x-data="{ qloc: '{{ $default }}' }">
                <label class="label">Текст вопроса *</label>
                <div class="mb-1.5 flex gap-1">
                    @foreach(config('locales.available') as $code => $meta)
                        <button type="button" @click="qloc='{{ $code }}'"
                            :class="qloc==='{{ $code }}' ? 'bg-ink-950 text-signal-300' : 'bg-paper text-ink-500'"
                            class="rounded-lg px-2.5 py-1 text-xs font-bold uppercase">{{ $meta['flag'] }} {{ $code }}</button>
                    @endforeach
                </div>
                @foreach(config('locales.available') as $code => $meta)
                    <div x-show="qloc==='{{ $code }}'" @if(!$loop->first) x-cloak @endif>
                        <textarea name="question[{{ $code }}]" rows="2" class="input" @if($code===$default) required @endif></textarea>
                    </div>
                @endforeach
            </div>

            <div class="grid gap-4 sm:grid-cols-[1fr_140px]">
                <div>
                    <label class="label">Тип</label>
                    <select name="type" x-model="type" class="input">
                        <option value="single">Один правильный ответ</option>
                        <option value="multiple">Несколько правильных</option>
                    </select>
                </div>
                <div>
                    <label class="label">Баллы</label>
                    <input type="number" name="points" value="1" min="1" class="input">
                </div>
            </div>

            {{-- Options: per-locale text + correctness --}}
            <div x-data="{ oloc: '{{ $default }}' }">
                <label class="label">Варианты ответа (отметьте правильные)</label>
                <div class="mb-1.5 flex gap-1">
                    @foreach(config('locales.available') as $code => $meta)
                        <button type="button" @click="oloc='{{ $code }}'"
                            :class="oloc==='{{ $code }}' ? 'bg-ink-950 text-signal-300' : 'bg-paper text-ink-500'"
                            class="rounded-lg px-2.5 py-1 text-xs font-bold uppercase">{{ $meta['flag'] }} {{ $code }}</button>
                    @endforeach
                </div>
                <div class="space-y-2">
                    <template x-for="(opt, i) in options" :key="i">
                        <div class="flex items-center gap-2">
                            <input type="checkbox" :name="'correct[]'" :value="i" @change="onCorrect(i)" :checked="opt.correct"
                                   class="h-4 w-4 accent-signal-500" :class="type === 'single' ? 'rounded-full' : ''">
                            @foreach(config('locales.available') as $code => $meta)
                                <input type="text" :name="'options['+i+'][text][{{ $code }}]'"
                                       x-show="oloc==='{{ $code }}'" @if(!$loop->first) x-cloak @endif
                                       @if($code===$default) x-model="opt.text" required @endif
                                       class="input flex-1" :placeholder="'{{ strtoupper($code) }} · вариант '+(i+1)">
                            @endforeach
                            <button type="button" @click="remove(i)" x-show="options.length > 2" class="text-coral-500">✕</button>
                        </div>
                    </template>
                </div>
                <button type="button" @click="add()" class="mt-2 text-sm font-semibold text-cyan-500">+ Добавить вариант</button>
            </div>

            {{-- Explanation per locale --}}
            <div x-data="{ eloc: '{{ $default }}' }">
                <label class="label">Пояснение (показывается в разборе)</label>
                <div class="mb-1.5 flex gap-1">
                    @foreach(config('locales.available') as $code => $meta)
                        <button type="button" @click="eloc='{{ $code }}'"
                            :class="eloc==='{{ $code }}' ? 'bg-ink-950 text-signal-300' : 'bg-paper text-ink-500'"
                            class="rounded-lg px-2.5 py-1 text-xs font-bold uppercase">{{ $meta['flag'] }} {{ $code }}</button>
                    @endforeach
                </div>
                @foreach(config('locales.available') as $code => $meta)
                    <div x-show="eloc==='{{ $code }}'" @if(!$loop->first) x-cloak @endif>
                        <textarea name="explanation[{{ $code }}]" rows="2" class="input"></textarea>
                    </div>
                @endforeach
            </div>

            <div class="flex justify-end">
                <button class="btn btn-ink">Добавить вопрос</button>
            </div>
        </form>
    </div>
@endif

@push('scripts')
<script>
function questionForm() {
    return {
        type: 'single',
        options: [{ text: '', correct: false }, { text: '', correct: false }],
        add() { this.options.push({ text: '', correct: false }); },
        remove(i) { this.options.splice(i, 1); },
        onCorrect(i) {
            if (this.type === 'single') {
                this.options.forEach((o, idx) => o.correct = (idx === i));
            } else {
                this.options[i].correct = !this.options[i].correct;
            }
        }
    }
}
</script>
@endpush
@endsection
