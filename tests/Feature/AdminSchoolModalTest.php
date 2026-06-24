<?php

namespace Tests\Feature;

use App\Filament\Resources\Schools\Pages\ListSchools;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminSchoolModalTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_creates_school_from_modal_with_automatic_code(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $component = Livewire::actingAs($admin)
            ->test(ListSchools::class);

        $this->assertNull($component->instance()->getAction('create')->getUrl());

        $component->callAction('create', data: [
            'name' => 'SMA Negeri 1 Mentari',
            'address' => 'Jl. Mentari No. 1',
        ]);

        $this->assertDatabaseHas('schools', [
            'name' => 'SMA Negeri 1 Mentari',
            'code' => 'SMA-NEGERI-1-MENTARI',
            'address' => 'Jl. Mentari No. 1',
        ]);
    }

    public function test_automatic_school_code_is_unique(): void
    {
        School::create(['name' => 'SMA Mentari']);
        $duplicate = School::create(['name' => 'SMA Mentari']);

        $this->assertSame('SMA-MENTARI-2', $duplicate->code);
    }

    public function test_view_and_edit_school_use_modals(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $school = School::create([
            'name' => 'SMP Mentari',
            'address' => 'Jl. Pelajar No. 2',
        ]);

        $component = Livewire::actingAs($admin)
            ->test(ListSchools::class)
            ->assertTableActionDoesNotHaveUrl('view', $school)
            ->assertTableActionDoesNotHaveUrl('edit', $school);

        $component
            ->callTableAction('edit', $school, data: [
                'name' => 'SMP Mentari Utama',
                'address' => 'Jl. Pelajar No. 3',
            ]);

        $this->assertDatabaseHas('schools', [
            'id' => $school->id,
            'name' => 'SMP Mentari Utama',
            'code' => 'SMP-MENTARI',
            'address' => 'Jl. Pelajar No. 3',
        ]);
    }
}
