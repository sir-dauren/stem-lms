@extends('layouts.admin')
@section('title', 'Меню')
@section('heading', 'Меню навигации')

@section('content')
<p class="text-sm text-ink-500">Управляйте пунктами меню в шапке и подвале сайта. Для подпункта выберите родителя (поддерживается двухуровневое выпадающее меню в шапке).</p>

<div class="mt-6 grid gap-6 lg:grid-cols-2">
    @foreach($menus as $menu)
        @php($topItems = $menu->allItems->whereNull('parent_id'))
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <h2 class="font-display text-lg font-bold">{{ $menu->name }}</h2>
                <span class="chip bg-paper text-ink-500">{{ $menu->location }}</span>
            </div>

            {{-- Existing items --}}
            <div class="mt-4 space-y-2">
                @forelse($topItems as $item)
                    @include('admin.menus.item', ['item' => $item, 'menu' => $menu])
                    @foreach($menu->allItems->where('parent_id', $item->id) as $child)
                        <div class="ml-6">
                            @include('admin.menus.item', ['item' => $child, 'menu' => $menu])
                        </div>
                    @endforeach
                @empty
                    <p class="rounded-xl border border-dashed border-paper-line p-4 text-center text-sm text-ink-400">Пунктов пока нет.</p>
                @endforelse
            </div>

            {{-- Add item --}}
            <form method="POST" action="{{ route('admin.menus.items.store', $menu) }}" class="mt-5 space-y-3 border-t border-paper-line pt-5">
                @csrf
                <p class="text-sm font-bold">Добавить пункт</p>
                <x-i18n-field name="label" label="Название" :required="true" placeholder="Например: Каталог" />
                <div class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <label class="label">Ссылка (URL)</label>
                        <input name="url" required class="input" placeholder="/courses или https://…">
                    </div>
                    <div>
                        <label class="label">Родитель</label>
                        <select name="parent_id" class="input">
                            <option value="">— Верхний уровень —</option>
                            @foreach($topItems as $item)
                                <option value="{{ $item->id }}">{{ $item->label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label">Открывать</label>
                        <select name="target" class="input">
                            <option value="_self">В этой вкладке</option>
                            <option value="_blank">В новой вкладке</option>
                        </select>
                    </div>
                </div>
                <button class="btn btn-signal w-full">+ Добавить пункт</button>
            </form>
        </div>
    @endforeach
</div>

<div class="card mt-6 p-5">
    <h3 class="font-display font-bold">Подсказки по ссылкам</h3>
    <ul class="mt-3 space-y-1 text-sm text-ink-600">
        <li><code class="rounded bg-paper px-1.5 py-0.5 text-xs">/</code> — главная страница</li>
        <li><code class="rounded bg-paper px-1.5 py-0.5 text-xs">/courses</code> — каталог курсов</li>
        <li><code class="rounded bg-paper px-1.5 py-0.5 text-xs">/quizzes</code> — тесты</li>
        <li><code class="rounded bg-paper px-1.5 py-0.5 text-xs">/about</code> — о платформе</li>
        <li>Внешние ссылки указывайте полностью: <code class="rounded bg-paper px-1.5 py-0.5 text-xs">https://…</code></li>
    </ul>
</div>
@endsection
