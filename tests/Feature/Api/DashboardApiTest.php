<?php

namespace Tests\Feature\Api;

use App\Models\Recommendation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DashboardApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_returns_personalized_recommendation_from_latest_screening(): void
    {
        Sanctum::actingAs($user = User::factory()->create(['role' => 'student']));
        $script = Recommendation::create([
            'title' => 'Skrip konseling - Sedang',
            'category' => Recommendation::COUNSELING_SCRIPT_CATEGORY,
            'severity' => 'moderate',
            'description' => 'Hasil skrining menunjukkan gejala sedang.',
            'priority' => 'personalized',
            'is_active' => true,
        ]);
        Recommendation::create([
            'title' => 'Latihan napas 4-4',
            'category' => 'relaxation',
            'description' => 'Tarik napas selama 4 detik dan hembuskan selama 4 detik.',
            'priority' => 'high',
            'is_active' => true,
        ]);
        Recommendation::create([
            'title' => 'Analisis Sedang dari Admin',
            'category' => Recommendation::DASHBOARD_ANALYSIS_CATEGORY,
            'severity' => 'moderate',
            'description' => 'Analisis sedang yang dikelola admin.',
            'main_points' => [
                'Poin analisis dashboard dari admin.',
            ],
            'education_message' => 'Pesan edukasi dashboard dari admin.',
            'priority' => 'personalized',
            'is_active' => true,
        ]);

        $user->screeningResults()->create([
            'taken_at' => now(),
            'depression_score' => 14,
            'depression_severity' => 'moderate',
            'anxiety_score' => 6,
            'anxiety_severity' => 'normal',
            'stress_score' => 10,
            'stress_severity' => 'normal',
            'summary' => 'Hasil screening untuk dashboard.',
            'recommendation_id' => $script->id,
        ]);

        $this->getJson('/api/v1/dashboard')
            ->assertOk()
            ->assertJsonPath('data.latest_screening.recommendation.severity', 'moderate')
            ->assertJsonPath('data.latest_screening.analysis.title', 'Analisis Sedang dari Admin')
            ->assertJsonPath('data.latest_screening.analysis.main_points.0', 'Poin analisis dashboard dari admin.')
            ->assertJsonPath('data.screening_analysis.education_message', 'Pesan edukasi dashboard dari admin.')
            ->assertJsonPath('data.personalized_recommendation.severity', 'moderate')
            ->assertJsonPath('data.recommendations.0.category', 'relaxation');
    }
}
