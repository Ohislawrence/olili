<template>
  <StudentLayout>
    <Head title="My Quiz Attempts" />

    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-2xl font-bold text-gray-900">My Quiz Attempts</h1>
          <p class="mt-1 text-sm text-gray-600">
            Track your quiz performance and progress
          </p>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <ClipboardDocumentListIcon class="h-8 w-8 text-emerald-600" />
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Attempts</p>
                <p class="text-2xl font-bold text-gray-900">{{ stats.total_attempts }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <ChartBarIcon class="h-8 w-8 text-emerald-600" />
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Average Score</p>
                <p class="text-2xl font-bold text-gray-900">
                  {{ Math.round(stats.average_score) }}%
                </p>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <CheckCircleIcon class="h-8 w-8 text-emerald-600" />
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Passed Quizzes</p>
                <p class="text-2xl font-bold text-gray-900">{{ stats.passed_quizzes }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <TrophyIcon class="h-8 w-8 text-emerald-600" />
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Perfect Scores</p>
                <p class="text-2xl font-bold text-gray-900">{{ stats.perfect_scores }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Filters -->
        <div class="mb-6 bg-white rounded-xl shadow-sm p-4 border border-gray-100">
          <div class="flex flex-col sm:flex-row gap-4">
            <div class="w-full sm:w-48">
              <select
                v-model="filters.status"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2 px-3 text-gray-700"
                @change="filter"
              >
                <option value="">All Status</option>
                <option value="completed">Completed</option>
                <option value="in_progress">In Progress</option>
                <option value="not_started">Not Started</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Quiz Attempts -->
        <div class="bg-white shadow-sm rounded-xl border border-gray-100">
          <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-900">Recent Quiz Attempts</h2>
          </div>
          <div class="p-6">
            <div v-if="quiz_attempts.data.length" class="space-y-4">
              <div
                v-for="attempt in quiz_attempts.data"
                :key="attempt.id"
                class="border border-gray-200 rounded-xl p-5 hover:bg-emerald-50/30 transition-colors duration-200"
              >
                <div class="flex items-center justify-between">
                  <div class="flex-1">
                    <div class="flex items-center justify-between mb-2">
                      <h3 class="text-lg font-bold text-gray-900">
                        {{ attempt.quiz.title }}
                      </h3>
                      <div class="flex items-center gap-2">
                        <span
                          :class="[
                            'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold',
                            attempt.is_passed ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800'
                          ]"
                        >
                          {{ attempt.is_passed ? 'Passed' : 'Failed' }}
                        </span>
                        <span
                          v-if="!attempt.completed_at"
                          class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800"
                        >
                          In Progress
                        </span>
                      </div>
                    </div>

                    <p class="text-sm text-gray-600 mb-2">{{ attempt.quiz.course_outline.module.course.title }} •
                      {{ attempt.quiz.course_outline.module.title }} •
                      Attempt #{{ attempt.attempt_number }}
                    </p>

                    <div class="flex items-center justify-between text-sm text-gray-500">
                      <div class="flex items-center gap-4">
                        <span class="font-medium">Score: {{ attempt.percentage }}%</span>
                        <span>Time: {{ formatTimeTaken(attempt.time_taken_seconds) }}</span>
                      </div>
                      <span v-if="attempt.completed_at" class="font-medium">
                        {{ formatDate(attempt.completed_at) }}
                      </span>
                      <span v-else class="text-yellow-600 font-medium">
                        Started: {{ formatDate(attempt.started_at) }}
                      </span>
                    </div>
                  </div>

                  <div class="ml-4 flex-shrink-0">
                    <Link
                      :href="attempt.completed_at
                        ? route('student.quiz-attempts.result', attempt.id)
                        : route('student.quizzes.continue.attempt', { quiz: attempt.quiz.id, attempt: attempt.id })"
                      class="inline-flex items-center px-4 py-2 border border-emerald-300 text-sm font-semibold rounded-lg text-emerald-700 bg-white hover:bg-emerald-50 shadow-sm hover:shadow-md transition-all duration-200"
                    >
                      {{ attempt.completed_at ? 'View Result' : 'Continue' }}
                    </Link>
                  </div>
                </div>
              </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-12">
              <ClipboardDocumentListIcon class="mx-auto h-12 w-12 text-emerald-400" />
              <h3 class="mt-2 text-sm font-semibold text-gray-900">No quiz attempts</h3>
              <p class="mt-1 text-sm text-gray-500">
                Get started by taking a quiz from one of your courses.
              </p>
              <div class="mt-6">
                <Link
                  :href="route('student.courses.index')"
                  class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-semibold rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 shadow-md hover:shadow-lg transition-all duration-200"
                >
                  View Courses
                </Link>
              </div>
            </div>
          </div>

          <!-- Pagination -->
          <div v-if="quiz_attempts.data.length" class="px-6 py-4 border-t border-gray-200">
            <Pagination :links="quiz_attempts.links" />
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
import Pagination from '@/Components/Pagination.vue'
import {
  ClipboardDocumentListIcon,
  ChartBarIcon,
  CheckCircleIcon,
  TrophyIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  quiz_attempts: Object,
  stats: Object,
  filters: Object,
})

const filters = ref({
  status: props.filters.status || '',
})

const filter = () => {
  router.get(route('student.quizzes.index'), filters.value, {
    preserveState: true,
    replace: true,
  })
}

const formatDate = (dateString) => {
  if (!dateString) return 'Not completed'
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'short',
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
