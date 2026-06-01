<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class QuizQuestion extends Model
{
    use HasTranslations;

    public array $translatable = ['question', 'explanation'];

    protected $fillable = ['quiz_id', 'question', 'type', 'explanation', 'points', 'sort_order'];

    public function quiz(): BelongsTo { return $this->belongsTo(Quiz::class); }

    public function options(): HasMany
    {
        return $this->hasMany(QuizOption::class, 'question_id')->orderBy('sort_order');
    }

    public function correctOptionIds(): array
    {
        return $this->options->where('is_correct', true)->pluck('id')->all();
    }
}
