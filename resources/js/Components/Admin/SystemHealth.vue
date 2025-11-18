<!-- resources/js/Components/Admin/SystemHealth.vue -->
<template>
  <div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200">
      <h2 class="text-lg font-semibold text-gray-900">System Health</h2>
    </div>
    <div class="p-6">
      <!-- Status Indicator -->
      <div class="flex items-center justify-between mb-6">
        <div class="flex items-center">
          <div
            class="w-3 h-3 rounded-full mr-2"
            :class="statusColor"
          ></div>
          <span class="text-sm font-medium text-gray-900 capitalize">
            {{ health.status }}
          </span>
        </div>
        <span class="text-xs text-gray-500">Last checked: Just now</span>
      </div>

      <!-- Metrics -->
      <div class="space-y-4">
        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <ExclamationTriangleIcon class="h-4 w-4 text-gray-400 mr-2" />
            <span class="text-sm text-gray-600">Failed Requests (7d)</span>
          </div>
          <span
            class="text-sm font-medium"
            :class="health.failed_requests > 10 ? 'text-red-600' : 'text-green-600'"
          >
            {{ health.failed_requests }}
          </span>
        </div>

        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <ChatBubbleLeftRightIcon class="h-4 w-4 text-gray-400 mr-2" />
            <span class="text-sm text-gray-600">Active Chat Sessions</span>
          </div>
          <span class="text-sm font-medium text-gray-900">
            {{ health.active_sessions }}
          </span>
        </div>

        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <ExclamationCircleIcon class="h-4 w-4 text-gray-400 mr-2" />
            <span class="text-sm text-gray-600">Recent Errors (24h)</span>
          </div>
          <span
            class="text-sm font-medium"
            :class="health.recent_errors > 0 ? 'text-red-600' : 'text-green-600'"
          >
            {{ health.recent_errors }}
          </span>
        </div>
      </div>

      <!-- Actions -->
      <div class="mt-6 pt-4 border-t border-gray-200">
        <div class="flex space-x-3">
          <button
            @click="clearCache"
            class="flex-1 text-center px-3 py-2 text-xs font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            Clear Cache
          </button>
          <button
            @click="runMaintenance"
            class="flex-1 text-center px-3 py-2 text-xs font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            Run Maintenance
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import { computed, ref, onMounted } from 'vue'
import {
  ExclamationTriangleIcon,
  ChatBubbleLeftRightIcon,
  ExclamationCircleIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  health: Object,
})

const statusColor = computed(() => {
  const colors = {
    healthy: 'bg-green-400',
    degraded: 'bg-yellow-400',
    critical: 'bg-red-400',
  }
  return colors[props.health.status] || 'bg-gray-400'
})

const clearCache = () => {
  router.post(route('admin.settings.clear-cache'))
}

const runMaintenance = () => {
  router.post(route('admin.settings.run-maintenance'))
}
</script>
