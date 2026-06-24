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
            ->callTableAction('resetScreening', $student);

        $this->assertTrue($student->fresh()->can_take_screening);
    }
}
