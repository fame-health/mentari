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
}
