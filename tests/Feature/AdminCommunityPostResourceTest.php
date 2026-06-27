<?php

namespace Tests\Feature;

use App\Models\CommunityPost;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCommunityPostResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_community_posts_as_chat_list(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $school = School::create(['name' => 'SMA Mentari']);
        $student = User::factory()->create([
            'name' => 'Rani Putri',
            'school_id' => $school->id,
        ]);

        CommunityPost::create([
            'user_id' => $student->id,
            'school_id' => $school->id,
            'tag' => 'curhat',
            'content' => 'Hari ini aku merasa lebih tenang setelah menulis jurnal.',
            'is_pinned' => true,
            'likes_count' => 7,
        ]);

        $this->actingAs($admin)
            ->get('/admin/community-posts')
            ->assertOk()
            ->assertSee('Percakapan Komunitas')
            ->assertSee('Rani Putri')
            ->assertSee('Hari ini aku merasa lebih tenang setelah menulis jurnal.')
            ->assertSee('7 suka')
            ->assertSee('Disematkan');
    }

    public function test_community_post_likes_resource_is_not_registered_separately(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get('/admin/community-post-likes')
            ->assertNotFound();
    }

    public function test_community_post_create_view_and_edit_pages_are_modal_only(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create();
        $post = CommunityPost::create([
            'user_id' => $student->id,
            'content' => 'Postingan ini dikelola dari modal.',
            'is_pinned' => false,
            'likes_count' => 0,
        ]);

        $this->actingAs($admin)->get('/admin/community-posts/create')->assertNotFound();
        $this->actingAs($admin)->get('/admin/community-posts/'.$post->id)->assertNotFound();
        $this->actingAs($admin)->get('/admin/community-posts/'.$post->id.'/edit')->assertNotFound();
    }
}
