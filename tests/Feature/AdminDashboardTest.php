<?php

namespace Tests\Feature;

use App\Filament\Widgets\MoodTrendChart;
use App\Filament\Widgets\RiskLevelChart;
use App\Models\MoodEntry;
use App\Models\MoodOption;
use App\Models\ScreeningResult;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ReflectionMethod;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_filament_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Dashboard MENTARI')
            ->assertSeeInOrder([
                'Data &amp; Pengaturan',
                'Sekolah',
                'Pengguna',
                'Pilihan Mood',
                'Pertanyaan Screening',
                'Monitoring Siswa',
                'Konten &amp; Dukungan',
                'Komunitas',
            ], escape: false);
    }

    public function test_student_cannot_open_filament_dashboard(): void
    {
        $student = User::factory()->create(['role' => 'student']);

        $this->actingAs($student)
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_risk_level_chart_counts_latest_screening_statuses(): void
    {
        $normalStudent = User::factory()->create(['role' => 'student']);
        $attentionStudent = User::factory()->create(['role' => 'student']);
        $urgentStudent = User::factory()->create(['role' => 'student']);
        $retestedStudent = User::factory()->create(['role' => 'student']);

        $this->createScreeningResult($normalStudent, [
            'depression_severity' => 'normal',
            'anxiety_severity' => 'normal',
            'stress_severity' => 'normal',
        ]);
        $this->createScreeningResult($attentionStudent, [
            'depression_severity' => 'normal',
            'anxiety_severity' => 'moderate',
            'stress_severity' => 'normal',
        ]);
        $this->createScreeningResult($urgentStudent, [
            'depression_severity' => 'severe',
            'anxiety_severity' => 'normal',
            'stress_severity' => 'normal',
        ]);
        $this->createScreeningResult($retestedStudent, [
            'depression_severity' => 'extremely_severe',
            'anxiety_severity' => 'normal',
            'stress_severity' => 'normal',
            'taken_at' => now()->subDay(),
        ]);
        $this->createScreeningResult($retestedStudent, [
            'depression_severity' => 'normal',
            'anxiety_severity' => 'normal',
            'stress_severity' => 'normal',
        ]);

        $method = new ReflectionMethod(RiskLevelChart::class, 'getData');
        $data = $method->invoke(new RiskLevelChart);

        $this->assertSame(['Urgent', 'Attention', 'Normal'], $data['labels']);
        $this->assertSame([1, 1, 2], $data['datasets'][0]['data']);
    }

    public function test_mood_trend_chart_explains_empty_and_average_data(): void
    {
        $emptyWidget = new MoodTrendChart;

        $this->assertStringContainsString('Belum ada check-in mood', $emptyWidget->getDescription());

        $student = User::factory()->create(['role' => 'student']);
        $classmate = User::factory()->create(['role' => 'student']);
        $sad = MoodOption::create([
            'key' => 'sad',
            'emoji' => ':(',
            'label' => 'Sedih',
            'color' => '#94a3b8',
            'score' => 2,
            'sort_order' => 1,
            'is_active' => true,
        ]);
        $happy = MoodOption::create([
            'key' => 'happy',
            'emoji' => ':)',
            'label' => 'Baik',
            'color' => '#22c55e',
            'score' => 4,
            'sort_order' => 2,
            'is_active' => true,
        ]);

        MoodEntry::create([
            'user_id' => $student->id,
            'mood_option_id' => $sad->id,
            'entry_date' => today(),
            'energy' => 5,
            'stress' => 5,
        ]);
        MoodEntry::create([
            'user_id' => $classmate->id,
            'mood_option_id' => $happy->id,
            'entry_date' => today(),
            'energy' => 8,
            'stress' => 3,
        ]);

        $widget = new MoodTrendChart;
        $method = new ReflectionMethod(MoodTrendChart::class, 'getData');
        $data = $method->invoke($widget);

        $this->assertSame(14, count($data['labels']));
        $this->assertSame(3.0, $data['datasets'][0]['data'][13]);
        $this->assertStringContainsString('2 check-in dalam 14 hari', $widget->getDescription());
        $this->assertStringContainsString('Hari ini: 3.00/5', $widget->getDescription());
    }

    public function test_admin_can_open_all_resource_indexes(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $urls = [
            '/admin/schools',
            '/admin/users',
            '/admin/analysis-results',
            '/admin/mood-options',
            '/admin/mood-entries',
            '/admin/education-categories',
            '/admin/education-contents',
            '/admin/recommendations',
            '/admin/screening-questions',
            '/admin/screening-results',
            '/admin/community-posts',
            '/admin/risk-alerts',
        ];

        foreach ($urls as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }
    }

    private function createScreeningResult(User $user, array $overrides = []): ScreeningResult
    {
        return ScreeningResult::create([
            'user_id' => $user->id,
            'taken_at' => $overrides['taken_at'] ?? now(),
            'depression_score' => 0,
            'depression_severity' => $overrides['depression_severity'] ?? 'normal',
            'anxiety_score' => 0,
            'anxiety_severity' => $overrides['anxiety_severity'] ?? 'normal',
            'stress_score' => 0,
            'stress_severity' => $overrides['stress_severity'] ?? 'normal',
            'summary' => 'Hasil screening untuk dashboard.',
        ]);
    }
}
