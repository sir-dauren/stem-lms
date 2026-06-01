<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Quiz;

class HomeController extends Controller
{
    public function index()
    {
        $featured = Course::published()->where('is_featured', true)
            ->withCount(['lessons', 'enrollments', 'likes'])
            ->latest('published_at')->take(6)->get();

        $newest = Course::published()
            ->withCount(['lessons', 'enrollments'])
            ->latest('published_at')->take(8)->get();

        $categories = Category::whereNull('parent_id')->where('is_active', true)
            ->withCount('courses')->orderBy('sort_order')->take(8)->get();

        $stats = [
            'courses'   => Course::published()->count(),
            'learners'  => Enrollment::distinct('user_id')->count('user_id'),
            'certs'     => Certificate::count(),
            'quizzes'   => Quiz::where('is_published', true)->count(),
        ];

        return view('frontend.home', compact('featured', 'newest', 'categories', 'stats'));
    }

    public function about()
    {
        return view('frontend.about');
    }
}
