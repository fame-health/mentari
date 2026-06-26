<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('screening_results', function (Blueprint $table): void {
            $table->foreignId('recommendation_id')
                ->nullable()
                ->after('summary')
                ->constrained('recommendations')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('screening_results', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('recommendation_id');
        });
    }
};
