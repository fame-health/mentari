<?php

namespace App\Filament\Widgets;

use App\Models\ScreeningResult;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

class RiskLevelChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected ?string $heading = 'Komposisi Alert Aktif';

    protected ?string $description = 'Distribusi status screening terbaru siswa.';

    protected ?string $pollingInterval = '10s';

    protected ?string $maxHeight = '320px';

    protected int|string|array $columnSpan = [
        'default' => 1,
        'md' => 6,
        'xl' => 4,
    ];

    protected function getData(): array
    {
        $levelExpression = <<<'SQL'
CASE
    WHEN depression_severity IN ('severe', 'extremely_severe')
        OR anxiety_severity IN ('severe', 'extremely_severe')
        OR stress_severity IN ('severe', 'extremely_severe')
    THEN 'urgent'
    WHEN depression_severity = 'moderate'
        OR anxiety_severity = 'moderate'
        OR stress_severity = 'moderate'
    THEN 'attention'
    ELSE 'normal'
END
SQL;

        $latestScreeningIds = ScreeningResult::query()
            ->selectRaw('MAX(id)')
            ->groupBy('user_id');

        $counts = ScreeningResult::query()
            ->whereIn('id', $latestScreeningIds)
            ->selectRaw("{$levelExpression} as alert_level, COUNT(*) as aggregate_count")
            ->groupBy('alert_level')
            ->pluck('aggregate_count', 'alert_level');

        return [
            'datasets' => [
                [
                    'data' => [
                        (int) ($counts['urgent'] ?? 0),
                        (int) ($counts['attention'] ?? 0),
                        (int) ($counts['normal'] ?? 0),
                    ],
                    'backgroundColor' => ['#ef4444', '#f59e0b', '#10b981'],
                    'borderColor' => '#ffffff',
                    'borderWidth' => 3,
                    'hoverOffset' => 8,
                ],
            ],
            'labels' => ['Urgent', 'Attention', 'Normal'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array|RawJs|null
    {
        return [
            'cutout' => '68%',
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'boxWidth' => 12,
                        'usePointStyle' => true,
                    ],
                ],
            ],
        ];
    }
}
