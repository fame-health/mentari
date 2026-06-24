<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\JsonResponse;

class SchoolController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $schools = School::query()
            ->select(['id', 'name', 'code', 'address'])
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => $schools,
            'schools' => $schools,
        ]);
    }
}
