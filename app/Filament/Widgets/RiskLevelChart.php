<?php

namespace App\Filament\Widgets;

use App\Models\ScreeningResult;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Collection;

class RiskLevelChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected ?string $heading = 'Komposisi Risiko Screening';

    protected ?string $pollingInterval = '10s';

    protected ?string $maxHeight = '245px';

    private ?Collection $riskCounts = null;

    protected int|string|array $columnSpan = [
        'default' => 1,
        'md' => 6,
        'xl' => 4,
    ];

    protected function getData(): array
    {
        $counts = $this->getRiskCounts();

        return [
            'datasets' => [
                [
                    'data' => [
                        (int) ($counts['extremely_severe'] ?? 0),
                        (int) ($counts['severe'] ?? 0),
                        (int) ($counts['moderate'] ?? 0),
                        (int) ($counts['mild'] ?? 0),
                        (int) ($counts['normal'] ?? 0),
                    ],
                    'backgroundColor' => ['#e11d48', '#fb7185', '#fbbf24', '#38bdf8', '#34d399'],
                    'hoverBackgroundColor' => ['#be123c', '#f43f5e', '#f59e0b', '#0ea5e9', '#10b981'],
                    'borderColor' => '#ffffff',
                    'borderWidth' => 4,
                    'borderRadius' => 6,
                    'spacing' => 3,
                    'hoverOffset' => 10,
                ],
            ],
            'labels' => ['Sangat Berat', 'Berat', 'Sedang', 'Ringan', 'Normal'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array|RawJs|null
    {
        return RawJs::make(<<<'JS'
            {
                cutout: '68%',
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        top: 2,
                        right: 4,
                        bottom: 2,
                        left: 2,
                    },
                },
                plugins: {
                    legend: {
                        position: 'right',
                        align: 'center',
                        labels: {
                            boxWidth: 8,
                            boxHeight: 8,
                            padding: 9,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            pointStyleWidth: 8,
                            font: {
                                size: 11,
                                weight: 600,
                            },
                        },
                    },
                    tooltip: {
                        displayColors: true,
                        callbacks: {
                            label: (context) => {
                                const total = context.dataset.data.reduce((sum, value) => sum + value, 0)
                                const value = context.parsed
                                const percent = total > 0 ? Math.round((value / total) * 100) : 0

                                return `${context.label}: ${value} siswa (${percent}%)`
                            },
                        },
                    },
                },
            }
        JS);
    }

    public function getDescription(): string
    {
        $counts = $this->getRiskCounts();
        $total = (int) $counts->sum();

        if ($total === 0) {
            return 'Belum ada hasil screening terbaru. Grafik akan terisi setelah siswa menyelesaikan DASS-21.';
        }

        $extremelySevere = (int) ($counts['extremely_severe'] ?? 0);
        $severe = (int) ($counts['severe'] ?? 0);
        $moderate = (int) ($counts['moderate'] ?? 0);
        $mild = (int) ($counts['mild'] ?? 0);
        $normal = (int) ($counts['normal'] ?? 0);

        return "Terbaru: {$extremelySevere} sangat berat, {$severe} berat, {$moderate} sedang, {$mild} ringan, {$normal} normal. Prioritaskan sangat berat dan berat untuk follow-up segera.";
    }

    private function getRiskCounts(): Collection
    {
        if ($this->riskCounts !== null) {
            return $this->riskCounts;
        }

        $levelExpression = <<<'SQL'
CASE
    WHEN depression_severity = 'extremely_severe'
        OR anxiety_severity = 'extremely_severe'
        OR stress_severity = 'extremely_severe'
    THEN 'extremely_severe'
    WHEN depression_severity = 'severe'
        OR anxiety_severity = 'severe'
        OR stress_severity = 'severe'
    THEN 'severe'
    WHEN depression_severity = 'moderate'
        OR anxiety_severity = 'moderate'
        OR stress_severity = 'moderate'
    THEN 'moderate'
    WHEN depression_severity = 'mild'
        OR anxiety_severity = 'mild'
        OR stress_severity = 'mild'
    THEN 'mild'
    ELSE 'normal'
END
SQL;

        $latestScreeningIds = ScreeningResult::query()
            ->selectRaw('MAX(id)')
            ->groupBy('user_id');

        return $this->riskCounts = ScreeningResult::query()
            ->whereIn('id', $latestScreeningIds)
            ->selectRaw("{$levelExpression} as risk_level, COUNT(*) as aggregate_count")
            ->groupBy('risk_level')
            ->pluck('aggregate_count', 'risk_level');
    }
}
