<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $courses = Course::with('category')
            ->withCount(['lessons', 'enrollments'])
            ->when($request->q, fn ($q) => $q->where('title', 'like', '%'.$request->q.'%'))
            ->latest()->paginate(15)->withQueryString();

        return view('admin.courses.index', compact('courses'));
    }

    public function create()
    {
        return view('admin.courses.create', [
            'course'     => new Course(['level' => 'beginner', 'language' => 'Русский']),
            'categories' => $this->categoryOptions(),
            'allSkills'  => Skill::orderBy('slug')->get()->pluck('name'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateCourse($request);
        $course = new Course($data);

        if ($request->hasFile('thumbnail')) {
            $course->thumbnail = $request->file('thumbnail')->store('courses', 'public');
        }
        $course->published_at = $course->is_published ? now() : null;
        $course->save();

        $this->syncSkills($course, $request->input('skills'));

        return redirect()->route('admin.courses.edit', $course)
            ->with('status', 'Курс создан. Теперь добавьте разделы и уроки.');
    }

    public function edit(Course $course)
    {
        $course->load([
            'sections.lessons.materials',
            'materials',
            'quizzes.questions',
        ]);

        return view('admin.courses.edit', [
            'course'     => $course,
            'categories' => $this->categoryOptions(),
            'allSkills'  => Skill::orderBy('slug')->get()->pluck('name'),
        ]);
    }

    public function update(Request $request, Course $course)
    {
        $data = $this->validateCourse($request, $course);

        if ($request->hasFile('thumbnail')) {
            if ($course->thumbnail) Storage::disk('public')->delete($course->thumbnail);
            $data['thumbnail'] = $request->file('thumbnail')->store('courses', 'public');
        }

        $wasPublished = $course->is_published;
        $course->fill($data);
        if (! $wasPublished && $course->is_published) {
            $course->published_at = now();
        }
        $course->save();

        $this->syncSkills($course, $request->input('skills'));

        return back()->with('status', 'Курс сохранён.');
    }

    public function destroy(Course $course)
    {
        if ($course->thumbnail) Storage::disk('public')->delete($course->thumbnail);
        $course->delete();
        return redirect()->route('admin.courses.index')->with('status', 'Курс удалён.');
    }

    public function togglePublish(Course $course)
    {
        $course->is_published = ! $course->is_published;
        if ($course->is_published && ! $course->published_at) $course->published_at = now();
        $course->save();
        return back()->with('status', $course->is_published ? 'Курс опубликован.' : 'Курс снят с публикации.');
    }

    protected function validateCourse(Request $request, ?Course $course = null): array
    {
        $default = config('app.fallback_locale');

        $request->validate([
            'title'            => ['required', 'array'],
            'title.'.$default  => ['required', 'string', 'max:160'],
            'subtitle'         => ['nullable', 'array'],
            'description'      => ['nullable', 'array'],
            'outcomes'         => ['nullable', 'array'],
            'requirements'     => ['nullable', 'array'],
            'category_id'      => ['nullable', 'exists:categories,id'],
            'level'            => ['required', 'in:beginner,intermediate,advanced'],
            'language'         => ['nullable', 'string', 'max:40'],
            'duration_minutes' => ['nullable', 'integer', 'min:0'],
            'instructor_name'  => ['nullable', 'string', 'max:120'],
            'instructor_title' => ['nullable', 'string', 'max:120'],
            'promo_video'      => ['nullable', 'string', 'max:255'],
            'thumbnail'        => ['nullable', 'image', 'max:4096'],
            'is_published'     => ['nullable', 'boolean'],
            'is_featured'      => ['nullable', 'boolean'],
        ], [
            'title.'.$default.'.required' => 'Укажите название курса хотя бы на языке по умолчанию.',
        ]);

        return [
            'title'            => $this->cleanTranslations($request->input('title', [])),
            'subtitle'         => $this->cleanTranslations($request->input('subtitle', [])),
            'description'      => $this->cleanTranslations($request->input('description', [])),
            'outcomes'         => $this->cleanTranslations($request->input('outcomes', [])),
            'requirements'     => $this->cleanTranslations($request->input('requirements', [])),
            'category_id'      => $request->input('category_id'),
            'level'            => $request->input('level'),
            'language'         => $request->input('language'),
            'duration_minutes' => (int) $request->input('duration_minutes', 0),
            'instructor_name'  => $request->input('instructor_name'),
            'instructor_title' => $request->input('instructor_title'),
            'promo_video'      => $request->input('promo_video'),
            'is_published'     => $request->boolean('is_published'),
            'is_featured'      => $request->boolean('is_featured'),
        ];
    }

    /**
     * Drop empty locale values so we don't store blank translations.
     *
     * @return array<string,string>
     */
    protected function cleanTranslations(array $values): array
    {
        return collect($values)
            ->map(fn ($v) => is_string($v) ? trim($v) : $v)
            ->filter(fn ($v) => filled($v))
            ->all();
    }

    protected function syncSkills(Course $course, ?string $raw): void
    {
        $names = collect(explode(',', (string) $raw))->map(fn ($n) => trim($n))->filter()->unique();
        $ids = $names->map(fn ($n) => Skill::fromName($n)->id)->all();
        $course->skills()->sync($ids);
    }

    protected function categoryOptions()
    {
        $roots = Category::whereNull('parent_id')->with('descendants')->orderBy('sort_order')->get();
        $options = [];
        $walk = function ($nodes, $depth) use (&$walk, &$options) {
            foreach ($nodes as $node) {
                $options[] = ['id' => $node->id, 'label' => str_repeat('— ', $depth).$node->name];
                if ($node->children->isNotEmpty()) $walk($node->children, $depth + 1);
            }
        };
        $walk($roots, 0);
        return $options;
    }
}
