<?php

namespace Tests\Feature;

use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\Classroom;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminUsersBySchoolAndClassTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_selects_school_and_class_before_users_are_listed(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $firstSchool = School::create(['name' => 'SMA Mentari Satu', 'code' => 'SMS-01']);
        $secondSchool = School::create(['name' => 'SMA Mentari Dua', 'code' => 'SMS-02']);
        $classX = Classroom::create([
            'school_id' => $firstSchool->id,
            'name' => 'X',
            'sort_order' => 1,
        ]);
        $classXI = Classroom::create([
            'school_id' => $firstSchool->id,
            'name' => 'XI',
            'sort_order' => 2,
        ]);
        $otherClassX = Classroom::create([
            'school_id' => $secondSchool->id,
            'name' => 'X',
            'sort_order' => 1,
        ]);
        $classXStudent = User::factory()->create([
            'school_id' => $firstSchool->id,
            'classroom_id' => $classX->id,
            'name' => 'Siswa Kelas Sepuluh',
            'role' => 'student',
            'level' => 'X',
        ]);
        $classXIStudent = User::factory()->create([
            'school_id' => $firstSchool->id,
            'classroom_id' => $classXI->id,
            'name' => 'Siswa Kelas Sebelas',
            'role' => 'student',
            'level' => 'XI',
        ]);
        $otherSchoolStudent = User::factory()->create([
            'school_id' => $secondSchool->id,
            'classroom_id' => $otherClassX->id,
            'name' => 'Siswa Sekolah Lain',
            'role' => 'student',
            'level' => 'X',
        ]);

        $this->actingAs($admin)
            ->get('/admin/users')
            ->assertOk()
            ->assertSee('Pengguna Berdasarkan Sekolah dan Kelas')
            ->assertSee('1. Pilih Sekolah');

        $component = Livewire::actingAs($admin)
            ->test(ListUsers::class)
            ->assertSee('1. Pilih Sekolah')
            ->assertSee($firstSchool->name)
            ->assertSee($secondSchool->name)
            ->assertDontSee($classXStudent->name);

        $component
            ->call('selectSchool', $firstSchool->id)
            ->assertSet('selectedSchoolId', $firstSchool->id)
            ->assertSee('2. Pilih Kelas')
            ->assertSee('Kelas X')
            ->assertSee('Kelas XI')
            ->assertDontSee($classXStudent->name);

        $component
            ->call('selectLevel', (string) $classX->id)
            ->assertSet('selectedLevel', (string) $classX->id)
            ->assertSee('Daftar Pengguna')
            ->assertCanSeeTableRecords([$classXStudent])
            ->assertCanNotSeeTableRecords([$classXIStudent, $otherSchoolStudent]);
    }

    public function test_users_without_a_class_remain_accessible(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $school = School::create(['name' => 'Sekolah Mentari', 'code' => 'SM-01']);
        $userWithoutClass = User::factory()->create([
            'school_id' => $school->id,
            'name' => 'Pengguna Tanpa Kelas',
            'level' => null,
        ]);

        Livewire::actingAs($admin)
            ->test(ListUsers::class)
            ->call('selectSchool', $school->id)
            ->assertSee('Kelas belum diisi')
            ->call('selectLevel', '__empty__')
            ->assertCanSeeTableRecords([$userWithoutClass]);
    }

    public function test_users_without_a_school_remain_accessible(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $userWithoutSchool = User::factory()->create([
            'school_id' => null,
            'name' => 'Pengguna Tanpa Sekolah',
            'level' => null,
        ]);

        Livewire::actingAs($admin)
            ->test(ListUsers::class)
            ->assertSee('Sekolah belum diisi')
            ->call('selectSchool', 0)
            ->assertSee('Kelas belum diisi')
            ->call('selectLevel', '__empty__')
            ->assertCanSeeTableRecords([$userWithoutSchool]);
    }
}
