<?php

namespace App\Services;

use App\Models\Classroom;
use App\Models\MoodEntry;
use App\Models\RiskAlert;
use App\Models\School;
use App\Models\ScreeningResult;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class AnalysisResultReportData
{
    private const DASS_MAX_TOTAL = 126;

    private const STREAK_TARGET_DAYS = 30;

    private const SEVERITY_ORDER = [
        'normal' => 1,
        'mild' => 2,
        'moderate' => 3,
        'severe' => 4,
        'extremely_severe' => 5,
    ];

    public function __construct(private readonly SchoolScreeningReportData $screeningReportData) {}

    public function report(School $school, ?int $classroomId = null): array
    {
        $classroom = $classroomId
            ? Classroom::query()
                ->where('school_id', $school->id)
                ->find($classroomId)
            : null;
        $scopeClassroomId = $classroom?->id;
        $summary = $this->summaryFor($school->id, $scopeClassroomId);
        $students = $this->studentCharts($school->id, $scopeClassroomId);
        $severityDistribution = $this->severityDistribution($school->id, $scopeClassroomId);

        return [
            'school' => $school,
            'classroom' => $classroom,
            'generated_at' => now(),
            'summary' => $summary,
            'combined_analysis' => $this->combinedAnalysis($summary),
            'school_overview' => $this->schoolOverview($school, $this->summaryFor($school->id, null)),
            'classrooms' => $this->classroomCharts($school->id, $scopeClassroomId),
            'students' => $students,
            'streak_students' => $students->sortByDesc('streak_days')->values(),
            'severity_distribution' => $severityDistribution,
        ];
    }

    public function summaryFor(int $schoolId, ?int $classroomId = null): array
    {
        $studentQuery = $this->studentQuery($schoolId, $classroomId);
        $studentCount = (clone $studentQuery)->count();
        $averageStreak = round((float) ((clone $studentQuery)->avg('streak_days') ?? 0), 1);
        $maxStreak = (int) ((clone $studentQuery)->max('streak_days') ?? 0);
        $activeLogins = (clone $studentQuery)
            ->whereDate('last_activity_date', '>=', today()->subDays(6))
            ->count();

        $moodQuery = $this->moodEntryQuery($schoolId, $classroomId);
        $moodEntries = (clone $moodQuery)->count();
        $moodStudents = (clone $moodQuery)->distinct()->count('user_id');
        $moodAverage = (clone $moodQuery)
            ->join('mood_options', 'mood_entries.mood_option_id', '=', 'mood_options.id')
            ->avg('mood_options.score');
        $energyAverage = (clone $moodQuery)->avg('energy');
        $stressAverage = (clone $moodQuery)->avg('stress');

        $screeningQuery = $this->screeningResultQuery($schoolId, $classroomId);
        $screeningCount = (clone $screeningQuery)->count();
        $screenedStudents = (clone $screeningQuery)->distinct()->count('user_id');
        $monthlyScreenings = (clone $screeningQuery)
            ->whereBetween('taken_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();
        $dassAverage = (clone $screeningQuery)
            ->selectRaw('AVG(depression_score + anxiety_score + stress_score) as aggregate_average')
            ->value('aggregate_average');

        $alertQuery = $this->riskAlertQuery($schoolId, $classroomId);
        $activeAlerts = (clone $alertQuery)->count();
        $urgentAlerts = (clone $alertQuery)->where('level', 'urgent')->count();

        return [
            'student_count' => $studentCount,
            'average_streak' => $averageStreak,
            'average_streak_percent' => $this->percent($averageStreak, self::STREAK_TARGET_DAYS),
            'max_streak' => $maxStreak,
            'active_logins' => $activeLogins,
            'active_login_percent' => $studentCount > 0 ? $this->percent($activeLogins, $studentCount) : 0,
            'mood_entries' => $moodEntries,
            'mood_students' => $moodStudents,
            'mood_average' => $moodAverage === null ? null : round((float) $moodAverage, 1),
            'mood_average_percent' => $moodAverage === null ? 0 : $this->percent((float) $moodAverage, 5),
            'energy_average' => $energyAverage === null ? null : round((float) $energyAverage, 1),
            'stress_average' => $stressAverage === null ? null : round((float) $stressAverage, 1),
            'screening_count' => $screeningCount,
            'screened_students' => $screenedStudents,
            'monthly_screenings' => $monthlyScreenings,
            'screening_coverage' => $studentCount > 0 ? $this->percent($screenedStudents, $studentCount) : 0,
            'dass_average' => $dassAverage === null ? null : round((float) $dassAverage, 1),
            'screening_wellness_percent' => $dassAverage === null ? 0 : 100 - $this->percent((float) $dassAverage, self::DASS_MAX_TOTAL),
            'active_alerts' => $activeAlerts,
            'urgent_alerts' => $urgentAlerts,
            'alert_free_percent' => $studentCount > 0 ? 100 - $this->percent($activeAlerts, $studentCount) : 0,
        ];
    }

    public function combinedAnalysis(array $summary): Collection
    {
        return collect([
            [
                'label' => 'Rata-rata mood',
                'value' => $summary['mood_average'] === null ? 'Belum ada' : number_format($summary['mood_average'], 1).'/5',
                'percent' => $summary['mood_average_percent'],
                'color' => '#10b981',
            ],
            [
                'label' => 'Stabilitas screening',
                'value' => $summary['dass_average'] === null ? 'Belum ada' : $summary['screening_wellness_percent'].'%',
                'percent' => $summary['screening_wellness_percent'],
                'color' => '#0ea5e9',
            ],
            [
                'label' => 'Streak login',
                'value' => number_format($summary['average_streak'], 1).' hari',
                'percent' => $summary['average_streak_percent'],
                'color' => '#f59e0b',
            ],
            [
                'label' => 'Cakupan tes screening',
                'value' => $summary['screening_coverage'].'%',
                'percent' => $summary['screening_coverage'],
                'color' => '#8b5cf6',
            ],
            [
                'label' => 'Bebas alert aktif',
                'value' => $summary['alert_free_percent'].'%',
                'percent' => $summary['alert_free_percent'],
                'color' => '#14b8a6',
            ],
        ]);
    }

    public function schoolOverview(School $school, array $summary): array
    {
        return [
            'name' => $school->name,
            'summary' => $summary,
            'bars' => [
                [
                    'label' => 'Siswa aktif login 7 hari',
                    'value' => $summary['active_logins'].'/'.$summary['student_count'],
                    'percent' => $summary['active_login_percent'],
                    'color' => '#f59e0b',
                ],
                [
                    'label' => 'Cakupan tes screening',
                    'value' => $summary['screening_coverage'].'%',
                    'percent' => $summary['screening_coverage'],
                    'color' => '#0ea5e9',
                ],
                [
                    'label' => 'Rata-rata mood',
                    'value' => $summary['mood_average'] === null ? 'Belum ada' : number_format($summary['mood_average'], 1).'/5',
                    'percent' => $summary['mood_average_percent'],
                    'color' => '#10b981',
                ],
                [
                    'label' => 'Bebas alert aktif',
                    'value' => $summary['alert_free_percent'].'%',
                    'percent' => $summary['alert_free_percent'],
                    'color' => '#14b8a6',
                ],
            ],
        ];
    }

    public function classroomCharts(int $schoolId, ?int $selectedClassroomId = null): Collection
    {
        return Classroom::query()
            ->where('school_id', $schoolId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(function (Classroom $classroom) use ($schoolId, $selectedClassroomId): array {
                $summary = $this->summaryFor($schoolId, $classroom->id);

                return [
                    'id' => $classroom->id,
                    'name' => 'Kelas '.$classroom->name,
                    'is_selected' => $selectedClassroomId === $classroom->id,
                    'summary' => $summary,
                    'bars' => [
                        [
                            'label' => 'Mood',
                            'value' => $summary['mood_average'] === null ? 'Belum ada' : number_format($summary['mood_average'], 1).'/5',
                            'percent' => $summary['mood_average_percent'],
                            'color' => '#10b981',
                        ],
                        [
                            'label' => 'Screening',
                            'value' => $summary['screening_coverage'].'%',
                            'percent' => $summary['screening_coverage'],
                            'color' => '#0ea5e9',
                        ],
                        [
                            'label' => 'Streak',
                            'value' => number_format($summary['average_streak'], 1).' hari',
                            'percent' => $summary['average_streak_percent'],
                            'color' => '#f59e0b',
                        ],
                    ],
                ];
            });
    }

    public function studentCharts(int $schoolId, ?int $classroomId = null): Collection
    {
        $students = $this->studentQuery($schoolId, $classroomId)
            ->with('classroom:id,name')
            ->orderBy('name')
            ->get(['id', 'classroom_id', 'name', 'email', 'streak_days', 'last_activity_date']);

        if ($students->isEmpty()) {
            return collect();
        }

        $studentIds = $students->pluck('id');
        $moodAverages = MoodEntry::query()
            ->join('mood_options', 'mood_entries.mood_option_id', '=', 'mood_options.id')
            ->whereIn('mood_entries.user_id', $studentIds)
            ->selectRaw('mood_entries.user_id, COUNT(*) as mood_count')
            ->selectRaw('AVG(mood_options.score) as mood_average')
            ->selectRaw('AVG(mood_entries.energy) as energy_average')
            ->selectRaw('AVG(mood_entries.stress) as stress_average')
            ->groupBy('mood_entries.user_id')
            ->get()
            ->keyBy('user_id');

        $latestMoods = MoodEntry::query()
            ->with('moodOption:id,label,score,color')
            ->whereIn('user_id', $studentIds)
            ->orderByDesc('entry_date')
            ->orderByDesc('id')
            ->get()
            ->unique('user_id')
            ->keyBy('user_id');

        $latestScreenings = ScreeningResult::query()
            ->whereIn('user_id', $studentIds)
            ->orderByDesc('taken_at')
            ->orderByDesc('id')
            ->get()
            ->unique('user_id')
            ->keyBy('user_id');

        $screeningCounts = ScreeningResult::query()
            ->whereIn('user_id', $studentIds)
            ->selectRaw('user_id, COUNT(*) as aggregate_count')
            ->groupBy('user_id')
            ->pluck('aggregate_count', 'user_id');

        $activeAlertCounts = RiskAlert::query()
            ->whereIn('user_id', $studentIds)
            ->whereNull('dismissed_at')
            ->selectRaw('user_id, COUNT(*) as aggregate_count')
            ->groupBy('user_id')
            ->pluck('aggregate_count', 'user_id');

        return $students->map(function (User $student) use ($moodAverages, $latestMoods, $latestScreenings, $screeningCounts, $activeAlertCounts): array {
            $moodAverage = $moodAverages->get($student->id);
            $latestMood = $latestMoods->get($student->id);
            $latestScreening = $latestScreenings->get($student->id);
            $screeningTotal = $latestScreening
                ? $latestScreening->depression_score + $latestScreening->anxiety_score + $latestScreening->stress_score
                : null;
            $worstSeverity = $latestScreening
                ? $this->worstSeverity([
                    $latestScreening->depression_severity,
                    $latestScreening->anxiety_severity,
                    $latestScreening->stress_severity,
                ])
                : null;

            return [
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
                'classroom' => $student->classroom?->name ? 'Kelas '.$student->classroom->name : 'Kelas belum diisi',
                'streak_days' => (int) $student->streak_days,
                'streak_percent' => $this->percent((float) $student->streak_days, self::STREAK_TARGET_DAYS),
                'last_activity' => $student->last_activity_date?->translatedFormat('d M Y') ?? 'Belum ada aktivitas',
                'mood_average' => $moodAverage?->mood_average === null ? null : round((float) $moodAverage->mood_average, 1),
                'mood_count' => (int) ($moodAverage?->mood_count ?? 0),
                'mood_percent' => $moodAverage?->mood_average === null ? 0 : $this->percent((float) $moodAverage->mood_average, 5),
                'latest_mood' => $latestMood?->moodOption?->label ?? 'Belum ada mood',
                'energy_average' => $moodAverage?->energy_average === null ? null : round((float) $moodAverage->energy_average, 1),
                'stress_average' => $moodAverage?->stress_average === null ? null : round((float) $moodAverage->stress_average, 1),
                'screening_count' => (int) ($screeningCounts[$student->id] ?? 0),
                'screening_total' => $screeningTotal,
                'screening_percent' => $screeningTotal === null ? 0 : $this->percent((float) $screeningTotal, self::DASS_MAX_TOTAL),
                'latest_screening_at' => $latestScreening?->taken_at?->translatedFormat('d M Y') ?? 'Belum ada screening',
                'severity_key' => $worstSeverity,
                'severity_label' => $worstSeverity ? SchoolScreeningReportData::severityLabel($worstSeverity) : 'Belum ada',
                'active_alerts' => (int) ($activeAlertCounts[$student->id] ?? 0),
            ];
        });
    }

    public function severityDistribution(int $schoolId, ?int $classroomId = null): Collection
    {
        $distribution = $this->screeningReportData->severityDistribution($schoolId, $classroomId);

        return collect($distribution['labels'])
            ->map(fn (string $label, int $index): array => [
                'label' => $label,
                'depression' => $distribution['depression'][$index] ?? 0,
                'anxiety' => $distribution['anxiety'][$index] ?? 0,
                'stress' => $distribution['stress'][$index] ?? 0,
            ]);
    }

    private function studentQuery(int $schoolId, ?int $classroomId = null): Builder
    {
        $query = User::query()
            ->where('school_id', $schoolId)
            ->where('role', 'student');

        if ($classroomId !== null) {
            $query->where('classroom_id', $classroomId);
        }

        return $query;
    }

    private function moodEntryQuery(int $schoolId, ?int $classroomId = null): Builder
    {
        return MoodEntry::query()
            ->whereHas('user', fn (Builder $query): Builder => $this->scopeStudentQuery($query, $schoolId, $classroomId));
    }

    private function screeningResultQuery(int $schoolId, ?int $classroomId = null): Builder
    {
        return ScreeningResult::query()
            ->whereHas('user', fn (Builder $query): Builder => $this->scopeStudentQuery($query, $schoolId, $classroomId));
    }

    private function riskAlertQuery(int $schoolId, ?int $classroomId = null): Builder
    {
        return RiskAlert::query()
            ->whereNull('dismissed_at')
            ->whereHas('user', fn (Builder $query): Builder => $this->scopeStudentQuery($query, $schoolId, $classroomId));
    }

    private function scopeStudentQuery(Builder $query, int $schoolId, ?int $classroomId = null): Builder
    {
        $query
            ->where('school_id', $schoolId)
            ->where('role', 'student');

        if ($classroomId !== null) {
            $query->where('classroom_id', $classroomId);
        }

        return $query;
    }

    private function worstSeverity(array $severities): ?string
    {
        return collect($severities)
            ->filter()
            ->sortByDesc(fn (string $severity): int => self::SEVERITY_ORDER[$severity] ?? 0)
            ->first();
    }

    private function percent(float|int $value, float|int $max): int
    {
        if ($max <= 0) {
            return 0;
        }

        return (int) max(0, min(100, round(($value / $max) * 100)));
    }
}
