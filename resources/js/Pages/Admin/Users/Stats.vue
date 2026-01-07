<template>
  <AdminLayout>
    <Head title="User Statistics Dashboard" />

    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex justify-between items-center">
            <div>
              <h1 class="text-3xl font-bold text-gray-900">User Statistics Dashboard</h1>
              <p class="mt-2 text-gray-600">
                Comprehensive analytics and insights about all users
              </p>
            </div>
            <div class="flex space-x-3">
              <Link
                :href="route('admin.users.stats.export', filters)"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
              >
                Export Stats
              </Link>
              <Link
                :href="route('admin.users.index')"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
              >
                View Users
              </Link>
            </div>
          </div>
        </div>

        <!-- Filters -->
        <div class="bg-white shadow rounded-lg p-6 mb-8">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Filters</h3>
          <form @submit.prevent="applyFilters" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
              <select
                v-model="filters.role"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
              >
                <option value="">All Roles</option>
                <option value="student">Student</option>
                <option value="tutor">Tutor</option>
                <option value="admin">Admin</option>
                <option value="organization">Organization</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
              <select
                v-model="filters.status"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
              >
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
              <input
                v-model="filters.date_from"
                type="date"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
              <input
                v-model="filters.date_to"
                type="date"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
              />
            </div>
            <div class="md:col-span-4 flex justify-end space-x-3">
              <button
                type="submit"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700"
              >
                Apply Filters
              </button>
              <button
                type="button"
                @click="resetFilters"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
              >
                Reset
              </button>
            </div>
          </form>
        </div>

        <!-- Overall Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <StatsCard
            title="Total Users"
            :value="stats.total_users"
            icon="users"
            color="blue"
            :description="`${stats.active_users} active`"
          />
          <StatsCard
            title="Total Enrollments"
            :value="stats.total_enrollments"
            icon="academic-cap"
            color="green"
            :description="`${stats.completed_enrollments} completed`"
          />
          <StatsCard
            title="Completion Rate"
            :value="`${stats.completion_rate}%`"
            icon="trophy"
            color="orange"
            :description="`Avg progress: ${stats.avg_progress}%`"
          />
          <StatsCard
            title="Total Study Hours"
            :value="stats.total_study_hours"
            icon="clock"
            color="purple"
            :description="`${stats.active_students} active students`"
          />
          <StatsCard
            title="Certificates Issued"
            :value="stats.total_certificates"
            icon="check-badge"
            color="emerald"
            :description="`${stats.active_certificates} active, ${stats.certificate_rate}% rate`"
          />
          <StatsCard
            title="New Users (Month)"
            :value="stats.new_users_month"
            icon="user-plus"
            color="pink"
            :description="`${stats.new_users_week} this week`"
          />
          <StatsCard
            title="Avg Study Hours"
            :value="stats.avg_study_hours"
            icon="chart-bar"
            color="indigo"
            :description="`Per user`"
          />
          <StatsCard
            title="Verified Users"
            :value="stats.verified_users"
            icon="shield-check"
            color="teal"
            :description="`${Math.round((stats.verified_users / stats.total_users) * 100)}% of total`"
          />
        </div>

        <!-- Charts and Graphs -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
          <!-- Role Distribution -->
          <div class="bg-white shadow rounded-lg p-6 mb-8">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Role Distribution</h3>
                <div class="text-sm text-gray-500">
                Total: {{ stats.total_users }} users
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2">
                <BarChart
                    v-if="roleChartData.labels && roleChartData.labels.length > 0"
                    :data="roleChartData"
                    :options="roleChartOptions"
                    height="250px"
                />
                <div v-else class="h-64 flex items-center justify-center text-gray-500 border-2 border-dashed border-gray-300 rounded-lg">
                    No role distribution data available
                </div>
                </div>

                <div class="space-y-4">
                <div
                    v-for="(count, role) in roleDistribution"
                    :key="role"
                    class="p-4 rounded-lg border border-gray-200 hover:bg-gray-50"
                >
                    <div class="flex justify-between items-center mb-2">
                    <span class="font-medium text-gray-900 capitalize">{{ role }}</span>
                    <span class="text-sm px-2 py-1 rounded-full" :class="getRoleBadgeClass(role)">
                        {{ count }}
                    </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                    <div
                        class="h-2 rounded-full"
                        :class="getRoleColorClass(role)"
                        :style="{ width: `${getPercentage(count, stats.total_users)}%` }"
                    ></div>
                    </div>
                    <div class="text-xs text-gray-500 mt-1 text-right">
                    {{ getPercentage(count, stats.total_users) }}%
                    </div>
                </div>

                <div v-if="Object.keys(roleDistribution).length === 0" class="text-center py-8 text-gray-500">
                    No roles data available
                </div>
                </div>
            </div>
            </div>

            <!-- Monthly User Growth with Chart -->
            <div class="bg-white shadow rounded-lg p-6 mb-8">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Monthly User Growth</h3>
                <div class="text-sm text-gray-500">
                Last 12 months
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2">
                <LineChart
                    v-if="growthChartData.labels && growthChartData.labels.length > 0"
                    :data="growthChartData"
                    :options="growthChartOptions"
                    height="250px"
                />
                <div v-else class="h-64 flex items-center justify-center text-gray-500 border-2 border-dashed border-gray-300 rounded-lg">
                    No growth data available
                </div>
                </div>

                <div class="space-y-4">
                <div class="p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg">
                    <div class="text-3xl font-bold text-gray-900 mb-2">
                    {{ getCurrentMonthGrowth() }}
                    </div>
                    <div class="text-sm text-gray-600">New users this month</div>
                    <div class="text-xs text-gray-500 mt-1">
                    {{ getGrowthPercentage() }}% change from last month
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 bg-gray-50 rounded-lg">
                    <div class="text-2xl font-bold text-gray-900">
                        {{ getTotalYearGrowth() }}
                    </div>
                    <div class="text-sm text-gray-600">Total this year</div>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                    <div class="text-2xl font-bold text-gray-900">
                        {{ getAverageMonthlyGrowth() }}
                    </div>
                    <div class="text-sm text-gray-600">Monthly average</div>
                    </div>
                </div>

                <div class="p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg">
                    <div class="text-lg font-semibold text-gray-900 mb-2">
                    Peak Month
                    </div>
                    <div class="text-2xl font-bold text-green-900">
                    {{ getPeakMonth() }}
                    </div>
                    <div class="text-sm text-gray-600">
                    {{ getPeakMonthCount() }} users
                    </div>
                </div>
                </div>
            </div>
            </div>
        </div>

        <!-- Learning Analytics -->
        <div class="bg-white shadow rounded-lg p-6 mb-8">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Learning Analytics Summary</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div class="text-center p-4 bg-blue-50 rounded-lg">
              <div class="text-2xl font-bold text-blue-900">{{ learningAnalytics.avg_study_hours }}</div>
              <div class="text-sm text-blue-600">Avg Study Hours</div>
              <div class="text-xs text-blue-500 mt-1">Per active learner</div>
            </div>
            <div class="text-center p-4 bg-green-50 rounded-lg">
              <div class="text-2xl font-bold text-green-900">{{ learningAnalytics.avg_score }}</div>
              <div class="text-sm text-green-600">Average Score</div>
              <div class="text-xs text-green-500 mt-1">Out of 100</div>
            </div>
            <div class="text-center p-4 bg-purple-50 rounded-lg">
              <div class="text-2xl font-bold text-purple-900">{{ learningAnalytics.active_learners }}</div>
              <div class="text-sm text-purple-600">Active Learners</div>
              <div class="text-xs text-purple-500 mt-1">Last 30 days</div>
            </div>
            <div class="text-center p-4 bg-orange-50 rounded-lg">
              <div class="text-2xl font-bold text-orange-900">{{ learningAnalytics.active_days }}</div>
              <div class="text-sm text-orange-600">Active Days</div>
              <div class="text-xs text-orange-500 mt-1">Last 30 days</div>
            </div>
          </div>

          <!-- Streak Distribution -->
          <div v-if="learningAnalytics.streak_distribution && Object.keys(learningAnalytics.streak_distribution).length > 0">
            <h4 class="text-sm font-medium text-gray-700 mb-3">Learning Streak Distribution (Last 7 Days)</h4>
            <div class="space-y-2">
              <div
                v-for="(count, days) in learningAnalytics.streak_distribution"
                :key="days"
                class="flex items-center"
              >
                <div class="w-24 text-sm text-gray-600">{{ days }} day(s)</div>
                <div class="flex-1">
                  <div class="w-full bg-gray-200 rounded-full h-4">
                    <div
                      class="bg-gradient-to-r from-green-500 to-emerald-600 h-4 rounded-full"
                      :style="{ width: `${(count / learningAnalytics.active_learners) * 100}%` }"
                    ></div>
                  </div>
                </div>
                <div class="w-16 text-right text-sm font-medium text-gray-900">{{ count }} users</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Top Performers -->
        <div class="bg-white shadow rounded-lg overflow-hidden mb-8">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Top Performers</h3>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completed Courses</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Certificates</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Study Hours</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Active</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="user in topPerformers" :key="user.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10">
                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold">
                          {{ getUserInitials(user.name) }}
                        </div>
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">
                          <Link :href="route('admin.users.show', user.id)" class="hover:text-blue-600">
                            {{ user.name }}
                          </Link>
                        </div>
                        <div class="text-sm text-gray-500">{{ user.email }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                      :class="getRoleBadgeClass(user.role)"
                    >
                      {{ user.role }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ user.completed_courses }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ user.certificates }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ user.study_hours }}h</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ formatDate(user.last_active) }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-if="topPerformers.length === 0" class="text-center py-8 text-gray-500">
            No top performers data available
          </div>
        </div>

        <!-- Users Table -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
              <h3 class="text-lg font-semibold text-gray-900">All Users</h3>
              <div class="text-sm text-gray-500">
                Showing {{ users.from }} to {{ users.to }} of {{ users.total }} users
              </div>
            </div>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Enrollments</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Certificates</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10">
                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold">
                          {{ getUserInitials(user.name) }}
                        </div>
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">
                          <Link :href="route('admin.users.show', user.id)" class="hover:text-blue-600">
                            {{ user.name }}
                          </Link>
                        </div>
                        <div class="text-sm text-gray-500">{{ user.email }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                      :class="getRoleBadgeClass(user.roles[0]?.name)"
                    >
                      {{ user.roles[0]?.name || 'N/A' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">
                      {{ user.course_enrollments_count }}
                      <span class="text-xs text-gray-500">
                        ({{ user.active_enrollments_count }} active, {{ user.completed_enrollments_count }} completed)
                      </span>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                      :class="user.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                    >
                      {{ user.is_active ? 'Active' : 'Inactive' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">
                      {{ user.certificates_count }}
                      <span class="text-xs text-gray-500">
                        ({{ user.active_certificates_count }} active)
                      </span>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ formatDate(user.created_at) }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <!-- Pagination -->
          <div class="px-6 py-4 border-t border-gray-200">
            <Pagination :links="users.links" />
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import StatsCard from '@/Components/Admin/StatsCard.vue'
import Pagination from '@/Components/Admin/Pagination.vue'
import BarChart from '@/Components/Charts/BarChart.vue'
import LineChart from '@/Components/Charts/LineChart.vue'
import { ref, computed, onMounted , watch} from 'vue'

const props = defineProps({
  users: Object,
  stats: Object,
  roleDistribution: Object,
  monthlyGrowth: Object,
  topPerformers: Array,
  learningAnalytics: Object,
  filters: Object,
})

// Local filters state
const filters = ref({
  role: props.filters.role || '',
  status: props.filters.status || '',
  date_from: props.filters.date_from || '',
  date_to: props.filters.date_to || '',
})

// Apply filters
const applyFilters = () => {
  router.get(route('admin.users.stats'), filters.value, {
    preserveState: true,
    preserveScroll: true,
  })
}

// Reset filters
const resetFilters = () => {
  filters.value = {
    role: '',
    status: '',
    date_from: '',
    date_to: '',
  }
  applyFilters()
}

// Helper methods
const getRoleColor = (role) => {
  const colors = {
    admin: 'bg-gradient-to-t from-red-500 to-red-400',
    tutor: 'bg-gradient-to-t from-blue-500 to-blue-400',
    student: 'bg-gradient-to-t from-green-500 to-green-400',
    organization: 'bg-gradient-to-t from-purple-500 to-purple-400',
  }
  return colors[role] || 'bg-gradient-to-t from-gray-500 to-gray-400'
}

const getBarHeight = (count, total) => {
  if (!total || total === 0) return 0
  return Math.max((count / total) * 100, 10)
}

const getGrowthBarHeight = (count, data) => {
  const max = Math.max(...data, 1)
  return (count / max) * 100
}

const formatMonthLabel = (monthString) => {
  if (!monthString) return ''
  const [year, month] = monthString.split('-')
  const date = new Date(year, month - 1, 1)
  return date.toLocaleDateString('en-US', { month: 'short' })
}

const getRoleBadgeClass = (role) => {
  const classes = {
    admin: 'bg-red-100 text-red-800',
    tutor: 'bg-blue-100 text-blue-800',
    student: 'bg-green-100 text-green-800',
    organization: 'bg-purple-100 text-purple-800',
  }
  return classes[role] || 'bg-gray-100 text-gray-800'
}

const getUserInitials = (name) => {
  if (!name) return '?'
  return name
    .split(' ')
    .map(part => part[0])
    .join('')
    .toUpperCase()
    .substring(0, 2)
}

const formatDate = (dateString) => {
  if (!dateString) return 'Never'
  const date = new Date(dateString)
  const now = new Date()
  const diffMs = now - date
  const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24))

  if (diffDays === 0) return 'Today'
  if (diffDays === 1) return 'Yesterday'
  if (diffDays < 7) return `${diffDays} days ago`
  if (diffDays < 30) return `${Math.floor(diffDays / 7)} weeks ago`

  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}
// Role Distribution Chart Data
const roleChartData = computed(() => {
  if (!props.roleDistribution || Object.keys(props.roleDistribution).length === 0) {
    return {}
  }

  const roles = Object.keys(props.roleDistribution)
  const counts = Object.values(props.roleDistribution)

  // Generate colors based on role
  const backgroundColors = roles.map(role => getRoleChartColor(role))
  const borderColors = backgroundColors.map(color => color.replace('0.8', '1'))

  return {
    labels: roles.map(role => role.charAt(0).toUpperCase() + role.slice(1)),
    datasets: [{
      label: 'Number of Users',
      data: counts,
      backgroundColor: backgroundColors,
      borderColor: borderColors,
      borderWidth: 1
    }]
  }
})

const roleChartOptions = {
  plugins: {
    legend: {
      display: false
    },
    tooltip: {
      callbacks: {
        label: function(context) {
          const total = props.stats.total_users || 1
          const percentage = ((context.raw / total) * 100).toFixed(1)
          return `${context.label}: ${context.raw} users (${percentage}%)`
        }
      }
    }
  },
  scales: {
    y: {
      beginAtZero: true,
      title: {
        display: true,
        text: 'Number of Users'
      }
    },
    x: {
      title: {
        display: true,
        text: 'Roles'
      }
    }
  }
}

// Monthly Growth Chart Data
const growthChartData = computed(() => {
  if (!props.monthlyGrowth || !props.monthlyGrowth.labels || props.monthlyGrowth.labels.length === 0) {
    return {}
  }

  const labels = props.monthlyGrowth.labels.map(label => formatMonthShort(label))
  const data = props.monthlyGrowth.data || []

  // Calculate cumulative growth
  const cumulativeData = []
  let cumulative = 0
  data.forEach(count => {
    cumulative += count
    cumulativeData.push(cumulative)
  })

  return {
    labels: labels,
    datasets: [
      {
        label: 'New Users',
        data: data,
        borderColor: '#3b82f6',
        backgroundColor: 'rgba(59, 130, 246, 0.1)',
        tension: 0.4,
        fill: true
      },
      {
        label: 'Total Users',
        data: cumulativeData,
        borderColor: '#10b981',
        backgroundColor: 'rgba(16, 185, 129, 0.1)',
        tension: 0.4,
        fill: false,
        borderDash: [5, 5]
      }
    ]
  }
})

const growthChartOptions = {
  plugins: {
    tooltip: {
      mode: 'index',
      intersect: false
    }
  },
  scales: {
    y: {
      beginAtZero: true,
      title: {
        display: true,
        text: 'Number of Users'
      }
    },
    x: {
      title: {
        display: true,
        text: 'Month'
      }
    }
  },
  interaction: {
    mode: 'nearest',
    axis: 'x',
    intersect: false
  }
}

// Helper methods
const getRoleChartColor = (role) => {
  const colors = {
    admin: 'rgba(239, 68, 68, 0.8)',
    tutor: 'rgba(59, 130, 246, 0.8)',
    student: 'rgba(16, 185, 129, 0.8)',
    organization: 'rgba(139, 92, 246, 0.8)',
  }
  return colors[role] || 'rgba(156, 163, 175, 0.8)'
}

const getRoleColorClass = (role) => {
  const classes = {
    admin: 'bg-red-500',
    tutor: 'bg-blue-500',
    student: 'bg-green-500',
    organization: 'bg-purple-500',
  }
  return classes[role] || 'bg-gray-500'
}

const getPercentage = (part, total) => {
  if (!total || total === 0) return 0
  return ((part / total) * 100).toFixed(1)
}

const formatMonthShort = (monthString) => {
  if (!monthString) return ''
  const [year, month] = monthString.split('-')
  const date = new Date(year, month - 1, 1)
  return date.toLocaleDateString('en-US', { month: 'short', year: '2-digit' })
}

const getCurrentMonthGrowth = () => {
  if (!props.monthlyGrowth?.data || props.monthlyGrowth.data.length === 0) return 0
  return props.monthlyGrowth.data.slice(-1)[0] || 0
}

const getGrowthPercentage = () => {
  if (!props.monthlyGrowth?.data || props.monthlyGrowth.data.length < 2) return 0
  const current = props.monthlyGrowth.data.slice(-1)[0] || 0
  const previous = props.monthlyGrowth.data.slice(-2)[0] || 0
  if (previous === 0) return 100
  return (((current - previous) / previous) * 100).toFixed(1)
}

const getTotalYearGrowth = () => {
  if (!props.monthlyGrowth?.data) return 0
  return props.monthlyGrowth.data.reduce((sum, count) => sum + count, 0)
}

const getAverageMonthlyGrowth = () => {
  if (!props.monthlyGrowth?.data || props.monthlyGrowth.data.length === 0) return 0
  const total = getTotalYearGrowth()
  return Math.round(total / props.monthlyGrowth.data.length)
}

const getPeakMonth = () => {
  if (!props.monthlyGrowth?.data || props.monthlyGrowth.data.length === 0) return 'N/A'
  const maxIndex = props.monthlyGrowth.data.indexOf(Math.max(...props.monthlyGrowth.data))
  return formatMonthShort(props.monthlyGrowth.labels[maxIndex])
}

const getPeakMonthCount = () => {
  if (!props.monthlyGrowth?.data || props.monthlyGrowth.data.length === 0) return 0
  return Math.max(...props.monthlyGrowth.data)
}


</script>
