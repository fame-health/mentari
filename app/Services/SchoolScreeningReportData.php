<?php

namespace App\Services;

use App\Models\Classroom;
use App\Models\RiskAlert;
use App\Models\School;
use App\Models\ScreeningResult;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SchoolScreeningReportData
{
    public const SEVERITIES = [
        'normal' => 'Normal',
        'mild' => 'Ringan',
        'moderate' => 'Sedang',
        'severe' => 'Berat',
        'extremely_severe' => 'Sangat Berat',
    ];

    public function report(School $school, ?int $classroomId = null): array
    {
        $classroom = $classroomId
            ? Classroom::query()
                ->where('school_id', $school->id)
                ->find($classroomId)
            : null;

        return [
            'school' => $school,
            'classroom' => $classroom,
            'generated_at' => now(),
            'summary' => $this->summary($school->id, $classroom?->id),
            'distribution' => $this->severityDistribution($school->id, $classroom?->id),
            'trend' => $this->trend($school->id, $classroom?->id),
            'results' => $this->results($school->id, $classroom?->id),
        ];
    }

    public function summary(?int $schoolId, ?int $classroomId = null): array
    {
        $studentCount = User::query()
            ->where('school_id', $schoolId)
            ->when($classroomId, fn (Builder $query): Builder => $query->where('classroom_id', $classroomId))
            ->where('role', 'student')
            ->count();
        $screeningQuery = $this->screeningQuery($schoolId, $classroomId);
        $screeningCount = (clone $screeningQuery)->count();
        $screenedStudents = (clone $screeningQuery)->distinct()->count('user_id');
        $monthlyScreenings = (clone $screeningQuery)
            ->whereBetween('taken_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();
        $alertQuery = RiskAlert::query()
            ->whereHas('user', fn (Builder $query): Builder => $query
                ->where('school_id', $schoolId)
                ->when($classroomId, fn (Builder $query): Builder => $query->where('classroom_id', $classroomId))
                ->where('role', 'student'))
            ->whereNull('dismissed_at');
        $activeAlerts = (clone $alertQuery)->count();
        $urgentAlerts = (clone $alertQuery)->where('level', 'urgent')->count();

        return [
            'student_count' => $studentCount,
            'screening_count' => $screeningCount,
            'screened_students' => $screenedStudents,
            'monthly_screenings' => $monthlyScreenings,
            'coverage' => $studentCount > 0 ? round(($screenedStudents / $studentCount) * 100) : 0,
            'active_alerts' => $activeAlerts,
            'urgent_alerts' => $urgentAlerts,
        ];
    }

    public function severityDistribution(?int $schoolId, ?int $classroomId = null): array
    {
        $columns = [
            'depression' => 'depression_severity',
            'anxiety' => 'anxiety_severity',
            'stress' => 'stress_severity',
        ];
        $counts = [];

        foreach ($columns as $key => $column) {
            $counts[$key] = $this->screeningQuery($schoolId, $classroomId)
                ->select($column)
                ->selectRaw('COUNT(*) as aggregate_count')
                ->groupBy($column)
                ->pluck('aggregate_count', $column)
                ->map(fn ($count): int => (int) $count)
                ->all();
        }

        return [
            'keys' => array_keys(self::SEVERITIES),
            'labels' => array_values(self::SEVERITIES),
            'depression' => $this->orderedCounts($counts['depression']),
            'anxiety' => $this->orderedCounts($counts['anxiety']),
            'stress' => $this->orderedCounts($counts['stress']),
        ];
    }

    public function trend(?int $schoolId, ?int $classroomId = null): array
    {
        $start = now()->startOfMonth()->subMonths(5);
        $monthExpression = DB::connection()->getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', taken_at)"
            : "DATE_FORMAT(taken_at, '%Y-%m')";

        $averages = $this->screeningQuery($schoolId, $classroomId)
            ->where('taken_at', '>=', $start)
            ->selectRaw("{$monthExpression} as month_key")
            ->selectRaw('AVG(depression_score) as depression_average')
            ->selectRaw('AVG(anxiety_score) as anxiety_average')
            ->selectRaw('AVG(stress_score) as stress_average')
            ->groupByRaw($monthExpression)
            ->get()
            ->keyBy('month_key');
        $months = collect(range(0, 5))->map(fn (int $offset) => $start->copy()->addMonths($offset));

        return [
            'labels' => $months->map(fn ($month): string => $month->translatedFormat('M Y'))->all(),
            'depression' => $months->map(fn ($month): ?float => $this->averageFor($averages, $month->format('Y-m'), 'depression_average'))->all(),
            'anxiety' => $months->map(fn ($month): ?float => $this->averageFor($averages, $month->format('Y-m'), 'anxiety_average'))->all(),
            'stress' => $months->map(fn ($month): ?float => $this->averageFor($averages, $month->format('Y-m'), 'stress_average'))->all(),
        ];
    }

    public function results(?int $schoolId, ?int $classroomId = null): Collection
    {
        return $this->screeningQuery($schoolId, $classroomId)
            ->with('user:id,name,email,level,school_id,classroom_id')
            ->latest('taken_at')
            ->get();
    }

    public static function severityLabel(?string $severity): string
    {
        return self::SEVERITIES[$severity] ?? ($severity ?: '-');
    }

    private function screeningQuery(?int $schoolId, ?int $classroomId = null): Builder
    {
        return ScreeningResult::query()
            ->whereHas('user', fn (Builder $query): Builder => $query
                ->where('school_id', $schoolId)
                ->when($classroomId, fn (Builder $query): Builder => $query->where('classroom_id', $classroomId))
                ->where('role', 'student'));
    }

    private function orderedCounts(array $counts): array
    {
        return collect(array_keys(self::SEVERITIES))
            ->map(fn (string $severity): int => $counts[$severity] ?? 0)
            ->all();
    }

    private function averageFor(Collection $averages, string $monthKey, string $column): ?float
    {
        $value = $averages->get($monthKey)?->{$column};

        return $value === null ? null : round((float) $value, 1);
    }
}
