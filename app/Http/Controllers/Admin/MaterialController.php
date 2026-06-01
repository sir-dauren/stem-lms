<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    /** Attach one or many documents (PDF, Word, Excel, PPT, ZIP, images...) to a course/lesson. */
    public function store(Request $request, Course $course)
    {
        $request->validate([
            'files'     => ['required', 'array'],
            'files.*'   => [
                'file', 'max:51200', // 50MB per file (adjust php.ini accordingly)
                'mimes:pdf,doc,docx,xls,xlsx,csv,ppt,pptx,txt,zip,rar,7z,png,jpg,jpeg,gif,webp,mp4',
            ],
            'lesson_id' => ['nullable', 'exists:lessons,id'],
            'title'     => ['nullable', 'string', 'max:160'],
        ]);

        $lessonId = $request->input('lesson_id');
        $order = (int) $course->materials()->max('sort_order');
        $default = config('app.fallback_locale');
        $customTitle = trim((string) $request->input('title'));

        foreach ($request->file('files') as $file) {
            $stored = $file->store('materials/'.$course->id, 'public');
            $name = $customTitle ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            LessonMaterial::create([
                'course_id'     => $course->id,
                'lesson_id'     => $lessonId ?: null,
                'title'         => [$default => $name],
                'file_path'     => $stored,
                'original_name' => $file->getClientOriginalName(),
                'mime_type'     => $file->getClientMimeType(),
                'extension'     => strtolower($file->getClientOriginalExtension()),
                'size'          => $file->getSize(),
                'sort_order'    => ++$order,
            ]);
        }

        return back()->with('status', 'Материалы загружены.');
    }

    public function destroy(LessonMaterial $material)
    {
        Storage::disk('public')->delete($material->file_path);
        $material->delete();
        return back()->with('status', 'Материал удалён.');
    }
}
