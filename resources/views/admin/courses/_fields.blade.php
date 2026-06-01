<div class="grid gap-5 lg:grid-cols-[1fr_320px]">
    <div class="space-y-4">
        <x-i18n-field name="title" label="Название курса"
            :translations="$course->getTranslations('title')" :required="true"
            placeholder="Введение в Python" />

        <x-i18n-field name="subtitle" label="Подзаголовок"
            :translations="$course->getTranslations('subtitle')"
            placeholder="Короткое описание в одну строку" />

        <x-i18n-field name="description" label="Описание" type="textarea" :rows="5"
            :translations="$course->getTranslations('description')" />

        <div class="grid gap-4 sm:grid-cols-2">
            <x-i18n-field name="outcomes" label="Чему научатся (по строке на пункт)" type="textarea" :rows="4"
                :translations="$course->getTranslations('outcomes')"
                placeholder="Решать уравнения&#10;Строить графики" />
            <x-i18n-field name="requirements" label="Требования (по строке на пункт)" type="textarea" :rows="4"
                :translations="$course->getTranslations('requirements')"
                placeholder="Базовая алгебра" />
        </div>

        <div>
            <label class="label">Навыки (через запятую, на языке по умолчанию)</label>
            <input name="skills" value="{{ old('skills', $course->skills->pluck('name')->join(', ')) }}" class="input" placeholder="Python, Линейная алгебра, Анализ данных">
        </div>
    </div>

    <div class="space-y-4">
        <div class="card p-4">
            <label class="label">Обложка</label>
            @if($course->thumbnail)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($course->thumbnail) }}" class="mb-2 aspect-video w-full rounded-xl object-cover" alt="">
            @endif
            <input type="file" name="thumbnail" accept="image/*" class="input">
        </div>

        <div>
            <label class="label">Категория</label>
            <select name="category_id" class="input">
                <option value="">— Без категории —</option>
                @foreach($categories as $opt)
                    <option value="{{ $opt['id'] }}" @selected(old('category_id', $course->category_id) == $opt['id'])>{{ $opt['label'] }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="label">Уровень *</label>
            <select name="level" class="input" required>
                <option value="beginner" @selected(old('level', $course->level)==='beginner')>Начальный</option>
                <option value="intermediate" @selected(old('level', $course->level)==='intermediate')>Средний</option>
                <option value="advanced" @selected(old('level', $course->level)==='advanced')>Продвинутый</option>
            </select>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="label">Язык контента</label>
                <input name="language" value="{{ old('language', $course->language ?: 'Русский') }}" class="input">
            </div>
            <div>
                <label class="label">Длит. (мин)</label>
                <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $course->duration_minutes) }}" min="0" class="input">
            </div>
        </div>
        <div>
            <label class="label">Преподаватель</label>
            <input name="instructor_name" value="{{ old('instructor_name', $course->instructor_name) }}" class="input">
        </div>
        <div>
            <label class="label">Должность преподавателя</label>
            <input name="instructor_title" value="{{ old('instructor_title', $course->instructor_title) }}" class="input">
        </div>
        <div>
            <label class="label">Промо-видео (URL)</label>
            <input name="promo_video" value="{{ old('promo_video', $course->promo_video) }}" class="input" placeholder="https://youtube.com/...">
        </div>
        <div class="space-y-2 rounded-xl border border-paper-line p-4">
            <label class="flex items-center gap-2 text-sm font-semibold">
                <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $course->is_published)) class="h-4 w-4 accent-cyan-500"> Опубликовать
            </label>
            <label class="flex items-center gap-2 text-sm font-semibold">
                <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $course->is_featured)) class="h-4 w-4 accent-cyan-500"> Рекомендуемый (★)
            </label>
        </div>
    </div>
</div>
