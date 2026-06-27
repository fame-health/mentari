<?php

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
