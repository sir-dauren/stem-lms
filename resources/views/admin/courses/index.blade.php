@extends('layouts.admin')
@section('title', 'Курсы')
@section('heading', 'Курсы')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-4">
    <form method="GET" class="flex flex-1 gap-2 sm:max-w-md">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Поиск курсов…" class="input">
        <button class="btn btn-ghost">Найти</button>
    </form>
    <a href="{{ route('admin.courses.create') }}" class="btn btn-signal">+ Новый курс</a>
</div>

<div class="card mt-6 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="border-b border-paper-line bg-paper text-left text-xs font-bold uppercase tracking-wider text-ink-500">
            <tr>
                <th class="px-5 py-3">Курс</th>
                <th class="px-5 py-3">Категория</th>
                <th class="px-5 py-3 text-center">Уроки</th>
                <th class="px-5 py-3 text-center">Ученики</th>
                <th class="px-5 py-3 text-center">Статус</th>
                <th class="px-5 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-paper-line">
            @forelse($courses as $course)
                <tr class="hover:bg-paper">
                    <td class="px-5 py-3">
                        <a href="{{ route('admin.courses.edit', $course) }}" class="font-semibold hover:text-cyan-500">{{ $course->title }}</a>
                    </td>
                    <td class="px-5 py-3 text-ink-500">{{ $course->category?->name ?: '—' }}</td>
                    <td class="px-5 py-3 text-center">{{ $course->lessons_count }}</td>
                    <td class="px-5 py-3 text-center">{{ $course->enrollments_count }}</td>
                    <td class="px-5 py-3 text-center">
                        @if($course->is_published)
                            <span class="chip bg-signal-300/20 text-signal-600">Опубликован</span>
                        @else
                            <span class="chip bg-ink-100 text-ink-500" style="background: #eef0ea">Черновик</span>
                        @endif
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center justify-end gap-1.5">
                            <form method="POST" action="{{ route('admin.courses.toggle', $course) }}">
                                @csrf
                                <button class="rounded-lg px-2.5 py-1.5 text-xs font-semibold text-cyan-500 hover:bg-white">{{ $course->is_published ? 'Снять' : 'Опубл.' }}</button>
                            </form>
                            <a href="{{ route('admin.courses.edit', $course) }}" class="rounded-lg px-2.5 py-1.5 text-xs font-semibold text-ink-700 hover:bg-white">Изменить</a>
                            <form method="POST" action="{{ route('admin.courses.destroy', $course) }}" onsubmit="return confirm('Удалить курс полностью?')">
                                @csrf @method('DELETE')
                                <button class="rounded-lg px-2.5 py-1.5 text-xs font-semibold text-coral-500 hover:bg-white">Удалить</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-5 py-12 text-center text-ink-500">Курсов пока нет. <a href="{{ route('admin.courses.create') }}" class="font-semibold text-cyan-500">Создайте первый.</a></td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $courses->links() }}</div>
@endsection
