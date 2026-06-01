<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $comments = Comment::with(['user', 'course', 'parent'])
            ->when($request->filter === 'hidden', fn ($q) => $q->where('is_hidden', true))
            ->when($request->filter === 'replies', fn ($q) => $q->whereNotNull('parent_id'))
            ->latest()->paginate(20)->withQueryString();

        return view('admin.comments.index', compact('comments'));
    }

    /** Admin reply to a comment (shown with a staff badge). */
    public function reply(Request $request, Comment $comment)
    {
        $data = $request->validate(['body' => ['required', 'string', 'max:2000']]);

        Comment::create([
            'user_id'   => Auth::id(),
            'course_id' => $comment->course_id,
            'parent_id' => $comment->parent_id ?? $comment->id,
            'body'      => $data['body'],
            'is_staff'  => true,
        ]);

        return back()->with('status', 'Ответ опубликован.');
    }

    public function toggleHide(Comment $comment)
    {
        $comment->update(['is_hidden' => ! $comment->is_hidden]);
        return back()->with('status', $comment->is_hidden ? 'Комментарий скрыт.' : 'Комментарий восстановлен.');
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();
        return back()->with('status', 'Комментарий удалён.');
    }
}
