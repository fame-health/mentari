<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScreeningQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'scale',
        'text',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'number' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ScreeningAnswer::class);
    }
}
