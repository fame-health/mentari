<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\EducationCategory;
use App\Models\EducationContent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EducationController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = EducationCategory::query()
            ->where('is_active', true)
            ->with(['contents' => fn ($query) => $query
                ->where('is_active', true)
                ->published()
                ->orderByDesc('published_at')])
            ->orderBy('sort_order')
            ->get();

        return response()->json(['data' => $categories]);
    }

    public function show(EducationContent $educationContent): JsonResponse
    {
        $educationContent->loadMissing('category');

        abort_unless(
            $educationContent->is_active &&
            $educationContent->category?->is_active &&
            $educationContent->published_at?->isPast(),
            404,
        );

        return response()->json([
            'data' => $educationContent->load('category'),
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate(['q' => ['required', 'string', 'min:2', 'max:100']]);

        return response()->json([
            'data' => EducationContent::query()
                ->with('category')
                ->visible()
                ->where(fn ($query) => $query
                    ->where('title', 'like', '%'.$validated['q'].'%')
                    ->orWhere('summary', 'like', '%'.$validated['q'].'%'))
                ->latest('published_at')
                ->limit(30)
                ->get(),
        ]);
    }
}
