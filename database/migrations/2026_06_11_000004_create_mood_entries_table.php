<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mood_entries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('mood_option_id')->constrained()->restrictOnDelete();
            $table->date('entry_date');
            $table->text('note')->nullable();
            $table->unsignedTinyInteger('energy');
            $table->unsignedTinyInteger('stress');
            $table->timestamps();

            $table->unique(['user_id', 'entry_date']);
            $table->index(['user_id', 'entry_date']);
        });

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE mood_entries ADD CONSTRAINT mood_entries_energy_check CHECK (energy BETWEEN 0 AND 10)');
            DB::statement('ALTER TABLE mood_entries ADD CONSTRAINT mood_entries_stress_check CHECK (stress BETWEEN 0 AND 10)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('mood_entries');
    }
};
