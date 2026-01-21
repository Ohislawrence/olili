<!-- resources/js/Pages/Admin/ExamPreps/Show.vue -->
<template>
  <AdminLayout>
    <Head :title="`Exam Prep: ${examPrep.name}`" />

    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex items-center justify-between">
            <div class="flex-1 min-w-0">
              <div class="flex items-center space-x-3 mb-2">
                <h1 class="text-2xl font-bold text-gray-900">{{ examPrep.name }}</h1>
                <span
                  :class="[
                    'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold',
                    examPrep.status === 'active' ? 'bg-emerald-100 text-emerald-800' :
                    examPrep.status === 'draft' ? 'bg-gray-100 text-gray-800' :
                    'bg-amber-100 text-amber-800'
                  ]"
                >
                  {{ examPrep.status }}
                </span>
                <span
                  v-if="examPrep.is_public"
                  class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800"
                >
                  Public
                </span>
              </div>
              <p class="text-gray-600 mb-4">{{ examPrep.description }}</p>
              <div class="flex items-center space-x-4 text-sm text-gray-500">
                <div class="flex items-center">
                  <AcademicCapIcon class="h-4 w-4 mr-1" />
                  <span>{{ examPrep.exam_board?.name }}</span>
                </div>
                <div class="flex items-center">
                  <QuestionMarkCircleIcon class="h-4 w-4 mr-1" />
                  <span>{{ examPrep.total_questions }} questions</span>
                </div>
                <div class="flex items-center">
                  <ClockIcon class="h-4 w-4 mr-1" />
                  <span>{{ examPrep.time_limit_minutes }} minutes</span>
                </div>
                <div class="flex items-center">
                  <CheckCircleIcon class="h-4 w-4 mr-1" />
                  <span>{{ examPrep.passing_score }}% passing</span>
                </div>
                <div class="flex items-center">
                  <UserGroupIcon class="h-4 w-4 mr-1" />
                  <span>{{ examPrep.enrolled_count }} enrolled</span>
                </div>
              </div>
            </div>
            <div class="flex items-center space-x-3">
              <Link
                :href="route('admin.exam-preps.edit', examPrep.id)"
                class="inline-flex items-center px-4 py-2 border border-emerald-300 shadow-sm text-sm font-medium rounded-lg text-emerald-700 bg-white hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
              >
                Edit
              </Link>
              <button
                v-if="examPrep.status === 'draft'"
                @click="publishExamPrep"
                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
              >
                Publish
              </button>
            </div>
          </div>
        </div>

        <!-- Tabs -->
        <div class="mb-6">
          <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8">
              <button
                v-for="tab in tabs"
                :key="tab.id"
                @click="activeTab = tab.id"
                :class="[
                  'py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200',
                  activeTab === tab.id
                    ? 'border-emerald-500 text-emerald-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                ]"
              >
                {{ tab.name }}
              </button>
            </nav>
          </div>
        </div>

        <!-- Tab Content -->
        <div>
          <!-- Statistics Tab -->
          <div v-if="activeTab === 'statistics'">
            <ExamPrepStats
              :exam-prep="examPrep"
              :initial-stats="statistics"
              :initial-recent-attempts="recentAttempts"
              :initial-top-performers="topPerformers"
            />
          </div>

          <!-- Questions Tab -->
          <div v-else-if="activeTab === 'questions'">
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
              <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Questions</h3>
                <button
                  @click="regenerateQuestions"
                  :disabled="regenerating"
                  class="inline-flex items-center px-4 py-2 border border-emerald-300 shadow-sm text-sm font-medium rounded-lg text-emerald-700 bg-white hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 disabled:opacity-50"
                >
                  <ArrowPathIcon class="h-4 w-4 mr-2" :class="{ 'animate-spin': regenerating }" />
                  Regenerate Questions
                </button>
              </div>
              <!-- Questions list here -->
            </div>
          </div>

          <!-- Attempts Tab -->
          <div v-else-if="activeTab === 'attempts'">
            <!-- Attempts list here -->
          </div>

          <!-- Settings Tab -->
          <div v-else-if="activeTab === 'settings'">
            <!-- Settings form here -->
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import ExamPrepStats from '@/Components/ExamPrepStats.vue'
import {
  AcademicCapIcon,
  QuestionMarkCircleIcon,
  ClockIcon,
  CheckCircleIcon,
  UserGroupIcon,
  ArrowPathIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  examPrep: Object,
  statistics: Object,
  recentAttempts: Array,
  topPerformers: Array,
})

const activeTab = ref('statistics')
const regenerating = ref(false)

const tabs = [
  { id: 'statistics', name: 'Statistics' },
  { id: 'questions', name: 'Questions' },
  { id: 'attempts', name: 'Attempts' },
  { id: 'settings', name: 'Settings' },
]

const publishExamPrep = async () => {
  if (confirm(`Publish "${props.examPrep.name}"? This will make it available to students.`)) {
    await router.post(route('admin.exam-preps.publish', props.examPrep.id), {}, {
      preserveScroll: true,
      onSuccess: () => {
        router.reload()
      },
    })
  }
}

const regenerateQuestions = async () => {
  if (confirm('Regenerate all questions? This will replace all current questions.')) {
    regenerating.value = true

    try {
      await router.post(route('admin.exam-preps.generate-questions', props.examPrep.id), {}, {
        preserveScroll: true,
        onSuccess: () => {
          router.reload({ only: ['examPrep'] })
        },
        onFinish: () => {
          regenerating.value = false
        }
      })
    } catch (error) {
      regenerating.value = false
      console.error('Failed to regenerate questions:', error)
    }
  }
}
</script>
