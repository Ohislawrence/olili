<!-- resources/js/Pages/Admin/Subscriptions/Show.vue -->
<template>
    <AdminLayout>
        <Head :title="`Subscription #${subscription.id}`" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex justify-between items-start">
                <div>
                    <div class="flex items-center space-x-2 mb-2">
                        <Link
                            :href="route('admin.subscriptions.index')"
                            class="text-indigo-600 hover:text-indigo-900 text-sm font-medium"
                        >
                            ← Back to Subscriptions
                        </Link>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="h-12 w-12 bg-indigo-100 rounded-full flex items-center justify-center">
                            <CreditCardIcon class="h-6 w-6 text-indigo-600" />
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">
                                Subscription #{{ subscription.id }}
                            </h1>
                            <div class="flex items-center space-x-4 mt-1">
                                <span class="text-sm text-gray-600">User: {{ subscription.user.name }}</span>
                                <span class="text-sm text-gray-600">Plan: {{ subscription.plan.name }}</span>
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                    :class="getStatusClass(subscription.status)"
                                >
                                    {{ getStatusLabel(subscription.status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <Link
                        :href="route('admin.subscriptions.edit', subscription.id)"
                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        Edit
                    </Link>
                    <button
                        v-if="subscription.is_active"
                        @click="cancelSubscription"
                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        Cancel
                    </button>
                    <button
                        v-if="subscription.is_expired"
                        @click="renewSubscription"
                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        Renew
                    </button>
                </div>
            </div>

            <!-- Main Content -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- User Information -->
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">User Information</h3>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-16 w-16 bg-gray-200 rounded-full flex items-center justify-center">
                                    <UserIcon class="h-8 w-8 text-gray-500" />
                                </div>
                                <div class="ml-4">
                                    <div class="text-lg font-medium text-gray-900">
                                        {{ subscription.user.name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ subscription.user.email }}
                                    </div>
                                    <div class="text-sm text-gray-500 mt-1">
                                        Member since {{ formatDate(subscription.user.created_at) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Plan Information -->
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Plan Information</h3>
                        </div>
                        <div class="p-6">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900">
                                        {{ subscription.plan.name }}
                                    </h4>
                                    <p class="text-sm text-gray-600 mt-1">
                                        {{ subscription.plan.description }}
                                    </p>
                                    <div class="mt-3 space-y-1">
                                        <div
                                            v-for="feature in subscription.plan.features"
                                            :key="feature"
                                            class="flex items-center text-sm text-gray-600"
                                        >
                                            <CheckIcon class="h-4 w-4 text-green-500 mr-2" />
                                            {{ formatFeatureName(feature) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-bold text-gray-900">
                                        {{ subscription.plan.formatted_price }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        per {{ subscription.plan.billing_cycle_days }} days
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Subscription Details -->
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Subscription Details</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Status</span>
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                    :class="getStatusClass(subscription.status)"
                                >
                                    {{ getStatusLabel(subscription.status) }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Start Date</span>
                                <span class="text-sm text-gray-900">{{ formatDate(subscription.starts_at) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">End Date</span>
                                <span class="text-sm text-gray-900">{{ formatDate(subscription.ends_at) }}</span>
                            </div>
                            <div v-if="subscription.trial_ends_at" class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Trial Ends</span>
                                <span class="text-sm text-gray-900">{{ formatDate(subscription.trial_ends_at) }}</span>
                            </div>
                            <div v-if="subscription.canceled_at" class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Cancelled At</span>
                                <span class="text-sm text-gray-900">{{ formatDate(subscription.canceled_at) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Created</span>
                                <span class="text-sm text-gray-900">{{ formatDate(subscription.created_at) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Updated</span>
                                <span class="text-sm text-gray-900">{{ formatDate(subscription.updated_at) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Usage Statistics -->
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Usage Statistics</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Courses Created</span>
                                <span class="text-sm text-gray-900">
                                    {{ usage_stats.courses_created }} /
                                    {{ usage_stats.courses_limit === -1 ? '∞' : usage_stats.courses_limit }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">AI Requests This Month</span>
                                <span class="text-sm text-gray-900">
                                    {{ usage_stats.ai_requests_this_month }} /
                                    {{ usage_stats.ai_requests_limit === -1 ? '∞' : usage_stats.ai_requests_limit }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Days Until Expiration</span>
                                <span class="text-sm text-gray-900">
                                    {{ usage_stats.days_until_expiration }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Information -->
                    <div v-if="subscription.paystack_subscription_code" class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Payment Information</h3>
                        </div>
                        <div class="p-6 space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Paystack Code</span>
                                <span class="text-sm text-gray-900 font-mono">
                                    {{ subscription.paystack_subscription_code }}
                                </span>
                            </div>
                            <div v-if="subscription.card_type" class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Card Type</span>
                                <span class="text-sm text-gray-900">{{ subscription.card_type }}</span>
                            </div>
                            <div v-if="subscription.last4" class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Last 4 Digits</span>
                                <span class="text-sm text-gray-900">**** {{ subscription.last4 }}</span>
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
    UserIcon,
    CheckIcon
} from '@heroicons/vue/24/outline'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    subscription: Object,
    usage_stats: Object,
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
        hour: '2-digit',
        minute: '2-digit',
    })
}

const getStatusClass = (status) => {
    const classes = {
        'active': 'bg-green-100 text-green-800',
        'canceled': 'bg-red-100 text-red-800',
        'expired': 'bg-gray-100 text-gray-800',
    }
    return classes[status] || 'bg-gray-100 text-gray-800'
}

const getStatusLabel = (status) => {
    const labels = {
        'active': 'Active',
        'canceled': 'Cancelled',
        'expired': 'Expired',
    }
    return labels[status] || status
}

const cancelSubscription = () => {
    if (confirm(`Are you sure you want to cancel this subscription?`)) {
        router.post(route('admin.subscriptions.cancel', props.subscription.id))
    }
}

const renewSubscription = () => {
    if (confirm(`Renew this subscription?`)) {
        router.post(route('admin.subscriptions.renew', props.subscription.id))
    }
}
</script>
