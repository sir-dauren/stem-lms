<form method="POST" action="{{ $action }}" enctype="multipart/form-data" x-data="{ type: '{{ $lesson->type ?? 'video' }}' }" class="space-y-3">
    @csrf
    @if($method === 'PUT') @method('PUT') @endif

    <div class="grid gap-3 sm:grid-cols-[1fr_180px]">
        <div>
            <x-i18n-field name="title" label="Название урока" :required="true"
                :translations="isset($lesson) && $lesson->exists ? $lesson->getTranslations('title') : []" />
        </div>
        <div>
            <label class="label">Тип</label>
            <select name="type" x-model="type" class="input">
                <option value="video" @selected(($lesson->type ?? '')==='video')>Видео</option>
                <option value="article" @selected(($lesson->type ?? '')==='article')>Статья</option>
                <option value="quiz" @selected(($lesson->type ?? '')==='quiz')>Тест</option>
            </select>
        </div>
    </div>

    {{-- Video fields --}}
    <div x-show="type === 'video'" class="space-y-3">
        <div>
            <label class="label">Ссылка на видео (YouTube / Vimeo / прямая)</label>
            <input name="video_url" value="{{ $lesson->video_url ?? '' }}" class="input" placeholder="https://youtube.com/watch?v=...">
        </div>
        <div>
            <label class="label">…или загрузить видеофайл (mp4/mov/mkv/webm, до 1 ГБ)</label>
            <input type="file" name="video_file" accept="video/mp4,video/quicktime,video/x-matroska,video/webm" class="input">
            @if(!empty($lesson?->video_path))<p class="mt-1 text-xs text-signal-600">Видео уже загружено.</p>@endif
        </div>
    </div>

    {{-- Article/content --}}
    <div x-show="type !== 'video'">
        <x-i18n-field name="content" label="Содержимое (HTML)" type="textarea" :rows="5"
            :translations="isset($lesson) && $lesson->exists ? $lesson->getTranslations('content') : []"
            placeholder="<h2>Заголовок</h2><p>Текст урока…</p>" />
    </div>
    <div x-show="type === 'video'">
        <x-i18n-field name="content" label="Описание / конспект (HTML, необязательно)" type="textarea" :rows="3"
            :translations="isset($lesson) && $lesson->exists ? $lesson->getTranslations('content') : []" />
    </div>

    <div class="flex flex-wrap items-end gap-4">
        <div class="w-32">
            <label class="label">Длит. (мин)</label>
            <input type="number" name="duration_minutes" value="{{ $lesson->duration_minutes ?? 0 }}" min="0" class="input">
        </div>
        <label class="flex items-center gap-2 pb-2.5 text-sm font-semibold">
            <input type="checkbox" name="is_preview" value="1" @checked($lesson->is_preview ?? false) class="h-4 w-4 accent-cyan-500"> Бесплатное превью
        </label>
        <button class="btn btn-signal ml-auto">{{ $method === 'PUT' ? 'Сохранить урок' : 'Добавить урок' }}</button>
    </div>
</form>
