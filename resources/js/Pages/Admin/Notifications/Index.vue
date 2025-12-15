<!-- resources/js/Pages/Admin/Notifications/Index.vue -->
<template>
  <AdminLayout>
    <Head title="Notification Management" />

    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex justify-between items-center">
            <div>
              <h1 class="text-3xl font-bold text-gray-900">Notification Management</h1>
              <p class="mt-2 text-gray-600">
                Create and manage system notifications
              </p>
            </div>
            <Link
              :href="route('admin.notifications.create')"
              class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
            >
              <PlusIcon class="h-4 w-4 mr-2" />
              Create Notification
            </Link>
          </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
          <div v-for="(stat, index) in formattedStats" :key="index" class="bg-white rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500">{{ stat.label }}</div>
            <div class="mt-1 text-2xl font-semibold text-gray-900">{{ stat.value }}</div>
            <div v-if="stat.trend" class="mt-1 text-sm" :class="stat.trendClass">
              {{ stat.trend }}
            </div>
          </div>
        </div>

        <!-- Filters -->
        <div class="mb-6 bg-white rounded-lg shadow p-4">
          <div class="flex flex-wrap gap-4 items-center">
            <div class="flex-1 min-w-[300px]">
              <input
                v-model="filters.search"
                type="text"
                placeholder="Search notifications..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                @input="search"
              />
            </div>
            <select
              v-model="filters.status"
              class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              @change="filter"
            >
              <option value="">All Status</option>
              <option value="draft">Draft</option>
              <option value="scheduled">Scheduled</option>
              <option value="sending">Sending</option>
              <option value="sent">Sent</option>
              <option value="failed">Failed</option>
            </select>
            <select
              v-model="filters.type"
              class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              @change="filter"
            >
              <option value="">All Types</option>
              <option value="info">Information</option>
              <option value="success">Success</option>
              <option value="warning">Warning</option>
              <option value="alert">Alert</option>
              <option value="system">System</option>
            </select>
          </div>
        </div>

        <!-- Notifications Table -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
          <NotificationTable :notifications="notifications.data" />

          <!-- Pagination -->
          <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <Pagination :links="notifications.links" />
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch, computed } from 'vue'
import { PlusIcon } from '@heroicons/vue/24/outline'
import NotificationTable from '@/Components/Admin/NotificationTable.vue'
import Pagination from '@/Components/Pagination.vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  notifications: Object,
  stats: Object,
  filters: Object,
})

// Create a reactive ref for filters based on props
const filters = ref({
  search: props.filters?.search || '',
  status: props.filters?.status || '',
  type: props.filters?.type || '',
})

// Format stats for display
const formattedStats = computed(() => {
  if (!props.stats) return []

  return [
    {
      label: 'Total Notifications',
      value: props.stats.total || 0,
      icon: 'BellIcon',
      color: 'bg-blue-500',
    },
    {
      label: 'Draft',
      value: props.stats.draft || 0,
      icon: 'DocumentIcon',
      color: 'bg-gray-500',
    },
    {
      label: 'Scheduled',
      value: props.stats.scheduled || 0,
      icon: 'ClockIcon',
      color: 'bg-yellow-500',
    },
    {
      label: 'Sent',
      value: props.stats.sent || 0,
      icon: 'CheckCircleIcon',
      color: 'bg-green-500',
    },
  ]
})

let searchTimeout = null

const search = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    filter()
  }, 300)
}

const filter = () => {
  router.get(route('admin.notifications.index'), filters.value, {
    preserveState: true,
    replace: true,
  })
}

watch(
  () => [filters.value.status, filters.value.type],
  () => {
    filter()
  }
)
</script>
