<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ScreeningQuestion;
use App\Services\Dass21ScoringService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ScreeningController extends Controller
{
    public function __construct(private readonly Dass21ScoringService $scoring) {}

    public function questions(): JsonResponse
    {
        return response()->json([
            'data' => ScreeningQuestion::where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        return response()->json(
            $request->user()
                ->screeningResults()
                ->with('riskAlert')
                ->latest('taken_at')
                ->paginate(min($request->integer('per_page', 20), 100)),
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'answers' => ['required', 'array', 'min:1'],
            'answers.*.question_id' => ['required', 'integer', 'distinct', 'exists:screening_questions,id'],
            'answers.*.score' => ['required', 'integer', 'between:0,3'],
        ]);

        $questions = ScreeningQuestion::where('is_active', true)->get()->keyBy('id');
        $answerIds = collect($validated['answers'])->pluck('question_id')->sort()->values();

        if ($questions->count() !== $answerIds->count() ||
            $questions->keys()->sort()->values()->all() !== $answerIds->all()) {
            throw ValidationException::withMessages([
                'answers' => ['Semua pertanyaan screening yang aktif harus dijawab tepat satu kali.'],
            ]);
        }

        $result = DB::transaction(function () use ($request, $validated, $questions) {
            $scores = ['depression' => 0, 'anxiety' => 0, 'stress' => 0];

            foreach ($validated['answers'] as $answer) {
                $scores[$questions[$answer['question_id']]->scale] += $answer['score'];
            }

            $scores = collect($scores)->map(fn (int $score): int => $score * 2)->all();
            $severities = $this->scoring->severities($scores);

            $result = $request->user()->screeningResults()->create([
                'taken_at' => now(),
                'depression_score' => $scores['depression'],
                'depression_severity' => $severities['depression'],
                'anxiety_score' => $scores['anxiety'],
                'anxiety_severity' => $severities['anxiety'],
                'stress_score' => $scores['stress'],
                'stress_severity' => $severities['stress'],
                'summary' => $this->scoring->summary($severities),
            ]);

            $result->answers()->createMany(
                collect($validated['answers'])->map(fn (array $answer): array => [
                    'screening_question_id' => $answer['question_id'],
                    'score' => $answer['score'],
                ])->all(),
            );

            if ($level = $this->scoring->riskLevel($severities)) {
                $request->user()->riskAlerts()->create([
                    'screening_result_id' => $result->id,
                    'level' => $level,
                    'title' => $level === 'urgent' ? 'Perlu dukungan segera' : 'Perlu perhatian',
                    'message' => $result->summary,
                    'recommendation' => $level === 'urgent'
                        ? 'Segera hubungi guru BK, psikolog, orang dewasa tepercaya, atau layanan darurat bila merasa tidak aman.'
                        : 'Pertimbangkan berbicara dengan guru BK atau orang dewasa tepercaya dan lakukan screening lanjutan.',
                ]);
            }

            return $result;
        });

        return response()->json([
            'message' => 'Screening berhasil disimpan.',
            'data' => $result->load(['answers.question', 'riskAlert']),
        ], 201);
    }
}
