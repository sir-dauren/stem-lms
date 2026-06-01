<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class Skill extends Model
{
    use HasTranslations;

    public array $translatable = ['name'];

    protected $fillable = ['name', 'slug'];

    protected static function booted(): void
    {
        static::saving(function (Skill $skill) {
            if (blank($skill->slug)) {
                $base = $skill->getTranslation('name', config('app.fallback_locale'), false)
                    ?: $skill->getTranslation('name', app()->getLocale());
                $skill->slug = Str::slug((string) $base);
            }
        });
    }

    /**
     * Find or create a skill from a plain name string (stored under the default locale).
     */
    public static function fromName(string $name): self
    {
        $name = trim($name);
        $slug = Str::slug($name) ?: Str::slug(Str::random(6));

        $skill = static::firstOrNew(['slug' => $slug]);
        if (! $skill->exists) {
            $skill->setTranslation('name', config('app.fallback_locale'), $name);
            $skill->save();
        }

        return $skill;
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_skill');
    }
}
