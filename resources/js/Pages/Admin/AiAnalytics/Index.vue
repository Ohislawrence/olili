<!-- resources/js/Pages/Admin/AiAnalytics/Index.vue -->
<template>
    <AdminLayout>
        <Head title="AI Analytics Dashboard" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">AI Analytics Dashboard</h1>
                    <p class="mt-1 text-sm text-gray-600">
                        Monitor AI usage, costs, and performance metrics
                    </p>
                </div>
                <div class="flex space-x-3">
                    <button
                        @click="refreshData"
                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                    >
                        <ArrowPathIcon class="h-4 w-4 mr-2" />
                        Refresh
                    </button>
                    <button
                        @click="exportData"
                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        <ArrowDownTrayIcon class="h-4 w-4 mr-2" />
                        Export CSV
                    </button>
                </div>
            </div>

            <!-- Loading State -->
            <div v-if="loading" class="flex justify-center items-center py-12">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
            </div>

            <!-- Content -->
            <div v-else>
                <!-- Summary Stats -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <SummaryCard
                        title="Total Cost"
                        :value="formatCurrency(summary?.total_cost || 0)"
                        subtitle="All time"
                        trend="+12%"
                        :trend-positive="false"
                        icon="CurrencyDollarIcon"
                    />
                    <SummaryCard
                        title="Total Requests"
                        :value="formatNumber(summary?.total_requests || 0)"
                        :subtitle="`${successRate}% success rate`"
                        :trend="`${failedRequests} failed`"
                        :trend-positive="failedRequests === 0"
                        icon="ChatBubbleLeftRightIcon"
                    />
                    <SummaryCard
                        title="Total Tokens"
                        :value="formatNumber(summary?.total_tokens || 0)"
                        subtitle="Prompt + Completion"
                        trend="+8%"
                        :trend-positive="true"
                        icon="CpuChipIcon"
                    />
                    <SummaryCard
                        title="Success Rate"
                        :value="successRate + '%'"
                        subtitle="Request success rate"
                        :trend="`${successfulRequests} successful`"
                        :trend-positive="true"
                        icon="CheckBadgeIcon"
                    />
                </div>

                <!-- Filters -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Filters</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    AI Provider
                                </label>
                                <select
                                    v-model="filters.provider"
                                    @change="applyFilters"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">All Providers</option>
                                    <option
                                        v-for="(name, code) in providers"
                                        :key="code"
                                        :value="code"
                                    >
                                        {{ name }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Purpose
                                </label>
                                <select
                                    v-model="filters.purpose"
                                    @change="applyFilters"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">All Purposes</option>
                                    <option
                                        v-for="purpose in purposes"
                                        :key="purpose"
                                        :value="purpose"
                                    >
                                        {{ formatPurpose(purpose) }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Status
                                </label>
                                <select
                                    v-model="filters.success"
                                    @change="applyFilters"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">All Status</option>
                                    <option value="1">Success</option>
                                    <option value="0">Failed</option>
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
                        </div>

                        <!-- Active Filters -->
                        <div v-if="hasActiveFilters" class="mt-4 flex flex-wrap gap-2">
                            <span
                                v-for="(value, key) in activeFilters"
                                :key="key"
                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800"
                            >
                                {{ getFilterLabel(key, value) }}
                                <button
                                    @click="removeFilter(key)"
                                    class="ml-2 hover:text-indigo-600"
                                >
                                    &times;
                                </button>
                            </span>
                            <button
                                @click="clearFilters"
                                class="text-sm text-gray-600 hover:text-gray-800 underline"
                            >
                                Clear all
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Daily Usage Chart -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Daily Usage</h3>
                            <select
                                v-model="chartPeriod"
                                @change="loadChartData"
                                class="text-sm border-gray-300 rounded-md focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="7d">Last 7 days</option>
                                <option value="30d">Last 30 days</option>
                                <option value="90d">Last 90 days</option>
                            </select>
                        </div>
                        <DailyUsageChart :data="chartData.daily_usage" />
                    </div>

                    <!-- Usage by Provider -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Usage by Provider</h3>
                        <ProviderUsageChart :data="chartData.usage_by_provider" />
                    </div>

                    <!-- Cost Efficiency -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Cost Efficiency</h3>
                        <CostEfficiencyChart :data="chartData.cost_efficiency" />
                    </div>

                    <!-- Usage by Purpose -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Usage by Purpose</h3>
                        <PurposeUsageChart :data="chartData.usage_by_purpose" />
                    </div>
                </div>

                <!-- Usage Logs Table -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-medium text-gray-900">Recent Usage Logs</h3>
                            <span class="text-sm text-gray-500">
                                {{ usageLogsTotal }} total records
                            </span>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        User
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Provider & Model
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Purpose
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tokens
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Cost
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Time
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr
                                    v-for="log in usageLogsData"
                                    :key="log.id"
                                    class="hover:bg-gray-50 transition-colors duration-150"
                                >
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center">
                                                <UserIcon class="h-6 w-6 text-gray-500" />
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ log.user?.name || 'System' }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ log.user?.email || 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ log.ai_provider?.name || 'Unknown' }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ log.model_used || 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 capitalize">
                                            {{ formatPurpose(log.purpose) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="text-right">
                                            <div>{{ (log.total_tokens || 0)?.toLocaleString() }}</div>
                                            <div class="text-xs text-gray-500">
                                                P:{{ log.prompt_tokens || 0 }} C:{{ log.completion_tokens || 0 }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        ${{ (log.cost || 0)?.toFixed(6) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                            :class="log.success ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                        >
                                            <CheckCircleIcon v-if="log.success" class="h-3 w-3 mr-1" />
                                            <XCircleIcon v-else class="h-3 w-3 mr-1" />
                                            {{ log.success ? 'Success' : 'Failed' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ formatDateTime(log.created_at) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <Link
                                            :href="route('admin.ai-analytics.show', log.id)"
                                            class="text-indigo-600 hover:text-indigo-900"
                                        >
                                            View Details
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Empty State -->
                    <div v-if="usageLogsData.length === 0" class="text-center py-12">
                        <UserIcon class="mx-auto h-12 w-12 text-gray-400" />
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No usage logs</h3>
                        <p class="mt-1 text-sm text-gray-500">No AI usage data found for the selected filters.</p>
                    </div>

                    <!-- Pagination -->
                    <div v-if="usageLogsData.length > 0" class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                        <Pagination :links="usageLogs.links || []" />
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import SummaryCard from '@/Components/SummaryCard.vue'
import DailyUsageChart from '@/Components/Charts/DailyUsageChart.vue'
import ProviderUsageChart from '@/Components/Charts/ProviderUsageChart.vue'
import CostEfficiencyChart from '@/Components/Charts/CostEfficiencyChart.vue'
import PurposeUsageChart from '@/Components/Charts/PurposeUsageChart.vue'
import {
    ArrowDownTrayIcon,
    ArrowPathIcon,
    UserIcon,
    CheckCircleIcon,
    XCircleIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
    usageLogs: {
        type: Object,
        default: () => ({ data: [], links: [], total: 0 })
    },
    summary: {
        type: Object,
        default: () => ({})
    },
    filters: {
        type: Object,
        default: () => ({})
    },
    providers: {
        type: Object,
        default: () => ({})
    },
    purposes: {
        type: Array,
        default: () => []
    },
})

// Reactive data
const loading = ref(false)
const chartPeriod = ref('7d')
const chartData = ref({
    daily_usage: [],
    usage_by_provider: [],
    usage_by_purpose: [],
    cost_efficiency: [],
})

// Computed properties with safe access
const usageLogsData = computed(() => {
    return props.usageLogs?.data || []
})

const usageLogsTotal = computed(() => {
    return props.usageLogs?.total || 0
})

const successRate = computed(() => {
    if (!props.summary?.total_requests) return 0
    return Math.round((props.summary.successful_requests / props.summary.total_requests) * 100)
})

const successfulRequests = computed(() => {
    return props.summary?.successful_requests || 0
})

const failedRequests = computed(() => {
    return props.summary?.failed_requests || 0
})

const hasActiveFilters = computed(() => {
    return Object.values(props.filters).some(value => value !== null && value !== '')
})

const activeFilters = computed(() => {
    return Object.fromEntries(
        Object.entries(props.filters).filter(([_, value]) => value !== null && value !== '')
    )
})

// Methods
const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 4,
    }).format(amount)
}

const formatNumber = (number) => {
    return new Intl.NumberFormat('en-US').format(number)
}

const formatDateTime = (dateString) => {
    if (!dateString) return 'N/A'
    return new Date(dateString).toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    })
}

const formatPurpose = (purpose) => {
    if (!purpose) return 'Unknown'
    return purpose.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())
}

