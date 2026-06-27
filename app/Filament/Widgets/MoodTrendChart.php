<?php

namespace App\Filament\Widgets;

use App\Models\MoodEntry;
use Carbon\CarbonPeriod;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class MoodTrendChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Rata-rata Mood Harian';

    private ?Collection $trendRows = null;

    protected ?string $pollingInterval = '10s';

    protected ?string $maxHeight = '230px';

    protected int|string|array $columnSpan = [
        'default' => 1,
        'md' => 6,
        'xl' => 8,
    ];

    protected function getData(): array
    {
        $start = today()->subDays(13);
        $trendRows = $this->getTrendRows();

        $period = collect(CarbonPeriod::create($start, today()));

        return [
            'datasets' => [
                [
                    'label' => 'Rata-rata skor mood',
                    'data' => $period
                        ->map(fn ($date): ?float => $trendRows->has($date->toDateString())
                            ? round((float) $trendRows->get($date->toDateString())->average_score, 2)
                            : null)
                        ->all(),
                    'borderColor' => '#f97316',
                    'backgroundColor' => 'rgba(249, 115, 22, 0.15)',
                    'pointBackgroundColor' => '#fb923c',
                    'pointBorderColor' => '#fff7ed',
                    'pointRadius' => 3,
                    'pointHoverRadius' => 5,
                    'spanGaps' => true,
                    'fill' => true,
                    'tension' => 0.42,
                ],
            ],
            'labels' => $period->map(fn ($date): string => $date->format('d M'))->all(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    public function getDescription(): string
    {
        $trendRows = $this->getTrendRows();
        $totalCheckIns = (int) $trendRows->sum('total');

        if ($totalCheckIns === 0) {
            return 'Belum ada check-in mood dalam 14 hari terakhir. Grafik akan terisi setelah siswa mengirim mood harian.';
        }

        $todayKey = today()->toDateString();
        $yesterdayKey = today()->subDay()->toDateString();
        $todayRow = $trendRows->get($todayKey);
        $yesterdayRow = $trendRows->get($yesterdayKey);
        $latestRow = $trendRows->last();
        $latestDate = $latestRow?->aggregate_date
            ? Carbon::parse($latestRow->aggregate_date)->format('d M')
            : '-';
        $todayAverage = $todayRow ? number_format((float) $todayRow->average_score, 2) : '-';
        $comparison = $this->formatAverageComparison(
            $todayRow ? (float) $todayRow->average_score : null,
            $yesterdayRow ? (float) $yesterdayRow->average_score : null,
        );

        return "{$totalCheckIns} check-in dalam 14 hari. Hari ini: {$todayAverage}/5. Data terbaru masuk pada {$latestDate}. {$comparison}";
    }

    protected function getOptions(): array|RawJs|null
    {
        return [
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
                'tooltip' => [
                    'displayColors' => false,
                ],
            ],
            'scales' => [
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                ],
                'y' => [
                    'min' => 1,
                    'suggestedMax' => 5,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }

    private function getTrendRows(): Collection
    {
        return $this->trendRows ??= MoodEntry::query()
            ->join('mood_options', 'mood_entries.mood_option_id', '=', 'mood_options.id')
            ->whereDate('entry_date', '>=', today()->subDays(13))
            ->selectRaw('DATE(entry_date) as aggregate_date')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('AVG(mood_options.score) as average_score')
            ->groupBy('aggregate_date')
            ->orderBy('aggregate_date')
            ->get()
            ->keyBy(fn ($row): string => (string) $row->aggregate_date);
    }

    private function formatAverageComparison(?float $todayAverage, ?float $yesterdayAverage): string
    {
        if ($todayAverage === null) {
            return 'Belum ada data hari ini.';
        }

        if ($yesterdayAverage === null) {
            return 'Belum ada pembanding dari kemarin.';
        }

        $delta = round($todayAverage - $yesterdayAverage, 2);

        if ($delta === 0.0) {
            return 'Rata-rata stabil dari kemarin.';
        }

        return $delta > 0
            ? 'Rata-rata naik '.number_format($delta, 2).' poin dari kemarin.'
            : 'Rata-rata turun '.number_format(abs($delta), 2).' poin dari kemarin.';
    }
}
