<!-- resources/js/Pages/Admin/SubscriptionPlans/Edit.vue -->
<template>
    <AdminLayout>
        <Head :title="`Edit ${plan.name}`" />

        <div class="max-w-4xl mx-auto py-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center space-x-2 mb-2">
                    <Link
                        :href="route('admin.subscription-plans.index')"
                        class="text-indigo-600 hover:text-indigo-900 text-sm font-medium"
                    >
                        ← Back to Plans
                    </Link>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">Edit {{ plan.name }}</h1>
                <p class="mt-1 text-sm text-gray-600">
                    Update subscription plan details and features
                </p>
            </div>

            <!-- Form -->
            <div class="bg-white shadow rounded-lg">
                <form @submit.prevent="submit">
                    <div class="space-y-6 p-6">
                        <!-- Basic Information -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Name -->
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">
                                        Plan Name *
                                    </label>
                                    <input
                                        type="text"
                                        id="name"
                                        v-model="form.name"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        :class="{ 'border-red-300': form.errors.name }"
                                    />
                                    <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.name }}
                                    </p>
                                </div>

                                <!-- Code -->
                                <div>
                                    <label for="code" class="block text-sm font-medium text-gray-700">
                                        Plan Code *
                                    </label>
                                    <input
                                        type="text"
                                        id="code"
                                        v-model="form.code"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        :class="{ 'border-red-300': form.errors.code }"
                                    />
                                    <p v-if="form.errors.code" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.code }}
                                    </p>
                                </div>

                                <!-- Price -->
                                <div>
                                    <label for="price" class="block text-sm font-medium text-gray-700">
                                        Price *
                                    </label>
                                    <input
                                        type="number"
                                        id="price"
                                        v-model="form.price"
                                        min="0"
                                        step="0.01"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        :class="{ 'border-red-300': form.errors.price }"
                                    />
                                    <p v-if="form.errors.price" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.price }}
                                    </p>
                                </div>

                                <!-- Currency -->
                                <div>
                                    <label for="currency" class="block text-sm font-medium text-gray-700">
                                        Currency *
                                    </label>
                                    <select
                                        id="currency"
                                        v-model="form.currency"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        :class="{ 'border-red-300': form.errors.currency }"
                                    >
                                        <option value="USD">USD ($)</option>
                                        <option value="EUR">EUR (€)</option>
                                        <option value="GBP">GBP (£)</option>
                                        <option value="NGN">NGN (₦)</option>
                                    </select>
                                    <p v-if="form.errors.currency" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.currency }}
                                    </p>
                                </div>

                                <!-- Billing Cycle -->
                                <div>
                                    <label for="billing_cycle_days" class="block text-sm font-medium text-gray-700">
                                        Billing Cycle (Days) *
                                    </label>
                                    <input
                                        type="number"
                                        id="billing_cycle_days"
                                        v-model="form.billing_cycle_days"
                                        min="1"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        :class="{ 'border-red-300': form.errors.billing_cycle_days }"
                                    />
                                    <p v-if="form.errors.billing_cycle_days" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.billing_cycle_days }}
                                    </p>
                                </div>

                                <!-- Sort Order -->
                                <div>
                                    <label for="sort_order" class="block text-sm font-medium text-gray-700">
                                        Sort Order *
                                    </label>
                                    <input
                                        type="number"
                                        id="sort_order"
                                        v-model="form.sort_order"
                                        min="0"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        :class="{ 'border-red-300': form.errors.sort_order }"
                                    />
                                    <p v-if="form.errors.sort_order" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.sort_order }}
                                    </p>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="mt-6">
                                <label for="description" class="block text-sm font-medium text-gray-700">
                                    Description
                                </label>
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    rows="3"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :class="{ 'border-red-300': form.errors.description }"
                                />
                                <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.description }}
                                </p>
                            </div>
                        </div>

                        <!-- Features -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Features</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div
                                    v-for="(label, value) in featureOptions"
                                    :key="value"
                                    class="flex items-center"
                                >
                                    <input
                                        type="checkbox"
                                        :id="`feature-${value}`"
                                        :value="value"
                                        v-model="form.features"
                                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                    />
                                    <label :for="`feature-${value}`" class="ml-2 block text-sm text-gray-900">
                                        {{ label }}
                                    </label>
                                </div>
                            </div>
                            <p v-if="form.errors.features" class="mt-2 text-sm text-red-600">
                                {{ form.errors.features }}
                            </p>
                        </div>

                        <!-- Limits -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Limits</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Max Courses -->
                                <div>
                                    <label for="max_courses" class="block text-sm font-medium text-gray-700">
                                        Maximum Courses *
                                    </label>
                                    <input
                                        type="number"
                                        id="max_courses"
                                        v-model="form.max_courses"
                                        min="-1"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        :class="{ 'border-red-300': form.errors.max_courses }"
                                    />
                                    <p v-if="form.errors.max_courses" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.max_courses }}
                                    </p>
                                </div>

                                <!-- Max AI Requests -->
                                <div>
                                    <label for="max_ai_requests_per_month" class="block text-sm font-medium text-gray-700">
                                        Maximum AI Requests/Month *
                                    </label>
                                    <input
                                        type="number"
                                        id="max_ai_requests_per_month"
                                        v-model="form.max_ai_requests_per_month"
                                        min="-1"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        :class="{ 'border-red-300': form.errors.max_ai_requests_per_month }"
                                    />
                                    <p v-if="form.errors.max_ai_requests_per_month" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.max_ai_requests_per_month }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Options -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Options</h3>
                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <input
                                        type="checkbox"
                                        id="ai_grading"
                                        v-model="form.ai_grading"
                                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                    />
                                    <label for="ai_grading" class="ml-2 block text-sm text-gray-900">
                                        Enable AI Grading
                                    </label>
                                </div>
                                <div class="flex items-center">
                                    <input
                                        type="checkbox"
                                        id="priority_support"
                                        v-model="form.priority_support"
                                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                    />
                                    <label for="priority_support" class="ml-2 block text-sm text-gray-900">
                                        Priority Support
                                    </label>
                                </div>
                                <div class="flex items-center">
                                    <input
                                        type="checkbox"
                                        id="is_active"
                                        v-model="form.is_active"
                                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                    />
                                    <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                        Active Plan
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between">
                        <button
                            type="button"
                            @click="confirmDelete"
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                            :disabled="plan.subscriptions_count > 0"
                        >
                            Delete
                        </button>
                        <div class="flex space-x-3">
                            <Link
                                :href="route('admin.subscription-plans.index')"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                            >
                                <span v-if="form.processing">Updating...</span>
                                <span v-else>Update Plan</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    plan: Object,
    featureOptions: Object,
})

const form = useForm({
    name: props.plan.name,
    code: props.plan.code,
    description: props.plan.description,
    price: props.plan.price,
    currency: props.plan.currency,
    billing_cycle_days: props.plan.billing_cycle_days,
    features: props.plan.features || [],
    max_courses: props.plan.max_courses,
    max_ai_requests_per_month: props.plan.max_ai_requests_per_month,
    ai_grading: props.plan.ai_grading,
    priority_support: props.plan.priority_support,
    is_active: props.plan.is_active,
    sort_order: props.plan.sort_order,
})

const submit = () => {
    form.put(route('admin.subscription-plans.update', props.plan.id))
}

const confirmDelete = () => {
    if (props.plan.subscriptions_count > 0) {
        alert('Cannot delete plan that has active subscriptions.')
        return
    }

    if (confirm(`Are you sure you want to delete "${props.plan.name}"? This action cannot be undone.`)) {
        router.delete(route('admin.subscription-plans.destroy', props.plan.id))
    }
}
</script>
