<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MoodOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'emoji',
        'label',
        'description',
        'color',
        'score',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function moodEntries(): HasMany
    {
        return $this->hasMany(MoodEntry::class);
    }
}
