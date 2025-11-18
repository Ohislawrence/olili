<?php
// app/Http/Controllers/Admin/SubscriptionPlanController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubscriptionPlanController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::withCount('subscriptions')
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('Admin/SubscriptionPlans/Index', [
            'plans' => $plans,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/SubscriptionPlans/Create', [
            'featureOptions' => $this->getFeatureOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:subscription_plans,name',
            'code' => 'required|string|max:255|unique:subscription_plans,code',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'billing_cycle_days' => 'required|integer|min:1',
            'features' => 'required|array',
            'max_courses' => 'required|integer|min:-1',
            'max_ai_requests_per_month' => 'required|integer|min:-1',
            'ai_grading' => 'boolean',
            'priority_support' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'required|integer|min:0',
        ]);

        SubscriptionPlan::create($request->all());

        return redirect()->route('admin.subscription-plans.index')
            ->with('success', 'Subscription plan created successfully.');
    }

    public function show(SubscriptionPlan $subscriptionPlan)
    {
        $subscriptionPlan->load(['subscriptions.user']);

        $recentSubscriptions = $subscriptionPlan->subscriptions()
            ->with('user')
            ->latest()
            ->limit(10)
            ->get();

        return Inertia::render('Admin/SubscriptionPlans/Show', [
            'plan' => $subscriptionPlan,
            'recent_subscriptions' => $recentSubscriptions,
        ]);
    }

    public function edit(SubscriptionPlan $subscriptionPlan)
    {
        return Inertia::render('Admin/SubscriptionPlans/Edit', [
            'plan' => $subscriptionPlan,
            'featureOptions' => $this->getFeatureOptions(),
        ]);
    }

    public function update(Request $request, SubscriptionPlan $subscriptionPlan)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:subscription_plans,name,' . $subscriptionPlan->id,
            'code' => 'required|string|max:255|unique:subscription_plans,code,' . $subscriptionPlan->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'billing_cycle_days' => 'required|integer|min:1',
            'features' => 'required|array',
            'max_courses' => 'required|integer|min:-1',
            'max_ai_requests_per_month' => 'required|integer|min:-1',
            'ai_grading' => 'boolean',
            'priority_support' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'required|integer|min:0',
        ]);

        $subscriptionPlan->update($request->all());

        return redirect()->route('admin.subscription-plans.index')
            ->with('success', 'Subscription plan updated successfully.');
    }

    public function destroy(SubscriptionPlan $subscriptionPlan)
    {
        if ($subscriptionPlan->subscriptions()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete plan that has active subscriptions.');
        }

        $subscriptionPlan->delete();

        return redirect()->route('admin.subscription-plans.index')
            ->with('success', 'Subscription plan deleted successfully.');
    }

    public function toggleActive(SubscriptionPlan $subscriptionPlan)
    {
        $subscriptionPlan->update([
            'is_active' => !$subscriptionPlan->is_active,
        ]);

        $status = $subscriptionPlan->is_active ? 'activated' : 'deactivated';

        return redirect()->back()->with('success', "Subscription plan {$status} successfully.");
    }

    private function getFeatureOptions()
    {
        return [
            'unlimited_courses' => 'Unlimited Courses',
            'ai_grading' => 'AI Grading',
            'priority_support' => 'Priority Support',
            'advanced_analytics' => 'Advanced Analytics',
            'custom_domains' => 'Custom Domains',
            'api_access' => 'API Access',
            'white_label' => 'White Label',
            'team_members' => 'Team Members',
            'backup_restore' => 'Backup & Restore',
            'premium_templates' => 'Premium Templates',
        ];
    }
}
