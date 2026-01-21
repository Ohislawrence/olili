<!-- resources/js/Components/ExamPrepStats.vue -->
<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h3 class="text-lg font-semibold text-gray-900">Exam Performance Analytics</h3>
        <p class="text-sm text-gray-600">Detailed statistics and insights</p>
      </div>
      <div class="flex items-center space-x-2">
        <select
          v-model="timeRange"
          @change="fetchStatistics"
          class="block w-full pl-3 pr-10 py-2 text-sm border-gray-300 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 rounded-lg"
        >
          <option value="7days">Last 7 days</option>
          <option value="30days">Last 30 days</option>
          <option value="90days">Last 90 days</option>
          <option value="all">All time</option>
        </select>
        <button
          @click="refreshStatistics"
          :disabled="loading"
          class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 disabled:opacity-50"
        >
          <ArrowPathIcon class="h-4 w-4" :class="{ 'animate-spin': loading }" />
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="animate-pulse">
        <div class="h-4 bg-gray-200 rounded w-1/4 mx-auto mb-4"></div>
        <div class="h-2 bg-gray-200 rounded w-1/2 mx-auto"></div>
      </div>
    </div>

    <!-- Main Stats Grid -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <!-- Total Attempts -->
      <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
              <AcademicCapIcon class="h-6 w-6 text-blue-600" />
            </div>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Total Attempts</p>
            <p class="text-2xl font-bold text-gray-900">{{ formatNumber(stats.total_attempts) }}</p>
          </div>
        </div>
        <div class="mt-4">
          <div class="flex items-center justify-between text-sm">
            <span class="text-gray-500">Unique Students</span>
            <span class="font-semibold text-gray-900">{{ formatNumber(stats.unique_students) }}</span>
          </div>
        </div>
      </div>

      <!-- Average Score -->
      <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center">
              <ChartBarIcon class="h-6 w-6 text-emerald-600" />
            </div>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Average Score</p>
            <p class="text-2xl font-bold text-gray-900">{{ parseFloat(stats.average_score || 0).toFixed(1) }}%</p>
          </div>
        </div>
        <div class="mt-4">
          <div class="flex items-center text-sm">
            <span class="text-gray-500">vs Target:</span>
            <span :class="[
              'ml-2 font-semibold',
              stats.average_score >= examPrep.passing_score ? 'text-emerald-600' : 'text-red-600'
            ]">
              {{ (stats.average_score - examPrep.passing_score).toFixed(1) }}%
            </span>
          </div>
        </div>
      </div>

      <!-- Pass Rate -->
      <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
              <CheckCircleIcon class="h-6 w-6 text-green-600" />
            </div>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Pass Rate</p>
            <p class="text-2xl font-bold text-gray-900">{{ parseFloat(stats.pass_rate || 0).toFixed(1) }}%</p>
          </div>
        </div>
        <div class="mt-4">
          <div class="w-full bg-gray-200 rounded-full h-2">
            <div
              class="bg-green-600 h-2 rounded-full transition-all duration-500"
              :style="{ width: `${stats.pass_rate}%` }"
            ></div>
          </div>
          <div class="mt-1 text-xs text-gray-500">
            {{ formatNumber(stats.passed_attempts) }} passed / {{ formatNumber(stats.total_attempts) }} total
          </div>
        </div>
      </div>

      <!-- Completion Rate -->
      <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center">
              <ClockIcon class="h-6 w-6 text-purple-600" />
            </div>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Completion Rate</p>
            <p class="text-2xl font-bold text-gray-900">{{ parseFloat(stats.completion_rate || 0).toFixed(1) }}%</p>
          </div>
        </div>
        <div class="mt-4">
          <div class="flex items-center justify-between text-sm">
            <span class="text-gray-500">Avg Time</span>
            <span class="font-semibold text-gray-900">{{ formatTime(stats.average_time) }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts and Detailed Stats -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Performance Over Time -->
      <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
        <h4 class="text-base font-semibold text-gray-900 mb-4">Performance Over Time</h4>
        <div class="h-64">
          <canvas ref="performanceChart"></canvas>
        </div>
      </div>

      <!-- Question Difficulty Analysis -->
      <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
        <h4 class="text-base font-semibold text-gray-900 mb-4">Question Difficulty Analysis</h4>
        <div class="space-y-4">
          <div
            v-for="(diff, index) in stats.question_stats"
            :key="index"
            class="space-y-2"
          >
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
                  :class="getDifficultyClass(diff.difficulty)">
                  {{ diff.difficulty }}
                </span>
                <span class="ml-2 text-sm text-gray-600">{{ diff.total }} questions</span>
              </div>
              <span class="text-sm font-semibold text-gray-900">
                {{ diff.success_rate.toFixed(1) }}%
              </span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
              <div
                class="h-2 rounded-full transition-all duration-500"
                :class="getDifficultyColor(diff.difficulty)"
                :style="{ width: `${diff.success_rate}%` }"
              ></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Attempt Distribution -->
      <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
        <h4 class="text-base font-semibold text-gray-900 mb-4">Attempt Distribution</h4>
        <div class="h-64">
          <canvas ref="attemptsChart"></canvas>
        </div>
      </div>

      <!-- Score Distribution -->
      <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
        <h4 class="text-base font-semibold text-gray-900 mb-4">Score Distribution</h4>
        <div class="h-64">
          <canvas ref="scoresChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Recent Attempts -->
    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
      <div class="flex items-center justify-between mb-4">
        <h4 class="text-base font-semibold text-gray-900">Recent Attempts</h4>
        <Link
          :href="route('admin.exam-preps.attempts', examPrep.id)"
          class="text-sm font-medium text-emerald-600 hover:text-emerald-500"
        >
          View all attempts →
        </Link>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead>
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Student
              </th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Score
              </th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Time Spent
              </th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Status
              </th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Date
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr
              v-for="attempt in recentAttempts"
              :key="attempt.id"
              class="hover:bg-gray-50"
            >
              <td class="px-4 py-3 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-8 w-8">
                    <div class="h-8 w-8 rounded-full bg-emerald-100 flex items-center justify-center">
                      <UserIcon class="h-5 w-5 text-emerald-600" />
                    </div>
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">
                      {{ attempt.user.name }}
                    </div>
                    <div class="text-sm text-gray-500">
                      {{ attempt.user.email }}
                    </div>
                  </div>
                </div>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <div class="text-sm font-semibold text-gray-900">
                  {{ parseFloat(attempt.percentage|| 0).toFixed(1) }}%
                </div>
                <div class="text-xs text-gray-500">
                  {{ attempt.score }}/{{ attempt.total_points }} points
                </div>
              </td>
              <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                {{ formatTime(attempt.time_spent_seconds) }}
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <span :class="[
                  'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                  attempt.is_passed ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800'
                ]">
                  {{ attempt.is_passed ? 'Passed' : 'Failed' }}
                </span>
              </td>
              <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                {{ formatDate(attempt.completed_at) }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Top Performers -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
        <h4 class="text-base font-semibold text-gray-900 mb-4">Top Performers</h4>
        <div class="space-y-4">
          <div
            v-for="(student, index) in topPerformers"
            :key="student.id"
            class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
          >
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center">
                  <span class="text-sm font-semibold text-emerald-700">
                    {{ index + 1 }}
                  </span>
                </div>
              </div>
              <div class="ml-3">
                <div class="text-sm font-medium text-gray-900">
                  {{ student.name }}
                </div>
                <div class="text-xs text-gray-500">
                  {{ student.email }}
                </div>
              </div>
            </div>
            <div class="text-right">
              <div class="text-sm font-bold text-gray-900">
                {{ student.best_score.toFixed(1) }}%
              </div>
              <div class="text-xs text-gray-500">
                {{ student.attempt_count }} attempts
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Weakest Areas -->
      <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
        <h4 class="text-base font-semibold text-gray-900 mb-4">Areas Needing Improvement</h4>
        <div class="space-y-4">
          <div
            v-for="(weakness, index) in weakAreas"
            :key="index"
            class="p-3 border border-red-100 bg-red-50 rounded-lg"
          >
            <div class="flex items-center justify-between mb-2">
              <span class="text-sm font-medium text-red-800">
                {{ weakness.topic || 'Unknown Topic' }}
              </span>
              <span class="text-xs text-red-600">
                {{ weakness.success_rate.toFixed(1) }}% success
              </span>
            </div>
            <div class="text-xs text-red-600">
              {{ weakness.incorrect_count }} incorrect attempts
            </div>
            <div v-if="weakness.common_mistakes" class="mt-2">
              <div class="text-xs font-medium text-red-700 mb-1">Common Mistakes:</div>
              <div class="text-xs text-red-600">
                {{ weakness.common_mistakes.join(', ') }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'
import {
  AcademicCapIcon,
  ChartBarIcon,
  CheckCircleIcon,
  ClockIcon,
  UserIcon,
  ArrowPathIcon,
} from '@heroicons/vue/24/outline'
import Chart from 'chart.js/auto'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  examPrep: Object,
  initialStats: Object,
  initialRecentAttempts: Array,
  initialTopPerformers: Array,
})

