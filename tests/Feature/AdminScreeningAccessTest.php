<?php

namespace Tests\Feature;

use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\Classroom;
use App\Models\School;
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
        $school = School::create([
            'name' => 'SMA Mentari',
            'code' => 'SMA-MENTARI',
        ]);
        $classroom = Classroom::create([
            'school_id' => $school->id,
            'name' => 'X',
            'sort_order' => 1,
        ]);
        $student = User::factory()->create([
            'school_id' => $school->id,
            'classroom_id' => $classroom->id,
            'role' => 'student',
            'level' => 'X',
            'can_take_screening' => false,
        ]);

        $component = Livewire::actingAs($admin)
            ->test(ListUsers::class);

        $component->call('selectSchool', $school->id);
        $component->call('selectLevel', (string) $classroom->id);

        $component
            ->assertTableActionVisible('resetScreening', $student)
            ->assertTableActionHasLabel('resetScreening', 'Kasih akses screening')
            ->assertTableActionHasIcon('resetScreening', 'heroicon-o-key')
            ->callTableAction('resetScreening', $student);

        $this->assertTrue($student->fresh()->can_take_screening);
    }
}
