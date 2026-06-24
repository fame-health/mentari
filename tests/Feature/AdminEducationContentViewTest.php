<?php

namespace Tests\Feature;

use App\Filament\Resources\EducationContents\Pages\ViewEducationContent;
use App\Models\EducationCategory;
use App\Models\EducationContent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminEducationContentViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_content_detail_uses_compact_readable_sections(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = EducationCategory::create([
            'title' => 'Kesehatan Mental',
            'is_active' => true,
        ]);
        $content = EducationContent::create([
            'education_category_id' => $category->id,
            'title' => 'Mengelola Stres',
            'type' => 'article',
            'read_time_minutes' => 5,
            'summary' => 'Ringkasan yang mudah dipahami.',
            'body' => '<h2>Kenali stres</h2><p>Ambil jeda dan atur napas.</p>',
            'published_at' => now()->subMinute(),
            'is_active' => true,
        ]);

        Livewire::actingAs($admin)
            ->test(ViewEducationContent::class, ['record' => $content->getRouteKey()])
            ->assertOk()
            ->assertSee('Informasi konten')
            ->assertSee('Ringkasan')
            ->assertSee('Isi artikel')
            ->assertSee('Riwayat')
            ->assertSee('Ringkasan yang mudah dipahami.')
            ->assertSee('Kenali stres')
            ->assertSee('Tayang')
            ->assertSee('Artikel')
            ->assertSee('5 menit')
            ->assertDontSee('Education category id')
            ->assertDontSee('Lihat Mengelola Stres');
    }
}
