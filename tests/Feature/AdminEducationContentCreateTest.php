<?php

namespace Tests\Feature;

use App\Filament\Resources\EducationContents\Pages\CreateEducationContent;
use App\Models\EducationCategory;
use App\Models\EducationContent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminEducationContentCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_page_uses_clear_editorial_sections_and_indonesian_labels(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)
            ->test(CreateEducationContent::class)
            ->assertOk()
            ->assertSee('Buat Konten Edukasi')
            ->assertSee('Informasi Dasar')
            ->assertSee('Tulis Konten')
            ->assertSee('Publikasi')
            ->assertSee('Pelengkap konten (opsional)')
            ->assertSee('Lanjut')
            ->assertSee('Kembali')
            ->assertSee('Simpan konten')
            ->assertDontSee('Education category id')
            ->assertDontSee('Read time label');
    }

    public function test_admin_can_create_education_content_from_the_improved_form(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = EducationCategory::create([
            'title' => 'Kesehatan Mental',
            'is_active' => true,
        ]);

        Livewire::actingAs($admin)
            ->test(CreateEducationContent::class)
            ->fillForm([
                'education_category_id' => $category->id,
                'title' => 'Cara Mengelola Stres',
                'type' => 'article',
                'summary' => 'Langkah sederhana untuk membantu mengelola stres sehari-hari.',
                'body' => '<p>Tarik napas dan ambil jeda sejenak.</p>',
                'read_time_minutes' => 5,
                'is_active' => true,
                'published_at' => now(),
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas(EducationContent::class, [
            'education_category_id' => $category->id,
            'title' => 'Cara Mengelola Stres',
            'type' => 'article',
            'read_time_minutes' => 5,
            'is_active' => true,
        ]);
    }
}
