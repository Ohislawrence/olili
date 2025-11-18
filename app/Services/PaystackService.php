<?php
// app/Services/PaystackService.php

namespace App\Services;

use App\Models\Payment;
use App\Models\SubscriptionPlan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class PaystackService
{
    protected $secretKey;
    protected $publicKey;
    protected $baseUrl = 'https://api.paystack.co';
    protected $client;

    public function __construct()
    {
        $this->secretKey = config('services.paystack.secret_key');
        $this->publicKey = config('services.paystack.public_key');

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ],
            'verify' => app()->environment('production')
                ? true
                : false, // Disable SSL verification in development
        ]);
    }

    /**
     * Make API request to Paystack
     */
    protected function makeRequest(string $method, string $endpoint, array $data = []): array
    {
        try {
            $options = [];

            if (!empty($data)) {
                $options['json'] = $data;
            }

            $response = $this->client->request($method, $endpoint, $options);
            $result = json_decode($response->getBody(), true);

            return [
                'status' => true,
                'message' => $result['message'] ?? 'Success',
                'data' => $result['data'] ?? null
            ];

        } catch (\Exception $e) {
            Log::error('Paystack request failed: ' . $e->getMessage());

            // Try to get error response body
            $errorMessage = $e->getMessage();
            if ($e->hasResponse()) {
                $errorResponse = json_decode($e->getResponse()->getBody(), true);
                $errorMessage = $errorResponse['message'] ?? $errorMessage;
            }

            return [
                'status' => false,
                'message' => $errorMessage,
                'data' => null
            ];
        }
    }

    /**
     * Initialize payment for subscription plan
     */
    public function initializeSubscriptionPayment(User $user, SubscriptionPlan $plan, array $metadata = []): array
    {
        try {
            $reference = $this->generateReference();
            $amount = (int)($plan->price * 100); // Convert to kobo and ensure it's integer

            Log::info('Initializing subscription payment:', [
                'plan_id' => $plan->id,
                'plan_price' => $plan->price,
                'amount_in_kobo' => $amount,
                'user_id' => $user->id
            ]);

            $response = $this->makeRequest('POST', '/transaction/initialize', [
                'email' => $user->email,
                'amount' => $amount,
                'reference' => $reference,
                'currency' => $plan->currency ?? 'NGN',
                'metadata' => array_merge($metadata, [
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                    'plan_code' => $plan->code,
                    'user_role' => $user->getRoleNames()->first(),
                    'payment_type' => 'subscription',
                ]),
                'callback_url' => route('payment.callback'),
            ]);

            if (!$response['status']) {
                Log::error('Paystack subscription initialization failed:', [
                    'message' => $response['message'],
                    'plan_id' => $plan->id
                ]);
                return [
                    'success' => false,
                    'message' => $response['message'],
                ];
            }

            // Create payment record
            Payment::create([
                'user_id' => $user->id,
                'subscription_plan_id' => $plan->id,
                'amount' => $plan->price,
                'currency' => $plan->currency,
                'reference' => $reference,
                'paystack_access_code' => $response['data']['access_code'],
                'status' => 'pending',
                'metadata' => $metadata,
            ]);

            Log::info('Subscription payment initialized successfully:', [
                'reference' => $reference,
                'authorization_url' => $response['data']['authorization_url']
            ]);

            return [
                'success' => true,
                'authorization_url' => $response['data']['authorization_url'],
                'access_code' => $response['data']['access_code'],
                'reference' => $response['data']['reference'],
            ];

        } catch (\Exception $e) {
            Log::error('Paystack subscription payment initialization failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Payment initialization failed. Please try again.',
            ];
        }
    }

    /**
     * Initialize one-time payment (for course purchases, etc.)
     */
    public function initializeOneTimePayment(User $user, float $amount, string $description, array $metadata = []): array
    {
        try {
            $reference = $this->generateReference();
            $amountInKobo = (int)($amount * 100); // Convert to kobo and ensure it's integer

            Log::info('Paystack Request Parameters:', [
                'email' => $user->email,
                'amount' => $amountInKobo,
                'original_amount' => $amount,
                'reference' => $reference,
                'currency' => 'NGN',
                'metadata' => array_merge($metadata, [
                    'user_id' => $user->id,
                    'user_role' => $user->getRoleNames()->first(),
                    'payment_type' => 'one_time',
                    'description' => $description,
                ]),
                'callback_url' => route('payment.callback'),
            ]);

            $response = $this->makeRequest('POST', '/transaction/initialize', [
                'email' => $user->email,
                'amount' => $amountInKobo, // Now integer
                'reference' => $reference,
                'currency' => 'NGN',
                'metadata' => array_merge($metadata, [
                    'user_id' => $user->id,
                    'user_role' => $user->getRoleNames()->first(),
                    'payment_type' => 'one_time',
                    'description' => $description,
                ]),
                'callback_url' => route('payment.callback'),
            ]);

            if (!$response['status']) {
                return [
                    'success' => false,
                    'message' => $response['message'],
                ];
            }

            Log::info('Paystack API Success Response:', ['response' => $response]);

            // Create payment record - ONLY AFTER SUCCESSFUL RESPONSE
            Payment::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'currency' => 'NGN',
                'reference' => $reference,
                'paystack_access_code' => $response['data']['access_code'],
                'status' => 'pending',
                'metadata' => $metadata,
                'description' => $description,
            ]);

            return [
                'success' => true,
                'authorization_url' => $response['data']['authorization_url'],
                'access_code' => $response['data']['access_code'],
                'reference' => $response['data']['reference'],
            ];

        } catch (\Exception $e) {
            Log::error('Paystack API Error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return [
                'success' => false,
                'message' => 'Payment initialization failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Verify payment and handle subscription activation
     */
    public function verifyPayment(string $reference): array
    {
        try {
            Log::info("Starting payment verification for reference: {$reference}");

            $response = $this->makeRequest('GET', "/transaction/verify/{$reference}");

            Log::info("Paystack verification response:", [
                'status' => $response['status'],
                'data_status' => $response['data']['status'] ?? 'no status',
                'reference' => $reference
            ]);

            $payment = Payment::where('reference', $reference)->first();

            if (!$payment) {
                Log::error("Payment record not found for reference: {$reference}");
                return [
                    'success' => false,
                    'message' => 'Payment record not found',
                ];
            }

            Log::info("Payment record found:", [
                'payment_id' => $payment->id,
                'user_id' => $payment->user_id,
                'plan_id' => $payment->subscription_plan_id,
                'status' => $payment->status
            ]);

            if ($response['status'] && $response['data']['status'] === 'success') {
                Log::info("Payment successful, updating payment record and activating subscription");

                $payment->update([
                    'status' => 'success',
                    'paid_at' => now(),
                    'paystack_data' => json_encode($response['data']),
                ]);

                // Handle subscription activation if this is a subscription payment
                if ($payment->subscription_plan_id) {
                    Log::info("This is a subscription payment, activating subscription for plan: {$payment->subscription_plan_id}");
                    $plan = SubscriptionPlan::find($payment->subscription_plan_id);
                    if ($plan) {
                        Log::info("Plan found, calling activateSubscription");
                        $this->activateSubscription($payment->user, $plan, $response['data']);
                    } else {
                        Log::error("Plan not found for ID: {$payment->subscription_plan_id}");
                    }
                } else {
                    Log::info("This is not a subscription payment (no subscription_plan_id)");
                }

                return [
                    'success' => true,
                    'data' => $response['data'],
                    'message' => 'Payment verified successfully',
                    'payment' => $payment,
                ];
            }

            // Payment failed
            Log::warning("Payment not successful for reference: {$reference}");
            $payment->update([
                'status' => 'failed',
                'paystack_data' => json_encode($response['data']),
            ]);

            return [
                'success' => false,
                'message' => 'Payment not successful',
                'data' => $response['data'],
            ];

        } catch (\Exception $e) {
            Log::error('Paystack verification failed: ' . $e->getMessage());

            // Update payment status to failed
            $payment = Payment::where('reference', $reference)->first();
            if ($payment) {
                $payment->update([
                    'status' => 'failed',
                    'paystack_data' => json_encode(['error' => $e->getMessage()]),
                ]);
            }

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Activate subscription after successful payment
     */
    protected function activateSubscription(User $user, SubscriptionPlan $plan, $paymentData): void
    {
        try {
            Log::info("Starting subscription activation for user: {$user->id}, plan: {$plan->id}");

            // Cancel any existing active subscription
            $canceledCount = $user->subscriptions()->active()->update([
                'status' => 'canceled',
                'canceled_at' => now(),
            ]);

            Log::info("Canceled {$canceledCount} existing active subscriptions");

            // Create new subscription
            $subscriptionData = [
                'user_id' => $user->id,
                'subscription_plan_id' => $plan->id,
                'status' => 'active',
                'paystack_subscription_code' => $paymentData['authorization']['subscription_code'] ?? null,
                'paystack_customer_code' => $paymentData['customer']['customer_code'] ?? null,
                'authorization_code' => $paymentData['authorization']['authorization_code'] ?? null,
                'card_type' => $paymentData['authorization']['card_type'] ?? null,
                'last4' => $paymentData['authorization']['last4'] ?? null,
                'starts_at' => now(),
                'ends_at' => now()->addDays($plan->billing_cycle_days),
            ];

            Log::info("Creating subscription with data:", $subscriptionData);

            $subscription = Subscription::create($subscriptionData);

            Log::info("Subscription activated successfully for user {$user->id} for plan {$plan->name}, subscription ID: {$subscription->id}");

        } catch (\Exception $e) {
            Log::error('Subscription activation failed: ' . $e->getMessage());
            throw $e;
        }
    }
    /**
     * Create subscription for recurring payments
     */
    public function createSubscription(User $user, SubscriptionPlan $plan, string $authorizationCode): array
    {
        try {
            // First, ensure customer exists
            $customerResponse = $this->createCustomer($user);
            if (!$customerResponse['success']) {
                return $customerResponse;
            }

            // Create Paystack plan if it doesn't exist
            $paystackPlanCode = $plan->paystack_plan_code;
            if (!$paystackPlanCode) {
                $planResponse = $this->createPlan($plan);
                if (!$planResponse['success']) {
                    return $planResponse;
                }
                $paystackPlanCode = $planResponse['plan_code'];
            }

            $response = $this->makeRequest('POST', '/subscription', [
                'customer' => $customerResponse['customer_code'],
                'plan' => $paystackPlanCode,
                'authorization' => $authorizationCode,
            ]);

            if (!$response['status']) {
                return [
                    'success' => false,
                    'message' => $response['message'],
                ];
            }

            return [
                'success' => true,
                'subscription_code' => $response['data']['subscription_code'],
                'data' => $response['data'],
            ];

        } catch (\Exception $e) {
            Log::error('Paystack subscription creation failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Cancel subscription
     */
    public function disableSubscription(string $subscriptionCode): array
    {
        try {
            $response = $this->makeRequest('POST', "/subscription/disable", [
                'code' => $subscriptionCode,
                'token' => $this->generateDisableToken(),
            ]);

            if (!$response['status']) {
                return [
                    'success' => false,
                    'message' => $response['message'],
                ];
            }

            return [
                'success' => true,
                'message' => 'Subscription disabled successfully',
            ];

        } catch (\Exception $e) {
            Log::error('Paystack subscription disable failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Create plan on Paystack
     */
    public function createPlan(SubscriptionPlan $plan): array
    {
        try {
            $interval = $this->getBillingInterval($plan->billing_cycle_days);
            $amount = (int)($plan->price * 100); // Convert to kobo and ensure integer

            Log::info('Creating Paystack plan:', [
                'plan_name' => $plan->name,
                'amount' => $amount,
                'interval' => $interval
            ]);

            $response = $this->makeRequest('POST', '/plan', [
                'name' => $plan->name . ' - ' . $plan->role,
                'amount' => $amount,
                'interval' => $interval,
                'currency' => $plan->currency ?? 'NGN',
                'description' => $plan->description,
            ]);

            if (!$response['status']) {
                Log::error('Paystack plan creation failed:', [
                    'message' => $response['message'],
                    'plan_id' => $plan->id
                ]);
                return [
                    'success' => false,
                    'message' => $response['message'],
                ];
            }

            // Update plan with Paystack code
            $plan->update([
                'paystack_plan_code' => $response['data']['plan_code'],
            ]);

            Log::info('Paystack plan created successfully:', [
                'plan_code' => $response['data']['plan_code']
            ]);

            return [
                'success' => true,
                'plan_code' => $response['data']['plan_code'],
                'data' => $response['data'],
            ];

        } catch (\Exception $e) {
            Log::error('Paystack plan creation failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Create customer on Paystack
     */
    public function createCustomer(User $user): array
    {
        try {
            $response = $this->makeRequest('POST', '/customer', [
                'email' => $user->email,
                'first_name' => $user->name,
                'metadata' => [
                    'user_id' => $user->id,
                    'role' => $user->getRoleNames()->first(),
                ],
            ]);

            if (!$response['status']) {
                return [
                    'success' => false,
                    'message' => $response['message'],
                ];
            }

            return [
                'success' => true,
                'customer_code' => $response['data']['customer_code'],
                'data' => $response['data'],
            ];

        } catch (\Exception $e) {
            Log::error('Paystack customer creation failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Handle webhook events
     */
    public function handleWebhook(array $payload): array
    {
        try {
            $event = $payload['event'];
            $data = $payload['data'];

            switch ($event) {
                case 'charge.success':
                    return $this->handleSuccessfulCharge($data);

                case 'subscription.create':
                    return $this->handleSubscriptionCreation($data);

                case 'subscription.disable':
                    return $this->handleSubscriptionDisable($data);

                case 'invoice.create':
                    return $this->handleInvoiceCreation($data);

                case 'invoice.payment_failed':
                    return $this->handlePaymentFailed($data);

                default:
                    Log::info("Unhandled Paystack webhook event: {$event}");
                    return ['success' => true, 'message' => 'Event not handled'];
            }

        } catch (\Exception $e) {
            Log::error('Paystack webhook handling failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Handle successful charge webhook
     */
    protected function handleSuccessfulCharge(array $data): array
    {
        $reference = $data['reference'];
        return $this->verifyPayment($reference);
    }

    /**
     * Handle subscription creation webhook
     */
    protected function handleSubscriptionCreation(array $data): array
    {
        // Update subscription status in database
        $subscription = Subscription::where('paystack_subscription_code', $data['subscription_code'])->first();

        if ($subscription) {
            $subscription->update([
                'status' => 'active',
                'paystack_customer_code' => $data['customer']['customer_code'],
            ]);
        }

        return ['success' => true, 'message' => 'Subscription created'];
    }

    /**
     * Handle subscription disable webhook
     */
    protected function handleSubscriptionDisable(array $data): array
    {
        $subscription = Subscription::where('paystack_subscription_code', $data['subscription_code'])->first();

        if ($subscription) {
            $subscription->update([
                'status' => 'canceled',
                'canceled_at' => now(),
            ]);
        }

        return ['success' => true, 'message' => 'Subscription disabled'];
    }

    /**
     * Handle invoice creation webhook
     */
    protected function handleInvoiceCreation(array $data): array
    {
        // Log invoice creation for record keeping
        Log::info('Paystack invoice created', $data);
        return ['success' => true, 'message' => 'Invoice created'];
    }

    /**
     * Handle payment failed webhook
     */
    protected function handlePaymentFailed(array $data): array
    {
        $subscription = Subscription::where('paystack_subscription_code', $data['subscription']['subscription_code'])->first();

        if ($subscription) {
            $subscription->update([
                'status' => 'past_due',
            ]);

            // Notify user about failed payment
            // You can implement notification logic here
        }

        return ['success' => true, 'message' => 'Payment failure handled'];
    }

    /**
     * Verify webhook signature
     */
    public function verifyWebhookSignature($payload, $signature): bool
    {
        $computedSignature = hash_hmac('sha512', $payload, $this->secretKey);
        return hash_equals($computedSignature, $signature);
    }

    /**
     * Generate unique reference
     */
    protected function generateReference(): string
    {
        return 'PSK_' . uniqid() . '_' . time();
    }

    /**
     * Get billing interval based on days
     */
    protected function getBillingInterval(int $billingCycleDays): string
    {
        if ($billingCycleDays >= 365) {
            return 'annually';
        } elseif ($billingCycleDays >= 90) {
            return 'quarterly';
        } elseif ($billingCycleDays >= 30) {
            return 'monthly';
        } else {
            return 'weekly';
        }
    }

    protected function generateDisableToken(): string
    {
        return bin2hex(random_bytes(16));
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    /**
     * Get user's active subscription
     */
    public function getUserSubscription(User $user): ?Subscription
    {
        return $user->currentSubscription;
    }

    /**
     * Check if user can upgrade/downgrade plan
     */
    public function canChangePlan(User $user, SubscriptionPlan $newPlan): bool
    {
        $currentSubscription = $user->currentSubscription;

        if (!$currentSubscription) {
            return true;
        }

        // Allow upgrades anytime, restrict downgrades based on business rules
        $currentPlan = $currentSubscription->plan;

        // Example: Allow upgrade if new plan is more expensive
        return $newPlan->price >= $currentPlan->price;
    }
}
