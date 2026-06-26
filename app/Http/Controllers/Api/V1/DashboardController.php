<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Recommendation;
use App\Services\Dass21ScoringService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private readonly Dass21ScoringService $scoring) {}

    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();
        $latestScreening = $user->screeningResults()->with('recommendation')->latest('taken_at')->first();
        $highestSeverity = $latestScreening
            ? $this->scoring->highestSeverity([
                'depression' => $latestScreening->depression_severity,
                'anxiety' => $latestScreening->anxiety_severity,
                'stress' => $latestScreening->stress_severity,
            ])
            : null;

        return response()->json([
            'data' => [
                'user' => $user->load('school'),
                'latest_mood' => $user->moodEntries()->with('moodOption')->latest('entry_date')->first(),
                'latest_screening' => $latestScreening,
                'active_risk_alerts' => $user->riskAlerts()->whereNull('dismissed_at')->latest()->get(),
                'personalized_recommendation' => $latestScreening?->recommendation
                    ?? Recommendation::counselingScriptForSeverity($highestSeverity),
                'recommendations' => Recommendation::where('is_active', true)
                    ->where('category', '!=', Recommendation::COUNSELING_SCRIPT_CATEGORY)
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
