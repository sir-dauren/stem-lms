<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Certificate extends Model
{
    protected $fillable = [
        'user_id', 'course_id', 'certificate_number',
        'recipient_name', 'course_title', 'issued_at',
    ];

    protected $casts = ['issued_at' => 'datetime'];

    public function getRouteKeyName(): string { return 'certificate_number'; }

    protected static function booted(): void
    {
        static::creating(function (Certificate $cert) {
            if (blank($cert->certificate_number)) {
                $cert->certificate_number = 'STM-'.Str::upper(Str::random(4)).'-'.now()->format('Y').'-'.Str::upper(Str::random(4));
            }
        });
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function course(): BelongsTo { return $this->belongsTo(Course::class); }
}
