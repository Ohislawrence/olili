<!-- resources/js/Pages/Admin/ExamPreps/Index.vue -->
<template>
  <AdminLayout>
    <Head title="AI-Powered Exam Preparations" />

    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="md:flex md:items-center md:justify-between mb-8">
          <div class="flex-1 min-w-0">
            <div class="flex items-center space-x-2">
              <h1 class="text-2xl font-bold text-gray-900">AI Exam Preparations</h1>
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gradient-to-r from-purple-100 to-blue-100 text-purple-800 border border-purple-200">
                <SparklesIcon class="h-3 w-3 mr-1" />
                AI Generated
              </span>
            </div>
            <p class="mt-1 text-sm text-gray-600">
              Create and manage AI-generated practice exams for students
            </p>
          </div>
          <div class="mt-4 md:mt-0 flex space-x-3">
            <Link
              :href="route('admin.exam-preps.create')"
              class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 border border-transparent rounded-lg font-semibold text-white shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200"
            >
              <SparklesIcon class="h-4 w-4 mr-2" />
              Create AI Exam
            </Link>
          </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
          <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-500">Total Exams</p>
                <p class="text-2xl font-bold text-gray-900">{{ stats.total }}</p>
              </div>
              <div class="p-3 bg-emerald-100 rounded-lg">
                <AcademicCapIcon class="h-6 w-6 text-emerald-600" />
              </div>
            </div>
          </div>

          <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-500">Active Exams</p>
                <p class="text-2xl font-bold text-green-600">{{ stats.active }}</p>
              </div>
              <div class="p-3 bg-green-100 rounded-lg">
                <CheckCircleIcon class="h-6 w-6 text-green-600" />
              </div>
            </div>
          </div>

          <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-500">Total Questions</p>
                <p class="text-2xl font-bold text-gray-900">{{ stats.totalQuestions }}</p>
              </div>
              <div class="p-3 bg-blue-100 rounded-lg">
                <QuestionMarkCircleIcon class="h-6 w-6 text-blue-600" />
              </div>
            </div>
          </div>

          <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-500">Student Attempts</p>
                <p class="text-2xl font-bold text-gray-900">{{ stats.totalAttempts }}</p>
              </div>
              <div class="p-3 bg-purple-100 rounded-lg">
                <UserGroupIcon class="h-6 w-6 text-purple-600" />
              </div>
            </div>
          </div>
        </div>

        <!-- Filters -->
        <div class="mb-6 bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
          <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- Search -->
            <div class="md:col-span-2">
              <label for="search" class="sr-only">Search</label>
              <div class="relative rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" />
                </div>
                <input
                  id="search"
                  v-model="filters.search"
                  type="search"
                  placeholder="Search exams by name or description..."
                  class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500"
                />
              </div>
            </div>

            <!-- Status Filter -->
            <div>
              <select
                v-model="filters.status"
                class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500"
              >
                <option value="">All Statuses</option>
                <option
                  v-for="(label, value) in statuses"
                  :key="value"
                  :value="value"
                >
                  {{ label }}
                </option>
              </select>
            </div>

            <!-- Exam Board Filter -->
            <div>
              <select
                v-model="filters.exam_board_id"
                class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500"
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

            <!-- Generation Status Filter -->
            <div>
              <select
                v-model="filters.generation_status"
                class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500"
              >
                <option value="">All Generation Status</option>
                <option value="completed">Completed</option>
                <option value="processing">Processing</option>
                <option value="pending">Pending</option>
                <option value="failed">Failed</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Exam Preps List -->
        <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
          <div v-if="examPreps.data.length > 0" class="divide-y divide-gray-200">
            <div
              v-for="examPrep in examPreps.data"
              :key="examPrep.id"
              class="p-6 hover:bg-gray-50 transition-colors duration-150"
            >
              <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                  <div class="flex items-center space-x-3 mb-2">
                    <Link
                      :href="route('admin.exam-preps.show', examPrep.id)"
                      class="text-lg font-semibold text-gray-900 hover:text-emerald-600 transition-colors"
                    >
                      {{ examPrep.name }}
                    </Link>
                    <StatusBadge :status="examPrep.status" :statuses="statuses" />
                    <GenerationStatusBadge :status="examPrep.content_generation_status" />
                    <span
                      v-if="examPrep.is_public"
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                    >
                      Public
                    </span>
                  </div>

                  <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                    {{ examPrep.description || 'No description provided' }}
                  </p>

                  <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500">
                    <div class="flex items-center">
                      <AcademicCapIcon class="h-4 w-4 mr-1" />
                      <span>{{ examPrep.exam_board?.name || 'No Board' }}</span>
                    </div>

                    <div class="flex items-center">
                      <QuestionMarkCircleIcon class="h-4 w-4 mr-1" />
                      <span>{{ examPrep.total_questions }} questions</span>
                      <span v-if="examPrep.questions_count" class="ml-1 text-xs text-gray-400">
                        ({{ examPrep.questions_count }} generated)
                      </span>
                    </div>

                    <div class="flex items-center">
                      <ClockIcon class="h-4 w-4 mr-1" />
                      <span>{{ examPrep.time_limit_minutes }} min</span>
                    </div>

                    <div class="flex items-center">
                      <UserGroupIcon class="h-4 w-4 mr-1" />
                      <span>{{ examPrep.enrolled_count || 0 }} enrolled</span>
                    </div>

                    <div class="flex items-center">
                      <ChartBarIcon class="h-4 w-4 mr-1" />
                      <span>{{ examPrep.passing_score }}% passing</span>
                    </div>

                    <div v-if="examPrep.content_generation_completed_at" class="flex items-center">
                      <CalendarIcon class="h-4 w-4 mr-1" />
                      <span>Generated {{ formatDate(examPrep.content_generation_completed_at) }}</span>
                    </div>
                  </div>

                  <!-- Difficulty Distribution -->
                  <div v-if="examPrep.generation_summary?.difficulty_distribution" class="mt-3 flex items-center space-x-2">
                    <span class="text-xs text-gray-500">Distribution:</span>
                    <div class="flex space-x-1">
                      <span class="px-2 py-0.5 bg-green-100 text-green-800 text-xs rounded-full">
                        Easy: {{ examPrep.generation_summary.difficulty_distribution.easy || 0 }}
                      </span>
                      <span class="px-2 py-0.5 bg-amber-100 text-amber-800 text-xs rounded-full">
                        Medium: {{ examPrep.generation_summary.difficulty_distribution.medium || 0 }}
                      </span>
                      <span class="px-2 py-0.5 bg-red-100 text-red-800 text-xs rounded-full">
                        Hard: {{ examPrep.generation_summary.difficulty_distribution.hard || 0 }}
                      </span>
                    </div>
                  </div>
                </div>

                <div class="flex items-center space-x-2 ml-4">
                  <Link
                    :href="route('admin.exam-preps.show', examPrep.id)"
                    class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
                  >
                    <EyeIcon class="h-4 w-4 mr-1" />
                    View
                  </Link>
                  <Link
                    :href="route('admin.exam-preps.edit', examPrep.id)"
                    class="inline-flex items-center px-3 py-2 border border-emerald-300 shadow-sm text-sm font-medium rounded-lg text-emerald-700 bg-white hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
                  >
                    <PencilIcon class="h-4 w-4 mr-1" />
                    Edit
                  </Link>
                  <button
                    v-if="examPrep.status === 'draft' && examPrep.content_generation_status === 'completed'"
                    @click="publishExamPrep(examPrep)"
                    class="inline-flex items-center px-3 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
                  >
                    <CheckCircleIcon class="h-4 w-4 mr-1" />
                    Publish
                  </button>
                  <button
                    v-else-if="examPrep.content_generation_status === 'failed'"
                    @click="retryGeneration(examPrep)"
                    class="inline-flex items-center px-3 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500"
                  >
                    <ArrowPathIcon class="h-4 w-4 mr-1" />
                    Retry
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-else class="text-center py-16 px-4">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-r from-emerald-100 to-teal-100 rounded-full mb-4">
              <SparklesIcon class="h-10 w-10 text-emerald-600" />
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No exam preparations yet</h3>
            <p class="text-gray-500 mb-6 max-w-md mx-auto">
              Create your first AI-powered exam preparation. Generate custom questions tailored to your exam board and subject.
            </p>
            <Link
              :href="route('admin.exam-preps.create')"
              class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 border border-transparent rounded-lg font-semibold text-white shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all"
            >
              <SparklesIcon class="h-5 w-5 mr-2" />
              Create Your First AI Exam
            </Link>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="examPreps.data.length > 0" class="mt-6">
          <Pagination :links="examPreps.links" />
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, watch, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import Pagination from '@/Components/Pagination.vue'
import StatusBadge from '@/Components/ExamPrep/StatusBadge.vue'
import GenerationStatusBadge from '@/Components/ExamPrep/GenerationStatusBadge.vue'
import {
  SparklesIcon,
  MagnifyingGlassIcon,
  AcademicCapIcon,
  QuestionMarkCircleIcon,
  ClockIcon,
  UserGroupIcon,
  CheckCircleIcon,
  EyeIcon,
  PencilIcon,
  ArrowPathIcon,
  CalendarIcon,
  ChartBarIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  examPreps: Object,
  filters: Object,
  examBoards: Array,
  subjects: Array,
  statuses: Object,
})

