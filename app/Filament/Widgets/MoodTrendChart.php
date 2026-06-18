<?php

namespace App\Filament\Widgets;

use App\Models\MoodEntry;
use Carbon\CarbonPeriod;
use Filament\Widgets\ChartWidget;

class MoodTrendChart extends ChartWidget
{
    protected ?string $heading = 'Tren Mood 14 Hari';

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $start = today()->subDays(13);
        $averages = MoodEntry::query()
            ->join('mood_options', 'mood_entries.mood_option_id', '=', 'mood_options.id')
            ->whereDate('entry_date', '>=', $start)
            ->selectRaw('entry_date, AVG(mood_options.score) as average_score')
            ->groupBy('entry_date')
            ->pluck('average_score', 'entry_date');

        $period = collect(CarbonPeriod::create($start, today()));

        return [
            'datasets' => [
                [
                    'label' => 'Rata-rata skor mood',
                    'data' => $period
                        ->map(fn ($date): ?float => isset($averages[$date->toDateString()])
                            ? round((float) $averages[$date->toDateString()], 2)
                            : null)
                        ->all(),
                    'borderColor' => '#f97316',
                    'backgroundColor' => 'rgba(249, 115, 22, 0.15)',
                    'fill' => true,
                    'tension' => 0.35,
                ],
            ],
            'labels' => $period->map(fn ($date): string => $date->format('d M'))->all(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
