<?php

namespace Tests\Feature\Api;

use App\Models\Recommendation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RecommendationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_recommendation_index_hides_internal_resources_unless_category_is_requested(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'student']));

        Recommendation::create([
            'title' => 'Latihan napas 4-4',
            'category' => 'relaxation',
            'description' => 'Tarik napas perlahan.',
            'is_active' => true,
        ]);
        Recommendation::create([
            'title' => 'Analisis Sedang',
            'category' => Recommendation::DASHBOARD_ANALYSIS_CATEGORY,
            'severity' => 'moderate',
            'description' => 'Analisis internal dashboard.',
            'main_points' => ['Poin internal dashboard.'],
            'education_message' => 'Pesan internal dashboard.',
            'is_active' => true,
        ]);

        $this->getJson('/api/v1/recommendations')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.category', 'relaxation');

        $this->getJson('/api/v1/recommendations?category='.Recommendation::DASHBOARD_ANALYSIS_CATEGORY.'&severity=moderate')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.category', Recommendation::DASHBOARD_ANALYSIS_CATEGORY);
    }
}
