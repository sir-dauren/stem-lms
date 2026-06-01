<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $enrollments = $user->enrollments()
            ->with('course.category')
            ->latest('last_accessed_at')->get();

        $inProgress = $enrollments->whereNull('completed_at');
        $completed  = $enrollments->whereNotNull('completed_at');

        $skills = $user->skills()->withPivot('course_id')->get();
        $certificates = $user->certificates()->with('course')->latest('issued_at')->get();

        // Recommendations: popular published courses the user hasn't enrolled in,
        // prioritising the categories of courses they've already taken.
        $takenCourseIds = $enrollments->pluck('course_id')->all();
        $preferredCategoryIds = $enrollments->pluck('course.category_id')->filter()->unique()->all();

        $recommendations = Course::published()
            ->whereNotIn('id', $takenCourseIds ?: [0])
            ->withCount(['lessons', 'enrollments'])
            ->when($preferredCategoryIds, fn ($q) => $q->orderByRaw(
                'CASE WHEN category_id IN ('.implode(',', array_fill(0, count($preferredCategoryIds), '?')).') THEN 0 ELSE 1 END',
                $preferredCategoryIds
            ))
            ->orderByDesc('enrollments_count')
            ->take(4)->get();

        $stats = [
            'enrolled'  => $enrollments->count(),
            'completed' => $completed->count(),
            'skills'    => $skills->count(),
            'certs'     => $certificates->count(),
        ];

        return view('frontend.dashboard.index', compact(
            'user', 'inProgress', 'completed', 'skills',
            'certificates', 'recommendations', 'stats'
        ));
    }
}
