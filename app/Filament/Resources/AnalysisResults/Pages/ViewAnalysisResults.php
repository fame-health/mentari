<?php

namespace App\Filament\Resources\AnalysisResults\Pages;

use App\Filament\Resources\AnalysisResults\AnalysisResultResource;
use App\Models\Classroom;
use App\Models\MoodEntry;
use App\Models\RiskAlert;
use App\Models\School;
use App\Models\ScreeningResult;
use App\Models\User;
use App\Services\SchoolScreeningReportData;
use Filament\Resources\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Attributes\Url;

class ViewAnalysisResults extends Page
{
    protected static string $resource = AnalysisResultResource::class;

    protected string $view = 'filament.resources.analysis-results.pages.view-analysis-results';

    protected Width|string|null $maxContentWidth = Width::Full;

    private const ALL_CLASSROOMS = 'all';

    private const DASS_MAX_TOTAL = 126;

    private const STREAK_TARGET_DAYS = 30;

    private const STUDENT_TREND_LIMIT = 8;

    private const SPARKLINE_WIDTH = 220;

    private const SPARKLINE_HEIGHT = 58;

    private const SPARKLINE_PADDING = 6;

    private const SEVERITY_ORDER = [
        'normal' => 1,
        'mild' => 2,
        'moderate' => 3,
        'severe' => 4,
        'extremely_severe' => 5,
    ];

    #[Url(as: 'school')]
    public ?int $selectedSchoolId = null;

    #[Url(as: 'class')]
    public ?string $selectedClassroomKey = null;

    public function getTitle(): string|Htmlable
    {
        return 'Hasil Analisis Data';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Pilih sekolah, lalu pilih semua kelas atau kelas tertentu untuk membuka grafik sekolah, kelas, dan siswa.';
    }

    public function getPageClasses(): array
    {
        return [
            ...parent::getPageClasses(),
            'mentari-analysis-results',
        ];
    }

    public function getCurrentStep(): int
    {
        if (! $this->hasSelectedSchool()) {
            return 1;
        }

        return $this->hasSelectedClassroomOption() ? 3 : 2;
    }

    public function getSchools(): Collection
    {
        return School::query()
            ->withCount([
                'classrooms as classrooms_count' => fn (Builder $query): Builder => $query->where('is_active', true),
                'users as students_count' => fn (Builder $query): Builder => $query->where('role', 'student'),
                'screeningResults as screening_results_count' => fn (Builder $query): Builder => $query->where('users.role', 'student'),
            ])
            ->orderBy('name')
            ->get()
            ->map(fn (School $school): array => [
                'id' => $school->id,
                'name' => $school->name,
                'code' => $school->code ?: 'Tanpa kode',
                'classrooms_count' => $school->classrooms_count,
                'students_count' => $school->students_count,
                'screening_results_count' => $school->screening_results_count,
            ]);
    }

    public function getClassroomOptions(): Collection
    {
        if (! $this->hasSelectedSchool()) {
            return collect();
        }

        $studentCount = User::query()
            ->where('school_id', $this->selectedSchoolId)
            ->where('role', 'student')
            ->count();

        $screeningCounts = ScreeningResult::query()
            ->join('users', 'screening_results.user_id', '=', 'users.id')
            ->where('users.school_id', $this->selectedSchoolId)
            ->where('users.role', 'student')
            ->selectRaw('users.classroom_id, COUNT(*) as aggregate_count')
            ->groupBy('users.classroom_id')
            ->pluck('aggregate_count', 'users.classroom_id');

        $moodCounts = MoodEntry::query()
            ->join('users', 'mood_entries.user_id', '=', 'users.id')
            ->where('users.school_id', $this->selectedSchoolId)
            ->where('users.role', 'student')
            ->selectRaw('users.classroom_id, COUNT(*) as aggregate_count')
            ->groupBy('users.classroom_id')
            ->pluck('aggregate_count', 'users.classroom_id');

        $options = collect([[
            'value' => self::ALL_CLASSROOMS,
            'label' => 'Semua kelas',
            'description' => 'Grafik persekolah dengan gabungan seluruh kelas',
            'students_count' => $studentCount,
            'screening_results_count' => $screeningCounts->sum(),
            'mood_entries_count' => $moodCounts->sum(),
            'tone' => 'emerald',
            'icon' => 'heroicon-o-rectangle-stack',
        ]]);

        $classrooms = Classroom::query()
            ->where('school_id', $this->selectedSchoolId)
            ->where('is_active', true)
            ->withCount([
                'users as students_count' => fn (Builder $query): Builder => $query->where('role', 'student'),
            ])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(fn (Classroom $classroom, int $index): array => [
                'value' => (string) $classroom->id,
                'label' => 'Kelas '.$classroom->name,
                'description' => 'Grafik dan data khusus kelas '.$classroom->name,
                'students_count' => $classroom->students_count,
                'screening_results_count' => (int) ($screeningCounts[$classroom->id] ?? 0),
                'mood_entries_count' => (int) ($moodCounts[$classroom->id] ?? 0),
                'tone' => ['sky', 'indigo', 'amber', 'fuchsia'][$index % 4],
                'icon' => 'heroicon-o-academic-cap',
            ]);

        return $options->merge($classrooms)->values();
    }

