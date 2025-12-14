<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WebPushSubscription;
use App\Models\PushNotification;
use App\Services\WebPush\Sender;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WebPushController extends Controller
{
    /**
     * Subscribe to push notifications
     */
    public function subscribe(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'endpoint' => 'required|url|max:500',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = Auth::user();

            $subscription = WebPushSubscription::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'endpoint' => $request->input('endpoint'),
                ],
                [
                    'p256dh_key' => $request->input('keys.p256dh'),
                    'auth_key' => $request->input('keys.auth'),
                    'user_agent' => $request->userAgent(),
                ]
            );

            Log::info('Web Push subscription saved', [
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Subscribed to push notifications',
                'data' => [
                    'id' => $subscription->id,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to save subscription', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to subscribe',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Unsubscribe from push notifications
     */
    public function unsubscribe(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'endpoint' => 'required|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = Auth::user();

            $deleted = WebPushSubscription::where('user_id', $user->id)
                ->where('endpoint', $request->input('endpoint'))
                ->delete();

            Log::info('Web Push subscription removed', [
                'user_id' => $user->id,
                'endpoint' => $request->input('endpoint'),
                'deleted' => $deleted,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Unsubscribed from push notifications',
                'deleted' => $deleted,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to remove subscription', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to unsubscribe',
            ], 500);
        }
    }

    /**
     * Send a test notification
     */
    public function sendTest(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = Auth::user();

            // Check if user has subscriptions
            if ($user->webPushSubscriptions()->count() === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No push subscriptions found',
                ], 400);
            }

            // Create notification record
            $notification = PushNotification::create([
                'user_id' => $user->id,
                'title' => $request->input('title'),
                'body' => $request->input('body'),
                'icon' => $request->input('icon', '/icons/icon-192x192.png'),
                'url' => $request->input('url', url('/')),
                'data' => $request->input('data', []),
            ]);

            // Send notification
            $sender = new Sender();
            $payload = $sender->createNotification(
                $request->input('title'),
                $request->input('body'),
                $request->input('url', url('/')),
                $request->input('icon', '/icons/icon-192x192.png'),
                $request->input('data', [])
            );

            $results = $sender->sendToSubscriptions(
                $user->webPushSubscriptions,
                $payload
            );

            // Update notification record
            $notification->update([
                'sent_at' => now(),
                'delivered_at' => $results['success'] > 0 ? now() : null,
            ]);

            Log::info('Test notification sent', [
                'user_id' => $user->id,
                'results' => $results,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Test notification sent',
                'results' => $results,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send test notification', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send test notification',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Get subscription status
     */
    public function status(): JsonResponse
    {
        $user = Auth::user();

        $subscriptions = $user->webPushSubscriptions()
            ->select(['id', 'endpoint', 'created_at'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'has_subscriptions' => $subscriptions->count() > 0,
                'subscription_count' => $subscriptions->count(),
                'subscriptions' => $subscriptions,
            ],
        ]);
    }

    /**
     * Send notification to user (admin endpoint)
     */
    public function sendToUser(Request $request, $userId): JsonResponse
    {
        // Add authorization check here
        if (!Auth::user()->is_admin) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = \App\Models\User::findOrFail($userId);

            if ($user->webPushSubscriptions()->count() === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'User has no push subscriptions',
                ], 400);
            }

            // Create notification record
            $notification = PushNotification::create([
                'user_id' => $user->id,
                'title' => $request->input('title'),
                'body' => $request->input('body'),
                'icon' => $request->input('icon', '/icons/icon-192x192.png'),
                'url' => $request->input('url', url('/')),
                'data' => $request->input('data', []),
            ]);

            // Send notification
            $sender = new Sender();
            $payload = $sender->createNotification(
                $request->input('title'),
                $request->input('body'),
                $request->input('url', url('/')),
                $request->input('icon', '/icons/icon-192x192.png'),
                $request->input('data', [])
            );

            $results = $sender->sendToSubscriptions(
                $user->webPushSubscriptions,
                $payload
            );

            // Update notification record
            $notification->update([
                'sent_at' => now(),
                'delivered_at' => $results['success'] > 0 ? now() : null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Notification sent to user',
                'results' => $results,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send notification to user', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send notification',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
