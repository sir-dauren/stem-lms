<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\HandlesTranslations;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseSection;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    use HandlesTranslations;

    public function store(Request $request, Course $course)
    {
        $request->validate(
            $this->translationRules(['title'], ['summary']),
            $this->translationMessages(['title'])
        );

        $course->sections()->create([
            'title'      => $this->translations($request, 'title'),
            'summary'    => $this->translations($request, 'summary'),
            'sort_order' => (int) $course->sections()->max('sort_order') + 1,
        ]);

        return back()->with('status', 'Раздел добавлен.');
    }

    public function update(Request $request, CourseSection $section)
    {
        $request->validate(
            $this->translationRules(['title'], ['summary']),
            $this->translationMessages(['title'])
        );

        $section->update([
            'title'   => $this->translations($request, 'title'),
            'summary' => $this->translations($request, 'summary'),
        ]);

        return back()->with('status', 'Раздел обновлён.');
    }

    public function reorder(Request $request, Course $course)
    {
        foreach ($request->input('order', []) as $pos => $id) {
            CourseSection::where('id', $id)->where('course_id', $course->id)
                ->update(['sort_order' => $pos]);
        }
        return response()->json(['ok' => true]);
    }

    public function destroy(CourseSection $section)
    {
        $section->delete();
        return back()->with('status', 'Раздел удалён.');
    }
}
