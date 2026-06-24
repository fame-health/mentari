<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DailyStreakTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();

        parent::tearDown();
    }

    public function test_student_streak_is_idempotent_and_tracks_consecutive_days(): void
    {
        CarbonImmutable::setTestNow('2026-06-24 08:00:00 Asia/Jakarta');

        $student = User::factory()->create();
        Sanctum::actingAs($student);

        $this->postJson('/api/v1/streak/check-in')
            ->assertOk()
            ->assertJsonPath('data.streak_days', 1)
            ->assertJsonPath('data.last_activity_date', '2026-06-24')
            ->assertJsonPath('data.checked_in_today', true);

        $this->postJson('/api/v1/streak/check-in')
            ->assertOk()
            ->assertJsonPath('data.streak_days', 1);

        CarbonImmutable::setTestNow('2026-06-25 08:00:00 Asia/Jakarta');

        $this->postJson('/api/v1/streak/check-in')
            ->assertOk()
            ->assertJsonPath('data.streak_days', 2);

        CarbonImmutable::setTestNow('2026-06-27 08:00:00 Asia/Jakarta');

        $this->postJson('/api/v1/streak/check-in')
            ->assertOk()
            ->assertJsonPath('data.streak_days', 1)
            ->assertJsonPath('data.last_activity_date', '2026-06-27');
    }

    public function test_login_records_the_students_daily_activity(): void
    {
        CarbonImmutable::setTestNow('2026-06-24 08:00:00 Asia/Jakarta');

        $student = User::factory()->create([
            'email' => 'student@example.com',
            'password' => 'password',
        ]);

        $this->postJson('/api/v1/auth/login', [
            'email' => 'student@example.com',
            'password' => 'password',
            'device_name' => 'test',
        ])
            ->assertOk()
            ->assertJsonPath('data.user.streak_days', 1)
            ->assertJsonPath('data.user.last_activity_date', '2026-06-24');

        $this->assertDatabaseHas('users', [
            'id' => $student->id,
            'streak_days' => 1,
        ]);
        $this->assertSame('2026-06-24', $student->fresh()->last_activity_date->toDateString());
    }

    public function test_any_authenticated_app_request_records_activity(): void
    {
        CarbonImmutable::setTestNow('2026-06-24 08:00:00 Asia/Jakarta');

        $student = User::factory()->create();
        Sanctum::actingAs($student);

        $this->getJson('/api/v1/auth/me')
            ->assertOk()
            ->assertJsonPath('data.streak_days', 1);
    }
}
