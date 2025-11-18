<template>
    <StudentLayout>
        <Head title="Payment History" />

        <div class="min-h-screen bg-gradient-to-br from-slate-50 to-white py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">
                                Payment History
                            </h1>
                            <p class="mt-2 text-gray-600">
                                View your payment transactions and billing history
                            </p>
                        </div>
                        <Link
                            :href="route('payment.pricing')"
                            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 border border-transparent rounded-lg font-semibold text-white shadow-md hover:shadow-lg transition-all duration-200 hover:scale-[1.02]"
                        >
                            <PlusIcon class="h-5 w-5 mr-2" />
                            Upgrade Plan
                        </Link>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                        <div class="flex items-center">
                            <div class="p-3 bg-blue-100 rounded-xl">
                                <CreditCardIcon class="h-6 w-6 text-blue-600" />
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Total Payments</p>
                                <p class="text-2xl font-bold text-gray-900">
                                    {{ stats.total_payments }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                        <div class="flex items-center">
                            <div class="p-3 bg-green-100 rounded-xl">
                                <CheckCircleIcon class="h-6 w-6 text-green-600" />
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Successful</p>
                                <p class="text-2xl font-bold text-gray-900">
                                    {{ stats.successful_payments }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                        <div class="flex items-center">
                            <div class="p-3 bg-red-100 rounded-xl">
                                <XCircleIcon class="h-6 w-6 text-red-600" />
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Failed</p>
                                <p class="text-2xl font-bold text-gray-900">
                                    {{ stats.failed_payments }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                        <div class="flex items-center">
                            <div class="p-3 bg-purple-100 rounded-xl">
                                <CurrencyDollarIcon class="h-6 w-6 text-purple-600" />
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Total Spent</p>
                                <p class="text-2xl font-bold text-gray-900">
                                    NGN {{ stats.total_spent.toLocaleString() }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-8">
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <!-- Search -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Search
                                </label>
                                <div class="relative">
                                    <input
                                        type="text"
                                        v-model="filters.search"
                                        placeholder="Search by reference or description..."
                                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                                        @input="debouncedFilter"
                                    />
                                    <MagnifyingGlassIcon class="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" />
                                </div>
                            </div>

                            <!-- Status Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Status
                                </label>
                                <select
                                    v-model="filters.status"
                                    @change="filterPayments"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                                >
                                    <option value="all">All Status</option>
                                    <option value="success">Successful</option>
                                    <option value="failed">Failed</option>
                                    <option value="pending">Pending</option>
                                </select>
                            </div>

                            <!-- Type Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Type
                                </label>
                                <select
                                    v-model="filters.type"
                                    @change="filterPayments"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                                >
                                    <option value="all">All Types</option>
                                    <option value="subscription">Subscriptions</option>
                                    <option value="one_time">One-time Payments</option>
                                </select>
                            </div>

                            <!-- Reset Filters -->
                            <div class="flex items-end">
                                <button
                                    @click="resetFilters"
                                    class="w-full px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium"
                                >
                                    Reset Filters
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payments Table -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200/70">
                        <h2 class="text-lg font-bold text-gray-900">
                            Transaction History
                        </h2>
                    </div>

                    <!-- Mobile View -->
                    <div class="md:hidden divide-y divide-gray-100">
                        <div
                            v-for="payment in payments.data"
                            :key="payment.id"
                            class="p-4 hover:bg-gray-50 transition-colors"
                        >
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <p class="font-semibold text-gray-900">
                                        {{ payment.description || payment.plan_name || 'Payment' }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ payment.paystack_reference }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-gray-900">
                                        {{ payment.formatted_amount }}
                                    </p>
                                    <PaymentStatusBadge :status="payment.status" />
                                </div>
                            </div>
                            <div class="flex justify-between items-center text-sm text-gray-500">
                                <div>
                                    <span class="capitalize">{{ payment.status }}</span>
                                    <span v-if="payment.paid_at"> • {{ payment.paid_at }}</span>
                                </div>
                                <div>
                                    <span v-if="payment.is_subscription" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Subscription
                                    </span>
                                    <span v-else class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        One-time
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Desktop View -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-200">
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Transaction
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Amount
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Type
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Date
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Reference
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr
                                    v-for="payment in payments.data"
                                    :key="payment.id"
                                    class="hover:bg-gray-50 transition-colors"
                                >
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">
                                                {{ payment.description || payment.plan_name || 'Payment' }}
                                            </p>
                                            <p class="text-sm text-gray-500">
                                                {{ payment.is_subscription ? 'Subscription Plan' : 'One-time Payment' }}
                                            </p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <p class="text-sm font-bold text-gray-900">
                                            {{ payment.formatted_amount }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <PaymentStatusBadge :status="payment.status" />
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            v-if="payment.is_subscription"
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                                        >
                                            Subscription
                                        </span>
                                        <span
                                            v-else
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                                        >
                                            One-time
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ payment.paid_at || payment.created_at }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                                        {{ payment.paystack_reference }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Empty State -->
                    <div
                        v-if="payments.data.length === 0"
                        class="text-center py-12"
                    >
                        <CreditCardIcon class="mx-auto h-12 w-12 text-gray-400" />
                        <h3 class="mt-4 text-lg font-semibold text-gray-900">
                            No payments found
                        </h3>
                        <p class="mt-2 text-gray-500 max-w-md mx-auto">
                            {{ hasFilters ? 'Try adjusting your filters to see more results.' : 'You haven\'t made any payments yet.' }}
                        </p>
                        <div class="mt-6">
                            <Link
                                :href="route('payment.pricing')"
                                class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-lg font-semibold text-white hover:bg-emerald-700 transition-colors"
                            >
                                <PlusIcon class="h-5 w-5 mr-2" />
                                Make Your First Payment
                            </Link>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div
                        v-if="payments.data.length > 0"
                        class="px-6 py-4 border-t border-gray-200"
                    >
                        <Pagination :links="payments.links" />
                    </div>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, computed, watch } from 'vue'
import { debounce } from 'lodash'
import StudentLayout from '@/Layouts/StudentLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import PaymentStatusBadge from '@/Components/Payment/PaymentStatusBadge.vue'
import {
    CreditCardIcon,
    CheckCircleIcon,
    XCircleIcon,
    CurrencyDollarIcon,
    MagnifyingGlassIcon,
    PlusIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
    payments: Object,
    stats: Object,
    filters: Object,
    user_role: String,
})

const filters = ref({
    search: props.filters.search || '',
    status: props.filters.status || 'all',
    type: props.filters.type || 'all',
})

const hasFilters = computed(() => {
    return filters.value.search !== '' ||
           filters.value.status !== 'all' ||
           filters.value.type !== 'all'
})

const filterPayments = () => {
    router.get(route('payment.history'), filters.value, {
        preserveState: true,
        replace: true,
    })
}

const debouncedFilter = debounce(() => {
    filterPayments()
}, 500)

const resetFilters = () => {
    filters.value = {
        search: '',
        status: 'all',
        type: 'all',
    }
    filterPayments()
}

// Watch for filter changes
watch(filters, () => {
    if (hasFilters.value) {
        debouncedFilter()
    }
}, { deep: true })
</script>
