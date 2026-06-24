<?php

namespace Tests\Feature;

use App\Filament\Resources\EducationCategories\Pages\ListEducationCategories;
use App\Models\EducationCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminEducationCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_creates_category_from_modal_with_automatic_slug_and_order(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        EducationCategory::create([
            'slug' => 'kategori-lama',
            'title' => 'Kategori Lama',
            'sort_order' => 4,
            'is_active' => true,
        ]);

        $component = Livewire::actingAs($admin)
            ->test(ListEducationCategories::class);

        $this->assertNull($component->instance()->getAction('create')->getUrl());

        $component->callAction('create', data: [
            'title' => 'Menjaga Kesehatan Mental',
            'description' => 'Materi untuk menjaga kesehatan mental siswa.',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('education_categories', [
            'title' => 'Menjaga Kesehatan Mental',
            'slug' => 'menjaga-kesehatan-mental',
            'sort_order' => 5,
            'is_active' => true,
        ]);
    }

    public function test_automatic_slug_is_unique_for_duplicate_titles(): void
    {
        EducationCategory::create([
            'title' => 'Kesehatan Mental',
            'is_active' => true,
        ]);

        $duplicate = EducationCategory::create([
            'title' => 'Kesehatan Mental',
            'is_active' => true,
        ]);

        $this->assertSame('kesehatan-mental-2', $duplicate->slug);
        $this->assertSame(2, $duplicate->sort_order);
    }
}
