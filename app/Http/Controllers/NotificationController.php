<?php
// app/Http/Controllers/NotificationController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class NotificationController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->user()->update([
            'push_subscription' => $request->all()
        ]);

        return response()->json(['success' => true]);
    }

    public function sendNotification(Request $request)
    {
        $user = $request->user();

        if (!$user->push_subscription) {
            return response()->json(['error' => 'No subscription'], 400);
        }

        $subscription = Subscription::create($user->push_subscription);

        $auth = [
            'VAPID' => [
                'subject' => 'mailto:your-email@example.com',
                'publicKey' => env('VAPID_PUBLIC_KEY'),
                'privateKey' => env('VAPID_PRIVATE_KEY'),
            ],
        ];

        $webPush = new WebPush($auth);

        $payload = json_encode([
            'title' => $request->title,
            'body' => $request->body,
            'icon' => '/icons/icon-192x192.png',
            'url' => $request->url ?? '/'
        ]);

        $webPush->queueNotification($subscription, $payload);

        foreach ($webPush->flush() as $report) {
            // Handle delivery report
        }

        return response()->json(['success' => true]);
    }
}
