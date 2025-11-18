<!-- resources/js/Pages/Admin/Subscriptions/Index.vue -->
<template>
    <AdminLayout>
        <Head title="Subscriptions Management" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Subscriptions</h1>
                    <p class="mt-1 text-sm text-gray-600">
                        Manage user subscriptions and billing
                    </p>
                </div>
                <Link
                    :href="route('admin.subscriptions.create')"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    <PlusIcon class="h-4 w-4 mr-2" />
                    New Subscription
                </Link>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Status
                        </label>
                        <select
                            v-model="filters.status"
                            @change="applyFilters"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="">All Status</option>
                            <option
                                v-for="(label, value) in statusOptions"
                                :key="value"
                                :value="value"
                            >
                                {{ label }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Plan
                        </label>
                        <select
                            v-model="filters.plan"
                            @change="applyFilters"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="">All Plans</option>
                            <option
                                v-for="(name, id) in planOptions"
                                :key="id"
                                :value="id"
                            >
                                {{ name }}
                            </option>
                        </select>
                    </div>

                    <div class="flex space-x-4">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                From Date
                            </label>
                            <input
                                type="date"
                                v-model="filters.date_from"
                                @change="applyFilters"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                To Date
                            </label>
                            <input
                                type="date"
                                v-model="filters.date_to"
                                @change="applyFilters"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                        </div>
                    </div>

                    <div class="flex items-end">
                        <button
                            @click="clearFilters"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            Clear Filters
                        </button>
                    </div>
                </div>
            </div>

            <!-- Subscriptions Table -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        All Subscriptions
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    User
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Plan
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Period
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr
                                v-for="subscription in subscriptions.data"
                                :key="subscription.id"
                                class="hover:bg-gray-50"
                            >
                                <td class="px-6 py-4 whitespace-nowrap">
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
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ subscription.plan.name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ subscription.plan.formatted_price }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                        :class="getStatusClass(subscription.status)"
                                    >
                                        {{ getStatusLabel(subscription.status) }}
                                    </span>
                                    <div v-if="subscription.is_on_trial" class="text-xs text-orange-600 mt-1">
                                        Trial ends {{ formatDate(subscription.trial_ends_at) }}
                                    </div>
                                    <div v-else-if="subscription.is_active" class="text-xs text-gray-500 mt-1">
                                        Expires {{ formatDate(subscription.ends_at) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div>Start: {{ formatDate(subscription.starts_at) }}</div>
                                    <div>End: {{ formatDate(subscription.ends_at) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <Link
                                        :href="route('admin.subscriptions.show', subscription.id)"
                                        class="text-indigo-600 hover:text-indigo-900"
                                    >
                                        View
                                    </Link>
                                    <Link
                                        :href="route('admin.subscriptions.edit', subscription.id)"
                                        class="text-blue-600 hover:text-blue-900"
                                    >
                                        Edit
                                    </Link>
                                    <button
                                        v-if="subscription.is_active"
                                        @click="cancelSubscription(subscription)"
                                        class="text-red-600 hover:text-red-900"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        v-if="subscription.is_expired"
                                        @click="renewSubscription(subscription)"
                                        class="text-green-600 hover:text-green-900"
                                    >
                                        Renew
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div v-if="subscriptions.data.length === 0" class="text-center py-12">
                    <CreditCardIcon class="mx-auto h-12 w-12 text-gray-400" />
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No subscriptions</h3>
                    <p class="mt-1 text-sm text-gray-500">No subscriptions found matching your criteria.</p>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <Pagination :links="subscriptions.links" />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import {
    PlusIcon,
    CreditCardIcon,
    UserIcon
} from '@heroicons/vue/24/outline'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Pagination.vue'

const props = defineProps({
    subscriptions: Object,
    filters: Object,
    statusOptions: Object,
    planOptions: Object,
})

const applyFilters = () => {
    router.get(route('admin.subscriptions.index'), props.filters, {
        preserveState: true,
        replace: true,
    })
}

const clearFilters = () => {
    router.get(route('admin.subscriptions.index'), {}, {
        preserveState: true,
        replace: true,
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
    return props.statusOptions[status] || status
}

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    })
}

const cancelSubscription = (subscription) => {
    if (confirm(`Are you sure you want to cancel ${subscription.user.name}'s subscription?`)) {
        router.post(route('admin.subscriptions.cancel', subscription.id))
    }
}

const renewSubscription = (subscription) => {
    if (confirm(`Renew ${subscription.user.name}'s subscription?`)) {
        router.post(route('admin.subscriptions.renew', subscription.id))
    }
}
</script>
