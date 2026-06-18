<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Recommendation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $query = Recommendation::where('is_active', true);

        if ($request->filled('category')) {
            $query->where('category', $request->string('category'));
        }

        return response()->json([
            'data' => $query->orderBy('category')->orderBy('title')->get(),
        ]);
    }
}
