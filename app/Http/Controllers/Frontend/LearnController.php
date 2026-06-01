<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LearnController extends Controller
{
    /** Enroll the authenticated user, then jump into the player. */
    public function enroll(Course $course)
    {
        abort_unless($course->is_published, 404);
        $user = Auth::user();

        Enrollment::firstOrCreate(
            ['user_id' => $user->id, 'course_id' => $course->id],
            ['progress' => 0, 'last_accessed_at' => now()]
        );

        $first = $course->lessons()->orderBy('sort_order')->first();

        return $first
            ? redirect()->route('learn.lesson', [$course, $first])
            : redirect()->route('courses.show', $course)->with('status', 'В курсе пока нет уроков.');
    }

    /** The learning player. */
    public function lesson(Course $course, Lesson $lesson)
    {
        abort_unless($course->is_published, 404);
        abort_unless($lesson->course_id === $course->id, 404);

        $user = Auth::user();
        $enrollment = Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->first();

        // Unauthorized/non-enrolled users cannot access non-preview lessons.
        if (! $enrollment && ! $lesson->is_preview) {
            return redirect()->route('courses.show', $course)
                ->with('status', 'Запишитесь на курс, чтобы получить доступ к урокам.');
        }

        if ($enrollment) {
            $enrollment->update(['last_accessed_at' => now()]);
        }

        $course->load([
            'sections.lessons' => fn ($q) => $q->orderBy('sort_order'),
            'quizzes' => fn ($q) => $q->where('is_published', true),
        ]);
        $lesson->load('materials');

        $completedIds = $user->completedLessons()->wherePivot('course_id', $course->id)->pluck('lessons.id')->all();

        $flat = $course->lessons()->orderBy('sort_order')->get();
        $idx = $flat->search(fn ($l) => $l->id === $lesson->id);
        $prev = $idx > 0 ? $flat[$idx - 1] : null;
        $next = $idx !== false && $idx < $flat->count() - 1 ? $flat[$idx + 1] : null;

        return view('frontend.learn.player', compact(
            'course', 'lesson', 'enrollment', 'completedIds', 'prev', 'next'
        ));
    }

    /** Mark a lesson complete and recompute course progress. */
    public function complete(Request $request, Course $course, Lesson $lesson)
    {
        $user = Auth::user();
        $enrollment = Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->firstOrFail();

        $user->completedLessons()->syncWithoutDetaching([
            $lesson->id => ['course_id' => $course->id, 'completed_at' => now()],
        ]);

        $total = $course->lessons()->count();
        $done  = $user->completedLessons()->wherePivot('course_id', $course->id)->count();
        $progress = $total ? (int) round($done / $total * 100) : 0;

        $enrollment->progress = $progress;
        $justCompleted = false;

        if ($progress >= 100 && ! $enrollment->completed_at) {
            $enrollment->completed_at = now();
            $justCompleted = true;
            $this->grantCompletion($user, $course);
        }
        $enrollment->save();

        if ($request->wantsJson()) {
            return response()->json(['progress' => $progress, 'completed' => $justCompleted]);
        }

        return back()->with('status', $justCompleted
            ? 'Поздравляем! Курс пройден — сертификат уже в личном кабинете.'
            : 'Урок отмечен как пройденный.');
    }

    /** Issue certificate + grant skills on completion. */
    protected function grantCompletion($user, Course $course): void
    {
        DB::transaction(function () use ($user, $course) {
            Certificate::firstOrCreate(
                ['user_id' => $user->id, 'course_id' => $course->id],
                [
                    'recipient_name' => $user->name,
                    'course_title'   => $course->title,
                    'issued_at'      => now(),
                ]
            );

            foreach ($course->skills as $skill) {
                $user->skills()->syncWithoutDetaching([
                    $skill->id => ['course_id' => $course->id],
                ]);
            }
        });
    }
}
