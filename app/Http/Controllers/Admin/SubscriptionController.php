<?php
// app/Http/Controllers/Admin/SubscriptionController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\User;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $query = Subscription::with(['user', 'plan'])
            ->latest();

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by plan
        if ($request->has('plan') && $request->plan) {
            $query->where('subscription_plan_id', $request->plan);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $subscriptions = $query->paginate(25);

        return Inertia::render('Admin/Subscriptions/Index', [
            'subscriptions' => $subscriptions,
            'filters' => $request->only(['status', 'plan', 'date_from', 'date_to']),
            'statusOptions' => $this->getStatusOptions(),
            'planOptions' => SubscriptionPlan::active()->get()->pluck('name', 'id'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Subscriptions/Create', [
            'users' => User::active()->get(['id', 'name', 'email']),
            'plans' => SubscriptionPlan::active()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
            'status' => 'required|in:active,canceled,expired',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'trial_ends_at' => 'nullable|date|after:starts_at',
        ]);

        Subscription::create($request->all());

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Subscription created successfully.');
    }

    public function show(Subscription $subscription)
    {
        $subscription->load(['user', 'plan']);

        $usageStats = $this->getUsageStats($subscription);

        return Inertia::render('Admin/Subscriptions/Show', [
            'subscription' => $subscription,
            'usage_stats' => $usageStats,
        ]);
    }

    public function edit(Subscription $subscription)
    {
        $subscription->load(['user', 'plan']);

        return Inertia::render('Admin/Subscriptions/Edit', [
            'subscription' => $subscription,
            'plans' => SubscriptionPlan::active()->get(),
            'statusOptions' => $this->getStatusOptions(),
        ]);
    }

    public function update(Request $request, Subscription $subscription)
    {
        $request->validate([
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
            'status' => 'required|in:active,canceled,expired',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'trial_ends_at' => 'nullable|date|after:starts_at',
        ]);

        $subscription->update($request->all());

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Subscription updated successfully.');
    }

    public function destroy(Subscription $subscription)
    {
        $subscription->delete();

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Subscription deleted successfully.');
    }

    public function cancel(Subscription $subscription)
    {
        $subscription->cancel();

        return redirect()->back()->with('success', 'Subscription cancelled successfully.');
    }

    public function renew(Subscription $subscription)
    {
        $subscription->renew();

        return redirect()->back()->with('success', 'Subscription renewed successfully.');
    }

    private function getStatusOptions()
    {
        return [
            'active' => 'Active',
            'canceled' => 'Cancelled',
            'expired' => 'Expired',
        ];
    }

    private function getUsageStats(Subscription $subscription)
    {
        $currentMonth = now()->startOfMonth();

        return [
            'courses_created' => $subscription->user->courses()->count(),
            'courses_limit' => $subscription->plan->max_courses,
            'ai_requests_this_month' => $subscription->user->aiUsageLogs()
                ->where('created_at', '>=', $currentMonth)
                ->count(),
            'ai_requests_limit' => $subscription->plan->max_ai_requests_per_month,
            'days_until_expiration' => $subscription->daysUntilExpiration(),
        ];
    }
}