const loading = ref(false)
const timeRange = ref('30days')
const stats = ref(props.initialStats || {})
const recentAttempts = ref(props.initialRecentAttempts || [])
const topPerformers = ref(props.initialTopPerformers || [])
const weakAreas = ref([])

// Chart refs
const performanceChart = ref(null)
const attemptsChart = ref(null)
const scoresChart = ref(null)

// Chart instances
let performanceChartInstance = null
let attemptsChartInstance = null
let scoresChartInstance = null

// Helper functions
const formatNumber = (num) => {
  return new Intl.NumberFormat().format(num || 0)
}

const formatTime = (seconds) => {
  if (!seconds) return '0:00'
  const minutes = Math.floor(seconds / 60)
  const remainingSeconds = seconds % 60
  return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const getDifficultyClass = (difficulty) => {
  const classes = {
    easy: 'bg-green-100 text-green-800',
    medium: 'bg-yellow-100 text-yellow-800',
    hard: 'bg-red-100 text-red-800',
  }
  return classes[difficulty] || 'bg-gray-100 text-gray-800'
}

const getDifficultyColor = (difficulty) => {
  const colors = {
    easy: 'bg-green-500',
    medium: 'bg-yellow-500',
    hard: 'bg-red-500',
  }
  return colors[difficulty] || 'bg-gray-500'
}

// Fetch statistics
const fetchStatistics = async () => {
  loading.value = true

  try {
    const response = await fetch(route('admin.exam-preps.statistics', {
      examPrep: props.examPrep.id,
      time_range: timeRange.value
    }))

    const data = await response.json()

    stats.value = data.statistics || {}
    recentAttempts.value = data.recent_attempts || []
    topPerformers.value = data.top_performers || []
    weakAreas.value = data.weak_areas || []

    // Update charts
    updateCharts()
  } catch (error) {
    console.error('Failed to fetch statistics:', error)
  } finally {
    loading.value = false
  }
}

const refreshStatistics = () => {
  fetchStatistics()
}

// Initialize charts
const initCharts = () => {
  if (performanceChart.value) {
    performanceChartInstance = new Chart(performanceChart.value, {
      type: 'line',
      data: {
        labels: stats.value.performance_over_time?.labels || [],
        datasets: [{
          label: 'Average Score (%)',
          data: stats.value.performance_over_time?.scores || [],
          borderColor: '#10b981',
          backgroundColor: 'rgba(16, 185, 129, 0.1)',
          tension: 0.4,
          fill: true
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            max: 100,
            ticks: {
              callback: (value) => `${value}%`
            }
          }
        }
      }
    })
  }

  if (attemptsChart.value) {
    attemptsChartInstance = new Chart(attemptsChart.value, {
      type: 'bar',
      data: {
        labels: stats.value.attempt_distribution?.labels || [],
        datasets: [{
          label: 'Attempts',
          data: stats.value.attempt_distribution?.counts || [],
          backgroundColor: '#3b82f6',
          borderRadius: 4
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          }
        }
      }
    })
  }

  if (scoresChart.value) {
    scoresChartInstance = new Chart(scoresChart.value, {
      type: 'doughnut',
      data: {
        labels: ['0-50%', '51-70%', '71-85%', '86-100%'],
        datasets: [{
          data: stats.value.score_distribution || [0, 0, 0, 0],
          backgroundColor: [
            '#ef4444',
            '#f59e0b',
            '#10b981',
            '#3b82f6'
          ]
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'right'
          }
        }
      }
    })
  }
}

// Update charts with new data
const updateCharts = () => {
  if (performanceChartInstance && stats.value.performance_over_time) {
    performanceChartInstance.data.labels = stats.value.performance_over_time.labels
    performanceChartInstance.data.datasets[0].data = stats.value.performance_over_time.scores
    performanceChartInstance.update()
  }

  if (attemptsChartInstance && stats.value.attempt_distribution) {
    attemptsChartInstance.data.labels = stats.value.attempt_distribution.labels
    attemptsChartInstance.data.datasets[0].data = stats.value.attempt_distribution.counts
    attemptsChartInstance.update()
  }

  if (scoresChartInstance && stats.value.score_distribution) {
    scoresChartInstance.data.datasets[0].data = stats.value.score_distribution
    scoresChartInstance.update()
  }
}

// Clean up charts
const destroyCharts = () => {
  if (performanceChartInstance) {
    performanceChartInstance.destroy()
    performanceChartInstance = null
  }
  if (attemptsChartInstance) {
    attemptsChartInstance.destroy()
    attemptsChartInstance = null
  }
  if (scoresChartInstance) {
    scoresChartInstance.destroy()
    scoresChartInstance = null
  }
}

// Lifecycle hooks
onMounted(() => {
  initCharts()
  fetchStatistics()
})

onUnmounted(() => {
  destroyCharts()
})
</script>
