<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EducationContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'education_category_id',
        'title',
        'type',
        'read_time_minutes',
        'read_time_label',
        'summary',
        'body',
        'media_url',
        'accent_color',
        'published_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'read_time_minutes' => 'integer',
            'published_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(EducationCategory::class, 'education_category_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->published()
            ->whereHas('category', fn (Builder $category) => $category->where('is_active', true));
    }
}
