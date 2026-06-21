<?php

namespace App\Filament\Widgets;

use App\Models\RiskAlert;
use App\Models\ScreeningResult;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Number;

class SchoolScreeningStats extends StatsOverviewWidget
{
    public ?int $schoolId = null;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $studentCount = User::query()
            ->where('school_id', $this->schoolId)
            ->where('role', 'student')
            ->count();
        $screeningQuery = $this->screeningQuery();
        $screeningCount = (clone $screeningQuery)->count();
        $screenedStudents = (clone $screeningQuery)->distinct()->count('user_id');
        $monthlyScreenings = (clone $screeningQuery)
            ->whereBetween('taken_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();
        $activeAlerts = RiskAlert::query()
            ->whereHas('user', fn (Builder $query): Builder => $query
                ->where('school_id', $this->schoolId)
                ->where('role', 'student'))
            ->whereNull('dismissed_at')
            ->count();
        $urgentAlerts = RiskAlert::query()
            ->whereHas('user', fn (Builder $query): Builder => $query
                ->where('school_id', $this->schoolId)
                ->where('role', 'student'))
            ->whereNull('dismissed_at')
            ->where('level', 'urgent')
            ->count();
        $coverage = $studentCount > 0 ? round(($screenedStudents / $studentCount) * 100) : 0;

        return [
            Stat::make('Total siswa', Number::format($studentCount))
                ->description('Siswa terdaftar di sekolah')
                ->icon('heroicon-o-users')
                ->color('primary'),
            Stat::make('Total screening', Number::format($screeningCount))
                ->description(Number::format($monthlyScreenings).' dilakukan bulan ini')
                ->icon('heroicon-o-clipboard-document-check')
                ->color('info'),
            Stat::make('Cakupan screening', $coverage.'%')
                ->description(Number::format($screenedStudents).' dari '.Number::format($studentCount).' siswa pernah screening')
                ->icon('heroicon-o-chart-pie')
                ->color($coverage >= 75 ? 'success' : ($coverage >= 40 ? 'warning' : 'danger')),
            Stat::make('Alert aktif', Number::format($activeAlerts))
                ->description(Number::format($urgentAlerts).' alert urgent')
                ->icon('heroicon-o-exclamation-triangle')
                ->color($urgentAlerts > 0 ? 'danger' : ($activeAlerts > 0 ? 'warning' : 'success')),
        ];
    }

    private function screeningQuery(): Builder
    {
        return ScreeningResult::query()
            ->whereHas('user', fn (Builder $query): Builder => $query
                ->where('school_id', $this->schoolId)
                ->where('role', 'student'));
    }
}
