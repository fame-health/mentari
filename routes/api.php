<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CommunityPostController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\EducationController;
use App\Http\Controllers\Api\V1\MoodController;
use App\Http\Controllers\Api\V1\RecommendationController;
use App\Http\Controllers\Api\V1\RiskAlertController;
use App\Http\Controllers\Api\V1\ScreeningController;
use App\Http\Controllers\Api\V1\SchoolController;
use App\Http\Controllers\Api\V1\StreakController;
use App\Http\Middleware\TrackDailyStreak;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('schools', SchoolController::class);

    Route::post('auth/register', [AuthController::class, 'register'])->middleware('throttle:6,1');
    Route::post('auth/login', [AuthController::class, 'login'])->middleware('throttle:6,1');

    Route::middleware(['auth:sanctum', TrackDailyStreak::class])->group(function (): void {
        Route::get('auth/me', [AuthController::class, 'me']);
        Route::patch('auth/profile', [AuthController::class, 'updateProfile']);
        Route::put('auth/password', [AuthController::class, 'changePassword']);
        Route::post('auth/logout', [AuthController::class, 'logout']);

        Route::get('dashboard', DashboardController::class);
        Route::post('streak/check-in', [StreakController::class, 'checkIn']);

        Route::get('mood-options', [MoodController::class, 'options']);
        Route::get('mood-entries', [MoodController::class, 'index']);
        Route::post('mood-entries', [MoodController::class, 'store']);
        Route::delete('mood-entries/{moodEntry}', [MoodController::class, 'destroy']);

        Route::get('screening/questions', [ScreeningController::class, 'questions']);
        Route::get('screening/results', [ScreeningController::class, 'index']);
        Route::post('screening/results', [ScreeningController::class, 'store']);

        Route::get('education', [EducationController::class, 'index']);
        Route::get('education/search', [EducationController::class, 'search']);
        Route::get('education/{educationContent}', [EducationController::class, 'show']);
        Route::get('recommendations', RecommendationController::class);

        Route::get('community/posts', [CommunityPostController::class, 'index']);
        Route::post('community/posts', [CommunityPostController::class, 'store']);
        Route::delete('community/posts/{communityPost}', [CommunityPostController::class, 'destroy']);
        Route::post('community/posts/{communityPost}/like', [CommunityPostController::class, 'toggleLike']);

        Route::get('risk-alerts', [RiskAlertController::class, 'index']);
        Route::patch('risk-alerts/{riskAlert}/dismiss', [RiskAlertController::class, 'dismiss']);
    });
});
