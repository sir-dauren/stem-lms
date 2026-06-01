<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Comment;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'courses'     => Course::count(),
            'published'   => Course::where('is_published', true)->count(),
            'users'       => User::where('role', 'user')->count(),
            'enrollments' => Enrollment::count(),
            'completions' => Enrollment::whereNotNull('completed_at')->count(),
            'certs'       => Certificate::count(),
            'comments'    => Comment::count(),
            'blocked'     => User::where('is_blocked', true)->count(),
        ];

        $topCourses = Course::withCount(['enrollments', 'likes'])
            ->orderByDesc('enrollments_count')->take(5)->get();

        $recentUsers = User::where('role', 'user')->latest()->take(6)->get();

        $recentComments = Comment::with(['user', 'course'])->latest()->take(6)->get();

        // simple 14-day enrollment trend for the chart
        $trend = Enrollment::selectRaw('DATE(created_at) as d, COUNT(*) as c')
            ->where('created_at', '>=', now()->subDays(13)->startOfDay())
            ->groupBy('d')->pluck('c', 'd');

        $days = collect(range(13, 0))->map(function ($i) use ($trend) {
            $date = now()->subDays($i)->toDateString();
            return ['label' => now()->subDays($i)->format('d.m'), 'value' => (int) ($trend[$date] ?? 0)];
        });

        return view('admin.dashboard', compact('stats', 'topCourses', 'recentUsers', 'recentComments', 'days'));
    }
}
