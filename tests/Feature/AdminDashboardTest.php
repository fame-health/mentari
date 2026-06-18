<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
            ->assertSee('MENTARI Admin');
    }

    public function test_student_cannot_open_filament_dashboard(): void
    {
        $student = User::factory()->create(['role' => 'student']);

        $this->actingAs($student)
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_admin_can_open_all_resource_indexes(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $urls = [
            '/admin/schools',
            '/admin/users',
            '/admin/mood-options',
            '/admin/mood-entries',
            '/admin/education-categories',
            '/admin/education-contents',
            '/admin/recommendations',
            '/admin/screening-questions',
            '/admin/screening-results',
            '/admin/screening-answers',
            '/admin/community-posts',
            '/admin/community-post-likes',
            '/admin/risk-alerts',
        ];

        foreach ($urls as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }
    }
}
