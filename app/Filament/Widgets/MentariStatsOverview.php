<?php

namespace App\Filament\Widgets;

use App\Models\MoodEntry;
use App\Models\RiskAlert;
use App\Models\School;
use App\Models\ScreeningResult;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class MentariStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        $studentCount = User::where('role', 'student')->count();
        $todayMoodCount = MoodEntry::whereDate('entry_date', today())->count();
        $yesterdayMoodCount = MoodEntry::whereDate('entry_date', today()->subDay())->count();
        $monthlyScreenings = ScreeningResult::whereBetween('taken_at', [now()->startOfMonth(), now()->endOfMonth()])->count();
        $activeAlerts = RiskAlert::whereNull('dismissed_at')->count();
        $urgentAlerts = RiskAlert::whereNull('dismissed_at')->where('level', 'urgent')->count();

        return [
            Stat::make('Siswa aktif', Number::format($studentCount))
                ->description(School::count().' sekolah terdaftar')
                ->icon('heroicon-o-users')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->chart($this->getDailyCounts(User::where('role', 'student'), 'created_at', 7))
                ->chartColor('primary')
                ->color('primary'),
            Stat::make('Mood hari ini', Number::format($todayMoodCount))
                ->description($this->formatDelta($todayMoodCount, $yesterdayMoodCount, 'dari kemarin'))
                ->icon('heroicon-o-face-smile')
                ->descriptionIcon($todayMoodCount >= $yesterdayMoodCount ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->descriptionColor($todayMoodCount >= $yesterdayMoodCount ? 'success' : 'warning')
                ->chart($this->getDailyCounts(MoodEntry::query(), 'entry_date', 7))
                ->chartColor('success')
                ->color('success'),
            Stat::make('Screening bulan ini', Number::format($monthlyScreenings))
                ->description('DASS-21 pada bulan berjalan')
                ->icon('heroicon-o-clipboard-document-check')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->chart($this->getDailyCounts(ScreeningResult::query(), 'taken_at', 7))
                ->chartColor('info')
                ->color('info'),
            Stat::make('Alert aktif', Number::format($activeAlerts))
                ->description($urgentAlerts.' urgent butuh perhatian')
                ->icon('heroicon-o-exclamation-triangle')
                ->descriptionIcon($urgentAlerts > 0 ? 'heroicon-m-bell-alert' : 'heroicon-m-check-circle')
                ->descriptionColor($urgentAlerts > 0 ? 'danger' : 'success')
                ->chart($this->getDailyCounts(RiskAlert::whereNull('dismissed_at'), 'created_at', 7))
                ->chartColor('danger')
                ->color('danger'),
        ];
    }

    private function formatDelta(int $current, int $previous, string $label): string
    {
        $delta = $current - $previous;

        if ($delta === 0) {
            return 'Stabil '.$label;
        }

        return ($delta > 0 ? '+' : '').$delta.' '.$label;
    }

    private function getDailyCounts($query, string $column, int $days): array
    {
        $start = today()->subDays($days - 1);
        $counts = (clone $query)
            ->whereDate($column, '>=', $start)
            ->selectRaw("DATE({$column}) as aggregate_date, COUNT(*) as aggregate_count")
            ->groupBy('aggregate_date')
            ->pluck('aggregate_count', 'aggregate_date');

        return collect(range(0, $days - 1))
            ->mapWithKeys(function (int $day) use ($counts, $start): array {
                $date = $start->copy()->addDays($day);

                return [$date->format('d M') => (int) ($counts[$date->toDateString()] ?? 0)];
            })
            ->all();
    }
}
