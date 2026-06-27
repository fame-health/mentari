<?php

namespace App\Filament\Widgets;

use App\Services\SchoolScreeningReportData;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

class SchoolSeverityDistributionChart extends ChartWidget
{
    public ?int $schoolId = null;

    public ?int $classroomId = null;

    protected ?string $heading = 'Distribusi Tingkat Keparahan DASS-21';

    protected ?string $description = 'Jumlah hasil screening pada setiap kategori (Normal, Ringan, Sedang, Berat, Sangat Berat) untuk Depresi, Kecemasan, dan Stres.';

    protected ?string $maxHeight = '300px';

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'md' => 6,
        'xl' => 5,
    ];

    protected function getData(): array
    {
        $distribution = app(SchoolScreeningReportData::class)->severityDistribution($this->schoolId, $this->classroomId);

        return [
            'datasets' => [
                [
                    'label' => 'Depresi',
                    'data' => $distribution['depression'],
                    'backgroundColor' => '#f43f5e',
                    'borderRadius' => 5,
                ],
                [
                    'label' => 'Kecemasan',
                    'data' => $distribution['anxiety'],
                    'backgroundColor' => '#f59e0b',
                    'borderRadius' => 5,
                ],
                [
                    'label' => 'Stres',
                    'data' => $distribution['stress'],
                    'backgroundColor' => '#0ea5e9',
                    'borderRadius' => 5,
                ],
            ],
            'labels' => $distribution['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array|RawJs|null
    {
        return [
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => ['position' => 'bottom'],
            ],
            'scales' => [
                'x' => ['grid' => ['display' => false]],
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => ['precision' => 0],
                ],
            ],
        ];
    }
}
