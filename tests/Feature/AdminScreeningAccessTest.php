<?php

namespace Tests\Feature;

use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminScreeningAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_list_prioritizes_compact_columns(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $component = Livewire::actingAs($admin)
            ->test(ListUsers::class)
            ->assertTableColumnVisible('name')
            ->assertTableColumnVisible('school.name')
            ->assertTableColumnVisible('role')
            ->assertTableColumnVisible('can_take_screening');

        foreach (['email_verified_at', 'streak_days', 'created_at', 'updated_at'] as $column) {
            $this->assertTrue(
                $component->instance()->getTable()->getColumn($column)->isToggledHiddenByDefault(),
                "Kolom {$column} seharusnya tersembunyi secara default.",
            );
        }
    }

    public function test_admin_can_reset_a_students_screening_access(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create([
            'role' => 'student',
            'can_take_screening' => false,
        ]);

        Livewire::actingAs($admin)
            ->test(ListUsers::class)
            ->assertTableActionVisible('resetScreening', $student)
            ->assertTableActionHasLabel('resetScreening', 'Kasih akses screening')
            ->assertTableActionHasIcon('resetScreening', 'heroicon-o-key')
            ->callTableAction('resetScreening', $student);

        $this->assertTrue($student->fresh()->can_take_screening);
    }
}
