<!-- resources/js/Pages/Student/ExamPreps/Index.vue -->
<template>
  <StudentLayout>
    <Head title="Exam Preparation" />

    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-gray-900">Exam Preparation</h1>
          <p class="mt-2 text-lg text-gray-600">
            Practice for your exams with realistic mock tests
          </p>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <!-- Total Attempts -->
          <div class="bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl p-6 text-white">
            <div class="flex items-center">
              <AcademicCapIcon class="h-8 w-8 mr-4 opacity-90" />
              <div>
                <p class="text-sm opacity-90">Total Attempts</p>
                <p class="text-2xl font-bold">{{ myAttempts.length }}</p>
              </div>
            </div>
          </div>

          <!-- Best Score -->
          <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl p-6 text-white">
            <div class="flex items-center">
              <TrophyIcon class="h-8 w-8 mr-4 opacity-90" />
              <div>
                <p class="text-sm opacity-90">Best Score</p>
                <p class="text-2xl font-bold">{{ bestScore }}%</p>
              </div>
            </div>
          </div>

          <!-- Recent Activity -->
          <div class="bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl p-6 text-white">
            <div class="flex items-center">
              <ClockIcon class="h-8 w-8 mr-4 opacity-90" />
              <div>
                <p class="text-sm opacity-90">Recent Activity</p>
                <p class="text-2xl font-bold">{{ recentActivity }}</p>
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
                  placeholder="Search exam preps..."
                  class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                />
              </div>
            </div>

            <!-- Exam Board Filter -->
            <div>
              <select
                v-model="filters.exam_board_id"
                class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
              >
                <option value="">All Exam Boards</option>
                <option
                  v-for="examBoard in examBoards"
                  :key="examBoard.id"
                  :value="examBoard.id"
                >
                  {{ examBoard.name }}
                </option>
              </select>
            </div>

            <!-- Subject Filter -->
            <div>
              <select
                v-model="filters.subject_id"
                class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
              >
                <option value="">All Subjects</option>
                <option
                  v-for="subject in subjects"
                  :key="subject.id"
                  :value="subject.id"
                >
                  {{ subject.name }}
                </option>
              </select>
            </div>

            <!-- Clear Filters -->
            <div>
              <button
                @click="clearFilters"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:ring-2 focus:ring-emerald-500"
              >
                Clear Filters
              </button>
            </div>
          </div>
        </div>

        <!-- My Recent Attempts -->
        <div v-if="myAttempts.length > 0" class="mb-8">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-gray-900">My Recent Attempts</h2>
            <Link
              :href="route('student.exam-preps.my-attempts')"
              class="text-emerald-600 hover:text-emerald-700 font-medium text-sm"
            >
              View all attempts →
            </Link>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div
              v-for="attempt in myAttempts"
              :key="attempt.id"
              class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm hover:shadow-md transition-shadow"
            >
              <div class="flex items-start justify-between mb-3">
                <div>
                  <h3 class="font-semibold text-gray-900">{{ attempt.exam_prep.name }}</h3>
                  <p class="text-sm text-gray-500">{{ formatDate(attempt.completed_at) }}</p>
                </div>
                <span
                  :class="[
                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                    attempt.is_passed ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800'
                  ]"
                >
                  {{ attempt.is_passed ? 'Passed' : 'Failed' }}
                </span>
              </div>
              <div class="flex items-center justify-between">
                <div class="text-sm">
                  <span class="text-gray-600">Score:</span>
                  <span class="ml-2 font-bold text-gray-900">{{ parseFloat(attempt.percentage).toFixed(1) }}%</span>
                </div>
                <Link
                  :href="route('student.exam-preps.view-attempt', attempt.id)"
                  class="text-emerald-600 hover:text-emerald-700 text-sm font-medium"
                >
                  View Details
                </Link>
              </div>
            </div>
          </div>
        </div>

        <!-- Available Exam Preps -->
        <div>
          <h2 class="text-xl font-bold text-gray-900 mb-4">Available Practice Exams</h2>

          <div v-if="examPreps.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div
              v-for="examPrep in examPreps.data"
              :key="examPrep.id"
              class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1"
            >
              <!-- Header -->
              <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                  <div>
                    <div class="flex items-center space-x-2 mb-2">
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                        {{ examPrep.exam_board?.name }}
                      </span>
                      <span
                        v-if="examPrep.subject"
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                      >
                        {{ examPrep.subject.name }}
                      </span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ examPrep.name }}</h3>
                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ examPrep.description }}</p>
                  </div>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-2 gap-3 text-sm mb-4">
                  <div class="flex items-center text-gray-600">
                    <QuestionMarkCircleIcon class="h-4 w-4 mr-2" />
                    <span>{{ examPrep.total_questions }} questions</span>
                  </div>
                  <div class="flex items-center text-gray-600">
                    <ClockIcon class="h-4 w-4 mr-2" />
                    <span>{{ examPrep.time_limit_minutes }} mins</span>
                  </div>
                  <div class="flex items-center text-gray-600">
                    <CheckCircleIcon class="h-4 w-4 mr-2" />
                    <span>{{ examPrep.passing_score }}% to pass</span>
                  </div>
                  <div class="flex items-center text-gray-600">
                    <UserGroupIcon class="h-4 w-4 mr-2" />
                    <span>{{ examPrep.enrolled_count }} enrolled</span>
                  </div>
                </div>

                <!-- Progress Bar -->
                <div v-if="examPrep.average_score > 0" class="mb-4">
                  <div class="flex justify-between text-sm text-gray-600 mb-1">
                    <span>Average Score</span>
                    <span class="font-semibold">{{ parseFloat(examPrep.average_score).toFixed(1) }}%</span>
                  </div>
                  <div class="w-full bg-gray-200 rounded-full h-2">
                    <div
                      class="bg-emerald-500 h-2 rounded-full transition-all duration-300"
                      :style="{ width: `${examPrep.average_score}%` }"
                    ></div>
                  </div>
                </div>
              </div>

              <!-- Footer -->
              <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                <div class="flex justify-between items-center">
                  <div class="text-sm text-gray-600">
                    {{ examPrep.max_attempts === 0 ? 'Unlimited attempts' : `${examPrep.max_attempts} attempts max` }}
                  </div>
                  <Link
                    :href="route('student.exam-preps.show', examPrep.id)"
                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-medium rounded-lg hover:from-emerald-600 hover:to-teal-700 transition-all duration-200 shadow-sm hover:shadow-md text-sm"
                  >
                    View Details
                    <ArrowRightIcon class="h-4 w-4 ml-2" />
                  </Link>
                </div>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-else class="text-center py-12 bg-white rounded-xl border border-gray-200">
            <AcademicCapIcon class="mx-auto h-16 w-16 text-gray-300 mb-4" />
            <h3 class="text-lg font-semibold text-gray-900 mb-2">No Exam Preps Available</h3>
            <p class="text-gray-500 mb-6 max-w-md mx-auto">
              There are no exam preparations available at the moment. Please check back later.
            </p>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="examPreps.data.length > 0" class="mt-8">
          <Pagination :links="examPreps.links" />
        </div>
      </div>
    </div>
  </StudentLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import Pagination from '@/Components/Pagination.vue'
