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

        $component
            ->mountAction('create')
            ->assertWizardStepExists(1)
            ->assertWizardStepExists(2);

        Livewire::actingAs($admin)
            ->test(ListSchools::class)
            ->callAction('create', data: [
                'name' => 'SMA Negeri 1 Mentari',
                'address' => 'Jl. Mentari No. 1',
                'classrooms' => [
                    [
                        'name' => 'X IPA 1',
                        'sort_order' => 1,
                        'is_active' => true,
                    ],
                    [
                        'name' => 'XI IPA 1',
                        'sort_order' => 2,
                        'is_active' => true,
                    ],
                ],
            ]);

        $this->assertDatabaseHas('schools', [
            'name' => 'SMA Negeri 1 Mentari',
            'code' => 'SMA-NEGERI-1-MENTARI',
            'address' => 'Jl. Mentari No. 1',
        ]);
        $this->assertDatabaseHas('classrooms', [
            'school_id' => School::query()->where('code', 'SMA-NEGERI-1-MENTARI')->value('id'),
            'name' => 'X IPA 1',
            'sort_order' => 1,
            'is_active' => true,
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

    public function test_view_school_modal_shows_polished_overview(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $school = School::create([
            'name' => 'SMA Mentari Visual',
            'address' => 'Jl. Modal Rapi No. 4',
        ]);

        $school->classrooms()->create([
            'name' => 'X IPA 1',
            'sort_order' => 1,
            'is_active' => true,
        ]);
        $school->classrooms()->create([
            'name' => 'XI IPS 1',
            'sort_order' => 2,
            'is_active' => false,
        ]);

        Livewire::actingAs($admin)
            ->test(ListSchools::class)
            ->mountTableAction('view', $school)
            ->assertMountedActionModalSee([
                'Profil sekolah',
                'SMA Mentari Visual',
                'Jl. Modal Rapi No. 4',
                'Daftar kelas',
                'X IPA 1',
                'XI IPS 1',
                'Riwayat data',
                'Alert aktif',
            ]);
    }

    public function test_admin_can_add_multiple_classes_from_a_school_row(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $school = School::create([
            'name' => 'SMA Mentari Kelas',
            'code' => 'SMK-01',
        ]);

        Livewire::actingAs($admin)
            ->test(ListSchools::class)
            ->assertTableActionVisible('manageClassrooms', $school)
            ->assertTableActionDoesNotHaveUrl('manageClassrooms', $school)
            ->callTableAction('manageClassrooms', $school, data: [
                'classrooms' => [
                    [
                        'id' => null,
                        'name' => 'X MIPA 1',
                        'sort_order' => 1,
                        'is_active' => true,
                    ],
                    [
                        'id' => null,
                        'name' => 'XI IPS 1',
                        'sort_order' => 2,
                        'is_active' => true,
                    ],
                ],
            ]);

        $this->assertDatabaseHas('classrooms', [
            'school_id' => $school->id,
            'name' => 'X MIPA 1',
            'sort_order' => 1,
        ]);
        $this->assertDatabaseHas('classrooms', [
            'school_id' => $school->id,
            'name' => 'XI IPS 1',
            'sort_order' => 2,
        ]);
        $this->assertSame(2, $school->classrooms()->count());
    }
}
