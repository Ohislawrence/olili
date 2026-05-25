<!-- resources/js/Pages/Student/ExamPreps/MyAttempts.vue -->
<template>
  <StudentLayout>
    <Head title="My Exam Attempts" />

    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
              <h1 class="text-3xl font-bold text-gray-900">My Exam Attempts</h1>
              <p class="mt-2 text-lg text-gray-600">
                Track your progress and review past exam performances
              </p>
            </div>
            <div class="mt-4 md:mt-0 flex space-x-3">
              <Link
                :href="route('student.exam-preps.index')"
                class="inline-flex items-center px-4 py-2.5 border border-emerald-300 text-emerald-700 font-medium rounded-lg hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-200"
              >
                <AcademicCapIcon class="h-4 w-4 mr-2" />
                Browse Exams
              </Link>
            </div>
          </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <!-- Total Attempts -->
          <div class="bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl p-6 text-white">
            <div class="flex items-center">
              <AcademicCapIcon class="h-8 w-8 mr-4 opacity-90" />
              <div>
                <p class="text-sm opacity-90">Total Attempts</p>
                <p class="text-2xl font-bold">{{ totalAttempts }}</p>
              </div>
            </div>
          </div>

          <!-- Average Score -->
          <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl p-6 text-white">
            <div class="flex items-center">
              <ChartBarIcon class="h-8 w-8 mr-4 opacity-90" />
              <div>
                <p class="text-sm opacity-90">Average Score</p>
                <p class="text-2xl font-bold">{{ averageScore }}%</p>
              </div>
            </div>
          </div>

          <!-- Pass Rate -->
          <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl p-6 text-white">
            <div class="flex items-center">
              <CheckCircleIcon class="h-8 w-8 mr-4 opacity-90" />
              <div>
                <p class="text-sm opacity-90">Pass Rate</p>
                <p class="text-2xl font-bold">{{ passRate }}%</p>
              </div>
            </div>
          </div>

          <!-- Best Score -->
          <div class="bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl p-6 text-white">
            <div class="flex items-center">
              <TrophyIcon class="h-8 w-8 mr-4 opacity-90" />
              <div>
                <p class="text-sm opacity-90">Best Score</p>
                <p class="text-2xl font-bold">{{ bestScore }}%</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Filters -->
        <div class="mb-6 bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
              <label for="search" class="sr-only">Search</label>
              <div class="relative rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" />
                </div>
                <input
                  id="search"
                  v-model="filters.search"
                  type="search"
                  placeholder="Search exams..."
                  class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                />
              </div>
            </div>

            <!-- Status Filter -->
            <div>
              <select
                v-model="filters.status"
                class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
              >
                <option value="">All Results</option>
                <option value="passed">Passed Only</option>
                <option value="failed">Failed Only</option>
              </select>
            </div>

            <!-- Date Range -->
            <div>
              <select
                v-model="filters.date_range"
                class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
              >
                <option value="">All Time</option>
                <option value="today">Today</option>
                <option value="week">Last 7 Days</option>
                <option value="month">Last 30 Days</option>
                <option value="quarter">Last 3 Months</option>
              </select>
            </div>

            <!-- Sort By -->
            <div>
              <select
                v-model="filters.sort"
                class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
              >
                <option value="latest">Latest First</option>
                <option value="oldest">Oldest First</option>
                <option value="highest">Highest Score</option>
                <option value="lowest">Lowest Score</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Attempts List -->
        <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
          <!-- Loading State -->
          <div v-if="attempts.data.length === 0 && !loading" class="text-center py-12">
            <AcademicCapIcon class="mx-auto h-16 w-16 text-gray-300 mb-4" />
            <h3 class="text-lg font-semibold text-gray-900 mb-2">No Attempts Yet</h3>
            <p class="text-gray-500 mb-6 max-w-md mx-auto">
              You haven't attempted any exams yet. Start practicing to track your progress.
            </p>
            <Link
              :href="route('student.exam-preps.index')"
              class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-medium rounded-lg hover:from-emerald-600 hover:to-teal-700 transition-all duration-200 shadow-sm hover:shadow-md"
            >
              <AcademicCapIcon class="h-4 w-4 mr-2" />
              Browse Available Exams
            </Link>
          </div>

          <!-- Attempts Table -->
          <div v-else-if="attempts.data.length > 0" class="divide-y divide-gray-200">
            <div
              v-for="attempt in attempts.data"
              :key="attempt.id"
              class="p-6 hover:bg-gray-50 transition-colors duration-150"
            >
              <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <!-- Left Section: Exam Info -->
                <div class="flex-1 min-w-0">
                  <div class="flex items-center space-x-3 mb-3">
                    <h3 class="text-lg font-semibold text-gray-900 truncate">
                      <Link
                        :href="route('student.exam-preps.show', attempt.exam_prep.id)"
                        class="hover:text-emerald-600 transition-colors"
                      >
                        {{ attempt.exam_prep.name }}
                      </Link>
                    </h3>
                    <span
                      :class="[
                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                        attempt.is_passed ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800'
                      ]"
                    >
                      {{ attempt.is_passed ? 'Passed' : 'Failed' }}
                    </span>
                  </div>

                  <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500">
                    <div class="flex items-center">
                      <AcademicCapIcon class="h-4 w-4 mr-1" />
                      <span>{{ attempt.exam_prep.exam_board?.name }}</span>
                    </div>
                    <div class="flex items-center">
                      <QuestionMarkCircleIcon class="h-4 w-4 mr-1" />
                      <span>Attempt {{ attempt.attempt_number }}</span>
                    </div>
                    <div class="flex items-center">
                      <CalendarIcon class="h-4 w-4 mr-1" />
                      <span>{{ formatDate(attempt.completed_at) }}</span>
                    </div>
                    <div class="flex items-center">
                      <ClockIcon class="h-4 w-4 mr-1" />
                      <span>{{ formatTime(attempt.time_spent_seconds) }}</span>
                    </div>
                  </div>
                </div>

                <!-- Right Section: Score & Actions -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                  <!-- Score Display -->
                  <div class="text-center sm:text-right">
                    <div class="text-2xl font-bold text-gray-900">
                      {{ parseFloat(attempt.percentage).toFixed(1) }}%
                    </div>
                    <div class="text-sm text-gray-500">
                      {{ attempt.score }}/{{ calculateTotalPoints(attempt) }} points
                    </div>
                  </div>

                  <!-- Action Buttons -->
                  <div class="flex items-center space-x-2">
                    <Link
                      :href="route('student.exam-preps.view-attempt', attempt.id)"
                      class="inline-flex items-center px-4 py-2 border border-emerald-300 text-emerald-700 font-medium rounded-lg hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors"
                    >
                      <EyeIcon class="h-4 w-4 mr-2" />
                      Details
                    </Link>
                    <Link
                      v-if="attempt.exam_prep.can_attempt"
                      :href="route('student.exam-preps.instructions', attempt.exam_prep.id)"
                      class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-medium rounded-lg hover:from-emerald-600 hover:to-teal-700 transition-all duration-200 shadow-sm hover:shadow-md"
                    >
                      <ArrowPathIcon class="h-4 w-4 mr-2" />
                      Retake
                    </Link>
                  </div>
                </div>
              </div>

              <!-- Progress Bar -->
              <div class="mt-4">
                <div class="flex items-center justify-between text-sm text-gray-600 mb-1">
                  <span>Performance</span>
                  <span>
                    {{ attempt.is_passed ? '✓ Passed' : '✗ Failed' }}
                    (Required: {{ attempt.exam_prep.passing_score }}%)
                  </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                  <div
                    class="h-2.5 rounded-full transition-all duration-500"
                    :class="attempt.is_passed ? 'bg-emerald-500' : 'bg-red-500'"
                    :style="{ width: `${Math.min(parseFloat(attempt.percentage), 100)}%` }"
                  ></div>
                </div>
                <div class="mt-1 flex justify-between text-xs text-gray-500">
                  <span>0%</span>
                  <span>{{ attempt.exam_prep.passing_score }}% (Pass)</span>
                  <span>100%</span>
                </div>
              </div>

              <!-- Quick Stats -->
              <div v-if="attempt.results_breakdown" class="mt-4 grid grid-cols-3 gap-4">
                <div class="text-center p-3 bg-emerald-50 rounded-lg">
                  <div class="text-lg font-bold text-emerald-700">
                    {{ calculateCorrectAnswers(attempt) }}
                  </div>
                  <div class="text-xs text-emerald-600">Correct</div>
                </div>
                <div class="text-center p-3 bg-red-50 rounded-lg">
                  <div class="text-lg font-bold text-red-700">
                    {{ calculateIncorrectAnswers(attempt) }}
                  </div>
                  <div class="text-xs text-red-600">Incorrect</div>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                  <div class="text-lg font-bold text-gray-700">
                    {{ calculateSkippedQuestions(attempt) }}
                  </div>
                  <div class="text-xs text-gray-600">Skipped</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Loading Indicator -->
          <div v-if="loading" class="text-center py-12">
            <div class="inline-flex items-center">
              <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span class="text-gray-600">Loading attempts...</span>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="attempts.data.length > 0" class="mt-6">
          <Pagination :links="attempts.links" />
        </div>

        <!-- Performance Summary -->
        <div v-if="attempts.data.length > 0" class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Performance Chart -->
          <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Performance Trend</h3>
            <div class="space-y-4">
              <div
                v-for="(attempt, index) in recentAttempts"
                :key="attempt.id"
                class="flex items-center"
              >
                <div class="w-8 text-sm text-gray-500 font-medium">#{{ index + 1 }}</div>
                <div class="flex-1 ml-4">
                  <div class="flex items-center justify-between mb-1">
                    <span class="text-sm font-medium text-gray-700 truncate">
                      {{ attempt.exam_prep.name }}
                    </span>
                    <span class="text-sm font-semibold" :class="attempt.is_passed ? 'text-emerald-600' : 'text-red-600'">
                      {{ parseFloat(attempt.percentage).toFixed(1) }}%
                    </span>
                  </div>
                  <div class="w-full bg-gray-200 rounded-full h-2">
                    <div
                      class="h-2 rounded-full"
                      :class="attempt.is_passed ? 'bg-emerald-500' : 'bg-red-500'"
                      :style="{ width: `${Math.min(parseFloat(attempt.percentage), 100)}%` }"
                    ></div>
                  </div>
                  <div class="text-xs text-gray-500 mt-1">
                    {{ formatDate(attempt.completed_at) }}
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Improvement Areas -->
          <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Areas for Improvement</h3>
            <div class="space-y-4">
              <div
                v-for="exam in weakExams"
                :key="exam.id"
                class="p-4 bg-red-50 border border-red-200 rounded-lg"
              >
                <div class="flex items-center justify-between mb-2">
                  <h4 class="font-medium text-red-800">{{ exam.name }}</h4>
                  <span class="text-sm text-red-600">{{ exam.average_score }}% avg</span>
                </div>
                <div class="text-sm text-red-700 mb-2">
                  Attempted {{ exam.attempt_count }} time{{ exam.attempt_count > 1 ? 's' : '' }}
                </div>
                <div class="flex items-center text-xs text-red-600">
                  <ExclamationTriangleIcon class="h-3 w-3 mr-1" />
                  <span>Focus on improving in this area</span>
                </div>
                <div class="mt-3">
                  <Link
                    :href="route('student.exam-preps.instructions', exam.id)"
                    class="inline-flex items-center text-sm font-medium text-red-700 hover:text-red-800"
                  >
                    <ArrowPathIcon class="h-3 w-3 mr-1" />
                    Retake Exam
                  </Link>
                </div>
              </div>

              <div v-if="weakExams.length === 0" class="text-center py-8">
                <CheckCircleIcon class="mx-auto h-12 w-12 text-emerald-400 mb-3" />
                <p class="text-gray-600">Great job! Keep up the good work.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </StudentLayout>
