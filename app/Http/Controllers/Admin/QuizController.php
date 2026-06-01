<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\HandlesTranslations;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    use HandlesTranslations;

    public function index()
    {
        $quizzes = Quiz::with(['course', 'category'])
            ->withCount(['questions', 'attempts'])->latest()->paginate(15);
        return view('admin.quizzes.index', compact('quizzes'));
    }

    public function create()
    {
        return view('admin.quizzes.edit', [
            'quiz'       => new Quiz(['passing_score' => 70, 'is_published' => true, 'is_standalone' => true]),
            'courses'    => Course::get()->pluck('title', 'id'),
            'categories' => Category::get()->pluck('name', 'id'),
        ]);
    }

    public function store(Request $request)
    {
        $quiz = Quiz::create($this->validateQuiz($request));
        return redirect()->route('admin.quizzes.edit', $quiz)->with('status', 'Тест создан. Добавьте вопросы.');
    }

    public function edit(Quiz $quiz)
    {
        $quiz->load('questions.options');
        return view('admin.quizzes.edit', [
            'quiz'       => $quiz,
            'courses'    => Course::get()->pluck('title', 'id'),
            'categories' => Category::get()->pluck('name', 'id'),
        ]);
    }

    public function update(Request $request, Quiz $quiz)
    {
        $quiz->update($this->validateQuiz($request));
        return back()->with('status', 'Тест сохранён.');
    }

    public function destroy(Quiz $quiz)
    {
        $quiz->delete();
        return redirect()->route('admin.quizzes.index')->with('status', 'Тест удалён.');
    }

    /** Save a single question with its options. */
    public function storeQuestion(Request $request, Quiz $quiz)
    {
        $request->validate(
            $this->translationRules(['question'], ['explanation']) + [
                'type'              => ['required', 'in:single,multiple'],
                'points'            => ['nullable', 'integer', 'min:1'],
                'options'           => ['required', 'array', 'min:2'],
                'options.*.text'    => ['required', 'array'],
                'options.*.text.'.config('app.fallback_locale') => ['required', 'string'],
                'correct'           => ['required', 'array', 'min:1'],
            ],
            $this->translationMessages(['question'])
        );

        DB::transaction(function () use ($request, $quiz) {
            $q = $quiz->questions()->create([
                'question'    => $this->translations($request, 'question'),
                'explanation' => $this->translations($request, 'explanation'),
                'type'        => $request->input('type'),
                'points'      => (int) $request->input('points', 1),
                'sort_order'  => (int) $quiz->questions()->max('sort_order') + 1,
            ]);

            $correct = (array) $request->input('correct', []);

            foreach ((array) $request->input('options', []) as $i => $opt) {
                $text = collect($opt['text'] ?? [])
                    ->map(fn ($v) => is_string($v) ? trim($v) : $v)
                    ->filter(fn ($v) => filled($v))->all();

                $q->options()->create([
                    'text'       => $text,
                    'is_correct' => in_array((string) $i, $correct, true),
                    'sort_order' => $i,
                ]);
            }
        });

        return back()->with('status', 'Вопрос добавлен.');
    }

    public function destroyQuestion(QuizQuestion $question)
    {
        $question->delete();
        return back()->with('status', 'Вопрос удалён.');
    }

    protected function validateQuiz(Request $request): array
    {
        $request->validate(
            $this->translationRules(['title'], ['description']) + [
                'course_id'          => ['nullable', 'exists:courses,id'],
                'category_id'        => ['nullable', 'exists:categories,id'],
                'passing_score'      => ['required', 'integer', 'min:1', 'max:100'],
                'time_limit_minutes' => ['nullable', 'integer', 'min:1'],
                'is_standalone'      => ['nullable', 'boolean'],
                'is_published'       => ['nullable', 'boolean'],
            ],
            $this->translationMessages(['title'])
        );

        return [
            'title'              => $this->translations($request, 'title'),
            'description'        => $this->translations($request, 'description'),
            'course_id'          => $request->input('course_id'),
            'category_id'        => $request->input('category_id'),
            'passing_score'      => (int) $request->input('passing_score'),
            'time_limit_minutes' => $request->input('time_limit_minutes') ?: null,
            'is_standalone'      => $request->boolean('is_standalone'),
            'is_published'       => $request->boolean('is_published'),
        ];
    }
}
