<?php

namespace Tests\Feature\Api;

use App\Models\EducationCategory;
use App\Models\EducationContent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EducationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_only_active_published_contents_from_active_categories(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $category = $this->createCategory(['title' => 'Visible Category']);
        $inactiveCategory = $this->createCategory([
            'slug' => 'hidden-category',
            'title' => 'Hidden Category',
            'is_active' => false,
        ]);

        $this->createContent($category, ['title' => 'Published Mind Article']);
        $this->createContent($category, ['title' => 'Draft Mind Article', 'published_at' => null]);
        $this->createContent($category, ['title' => 'Future Mind Article', 'published_at' => now()->addDay()]);
        $this->createContent($category, ['title' => 'Inactive Mind Article', 'is_active' => false]);
        $this->createContent($inactiveCategory, ['title' => 'Hidden Category Mind Article']);

        $response = $this->getJson('/api/v1/education');

        $response
            ->assertOk()
            ->assertJsonFragment(['title' => 'Visible Category'])
            ->assertJsonFragment(['title' => 'Published Mind Article'])
            ->assertJsonMissing(['title' => 'Hidden Category'])
            ->assertJsonMissing(['title' => 'Draft Mind Article'])
            ->assertJsonMissing(['title' => 'Future Mind Article'])
            ->assertJsonMissing(['title' => 'Inactive Mind Article'])
            ->assertJsonMissing(['title' => 'Hidden Category Mind Article']);
    }

    public function test_search_returns_only_visible_contents(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $category = $this->createCategory();
        $inactiveCategory = $this->createCategory([
            'slug' => 'hidden-search-category',
            'title' => 'Hidden Search Category',
            'is_active' => false,
        ]);

        $this->createContent($category, ['title' => 'Mindfulness Published']);
        $this->createContent($category, ['title' => 'Mindfulness Draft', 'published_at' => null]);
        $this->createContent($category, ['title' => 'Mindfulness Future', 'published_at' => now()->addDay()]);
        $this->createContent($category, ['title' => 'Mindfulness Inactive', 'is_active' => false]);
        $this->createContent($inactiveCategory, ['title' => 'Mindfulness Hidden Category']);

        $response = $this->getJson('/api/v1/education/search?q=Mindfulness');

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Mindfulness Published');
    }

    public function test_show_requires_content_to_be_visible(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $category = $this->createCategory();
        $inactiveCategory = $this->createCategory([
            'slug' => 'hidden-show-category',
            'title' => 'Hidden Show Category',
            'is_active' => false,
        ]);

        $visible = $this->createContent($category, ['title' => 'Visible Article']);
        $draft = $this->createContent($category, ['title' => 'Draft Article', 'published_at' => null]);
        $future = $this->createContent($category, ['title' => 'Future Article', 'published_at' => now()->addDay()]);
        $inactive = $this->createContent($category, ['title' => 'Inactive Article', 'is_active' => false]);
        $hiddenCategory = $this->createContent($inactiveCategory, ['title' => 'Hidden Category Article']);

        $this->getJson("/api/v1/education/{$visible->id}")
            ->assertOk()
            ->assertJsonPath('data.title', 'Visible Article');

        foreach ([$draft, $future, $inactive, $hiddenCategory] as $content) {
            $this->getJson("/api/v1/education/{$content->id}")
                ->assertNotFound();
        }
    }

    private function createCategory(array $attributes = []): EducationCategory
    {
        return EducationCategory::create([
            'slug' => $attributes['slug'] ?? 'kesehatan-mental',
            'title' => $attributes['title'] ?? 'Kesehatan Mental',
            'description' => $attributes['description'] ?? null,
            'sort_order' => $attributes['sort_order'] ?? 1,
            'is_active' => $attributes['is_active'] ?? true,
        ]);
    }

    private function createContent(EducationCategory $category, array $attributes = []): EducationContent
    {
        return EducationContent::create([
            'education_category_id' => $category->id,
            'title' => $attributes['title'] ?? 'Published Article',
            'type' => $attributes['type'] ?? 'article',
            'read_time_minutes' => $attributes['read_time_minutes'] ?? 5,
            'read_time_label' => $attributes['read_time_label'] ?? '5 menit baca',
            'summary' => $attributes['summary'] ?? 'Ringkasan konten edukasi.',
            'body' => $attributes['body'] ?? 'Isi konten edukasi.',
            'media_url' => $attributes['media_url'] ?? null,
            'accent_color' => $attributes['accent_color'] ?? '#F97316',
            'published_at' => array_key_exists('published_at', $attributes)
                ? $attributes['published_at']
                : now()->subHour(),
            'is_active' => $attributes['is_active'] ?? true,
        ]);
    }
}
