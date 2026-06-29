<?php

namespace Tests\Feature;

use App\Filament\Resources\AnalysisResults\Pages\ViewAnalysisResults;
use App\Models\Classroom;
use App\Models\MoodEntry;
use App\Models\MoodOption;
use App\Models\RiskAlert;
use App\Models\School;
use App\Models\ScreeningResult;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use ZipArchive;

class AdminAnalysisResultResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_selects_school_and_class_before_analysis_graphs_are_shown(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $firstSchool = School::create(['name' => 'SMA Mentari Analisis', 'code' => 'SMA-A']);
        $secondSchool = School::create(['name' => 'SMA Mentari Lain', 'code' => 'SMA-L']);
        $classX = Classroom::create(['school_id' => $firstSchool->id, 'name' => 'X', 'sort_order' => 1]);
        $classXI = Classroom::create(['school_id' => $firstSchool->id, 'name' => 'XI', 'sort_order' => 2]);
        $otherClass = Classroom::create(['school_id' => $secondSchool->id, 'name' => 'X', 'sort_order' => 1]);
        $happyMood = MoodOption::create([
            'key' => 'happy',
            'emoji' => ':)',
            'label' => 'Senang',
            'color' => '#10b981',
            'score' => 5,
            'sort_order' => 1,
            'is_active' => true,
        ]);
        $neutralMood = MoodOption::create([
            'key' => 'neutral',
            'emoji' => ':|',
            'label' => 'Biasa',
            'color' => '#0ea5e9',
            'score' => 3,
            'sort_order' => 2,
            'is_active' => true,
        ]);

        $classXStudent = User::factory()->create([
            'school_id' => $firstSchool->id,
            'classroom_id' => $classX->id,
            'name' => 'Siswa Analisis X',
            'email' => 'analisis.x@mentari.test',
            'role' => 'student',
            'level' => 'X',
            'streak_days' => 12,
            'last_activity_date' => today(),
        ]);
        $classXIStudent = User::factory()->create([
            'school_id' => $firstSchool->id,
            'classroom_id' => $classXI->id,
            'name' => 'Siswa Analisis XI',
            'email' => 'analisis.xi@mentari.test',
            'role' => 'student',
            'level' => 'XI',
            'streak_days' => 4,
            'last_activity_date' => today()->subDays(2),
        ]);
        $otherSchoolStudent = User::factory()->create([
            'school_id' => $secondSchool->id,
            'classroom_id' => $otherClass->id,
            'name' => 'Siswa Sekolah Lain',
            'email' => 'lain@mentari.test',
            'role' => 'student',
            'level' => 'X',
            'streak_days' => 30,
            'last_activity_date' => today(),
        ]);

        MoodEntry::create([
            'user_id' => $classXStudent->id,
            'mood_option_id' => $happyMood->id,
            'entry_date' => today(),
            'energy' => 8,
            'stress' => 2,
        ]);
        MoodEntry::create([
            'user_id' => $classXIStudent->id,
            'mood_option_id' => $neutralMood->id,
            'entry_date' => today(),
            'energy' => 6,
            'stress' => 5,
        ]);

        $screeningResult = $this->createScreeningResult($classXStudent, 'moderate');
        $this->createScreeningResult($classXIStudent, 'mild');
        $this->createScreeningResult($otherSchoolStudent, 'severe');
        RiskAlert::create([
            'user_id' => $classXStudent->id,
            'screening_result_id' => $screeningResult->id,
            'level' => 'attention',
            'title' => 'Perlu pemantauan',
            'message' => 'Siswa perlu dipantau.',
            'recommendation' => 'Jadwalkan tindak lanjut.',
        ]);

        $this->actingAs($admin)
            ->get('/admin/analysis-results')
            ->assertOk()
            ->assertSee('Hasil Analisis Data')
            ->assertSee('1. Pilih Sekolah')
            ->assertSee($firstSchool->name)
            ->assertSee($secondSchool->name);

        $component = Livewire::actingAs($admin)
            ->test(ViewAnalysisResults::class)
            ->assertSee('1. Pilih Sekolah')
            ->assertSee($firstSchool->name)
            ->assertDontSee($classXStudent->name);

        $component
            ->call('selectSchool', $firstSchool->id)
            ->assertSet('selectedSchoolId', $firstSchool->id)
            ->assertSee('2. Pilih Kelas')
            ->assertSee('Semua kelas')
            ->assertSee('Kelas X')
            ->assertSee('Kelas XI')
            ->assertDontSee($classXStudent->name);

        $component
            ->call('selectClassroom', 'all')
            ->assertSet('selectedClassroomKey', 'all')
            ->assertSee('Grafik Full Streak Login')
            ->assertSee('Gabungan Analisis Mood dan Tes Screening')
            ->assertSee('Grafik Per Sekolah')
            ->assertSee('Grafik Per Kelas')
            ->assertSee('Grafik Per Siswa')
            ->assertSee('Export PDF')
            ->assertSee('Export Excel')
            ->assertSee($classXStudent->name)
            ->assertSee($classXIStudent->name)
            ->assertDontSee($otherSchoolStudent->name);

        $component
            ->call('backToClassrooms')
            ->call('selectClassroom', (string) $classX->id)
            ->assertSet('selectedClassroomKey', (string) $classX->id)
            ->assertSee($classXStudent->name)
            ->assertDontSee($classXIStudent->name)
            ->assertDontSee($otherSchoolStudent->name);
    }

    public function test_admin_can_export_analysis_results_to_pdf_and_excel_by_school_or_class(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $school = School::create(['name' => 'SMA Mentari Analisis Export', 'code' => 'SMA-EXP']);
        $classX = Classroom::create(['school_id' => $school->id, 'name' => 'X', 'sort_order' => 1]);
        $classXI = Classroom::create(['school_id' => $school->id, 'name' => 'XI', 'sort_order' => 2]);
        $mood = MoodOption::create([
            'key' => 'happy-export',
            'emoji' => ':)',
            'label' => 'Senang',
            'color' => '#10b981',
            'score' => 5,
            'sort_order' => 1,
            'is_active' => true,
        ]);
        $student = User::factory()->create([
            'school_id' => $school->id,
            'classroom_id' => $classX->id,
            'name' => 'Siswa Analisis Export',
            'email' => 'analisis.export@mentari.test',
            'role' => 'student',
            'streak_days' => 14,
            'last_activity_date' => today(),
        ]);
        $otherStudent = User::factory()->create([
            'school_id' => $school->id,
            'classroom_id' => $classXI->id,
            'name' => 'Siswa Analisis Kelas Lain',
            'email' => 'analisis.lain@mentari.test',
            'role' => 'student',
            'streak_days' => 3,
            'last_activity_date' => today()->subDays(3),
        ]);
        $screeningResult = $this->createScreeningResult($student, 'moderate');
        $this->createScreeningResult($otherStudent, 'mild');

        MoodEntry::create([
            'user_id' => $student->id,
            'mood_option_id' => $mood->id,
            'entry_date' => today(),
            'energy' => 8,
            'stress' => 2,
        ]);
        RiskAlert::create([
            'user_id' => $student->id,
            'screening_result_id' => $screeningResult->id,
            'level' => 'attention',
            'title' => 'Perlu pemantauan',
            'message' => 'Siswa perlu dipantau.',
            'recommendation' => 'Jadwalkan tindak lanjut.',
        ]);

        $schoolPdfResponse = $this->actingAs($admin)
            ->get(route('admin.analysis-results.school.export.pdf', ['school' => $school, 'class' => 'all']));

        $schoolPdfResponse
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-1.4', $schoolPdfResponse->getContent());
        $this->assertStringContainsString('Laporan Hasil Analisis Data', $schoolPdfResponse->getContent());
        $this->assertStringContainsString('Siswa Analisis Export', $schoolPdfResponse->getContent());
        $this->assertStringContainsString('Siswa Analisis Kelas Lain', $schoolPdfResponse->getContent());

        $excelResponse = $this->actingAs($admin)
            ->get(route('admin.analysis-results.school.export.excel', ['school' => $school, 'class' => 'all']));

        $excelResponse
            ->assertOk()
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $path = $excelResponse->baseResponse->getFile()->getPathname();
        $this->assertFileExists($path);

        $zip = new ZipArchive;
        $this->assertTrue($zip->open($path) === true);
        $workbookXml = $zip->getFromName('xl/workbook.xml');
        $studentSheetXml = $zip->getFromName('xl/worksheets/sheet2.xml');
        $this->assertIsString($workbookXml);
        $this->assertIsString($studentSheetXml);
        $this->assertStringContainsString('Ringkasan Analisis', $workbookXml);
        $this->assertStringContainsString('Data Siswa', $workbookXml);
        $this->assertStringContainsString('Data Kelas', $workbookXml);
        $this->assertStringContainsString('Siswa Analisis Export', $studentSheetXml);
        $this->assertStringContainsString((string) $student->id, $studentSheetXml);
        $zip->close();
        @unlink($path);

        $classPdfResponse = $this->actingAs($admin)
            ->get(route('admin.analysis-results.school.export.pdf', ['school' => $school, 'class' => $classX->id]));

        $classPdfResponse
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString('Siswa Analisis Export', $classPdfResponse->getContent());
        $this->assertStringNotContainsString('Siswa Analisis Kelas Lain', $classPdfResponse->getContent());
    }

    public function test_student_cannot_export_analysis_results(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $school = School::create(['name' => 'SMA Analisis Rahasia', 'code' => 'SAR-01']);

        $this->actingAs($student)
            ->get(route('admin.analysis-results.school.export.pdf', $school))
            ->assertForbidden();

        $this->actingAs($student)
            ->get(route('admin.analysis-results.school.export.excel', $school))
            ->assertForbidden();
    }

    private function createScreeningResult(User $user, string $severity): ScreeningResult
    {
        return ScreeningResult::create([
            'user_id' => $user->id,
            'taken_at' => now(),
            'depression_score' => 8,
            'depression_severity' => $severity,
            'anxiety_score' => 10,
            'anxiety_severity' => $severity,
            'stress_score' => 14,
            'stress_severity' => $severity,
            'summary' => 'Hasil screening untuk pengujian analisis.',
        ]);
    }
}
