<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StreakController extends Controller
{
    public function checkIn(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'message' => 'Aktivitas harian berhasil dicatat.',
            'data' => [
                'streak_days' => $user->streak_days,
                'last_activity_date' => $user->last_activity_date?->toDateString(),
                'checked_in_today' => true,
                'timezone' => config('app.timezone'),
            ],
        ]);
    }
}
