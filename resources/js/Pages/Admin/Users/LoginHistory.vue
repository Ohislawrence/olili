<!-- resources/js/Pages/Admin/Users/LoginHistory.vue -->
<template>
  <AdminLayout>
    <Head title="User Login History" />

    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex justify-between items-center">
            <div>
              <h1 class="text-3xl font-bold text-gray-900">
                Login History - {{ user.name }}
              </h1>
              <p class="mt-2 text-gray-600">
                Track user login activity and security events
              </p>
            </div>
            <Link
              :href="route('admin.users.show', user.id)"
              class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
            >
              Back to User
            </Link>
          </div>
        </div>

        <!-- Login Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <StatsCard
            title="Total Logins"
            :value="loginStats.total_logins_30d"
            icon="users"
            color="blue"
          />
          <StatsCard
            title="Success Rate"
            :value="`${loginStats.success_rate_30d}%`"
            icon="academic-cap"
            color="green"
          />
          <StatsCard
            title="Unique Devices"
            :value="loginStats.unique_devices"
            icon="chart-bar"
            color="purple"
          />
          <StatsCard
            title="Consecutive Days"
            :value="loginStats.consecutive_days"
            icon="exclamation"
            color="orange"
          />
        </div>

        <!-- Login History Table -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">
              Recent Login Activity
            </h2>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Date & Time
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    IP Address
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Location
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Device
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Status
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Session Duration
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr
                  v-for="login in loginHistory.data"
                  :key="login.id"
                  class="hover:bg-gray-50"
                >
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ formatDateTime(login.login_at) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ login.ip_address }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ getLocation(login) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <div class="flex items-center">
                      <span class="capitalize">{{ login.device_type }}</span>
                      <span class="mx-1">•</span>
                      <span>{{ login.browser }}</span>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                      :class="login.was_successful ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                    >
                      {{ login.was_successful ? 'Success' : 'Failed' }}
                    </span>
                    <div
                      v-if="login.failure_reason"
                      class="text-xs text-gray-500 mt-1"
                    >
                      {{ login.failure_reason }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ formatSessionDuration(login) }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <Pagination :links="loginHistory.links" />
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import StatsCard from '@/Components/Admin/StatsCard.vue'
import Pagination from '@/Components/Pagination.vue'

defineProps({
  user: Object,
  loginHistory: Object,
  loginStats: Object,
})

const formatDateTime = (dateString) => {
  return new Date(dateString).toLocaleString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

const getLocation = (login) => {
  if (login.city && login.country) {
    return `${login.city}, ${login.country}`
  }
  return login.country || 'Unknown'
}

const formatSessionDuration = (login) => {
  if (!login.session_duration_seconds) {
    return 'Active'
  }

  const hours = Math.floor(login.session_duration_seconds / 3600)
  const minutes = Math.floor((login.session_duration_seconds % 3600) / 60)
  const seconds = login.session_duration_seconds % 60

  if (hours > 0) {
    return `${hours}h ${minutes}m ${seconds}s`
  }

  if (minutes > 0) {
    return `${minutes}m ${seconds}s`
  }

  return `${seconds}s`
}
</script>
