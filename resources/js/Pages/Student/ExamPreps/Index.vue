<template>
  <StudentLayout>
    <Head title="Exam Preparation" />

    <div class="py-4 sm:py-6">
      <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8">
        <!-- Header - Mobile Optimized -->
        <div class="mb-6 sm:mb-8">
          <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Exam Preparation</h1>
          <p class="mt-1 sm:mt-2 text-sm sm:text-base text-gray-600">
            Practice for your exams with realistic mock tests
          </p>
        </div>

        <!-- Stats Overview - Mobile Optimized -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-6 mb-6 sm:mb-8">
          <!-- Total Attempts -->
          <div class="bg-gradient-to-r from-emerald-500 to-teal-600 rounded-lg sm:rounded-xl p-4 sm:p-6 text-white">
            <div class="flex items-center">
              <AcademicCapIcon class="h-6 w-6 sm:h-8 sm:w-8 mr-3 sm:mr-4 opacity-90 flex-shrink-0" />
              <div>
                <p class="text-xs sm:text-sm opacity-90">Total Attempts</p>
                <p class="text-xl sm:text-2xl font-bold">{{ myAttempts.length }}</p>
              </div>
            </div>
          </div>

          <!-- Best Score -->
          <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg sm:rounded-xl p-4 sm:p-6 text-white">
            <div class="flex items-center">
              <TrophyIcon class="h-6 w-6 sm:h-8 sm:w-8 mr-3 sm:mr-4 opacity-90 flex-shrink-0" />
              <div>
                <p class="text-xs sm:text-sm opacity-90">Best Score</p>
                <p class="text-xl sm:text-2xl font-bold">{{ bestScore }}%</p>
              </div>
            </div>
          </div>

          <!-- Recent Activity -->
          <div class="bg-gradient-to-r from-purple-500 to-pink-600 rounded-lg sm:rounded-xl p-4 sm:p-6 text-white">
            <div class="flex items-center">
              <ClockIcon class="h-6 w-6 sm:h-8 sm:w-8 mr-3 sm:mr-4 opacity-90 flex-shrink-0" />
              <div>
                <p class="text-xs sm:text-sm opacity-90">Recent Activity</p>
                <p class="text-xl sm:text-2xl font-bold">{{ recentActivity }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Filters - Mobile Optimized -->
        <div class="mb-6 sm:mb-8 bg-white rounded-lg sm:rounded-xl border border-gray-200 p-3 sm:p-4 shadow-sm">
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-4">
            <!-- Search -->
            <div>
              <label for="search" class="sr-only">Search</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <MagnifyingGlassIcon class="h-4 w-4 sm:h-5 sm:w-5 text-gray-400" />
                </div>
                <input
                  id="search"
                  v-model="filters.search"
                  type="search"
                  placeholder="Search exam preps..."
                  class="block w-full pl-9 sm:pl-10 pr-3 py-2.5 sm:py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                />
              </div>
            </div>

            <!-- Exam Board Filter -->
            <div>
              <select
                v-model="filters.exam_board_id"
                class="block w-full px-3 py-2.5 sm:py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white"
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
                class="block w-full px-3 py-2.5 sm:py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white"
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
                class="w-full px-4 py-2.5 sm:py-3 border border-gray-300 rounded-lg text-gray-700 text-sm hover:bg-gray-50 focus:ring-2 focus:ring-emerald-500 transition-colors min-h-[44px]"
              >
                Clear Filters
              </button>
            </div>
          </div>
        </div>

        <!-- My Recent Attempts - Mobile Optimized -->
        <div v-if="myAttempts.length > 0" class="mb-6 sm:mb-8">
          <div class="flex items-center justify-between mb-3 sm:mb-4">
            <h2 class="text-base sm:text-xl font-bold text-gray-900">My Recent Attempts</h2>
            <Link
              :href="route('student.exam-preps.my-attempts')"
              class="text-emerald-600 hover:text-emerald-700 text-xs sm:text-sm font-medium"
            >
              View all
              <span class="hidden sm:inline">attempts</span>
              <span class="sm:hidden">→</span>
            </Link>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
            <div
              v-for="attempt in myAttempts.slice(0, 3)"
              :key="attempt.id"
              class="bg-white rounded-lg sm:rounded-xl border border-gray-200 p-3 sm:p-4 shadow-sm hover:shadow-md transition-shadow"
            >
              <div class="flex items-start justify-between mb-2">
                <div class="flex-1 min-w-0 pr-2">
                  <h3 class="text-sm sm:text-base font-semibold text-gray-900 truncate">{{ attempt.exam_prep.name }}</h3>
                  <p class="text-xs sm:text-sm text-gray-500">{{ formatDate(attempt.completed_at) }}</p>
                </div>
                <span
                  :class="[
                    'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium flex-shrink-0',
                    attempt.is_passed ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800'
                  ]"
                >
                  {{ attempt.is_passed ? 'Passed' : 'Failed' }}
                </span>
              </div>

              <div class="flex items-center justify-between mt-2 pt-2 border-t border-gray-100">
                <div class="text-xs sm:text-sm">
                  <span class="text-gray-600">Score:</span>
                  <span class="ml-1 sm:ml-2 font-bold text-gray-900">{{ parseFloat(attempt.percentage).toFixed(1) }}%</span>
                </div>
                <Link
                  :href="route('student.exam-preps.view-attempt', attempt.id)"
                  class="text-emerald-600 hover:text-emerald-700 text-xs sm:text-sm font-medium min-h-[36px] sm:min-h-[44px] inline-flex items-center"
                >
                  Details
                  <ArrowRightIcon class="h-3 w-3 ml-1" />
                </Link>
              </div>
            </div>
          </div>

          <!-- View More Button for Mobile -->
          <div v-if="myAttempts.length > 3" class="mt-3 sm:hidden">
            <Link
              :href="route('student.exam-preps.my-attempts')"
              class="w-full inline-flex items-center justify-center px-4 py-2.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors"
            >
              View All {{ myAttempts.length }} Attempts
            </Link>
          </div>
        </div>

        <!-- Available Exam Preps -->
        <div>
          <h2 class="text-base sm:text-xl font-bold text-gray-900 mb-3 sm:mb-4">Available Practice Exams</h2>

          <div v-if="examPreps.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            <div
              v-for="examPrep in examPreps.data"
              :key="examPrep.id"
              class="bg-white rounded-lg sm:rounded-xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1"
            >
              <!-- Header -->
              <div class="p-4 sm:p-6">
                <div class="mb-3">
                  <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 mb-2">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                      {{ examPrep.exam_board?.name }}
                    </span>
                    <span
                      v-if="examPrep.subject"
                      class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                    >
                      {{ examPrep.subject.name }}
                    </span>
                  </div>
                  <h3 class="text-base sm:text-lg font-bold text-gray-900 mb-1 sm:mb-2">{{ examPrep.name }}</h3>
                  <p class="text-xs sm:text-sm text-gray-600 line-clamp-2">{{ examPrep.description }}</p>
                </div>

                <!-- Stats - Mobile Optimized -->
                <div class="grid grid-cols-2 gap-2 sm:gap-3 text-xs sm:text-sm mb-3 sm:mb-4">
                  <div class="flex items-center text-gray-600">
                    <QuestionMarkCircleIcon class="h-3.5 w-3.5 sm:h-4 sm:w-4 mr-1 sm:mr-2 flex-shrink-0" />
                    <span>{{ examPrep.total_questions }} q</span>
                  </div>
                  <div class="flex items-center text-gray-600">
                    <ClockIcon class="h-3.5 w-3.5 sm:h-4 sm:w-4 mr-1 sm:mr-2 flex-shrink-0" />
                    <span>{{ examPrep.time_limit_minutes }} min</span>
                  </div>
                  <div class="flex items-center text-gray-600">
                    <CheckCircleIcon class="h-3.5 w-3.5 sm:h-4 sm:w-4 mr-1 sm:mr-2 flex-shrink-0" />
                    <span>{{ examPrep.passing_score }}%</span>
                  </div>
                  <div class="flex items-center text-gray-600">
                    <UserGroupIcon class="h-3.5 w-3.5 sm:h-4 sm:w-4 mr-1 sm:mr-2 flex-shrink-0" />
                    <span>{{ examPrep.enrolled_count }}</span>
                  </div>
                </div>

                <!-- Progress Bar -->
                <div v-if="examPrep.average_score > 0" class="mb-3 sm:mb-4">
                  <div class="flex justify-between text-xs text-gray-600 mb-1">
                    <span>Avg Score</span>
                    <span class="font-semibold">{{ parseFloat(examPrep.average_score).toFixed(1) }}%</span>
                  </div>
                  <div class="w-full bg-gray-200 rounded-full h-1.5 sm:h-2">
                    <div
                      class="bg-emerald-500 h-1.5 sm:h-2 rounded-full transition-all duration-300"
                      :style="{ width: `${examPrep.average_score}%` }"
                    ></div>
                  </div>
                </div>
              </div>

              <!-- Footer - Mobile Optimized -->
              <div class="px-4 sm:px-6 py-3 sm:py-4 bg-gray-50 border-t border-gray-100">
                <div class="flex flex-col xs:flex-row xs:items-center xs:justify-between gap-2">
                  <div class="text-xs text-gray-600">
                    {{ examPrep.max_attempts === 0 ? 'Unlimited' : `${examPrep.max_attempts} max` }}
                  </div>
                  <Link
                    :href="route('student.exam-preps.show', examPrep.id)"
                    class="w-full xs:w-auto inline-flex items-center justify-center px-3 sm:px-4 py-2 sm:py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white text-xs sm:text-sm font-medium rounded-lg hover:from-emerald-600 hover:to-teal-700 transition-all duration-200 shadow-sm min-h-[36px] sm:min-h-[44px]"
                  >
                    <span>View Details</span>
                    <ArrowRightIcon class="h-3 w-3 sm:h-4 sm:w-4 ml-1.5" />
                  </Link>
                </div>
              </div>
            </div>
          </div>

          <!-- Empty State - Mobile Optimized -->
          <div v-else class="text-center py-8 sm:py-12 bg-white rounded-lg sm:rounded-xl border border-gray-200">
            <AcademicCapIcon class="mx-auto h-12 w-12 sm:h-16 sm:w-16 text-gray-300 mb-3 sm:mb-4" />
            <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-1 sm:mb-2">No Exam Preps Available</h3>
            <p class="text-sm text-gray-500 mb-4 sm:mb-6 max-w-md mx-auto px-4">
              There are no exam preparations available at the moment. Please check back later.
            </p>

            <!-- Quick action to clear filters -->
            <button
              v-if="hasActiveFilters"
              @click="clearFilters"
              class="inline-flex items-center px-4 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors min-h-[44px]"
            >
              Clear Filters
            </button>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="examPreps.data.length > 0" class="mt-6 sm:mt-8">
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
  examPreps: {
    type: Object,
    required: true,
    default: () => ({ data: [], links: [] })
  },
  filters: {
    type: Object,
    default: () => ({ search: '', exam_board_id: '', subject_id: '' })
  },
  examBoards: {
    type: Array,
    default: () => []
  },
  subjects: {
    type: Array,
    default: () => []
  },
  myAttempts: {
    type: Array,
    default: () => []
  },
})

