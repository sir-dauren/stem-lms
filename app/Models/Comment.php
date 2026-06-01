<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comment extends Model
{
    protected $fillable = ['user_id', 'course_id', 'parent_id', 'body', 'is_hidden', 'is_staff'];

    protected $casts = ['is_hidden' => 'boolean', 'is_staff' => 'boolean'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function course(): BelongsTo { return $this->belongsTo(Course::class); }
    public function parent(): BelongsTo { return $this->belongsTo(Comment::class, 'parent_id'); }

    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')->oldest();
    }
}
