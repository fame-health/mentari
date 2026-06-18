<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\MoodEntry;
use App\Models\MoodOption;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MoodController extends Controller
{
    public function options(): JsonResponse
    {
        return response()->json([
            'data' => MoodOption::where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $entries = $request->user()
            ->moodEntries()
            ->with('moodOption')
            ->latest('entry_date')
            ->paginate(min($request->integer('per_page', 20), 100));

        return response()->json($entries);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'mood_option_id' => [
                'required',
                Rule::exists('mood_options', 'id')->where('is_active', true),
            ],
            'entry_date' => ['required', 'date', 'before_or_equal:today'],
            'note' => ['nullable', 'string', 'max:2000'],
            'energy' => ['required', 'integer', 'between:0,10'],
            'stress' => ['required', 'integer', 'between:0,10'],
        ]);

        $entry = $request->user()
            ->moodEntries()
            ->whereDate('entry_date', $validated['entry_date'])
            ->first();
        $wasRecentlyCreated = $entry === null;

        if ($entry) {
            $entry->update($validated);
        } else {
            $entry = $request->user()->moodEntries()->create($validated);
        }

        return response()->json([
            'message' => $wasRecentlyCreated ? 'Mood berhasil disimpan.' : 'Mood hari ini berhasil diperbarui.',
            'data' => $entry->load('moodOption'),
        ], $wasRecentlyCreated ? 201 : 200);
    }

    public function destroy(Request $request, MoodEntry $moodEntry): JsonResponse
    {
        abort_unless($moodEntry->user_id === $request->user()->id, 403);

        $moodEntry->delete();

        return response()->json(['message' => 'Catatan mood berhasil dihapus.']);
    }
}
