<?php

namespace Tests\Feature;

use App\Filament\Resources\RiskAlerts\Pages\ListRiskAlerts;
use App\Models\RiskAlert;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminRiskAlertTest extends TestCase
{
    use RefreshDatabase;

    public function test_latest_alerts_are_listed_first_and_admin_can_change_read_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'student']);
        $olderAlert = $this->createAlert($student, 'Alert Lama');
        $olderAlert->created_at = now()->subDay();
        $olderAlert->save();
        $latestAlert = $this->createAlert($student, 'Alert Terbaru');

        Livewire::actingAs($admin)
            ->test(ListRiskAlerts::class)
            ->assertCanSeeTableRecords([$latestAlert, $olderAlert], inOrder: true)
            ->assertTableActionVisible('markAsRead', $latestAlert)
            ->assertTableActionHasLabel('markAsRead', 'Tandai dibaca')
            ->assertTableActionHasIcon('markAsRead', 'heroicon-o-check-circle')
            ->callTableAction('markAsRead', $latestAlert);

        $this->assertNotNull($latestAlert->fresh()->dismissed_at);

        Livewire::actingAs($admin)
            ->test(ListRiskAlerts::class)
            ->assertTableActionVisible('markAsUnread', $latestAlert->fresh())
            ->callTableAction('markAsUnread', $latestAlert->fresh());

        $this->assertNull($latestAlert->fresh()->dismissed_at);
    }

    private function createAlert(User $student, string $title): RiskAlert
    {
        return RiskAlert::create([
            'user_id' => $student->id,
            'level' => 'attention',
            'title' => $title,
            'message' => 'Pesan alert untuk pengujian.',
            'recommendation' => 'Hubungi pendamping.',
        ]);
    }
}
