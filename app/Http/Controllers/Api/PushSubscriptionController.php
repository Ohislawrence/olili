<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PushSubscription;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PushSubscriptionController extends Controller
{
    /**
     * Subscribe to push notifications
     */
    public function store(Request $request): JsonResponse
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

            // Use the relationship method from User model
            $subscription = $user->addPushSubscription($request->all());

            Log::info('Push subscription saved', [
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Subscribed to push notifications',
                'data' => [
                    'id' => $subscription->id,
                    'endpoint' => $subscription->endpoint,
                    'created_at' => $subscription->created_at,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to save push subscription', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to subscribe to push notifications',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Unsubscribe from push notifications
     */
    public function destroy(Request $request): JsonResponse
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
            $deleted = $user->removePushSubscription($request->input('endpoint'));

            if ($deleted) {
                Log::info('Push subscription removed', [
                    'user_id' => $user->id,
                    'endpoint' => $request->input('endpoint'),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Unsubscribed from push notifications',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Subscription not found',
            ], 404);

        } catch (\Exception $e) {
            Log::error('Failed to remove push subscription', [
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
     * Get user's push subscription status
     */
    public function status(): JsonResponse
    {
        $user = Auth::user();

        $subscriptions = $user->activePushSubscriptions()
            ->select(['id', 'endpoint', 'user_agent', 'created_at', 'last_used_at'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'has_subscriptions' => $subscriptions->count() > 0,
                'subscription_count' => $subscriptions->count(),
                'subscriptions' => $subscriptions,
                'metadata' => [
                    'can_receive_notifications' => $subscriptions->count() > 0,
                    'devices_count' => $subscriptions->count(),
                    'latest_subscription' => $subscriptions->sortByDesc('created_at')->first(),
                ],
            ],
        ]);
    }

    /**
     * Update subscription (e.g., mark as used)
     */
    public function update(Request $request, PushSubscription $subscription): JsonResponse
    {
        // Ensure the subscription belongs to the authenticated user
        if ($subscription->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        try {
            $subscription->markAsUsed();

            return response()->json([
                'success' => true,
                'message' => 'Subscription updated',
                'data' => $subscription,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update subscription', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update subscription',
            ], 500);
        }
    }

    /**
     * Deactivate all user subscriptions
     */
    public function deactivateAll(): JsonResponse
    {
        try {
            $user = Auth::user();
            $user->deactivateAllPushSubscriptions();

            Log::info('All push subscriptions deactivated', [
                'user_id' => $user->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'All push subscriptions deactivated',
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to deactivate subscriptions', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to deactivate subscriptions',
            ], 500);
        }
    }

    public function test(Request $request): JsonResponse
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
            if ($user->pushSubscriptions()->active()->count() === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active push subscriptions found',
                ], 400);
            }

            // Use CUSTOM WebPushService
            $webPushService = new WebPushService();

            $notification = $webPushService->createNotification(
                $request->input('title'),
                $request->input('body'),
                $request->input('url', url('/')),
                $request->input('icon'),
                $request->input('badge'),
                $request->input('data', [])
            );

            $results = $webPushService->sendToUser($user, $notification);

            Log::info('Test notification sent (custom service)', [
                'user_id' => $user->id,
                'results' => $results,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Test notification sent',
                'results' => $results,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send test notification (custom)', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send test notification',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Broadcast notification to all users (admin)
     */
    public function broadcast(Request $request): JsonResponse
    {
        // Add admin check
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
            $webPushService = new WebPushService();

            $notification = $webPushService->createNotification(
                $request->input('title'),
                $request->input('body'),
                $request->input('url', url('/')),
                $request->input('icon'),
                $request->input('badge'),
                $request->input('data', [])
            );

            // Get all users with active subscriptions
            $users = \App\Models\User::whereHas('pushSubscriptions', function ($query) {
                $query->active();
            })->get();

            $totalResults = [
                'total_users' => $users->count(),
                'success' => 0,
                'failed' => 0,
                'failed_user_ids' => [],
            ];

            foreach ($users as $user) {
                $result = $webPushService->sendToUser($user, $notification);

                if ($result['success'] > 0) {
                    $totalResults['success']++;
                } else {
                    $totalResults['failed']++;
                    $totalResults['failed_user_ids'][] = $user->id;
                }
            }

            Log::info('Broadcast notification sent', [
                'admin_id' => Auth::id(),
                'results' => $totalResults,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Broadcast notification sent',
                'results' => $totalResults,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to broadcast notification', [
                'admin_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to broadcast notification',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
