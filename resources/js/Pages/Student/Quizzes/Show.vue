<template>
  <StudentLayout>
    <Head :title="quiz.title" />

    <div class="py-6">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex justify-between items-start">
            <div>
              <Link
                :href="route('student.quizzes.index')"
                class="text-sm font-medium text-emerald-600 hover:text-emerald-500 mb-2 inline-block"
              >
                ← Back to Quizzes
              </Link>
              <h1 class="text-2xl font-bold text-gray-900">{{ quiz.title }}</h1>
              <p class="mt-1 text-sm text-gray-600">
                {{ quiz.course_outline.module.course.title }} • {{ quiz.course_outline.module.title }}
              </p>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Quiz Info -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Quiz Details -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100">
              <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">Quiz Details</h2>
              </div>
              <div class="p-6">
                <p class="text-gray-600 mb-6">{{ quiz.description }}</p>

                <div class="grid grid-cols-2 gap-4 text-sm">
                  <div class="p-4 bg-gray-50 rounded-lg">
                    <span class="font-medium text-gray-700 block mb-1">Questions:</span>
                    <p class="text-gray-600">{{ quiz.questions?.length }}</p>
                  </div>
                  <div class="p-4 bg-gray-50 rounded-lg">
                    <span class="font-medium text-gray-700 block mb-1">Time Limit:</span>
                    <p class="text-gray-600">
                      {{ quiz.time_limit_minutes ? `${quiz.time_limit_minutes} minutes` : 'No limit' }}
                    </p>
                  </div>
                  <div class="p-4 bg-gray-50 rounded-lg">
                    <span class="font-medium text-gray-700 block mb-1">Passing Score:</span>
                    <p class="text-gray-600">{{ quiz.passing_score }}%</p>
                  </div>
                  <div class="p-4 bg-gray-50 rounded-lg">
                    <span class="font-medium text-gray-700 block mb-1">Max Attempts:</span>
                    <p class="text-gray-600">{{ quiz.max_attempts === 0 ? 'Unlimited' : quiz.max_attempts }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Start Quiz Section -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100">
              <div class="p-6">
                <div class="text-center">
                  <div v-if="can_attempt" class="space-y-4">
                    <p class="text-lg font-bold text-gray-900">Ready to take the quiz?</p>
                    <p class="text-sm text-gray-600">
                      This is attempt #{{ next_attempt_number }}
                      <span v-if="best_attempt">
                        • Your best score: {{ best_attempt.percentage }}%
                      </span>
                    </p>
                    <Link
                      :href="route('student.quizzes.start', quiz.id)"
                      method="post"
                      as="button"
                      class="inline-flex items-center px-6 py-3 border border-transparent text-base font-semibold rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 shadow-md hover:shadow-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                    >
                      Start Quiz
                    </Link>
                  </div>
                  <div v-else class="space-y-4">
                    <p class="text-lg font-bold text-gray-900">Maximum Attempts Reached</p>
                    <p class="text-sm text-gray-600">
                      You have reached the maximum number of attempts for this quiz.
                    </p>
                    <div v-if="best_attempt" class="bg-emerald-50 border border-emerald-200 rounded-lg p-4">
                      <p class="text-sm text-emerald-800 font-medium">
                        Your best score: {{ best_attempt.percentage }}%
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="space-y-6">
            <!-- Previous Attempts -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100">
              <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">Previous Attempts</h2>
              </div>
              <div class="p-6">
                <div v-if="previous_attempts.length" class="space-y-3">
                  <div
                    v-for="attempt in previous_attempts"
                    :key="attempt.id"
                    class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-emerald-50/50 transition-colors"
                  >
                    <div>
                      <p class="text-sm font-medium text-gray-900">
                        Attempt #{{ attempt.attempt_number }}
                      </p>
                      <p class="text-xs text-gray-500">
                        {{ formatDate(attempt.completed_at) }}
                      </p>
                    </div>
                    <div class="text-right">
                      <p
                        :class="[
                          'text-sm font-semibold',
                          attempt.is_passed ? 'text-emerald-600' : 'text-red-600'
                        ]"
                      >
                        {{ attempt.percentage }}%
                      </p>
                      <p class="text-xs text-gray-500">
                        {{ attempt.time_taken_formatted }}
                      </p>
                    </div>
                  </div>
                </div>
                <div v-else class="text-center py-4">
                  <p class="text-sm text-gray-500">No previous attempts</p>
                </div>
              </div>
            </div>

            <!-- Quiz Instructions -->
            <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-4">
              <h3 class="text-sm font-semibold text-emerald-800 mb-2">Quiz Instructions</h3>
              <ul class="text-sm text-emerald-700 space-y-1 list-disc list-inside">
                <li>Read each question carefully</li>
                <li>Answer all questions before submitting</li>
                <li v-if="quiz.time_limit_minutes">You have {{ quiz.time_limit_minutes }} minutes to complete</li>
                <li>You need {{ quiz.passing_score }}% to pass</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </StudentLayout>
</template>

<script setup>
import StudentLayout from '@/Layouts/StudentLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
  quiz: Object,
  previous_attempts: Array,
  can_attempt: Boolean,
  best_attempt: Object,
  next_attempt_number: Number,
})

const formatDate = (dateString) => {
  if (!dateString) return 'Not completed'
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    hour: 'numeric',
    minute: 'numeric',
  })
}
</script>
