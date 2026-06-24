<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class EducationCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'title',
        'description',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (EducationCategory $category): void {
            if (blank($category->slug)) {
                $category->slug = self::uniqueSlug($category->title);
            }

            if ($category->sort_order === null) {
                $category->sort_order = ((int) self::query()->max('sort_order')) + 1;
            }
        });
    }

    public function contents(): HasMany
    {
        return $this->hasMany(EducationContent::class);
    }

    private static function uniqueSlug(string $title): string
    {
        $baseSlug = Str::limit(Str::slug($title) ?: 'kategori', 70, '');
        $slug = $baseSlug;
        $suffix = 2;

        while (self::query()->where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
