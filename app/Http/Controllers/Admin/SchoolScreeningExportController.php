<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Services\SchoolScreeningExcelExporter;
use App\Services\SchoolScreeningPdfExporter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class SchoolScreeningExportController extends Controller
{
    public function pdf(
        Request $request,
        School $school,
        SchoolScreeningPdfExporter $exporter,
    ): Response {
        $this->authorizeAdmin($request);
        $filename = $this->filename($school, 'pdf');

        return response($exporter->make($school), 200, [
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
        SchoolScreeningExcelExporter $exporter,
    ): BinaryFileResponse {
        $this->authorizeAdmin($request);
        $path = $exporter->make($school);

        return response()
            ->download($path, $this->filename($school, 'xlsx'), [
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

    private function filename(School $school, string $extension): string
    {
        $schoolName = Str::slug($school->name) ?: 'sekolah';

        return "hasil-screening-{$schoolName}-".now()->format('Y-m-d').".{$extension}";
    }
}
