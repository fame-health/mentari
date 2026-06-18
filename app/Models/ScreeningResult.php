<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ScreeningResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'taken_at',
        'depression_score',
        'depression_severity',
        'anxiety_score',
        'anxiety_severity',
        'stress_score',
        'stress_severity',
        'summary',
    ];

    protected function casts(): array
    {
        return [
            'taken_at' => 'datetime',
            'depression_score' => 'integer',
            'anxiety_score' => 'integer',
            'stress_score' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ScreeningAnswer::class);
    }

    public function riskAlert(): HasOne
    {
        return $this->hasOne(RiskAlert::class);
    }
}
