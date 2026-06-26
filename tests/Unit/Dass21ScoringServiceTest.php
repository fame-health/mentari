<?php

namespace Tests\Unit;

use App\Services\Dass21ScoringService;
use PHPUnit\Framework\TestCase;

class Dass21ScoringServiceTest extends TestCase
{
    public function test_it_maps_scores_to_dass_21_severities(): void
    {
        $service = new Dass21ScoringService;

        $this->assertSame([
            'depression' => 'moderate',
            'anxiety' => 'severe',
            'stress' => 'mild',
        ], $service->severities([
            'depression' => 14,
            'anxiety' => 15,
            'stress' => 18,
        ]));
    }

    public function test_summary_includes_the_dass_21_medical_disclaimer(): void
    {
        $service = new Dass21ScoringService;

        $summary = $service->summary([
            'depression' => 'normal',
            'anxiety' => 'mild',
            'stress' => 'moderate',
        ]);

        $this->assertStringContainsString(
            'DASS-21 adalah instrumen skrining mandiri, bukan alat diagnostik medis formal.',
            $summary,
        );
        $this->assertStringContainsString(
            'Hasil ini tidak menggantikan evaluasi dari tenaga kesehatan profesional.',
            $summary,
        );
    }

    public function test_it_uses_the_highest_severity_for_personalized_recommendations(): void
    {
        $service = new Dass21ScoringService;

        $this->assertSame('severe', $service->highestSeverity([
            'depression' => 'mild',
            'anxiety' => 'severe',
            'stress' => 'moderate',
        ]));
    }
}
