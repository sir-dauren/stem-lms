@extends('layouts.admin')
@section('title', 'Тесты')
@section('heading', 'Тесты')

@section('content')
<div class="flex justify-end">
    <a href="{{ route('admin.quizzes.create') }}" class="btn btn-signal">+ Новый тест</a>
</div>

<div class="card mt-6 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="border-b border-paper-line bg-paper text-left text-xs font-bold uppercase tracking-wider text-ink-500">
            <tr>
                <th class="px-5 py-3">Тест</th>
                <th class="px-5 py-3">Привязка</th>
                <th class="px-5 py-3 text-center">Вопросы</th>
                <th class="px-5 py-3 text-center">Попытки</th>
                <th class="px-5 py-3 text-center">Статус</th>
                <th class="px-5 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-paper-line">
            @forelse($quizzes as $quiz)
                <tr class="hover:bg-paper">
                    <td class="px-5 py-3"><a href="{{ route('admin.quizzes.edit', $quiz) }}" class="font-semibold hover:text-cyan-500">{{ $quiz->title }}</a></td>
                    <td class="px-5 py-3 text-ink-500">
                        @if($quiz->course)📚 {{ $quiz->course->title }}
                        @elseif($quiz->category)🗂 {{ $quiz->category->name }}
                        @else — @endif
                        @if($quiz->is_standalone) <span class="chip bg-cyan-400/10 text-cyan-600">самопроверка</span>@endif
                    </td>
                    <td class="px-5 py-3 text-center">{{ $quiz->questions_count }}</td>
                    <td class="px-5 py-3 text-center">{{ $quiz->attempts_count }}</td>
                    <td class="px-5 py-3 text-center">
                        @if($quiz->is_published)<span class="chip bg-signal-300/20 text-signal-600">Опубликован</span>
                        @else<span class="chip" style="background:#eef0ea" >Черновик</span>@endif
                    </td>
                    <td class="px-5 py-3 text-right">
                        <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="text-xs font-semibold text-cyan-500">изменить</a>
                        <form method="POST" action="{{ route('admin.quizzes.destroy', $quiz) }}" class="inline" onsubmit="return confirm('Удалить тест?')">
                            @csrf @method('DELETE')
                            <button class="ml-2 text-xs font-semibold text-coral-500">удалить</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-5 py-12 text-center text-ink-500">Тестов пока нет.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $quizzes->links() }}</div>
@endsection
