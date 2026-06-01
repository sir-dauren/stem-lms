<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Course;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InteractionController extends Controller
{
    /** Toggle like (auth required by route middleware). */
    public function toggleLike(Course $course)
    {
        $user = Auth::user();
        $existing = Like::where('user_id', $user->id)->where('course_id', $course->id)->first();

        if ($existing) {
            $existing->delete();
            $liked = false;
        } else {
            Like::create(['user_id' => $user->id, 'course_id' => $course->id]);
            $liked = true;
        }

        $count = $course->likes()->count();

        if (request()->wantsJson()) {
            return response()->json(['liked' => $liked, 'count' => $count]);
        }
        return back();
    }

    /** Post a comment or reply. */
    public function comment(Request $request, Course $course)
    {
        $data = $request->validate([
            'body'      => ['required', 'string', 'max:2000'],
            'parent_id' => ['nullable', 'exists:comments,id'],
        ]);

        Comment::create([
            'user_id'   => Auth::id(),
            'course_id' => $course->id,
            'parent_id' => $data['parent_id'] ?? null,
            'body'      => $data['body'],
            'is_staff'  => Auth::user()->isAdmin(),
        ]);

        return back()->with('status', 'Комментарий добавлен.');
    }

    public function deleteComment(Comment $comment)
    {
        abort_unless($comment->user_id === Auth::id(), 403);
        $comment->delete();
        return back();
    }
}
