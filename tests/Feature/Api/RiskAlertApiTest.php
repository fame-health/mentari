<?php

namespace Tests\Feature\Api;

use App\Models\RiskAlert;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RiskAlertApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_cannot_change_risk_alert_read_status(): void
    {
        Sanctum::actingAs($student = User::factory()->create(['role' => 'student']));
        $alert = $this->createAlert($student);

        $this->patchJson("/api/v1/risk-alerts/{$alert->id}/dismiss")
            ->assertForbidden();

        $this->assertNull($alert->fresh()->dismissed_at);
    }

    public function test_admin_can_change_risk_alert_read_status(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $alert = $this->createAlert($student);
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));

        $this->patchJson("/api/v1/risk-alerts/{$alert->id}/dismiss")
            ->assertOk()
            ->assertJsonPath('message', 'Alert ditandai sudah dibaca.');

        $this->assertNotNull($alert->fresh()->dismissed_at);
    }

    private function createAlert(User $student): RiskAlert
    {
        return RiskAlert::create([
            'user_id' => $student->id,
            'level' => 'attention',
            'title' => 'Perlu perhatian',
            'message' => 'Pesan alert untuk pengujian.',
            'recommendation' => 'Hubungi pendamping.',
        ]);
    }
}
