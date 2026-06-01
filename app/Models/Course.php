<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class Course extends Model
{
    use HasFactory;
    use HasTranslations;

    public array $translatable = ['title', 'subtitle', 'description', 'outcomes', 'requirements'];

    protected $fillable = [
        'category_id', 'title', 'slug', 'subtitle', 'description', 'outcomes',
        'requirements', 'thumbnail', 'promo_video', 'level', 'language',
        'duration_minutes', 'instructor_name', 'instructor_title',
        'is_published', 'is_featured', 'views', 'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_featured'  => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (Course $course) {
            if (blank($course->slug)) {
                $title = $course->getTranslation('title', config('app.fallback_locale'), false)
                    ?: $course->getTranslation('title', app()->getLocale());
                $base = Str::slug((string) $title) ?: 'course';
                $slug = $base; $i = 1;
                while (static::where('slug', $slug)->where('id', '!=', $course->id)->exists()) {
                    $slug = $base.'-'.(++$i);
                }
                $course->slug = $slug;
            }
        });
    }

    public function getRouteKeyName(): string { return 'slug'; }

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('is_published', true);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(CourseSection::class)->orderBy('sort_order');
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('sort_order');
    }

    public function materials(): HasMany
    {
        return $this->hasMany(LessonMaterial::class)->orderBy('sort_order');
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'course_skill');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id')->latest();
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function getOutcomesListAttribute(): array
    {
        return collect(preg_split('/\r\n|\r|\n/', (string) $this->outcomes))
            ->map(fn ($l) => trim($l))->filter()->values()->all();
    }

    public function getRequirementsListAttribute(): array
    {
        return collect(preg_split('/\r\n|\r|\n/', (string) $this->requirements))
            ->map(fn ($l) => trim($l))->filter()->values()->all();
    }

    public function getDurationLabelAttribute(): string
    {
        $h = intdiv($this->duration_minutes, 60);
        $m = $this->duration_minutes % 60;
        return $h ? "{$h}h {$m}m" : "{$m}m";
    }
}
