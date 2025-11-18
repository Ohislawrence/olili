<!-- resources/js/Pages/Admin/SubscriptionPlans/Index.vue -->
<template>
    <AdminLayout>
        <Head title="Subscription Plans" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Subscription Plans</h1>
                    <p class="mt-1 text-sm text-gray-600">
                        Manage subscription plans and pricing
                    </p>
                </div>
                <Link
                    :href="route('admin.subscription-plans.create')"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    <PlusIcon class="h-4 w-4 mr-2" />
                    New Plan
                </Link>
            </div>

            <!-- Plans Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                    v-for="plan in plans"
                    :key="plan.id"
                    class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden"
                >
                    <!-- Plan Header -->
                    <div
                        class="px-6 py-4 border-b"
                        :class="plan.is_active ? 'bg-gradient-to-r from-indigo-500 to-purple-600 text-white' : 'bg-gray-100 text-gray-600'"
                    >
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-lg font-semibold">{{ plan.name }}</h3>
                                <p class="text-sm opacity-90">{{ plan.description }}</p>
                            </div>
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                :class="plan.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                            >
                                {{ plan.is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>

                    <!-- Plan Price -->
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="text-3xl font-bold text-gray-900">
                            {{ plan.formatted_price }}
                        </div>
                        <p class="text-sm text-gray-600">
                            per {{ plan.billing_cycle_days }} days
                        </p>
                    </div>

                    <!-- Plan Features -->
                    <div class="p-6">
                        <ul class="space-y-2">
                            <li
                                v-for="feature in plan.features"
                                :key="feature"
                                class="flex items-center text-sm text-gray-600"
                            >
                                <CheckIcon class="h-4 w-4 text-green-500 mr-2" />
                                {{ formatFeatureName(feature) }}
                            </li>
                        </ul>

                        <!-- Limits -->
                        <div class="mt-4 space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Max Courses:</span>
                                <span class="font-medium">
                                    {{ plan.max_courses === -1 ? 'Unlimited' : plan.max_courses }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">AI Requests/Month:</span>
                                <span class="font-medium">
                                    {{ plan.max_ai_requests_per_month === -1 ? 'Unlimited' : plan.max_ai_requests_per_month.toLocaleString() }}
                                </span>
                            </div>
                        </div>

                        <!-- Stats -->
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Active Subscriptions:</span>
                                <span class="font-medium">{{ plan.subscriptions_count }}</span>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="mt-6 flex space-x-2">
                            <Link
                                :href="route('admin.subscription-plans.show', plan.id)"
                                class="flex-1 text-center px-3 py-2 text-sm font-medium text-indigo-600 hover:text-indigo-900"
                            >
                                View
                            </Link>
                            <Link
                                :href="route('admin.subscription-plans.edit', plan.id)"
                                class="flex-1 text-center px-3 py-2 text-sm font-medium text-blue-600 hover:text-blue-900"
                            >
                                Edit
                            </Link>
                            <button
                                @click="toggleActive(plan)"
                                class="flex-1 text-center px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900"
                            >
                                {{ plan.is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="plans.length === 0" class="text-center py-12">
                <CreditCardIcon class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-medium text-gray-900">No subscription plans</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating your first subscription plan.</p>
                <div class="mt-6">
                    <Link
                        :href="route('admin.subscription-plans.create')"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        <PlusIcon class="h-4 w-4 mr-2" />
                        New Plan
                    </Link>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { CreditCardIcon, PlusIcon, CheckIcon } from '@heroicons/vue/24/outline'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    plans: Array
})

const formatFeatureName = (feature) => {
    const featureNames = {
        'unlimited_courses': 'Unlimited Courses',
        'ai_grading': 'AI Grading',
        'priority_support': 'Priority Support',
        'advanced_analytics': 'Advanced Analytics',
        'custom_domains': 'Custom Domains',
        'api_access': 'API Access',
        'white_label': 'White Label',
        'team_members': 'Team Members',
        'backup_restore': 'Backup & Restore',
        'premium_templates': 'Premium Templates',
    }
    return featureNames[feature] || feature.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())
}

const toggleActive = (plan) => {
    if (confirm(`Are you sure you want to ${plan.is_active ? 'deactivate' : 'activate'} this plan?`)) {
        router.post(route('admin.subscription-plans.toggle-active', plan.id))
    }
}
</script>
