<?php

namespace App\Services;

class Dass21ScoringService
{
    private const SEVERITY_RANK = [
        'normal' => 0,
        'mild' => 1,
        'moderate' => 2,
        'severe' => 3,
        'extremely_severe' => 4,
    ];

    /**
     * @param  array{depression: int, anxiety: int, stress: int}  $scores
     * @return array{depression: string, anxiety: string, stress: string}
     */
    public function severities(array $scores): array
    {
        return [
            'depression' => $this->depressionSeverity($scores['depression']),
            'anxiety' => $this->anxietySeverity($scores['anxiety']),
            'stress' => $this->stressSeverity($scores['stress']),
        ];
    }

    public function highestSeverity(array $severities): string
    {
        return collect($severities)
            ->sortByDesc(fn (string $severity): int => self::SEVERITY_RANK[$severity])
            ->first();
    }

    public function riskLevel(array $severities): ?string
    {
        $rank = self::SEVERITY_RANK[$this->highestSeverity($severities)];

        return match (true) {
            $rank >= 3 => 'urgent',
            $rank === 2 => 'attention',
            default => null,
        };
    }

    public function summary(array $severities): string
    {
        $labels = [
            'normal' => 'normal',
            'mild' => 'ringan',
            'moderate' => 'sedang',
            'severe' => 'berat',
            'extremely_severe' => 'sangat berat',
        ];

        return sprintf(
            'Depresi: %s, kecemasan: %s, dan stres: %s. Catatan: DASS-21 adalah instrumen skrining mandiri, bukan alat diagnostik medis formal. Hasil ini tidak menggantikan evaluasi dari tenaga kesehatan profesional.',
            $labels[$severities['depression']],
            $labels[$severities['anxiety']],
            $labels[$severities['stress']],
        );
    }

    private function depressionSeverity(int $score): string
    {
        return match (true) {
            $score <= 9 => 'normal',
            $score <= 13 => 'mild',
            $score <= 20 => 'moderate',
            $score <= 27 => 'severe',
            default => 'extremely_severe',
        };
    }

    private function anxietySeverity(int $score): string
    {
        return match (true) {
            $score <= 7 => 'normal',
            $score <= 9 => 'mild',
            $score <= 14 => 'moderate',
            $score <= 19 => 'severe',
            default => 'extremely_severe',
        };
    }

    private function stressSeverity(int $score): string
    {
        return match (true) {
            $score <= 14 => 'normal',
            $score <= 18 => 'mild',
            $score <= 25 => 'moderate',
            $score <= 33 => 'severe',
            default => 'extremely_severe',
        };
    }
}
