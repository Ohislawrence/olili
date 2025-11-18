<!-- resources/js/Pages/Admin/Subscriptions/Edit.vue -->
<template>
    <AdminLayout>
        <Head :title="`Edit Subscription #${subscription.id}`" />

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
                <h1 class="text-2xl font-bold text-gray-900">
                    Edit Subscription #{{ subscription.id }}
                </h1>
                <p class="mt-1 text-sm text-gray-600">
                    Update subscription details for {{ subscription.user.name }}
                </p>
            </div>

            <!-- Form -->
            <div class="bg-white shadow rounded-lg">
                <form @submit.prevent="submit">
                    <div class="space-y-6 p-6">
                        <!-- User Information (Read-only) -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">User Information</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center">
                                        <UserIcon class="h-6 w-6 text-gray-500" />
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ subscription.user.name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ subscription.user.email }}
                                        </div>
                                    </div>
                                </div>
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
                                        <option
                                            v-for="(label, value) in statusOptions"
                                            :key="value"
                                            :value="value"
                                        >
                                            {{ label }}
                                        </option>
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
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between">
                        <button
                            type="button"
                            @click="confirmDelete"
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                        >
                            Delete
                        </button>
                        <div class="flex space-x-3">
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
                                <span v-if="form.processing">Updating...</span>
                                <span v-else>Update Subscription</span>
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
import { UserIcon } from '@heroicons/vue/24/outline'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    subscription: Object,
    plans: Array,
    statusOptions: Object,
})

const form = useForm({
    subscription_plan_id: props.subscription.subscription_plan_id,
    status: props.subscription.status,
    starts_at: formatDateTimeLocal(props.subscription.starts_at),
    ends_at: formatDateTimeLocal(props.subscription.ends_at),
    trial_ends_at: props.subscription.trial_ends_at ? formatDateTimeLocal(props.subscription.trial_ends_at) : '',
})

const submit = () => {
    form.put(route('admin.subscriptions.update', props.subscription.id))
}

const confirmDelete = () => {
    if (confirm(`Are you sure you want to delete this subscription? This action cannot be undone.`)) {
        router.delete(route('admin.subscriptions.destroy', props.subscription.id))
    }
}

function formatDateTimeLocal(dateString) {
    if (!dateString) return ''
    const date = new Date(dateString)
    return date.toISOString().slice(0, 16)
}
</script>
