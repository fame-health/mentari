<?php

namespace Tests\Feature\Api;

use App\Models\MoodOption;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MoodApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_create_and_update_one_mood_entry_per_day(): void
    {
        Sanctum::actingAs($user = User::factory()->create());
        $mood = MoodOption::create([
            'key' => 'good',
            'emoji' => '🙂',
            'label' => 'Baik',
            'color' => '#22C55E',
            'score' => 4,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $payload = [
            'mood_option_id' => $mood->id,
            'entry_date' => today()->toDateString(),
            'note' => 'Hari yang cukup baik.',
            'energy' => 8,
            'stress' => 3,
        ];

        $this->postJson('/api/v1/mood-entries', $payload)->assertCreated();
        $this->postJson('/api/v1/mood-entries', [...$payload, 'energy' => 7])->assertOk();

        $this->assertDatabaseCount('mood_entries', 1);
        $this->assertDatabaseHas('mood_entries', [
            'user_id' => $user->id,
            'energy' => 7,
        ]);
    }
}
