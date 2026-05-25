<template>
  <StudentLayout>
    <Head :title="`Exam Prep: ${examPrep.name}`" />

    <div class="py-4 sm:py-6">
      <div class="max-w-6xl mx-auto px-3 sm:px-4 lg:px-8">
        <!-- Breadcrumb - Mobile Optimized -->
        <nav class="flex items-center space-x-1 sm:space-x-2 text-xs sm:text-sm text-gray-500 mb-4 sm:mb-6">
          <Link
            :href="route('student.exam-preps.index')"
            class="hover:text-gray-700 transition-colors flex items-center"
          >
            <span class="hidden sm:inline">Exam Preps</span>
            <ChevronLeftIcon class="h-4 w-4 sm:hidden" />
            <span class="sm:hidden">Back</span>
          </Link>
          <ChevronRightIcon class="h-3 w-3 sm:h-4 sm:w-4 hidden sm:block" />
          <span class="text-gray-900 font-medium truncate max-w-[200px] sm:max-w-none">{{ examPrep.name }}</span>
        </nav>

        <!-- Header - Mobile Optimized -->
        <div class="bg-white rounded-lg sm:rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6 sm:mb-8">
          <div class="p-4 sm:p-8">
            <div class="flex flex-col sm:flex-row items-start justify-between mb-4 sm:mb-6">
              <div class="flex-1 w-full">
                <!-- Badges -->
                <div class="flex flex-wrap items-center gap-2 sm:gap-3 mb-3 sm:mb-4">
                  <span class="inline-flex items-center px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm font-semibold bg-emerald-100 text-emerald-800">
                    {{ examPrep.exam_board?.name }}
                  </span>
                  <span
                    v-if="examPrep.subject"
                    class="inline-flex items-center px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm font-semibold bg-blue-100 text-blue-800"
                  >
                    {{ examPrep.subject.name }}
                  </span>
                </div>

                <!-- Title -->
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 mb-2 sm:mb-4">{{ examPrep.name }}</h1>
                <p class="text-sm sm:text-base text-gray-600 mb-4 sm:mb-6">{{ examPrep.description }}</p>
              </div>
            </div>

            <!-- Stats Grid - Mobile Optimized -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-4 mb-4 sm:mb-6">
              <div class="text-center p-3 sm:p-4 bg-gray-50 rounded-lg">
                <div class="text-lg sm:text-2xl font-bold text-gray-900">{{ examPrep.total_questions }}</div>
                <div class="text-xs sm:text-sm text-gray-600">Questions</div>
              </div>
              <div class="text-center p-3 sm:p-4 bg-gray-50 rounded-lg">
                <div class="text-lg sm:text-2xl font-bold text-gray-900">{{ examPrep.time_limit_minutes }}</div>
                <div class="text-xs sm:text-sm text-gray-600">Minutes</div>
              </div>
              <div class="text-center p-3 sm:p-4 bg-gray-50 rounded-lg">
                <div class="text-lg sm:text-2xl font-bold text-gray-900">{{ examPrep.passing_score }}%</div>
                <div class="text-xs sm:text-sm text-gray-600">Passing</div>
              </div>
              <div class="text-center p-3 sm:p-4 bg-gray-50 rounded-lg">
                <div class="text-lg sm:text-2xl font-bold text-gray-900">
                  {{ examPrep.max_attempts === 0 ? '∞' : examPrep.max_attempts }}
                </div>
                <div class="text-xs sm:text-sm text-gray-600">Attempts</div>
              </div>
            </div>

            <!-- Main Action Button - Mobile Optimized -->
            <div class="flex justify-center">
              <button
                v-if="!isEnrolled"
                @click="enroll"
                :disabled="enrolling"
                class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-8 py-3 sm:py-4 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-semibold sm:font-bold rounded-lg sm:rounded-xl transition-all duration-200 shadow-md hover:shadow-lg text-base sm:text-lg min-h-[44px] sm:min-h-[56px]"
              >
                <template v-if="enrolling">
                  <svg class="animate-spin -ml-1 mr-2 sm:mr-3 h-4 w-4 sm:h-5 sm:w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <span class="hidden xs:inline">Enrolling...</span>
                  <span class="xs:hidden">Loading...</span>
                </template>
                <template v-else>
                  <span class="hidden xs:inline">Start Practicing</span>
                  <span class="xs:hidden">Get Started</span>
                  <ArrowRightIcon class="h-4 w-4 sm:h-5 sm:w-5 ml-1 sm:ml-3" />
                </template>
              </button>

              <Link
                v-else
                :href="route('student.exam-preps.instructions', examPrep.id)"
                class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-8 py-3 sm:py-4 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-semibold sm:font-bold rounded-lg sm:rounded-xl transition-all duration-200 shadow-md hover:shadow-lg text-base sm:text-lg min-h-[44px] sm:min-h-[56px]"
              >
                <span class="hidden xs:inline">{{ attemptsCount > 0 ? 'Continue Practicing' : 'Start Exam' }}</span>
                <span class="xs:hidden">{{ attemptsCount > 0 ? 'Continue' : 'Start' }}</span>
                <ArrowRightIcon class="h-4 w-4 sm:h-5 sm:w-5 ml-1 sm:ml-3" />
              </Link>
            </div>

            <!-- Enrollment Status - Mobile Optimized -->
            <div v-if="isEnrolled" class="mt-4 sm:mt-8 p-3 sm:p-6 bg-emerald-50 border border-emerald-200 rounded-lg sm:rounded-xl">
              <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                  <h3 class="text-sm sm:text-base font-semibold text-emerald-900 mb-1">You're enrolled!</h3>
                  <div class="text-xs sm:text-sm text-emerald-700">
                    <p v-if="attemptsCount > 0">
                      {{ attemptsCount }} attempt{{ attemptsCount > 1 ? 's' : '' }}.
                      <span v-if="bestScore > 0">Best: {{ parseFloat(bestScore || 0).toFixed(1) }}%</span>
                    </p>
                    <p v-else>No attempts yet.</p>
                  </div>
                </div>
                <div v-if="attemptsCount > 0" class="flex items-center sm:block">
                  <div class="text-2xl sm:text-3xl font-bold text-emerald-700">{{ parseFloat(bestScore || 0).toFixed(1) }}%</div>
                  <div class="text-xs sm:text-sm text-emerald-600 ml-2 sm:ml-0">Best Score</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Instructions Section - Mobile Optimized -->
        <div class="bg-white rounded-lg sm:rounded-xl border border-gray-200 shadow-sm p-4 sm:p-8 mb-6 sm:mb-8">
          <h2 class="text-lg sm:text-2xl font-bold text-gray-900 mb-4 sm:mb-6">Exam Instructions</h2>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
            <div class="space-y-3 sm:space-y-4">
              <div class="flex items-start">
                <div class="flex-shrink-0">
                  <ClockIcon class="h-5 w-5 sm:h-6 sm:w-6 text-emerald-600 mt-0.5" />
                </div>
                <div class="ml-2 sm:ml-3">
                  <h3 class="text-sm sm:text-base font-semibold text-gray-900">Time Limit</h3>
                  <p class="text-xs sm:text-sm text-gray-600">
                    {{ examPrep.time_limit_minutes }} minutes to complete. Timer starts when you begin.
                  </p>
                </div>
              </div>

              <div class="flex items-start">
                <div class="flex-shrink-0">
                  <QuestionMarkCircleIcon class="h-5 w-5 sm:h-6 sm:w-6 text-emerald-600 mt-0.5" />
                </div>
                <div class="ml-2 sm:ml-3">
                  <h3 class="text-sm sm:text-base font-semibold text-gray-900">Question Format</h3>
                  <p class="text-xs sm:text-sm text-gray-600">
                    {{ examPrep.total_questions }} questions of various types.
                  </p>
                </div>
              </div>
            </div>

            <div class="space-y-3 sm:space-y-4">
              <div class="flex items-start">
                <div class="flex-shrink-0">
                  <CheckCircleIcon class="h-5 w-5 sm:h-6 sm:w-6 text-emerald-600 mt-0.5" />
                </div>
                <div class="ml-2 sm:ml-3">
                  <h3 class="text-sm sm:text-base font-semibold text-gray-900">Passing Score</h3>
                  <p class="text-xs sm:text-sm text-gray-600">
                    Need {{ examPrep.passing_score }}% to pass. Results available immediately.
                  </p>
                </div>
              </div>

              <div class="flex items-start">
                <div class="flex-shrink-0">
                  <AcademicCapIcon class="h-5 w-5 sm:h-6 sm:w-6 text-emerald-600 mt-0.5" />
                </div>
                <div class="ml-2 sm:ml-3">
                  <h3 class="text-sm sm:text-base font-semibold text-gray-900">Attempts</h3>
                  <p class="text-xs sm:text-sm text-gray-600">
                    {{ examPrep.max_attempts === 0 ? 'Unlimited attempts' : `${examPrep.max_attempts} attempt${examPrep.max_attempts > 1 ? 's' : ''}` }}
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Quick Start (for enrolled users) - Mobile Optimized -->
          <div v-if="isEnrolled" class="mt-4 sm:mt-8 pt-4 sm:pt-8 border-t border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
              <div>
                <h3 class="text-sm sm:text-base font-semibold text-gray-900">Ready to begin?</h3>
                <p class="text-xs sm:text-sm text-gray-600">Review instructions and start your exam</p>
              </div>
              <Link
                :href="route('student.exam-preps.instructions', examPrep.id)"
                class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-2.5 sm:py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white text-sm font-medium rounded-lg hover:from-emerald-600 hover:to-teal-700 transition-all duration-200 shadow-sm min-h-[44px]"
              >
                Review Instructions
                <ArrowRightIcon class="h-3.5 w-3.5 sm:h-4 sm:w-4 ml-1.5 sm:ml-2" />
              </Link>
            </div>
          </div>
        </div>

        <!-- Previous Attempts - Mobile Optimized -->
        <div v-if="attempts.length > 0" class="bg-white rounded-lg sm:rounded-xl border border-gray-200 shadow-sm p-4 sm:p-8">
          <h2 class="text-lg sm:text-2xl font-bold text-gray-900 mb-4 sm:mb-6">Your Previous Attempts</h2>

          <div class="space-y-3 sm:space-y-4">
            <div
              v-for="attempt in attempts"
              :key="attempt.id"
              class="flex flex-col xs:flex-row xs:items-center xs:justify-between p-3 sm:p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors gap-3"
            >
              <div class="flex items-center space-x-3 sm:space-x-4">
                <div
                  :class="[
                    'w-10 h-10 sm:w-12 sm:h-12 rounded-full flex items-center justify-center text-white font-bold text-xs sm:text-base flex-shrink-0',
                    attempt.is_passed ? 'bg-emerald-500' : 'bg-red-500'
                  ]"
                >
                  {{ parseFloat(attempt.percentage || 0).toFixed(0) }}%
                </div>
                <div class="flex-1 min-w-0">
                  <div class="font-medium text-sm sm:text-base text-gray-900">
                    Attempt {{ attempt.attempt_number }}
                  </div>
                  <div class="text-xs sm:text-sm text-gray-500 truncate">
                    {{ formatDate(attempt.completed_at) }}
                  </div>
                </div>
              </div>

              <div class="flex items-center justify-between xs:justify-end xs:space-x-4 ml-13 xs:ml-0">
                <div class="text-right">
                  <div class="font-semibold text-sm sm:text-base text-gray-900">
                    {{ formatTime(attempt.time_spent_seconds) }}
                  </div>
                  <div class="text-xs text-gray-500">Time spent</div>
                </div>
                <Link
                  :href="route('student.exam-preps.view-attempt', attempt.id)"
                  class="inline-flex items-center px-3 sm:px-4 py-1.5 sm:py-2 border border-emerald-300 text-emerald-700 text-xs sm:text-sm font-medium rounded-lg hover:bg-emerald-50 transition-colors min-h-[36px] sm:min-h-[44px]"
                >
                  <span class="hidden xs:inline">View Details</span>
                  <span class="xs:hidden">Details</span>
                  <ArrowRightIcon class="h-3 w-3 ml-1 xs:hidden" />
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </StudentLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import {
  ChevronRightIcon,
  ChevronLeftIcon,
  ArrowRightIcon,
  ClockIcon,
  QuestionMarkCircleIcon,
  CheckCircleIcon,
  AcademicCapIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  examPrep: {
    type: Object,
    required: true
  },
  isEnrolled: {
    type: Boolean,
    default: false
  },
  attempts: {
    type: Array,
    default: () => []
  },
  canAttempt: {
    type: Boolean,
    default: true
  },
  attemptsCount: {
    type: Number,
    default: 0
  },
  bestScore: {
    type: Number,
    default: 0
  },
})

