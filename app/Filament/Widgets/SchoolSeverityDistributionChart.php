<?php

namespace App\Filament\Widgets;

use App\Models\ScreeningResult;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Builder;

class SchoolSeverityDistributionChart extends ChartWidget
{
    public ?int $schoolId = null;

    protected ?string $heading = 'Distribusi Tingkat Keparahan';

    protected ?string $description = 'Jumlah hasil pada setiap kategori DASS-21.';

    protected ?string $maxHeight = '340px';

    protected function getData(): array
    {
        $severities = ['normal', 'mild', 'moderate', 'severe', 'extremely_severe'];
        $depression = $this->countsFor('depression_severity');
        $anxiety = $this->countsFor('anxiety_severity');
        $stress = $this->countsFor('stress_severity');

        return [
            'datasets' => [
                [
                    'label' => 'Depresi',
                    'data' => collect($severities)->map(fn (string $severity): int => $depression[$severity] ?? 0)->all(),
                    'backgroundColor' => '#f43f5e',
                    'borderRadius' => 5,
                ],
                [
                    'label' => 'Kecemasan',
                    'data' => collect($severities)->map(fn (string $severity): int => $anxiety[$severity] ?? 0)->all(),
                    'backgroundColor' => '#f59e0b',
                    'borderRadius' => 5,
                ],
                [
                    'label' => 'Stres',
                    'data' => collect($severities)->map(fn (string $severity): int => $stress[$severity] ?? 0)->all(),
                    'backgroundColor' => '#0ea5e9',
                    'borderRadius' => 5,
                ],
            ],
            'labels' => ['Normal', 'Ringan', 'Sedang', 'Berat', 'Sangat Berat'],
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

    private function countsFor(string $column): array
    {
        return ScreeningResult::query()
            ->whereHas('user', fn (Builder $query): Builder => $query
                ->where('school_id', $this->schoolId)
                ->where('role', 'student'))
            ->select($column)
            ->selectRaw('COUNT(*) as aggregate_count')
            ->groupBy($column)
            ->pluck('aggregate_count', $column)
            ->map(fn ($count): int => (int) $count)
            ->all();
    }
}
