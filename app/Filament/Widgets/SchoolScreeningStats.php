<?php

namespace App\Filament\Widgets;

use App\Services\SchoolScreeningReportData;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class SchoolScreeningStats extends StatsOverviewWidget
{
    public ?int $schoolId = null;

    public ?int $classroomId = null;

    protected int|string|array $columnSpan = [
        'default' => 'full',
    ];

    protected function getColumns(): int|array|null
    {
        return [
            'default' => 1,
            'sm' => 2,
            'xl' => 4,
        ];
    }

    protected function getStats(): array
    {
        $summary = app(SchoolScreeningReportData::class)->summary($this->schoolId, $this->classroomId);

        return [
            Stat::make('Total Siswa Terdaftar', Number::format($summary['student_count']))
                ->extraAttributes(['class' => 'mentari-school-stat mentari-school-stat--students'])
                ->description('Siswa aktif di sekolah ini')
                ->icon('heroicon-o-user-group')
                ->color('primary'),

            Stat::make('Total Screening Dilakukan', Number::format($summary['screening_count']))
                ->extraAttributes(['class' => 'mentari-school-stat mentari-school-stat--screenings'])
                ->description(Number::format($summary['monthly_screenings']).' screening dilakukan bulan ini')
                ->icon('heroicon-o-clipboard-document-check')
                ->color('info'),

            Stat::make('Cakupan Siswa Terscreening', $summary['coverage'].'%')
                ->extraAttributes(['class' => 'mentari-school-stat mentari-school-stat--coverage'])
                ->description(
                    Number::format($summary['screened_students']).' dari '.Number::format($summary['student_count']).' siswa pernah mengikuti screening'
                )
                ->icon('heroicon-o-chart-pie')
                ->color($summary['coverage'] >= 75 ? 'success' : ($summary['coverage'] >= 40 ? 'warning' : 'danger')),

            Stat::make('Alert Belum Ditangani', Number::format($summary['active_alerts']))
                ->extraAttributes(['class' => 'mentari-school-stat mentari-school-stat--alerts'])
                ->description(
                    $summary['urgent_alerts'] > 0
                        ? Number::format($summary['urgent_alerts']).' alert berstatus URGENT - perlu tindak lanjut segera'
                        : 'Tidak ada alert urgent saat ini'
                )
                ->icon('heroicon-o-exclamation-triangle')
                ->color($summary['urgent_alerts'] > 0 ? 'danger' : ($summary['active_alerts'] > 0 ? 'warning' : 'success')),
        ];
    }
}
