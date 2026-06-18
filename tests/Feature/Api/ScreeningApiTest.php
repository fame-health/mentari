<?php

namespace Tests\Feature\Api;

use App\Models\ScreeningQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ScreeningApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_screening_is_scored_and_creates_an_urgent_alert_for_high_scores(): void
    {
        Sanctum::actingAs($user = User::factory()->create());
        $scales = [
            1 => 'stress', 2 => 'anxiety', 3 => 'depression', 4 => 'anxiety', 5 => 'depression',
            6 => 'stress', 7 => 'anxiety', 8 => 'stress', 9 => 'anxiety', 10 => 'depression',
            11 => 'stress', 12 => 'stress', 13 => 'depression', 14 => 'stress', 15 => 'anxiety',
            16 => 'depression', 17 => 'depression', 18 => 'stress', 19 => 'anxiety', 20 => 'anxiety',
            21 => 'depression',
        ];

        $questions = collect($scales)->map(
            fn (string $scale, int $number) => ScreeningQuestion::create([
                'number' => $number,
                'scale' => $scale,
                'text' => 'Pertanyaan '.$number,
                'sort_order' => $number,
                'is_active' => true,
            ]),
        );

        $response = $this->postJson('/api/v1/screening/results', [
            'answers' => $questions->map(fn (ScreeningQuestion $question): array => [
                'question_id' => $question->id,
                'score' => 3,
            ])->values()->all(),
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.depression_score', 42)
            ->assertJsonPath('data.anxiety_score', 42)
            ->assertJsonPath('data.stress_score', 42)
            ->assertJsonPath('data.risk_alert.level', 'urgent');

        $this->assertDatabaseHas('risk_alerts', [
            'user_id' => $user->id,
            'level' => 'urgent',
        ]);
    }
}
