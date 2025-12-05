<?php
// app/Http/Controllers/PaymentController.php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\SubscriptionPlan;
use App\Models\Subscription;
use App\Models\User;
use App\Services\PaystackService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class PaymentController extends Controller
{
    protected $paystackService;

    public function __construct(PaystackService $paystackService)
    {
        $this->paystackService = $paystackService;
    }

    /**
     * Show pricing page based on user role
     */
    public function pricing(Request $request)
    {
        $user = $request->user();
        $role = $user->getRoleNames()->first();

        // Get plans for the user's role
        $plans = SubscriptionPlan::active()
            ->where('role', $role)
            ->orderBy('sort_order')
            ->get();

        $currentSubscription = $user->current_subscription;

        return Inertia::render('Payment/Pricing', [
            'plans' => $plans,
            'current_subscription' => $currentSubscription,
            'user_role' => $role,
            'paystack_public_key' => $this->paystackService->getPublicKey(),
        ]);
    }

    /**
     * Show pricing for specific role (for role switching)
     */
    public function pricingForRole($role)
    {
        if (!in_array($role, ['student', 'tutor', 'organization'])) {
            abort(404);
        }
        $user = auth()->user();

        $plans = SubscriptionPlan::active()
            ->where('role', $role)
            ->orderBy('sort_order')
            ->get();

        $currentSubscription = auth()->user()->current_subscription;



        return Inertia::render('Payment/Pricing', [
            'plans' => $plans,
            'current_subscription' => $currentSubscription,
            'user_role' => $role,
            'paystack_public_key' => $this->paystackService->getPublicKey(),
        ]);
    }

    /**
     * Initialize subscription payment
     */
    public function initializeSubscription(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
        ]);

        $user = $request->user();
        $plan = SubscriptionPlan::findOrFail($request->plan_id);

        // Check if user can change to this plan
        if (!$this->paystackService->canChangePlan($user, $plan)) {
            return back()->with('error', 'You cannot change to this plan. Please contact support.');
        }

        // Check if plan matches user's role
        if ($plan->role !== $user->getRoleNames()->first()) {
            return back()->with('error', 'This plan is not available for your role.');
        }

        // Initialize payment
        $metadata = [
            'billing_cycle' => 'monthly', // Default, can be made dynamic
            'user_role' => $user->getRoleNames()->first(),
        ];

        $result = $this->paystackService->initializeSubscriptionPayment($user, $plan, $metadata);

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return Inertia::location($result['authorization_url']);
    }

    /**
     * Initialize one-time payment (for courses, etc.)
     */
    public function initializeOneTimePayment(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string|max:255',
            'metadata' => 'sometimes|array',
        ]);

        $user = $request->user();


        $result = $this->paystackService->initializeOneTimePayment(
            $user,
            $request->amount,
            $request->description,
            $request->metadata ?? []
        );

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return Inertia::location($result['authorization_url']);
    }

    /**
     * Handle payment callback from Paystack
     */
    public function handleCallback(Request $request)
    {
        $reference = $request->query('reference');

        Log::info("Payment callback received", [
            'reference' => $reference,
            'all_query_params' => $request->query()
        ]);

        if (!$reference) {
            Log::warning("No reference in callback");
            return $this->redirectToDashboardWithError('Invalid payment reference');
        }

        // Verify payment
        $verification = $this->paystackService->verifyPayment($reference);

        Log::info("Payment verification result:", [
            'success' => $verification['success'],
            'message' => $verification['message'] ?? 'No message'
        ]);

        if (!$verification['success']) {
            return $this->redirectToDashboardWithError('Payment verification failed: ' . $verification['message']);
        }

        $payment = Payment::where('reference', $reference)->first();

        if (!$payment) {
            Log::error("Payment record not found after verification for reference: {$reference}");
            return $this->redirectToDashboardWithError('Payment record not found');
        }

        // Check payment status from verification data
        $paymentStatus = $verification['data']['status'] ?? null;

        Log::info("Final payment status check:", [
            'payment_status' => $paymentStatus,
            'payment_id' => $payment->id
        ]);

        if ($paymentStatus === 'success') {
            return $this->handleSuccessfulPayment($payment, $verification['data']);
        }

        // Payment failed
        return $this->redirectToDashboardWithError('Payment was not successful. Please try again.');
    }

    /**
     * Handle successful payment
     */
    protected function handleSuccessfulPayment(Payment $payment, $paymentData)
    {
        try {
            $user = $payment->user;
            $role = $user->getRoleNames()->first();

            // Determine success message and redirect route based on payment type
            if ($payment->subscription_plan_id) {
                $message = 'Payment successful! Your subscription has been activated.';
                $route = $this->getDashboardRoute($role);
            } else {
                $message = 'Payment successful! ' . ($payment->description ?? 'Your purchase has been completed.');
                $route = $this->getDashboardRoute($role);
            }

            return redirect()->route($route)
                ->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Error handling successful payment: ' . $e->getMessage());
            return $this->redirectToDashboardWithError('Payment successful but there was an error processing your order. Please contact support.');
        }
    }

    /**
     * Handle Paystack webhooks
     */
    public function handleWebhook(Request $request)
    {
        // Verify webhook signature
        $signature = $request->header('x-paystack-signature');
        $payload = $request->getContent();

        if (!$this->paystackService->verifyWebhookSignature($payload, $signature)) {
            Log::warning('Invalid Paystack webhook signature');
            abort(401, 'Invalid signature');
        }

        $eventData = $request->all();
        $event = $eventData['event'] ?? null;
        $data = $eventData['data'] ?? [];

        // Log webhook for debugging
        Log::info("Paystack webhook received: {$event}", $eventData);

        // Handle the webhook
        $result = $this->paystackService->handleWebhook($eventData);

        if (!$result['success']) {
            Log::error('Webhook handling failed: ' . ($result['message'] ?? 'Unknown error'));
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Cancel subscription
     */
    public function cancelSubscription(Request $request)
    {
        $user = $request->user();
        $subscription = $user->current_subscription;

        if (!$subscription) {
            return back()->with('error', 'No active subscription found.');
        }

        // Cancel on Paystack if subscription code exists
        if ($subscription->paystack_subscription_code) {
            $response = $this->paystackService->disableSubscription($subscription->paystack_subscription_code);

            if (!$response['success']) {
                Log::warning('Failed to cancel Paystack subscription: ' . $response['message']);
                // Continue with local cancellation anyway
            }
        }

        // Cancel locally
        $subscription->cancel();

        $role = $user->getRoleNames()->first();
        return redirect()->route($this->getDashboardRoute($role))
            ->with('success', 'Your subscription has been cancelled successfully.');
    }

    /**
     * Upgrade or change subscription plan
     */
    public function changePlan(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
        ]);

        $user = $request->user();
        $newPlan = SubscriptionPlan::findOrFail($request->plan_id);
        $currentSubscription = $user->current_subscription;

        // Check if user can change to this plan
        if (!$this->paystackService->canChangePlan($user, $newPlan)) {
            return back()->with('error', 'You cannot change to this plan. Please contact support.');
        }

        // Check if plan matches user's role
        if ($newPlan->role !== $user->getRoleNames()->first()) {
            return back()->with('error', 'This plan is not available for your role.');
        }

        // If user has no current subscription or is on free plan, initialize payment
        if (!$currentSubscription || $currentSubscription->plan->isFree()) {
            return $this->initializeSubscription($request);
        }

        // Handle plan upgrade/downgrade logic
        return $this->handlePlanChange($user, $currentSubscription, $newPlan);
    }

    /**
     * Handle plan change logic
     */
    protected function handlePlanChange(User $user, Subscription $currentSubscription, SubscriptionPlan $newPlan)
    {
        // For now, initialize payment for the new plan
        // In a more complex system, you might prorate or handle immediate changes
        $metadata = [
            'billing_cycle' => 'monthly',
            'user_role' => $user->getRoleNames()->first(),
            'previous_plan_id' => $currentSubscription->subscription_plan_id,
            'change_type' => $newPlan->price > $currentSubscription->plan->price ? 'upgrade' : 'downgrade',
        ];

        $result = $this->paystackService->initializeSubscriptionPayment($user, $newPlan, $metadata);

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return Inertia::location($result['authorization_url']);
    }

    /**
     * Show payment history
     */
    public function history(Request $request)
{
    try {
        $user = $request->user();
        $role = $user->getRoleNames()->first();

        \Log::info('=== PAYMENT HISTORY DEBUG START ===');
        \Log::info('User ID: ' . $user->id);
        \Log::info('User email: ' . $user->email);

        // Check if user has any payments
        $paymentCount = $user->payments()->count();
        \Log::info('Total payments for user: ' . $paymentCount);

        if ($paymentCount > 0) {
            $samplePayment = $user->payments()->first();
            \Log::info('Sample payment data:', [
                'id' => $samplePayment->id,
                'amount' => $samplePayment->amount,
                'status' => $samplePayment->status,
                'subscription_plan_id' => $samplePayment->subscription_plan_id,
            ]);
        }

        $payments = $user->payments()
            ->with(['subscriptionPlan'])
            ->latest()
            ->paginate(10);

        \Log::info('Payments query executed');
        \Log::info('Payments count from paginator: ' . $payments->count());
        \Log::info('Payments total: ' . $payments->total());

        // Transform the payments with safe access
        $transformedPayments = $payments->through(function ($payment) {
            \Log::info('Transforming payment ID: ' . $payment->id);

            $planName = 'N/A';
            if ($payment->subscriptionPlan) {
                $planName = $payment->subscriptionPlan->name;
                \Log::info('Found subscription plan: ' . $planName);
            } else {
                \Log::info('No subscription plan found for payment: ' . $payment->id);
            }

            return [
                'id' => $payment->id,
                'amount' => $payment->amount,
                'currency' => $payment->currency,
                'status' => $payment->status,
                'reference' => $payment->reference,
                'description' => $payment->description,
                'plan_name' => $planName,
                'paid_at' => $payment->paid_at?->format('M j, Y g:i A'),
                'created_at' => $payment->created_at->format('M j, Y g:i A'),
                'formatted_amount' => $payment->currency . ' ' . number_format($payment->amount, 2),
                'is_subscription' => !is_null($payment->subscription_plan_id),
                'metadata' => $payment->metadata,
            ];
        });

        $totalSpent = $user->payments()
            ->where('status', 'success')
            ->sum('amount');

        $stats = [
            'total_payments' => $paymentCount,
            'successful_payments' => $user->payments()->where('status', 'success')->count(),
            'failed_payments' => $user->payments()->where('status', 'failed')->count(),
            'pending_payments' => $user->payments()->where('status', 'pending')->count(),
            'total_spent' => $totalSpent,
        ];

        \Log::info('Payment stats:', $stats);
        \Log::info('=== PAYMENT HISTORY DEBUG END ===');

        return Inertia::render('Payment/History', [
            'payments' => $transformedPayments,
            'stats' => $stats,
            'filters' => $request->only(['search', 'status', 'type']),
            'user_role' => $role,
        ]);

    } catch (\Exception $e) {
        \Log::error('Payment history error: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());

        return Inertia::render('Payment/History', [
            'payments' => [
                'data' => [],
                'links' => [],
                'meta' => [
                    'current_page' => 1,
                    'from' => null,
                    'last_page' => 1,
                    'links' => [],
                    'path' => url()->current(),
                    'per_page' => 10,
                    'to' => null,
                    'total' => 0,
                ]
            ],
            'stats' => [
                'total_payments' => 0,
                'successful_payments' => 0,
                'failed_payments' => 0,
                'pending_payments' => 0,
                'total_spent' => 0,
            ],
            'filters' => $request->only(['search', 'status', 'type']),
            'user_role' => $request->user()->getRoleNames()->first(),
        ]);
    }
}

    public function paymentHistory(Request $request)
    {
        try {
            $user = $request->user();
            $role = $user->getRoleNames()->first();

            \Log::info('=== PAYMENT HISTORY DEBUG START ===');
            \Log::info('User ID: ' . $user->id);
            \Log::info('User email: ' . $user->email);

            // Check if user has any payments
            $paymentCount = $user->payments()->count();
            \Log::info('Total payments for user: ' . $paymentCount);

            if ($paymentCount > 0) {
                $samplePayment = $user->payments()->first();
                \Log::info('Sample payment data:', [
                    'id' => $samplePayment->id,
                    'amount' => $samplePayment->amount,
                    'status' => $samplePayment->status,
                    'subscription_plan_id' => $samplePayment->subscription_plan_id,
                ]);
            }

            $payments = $user->payments()
                ->with(['subscriptionPlan'])
                ->latest()
                ->paginate(10);

            \Log::info('Payments query executed');
            \Log::info('Payments count from paginator: ' . $payments->count());
            \Log::info('Payments total: ' . $payments->total());

            // Transform the payments with safe access
            $transformedPayments = $payments->through(function ($payment) {
                \Log::info('Transforming payment ID: ' . $payment->id);

                $planName = 'N/A';
                if ($payment->subscriptionPlan) {
                    $planName = $payment->subscriptionPlan->name;
                    \Log::info('Found subscription plan: ' . $planName);
                } else {
                    \Log::info('No subscription plan found for payment: ' . $payment->id);
                }

                return [
                    'id' => $payment->id,
                    'amount' => $payment->amount,
                    'currency' => $payment->currency,
                    'status' => $payment->status,
                    'reference' => $payment->reference,
                    'description' => $payment->description,
                    'plan_name' => $planName,
                    'paid_at' => $payment->paid_at?->format('M j, Y g:i A'),
                    'created_at' => $payment->created_at->format('M j, Y g:i A'),
                    'formatted_amount' => $payment->currency . ' ' . number_format($payment->amount, 2),
                    'is_subscription' => !is_null($payment->subscription_plan_id),
                    'metadata' => $payment->metadata,
                ];
            });

            $totalSpent = $user->payments()
                ->where('status', 'success')
                ->sum('amount');

            $stats = [
                'total_payments' => $paymentCount,
                'successful_payments' => $user->payments()->where('status', 'success')->count(),
                'failed_payments' => $user->payments()->where('status', 'failed')->count(),
                'pending_payments' => $user->payments()->where('status', 'pending')->count(),
                'total_spent' => $totalSpent,
            ];

            \Log::info('Payment stats:', $stats);
            \Log::info('=== PAYMENT HISTORY DEBUG END ===');

            return Inertia::render('Payment/History', [
                'payments' => $transformedPayments,
                'stats' => $stats,
                'filters' => $request->only(['search', 'status', 'type']),
                'user_role' => $role,
            ]);

        } catch (\Exception $e) {
            \Log::error('Payment history error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return Inertia::render('Payment/History', [
                'payments' => [
                    'data' => [],
                    'links' => [],
                    'meta' => [
                        'current_page' => 1,
                        'from' => null,
                        'last_page' => 1,
                        'links' => [],
                        'path' => url()->current(),
                        'per_page' => 10,
                        'to' => null,
                        'total' => 0,
                    ]
                ],
                'stats' => [
                    'total_payments' => 0,
                    'successful_payments' => 0,
                    'failed_payments' => 0,
                    'pending_payments' => 0,
                    'total_spent' => 0,
                ],
                'filters' => $request->only(['search', 'status', 'type']),
                'user_role' => $request->user()->getRoleNames()->first(),
            ]);
        }
    }
    /**
     * Filter payment history (AJAX endpoint)
     */
    public function filterHistory(Request $request)
    {
        try {
            $user = $request->user();

            $query = $user->payments()->with(['subscriptionPlan']);

            // Filter by status
            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            // Filter by type
            if ($request->filled('type')) {
                if ($request->type === 'subscription') {
                    $query->whereNotNull('subscription_plan_id');
                } elseif ($request->type === 'one_time') {
                    $query->whereNull('subscription_plan_id');
                }
            }

            // Search by reference or description
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('reference', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('subscriptionPlan', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
                });
            }

            $payments = $query->latest()
                ->paginate(10)
                ->through(function ($payment) {
                    return [
                        'id' => $payment->id,
                        'amount' => $payment->amount,
                        'currency' => $payment->currency,
                        'status' => $payment->status,
                        'reference' => $payment->reference,
                        'description' => $payment->description,
                        'plan_name' => $payment->subscriptionPlan?->name ?? 'N/A',
                        'paid_at' => $payment->paid_at?->format('M j, Y g:i A'),
                        'created_at' => $payment->created_at->format('M j, Y g:i A'),
                        'formatted_amount' => $payment->currency . ' ' . number_format($payment->amount, 2),
                        'is_subscription' => !is_null($payment->subscription_plan_id),
                        'metadata' => $payment->metadata,
                    ];
                });

            return response()->json([
                'payments' => $payments,
                'filters' => $request->only(['search', 'status', 'type'])
            ]);

        } catch (\Exception $e) {
            \Log::error('Filter payment history error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to filter payments'], 500);
        }
    }

    /**
     * Get subscription status
     */
    public function subscriptionStatus(Request $request)
    {
        $user = $request->user();
        $subscriptionStatus = $this->paystackService->getUserSubscriptionStatus($user);

        return response()->json($subscriptionStatus);
    }

    /**
     * Helper method to redirect to dashboard with error
     */
    protected function redirectToDashboardWithError(string $message)
    {
        $user = auth()->user();
        $role = $user ? $user->getRoleNames()->first() : 'student';
        $route = $this->getDashboardRoute($role);

        return redirect()->route($route)->with('error', $message);
    }

    /**
     * Get dashboard route based on role
     */
    protected function getDashboardRoute(string $role): string
    {
        return match($role) {
            'tutor' => 'tutor.dashboard',
            'organization' => 'organization.dashboard',
            default => 'student.dashboard',
        };
    }

    /**
     * Show payment success page
     */
    public function success(Request $request)
    {
        $reference = $request->query('reference');
        $payment = $reference ? Payment::where('reference', $reference)->first() : null;

        $user = $request->user();
        $role = $user->getRoleNames()->first();

        return Inertia::render('Payment/Success', [
            'payment' => $payment,
            'user_role' => $role,
        ]);
    }

    /**
     * Show payment failure page
     */
    public function failure(Request $request)
    {
        $reference = $request->query('reference');
        $payment = $reference ? Payment::where('reference', $reference)->first() : null;

        $user = $request->user();
        $role = $user->getRoleNames()->first();

        return Inertia::render('Payment/Failure', [
            'payment' => $payment,
            'user_role' => $role,
        ]);
    }
}
