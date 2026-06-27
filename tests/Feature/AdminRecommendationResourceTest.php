<?php

namespace Tests\Feature;

use App\Models\Recommendation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRecommendationResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_the_recommendation_list_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get('/admin/recommendations')
            ->assertOk()
            ->assertSee('Rekomendasi')
            ->assertSee('Pilih Jenis Rekomendasi')
            ->assertSee('Daftar Rekomendasi')
            ->assertSee('Skrip konseling singkat')
            ->assertSee('Analisis dashboard');
    }

    public function test_admin_can_open_recommendation_data_after_choosing_a_type(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Recommendation::create([
            'title' => 'Skrip konseling terpilih',
            'category' => Recommendation::COUNSELING_SCRIPT_CATEGORY,
            'severity' => 'moderate',
            'description' => 'Isi skrip konseling.',
            'is_active' => true,
        ]);

        Recommendation::create([
            'title' => 'Analisis dashboard lain',
            'category' => Recommendation::DASHBOARD_ANALYSIS_CATEGORY,
            'severity' => 'moderate',
            'description' => 'Isi analisis dashboard.',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get('/admin/recommendations?category='.Recommendation::COUNSELING_SCRIPT_CATEGORY)
            ->assertOk()
            ->assertSee('Skrip konseling singkat')
            ->assertSee('Judul')
            ->assertSee('Aktif')
            ->assertSee('Skrip konseling terpilih')
            ->assertDontSee('Analisis dashboard lain');
    }

    public function test_admin_can_open_the_improved_recommendation_create_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get('/admin/recommendations/create')
            ->assertOk()
            ->assertSee('Buat Rekomendasi')
            ->assertSee('Rekomendasi ini untuk apa?')
            ->assertSee('Jenis rekomendasi')
            ->assertSee('Skrip konseling singkat')
            ->assertSee('Analisis dashboard');
    }

    public function test_admin_can_open_the_compact_recommendation_view_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $recommendation = Recommendation::create([
            'title' => 'Skrip konseling - Sedang',
            'category' => Recommendation::COUNSELING_SCRIPT_CATEGORY,
            'severity' => 'moderate',
            'description' => 'Hasil skrining menunjukkan gejala sedang dan perlu dukungan lanjutan.',
            'duration_label' => 'Skrip singkat',
            'priority' => 'personalized',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get('/admin/recommendations/'.$recommendation->id)
            ->assertOk()
            ->assertSee('Isi rekomendasi')
            ->assertSee('Detail singkat')
            ->assertSee('Skrip konseling singkat')
            ->assertSee('Sedang');
    }
}
