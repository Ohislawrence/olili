<?php
// routes/api.php
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\ProgressController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Admin\BlogPostController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PushSubscriptionController;
use App\Http\Controllers\Api\WebPushController;

Route::middleware(['auth:sanctum'])->group(function () {
    // Real-time chat
    Route::post('/chat/{session}/message', [ChatController::class, 'sendMessage']);
    Route::get('/chat/{session}/messages', [ChatController::class, 'getMessages']);

    // Progress updates
    Route::post('/progress/track', [ProgressController::class, 'track']);

    // Notifications
    Route::prefix('web-push')->group(function () {
        Route::post('subscribe', [WebPushController::class, 'subscribe']);
        Route::delete('unsubscribe', [WebPushController::class, 'unsubscribe']);
        Route::post('test', [WebPushController::class, 'sendTest']);
        Route::get('status', [WebPushController::class, 'status']);

        // Admin routes
        Route::post('send-to-user/{user}', [WebPushController::class, 'sendToUser'])
            ->middleware('can:send_push_notifications');
    });

    Route::post('/push/subscribe', [PushSubscriptionController::class, 'store']);
    Route::post('/push/test', [PushSubscriptionController::class, 'test']);
    Route::get('/push/status', [PushSubscriptionController::class, 'status']);

});

Route::get('/api/featured-blog-posts', [BlogPostController::class, 'featured']);