const filters = ref({
  search: props.filters.search || '',
  exam_board_id: props.filters.exam_board_id || '',
  subject_id: props.filters.subject_id || '',
})

// Computed properties
const bestScore = computed(() => {
  if (props.myAttempts.length === 0) return 0
  return Math.max(...props.myAttempts.map(a => parseFloat(a.percentage) || 0))
})

const recentActivity = computed(() => {
  if (props.myAttempts.length === 0) return 'No attempts'

  const lastAttempt = props.myAttempts[0]
  if (!lastAttempt.completed_at) return 'No attempts'

  const daysAgo = Math.floor((new Date() - new Date(lastAttempt.completed_at)) / (1000 * 60 * 60 * 24))

  if (daysAgo === 0) return 'Today'
  if (daysAgo === 1) return 'Yesterday'
  return `${daysAgo} days ago`
})

const hasActiveFilters = computed(() => {
  return filters.value.search || filters.value.exam_board_id || filters.value.subject_id
})

// Helper functions
const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
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

<style scoped>
/* Line clamp utilities */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Touch-friendly tap targets */
@media (max-width: 640px) {
  button,
  a,
  select,
  input,
  [role="button"] {
    min-height: 44px;
  }

  /* Prevent zoom on input focus for iOS */
  input[type="search"],
  select {
    font-size: 16px;
  }
}

/* Extra small devices */
@media (min-width: 480px) {
  .xs\:inline {
    display: inline;
  }
  .xs\:hidden {
    display: none;
  }
  .xs\:flex-row {
    flex-direction: row;
  }
  .xs\:items-center {
    align-items: center;
  }
  .xs\:w-auto {
    width: auto;
  }
}

/* Smooth transitions */
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 200ms;
}

/* Truncate text */
.truncate {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
</style>
