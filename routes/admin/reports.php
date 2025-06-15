<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;

    Route::prefix('reports')->group(function() {
        Route::get('/', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/generate', [ReportController::class, 'generate'])->name('reports.generate');
        Route::get('/download-pdf', [ReportController::class, 'downloadPdf'])->name('reports.download');
    });