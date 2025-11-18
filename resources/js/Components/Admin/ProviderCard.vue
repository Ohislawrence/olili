<!-- resources/js/Components/Admin/ProviderCard.vue -->
<template>
  <div class="bg-white rounded-lg shadow hover:shadow-md transition-shadow">
    <div class="p-6">
      <!-- Header -->
      <div class="flex items-start justify-between mb-4">
        <div>
          <h3 class="text-lg font-semibold text-gray-900">
            {{ provider.name }}
          </h3>
          <p class="text-sm text-gray-600 mt-1">
            Code: {{ provider.code }}
          </p>
        </div>
        <div class="flex items-center space-x-2">
          <span
            v-if="provider.is_default"
            class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800"
          >
            Default
          </span>
          <span
            class="inline-flex items-center px-2 py-1 rounded text-xs font-medium"
            :class="provider.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
          >
            {{ provider.is_active ? 'Active' : 'Inactive' }}
          </span>
        </div>
      </div>

      <!-- Models -->
      <div class="mb-4">
        <p class="text-sm font-medium text-gray-900 mb-2">Available Models</p>
        <div class="flex flex-wrap gap-1">
          <span
            v-for="model in provider.available_models"
            :key="model"
            class="inline-flex items-center px-2 py-1 rounded text-xs bg-gray-100 text-gray-800"
          >
            {{ model }}
          </span>
        </div>
      </div>

      <!-- Stats -->
      <div class="grid grid-cols-2 gap-4 text-sm mb-4">
        <div>
          <p class="text-gray-600">Total Requests</p>
          <p class="font-semibold text-gray-900">
            {{ provider.total_requests.toLocaleString() }}
          </p>
        </div>
        <div>
          <p class="text-gray-600">Total Cost</p>
          <p class="font-semibold text-gray-900">
            ${{ provider.total_cost.toFixed(4) }}
          </p>
        </div>
        <div>
          <p class="text-gray-600">Success Rate</p>
          <p class="font-semibold text-gray-900">
            {{
              Math.round(
                (provider.successful_requests /
                  (provider.successful_requests + provider.failed_requests)) *
                  100
              )
            }}%
          </p>
        </div>
        <div>
          <p class="text-gray-600">Total Tokens</p>
          <p class="font-semibold text-gray-900">
            {{ provider.total_tokens.toLocaleString() }}
          </p>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex space-x-2">
        <Link
          :href="route('admin.ai-providers.edit', provider.id)"
          class="flex-1 text-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          Edit
        </Link>
        <button
          @click="testConnection"
          class="flex-1 text-center px-3 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"
          :disabled="testingConnection"
        >
          <ArrowPathIcon
            v-if="testingConnection"
            class="h-4 w-4 mx-auto animate-spin"
          />
          <span v-else>Test</span>
        </button>
        <button
          v-if="!provider.is_default && provider.is_active"
          @click="setAsDefault"
          class="flex-1 text-center px-3 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          Set Default
        </button>
        <button
          @click="toggleActive"
          class="flex-1 text-center px-3 py-2 text-sm font-medium text-white border border-transparent rounded-md focus:outline-none focus:ring-2"
          :class="
            provider.is_active
              ? 'bg-orange-600 hover:bg-orange-700 focus:ring-orange-500'
              : 'bg-green-600 hover:bg-green-700 focus:ring-green-500'
          "
        >
          {{ provider.is_active ? 'Deactivate' : 'Activate' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import { ArrowPathIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  provider: Object,
})

const testingConnection = ref(false)

const testConnection = async () => {
  testingConnection.value = true
  try {
    const response = await axios.post(
      route('admin.ai-providers.test-connection', props.provider.id)
    )

    if (response.data.success) {
      alert('Connection test successful!')
    } else {
      alert('Connection test failed: ' + response.data.message)
    }
  } catch (error) {
    alert('Connection test failed: ' + error.response?.data?.message || error.message)
  } finally {
    testingConnection.value = false
  }
}

const setAsDefault = () => {
  router.post(route('admin.ai-providers.set-default', props.provider.id))
}

const toggleActive = () => {
  router.post(route('admin.ai-providers.toggle-active', props.provider.id))
}
</script>
