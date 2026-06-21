<?php

namespace App\Filament\Widgets;

use App\Models\ScreeningResult;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class SchoolScreeningTrendChart extends ChartWidget
{
    public ?int $schoolId = null;

    protected ?string $heading = 'Tren Skor Rata-rata 6 Bulan';

    protected ?string $description = 'Perubahan rata-rata skor depresi, kecemasan, dan stres setiap bulan.';

    protected ?string $maxHeight = '340px';

    protected function getData(): array
    {
        $start = now()->startOfMonth()->subMonths(5);
        $monthExpression = DB::connection()->getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', taken_at)"
            : "DATE_FORMAT(taken_at, '%Y-%m')";

        $averages = ScreeningResult::query()
            ->whereHas('user', fn (Builder $query): Builder => $query
                ->where('school_id', $this->schoolId)
                ->where('role', 'student'))
            ->where('taken_at', '>=', $start)
            ->selectRaw("{$monthExpression} as month_key")
            ->selectRaw('AVG(depression_score) as depression_average')
            ->selectRaw('AVG(anxiety_score) as anxiety_average')
            ->selectRaw('AVG(stress_score) as stress_average')
            ->groupByRaw($monthExpression)
            ->get()
            ->keyBy('month_key');

        $months = collect(range(0, 5))->map(fn (int $offset) => $start->copy()->addMonths($offset));

        return [
            'datasets' => [
                [
                    'label' => 'Depresi',
                    'data' => $months->map(fn ($month): ?float => $this->averageFor($averages, $month->format('Y-m'), 'depression_average'))->all(),
                    'borderColor' => '#f43f5e',
                    'backgroundColor' => '#f43f5e',
                    'tension' => 0.35,
                ],
                [
                    'label' => 'Kecemasan',
                    'data' => $months->map(fn ($month): ?float => $this->averageFor($averages, $month->format('Y-m'), 'anxiety_average'))->all(),
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => '#f59e0b',
                    'tension' => 0.35,
                ],
                [
                    'label' => 'Stres',
                    'data' => $months->map(fn ($month): ?float => $this->averageFor($averages, $month->format('Y-m'), 'stress_average'))->all(),
                    'borderColor' => '#0ea5e9',
                    'backgroundColor' => '#0ea5e9',
                    'tension' => 0.35,
                ],
            ],
            'labels' => $months->map(fn ($month): string => $month->translatedFormat('M Y'))->all(),
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

    private function averageFor($averages, string $monthKey, string $column): ?float
    {
        $value = $averages->get($monthKey)?->{$column};

        return $value === null ? null : round((float) $value, 1);
    }
}
