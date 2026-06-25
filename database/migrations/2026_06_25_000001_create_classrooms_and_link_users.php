<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classrooms', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('name', 50);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['school_id', 'name']);
            $table->index(['school_id', 'is_active', 'sort_order']);
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->foreignId('classroom_id')
                ->nullable()
                ->after('school_id')
                ->constrained('classrooms')
                ->nullOnDelete();
        });

        $legacyClasses = DB::table('users')
            ->whereNotNull('school_id')
            ->whereNotNull('level')
            ->where('level', '!=', '')
            ->select(['school_id', 'level'])
            ->distinct()
            ->orderBy('school_id')
            ->orderBy('level')
            ->get()
            ->map(fn (object $legacyClass): object => (object) [
                'school_id' => $legacyClass->school_id,
                'level' => trim($legacyClass->level),
            ])
            ->unique(fn (object $legacyClass): string => $legacyClass->school_id.'|'.$legacyClass->level)
            ->values();

        $schoolOrder = [];

        foreach ($legacyClasses as $legacyClass) {
            $schoolOrder[$legacyClass->school_id] = ($schoolOrder[$legacyClass->school_id] ?? 0) + 1;
            $classroomId = DB::table('classrooms')->insertGetId([
                'school_id' => $legacyClass->school_id,
                'name' => $legacyClass->level,
                'sort_order' => $schoolOrder[$legacyClass->school_id],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('users')
                ->where('school_id', $legacyClass->school_id)
                ->where('level', $legacyClass->level)
                ->update(['classroom_id' => $classroomId]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('classroom_id');
        });

        Schema::dropIfExists('classrooms');
    }
};
