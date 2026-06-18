<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Recommendation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'data' => [
                'user' => $user->load('school'),
                'latest_mood' => $user->moodEntries()->with('moodOption')->latest('entry_date')->first(),
                'latest_screening' => $user->screeningResults()->latest('taken_at')->first(),
                'active_risk_alerts' => $user->riskAlerts()->whereNull('dismissed_at')->latest()->get(),
                'recommendations' => Recommendation::where('is_active', true)
                    ->orderByRaw("CASE WHEN priority = 'high' THEN 1 WHEN priority = 'medium' THEN 2 ELSE 3 END")
                    ->limit(5)
                    ->get(),
                'statistics' => [
                    'streak_days' => $user->streak_days,
                    'mood_entries' => $user->moodEntries()->count(),
                    'screenings' => $user->screeningResults()->count(),
                    'community_posts' => $user->communityPosts()->count(),
                ],
            ],
        ]);
    }
}
