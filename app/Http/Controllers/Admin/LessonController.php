<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\HandlesTranslations;
use App\Http\Controllers\Controller;
use App\Models\CourseSection;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LessonController extends Controller
{
    use HandlesTranslations;

    public function store(Request $request, CourseSection $section)
    {
        $request->validate(
            $this->translationRules(['title'], ['content']) + [
                'type'             => ['required', 'in:video,article,quiz'],
                'video_url'        => ['nullable', 'string', 'max:255'],
                'video_file'       => ['nullable', 'file', 'mimetypes:video/mp4,video/quicktime,video/x-matroska,video/webm', 'max:1048576'],
                'duration_minutes' => ['nullable', 'integer', 'min:0'],
                'is_preview'       => ['nullable', 'boolean'],
            ],
            $this->translationMessages(['title'])
        );

        $lesson = new Lesson([
            'section_id'       => $section->id,
            'course_id'        => $section->course_id,
            'title'            => $this->translations($request, 'title'),
            'content'          => $this->translations($request, 'content'),
            'type'             => $request->input('type'),
            'video_url'        => $request->input('video_url'),
            'duration_minutes' => (int) $request->input('duration_minutes', 0),
            'is_preview'       => $request->boolean('is_preview'),
            'sort_order'       => (int) $section->lessons()->max('sort_order') + 1,
        ]);

        if ($request->hasFile('video_file')) {
            $lesson->video_path = $request->file('video_file')->store('videos', 'public');
        }

        $lesson->save();

        return back()->with('status', 'Урок добавлен.');
    }

    public function update(Request $request, Lesson $lesson)
    {
        $request->validate(
            $this->translationRules(['title'], ['content']) + [
                'type'             => ['required', 'in:video,article,quiz'],
                'video_url'        => ['nullable', 'string', 'max:255'],
                'video_file'       => ['nullable', 'file', 'mimetypes:video/mp4,video/quicktime,video/x-matroska,video/webm', 'max:1048576'],
                'duration_minutes' => ['nullable', 'integer', 'min:0'],
                'is_preview'       => ['nullable', 'boolean'],
            ],
            $this->translationMessages(['title'])
        );

        $lesson->fill([
            'title'            => $this->translations($request, 'title'),
            'content'          => $this->translations($request, 'content'),
            'type'             => $request->input('type'),
            'video_url'        => $request->input('video_url'),
            'duration_minutes' => (int) $request->input('duration_minutes', 0),
            'is_preview'       => $request->boolean('is_preview'),
        ]);

        if ($request->hasFile('video_file')) {
            if ($lesson->video_path) Storage::disk('public')->delete($lesson->video_path);
            $lesson->video_path = $request->file('video_file')->store('videos', 'public');
        }

        $lesson->save();

        return back()->with('status', 'Урок обновлён.');
    }

    public function reorder(Request $request, CourseSection $section)
    {
        foreach ($request->input('order', []) as $pos => $id) {
            Lesson::where('id', $id)->where('section_id', $section->id)
                ->update(['sort_order' => $pos]);
        }
        return response()->json(['ok' => true]);
    }

    public function destroy(Lesson $lesson)
    {
        if ($lesson->video_path) Storage::disk('public')->delete($lesson->video_path);
        $lesson->delete();
        return back()->with('status', 'Урок удалён.');
    }
}
