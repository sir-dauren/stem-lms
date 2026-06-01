<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class Quiz extends Model
{
    use HasTranslations;

    public array $translatable = ['title', 'description'];

    protected $fillable = [
        'course_id', 'category_id', 'title', 'slug', 'description',
        'passing_score', 'time_limit_minutes', 'is_standalone', 'is_published',
    ];

    protected $casts = ['is_standalone' => 'boolean', 'is_published' => 'boolean'];

    public function getRouteKeyName(): string { return 'slug'; }

    protected static function booted(): void
    {
        static::saving(function (Quiz $quiz) {
            if (blank($quiz->slug)) {
                $title = $quiz->getTranslation('title', config('app.fallback_locale'), false)
                    ?: $quiz->getTranslation('title', app()->getLocale());
                $base = Str::slug((string) $title) ?: 'quiz';
                $slug = $base; $i = 1;
                while (static::where('slug', $slug)->where('id', '!=', $quiz->id)->exists()) {
                    $slug = $base.'-'.(++$i);
                }
                $quiz->slug = $slug;
            }
        });
    }

    public function course(): BelongsTo { return $this->belongsTo(Course::class); }
    public function category(): BelongsTo { return $this->belongsTo(Category::class); }

    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('sort_order');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function totalPoints(): int
    {
        return (int) $this->questions()->sum('points');
    }
}
