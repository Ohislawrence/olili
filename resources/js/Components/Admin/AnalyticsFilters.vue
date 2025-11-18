<!-- resources/js/Components/Admin/AnalyticsFilters.vue -->
<template>
  <div class="bg-white rounded-lg shadow p-4 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
      <!-- Provider Filter -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          Provider
        </label>
        <select
          v-model="localFilters.provider"
          @change="emitFilters"
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        >
          <option value="">All Providers</option>
          <option
            v-for="provider in providers"
            :key="provider.code"
            :value="provider.code"
          >
            {{ provider.name }}
          </option>
        </select>
      </div>

      <!-- Purpose Filter -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          Purpose
        </label>
        <select
          v-model="localFilters.purpose"
          @change="emitFilters"
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        >
          <option value="">All Purposes</option>
          <option
            v-for="purpose in purposes"
            :key="purpose"
            :value="purpose"
          >
            {{ purpose.replace('_', ' ') }}
          </option>
        </select>
      </div>

      <!-- Status Filter -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          Status
        </label>
        <select
          v-model="localFilters.success"
          @change="emitFilters"
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        >
          <option value="">All Status</option>
          <option value="1">Success</option>
          <option value="0">Failed</option>
        </select>
      </div>

      <!-- Date From -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          From Date
        </label>
        <input
          v-model="localFilters.date_from"
          type="date"
          @change="emitFilters"
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        />
      </div>

      <!-- Date To -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          To Date
        </label>
        <input
          v-model="localFilters.date_to"
          type="date"
          @change="emitFilters"
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        />
      </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-end space-x-3 mt-4">
      <button
        @click="resetFilters"
        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
      >
        Reset Filters
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  filters: Object,
  providers: Array,
  purposes: Array,
})

const emit = defineEmits(['filter'])

const localFilters = ref({
  provider: props.filters.provider || '',
  purpose: props.filters.purpose || '',
  success: props.filters.success || '',
  date_from: props.filters.date_from || '',
  date_to: props.filters.date_to || '',
})

const emitFilters = () => {
  emit('filter', localFilters.value)
}

const resetFilters = () => {
  localFilters.value = {
    provider: '',
    purpose: '',
    success: '',
    date_from: '',
    date_to: '',
  }
  emit('filter', localFilters.value)
}

// Watch for external filter changes
watch(
  () => props.filters,
  (newFilters) => {
    localFilters.value = { ...newFilters }
  },
  { deep: true }
)
</script>
