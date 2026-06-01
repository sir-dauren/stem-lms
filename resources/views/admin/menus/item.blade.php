<div x-data="{ edit: false }" class="rounded-xl border border-paper-line bg-paper">
    <div class="flex items-center gap-2 px-3 py-2">
        <span class="text-ink-400">≡</span>
        <span class="flex-1 text-sm font-semibold">{{ $item->label }}</span>
        <code class="hidden text-xs text-ink-400 sm:block">{{ $item->url }}</code>
        @if($item->target === '_blank')<span class="chip bg-white text-ink-500">↗</span>@endif
        @unless($item->is_active)<span class="chip bg-coral-400/15 text-coral-500">скрыт</span>@endunless
        <button @click="edit = !edit" class="rounded-lg px-2 py-1 text-xs font-semibold text-cyan-500 hover:bg-white">✎</button>
        <form method="POST" action="{{ route('admin.menus.items.destroy', $item) }}" onsubmit="return confirm('Удалить пункт меню?')">
            @csrf @method('DELETE')
            <button class="rounded-lg px-2 py-1 text-xs font-semibold text-coral-500 hover:bg-white">🗑</button>
        </form>
    </div>

    <div x-show="edit" x-cloak class="border-t border-paper-line p-3">
        <form method="POST" action="{{ route('admin.menus.items.update', $item) }}" class="space-y-2">
            @csrf @method('PUT')
            <x-i18n-field name="label" label="Название" :required="true" :translations="$item->getTranslations('label')" />
            <input name="url" value="{{ $item->url }}" required class="input" placeholder="URL">
            <div class="flex flex-wrap items-center gap-4">
                <select name="target" class="input w-auto">
                    <option value="_self" @selected($item->target==='_self')>Эта вкладка</option>
                    <option value="_blank" @selected($item->target==='_blank')>Новая вкладка</option>
                </select>
                <label class="flex items-center gap-2 text-sm font-semibold">
                    <input type="checkbox" name="is_active" value="1" @checked($item->is_active) class="h-4 w-4 accent-cyan-500"> Активен
                </label>
                <button class="btn btn-ink ml-auto">Сохранить</button>
            </div>
        </form>
    </div>
</div>
