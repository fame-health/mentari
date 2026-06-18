<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recommendations', function (Blueprint $table): void {
            $table->id();
            $table->string('title', 150);
            $table->string('category', 100);
            $table->text('description');
            $table->unsignedSmallInteger('duration_minutes')->nullable();
            $table->string('duration_label', 50)->nullable();
            $table->string('priority', 50)->nullable();
            $table->char('accent_color', 10)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['category', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recommendations');
    }
};
