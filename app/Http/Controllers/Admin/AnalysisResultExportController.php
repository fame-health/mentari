<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\School;
use App\Services\AnalysisResultExcelExporter;
use App\Services\AnalysisResultPdfExporter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class AnalysisResultExportController extends Controller
{
    public function pdf(
        Request $request,
        School $school,
        AnalysisResultPdfExporter $exporter,
    ): Response {
        $this->authorizeAdmin($request);
        $classroom = $this->resolveClassroom($request, $school);
        $filename = $this->filename($school, 'pdf', $classroom);

        return response($exporter->make($school, $classroom?->id), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control' => 'private, no-store, max-age=0',
            'Pragma' => 'no-cache',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function excel(
        Request $request,
        School $school,
        AnalysisResultExcelExporter $exporter,
    ): BinaryFileResponse {
        $this->authorizeAdmin($request);
        $classroom = $this->resolveClassroom($request, $school);
        $path = $exporter->make($school, $classroom?->id);

        return response()
            ->download($path, $this->filename($school, 'xlsx', $classroom), [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Cache-Control' => 'private, no-store, max-age=0',
                'Pragma' => 'no-cache',
                'X-Content-Type-Options' => 'nosniff',
            ])
            ->deleteFileAfterSend(true);
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()?->role === 'admin', 403);
    }

    private function resolveClassroom(Request $request, School $school): ?Classroom
    {
        $classroomKey = $request->query('class');

        if (blank($classroomKey) || $classroomKey === 'all') {
            return null;
        }

        abort_unless(ctype_digit((string) $classroomKey), 404);

        $classroom = $school->classrooms()
            ->whereKey((int) $classroomKey)
            ->first();

        abort_unless($classroom, 404);

        return $classroom;
    }

    private function filename(School $school, string $extension, ?Classroom $classroom = null): string
    {
        $schoolName = Str::slug($school->name) ?: 'sekolah';
        $classroomName = $classroom ? '-kelas-'.Str::slug($classroom->name) : '-semua-kelas';

        return "hasil-analisis-{$schoolName}{$classroomName}-".now()->format('Y-m-d').".{$extension}";
    }
}
