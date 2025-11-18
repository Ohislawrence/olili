<template>
  <StudentLayout>
    <Head :title="`Quiz Result: ${attempt.quiz.title}`" />

    <div class="py-6">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex justify-between items-start">
            <div>
              <Link
                :href="route('student.quizzes.show', attempt.quiz.id)"
                class="text-sm font-medium text-emerald-600 hover:text-emerald-500 mb-2 inline-block"
              >
                ← Back to Quiz
              </Link>
              <h1 class="text-2xl font-bold text-gray-900">Quiz Result</h1>
              <p class="mt-1 text-sm text-gray-600">
                {{ attempt.quiz.title }} • {{ attempt.quiz.course_outline.module.title }}
              </p>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Main Content -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Result Summary -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100">
              <div class="p-6">
                <div class="text-center">
                  <div
                    :class="[
                      'mx-auto flex items-center justify-center h-16 w-16 rounded-full',
                      attempt.is_passed ? 'bg-emerald-100' : 'bg-red-100'
                    ]"
                  >
                    <CheckCircleIcon
                      v-if="attempt.is_passed"
                      class="h-8 w-8 text-emerald-600"
                    />
                    <XCircleIcon
                      v-else
                      class="h-8 w-8 text-red-600"
                    />
                  </div>
                  <h2 class="mt-4 text-xl font-bold text-gray-900">
                    {{ attempt.is_passed ? 'Quiz Passed!' : 'Quiz Not Passed' }}
                  </h2>
                  <p class="mt-2 text-sm text-gray-600">
                    Attempt #{{ attempt.attempt_number }} •
                    Completed on {{ formatDate(attempt.completed_at) }}
                  </p>

                  <div class="mt-6 grid grid-cols-3 gap-4">
                    <div class="text-center">
                      <p class="text-2xl font-bold text-gray-900">{{ attempt.percentage }}%</p>
                      <p class="text-sm text-gray-600">Score</p>
                    </div>
                    <div class="text-center">
                      <p class="text-2xl font-bold text-gray-900">
                        {{ attempt.score }}/{{ attempt.quiz.questions?.reduce((sum, q) => sum + (q.points || 1), 0) }}
                      </p>
                      <p class="text-sm text-gray-600">Points</p>
                    </div>
                    <div class="text-center">
                      <p class="text-2xl font-bold text-gray-900">{{ formatTimeTaken(attempt.time_taken_seconds) }}</p>
                      <p class="text-sm text-gray-600">Time Taken</p>
                    </div>
                  </div>

                  <div class="mt-6">
                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                      <span>Passing Score: {{ attempt.quiz.passing_score }}%</span>
                      <span>Your Score: {{ attempt.percentage }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                      <div
                        :class="[
                          'h-3 rounded-full transition-all duration-300',
                          attempt.is_passed ? 'bg-emerald-500' : 'bg-red-500'
                        ]"
                        :style="{ width: `${Math.min(attempt.percentage, 100)}%` }"
                      ></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Question Review -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100">
              <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">Question Review</h2>
              </div>
              <div class="p-6 space-y-6">
                <div
                  v-for="(question, index) in attempt.quiz.questions"
                  :key="index"
                  class="border border-gray-200 rounded-xl p-4"
                  :class="{
                    'bg-emerald-50 border-emerald-200': isQuestionCorrect(question, index),
                    'bg-red-50 border-red-200': !isQuestionCorrect(question, index)
                  }"
                >
                  <div class="flex items-start justify-between mb-3">
                    <h3 class="text-sm font-medium text-gray-900">
                      Question {{ index + 1 }}
                    </h3>
                    <span
                      :class="[
                        'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold',
                        isQuestionCorrect(question, index)
                          ? 'bg-emerald-100 text-emerald-800'
                          : 'bg-red-100 text-red-800'
                      ]"
                    >
                      {{ isQuestionCorrect(question, index) ? 'Correct' : 'Incorrect' }}
                    </span>
                  </div>

                  <p class="text-gray-700 mb-4">{{ question.question }}</p>

                  <!-- Options -->
                  <div class="space-y-2">
                    <div
                      v-for="(option, optIndex) in question.options"
                      :key="optIndex"
                      :class="[
                        'p-3 rounded-lg border',
                        isCorrectAnswer(question, option)
                          ? 'bg-emerald-100 border-emerald-300 text-emerald-800'
                          : isUserAnswer(question, index, option)
                          ? 'bg-red-100 border-red-300 text-red-800'
                          : 'bg-gray-50 border-gray-200 text-gray-700'
                      ]"
                    >
                      <div class="flex items-center">
                        <span class="flex-1">{{ option }}</span>
                        <span v-if="isCorrectAnswer(question, option)" class="text-emerald-600 text-sm">
                          ✓ Correct Answer
                        </span>
                        <span v-else-if="isUserAnswer(question, index, option)" class="text-red-600 text-sm">
                          ✗ Your Answer
                        </span>
                      </div>
                    </div>
                  </div>

                  <!-- Explanation -->
                  <div v-if="question.explanation" class="mt-3 p-3 bg-blue-50 rounded-lg">
                    <p class="text-sm font-semibold text-blue-800 mb-1">Explanation:</p>
                    <p class="text-sm text-blue-700">{{ question.explanation }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="space-y-6">
            <!-- Quiz Info -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100">
              <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">Quiz Information</h2>
              </div>
              <div class="p-6 space-y-3">
                <div>
                  <p class="text-sm font-medium text-gray-700">Quiz Title</p>
                  <p class="text-sm text-gray-600">{{ attempt.quiz.title }}</p>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-700">Course</p>
                  <p class="text-sm text-gray-600">{{ attempt.quiz.course_outline.module.title }}</p>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-700">Attempt Number</p>
                  <p class="text-sm text-gray-600">#{{ attempt.attempt_number }}</p>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-700">Completed</p>
                  <p class="text-sm text-gray-600">{{ formatDate(attempt.completed_at) }}</p>
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100">
              <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">Actions</h2>
              </div>
              <div class="p-6 space-y-3">
                <Link
                  :href="route('student.quizzes.show', attempt.quiz.id)"
                  class="w-full flex items-center justify-center px-4 py-2.5 border border-emerald-300 text-sm font-semibold rounded-lg text-emerald-700 bg-white hover:bg-emerald-50 shadow-sm hover:shadow-md transition-all duration-200"
                >
                  View Quiz Details
                </Link>
                <Link
                  v-if="attempt.quiz.can_user_attempt"
                  :href="route('student.quizzes.start', attempt.quiz.id)"
                  method="post"
                  as="button"
                  class="w-full flex items-center justify-center px-4 py-2.5 border border-transparent text-sm font-semibold rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 shadow-md hover:shadow-lg transition-all duration-200"
                >
                  Try Again
                </Link>
                <Link
                  :href="route('student.courses.learn', { course: attempt.quiz.course_outline.module.course.id, topic: attempt.quiz.course_outline.id })"
                  class="w-full flex items-center justify-center px-4 py-2.5 border border-emerald-300 text-sm font-semibold rounded-lg text-emerald-700 bg-white hover:bg-emerald-50 shadow-sm hover:shadow-md transition-all duration-200"
                >
                  Continue Learning
                </Link>
              </div>
            </div>

            <!-- Feedback -->
            <div v-if="attempt.ai_feedback || attempt.manual_feedback" class="bg-white shadow-sm rounded-xl border border-gray-100">
              <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">Feedback</h2>
              </div>
              <div class="p-6">
                <div v-if="attempt.ai_feedback" class="mb-4">
                  <p class="text-sm font-semibold text-gray-700 mb-2">AI Feedback</p>
                  <p class="text-sm text-gray-600">{{ attempt.ai_feedback.overview }}</p>
                </div>
                <div v-if="attempt.manual_feedback">
                  <p class="text-sm font-semibold text-gray-700 mb-2">Tutor Feedback</p>
                  <p class="text-sm text-gray-600">{{ attempt.manual_feedback }}</p>
                </div>
              </div>
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
import { CheckCircleIcon, XCircleIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  attempt: Object,
  questions: Array,
  user_answers: Array,
  score_data: Object,
})

