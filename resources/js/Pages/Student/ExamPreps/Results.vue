<!-- resources/js/Pages/Student/ExamPreps/Results.vue -->
<template>
  <StudentLayout>
    <Head :title="`Results: ${examPrep.name}`" />

    <div class="py-6">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
          <h1 class="text-3xl font-bold text-gray-900">Exam Results</h1>
          <p class="mt-2 text-lg text-gray-600">{{ examPrep.name }}</p>
        </div>

        <!-- Results Card -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-lg overflow-hidden mb-8">
          <!-- Result Header -->
          <div :class="[
            'p-8 text-center text-white',
            attempt.is_passed ? 'bg-gradient-to-r from-emerald-500 to-teal-600' : 'bg-gradient-to-r from-red-500 to-pink-600'
          ]">
            <div class="mb-6">
              <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-white/20 backdrop-blur-sm border-4 border-white/30">
                <span class="text-3xl font-bold">{{ parseFloat($props.attempt.percentage || 0).toFixed(0) }}%</span>
              </div>
            </div>

            <h2 class="text-2xl font-bold mb-2">
              {{ attempt.is_passed ? 'Congratulations!' : 'Keep Practicing!' }}
            </h2>
            <p class="opacity-90">
              You scored {{ attempt.score }} out of {{ totalPoints }} points
            </p>
          </div>

          <!-- Detailed Results -->
          <div class="p-8">
            <!-- Stats Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
              <div class="text-center p-4 bg-gray-50 rounded-lg">
                <div class="text-2xl font-bold text-gray-900">{{ correctAnswers }}</div>
                <div class="text-sm text-gray-600">Correct</div>
              </div>
              <div class="text-center p-4 bg-gray-50 rounded-lg">
                <div class="text-2xl font-bold text-gray-900">{{ incorrectAnswers }}</div>
                <div class="text-sm text-gray-600">Incorrect</div>
              </div>
              <div class="text-center p-4 bg-gray-50 rounded-lg">
                <div class="text-2xl font-bold text-gray-900">{{ skippedQuestions }}</div>
                <div class="text-sm text-gray-600">Skipped</div>
              </div>
              <div class="text-center p-4 bg-gray-50 rounded-lg">
                <div class="text-2xl font-bold text-gray-900">{{ formatTime(attempt.time_spent_seconds) }}</div>
                <div class="text-sm text-gray-600">Time Taken</div>
              </div>
            </div>

            <!-- Passing Status -->
            <div class="mb-8">
              <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">Passing Score: {{ examPrep.passing_score }}%</span>
                <span class="text-sm font-medium text-gray-700">Your Score: {{ parseFloat($props.attempt.percentage || 0).toFixed(0) }}%</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-4">
                <div
                  class="h-4 rounded-full transition-all duration-500"
                  :class="attempt.is_passed ? 'bg-emerald-500' : 'bg-red-500'"
                  :style="{ width: `${Math.min(attempt.percentage, 100)}%` }"
                ></div>
              </div>
              <div class="mt-1 flex justify-between text-sm text-gray-600">
                <span>0%</span>
                <span>{{ examPrep.passing_score }}%</span>
                <span>100%</span>
              </div>
            </div>

            <!-- Performance Summary -->
            <div class="mb-8">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Performance Summary</h3>
              <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                  <span class="text-gray-700">Attempt Number</span>
                  <span class="font-semibold text-gray-900">{{ attempt.attempt_number }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                  <span class="text-gray-700">Completed On</span>
                  <span class="font-semibold text-gray-900">{{ formatDate(attempt.completed_at) }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                  <span class="text-gray-700">Exam Duration</span>
                  <span class="font-semibold text-gray-900">{{ examPrep.time_limit_minutes }} minutes</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                  <span class="text-gray-700">Time Used</span>
                  <span class="font-semibold text-gray-900">{{ formatTime(attempt.time_spent_seconds) }}</span>
                </div>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4">
              <Link
                :href="route('student.exam-preps.show', examPrep.id)"
                class="flex-1 inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors"
              >
                <ArrowLeftIcon class="h-4 w-4 mr-2" />
                Back to Exam
              </Link>

              <button
                v-if="canRetake"
                @click="retakeExam"
                class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-medium rounded-lg transition-colors shadow-sm hover:shadow-md"
              >
                <ArrowPathIcon class="h-4 w-4 mr-2" />
                Retake Exam (Attempt {{ nextAttemptNumber }})
              </button>

              <Link
                :href="route('student.exam-preps.my-attempts')"
                class="flex-1 inline-flex items-center justify-center px-6 py-3 border border-emerald-300 text-emerald-700 font-medium rounded-lg hover:bg-emerald-50 transition-colors"
              >
                <AcademicCapIcon class="h-4 w-4 mr-2" />
                View All Attempts
              </Link>
            </div>
          </div>
        </div>

        <!-- Question Review -->
        <div v-if="attempt.results_breakdown" class="bg-white rounded-xl border border-gray-200 shadow-sm p-8">
          <h3 class="text-xl font-bold text-gray-900 mb-6">Question Review</h3>

          <div class="space-y-6">
            <div
              v-for="(result, index) in attempt.results_breakdown"
              :key="index"
              :class="[
                'p-6 border rounded-xl',
                result.is_correct ? 'border-emerald-200 bg-emerald-50' : 'border-red-200 bg-red-50'
              ]"
            >
              <div class="flex items-start justify-between mb-4">
                <div>
                  <h4 class="font-semibold text-gray-900">Question {{ index + 1 }}</h4>
                  <span :class="[
                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-2',
                    result.is_correct ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800'
                  ]">
                    {{ result.is_correct ? 'Correct' : 'Incorrect' }}
                  </span>
                </div>
                <span class="text-sm text-gray-600">{{ result.points_earned }} / {{ result.points }} points</span>
              </div>

              <p class="text-gray-700 mb-4">{{ result.question_text }}</p>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <span class="block text-sm font-medium text-gray-500 mb-1">Your Answer:</span>
                  <p :class="[
                    'p-3 rounded-lg border',
                    result.is_correct ? 'border-emerald-300 bg-emerald-25 text-emerald-700' : 'border-red-300 bg-red-25 text-red-700'
                  ]">
                    {{ result.user_answer || 'No answer' }}
                  </p>
                </div>

                <div v-if="!result.is_correct">
                  <span class="block text-sm font-medium text-gray-500 mb-1">Correct Answer:</span>
                  <p class="p-3 rounded-lg border border-emerald-300 bg-emerald-25 text-emerald-700">
                    {{ result.correct_answer }}
                  </p>
                </div>
              </div>

              <div v-if="result.explanation" class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <span class="block text-sm font-medium text-blue-700 mb-1">Explanation:</span>
                <p class="text-blue-600">{{ result.explanation }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- AI Feedback -->
        <div v-if="attempt.ai_feedback" class="mt-8 bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-200 rounded-xl p-8">
          <h3 class="text-xl font-bold text-gray-900 mb-4">AI Feedback</h3>
          <div class="prose prose-purple max-w-none">
            <p class="text-gray-700">{{ attempt.ai_feedback }}</p>
          </div>
        </div>

        <!-- Tips for Improvement -->
        <div v-if="!attempt.is_passed" class="mt-8 bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-xl p-8">
          <h3 class="text-xl font-bold text-gray-900 mb-4">Tips for Improvement</h3>
          <div class="space-y-3">
            <div class="flex items-start">
              <LightBulbIcon class="h-5 w-5 text-amber-600 mt-1 mr-3 flex-shrink-0" />
              <span class="text-gray-700">Review the questions you got wrong and understand the correct answers</span>
            </div>
            <div class="flex items-start">
              <ClockIcon class="h-5 w-5 text-amber-600 mt-1 mr-3 flex-shrink-0" />
              <span class="text-gray-700">Practice time management to ensure you can answer all questions</span>
            </div>
            <div class="flex items-start">
              <BookOpenIcon class="h-5 w-5 text-amber-600 mt-1 mr-3 flex-shrink-0" />
              <span class="text-gray-700">Study the related topics before attempting the exam again</span>
            </div>
            <div class="flex items-start">
              <CheckCircleIcon class="h-5 w-5 text-amber-600 mt-1 mr-3 flex-shrink-0" />
              <span class="text-gray-700">Take your time to read each question carefully before answering</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </StudentLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import {
  ArrowLeftIcon,
  ArrowPathIcon,
  AcademicCapIcon,
  LightBulbIcon,
  ClockIcon,
  BookOpenIcon,
  CheckCircleIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  examPrep: Object,
  attempt: Object,
  canRetake: Boolean,
  nextAttemptNumber: Number,
})

// Computed properties
const totalPoints = computed(() => {
  return props.attempt.questions?.reduce((sum, q) => sum + (q.points || 1), 0) || 0
})

const correctAnswers = computed(() => {
  return props.attempt.results_breakdown?.filter(r => r.is_correct).length || 0
})

const incorrectAnswers = computed(() => {
  return props.attempt.results_breakdown?.filter(r => !r.is_correct && r.user_answer).length || 0
})

const skippedQuestions = computed(() => {
  return props.attempt.results_breakdown?.filter(r => !r.user_answer).length || 0
})

// Helper functions
const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'long',
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

// Retake exam
const retakeExam = async () => {
  await router.visit(route('student.exam-preps.instructions', props.examPrep.id))
}
</script>
