<?php
// routes/api.php
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\ProgressController;
use App\Http\Controllers\Api\NotificationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    // Real-time chat
    Route::post('/chat/{session}/message', [ChatController::class, 'sendMessage']);
    Route::get('/chat/{session}/messages', [ChatController::class, 'getMessages']);

    // Progress updates
    Route::post('/progress/track', [ProgressController::class, 'track']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/mark-read', [NotificationController::class, 'markAsRead']);
});