    public function getSelectedSchool(): ?School
    {
        return $this->selectedSchoolId
            ? School::query()->find($this->selectedSchoolId)
            : null;
    }

    public function getSelectedSchoolLabel(): string
    {
        return $this->getSelectedSchool()?->name ?? 'Belum dipilih';
    }

    public function getSelectedClassroomLabel(): string
    {
        if ($this->selectedClassroomKey === self::ALL_CLASSROOMS) {
            return 'Semua kelas';
        }

        if (blank($this->selectedClassroomKey)) {
            return 'Belum dipilih';
        }

        $classroomName = Classroom::query()
            ->whereKey($this->selectedClassroomKey)
            ->where('school_id', $this->selectedSchoolId)
            ->value('name');

        return $classroomName ? 'Kelas '.$classroomName : 'Kelas tidak ditemukan';
    }

    public function getPdfExportUrl(): string
    {
        return route('admin.analysis-results.school.export.pdf', $this->exportRouteParameters());
    }

    public function getExcelExportUrl(): string
    {
        return route('admin.analysis-results.school.export.excel', $this->exportRouteParameters());
    }

    public function selectSchool(int $schoolId): void
    {
        abort_unless(School::query()->whereKey($schoolId)->exists(), 404);

        $this->selectedSchoolId = $schoolId;
        $this->selectedClassroomKey = null;
    }

    public function selectClassroom(string $classroomKey): void
    {
        abort_unless($this->getClassroomOptions()->pluck('value')->contains($classroomKey), 404);

        $this->selectedClassroomKey = $classroomKey;
    }

    public function backToSchools(): void
    {
        $this->selectedSchoolId = null;
        $this->selectedClassroomKey = null;
    }

    public function backToClassrooms(): void
    {
        $this->selectedClassroomKey = null;
    }

    public function getScopeSummary(): array
    {
        return $this->summaryFor($this->selectedClassroomId());
    }

