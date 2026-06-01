<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::withCount(['enrollments', 'comments', 'certificates'])
            ->when($request->q, fn ($q) => $q->where(fn ($w) => $w
                ->where('name', 'like', '%'.$request->q.'%')
                ->orWhere('email', 'like', '%'.$request->q.'%')))
            ->when($request->filter === 'blocked', fn ($q) => $q->where('is_blocked', true))
            ->when($request->filter === 'admins', fn ($q) => $q->where('role', 'admin'))
            ->latest()->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load([
            'enrollments.course', 'certificates.course', 'skills',
            'comments.course',
        ]);
        return view('admin.users.show', compact('user'));
    }

    public function block(Request $request, User $user)
    {
        if ($user->isAdmin()) {
            return back()->withErrors(['user' => 'Нельзя заблокировать администратора.']);
        }
        $user->update([
            'is_blocked'     => true,
            'blocked_reason' => $request->input('reason', 'Нарушение правил сообщества.'),
        ]);
        return back()->with('status', 'Пользователь заблокирован.');
    }

    public function unblock(User $user)
    {
        $user->update(['is_blocked' => false, 'blocked_reason' => null]);
        return back()->with('status', 'Пользователь разблокирован.');
    }

    public function toggleRole(User $user)
    {
        $user->update(['role' => $user->isAdmin() ? 'user' : 'admin']);
        return back()->with('status', 'Роль изменена.');
    }
}
