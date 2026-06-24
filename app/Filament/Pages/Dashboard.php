<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\MentariStatsOverview;
use App\Filament\Widgets\MoodTrendChart;
use App\Filament\Widgets\RecentRiskAlerts;
use App\Filament\Widgets\RiskLevelChart;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Dashboard MENTARI';

    protected Width|string|null $maxContentWidth = Width::Full;

    public function getHeading(): string|Htmlable|null
    {
        return null;
    }

    public function getSubheading(): string|Htmlable|null
    {
        return null;
    }

    public function getHeader(): ?View
    {
        return view('filament.pages.dashboard-header');
    }

    public function getColumns(): int|array
    {
        return [
            'default' => 1,
            'md' => 6,
            'xl' => 12,
        ];
    }

    public function getPageClasses(): array
    {
        return ['mentari-dashboard'];
    }

    public function getWidgets(): array
    {
        return [
            MentariStatsOverview::class,
            MoodTrendChart::class,
            RiskLevelChart::class,
            RecentRiskAlerts::class,
        ];
    }
}
