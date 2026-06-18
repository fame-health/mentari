<?php

namespace App\Filament\Widgets;

use App\Models\MoodEntry;
use App\Models\RiskAlert;
use App\Models\ScreeningResult;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MentariStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Siswa aktif', User::where('role', 'student')->count())
                ->description('Total akun siswa')
                ->icon('heroicon-o-users')
                ->color('primary'),
            Stat::make('Mood hari ini', MoodEntry::whereDate('entry_date', today())->count())
                ->description('Check-in yang masuk hari ini')
                ->icon('heroicon-o-face-smile')
                ->color('success'),
            Stat::make('Screening bulan ini', ScreeningResult::whereBetween('taken_at', [now()->startOfMonth(), now()->endOfMonth()])->count())
                ->description('DASS-21 pada bulan berjalan')
                ->icon('heroicon-o-clipboard-document-check')
                ->color('info'),
            Stat::make('Alert belum ditangani', RiskAlert::whereNull('dismissed_at')->count())
                ->description(RiskAlert::whereNull('dismissed_at')->where('level', 'urgent')->count().' berstatus urgent')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('danger'),
        ];
    }
}