const enrolling = ref(false)

// Helper functions
const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  const now = new Date()
  const diffDays = Math.floor((now - date) / (1000 * 60 * 60 * 24))

  if (diffDays === 0) {
    return `Today at ${date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}`
  }
  if (diffDays === 1) {
    return `Yesterday at ${date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}`
  }
  if (diffDays < 7) {
    return `${diffDays} days ago`
  }

  return date.toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  })
}

const formatTime = (seconds) => {
  if (!seconds) return '0:00'
  const minutes = Math.floor(seconds / 60)
  const remainingSeconds = seconds % 60
  return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`
}

// Enroll in exam prep
const enroll = async () => {
  enrolling.value = true

  try {
    await router.post(route('student.exam-preps.enroll', props.examPrep.id), {}, {
      preserveScroll: true,
      onFinish: () => {
        enrolling.value = false
      }
    })
  } catch (error) {
    console.error('Failed to enroll:', error)
    enrolling.value = false
  }
}
</script>

<style scoped>
/* Touch-friendly tap targets */
@media (max-width: 640px) {
  button,
  a,
  [role="button"] {
    min-height: 44px;
  }

  /* Better touch targets for cards */
  .border.rounded-lg {
    cursor: pointer;
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
  .xs\:justify-end {
    justify-content: flex-end;
  }
  .xs\:space-x-4 > :not([hidden]) ~ :not([hidden]) {
    --tw-space-x-reverse: 0;
    margin-right: calc(1rem * var(--tw-space-x-reverse));
    margin-left: calc(1rem * calc(1 - var(--tw-space-x-reverse)));
  }
  .xs\:ml-0 {
    margin-left: 0;
  }
}

/* Truncate text */
.truncate {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

/* ML-13 utility for alignment */
.ml-13 {
  margin-left: 3.25rem;
}
</style>