</template>

<script setup>
import { ref, reactive, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import Pagination from '@/Components/Pagination.vue'
import {
  AcademicCapIcon,
  ChartBarIcon,
  CheckCircleIcon,
  TrophyIcon,
  MagnifyingGlassIcon,
  QuestionMarkCircleIcon,
  CalendarIcon,
  ClockIcon,
  EyeIcon,
  ArrowPathIcon,
  ExclamationTriangleIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  attempts: Object,
})

// Filters
const filters = ref({
  search: '',
  status: '',
  date_range: '',
  sort: 'latest',
})

const loading = ref(false)

// Computed properties
const totalAttempts = computed(() => {
  return props.attempts.total || 0
})

const averageScore = computed(() => {
  if (props.attempts.data.length === 0) return 0
  const total = props.attempts.data.reduce((sum, attempt) => {
    return sum + parseFloat(attempt.percentage || 0)
  }, 0)
  return (total / props.attempts.data.length).toFixed(1)
})

const passRate = computed(() => {
  if (props.attempts.data.length === 0) return 0
  const passed = props.attempts.data.filter(a => a.is_passed).length
  return ((passed / props.attempts.data.length) * 100).toFixed(1)
})

const bestScore = computed(() => {
  if (props.attempts.data.length === 0) return 0
  const scores = props.attempts.data.map(a => parseFloat(a.percentage || 0))
  return Math.max(...scores).toFixed(1)
})

