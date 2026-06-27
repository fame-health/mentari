<?php

namespace App\Filament\Widgets;

use App\Services\SchoolScreeningReportData;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

class SchoolScreeningTrendChart extends ChartWidget
{
    public ?int $schoolId = null;

    protected ?string $heading = 'Tren Rata-rata Skor DASS-21 (6 Bulan Terakhir)';

    protected ?string $description = 'Grafik ini menampilkan perubahan rata-rata skor Depresi, Kecemasan, dan Stres setiap bulan. Skor yang meningkat mengindikasikan kondisi yang perlu perhatian lebih.';

    protected ?string $maxHeight = '300px';

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'md' => 6,
        'xl' => 7,
    ];

    protected function getData(): array
    {
        $trend = app(SchoolScreeningReportData::class)->trend($this->schoolId);

        return [
            'datasets' => [
                [
                    'label' => 'Depresi',
                    'data' => $trend['depression'],
                    'borderColor' => '#f43f5e',
                    'backgroundColor' => '#f43f5e',
                    'tension' => 0.35,
                ],
                [
                    'label' => 'Kecemasan',
                    'data' => $trend['anxiety'],
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => '#f59e0b',
                    'tension' => 0.35,
                ],
                [
                    'label' => 'Stres',
                    'data' => $trend['stress'],
                    'borderColor' => '#0ea5e9',
                    'backgroundColor' => '#0ea5e9',
                    'tension' => 0.35,
                ],
            ],
            'labels' => $trend['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array|RawJs|null
    {
        return [
            'maintainAspectRatio' => false,
            'interaction' => [
                'mode' => 'index',
                'intersect' => false,
            ],
            'plugins' => [
                'legend' => ['position' => 'bottom'],
            ],
            'scales' => [
                'x' => ['grid' => ['display' => false]],
                'y' => [
                    'beginAtZero' => true,
                    'suggestedMax' => 42,
                ],
            ],
        ];
    }
}
