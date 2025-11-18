<!-- resources/js/Pages/Admin/AiProviders/Index.vue -->
<template>
  <AdminLayout>
    <Head title="AI Providers Management" />

    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex justify-between items-center">
            <div>
              <h1 class="text-3xl font-bold text-gray-900">AI Providers</h1>
              <p class="mt-2 text-gray-600">
                Manage AI service providers and monitor usage
              </p>
            </div>
            <Link
              :href="route('admin.ai-providers.create')"
              class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
            >
              <PlusIcon class="h-4 w-4 mr-2" />
              Add Provider
            </Link>
          </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <StatsCard
            title="Total Providers"
            :value="providers.length"
            icon="cpu-chip"
            color="blue"
          />
          <StatsCard
            title="Active Providers"
            :value="activeProvidersCount"
            icon="check-circle"
            color="green"
          />
          <StatsCard
            title="Total Requests"
            :value="totalRequests"
            icon="chart-bar"
            color="purple"
          />
          <StatsCard
            title="Total Cost"
            :value="`$${totalCost}`"
            icon="currency-dollar"
            color="orange"
          />
        </div>

        <!-- Providers Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="provider in providers"
            :key="provider.id"
            class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200"
          >
            <!-- Provider Header -->
            <div class="p-6 border-b border-gray-200">
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                  <div
                    class="w-10 h-10 rounded-lg flex items-center justify-center text-white"
                    :class="getProviderColor(provider.code)"
                  >
                    <CpuChipIcon class="h-6 w-6" />
                  </div>
                  <div>
                    <h3 class="text-lg font-semibold text-gray-900">
                      {{ provider.name }}
                    </h3>
                    <p class="text-sm text-gray-500">
                      {{ provider.code }}
                    </p>
                  </div>
                </div>
                <div class="flex items-center space-x-2">
                  <span
                    v-if="provider.is_default"
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800"
                  >
                    Default
                  </span>
                  <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                    :class="provider.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                  >
                    {{ provider.is_active ? 'Active' : 'Inactive' }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Provider Stats -->
            <div class="p-6 space-y-4">
              <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                  <p class="text-gray-500">Models</p>
                  <p class="font-medium text-gray-900">
                    {{ provider.available_models?.length || 0 }}
                  </p>
                </div>
                <div>
                  <p class="text-gray-500">Success Rate</p>
                  <p class="font-medium text-gray-900">
                    {{
                      provider.total_requests > 0
                        ? Math.round((provider.successful_requests / provider.total_requests) * 100)
                        : 0
                    }}%
                  </p>
                </div>
                <div>
                  <p class="text-gray-500">Total Tokens</p>
                  <p class="font-medium text-gray-900">
                    {{ formatNumber(provider.total_tokens) }}
                  </p>
                </div>
                <div>
                  <p class="text-gray-500">Total Cost</p>
                  <p class="font-medium text-gray-900">
                    ${{ (provider.total_cost || 0) }}
                  </p>
                </div>
              </div>

              <!-- Usage Progress -->
              <div class="space-y-2">
                <div class="flex justify-between text-xs text-gray-500">
                  <span>Success: {{ provider.successful_requests || 0 }}</span>
                  <span>Failed: {{ provider.failed_requests || 0 }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                  <div
                    class="h-2 rounded-full transition-all duration-300"
                    :class="getSuccessRateColor(provider)"
                    :style="{
                      width: `${
                        provider.total_requests > 0
                          ? (provider.successful_requests / provider.total_requests) * 100
                          : 0
                      }%`
                    }"
                  ></div>
                </div>
              </div>
            </div>

            <!-- Provider Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between">
              <div class="flex space-x-2">
                <button
                  @click="testConnection(provider)"
                  class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                  <ArrowPathIcon class="h-3 w-3 mr-1" />
                  Test
                </button>
                <button
                  @click="toggleActive(provider)"
                  class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                  {{ provider.is_active ? 'Deactivate' : 'Activate' }}
                </button>
              </div>
              <div class="flex space-x-2">
                <Link
                  :href="route('admin.ai-providers.edit', provider.id)"
                  class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                  Edit
                </Link>
                <button
                  v-if="!provider.is_default"
                  @click="setAsDefault(provider)"
                  class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-yellow-700 bg-yellow-100 hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500"
                >
                  Set Default
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div
          v-if="providers.length === 0"
          class="text-center py-12 bg-white rounded-lg shadow-sm border border-gray-200"
        >
          <CpuChipIcon class="mx-auto h-12 w-12 text-gray-400" />
          <h3 class="mt-2 text-sm font-medium text-gray-900">No AI providers</h3>
          <p class="mt-1 text-sm text-gray-500">
            Get started by adding your first AI provider.
          </p>
          <div class="mt-6">
            <Link
              :href="route('admin.ai-providers.create')"
              class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              <PlusIcon class="h-4 w-4 mr-2" />
              Add Provider
            </Link>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { computed } from 'vue'
import { PlusIcon, CpuChipIcon, ArrowPathIcon } from '@heroicons/vue/24/outline'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import StatsCard from '@/Components/Admin/StatsCard.vue'

const props = defineProps({
  providers: Array,
})

const activeProvidersCount = computed(() => {
  return props.providers.filter(p => p.is_active).length
})

const totalRequests = computed(() => {
  return props.providers.reduce((sum, provider) => sum + (provider.total_requests || 0), 0)
})

const totalCost = computed(() => {
  return props.providers.reduce((sum, provider) => sum + (provider.total_cost || 0), 0)
})

const getProviderColor = (code) => {
  const colors = {
    openai: 'bg-green-500',
    deepseek: 'bg-blue-500',
    anthropic: 'bg-purple-500',
    google: 'bg-red-500',
  }
  return colors[code] || 'bg-gray-500'
}

const getSuccessRateColor = (provider) => {
  const successRate = provider.total_requests > 0
    ? (provider.successful_requests / provider.total_requests) * 100
    : 0

  if (successRate >= 90) return 'bg-green-500'
  if (successRate >= 70) return 'bg-yellow-500'
  return 'bg-red-500'
}

const formatNumber = (num) => {
  if (!num) return '0'
  if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M'
  if (num >= 1000) return (num / 1000).toFixed(1) + 'K'
  return num.toString()
}

const testConnection = async (provider) => {
  try {
    const response = await fetch(route('admin.ai-providers.test-connection', provider.id))
    const result = await response.json()

    if (result.success) {
      alert('Connection test successful!')
    } else {
      alert(`Connection test failed: ${result.message}`)
    }
  } catch (error) {
    alert('Connection test failed: ' + error.message)
  }
}

const toggleActive = (provider) => {
  if (confirm(`Are you sure you want to ${provider.is_active ? 'deactivate' : 'activate'} ${provider.name}?`)) {
    router.patch(route('admin.ai-providers.toggle-active', provider.id))
  }
}

const setAsDefault = (provider) => {
  if (confirm(`Set ${provider.name} as the default AI provider?`)) {
    router.patch(route('admin.ai-providers.set-default', provider.id))
  }
}
</script>