// Filters state
const filters = ref({
  search: props.filters.search || '',
  status: props.filters.status || '',
  exam_board_id: props.filters.exam_board_id || '',
  subject_id: props.filters.subject_id || '',
  generation_status: props.filters.generation_status || '',
})

// Computed stats
const stats = computed(() => {
  return {
    total: props.examPreps.total || 0,
    active: props.examPreps.data?.filter(e => e.status === 'active').length || 0,
    totalQuestions: props.examPreps.total_questions_sum || 0,
    totalAttempts: props.examPreps.total_attempts_sum || 0,
  }
})

// Watch filters
watch(filters, () => {
  router.get(route('admin.exam-preps.index'), filters.value, {
    preserveState: true,
    replace: true,
  })
}, { deep: true })

// Methods
const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
  })
}

const publishExamPrep = async (examPrep) => {
  if (confirm(`Publish "${examPrep.name}"? This will make it available to students.`)) {
    await router.post(route('admin.exam-preps.publish', examPrep.id), {}, {
      preserveScroll: true,
      onSuccess: () => {
        router.reload({ only: ['examPreps'] })
      },
    })
  }
}

const retryGeneration = async (examPrep) => {
  if (confirm(`Retry AI question generation for "${examPrep.name}"?`)) {
    await router.post(route('admin.exam-preps.generate-questions', examPrep.id), {}, {
      preserveScroll: true,
      onSuccess: () => {
        router.reload({ only: ['examPreps'] })
      },
    })
  }
}
</script>
