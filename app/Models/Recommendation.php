<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recommendation extends Model
{
    use HasFactory;

    public const COUNSELING_SCRIPT_CATEGORY = 'counseling_script';

    public const DASHBOARD_ANALYSIS_CATEGORY = 'dashboard_analysis';

    public const CATEGORY_LABELS = [
        self::COUNSELING_SCRIPT_CATEGORY => 'Skrip konseling singkat',
        self::DASHBOARD_ANALYSIS_CATEGORY => 'Analisis dashboard',
        'relaxation' => 'Relaksasi',
        'reflection' => 'Refleksi',
        'activity' => 'Aktivitas',
    ];

    public const SEVERITY_LABELS = [
        'normal' => 'Normal',
        'mild' => 'Ringan',
        'moderate' => 'Sedang',
        'severe' => 'Berat',
        'extremely_severe' => 'Sangat Berat',
    ];

    protected $fillable = [
        'title',
        'category',
        'severity',
        'description',
        'main_points',
        'education_message',
        'duration_minutes',
        'duration_label',
        'priority',
        'accent_color',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'main_points' => 'array',
            'duration_minutes' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public static function counselingScriptForSeverity(?string $severity): ?self
    {
        if (! $severity) {
            return null;
        }

        return self::query()
            ->where('is_active', true)
            ->where('category', self::COUNSELING_SCRIPT_CATEGORY)
            ->where('severity', $severity)
            ->first();
    }

    public static function dashboardAnalysisForSeverity(?string $severity): ?self
    {
        if (! $severity) {
            return null;
        }

        return self::query()
            ->where('is_active', true)
            ->where('category', self::DASHBOARD_ANALYSIS_CATEGORY)
            ->where('severity', $severity)
            ->first();
    }

    public function toDashboardAnalysisContent(): array
    {
        return [
            'title' => $this->title,
            'main_points' => $this->main_points ?: $this->descriptionLines(),
            'education_message' => $this->education_message ?: $this->description,
        ];
    }

    private function descriptionLines(): array
    {
        return str($this->description)
            ->replace(["\r\n", "\r"], "\n")
            ->explode("\n")
            ->map(fn (string $line): string => trim($line, " \t\n\r\0\x0B-•"))
            ->filter()
            ->values()
            ->all();
    }
}
