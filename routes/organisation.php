<?php

use App\Http\Controllers\Organization\CertificateController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified', 'role:organization'])->prefix('organization')->name('organization.')->group(function () {

    // Organization routes
    //Route::middleware(['subscription:student_management'])->get('/org/students', [OrgStudentController::class, 'index']);
    //Route::middleware(['subscription:ai_grading'])->post('/org/grading', [OrgGradingController::class, 'grade']);


    Route::prefix('certificates')->name('certificates.')->group(function () {
        Route::get('/', [CertificateController::class, 'index'])->name('index');
        Route::post('/batch-generate', [CertificateController::class, 'batchGenerate'])->name('batch-generate');
        Route::post('/{certificate}/revoke', [CertificateController::class, 'revoke'])->name('revoke');
        Route::get('/settings', [CertificateController::class, 'settings'])->name('settings');
        Route::put('/settings', [CertificateController::class, 'updateSettings'])->name('settings.update');
    });
});