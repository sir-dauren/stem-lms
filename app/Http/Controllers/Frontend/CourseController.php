<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::published()
            ->with('category')
            ->withCount(['lessons', 'enrollments', 'likes']);

        if ($search = $request->string('q')->trim()->value()) {
            $query->where(fn ($q) => $q
                ->where('title', 'like', "%{$search}%")
                ->orWhere('subtitle', 'like', "%{$search}%"));
        }

        if ($cat = $request->string('category')->value()) {
            $category = Category::where('slug', $cat)->first();
            if ($category) {
                // include the category and ALL its descendants (any depth)
                $ids = $this->categoryAndDescendantIds($category);
                $query->whereIn('category_id', $ids);
            }
        }

        if ($level = $request->string('level')->value()) {
            $query->where('level', $level);
        }

        $sort = $request->string('sort', 'newest')->value();
        match ($sort) {
            'popular'  => $query->orderByDesc('enrollments_count'),
            'liked'    => $query->orderByDesc('likes_count'),
            default    => $query->latest('published_at'),
        };

        $courses = $query->paginate(12)->withQueryString();

        $categories = Category::whereNull('parent_id')->where('is_active', true)
            ->with('descendants')->withCount('courses')->orderBy('sort_order')->get();

        return view('frontend.courses.index', compact('courses', 'categories'));
    }

    public function show(Course $course)
    {
        abort_unless($course->is_published, 404);

        $course->increment('views');
        $course->load([
            'category', 'skills',
            'sections.lessons' => fn ($q) => $q->orderBy('sort_order'),
            'comments.user', 'comments.replies.user',
            'quizzes' => fn ($q) => $q->where('is_published', true),
        ]);
        $course->loadCount(['lessons', 'enrollments', 'likes', 'comments']);

        $user = Auth::user();
        $enrolled = $user && $user->isEnrolledIn($course);
        $liked = $user && $course->likes()->where('user_id', $user->id)->exists();

        $related = Course::published()->where('id', '!=', $course->id)
            ->when($course->category_id, fn ($q) => $q->where('category_id', $course->category_id))
            ->withCount('lessons')->take(3)->get();

        return view('frontend.courses.show', compact('course', 'enrolled', 'liked', 'related'));
    }

    protected function categoryAndDescendantIds(Category $category): array
    {
        $ids = [$category->id];
        $stack = [$category];
        while ($node = array_pop($stack)) {
            foreach ($node->children as $child) {
                $ids[] = $child->id;
                $stack[] = $child;
            }
        }
        return $ids;
    }
}
