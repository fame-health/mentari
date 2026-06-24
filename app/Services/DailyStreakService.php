<?php

namespace App\Services;

use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class DailyStreakService
{
    public function recordActivity(User $user): User
    {
        if ($user->role !== 'student') {
            return $user;
        }

        $today = CarbonImmutable::today(config('app.timezone'));

        $updatedUser = DB::transaction(function () use ($user, $today): User {
            $lockedUser = User::query()
                ->lockForUpdate()
                ->findOrFail($user->getKey());

            $lastActivityDate = $lockedUser->last_activity_date;

            if ($lastActivityDate?->isSameDay($today)) {
                return $lockedUser;
            }

            $lockedUser->forceFill([
                'streak_days' => $lastActivityDate?->isSameDay($today->subDay())
                    ? $lockedUser->streak_days + 1
                    : 1,
                'last_activity_date' => $today,
            ])->save();

            return $lockedUser;
        });

        $user->setRawAttributes($updatedUser->getAttributes(), true);

        return $user;
    }
}
