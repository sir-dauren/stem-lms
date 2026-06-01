<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class Category extends Model
{
    use HasFactory;
    use HasTranslations;

    public array $translatable = ['name', 'description'];

    protected $fillable = [
        'parent_id', 'name', 'slug', 'description', 'icon', 'color', 'sort_order', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    protected static function booted(): void
    {
        static::saving(function (Category $category) {
            if (blank($category->slug)) {
                $category->slug = static::uniqueSlug($category->defaultName(), $category->id);
            }
        });
    }

    public function defaultName(): string
    {
        return (string) $this->getTranslation('name', config('app.fallback_locale'), false)
            ?: (string) $this->getTranslation('name', app()->getLocale());
    }

    public static function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'category';
        $slug = $base; $i = 1;
        while (static::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base.'-'.(++$i);
        }
        return $slug;
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order');
    }

    // Recursive eager-loadable tree
    public function descendants(): HasMany
    {
        return $this->children()->with('descendants');
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function getDepthAttribute(): int
    {
        $depth = 0; $node = $this;
        while ($node->parent_id) { $depth++; $node = $node->parent; }
        return $depth;
    }

    public function breadcrumb(): array
    {
        $trail = []; $node = $this;
        while ($node) { array_unshift($trail, $node); $node = $node->parent; }
        return $trail;
    }
}