const recentAttempts = computed(() => {
  return props.attempts.data.slice(0, 5)
})

const weakExams = computed(() => {
  const examMap = new Map()

  props.attempts.data.forEach(attempt => {
    if (!attempt.exam_prep || parseFloat(attempt.percentage) >= 70) return

    const examId = attempt.exam_prep.id
    if (!examMap.has(examId)) {
      examMap.set(examId, {
        id: examId,
        name: attempt.exam_prep.name,
        total_score: 0,
        attempt_count: 0,
      })
    }

    const exam = examMap.get(examId)
    exam.total_score += parseFloat(attempt.percentage)
    exam.attempt_count++
  })

  return Array.from(examMap.values())
    .map(exam => ({
      ...exam,
      average_score: (exam.total_score / exam.attempt_count).toFixed(1)
    }))
    .filter(exam => exam.average_score < 70)
    .slice(0, 3)
})

// Helper functions
const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
  })
}

const formatTime = (seconds) => {
  if (!seconds) return '0:00'
  const secs = parseInt(seconds) || 0
  const minutes = Math.floor(secs / 60)
  const remainingSeconds = secs % 60
  return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`
}

const calculateTotalPoints = (attempt) => {
  if (attempt.results_breakdown) {
    return attempt.results_breakdown.reduce((sum, result) => sum + (result.points || 1), 0)
  }
  return attempt.score * (100 / parseFloat(attempt.percentage)) || 0
}

const calculateCorrectAnswers = (attempt) => {
  if (!attempt.results_breakdown) return 0
  return attempt.results_breakdown.filter(r => r.is_correct).length
}

const calculateIncorrectAnswers = (attempt) => {
  if (!attempt.results_breakdown) return 0
  return attempt.results_breakdown.filter(r => !r.is_correct && r.user_answer).length
}

const calculateSkippedQuestions = (attempt) => {
  if (!attempt.results_breakdown) return 0
  return attempt.results_breakdown.filter(r => !r.user_answer).length
}

// Watch filters and reload data
watch(filters, () => {
  loading.value = true
  router.get(route('student.exam-preps.my-attempts'), filters.value, {
    preserveState: true,
    replace: true,
    onFinish: () => {
      loading.value = false
    }
  })
}, { deep: true })

// Clear all filters
const clearFilters = () => {
  filters.value = {
    search: '',
    status: '',
    date_range: '',
    sort: 'latest',
  }
}
</script>

<style scoped>
/* Custom styles */
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 150ms;
}

.truncate {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Hover effects */
.hover-lift:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}
</style>
