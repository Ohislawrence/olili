<!-- resources/js/Pages/Admin/Dashboard.vue -->
<template>
    <AdminLayout>
  <div class="min-h-screen bg-gray-50">
    <Head title="Admin Dashboard" />

    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
          <p class="mt-2 text-gray-600">
            Overview of your AI Tutor platform
          </p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <StatsCard
            title="Total Students"
            :value="stats.total_students"
            icon="users"
            color="blue"
            :change="stats.new_students_today"
            change-label="today"
          />
          <StatsCard
            title="Active Courses"
            :value="stats.active_courses"
            icon="academic-cap"
            color="green"
            :change="stats.completion_rate"
            change-label="completion rate"
            change-type="percentage"
          />
          <StatsCard
            title="AI Cost Today"
            :value="`$${stats.ai_cost_today}`"
            icon="currency-dollar"
            color="purple"
            change-label="daily"
          />
          <StatsCard
            title="Total Tutors"
            :value="stats.total_tutors"
            icon="user-group"
            color="orange"
          />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Left Column -->
          <div class="lg:col-span-2 space-y-8">
            <!-- Charts -->
            <div class="bg-white rounded-lg shadow">
              <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                  <h2 class="text-lg font-semibold text-gray-900">
                    Platform Analytics
                  </h2>
                  <select
                    v-model="chartPeriod"
                    @change="loadChartData"
                    class="text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                  >
                    <option value="7d">Last 7 days</option>
                    <option value="30d">Last 30 days</option>
                    <option value="90d">Last 90 days</option>
                  </select>
                </div>
              </div>
              <div class="p-6">
                <UsageChart
                  :user-registrations="chartData.user_registrations"
                  :course-creations="chartData.course_creations"
                  :ai-costs="chartData.ai_costs"
                />
              </div>
            </div>

            <!-- Recent Activity -->
            <div class="grid grid-cols-1 gap-8">
              <!-- Recent Courses -->
              <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                  <h2 class="text-lg font-semibold text-gray-900">
                    Recent Courses
                  </h2>
                </div>
                <div class="divide-y divide-gray-200">
                  <div
                    v-for="course in recentActivity.recent_courses"
                    :key="course.id"
                    class="px-6 py-4 hover:bg-gray-50"
                  >
                    <div class="flex items-center justify-between">
                      <div>
                        <p class="text-sm font-medium text-gray-900">
                          {{ course.title }}
                        </p>
                        <p class="text-sm text-gray-500">
                          {{ course.student_name }}
                        </p>
                      </div>
                      <div class="flex items-center space-x-4">
                        <span
                          class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
                          :class="statusClasses[course.status]"
                        >
                          {{ course.status }}
                        </span>
                        <span class="text-sm text-gray-500">
                          {{ course.created_at }}
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                  <Link
                    :href="route('admin.courses.index')"
                    class="text-sm font-medium text-blue-600 hover:text-blue-500"
                  >
                    View all courses →
                  </Link>
                </div>
              </div>

              <!-- Recent AI Usage -->
              <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                  <h2 class="text-lg font-semibold text-gray-900">
                    Recent AI Usage
                  </h2>
                </div>
                <div class="divide-y divide-gray-200">
                  <div
                    v-for="usage in recentActivity.recent_ai_usage"
                    :key="usage.id"
                    class="px-6 py-4 hover:bg-gray-50"
                  >
                    <div class="flex items-center justify-between">
                      <div>
                        <p class="text-sm font-medium text-gray-900">
                          {{ usage.user_name }}
                        </p>
                        <p class="text-sm text-gray-500">
                          {{ usage.provider }} • {{ usage.purpose }}
                        </p>
                      </div>
                      <div class="flex items-center space-x-4">
                        <span class="text-sm font-medium text-gray-900">
                          ${{ usage.cost}}
                        </span>
                        <span class="text-sm text-gray-500">
                          {{ usage.created_at }}
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                  <Link
                    :href="route('admin.ai-analytics.index')"
                    class="text-sm font-medium text-blue-600 hover:text-blue-500"
                  >
                    View all analytics →
                  </Link>
                </div>
              </div>
            </div>
          </div>

          <!-- Right Column -->
          <div class="space-y-8">
            <!-- System Health -->
            <SystemHealth :health="systemHealth" />

            <!-- AI Usage Stats -->
            <div class="bg-white rounded-lg shadow">
              <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">
                  AI Usage Overview
                </h2>
              </div>
              <div class="p-6">
                <!-- By Provider -->
                <div class="mb-6">
                  <h3 class="text-sm font-medium text-gray-900 mb-3">
                    By Provider
                  </h3>
                  <div class="space-y-3">
                    <div
                      v-for="provider in aiUsage.by_provider"
                      :key="provider.provider"
                      class="flex items-center justify-between"
                    >
                      <span class="text-sm text-gray-600">
                        {{ provider.provider }}
                      </span>
                      <div class="flex items-center space-x-4">
                        <span class="text-sm font-medium text-gray-900">
                          ${{ provider.total_cost.toFixed(4) }}
                        </span>
                        <span class="text-xs text-gray-500">
                          {{ provider.total_requests }} req
                        </span>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- By Purpose -->
                <div>
                  <h3 class="text-sm font-medium text-gray-900 mb-3">
                    By Purpose
                  </h3>
                  <div class="space-y-3">
                    <div
                      v-for="purpose in aiUsage.by_purpose"
                      :key="purpose.purpose"
                      class="flex items-center justify-between"
                    >
                      <span class="text-sm text-gray-600 capitalize">
                        {{ purpose.purpose.replace('_', ' ') }}
                      </span>
                      <div class="flex items-center space-x-4">
                        <span class="text-sm font-medium text-gray-900">
                          ${{ purpose.total_cost.toFixed(4) }}
                        </span>
                        <span class="text-xs text-gray-500">
                          {{ purpose.total_requests }} req
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <LoginAnalytics :stats="loginStats" />

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow">
              <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">
                  Quick Actions
                </h2>
              </div>
              <div class="p-6">
                <div class="grid grid-cols-2 gap-4">
                  <Link
                    :href="route('admin.users.create')"
                    class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
                  >
                    <UserPlusIcon class="h-8 w-8 text-blue-600" />
                    <span class="mt-2 text-sm font-medium text-gray-900">
                      Add User
                    </span>
                  </Link>

                  <Link
                    :href="route('admin.courses.create')"
                    class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
                  >
                    <PlusCircleIcon class="h-8 w-8 text-green-600" />
                    <span class="mt-2 text-sm font-medium text-gray-900">
                      Create Course
                    </span>
                  </Link>

                  <Link
                    :href="route('admin.ai-providers.create')"
                    class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
                  >
                    <CpuChipIcon class="h-8 w-8 text-purple-600" />
                    <span class="mt-2 text-sm font-medium text-gray-900">
                      Add AI Provider
                    </span>
                  </Link>

                  <Link
                    :href="route('admin.settings.index')"
                    class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
                  >
                    <Cog6ToothIcon class="h-8 w-8 text-orange-600" />
                    <span class="mt-2 text-sm font-medium text-gray-900">
                      Settings
                    </span>
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  </AdminLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import {
  UserPlusIcon,
  PlusCircleIcon,
  CpuChipIcon,
  Cog6ToothIcon,
  ComputerDesktopIcon,
  GlobeAltIcon,
  DevicePhoneMobileIcon,
  ClockIcon
} from '@heroicons/vue/24/outline'
import StatsCard from '@/Components/Admin/StatsCard.vue'
import UsageChart from '@/Components/Admin/UsageChart.vue'
import SystemHealth from '@/Components/Admin/SystemHealth.vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import LoginAnalytics from '@/Components/Admin/LoginAnalytics.vue'

const props = defineProps({
  stats: Object,
  recentActivity: Object,
  aiUsage: Object,
  systemHealth: Object,
  loginStats: Object,
})

const chartPeriod = ref('7d')
const chartData = ref({
  user_registrations: [],
  course_creations: [],
  ai_costs: [],
})

const statusClasses = {
  draft: 'bg-gray-100 text-gray-800',
  active: 'bg-blue-100 text-blue-800',
  completed: 'bg-green-100 text-green-800',
  paused: 'bg-orange-100 text-orange-800',
}

const loadChartData = async () => {
  try {
    const response = await axios.get(route('admin.dashboard.chart-data'), {
      params: { period: chartPeriod.value }
    })
    chartData.value = response.data
  } catch (error) {
    console.error('Failed to load chart data:', error)
  }
}

onMounted(() => {
  loadChartData()
})
</script>