    public function getCombinedAnalysis(): Collection
    {
        $summary = $this->getScopeSummary();

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

    public function getSchoolOverview(): array
    {
        $summary = $this->summaryFor(null);

        return [
            'name' => $this->getSelectedSchoolLabel(),
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

    public function getClassroomCharts(): Collection
    {
        if (! $this->hasSelectedSchool()) {
            return collect();
        }

        return Classroom::query()
            ->where('school_id', $this->selectedSchoolId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(function (Classroom $classroom): array {
                $summary = $this->summaryFor($classroom->id);

                return [
                    'id' => $classroom->id,
                    'name' => 'Kelas '.$classroom->name,
                    'is_selected' => $this->selectedClassroomKey === (string) $classroom->id,
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

    public function getStudentCharts(): Collection
    {
        if (! $this->hasSelectedClassroomOption()) {
            return collect();
        }

        $students = $this->studentQuery($this->selectedClassroomId())
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

        $moodTrends = MoodEntry::query()
            ->join('mood_options', 'mood_entries.mood_option_id', '=', 'mood_options.id')
            ->whereIn('mood_entries.user_id', $studentIds)
            ->orderBy('mood_entries.entry_date')
            ->orderBy('mood_entries.id')
            ->get([
                'mood_entries.user_id',
                'mood_entries.entry_date',
                'mood_options.score',
            ])
            ->groupBy('user_id')
            ->map(fn (Collection $entries): array => $this->trendData(
                $entries
                    ->slice(max(0, $entries->count() - self::STUDENT_TREND_LIMIT))
                    ->values()
                    ->map(fn (MoodEntry $entry): array => [
                        'label' => Carbon::parse($entry->entry_date)->translatedFormat('d M'),
                        'value' => (float) $entry->score,
                        'display' => number_format((float) $entry->score, 1).'/5',
                    ]),
                5
            ));

        $screeningTrends = ScreeningResult::query()
            ->whereIn('user_id', $studentIds)
            ->orderBy('taken_at')
            ->orderBy('id')
            ->get([
                'user_id',
                'taken_at',
                'depression_score',
                'anxiety_score',
                'stress_score',
            ])
            ->groupBy('user_id')
            ->map(fn (Collection $entries): array => $this->trendData(
                $entries
                    ->slice(max(0, $entries->count() - self::STUDENT_TREND_LIMIT))
                    ->values()
                    ->map(function (ScreeningResult $entry): array {
                        $total = $entry->depression_score + $entry->anxiety_score + $entry->stress_score;

                        return [
                            'label' => $entry->taken_at->translatedFormat('d M'),
                            'value' => (float) $total,
                            'display' => $total.' poin',
                        ];
                    }),
                self::DASS_MAX_TOTAL
            ));

        return $students->map(function (User $student) use ($moodAverages, $latestMoods, $latestScreenings, $screeningCounts, $activeAlertCounts, $moodTrends, $screeningTrends): array {
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
                'initials' => $this->initials($student->name),
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
                'mood_trend' => $moodTrends->get($student->id, $this->emptyTrend()),
                'screening_trend' => $screeningTrends->get($student->id, $this->emptyTrend()),
            ];
        });
    }

    public function getSeverityDistribution(): Collection
    {
        if (! $this->hasSelectedClassroomOption()) {
            return collect();
        }

        $distribution = app(SchoolScreeningReportData::class)
            ->severityDistribution($this->selectedSchoolId, $this->selectedClassroomId());

        return collect($distribution['labels'])
            ->map(fn (string $label, int $index): array => [
                'label' => $label,
                'depression' => $distribution['depression'][$index] ?? 0,
                'anxiety' => $distribution['anxiety'][$index] ?? 0,
                'stress' => $distribution['stress'][$index] ?? 0,
            ]);
    }

    private function summaryFor(?int $classroomId): array
    {
        if (! $this->hasSelectedSchool()) {
            return $this->emptySummary();
        }

        $studentQuery = $this->studentQuery($classroomId);
        $studentCount = (clone $studentQuery)->count();
        $averageStreak = round((float) ((clone $studentQuery)->avg('streak_days') ?? 0), 1);
        $maxStreak = (int) ((clone $studentQuery)->max('streak_days') ?? 0);
        $activeLogins = (clone $studentQuery)
            ->whereDate('last_activity_date', '>=', today()->subDays(6))
            ->count();

        $moodQuery = $this->moodEntryQuery($classroomId);
        $moodEntries = (clone $moodQuery)->count();
        $moodStudents = (clone $moodQuery)->distinct()->count('user_id');
        $moodAverage = (clone $moodQuery)
            ->join('mood_options', 'mood_entries.mood_option_id', '=', 'mood_options.id')
            ->avg('mood_options.score');
        $energyAverage = (clone $moodQuery)->avg('energy');
        $stressAverage = (clone $moodQuery)->avg('stress');

        $screeningQuery = $this->screeningResultQuery($classroomId);
        $screeningCount = (clone $screeningQuery)->count();
        $screenedStudents = (clone $screeningQuery)->distinct()->count('user_id');
        $monthlyScreenings = (clone $screeningQuery)
            ->whereBetween('taken_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();
        $dassAverage = (clone $screeningQuery)
            ->selectRaw('AVG(depression_score + anxiety_score + stress_score) as aggregate_average')
            ->value('aggregate_average');

        $alertQuery = $this->riskAlertQuery($classroomId);
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

    private function emptySummary(): array
    {
        return [
            'student_count' => 0,
            'average_streak' => 0.0,
            'average_streak_percent' => 0,
            'max_streak' => 0,
            'active_logins' => 0,
            'active_login_percent' => 0,
            'mood_entries' => 0,
            'mood_students' => 0,
            'mood_average' => null,
            'mood_average_percent' => 0,
            'energy_average' => null,
            'stress_average' => null,
            'screening_count' => 0,
            'screened_students' => 0,
            'monthly_screenings' => 0,
            'screening_coverage' => 0,
            'dass_average' => null,
            'screening_wellness_percent' => 0,
            'active_alerts' => 0,
            'urgent_alerts' => 0,
            'alert_free_percent' => 0,
        ];
    }

    private function studentQuery(?int $classroomId = null): Builder
    {
        $query = User::query()
            ->where('school_id', $this->selectedSchoolId)
            ->where('role', 'student');

        if ($classroomId !== null) {
            $query->where('classroom_id', $classroomId);
        }

        return $query;
    }

    private function moodEntryQuery(?int $classroomId = null): Builder
    {
        return MoodEntry::query()
            ->whereHas('user', fn (Builder $query): Builder => $this->scopeStudentQuery($query, $classroomId));
    }

    private function screeningResultQuery(?int $classroomId = null): Builder
    {
        return ScreeningResult::query()
            ->whereHas('user', fn (Builder $query): Builder => $this->scopeStudentQuery($query, $classroomId));
    }

    private function riskAlertQuery(?int $classroomId = null): Builder
    {
        return RiskAlert::query()
            ->whereNull('dismissed_at')
            ->whereHas('user', fn (Builder $query): Builder => $this->scopeStudentQuery($query, $classroomId));
    }

    private function scopeStudentQuery(Builder $query, ?int $classroomId = null): Builder
    {
        $query
            ->where('school_id', $this->selectedSchoolId)
            ->where('role', 'student');

        if ($classroomId !== null) {
            $query->where('classroom_id', $classroomId);
        }

        return $query;
    }

    private function selectedClassroomId(): ?int
    {
        if (blank($this->selectedClassroomKey) || $this->selectedClassroomKey === self::ALL_CLASSROOMS) {
            return null;
        }

        if (! ctype_digit((string) $this->selectedClassroomKey)) {
            return null;
        }

        $classroomId = (int) $this->selectedClassroomKey;

        return Classroom::query()
            ->where('school_id', $this->selectedSchoolId)
            ->whereKey($classroomId)
            ->exists()
            ? $classroomId
            : null;
    }

    private function exportRouteParameters(): array
    {
        return [
            'school' => $this->getSelectedSchool(),
            'class' => $this->selectedClassroomKey,
        ];
    }

    private function initials(string $name): string
    {
        $initials = collect(preg_split('/\s+/', trim($name)) ?: [])
            ->filter()
            ->take(2)
            ->map(fn (string $part): string => Str::of($part)->substr(0, 1)->upper()->value())
            ->implode('');

        return $initials ?: 'S';
    }

    private function hasSelectedSchool(): bool
    {
        return $this->getSelectedSchool() !== null;
    }

    private function hasSelectedClassroomOption(): bool
    {
        if (! $this->hasSelectedSchool() || blank($this->selectedClassroomKey)) {
            return false;
        }

        return $this->getClassroomOptions()
            ->pluck('value')
            ->contains($this->selectedClassroomKey);
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

    private function trendData(Collection $values, float|int $max): array
    {
        if ($values->isEmpty()) {
            return $this->emptyTrend();
        }

        $count = $values->count();
        $width = self::SPARKLINE_WIDTH;
        $height = self::SPARKLINE_HEIGHT;
        $padding = self::SPARKLINE_PADDING;
        $usableWidth = $width - ($padding * 2);
        $usableHeight = $height - ($padding * 2);

        $dots = $values
            ->values()
            ->map(function (array $point, int $index) use ($count, $width, $height, $padding, $usableWidth, $usableHeight, $max): array {
                $percent = $this->percent((float) $point['value'], $max);
                $x = $count === 1
                    ? $width / 2
                    : $padding + (($usableWidth / ($count - 1)) * $index);
                $y = $height - $padding - (($percent / 100) * $usableHeight);

                return [
                    'x' => round($x, 1),
                    'y' => round($y, 1),
                    'label' => $point['label'],
                    'value' => $point['display'],
                ];
            })
            ->all();

        return [
            'count' => $count,
            'points' => collect($dots)
                ->map(fn (array $dot): string => $dot['x'].','.$dot['y'])
                ->implode(' '),
            'dots' => $dots,
        ];
    }

    private function emptyTrend(): array
    {
        return [
            'count' => 0,
            'points' => '',
            'dots' => [],
        ];
    }
}
