@extends('layouts.admin')
@section('title', 'Комментарии')
@section('heading', 'Модерация комментариев')

@section('content')
<div class="flex flex-wrap gap-2">
    <a href="{{ route('admin.comments.index') }}" class="chip {{ !request('filter') ? 'bg-ink-950 text-white' : 'bg-white text-ink-600' }} px-4 py-2">Все</a>
    <a href="{{ route('admin.comments.index', ['filter' => 'hidden']) }}" class="chip {{ request('filter')==='hidden' ? 'bg-ink-950 text-white' : 'bg-white text-ink-600' }} px-4 py-2">Скрытые</a>
    <a href="{{ route('admin.comments.index', ['filter' => 'replies']) }}" class="chip {{ request('filter')==='replies' ? 'bg-ink-950 text-white' : 'bg-white text-ink-600' }} px-4 py-2">Ответы</a>
</div>

<div class="mt-6 space-y-4">
    @forelse($comments as $comment)
        <div class="card p-5" x-data="{ reply: false }">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-start gap-3">
                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-ink-950 text-xs font-bold text-signal-300">{{ initials($comment->user?->name) }}</span>
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <a href="{{ $comment->user ? route('admin.users.show', $comment->user) : '#' }}" class="font-semibold hover:text-cyan-500">{{ $comment->user?->name ?: 'Удалён' }}</a>
                            @if($comment->is_staff)<span class="chip bg-signal-300/20 text-signal-600">Команда</span>@endif
                            @if($comment->is_hidden)<span class="chip bg-coral-400/15 text-coral-500">Скрыт</span>@endif
                            @if($comment->parent_id)<span class="chip bg-paper text-ink-500">ответ</span>@endif
                            <span class="text-xs text-ink-400">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="mt-1.5 text-ink-700">{{ $comment->body }}</p>
                        <p class="mt-1 text-xs text-ink-400">Курс: <a href="{{ $comment->course ? route('courses.show', $comment->course) : '#' }}" class="text-cyan-500">{{ $comment->course?->title }}</a></p>
                    </div>
                </div>
                <div class="flex shrink-0 items-center gap-1.5">
                    <button @click="reply = !reply" class="rounded-lg px-2.5 py-1.5 text-xs font-semibold text-cyan-500 hover:bg-paper">Ответить</button>
                    <form method="POST" action="{{ route('admin.comments.toggle', $comment) }}">
                        @csrf
                        <button class="rounded-lg px-2.5 py-1.5 text-xs font-semibold text-ink-700 hover:bg-paper">{{ $comment->is_hidden ? 'Показать' : 'Скрыть' }}</button>
                    </form>
                    <form method="POST" action="{{ route('admin.comments.destroy', $comment) }}" onsubmit="return confirm('Удалить комментарий?')">
                        @csrf @method('DELETE')
                        <button class="rounded-lg px-2.5 py-1.5 text-xs font-semibold text-coral-500 hover:bg-paper">Удалить</button>
                    </form>
                </div>
            </div>

            <div x-show="reply" x-cloak class="mt-4 border-t border-paper-line pt-4">
                <form method="POST" action="{{ route('admin.comments.reply', $comment) }}" class="flex gap-2">
                    @csrf
                    <input name="body" required class="input" placeholder="Ответ от имени команды…">
                    <button class="btn btn-signal">Отправить</button>
                </form>
            </div>
        </div>
    @empty
        <div class="card grid place-items-center p-12 text-center text-ink-500">Комментариев нет.</div>
    @endforelse
</div>

<div class="mt-6">{{ $comments->links() }}</div>
@endsection
