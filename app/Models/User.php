<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'school_id',
        'classroom_id',
        'name',
        'email',
        'password',
        'role',
        'level',
        'avatar_initial',
        'streak_days',
        'last_activity_date',
        'can_take_screening',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'streak_days' => 'integer',
            'last_activity_date' => 'date:Y-m-d',
            'can_take_screening' => 'boolean',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    protected static function booted(): void
    {
        static::saving(function (User $user): void {
            if ($user->isDirty('classroom_id')) {
                if (! $user->classroom_id) {
                    $user->level = null;

                    return;
                }

                $classroom = Classroom::query()->find($user->classroom_id);

                if ($classroom) {
                    $user->school_id = $classroom->school_id;
                    $user->level = $classroom->name;
                }

                return;
            }

            if ($user->isDirty('level')) {
                if (! $user->school_id || blank($user->level)) {
                    $user->classroom_id = null;

                    return;
                }

                $user->classroom_id = Classroom::query()
                    ->where('school_id', $user->school_id)
                    ->where('name', trim($user->level))
                    ->value('id');

                return;
            }

            if ($user->isDirty('school_id')) {
                $user->classroom_id = null;
                $user->level = null;

                return;
            }

            if ($user->classroom_id) {
                $classroom = Classroom::query()->find($user->classroom_id);

                if ($classroom) {
                    $user->school_id = $classroom->school_id;
                    $user->level = $classroom->name;
                }
            }
        });
    }

    public function moodEntries(): HasMany
    {
        return $this->hasMany(MoodEntry::class);
    }

    public function screeningResults(): HasMany
    {
        return $this->hasMany(ScreeningResult::class);
    }

    public function screeningAnswers(): HasManyThrough
    {
        return $this->hasManyThrough(ScreeningAnswer::class, ScreeningResult::class);
    }

    public function communityPosts(): HasMany
    {
        return $this->hasMany(CommunityPost::class);
    }

    public function communityPostLikes(): HasMany
    {
        return $this->hasMany(CommunityPostLike::class);
    }

    public function riskAlerts(): HasMany
    {
        return $this->hasMany(RiskAlert::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role === 'admin';
    }
}
