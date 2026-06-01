<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class Lesson extends Model
{
    use HasTranslations;

    public array $translatable = ['title', 'content'];

    protected $fillable = [
        'section_id', 'course_id', 'title', 'slug', 'type', 'content',
        'video_path', 'video_url', 'duration_minutes', 'is_preview', 'sort_order',
    ];

    protected $casts = ['is_preview' => 'boolean'];

    protected static function booted(): void
    {
        static::saving(function (Lesson $lesson) {
            if (blank($lesson->slug)) {
                $title = $lesson->getTranslation('title', config('app.fallback_locale'), false)
                    ?: $lesson->getTranslation('title', app()->getLocale());
                $lesson->slug = Str::slug((string) $title).'-'.Str::lower(Str::random(5));
            }
        });
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'section_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function materials(): HasMany
    {
        return $this->hasMany(LessonMaterial::class)->orderBy('sort_order');
    }

    public function embedUrl(): ?string
    {
        if (! $this->video_url) {
            return null;
        }
        $url = $this->video_url;
        if (preg_match('~youtube\.com/watch\?v=([\w-]+)~', $url, $m) || preg_match('~youtu\.be/([\w-]+)~', $url, $m)) {
            return "https://www.youtube.com/embed/{$m[1]}";
        }
        if (preg_match('~vimeo\.com/(\d+)~', $url, $m)) {
            return "https://player.vimeo.com/video/{$m[1]}";
        }
        return $url;
    }
}
