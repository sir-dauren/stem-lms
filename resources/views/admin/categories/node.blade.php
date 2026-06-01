<div x-data="{ edit: false }" style="margin-left: {{ $depth * 1.25 }}rem">
    <div class="flex items-center gap-2 rounded-xl border border-paper-line bg-paper px-3 py-2">
        <span class="text-lg">{{ $node->icon ?: '📁' }}</span>
        <span class="flex-1 text-sm font-semibold">{{ $node->name }}</span>
        <span class="chip bg-white text-ink-500">{{ $node->courses_count ?? $node->courses()->count() }} курс.</span>
        @unless($node->is_active)<span class="chip bg-coral-400/15 text-coral-500">скрыта</span>@endunless
        <button @click="edit = !edit" class="rounded-lg px-2 py-1 text-xs font-semibold text-cyan-500 hover:bg-white">✎</button>
        <form method="POST" action="{{ route('admin.categories.destroy', $node) }}" onsubmit="return confirm('Удалить категорию? Подкатегории станут корневыми.')">
            @csrf @method('DELETE')
            <button class="rounded-lg px-2 py-1 text-xs font-semibold text-coral-500 hover:bg-white">🗑</button>
        </form>
    </div>

    {{-- Inline edit --}}
    <div x-show="edit" x-cloak class="mt-2 rounded-xl border border-cyan-400/40 bg-cyan-400/5 p-4" style="margin-left: 0.5rem">
        <form method="POST" action="{{ route('admin.categories.update', $node) }}" class="space-y-3">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-3">
                <div class="col-span-2">
                    <x-i18n-field name="name" label="Название" :required="true" :translations="$node->getTranslations('name')" />
                </div>
                <div class="col-span-2">
                    <label class="label">Родитель</label>
                    <select name="parent_id" class="input">
                        <option value="">— Корневая —</option>
                        @foreach($flat as $cat)
                            @if($cat->id !== $node->id)
                                <option value="{{ $cat->id }}" @selected($cat->id === $node->parent_id)>{{ str_repeat('— ', $cat->depth) }}{{ $cat->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">Иконка</label>
                    <input name="icon" value="{{ $node->icon }}" maxlength="8" class="input">
                </div>
                <div>
                    <label class="label">Цвет</label>
                    <input name="color" type="color" value="{{ $node->color ?: '#c2e72f' }}" class="input h-[42px] p-1">
                </div>
                <div class="col-span-2">
                    <x-i18n-field name="description" label="Описание" type="textarea" :rows="2" :translations="$node->getTranslations('description')" />
                </div>
            </div>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_active" value="1" @checked($node->is_active) class="h-4 w-4 accent-cyan-500"> Активна
            </label>
            <div class="flex gap-2">
                <button class="btn btn-ink btn-sm">Сохранить</button>
                <button type="button" @click="edit = false" class="btn btn-ghost">Отмена</button>
            </div>
        </form>
    </div>

    {{-- Children --}}
    @if($node->children->isNotEmpty())
        <div class="mt-2 space-y-2">
            @foreach($node->children as $child)
                @include('admin.categories.node', ['node' => $child, 'depth' => $depth + 1, 'flat' => $flat])
            @endforeach
        </div>
    @endif
</div>
