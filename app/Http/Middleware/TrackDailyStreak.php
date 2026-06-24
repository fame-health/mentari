<?php

namespace App\Http\Middleware;

use App\Services\DailyStreakService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackDailyStreak
{
    public function __construct(private readonly DailyStreakService $dailyStreakService) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($user = $request->user()) {
            $this->dailyStreakService->recordActivity($user);
        }

        return $next($request);
    }
}
