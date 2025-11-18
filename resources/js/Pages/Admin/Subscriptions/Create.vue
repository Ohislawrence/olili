<!-- resources/js/Pages/Admin/Subscriptions/Create.vue -->
<template>
    <AdminLayout>
        <Head title="Create Subscription" />

        <div class="max-w-2xl mx-auto py-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center space-x-2 mb-2">
                    <Link
                        :href="route('admin.subscriptions.index')"
                        class="text-indigo-600 hover:text-indigo-900 text-sm font-medium"
                    >
                        ← Back to Subscriptions
                    </Link>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">Create Subscription</h1>
                <p class="mt-1 text-sm text-gray-600">
                    Create a new subscription for a user
                </p>
            </div>

            <!-- Form -->
            <div class="bg-white shadow rounded-lg">
                <form @submit.prevent="submit">
                    <div class="space-y-6 p-6">
                        <!-- User Selection -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">User Information</h3>
                            <div>
                                <label for="user_id" class="block text-sm font-medium text-gray-700">
                                    Select User *
                                </label>
                                <select
                                    id="user_id"
                                    v-model="form.user_id"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :class="{ 'border-red-300': form.errors.user_id }"
                                >
                                    <option value="">Select a user</option>
                                    <option
                                        v-for="user in users"
                                        :key="user.id"
                                        :value="user.id"
                                    >
                                        {{ user.name }} ({{ user.email }})
                                    </option>
                                </select>
                                <p v-if="form.errors.user_id" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.user_id }}
                                </p>
                            </div>
                        </div>

                        <!-- Plan Selection -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Plan Information</h3>
                            <div>
                                <label for="subscription_plan_id" class="block text-sm font-medium text-gray-700">
                                    Select Plan *
                                </label>
                                <select
                                    id="subscription_plan_id"
                                    v-model="form.subscription_plan_id"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :class="{ 'border-red-300': form.errors.subscription_plan_id }"
                                >
                                    <option value="">Select a plan</option>
                                    <option
                                        v-for="plan in plans"
                                        :key="plan.id"
                                        :value="plan.id"
                                    >
                                        {{ plan.name }} - {{ plan.formatted_price }}
                                    </option>
                                </select>
                                <p v-if="form.errors.subscription_plan_id" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.subscription_plan_id }}
                                </p>
                            </div>
                        </div>

                        <!-- Subscription Details -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Subscription Details</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Status -->
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700">
                                        Status *
                                    </label>
                                    <select
                                        id="status"
                                        v-model="form.status"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        :class="{ 'border-red-300': form.errors.status }"
                                    >
                                        <option value="active">Active</option>
                                        <option value="canceled">Cancelled</option>
                                        <option value="expired">Expired</option>
                                    </select>
                                    <p v-if="form.errors.status" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.status }}
                                    </p>
                                </div>

                                <!-- Start Date -->
                                <div>
                                    <label for="starts_at" class="block text-sm font-medium text-gray-700">
                                        Start Date *
                                    </label>
                                    <input
                                        type="datetime-local"
                                        id="starts_at"
                                        v-model="form.starts_at"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        :class="{ 'border-red-300': form.errors.starts_at }"
                                    />
                                    <p v-if="form.errors.starts_at" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.starts_at }}
                                    </p>
                                </div>

                                <!-- End Date -->
                                <div>
                                    <label for="ends_at" class="block text-sm font-medium text-gray-700">
                                        End Date *
                                    </label>
                                    <input
                                        type="datetime-local"
                                        id="ends_at"
                                        v-model="form.ends_at"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        :class="{ 'border-red-300': form.errors.ends_at }"
                                    />
                                    <p v-if="form.errors.ends_at" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.ends_at }}
                                    </p>
                                </div>

                                <!-- Trial End Date -->
                                <div>
                                    <label for="trial_ends_at" class="block text-sm font-medium text-gray-700">
                                        Trial End Date
                                    </label>
                                    <input
                                        type="datetime-local"
                                        id="trial_ends_at"
                                        v-model="form.trial_ends_at"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        :class="{ 'border-red-300': form.errors.trial_ends_at }"
                                    />
                                    <p v-if="form.errors.trial_ends_at" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.trial_ends_at }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
                        <Link
                            :href="route('admin.subscriptions.index')"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                        >
                            <span v-if="form.processing">Creating...</span>
                            <span v-else>Create Subscription</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    users: Array,
    plans: Array,
})

const form = useForm({
    user_id: '',
    subscription_plan_id: '',
    status: 'active',
    starts_at: new Date().toISOString().slice(0, 16),
    ends_at: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().slice(0, 16), // 30 days from now
    trial_ends_at: '',
})

const submit = () => {
    form.post(route('admin.subscriptions.store'))
}
</script>
