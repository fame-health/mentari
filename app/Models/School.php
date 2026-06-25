<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class School extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'address',
    ];

    protected static function booted(): void
    {
        static::creating(function (School $school): void {
            if (blank($school->code)) {
                $school->code = self::uniqueCode($school->name);
            }
        });
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function classrooms(): HasMany
    {
        return $this->hasMany(Classroom::class)->orderBy('sort_order')->orderBy('name');
    }

    public function communityPosts(): HasMany
    {
        return $this->hasMany(CommunityPost::class);
    }

    public function screeningResults(): HasManyThrough
    {
        return $this->hasManyThrough(ScreeningResult::class, User::class);
    }

    public function riskAlerts(): HasManyThrough
    {
        return $this->hasManyThrough(RiskAlert::class, User::class);
    }

    private static function uniqueCode(string $name): string
    {
        $baseCode = Str::of($name)
            ->ascii()
            ->upper()
            ->replaceMatches('/[^A-Z0-9]+/', '-')
            ->trim('-')
            ->limit(42, '')
            ->value();

        $baseCode = $baseCode ?: 'SEKOLAH';
        $code = $baseCode;
        $suffix = 2;

        while (self::withTrashed()->where('code', $code)->exists()) {
            $code = Str::limit($baseCode, 42 - strlen((string) $suffix), '').'-'.$suffix;
            $suffix++;
        }

        return $code;
    }
}
