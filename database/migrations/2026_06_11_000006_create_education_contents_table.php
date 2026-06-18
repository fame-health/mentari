<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('education_contents', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('education_category_id')->constrained()->restrictOnDelete();
            $table->string('title', 180);
            $table->enum('type', ['article', 'infographic', 'video']);
            $table->unsignedSmallInteger('read_time_minutes')->nullable();
            $table->string('read_time_label', 50)->nullable();
            $table->text('summary');
            $table->longText('body')->nullable();
            $table->string('media_url')->nullable();
            $table->char('accent_color', 10)->nullable();
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['education_category_id', 'is_active']);
            $table->index(['published_at', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('education_contents');
    }
};
