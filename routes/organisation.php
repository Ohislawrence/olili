<?php

use App\Http\Controllers\Organization\CertificateController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified', 'role:organization'])->prefix('organization')->name('organization.')->group(function () {

    // Organization routes
    //Route::middleware(['subscription:student_management'])->get('/org/students', [OrgStudentController::class, 'index']);
    //Route::middleware(['subscription:ai_grading'])->post('/org/grading', [OrgGradingController::class, 'grade']);


    Route::prefix('certificates')->name('certificates.')->group(function () {
        Route::get('/', [CertificateController::class, 'index'])->name('index');
        Route::get('/create', [CertificateController::class, 'create'])->name('create');
        Route::post('/', [CertificateController::class, 'store'])->name('store');
        Route::get('/{certificate}', [CertificateController::class, 'show'])->name('show');
        Route::put('/{certificate}', [CertificateController::class, 'update'])->name('update');
        Route::post('/batch-generate', [CertificateController::class, 'batchGenerate'])->name('batch-generate');
        Route::post('/{certificate}/revoke', [CertificateController::class, 'revoke'])->name('revoke');
        Route::post('/{certificate}/renew', [CertificateController::class, 'renew'])->name('renew');
        Route::post('/{certificate}/regenerate-image', [CertificateController::class, 'regenerateImage'])->name('regenerate-image');
        Route::get('/{certificate}/download', [CertificateController::class, 'download'])->name('download');
        Route::get('/{certificate}/download-image', [CertificateController::class, 'downloadImage'])->name('download-image');
        Route::post('/bulk-download', [CertificateController::class, 'bulkDownload'])->name('bulk-download');
        Route::post('/export', [CertificateController::class, 'export'])->name('export');
        Route::get('/settings', [CertificateController::class, 'settings'])->name('settings');
        Route::post('/settings', [CertificateController::class, 'updateSettings'])->name('update-settings');
        Route::post('/templates/{template}/preview', [CertificateController::class, 'previewTemplate'])->name('preview-template');
    });
});