import {
  AcademicCapIcon,
  TrophyIcon,
  ClockIcon,
  MagnifyingGlassIcon,
  QuestionMarkCircleIcon,
  CheckCircleIcon,
  UserGroupIcon,
  ArrowRightIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  examPreps: Object,
  filters: Object,
  examBoards: Array,
  subjects: Array,
  myAttempts: Array,
})

const filters = ref({
  search: props.filters.search || '',
  exam_board_id: props.filters.exam_board_id || '',
  subject_id: props.filters.subject_id || '',
})

// Computed properties
const bestScore = computed(() => {
  if (props.myAttempts.length === 0) return 0
  return Math.max(...props.myAttempts.map(a => a.percentage))
})

const recentActivity = computed(() => {
  if (props.myAttempts.length === 0) return 'No attempts'

  const lastAttempt = props.myAttempts[0]
  const daysAgo = Math.floor((new Date() - new Date(lastAttempt.completed_at)) / (1000 * 60 * 60 * 24))

  if (daysAgo === 0) return 'Today'
  if (daysAgo === 1) return 'Yesterday'
  return `${daysAgo} days ago`
})

// Helper functions
const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
  })
}

const clearFilters = () => {
  filters.value = {
    search: '',
    exam_board_id: '',
    subject_id: '',
  }
}

// Watch filters and reload data
watch(filters, () => {
  router.get(route('student.exam-preps.index'), filters.value, {
    preserveState: true,
    replace: true,
  })
}, { deep: true })
</script>
