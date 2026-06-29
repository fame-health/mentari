<?php

use App\Http\Controllers\Admin\AnalysisResultExportController;
use App\Http\Controllers\Admin\SchoolScreeningExportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $schools = \App\Models\School::orderBy('name')->get();
    return view('welcome', compact('schools'));
});

Route::middleware('auth')->prefix('admin/screening-results/school/{school}')->group(function (): void {
    Route::get('/export/pdf', [SchoolScreeningExportController::class, 'pdf'])
        ->name('admin.screening-results.school.export.pdf');
    Route::get('/export/excel', [SchoolScreeningExportController::class, 'excel'])
        ->name('admin.screening-results.school.export.excel');
});

Route::middleware('auth')->prefix('admin/analysis-results/school/{school}')->group(function (): void {
    Route::get('/export/pdf', [AnalysisResultExportController::class, 'pdf'])
        ->name('admin.analysis-results.school.export.pdf');
    Route::get('/export/excel', [AnalysisResultExportController::class, 'excel'])
        ->name('admin.analysis-results.school.export.excel');
});
