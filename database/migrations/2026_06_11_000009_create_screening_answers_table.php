<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('screening_answers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('screening_result_id')->constrained()->cascadeOnDelete();
            $table->foreignId('screening_question_id')->constrained()->restrictOnDelete();
            $table->unsignedTinyInteger('score');
            $table->timestamps();

            $table->unique(['screening_result_id', 'screening_question_id'], 'screening_answers_result_question_unique');
        });

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE screening_answers ADD CONSTRAINT screening_answers_score_check CHECK (score BETWEEN 0 AND 3)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('screening_answers');
    }
};
