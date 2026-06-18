<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScreeningAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'screening_result_id',
        'screening_question_id',
        'score',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'integer',
        ];
    }

    public function result(): BelongsTo
    {
        return $this->belongsTo(ScreeningResult::class, 'screening_result_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(ScreeningQuestion::class, 'screening_question_id');
    }
}
