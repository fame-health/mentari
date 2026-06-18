<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('risk_alerts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('screening_result_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('level', ['stable', 'attention', 'urgent']);
            $table->string('title', 150);
            $table->text('message');
            $table->text('recommendation');
            $table->timestamp('dismissed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'level', 'created_at']);
            $table->index(['dismissed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risk_alerts');
    }
};
