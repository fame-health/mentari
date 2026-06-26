<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recommendations', function (Blueprint $table): void {
            $table->enum('severity', ['normal', 'mild', 'moderate', 'severe', 'extremely_severe'])
                ->nullable()
                ->after('category');

            $table->index(['category', 'severity', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::table('recommendations', function (Blueprint $table): void {
            $table->dropIndex(['category', 'severity', 'is_active']);
            $table->dropColumn('severity');
        });
    }
};
