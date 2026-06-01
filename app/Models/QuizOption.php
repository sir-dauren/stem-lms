<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class QuizOption extends Model
{
    use HasTranslations;

    public array $translatable = ['text'];

    protected $fillable = ['question_id', 'text', 'is_correct', 'sort_order'];

    protected $casts = ['is_correct' => 'boolean'];

    public function question(): BelongsTo { return $this->belongsTo(QuizQuestion::class, 'question_id'); }
}
