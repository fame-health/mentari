<?php

namespace App\Filament\Widgets;

use App\Models\RiskAlert;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

class RiskLevelChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected ?string $heading = 'Komposisi Alert Aktif';

    protected ?string $description = 'Distribusi level risiko yang belum ditangani.';

    protected ?string $pollingInterval = '10s';

    protected ?string $maxHeight = '320px';

    protected int|string|array $columnSpan = [
        'default' => 1,
        'md' => 6,
        'xl' => 4,
    ];

    protected function getData(): array
    {
        $counts = RiskAlert::query()
            ->whereNull('dismissed_at')
            ->selectRaw('level, COUNT(*) as aggregate_count')
            ->groupBy('level')
            ->pluck('aggregate_count', 'level');

        return [
            'datasets' => [
                [
                    'data' => [
                        (int) ($counts['urgent'] ?? 0),
                        (int) ($counts['attention'] ?? 0),
                        (int) ($counts['stable'] ?? 0),
                    ],
                    'backgroundColor' => ['#ef4444', '#f59e0b', '#10b981'],
                    'borderColor' => '#ffffff',
                    'borderWidth' => 3,
                    'hoverOffset' => 8,
                ],
            ],
            'labels' => ['Urgent', 'Attention', 'Stable'],
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
