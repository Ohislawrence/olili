<!-- resources/js/Pages/Admin/SubscriptionPlans/Show.vue -->
<template>
    <AdminLayout>
        <Head :title="plan.name" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex justify-between items-start">
                <div>
                    <div class="flex items-center space-x-2 mb-2">
                        <Link
                            :href="route('admin.subscription-plans.index')"
                            class="text-indigo-600 hover:text-indigo-900 text-sm font-medium"
                        >
                            ← Back to Plans
                        </Link>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="h-12 w-12 bg-indigo-100 rounded-full flex items-center justify-center">
                            <CreditCardIcon class="h-6 w-6 text-indigo-600" />
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ plan.name }}</h1>
                            <div class="flex items-center space-x-4 mt-1">
                                <span class="text-sm text-gray-600">Code: {{ plan.code }}</span>
                                <span class="text-sm text-gray-600">Price: {{ plan.formatted_price }}</span>
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                    :class="plan.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                >
                                    {{ plan.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <Link
                        :href="route('admin.subscription-plans.edit', plan.id)"
                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        Edit
                    </Link>
                    <button
                        @click="toggleActive"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        {{ plan.is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                </div>
            </div>

            <!-- Description -->
            <div v-if="plan.description" class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-sm text-blue-800">{{ plan.description }}</p>
            </div>

            <!-- Main Content -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Features -->
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Plan Features</h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div
                                    v-for="feature in plan.features"
                                    :key="feature"
                                    class="flex items-center text-sm text-gray-600"
                                >
                                    <CheckIcon class="h-4 w-4 text-green-500 mr-2" />
                                    {{ formatFeatureName(feature) }}
                                </div>
                            </div>
                            <p v-if="plan.features.length === 0" class="text-sm text-gray-500 italic">
                                No features configured for this plan.
                            </p>
                        </div>
                    </div>

                    <!-- Recent Subscriptions -->
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Recent Subscriptions</h3>
                        </div>
                        <div class="divide-y divide-gray-200">
                            <div
                                v-for="subscription in recent_subscriptions"
                                :key="subscription.id"
                                class="px-6 py-4"
                            >
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-sm font-semibold text-gray-900">
                                            {{ subscription.user.name }}
                                        </h4>
                                        <p class="text-sm text-gray-500">
                                            {{ subscription.user.email }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            Status:
                                            <span :class="getStatusClass(subscription.status)">
                                                {{ getStatusLabel(subscription.status) }}
                                            </span>
                                        </p>
                                    </div>
                                    <div class="text-right text-sm text-gray-500">
                                        <div>Start: {{ formatDate(subscription.starts_at) }}</div>
                                        <div>End: {{ formatDate(subscription.ends_at) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-if="recent_subscriptions.length === 0" class="px-6 py-8 text-center">
                            <UserGroupIcon class="mx-auto h-8 w-8 text-gray-400" />
                            <p class="mt-2 text-sm text-gray-500">No subscriptions for this plan.</p>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Plan Details -->
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Plan Details</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Price</span>
                                <span class="text-sm text-gray-900">{{ plan.formatted_price }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Billing Cycle</span>
                                <span class="text-sm text-gray-900">{{ plan.billing_cycle_days }} days</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Currency</span>
                                <span class="text-sm text-gray-900">{{ plan.currency }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Sort Order</span>
                                <span class="text-sm text-gray-900">{{ plan.sort_order }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Created</span>
                                <span class="text-sm text-gray-900">{{ formatDate(plan.created_at) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Updated</span>
                                <span class="text-sm text-gray-900">{{ formatDate(plan.updated_at) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Limits -->
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Usage Limits</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Max Courses</span>
                                <span class="text-sm text-gray-900">
                                    {{ plan.max_courses === -1 ? 'Unlimited' : plan.max_courses }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">AI Requests/Month</span>
                                <span class="text-sm text-gray-900">
                                    {{ plan.max_ai_requests_per_month === -1 ? 'Unlimited' : plan.max_ai_requests_per_month.toLocaleString() }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">AI Grading</span>
                                <span class="text-sm text-gray-900">
                                    {{ plan.ai_grading ? 'Enabled' : 'Disabled' }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Priority Support</span>
                                <span class="text-sm text-gray-900">
                                    {{ plan.priority_support ? 'Yes' : 'No' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Statistics</h3>
                        </div>
                        <div class="p-6">
                            <div class="text-center">
                                <div class="text-3xl font-bold text-gray-900">{{ plan.subscriptions_count }}</div>
                                <div class="text-sm text-gray-600">Active Subscriptions</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import {
    CreditCardIcon,
    CheckIcon,
    UserGroupIcon
} from '@heroicons/vue/24/outline'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    plan: Object,
    recent_subscriptions: Array,
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

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    })
}

const getStatusClass = (status) => {
    const classes = {
        'active': 'text-green-600',
        'canceled': 'text-red-600',
        'expired': 'text-gray-600',
    }
    return classes[status] || 'text-gray-600'
}

const getStatusLabel = (status) => {
    const labels = {
        'active': 'Active',
        'canceled': 'Cancelled',
        'expired': 'Expired',
    }
    return labels[status] || status
}

const toggleActive = () => {
    if (confirm(`Are you sure you want to ${props.plan.is_active ? 'deactivate' : 'activate'} this plan?`)) {
        router.post(route('admin.subscription-plans.toggle-active', props.plan.id))
    }
}
</script>
