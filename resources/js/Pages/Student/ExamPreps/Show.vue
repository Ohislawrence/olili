<!-- resources/js/Pages/Student/ExamPreps/Show.vue -->
<template>
  <StudentLayout>
    <Head :title="`Exam Prep: ${examPrep.name}`" />

    <div class="py-6">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-6">
          <Link
            :href="route('student.exam-preps.index')"
            class="hover:text-gray-700 transition-colors"
          >
            Exam Preps
          </Link>
          <ChevronRightIcon class="h-4 w-4" />
          <span class="text-gray-900 font-medium">{{ examPrep.name }}</span>
        </nav>

        <!-- Header -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-8">
          <div class="p-8">
            <div class="flex items-start justify-between mb-6">
              <div class="flex-1">
                <div class="flex items-center space-x-3 mb-4">
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-emerald-100 text-emerald-800">
                    {{ examPrep.exam_board?.name }}
                  </span>
                  <span
                    v-if="examPrep.subject"
                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800"
                  >
                    {{ examPrep.subject.name }}
                  </span>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ examPrep.name }}</h1>
                <p class="text-lg text-gray-600 mb-6">{{ examPrep.description }}</p>
              </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
              <div class="text-center p-4 bg-gray-50 rounded-lg">
                <div class="text-2xl font-bold text-gray-900">{{ examPrep.total_questions }}</div>
                <div class="text-sm text-gray-600">Questions</div>
              </div>
              <div class="text-center p-4 bg-gray-50 rounded-lg">
                <div class="text-2xl font-bold text-gray-900">{{ examPrep.time_limit_minutes }}</div>
                <div class="text-sm text-gray-600">Minutes</div>
              </div>
              <div class="text-center p-4 bg-gray-50 rounded-lg">
                <div class="text-2xl font-bold text-gray-900">{{ examPrep.passing_score }}%</div>
                <div class="text-sm text-gray-600">Passing Score</div>
              </div>
              <div class="text-center p-4 bg-gray-50 rounded-lg">
                <div class="text-2xl font-bold text-gray-900">
                  {{ examPrep.max_attempts === 0 ? '∞' : examPrep.max_attempts }}
                </div>
                <div class="text-sm text-gray-600">Attempts</div>
              </div>
            </div>

            <!-- Main Action Button -->
            <div class="flex justify-center">
              <button
                v-if="!isEnrolled"
                @click="enroll"
                :disabled="enrolling"
                class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl text-lg"
              >
                <template v-if="enrolling">
                  <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Enrolling...
                </template>
                <template v-else>
                  Start Practicing
                  <ArrowRightIcon class="h-5 w-5 ml-3" />
                </template>
              </button>

              <Link
                v-else
                :href="route('student.exam-preps.instructions', examPrep.id)"
                class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl text-lg"
              >
                {{ attemptsCount > 0 ? 'Continue Practicing' : 'Start Exam' }}
                <ArrowRightIcon class="h-5 w-5 ml-3" />
              </Link>
            </div>

            <!-- Additional Info -->
            <div v-if="isEnrolled" class="mt-8 p-6 bg-emerald-50 border border-emerald-200 rounded-xl">
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="font-semibold text-emerald-900 mb-2">You're enrolled in this exam prep!</h3>
                  <div class="text-emerald-700">
                    <p v-if="attemptsCount > 0">
                      You've completed {{ attemptsCount }} attempt{{ attemptsCount > 1 ? 's' : '' }}.
                      <span v-if="bestScore > 0">Best score: {{ bestScore.toFixed(1) }}%</span>
                    </p>
                    <p v-else>You haven't attempted this exam yet.</p>
                  </div>
                </div>
                <div v-if="attemptsCount > 0" class="text-right">
                  <div class="text-3xl font-bold text-emerald-700">{{ bestScore.toFixed(1) }}%</div>
                  <div class="text-sm text-emerald-600">Best Score</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Instructions Section -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-8 mb-8">
          <h2 class="text-2xl font-bold text-gray-900 mb-6">Exam Instructions</h2>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
              <div class="flex items-start">
                <div class="flex-shrink-0">
                  <ClockIcon class="h-6 w-6 text-emerald-600 mt-1" />
                </div>
                <div class="ml-3">
                  <h3 class="font-semibold text-gray-900">Time Limit</h3>
                  <p class="text-gray-600">
                    You have {{ examPrep.time_limit_minutes }} minutes to complete the exam.
                    The timer starts when you begin and continues even if you close the window.
                  </p>
                </div>
              </div>

              <div class="flex items-start">
                <div class="flex-shrink-0">
                  <QuestionMarkCircleIcon class="h-6 w-6 text-emerald-600 mt-1" />
                </div>
                <div class="ml-3">
                  <h3 class="font-semibold text-gray-900">Question Format</h3>
                  <p class="text-gray-600">
                    The exam contains {{ examPrep.total_questions }} questions of various types
                    including multiple choice, true/false, and short answer.
                  </p>
                </div>
              </div>
            </div>

            <div class="space-y-4">
              <div class="flex items-start">
                <div class="flex-shrink-0">
                  <CheckCircleIcon class="h-6 w-6 text-emerald-600 mt-1" />
                </div>
                <div class="ml-3">
                  <h3 class="font-semibold text-gray-900">Passing Score</h3>
                  <p class="text-gray-600">
                    You need to score at least {{ examPrep.passing_score }}% to pass this exam.
                    Your results will be available immediately after submission.
                  </p>
                </div>
              </div>

              <div class="flex items-start">
                <div class="flex-shrink-0">
                  <AcademicCapIcon class="h-6 w-6 text-emerald-600 mt-1" />
                </div>
                <div class="ml-3">
                  <h3 class="font-semibold text-gray-900">Attempts</h3>
                  <p class="text-gray-600">
                    {{ examPrep.max_attempts === 0 ? 'You can attempt this exam unlimited times.' : `You have ${examPrep.max_attempts} attempt${examPrep.max_attempts > 1 ? 's' : ''} available.` }}
                    Your best score will be recorded.
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Quick Start (for enrolled users) -->
          <div v-if="isEnrolled" class="mt-8 pt-8 border-t border-gray-200">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
              <div>
                <h3 class="font-semibold text-gray-900">Ready to begin?</h3>
                <p class="text-gray-600">Review instructions and start your exam</p>
              </div>
              <Link
                :href="route('student.exam-preps.instructions', examPrep.id)"
                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-medium rounded-lg hover:from-emerald-600 hover:to-teal-700 transition-all duration-200 shadow-sm hover:shadow-md"
              >
                Review Instructions
                <ArrowRightIcon class="h-4 w-4 ml-2" />
              </Link>
            </div>
          </div>
        </div>

        <!-- Previous Attempts -->
        <div v-if="attempts.length > 0" class="bg-white rounded-xl border border-gray-200 shadow-sm p-8">
          <h2 class="text-2xl font-bold text-gray-900 mb-6">Your Previous Attempts</h2>

          <div class="space-y-4">
            <div
              v-for="attempt in attempts"
              :key="attempt.id"
              class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
            >
              <div class="flex items-center space-x-4">
                <div
                  :class="[
                    'w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg',
                    attempt.is_passed ? 'bg-emerald-500' : 'bg-red-500'
                  ]"
                >
                  {{ attempt.percentage.toFixed(0) }}%
                </div>
                <div>
                  <div class="font-medium text-gray-900">
                    Attempt {{ attempt.attempt_number }}
                  </div>
                  <div class="text-sm text-gray-500">
                    {{ formatDate(attempt.completed_at) }}
                  </div>
                </div>
              </div>
              <div class="flex items-center space-x-4">
                <div class="text-right">
                  <div class="font-semibold text-gray-900">
                    {{ formatTime(attempt.time_spent_seconds) }}
                  </div>
                  <div class="text-sm text-gray-500">Time spent</div>
                </div>
                <Link
                  :href="route('student.exam-preps.view-attempt', attempt.id)"
                  class="inline-flex items-center px-4 py-2 border border-emerald-300 text-emerald-700 font-medium rounded-lg hover:bg-emerald-50 transition-colors"
                >
                  View Details
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
  ArrowRightIcon,
  ClockIcon,
  QuestionMarkCircleIcon,
  CheckCircleIcon,
  AcademicCapIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  examPrep: Object,
  isEnrolled: Boolean,
  attempts: Array,
  canAttempt: Boolean,
  attemptsCount: Number,
  bestScore: Number,
})

const enrolling = ref(false)

// Helper functions
const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
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
    })
  } catch (error) {
    console.error('Failed to enroll:', error)
    enrolling.value = false
  }
}
</script>
