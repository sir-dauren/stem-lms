<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('course_sections')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->text('title');
            $table->string('slug');
            $table->enum('type', ['video', 'article', 'quiz'])->default('video');
            $table->longText('content')->nullable();        // HTML article body
            $table->string('video_path')->nullable();       // stored uploaded video
            $table->string('video_url')->nullable();        // OR external (YouTube/Vimeo)
            $table->unsignedInteger('duration_minutes')->default(0);
            $table->boolean('is_preview')->default(false);   // viewable w/o enrolment
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['course_id', 'section_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
