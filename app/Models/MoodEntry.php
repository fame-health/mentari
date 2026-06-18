<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MoodEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'mood_option_id',
        'entry_date',
        'note',
        'energy',
        'stress',
    ];

    protected function casts(): array
    {
        return [
            'entry_date' => 'date',
            'energy' => 'integer',
            'stress' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function moodOption(): BelongsTo
    {
        return $this->belongsTo(MoodOption::class);
    }
}
