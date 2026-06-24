<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->boolean('can_take_screening')->default(true);
        });

        DB::table('users')
            ->whereExists(fn ($query) => $query
                ->selectRaw('1')
                ->from('screening_results')
                ->whereColumn('screening_results.user_id', 'users.id'))
            ->update(['can_take_screening' => false]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('can_take_screening');
        });
    }
};