const getFilterLabel = (key, value) => {
    const labels = {
        provider: `Provider: ${props.providers[value] || value}`,
        purpose: `Purpose: ${formatPurpose(value)}`,
        success: `Status: ${value === '1' ? 'Success' : 'Failed'}`,
        date_from: `From: ${value}`,
        date_to: `To: ${value}`,
    }
    return labels[key] || `${key}: ${value}`
}

const applyFilters = () => {
    loading.value = true
    router.get(route('admin.ai-analytics.index'), props.filters, {
        preserveState: true,
        replace: true,
        onFinish: () => {
            loading.value = false
        }
    })
}

const removeFilter = (key) => {
    loading.value = true
    router.get(route('admin.ai-analytics.index'), {
        ...props.filters,
        [key]: '',
    }, {
        preserveState: true,
        replace: true,
        onFinish: () => {
            loading.value = false
        }
    })
}

const clearFilters = () => {
    loading.value = true
    router.get(route('admin.ai-analytics.index'), {}, {
        preserveState: true,
        replace: true,
        onFinish: () => {
            loading.value = false
        }
    })
}

const refreshData = () => {
    loading.value = true
    router.reload({
        onFinish: () => {
            loading.value = false
        }
    })
}

const exportData = () => {
    const params = new URLSearchParams()
    Object.entries(props.filters).forEach(([key, value]) => {
        if (value) params.append(key, value)
    })

    window.location.href = route('admin.ai-analytics.export') + '?' + params.toString()
}

const loadChartData = async () => {
    try {
        const response = await axios.get(route('admin.ai-analytics.usage-stats'), {
            params: { period: chartPeriod.value }
        })
        chartData.value = response.data
    } catch (error) {
        console.error('Failed to load chart data:', error)
        // Set empty data on error
        chartData.value = {
            daily_usage: [],
            usage_by_provider: [],
            usage_by_purpose: [],
            cost_efficiency: [],
        }
    }
}

// Watch for changes in props to handle initial load
watch(() => props.usageLogs, () => {
    console.log('Usage logs updated:', props.usageLogs)
}, { immediate: true })

// Lifecycle
onMounted(() => {
    loadChartData()
})
</script>
