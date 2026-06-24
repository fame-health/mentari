<?php

namespace App\Filament\Widgets;

use App\Services\SchoolScreeningReportData;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class SchoolScreeningStats extends StatsOverviewWidget
{
    public ?int $schoolId = null;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $summary = app(SchoolScreeningReportData::class)->summary($this->schoolId);

        return [
            Stat::make('Total siswa', Number::format($summary['student_count']))
                ->description('Siswa terdaftar di sekolah')
                ->icon('heroicon-o-users')
                ->color('primary'),
            Stat::make('Total screening', Number::format($summary['screening_count']))
                ->description(Number::format($summary['monthly_screenings']).' dilakukan bulan ini')
                ->icon('heroicon-o-clipboard-document-check')
                ->color('info'),
            Stat::make('Cakupan screening', $summary['coverage'].'%')
                ->description(Number::format($summary['screened_students']).' dari '.Number::format($summary['student_count']).' siswa pernah screening')
                ->icon('heroicon-o-chart-pie')
                ->color($summary['coverage'] >= 75 ? 'success' : ($summary['coverage'] >= 40 ? 'warning' : 'danger')),
            Stat::make('Alert aktif', Number::format($summary['active_alerts']))
                ->description(Number::format($summary['urgent_alerts']).' alert urgent')
                ->icon('heroicon-o-exclamation-triangle')
                ->color($summary['urgent_alerts'] > 0 ? 'danger' : ($summary['active_alerts'] > 0 ? 'warning' : 'success')),
        ];
    }
}
