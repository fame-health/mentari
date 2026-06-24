<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\ScreeningResult;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use ZipArchive;

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
            ->assertDontSee('Siswa Sekolah Kedua')
            ->assertSee('Export PDF')
            ->assertSee('Export Excel');
    }

    public function test_admin_can_export_school_screening_results_to_pdf_and_excel(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $school = School::create(['name' => 'SMA Mentari Export', 'code' => 'SME-01']);
        $student = User::factory()->create([
            'school_id' => $school->id,
            'name' => 'Siswa Data Export',
            'email' => 'siswa.export@mentari.test',
            'role' => 'student',
            'level' => 'XI',
        ]);
        $result = $this->createScreeningResult($student);

        $pdfResponse = $this->actingAs($admin)
            ->get(route('admin.screening-results.school.export.pdf', $school));

        $pdfResponse
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString('no-store', $pdfResponse->headers->get('cache-control'));
        $this->assertStringStartsWith('%PDF-1.4', $pdfResponse->getContent());
        $this->assertStringContainsString('Diagram Batang', $pdfResponse->getContent());
        $this->assertStringContainsString('Siswa Data Export', $pdfResponse->getContent());

        $excelResponse = $this->actingAs($admin)
            ->get(route('admin.screening-results.school.export.excel', $school));

        $excelResponse
            ->assertOk()
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $path = $excelResponse->baseResponse->getFile()->getPathname();
        $this->assertFileExists($path);

        $zip = new ZipArchive;
        $this->assertTrue($zip->open($path) === true);
        $workbookXml = $zip->getFromName('xl/workbook.xml');
        $dataSheetXml = $zip->getFromName('xl/worksheets/sheet2.xml');
        $this->assertIsString($workbookXml);
        $this->assertIsString($dataSheetXml);
        $this->assertStringContainsString('Ringkasan', $workbookXml);
        $this->assertStringContainsString('Data Screening', $workbookXml);
        $this->assertStringContainsString('Siswa Data Export', $dataSheetXml);
        $this->assertStringContainsString((string) $result->id, $dataSheetXml);
        $zip->close();
        @unlink($path);
    }

    public function test_student_cannot_export_school_screening_results(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $school = School::create(['name' => 'SMA Rahasia', 'code' => 'SMR-01']);

        $this->actingAs($student)
            ->get(route('admin.screening-results.school.export.pdf', $school))
            ->assertForbidden();

        $this->actingAs($student)
            ->get(route('admin.screening-results.school.export.excel', $school))
            ->assertForbidden();
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
