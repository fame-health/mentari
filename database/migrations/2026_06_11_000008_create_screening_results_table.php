<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('screening_results', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('taken_at');
            $table->unsignedSmallInteger('depression_score');
            $table->enum('depression_severity', ['normal', 'mild', 'moderate', 'severe', 'extremely_severe']);
            $table->unsignedSmallInteger('anxiety_score');
            $table->enum('anxiety_severity', ['normal', 'mild', 'moderate', 'severe', 'extremely_severe']);
            $table->unsignedSmallInteger('stress_score');
            $table->enum('stress_severity', ['normal', 'mild', 'moderate', 'severe', 'extremely_severe']);
            $table->text('summary');
            $table->timestamps();

            $table->index(['user_id', 'taken_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('screening_results');
    }
};
