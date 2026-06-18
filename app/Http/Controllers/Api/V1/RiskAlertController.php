<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\RiskAlert;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RiskAlertController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(
            $request->user()
                ->riskAlerts()
                ->with('screeningResult')
                ->latest()
                ->paginate(min($request->integer('per_page', 20), 100)),
        );
    }

    public function dismiss(Request $request, RiskAlert $riskAlert): JsonResponse
    {
        abort_unless($riskAlert->user_id === $request->user()->id, 403);

        $riskAlert->update(['dismissed_at' => now()]);

        return response()->json([
            'message' => 'Alert ditandai sudah dibaca.',
            'data' => $riskAlert->fresh(),
        ]);
    }
}
