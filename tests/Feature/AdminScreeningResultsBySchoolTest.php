<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\ScreeningResult;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminScreeningResultsBySchoolTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_selects_a_school_before_seeing_its_screening_results(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $firstSchool = School::create(['name' => 'SMA Mentari Satu', 'code' => 'SMS-01']);
        $secondSchool = School::create(['name' => 'SMA Mentari Dua', 'code' => 'SMS-02']);
        $firstStudent = User::factory()->create([
            'school_id' => $firstSchool->id,
            'name' => 'Siswa Sekolah Pertama',
            'role' => 'student',
        ]);
        $secondStudent = User::factory()->create([
            'school_id' => $secondSchool->id,
            'name' => 'Siswa Sekolah Kedua',
            'role' => 'student',
        ]);

        $this->createScreeningResult($firstStudent);
        $this->createScreeningResult($secondStudent);

        $this->actingAs($admin)
            ->get('/admin/screening-results')
            ->assertOk()
            ->assertSee('Pilih sekolah terlebih dahulu')
            ->assertSee('SMA Mentari Satu')
            ->assertSee('SMA Mentari Dua');

        $this->actingAs($admin)
            ->get("/admin/screening-results/school/{$firstSchool->id}")
            ->assertOk()
            ->assertSee('Hasil Screening')
            ->assertSee('Siswa Sekolah Pertama')
            ->assertDontSee('Siswa Sekolah Kedua');
    }

    private function createScreeningResult(User $user): ScreeningResult
    {
        return ScreeningResult::create([
            'user_id' => $user->id,
            'taken_at' => now(),
            'depression_score' => 8,
            'depression_severity' => 'normal',
            'anxiety_score' => 8,
            'anxiety_severity' => 'mild',
            'stress_score' => 16,
            'stress_severity' => 'mild',
            'summary' => 'Hasil screening untuk pengujian.',
        ]);
    }
}