const isQuestionCorrect = (question, index) => {
  const userAnswer = props.user_answers?.[index]
  const correctAnswer = question.correct_answer

  if (question.type === 'multiple_choice') {
    return userAnswer === correctAnswer
  } else if (question.type === 'multiple_answer') {
    if (!Array.isArray(userAnswer) || !Array.isArray(correctAnswer)) return false
    const sortedUser = [...userAnswer].sort()
    const sortedCorrect = [...correctAnswer].sort()
    return JSON.stringify(sortedUser) === JSON.stringify(sortedCorrect)
  }

  return false
}

const isCorrectAnswer = (question, option) => {
  if (question.type === 'multiple_choice') {
    return option === question.correct_answer
  } else if (question.type === 'multiple_answer') {
    return question.correct_answer?.includes(option)
  }
  return false
}

const isUserAnswer = (question, index, option) => {
  const userAnswer = props.user_answers?.[index]

  if (question.type === 'multiple_choice') {
    return option === userAnswer
  } else if (question.type === 'multiple_answer') {
    return userAnswer?.includes(option)
  }
  return false
}

const formatDate = (dateString) => {
  if (!dateString) return 'Not completed'
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: 'numeric',
    minute: 'numeric',
  })
}

const formatTimeTaken = (timeInSeconds) => {
  // Validate input
  if (typeof timeInSeconds !== 'number' || isNaN(timeInSeconds)) {
    console.warn('Invalid input for formatTimeTaken:', timeInSeconds);
    return '0s'; // Default return value for invalid inputs
  }

  // Remove the minus sign and convert to positive integer
  const seconds = Math.abs(timeInSeconds);

  // Calculate minutes and remaining seconds
  const minutes = Math.floor(seconds / 60);
  const remainingSeconds = seconds % 60;

  // Format with leading zero for seconds if needed
  const formattedSeconds = remainingSeconds.toString().padStart(2, '0');

  // Return different formats based on whether we have minutes
  if (minutes === 0) {
    return `${remainingSeconds}s`;
  } else {
    return `${minutes}m ${formattedSeconds}s`;
  }
}
</script>

