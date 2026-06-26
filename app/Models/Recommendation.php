<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recommendation extends Model
{
    use HasFactory;

    public const COUNSELING_SCRIPT_CATEGORY = 'counseling_script';

    public const CATEGORY_LABELS = [
        self::COUNSELING_SCRIPT_CATEGORY => 'Skrip konseling singkat',
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
        'duration_minutes',
        'duration_label',
        'priority',
        'accent_color',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
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
}
