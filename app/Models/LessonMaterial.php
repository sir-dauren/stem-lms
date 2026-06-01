<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class LessonMaterial extends Model
{
    use HasTranslations;

    public array $translatable = ['title'];

    protected $fillable = [
        'lesson_id', 'course_id', 'title', 'file_path', 'original_name',
        'mime_type', 'extension', 'size', 'sort_order',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function getHumanSizeAttribute(): string
    {
        $bytes = (int) $this->size;
        if ($bytes <= 0) return '—';
        $units = ['B', 'KB', 'MB', 'GB'];
        $pow = min((int) floor(log($bytes, 1024)), count($units) - 1);
        return round($bytes / (1024 ** $pow), 1).' '.$units[$pow];
    }

    public function getIconAttribute(): string
    {
        return match (strtolower((string) $this->extension)) {
            'pdf' => '📕',
            'doc', 'docx' => '📘',
            'xls', 'xlsx', 'csv' => '📗',
            'ppt', 'pptx' => '📙',
            'zip', 'rar', '7z' => '🗜️',
            'png', 'jpg', 'jpeg', 'gif', 'webp' => '🖼️',
            'mp4', 'mov', 'avi', 'mkv' => '🎬',
            default => '📄',
        };
    }
}
