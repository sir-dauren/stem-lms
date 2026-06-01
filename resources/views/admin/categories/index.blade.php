@extends('layouts.admin')
@section('title', 'Категории')
@section('heading', 'Категории и подкатегории')

@section('content')
<div class="grid gap-6 lg:grid-cols-[1fr_360px]">
    {{-- Tree --}}
    <div class="card p-6">
        <h2 class="font-display text-lg font-bold">Дерево категорий</h2>
        <p class="mt-1 text-sm text-ink-500">Поддерживается любая глубина вложенности.</p>

        <div class="mt-5 space-y-2">
            @forelse($roots as $root)
                @include('admin.categories.node', ['node' => $root, 'depth' => 0, 'flat' => $flat])
            @empty
                <p class="text-sm text-ink-500">Категорий пока нет. Создайте первую справа.</p>
            @endforelse
        </div>
    </div>

    {{-- Add form --}}
    <div>
        <div class="card p-6">
            <h2 class="font-display text-lg font-bold">Новая категория</h2>
            <form method="POST" action="{{ route('admin.categories.store') }}" class="mt-4 space-y-4">
                @csrf
                <x-i18n-field name="name" label="Название" :required="true" placeholder="Например: Физика" />
                <div>
                    <label class="label">Родительская категория</label>
                    <select name="parent_id" class="input">
                        <option value="">— Корневая —</option>
                        @foreach($flat as $cat)
                            <option value="{{ $cat->id }}">{{ str_repeat('— ', $cat->depth) }}{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="label">Иконка (эмодзи)</label>
                        <input name="icon" maxlength="8" class="input" placeholder="🧲">
                    </div>
                    <div>
                        <label class="label">Цвет</label>
                        <input name="color" type="color" value="#c2e72f" class="input h-[42px] p-1">
                    </div>
                </div>
                <x-i18n-field name="description" label="Описание" type="textarea" :rows="2" />
                <button class="btn btn-signal w-full">Создать категорию</button>
            </form>
        </div>
    </div>
</div>
@endsection
