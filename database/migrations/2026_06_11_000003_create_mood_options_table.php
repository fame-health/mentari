<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mood_options', function (Blueprint $table): void {
            $table->id();
            $table->string('key', 50)->unique();
            $table->string('emoji', 16);
            $table->string('label', 80);
            $table->string('description')->nullable();
            $table->char('color', 10);
            $table->unsignedTinyInteger('score');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mood_options');
    }
};
