<!-- resources/js/Pages/Admin/Courses/Show.vue -->
<template>
  <AdminLayout>
    <Head :title="`Course - ${course.title}`" />

    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex justify-between items-center">
            <div>
              <h1 class="text-3xl font-bold text-gray-900">{{ course.title }}</h1>
              <p class="mt-2 text-gray-600">
                {{ course.description }}
              </p>
              <div class="mt-2 flex items-center space-x-4">
                <span
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
                  :class="getStatusClass(course.status)"
                >
                  {{ course.status }}
                </span>
                <span class="text-sm text-gray-500 capitalize">{{ course.subject }} • {{ course.level }}</span>
              </div>
            </div>
            <div class="flex space-x-3">
              <Link
                :href="route('admin.courses.edit', course.id)"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
              >
                Edit Course
              </Link>
              <Link
                :href="route('admin.courses.index')"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
              >
                Back to Courses
              </Link>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Main Content -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Progress Overview -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Progress Overview</h3>
              </div>
              <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                  <div>
                    <h4 class="text-sm font-medium text-gray-500">Overall Progress</h4>
                    <p class="text-2xl font-bold text-gray-900">
                      {{ Math.round(course.progress_percentage) }}%
                    </p>
                  </div>
                  <div class="text-right">
                    <h4 class="text-sm font-medium text-gray-500">Completed</h4>
                    <p class="text-2xl font-bold text-gray-900">
                      {{ course.completed_outlines_count }}/{{ course.outlines_count }}
                    </p>
                  </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-4">
                  <div
                    class="bg-blue-600 h-4 rounded-full transition-all duration-300"
                    :style="{ width: `${course.progress_percentage}%` }"
                  ></div>
                </div>
              </div>
            </div>

            <!-- Course Outline -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Course Outline</h3>
              </div>
              <div class="p-6">
                <div class="space-y-4">
                  <div
                    v-for="outline in course.outlines"
                    :key="outline.id"
                    class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50"
                  >
                    <div class="flex items-center space-x-4">
                      <div
                        class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center"
                        :class="getOutlineIconClass(outline.type)"
                      >
                        <component
                          :is="getOutlineIcon(outline.type)"
                          class="h-4 w-4 text-white"
                        />
                      </div>
                      <div>
                        <h4 class="text-sm font-medium text-gray-900">{{ outline.title }}</h4>
                        <p class="text-sm text-gray-500">{{ outline.description }}</p>
                        <div class="flex items-center space-x-4 mt-1">
                          <span class="text-xs text-gray-400 capitalize">{{ outline.type }}</span>
                          <span class="text-xs text-gray-400">
                            {{ outline.estimated_duration_minutes }} minutes
                          </span>
                        </div>
                      </div>
                    </div>
                    <div class="flex items-center space-x-3">
                      <span
                        v-if="outline.is_completed"
                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800"
                      >
                        Completed
                      </span>
                      <span
                        v-else
                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                      >
                        Pending
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Learning Objectives -->
            <div v-if="course.learning_objectives?.length" class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Learning Objectives</h3>
              </div>
              <div class="p-6">
                <ul class="space-y-2">
                  <li
                    v-for="(objective, index) in course.learning_objectives"
                    :key="index"
                    class="flex items-start"
                  >
                    <CheckCircleIcon class="h-5 w-5 text-green-500 mr-3 mt-0.5 flex-shrink-0" />
                    <span class="text-sm text-gray-700">{{ objective }}</span>
                  </li>
                </ul>
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="space-y-6">
            <!-- Student Information -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Student Information</h3>
              </div>
              <div class="p-6">
                <div class="flex items-center space-x-3 mb-4">
                  <div class="flex-shrink-0">
                    <div class="h-10 w-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                      {{ course.student_profile.user.name.charAt(0).toUpperCase() }}
                    </div>
                  </div>
                  <div>
                    <h4 class="text-sm font-medium text-gray-900">
                      {{ course.student_profile.user.name }}
                    </h4>
                    <p class="text-sm text-gray-500">
                      {{ course.student_profile.user.email }}
                    </p>
                  </div>
                </div>
                <dl class="space-y-2">
                  <div>
                    <dt class="text-xs text-gray-500">Current Level</dt>
                    <dd class="text-sm text-gray-900 capitalize">
                      {{ course.student_profile.current_level }}
                    </dd>
                  </div>
                  <div>
                    <dt class="text-xs text-gray-500">Target Level</dt>
                    <dd class="text-sm text-gray-900 capitalize">
                      {{ course.student_profile.target_level }}
                    </dd>
                  </div>
                  <div>
                    <dt class="text-xs text-gray-500">Weekly Study Hours</dt>
                    <dd class="text-sm text-gray-900">
                      {{ course.student_profile.weekly_study_hours }} hours
                    </dd>
                  </div>
                </dl>
              </div>
            </div>

            <!-- Course Details -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Course Details</h3>
              </div>
              <div class="p-6">
                <dl class="space-y-3">
                  <div>
                    <dt class="text-xs text-gray-500">Start Date</dt>
                    <dd class="text-sm text-gray-900">
                      {{ formatDate(course.start_date) }}
                    </dd>
                  </div>
                  <div>
                    <dt class="text-xs text-gray-500">Target Completion</dt>
                    <dd class="text-sm text-gray-900">
                      {{ formatDate(course.target_completion_date) }}
                    </dd>
                  </div>
                  <div>
                    <dt class="text-xs text-gray-500">Estimated Duration</dt>
                    <dd class="text-sm text-gray-900">
                      {{ course.estimated_duration_hours }} hours
                    </dd>
                  </div>
                  <div>
                    <dt class="text-xs text-gray-500">AI Model Used</dt>
                    <dd class="text-sm text-gray-900">
                      {{ course.ai_model_used }}
                    </dd>
                  </div>
                  <div v-if="course.exam_board">
                    <dt class="text-xs text-gray-500">Exam Board</dt>
                    <dd class="text-sm text-gray-900">
                      {{ course.exam_board.name }}
                    </dd>
                  </div>
                </dl>
              </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
              </div>
              <div class="p-6 space-y-3">
                <button
                  @click="regenerateOutline"
                  class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                >
                  <ArrowPathIcon class="h-4 w-4 mr-2" />
                  Regenerate Outline
                </button>
                <button
                  v-if="course.status !== 'completed'"
                  @click="markAsCompleted"
                  class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700"
                >
                  <CheckCircleIcon class="h-4 w-4 mr-2" />
                  Mark as Completed
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import {
  AcademicCapIcon,
  DocumentTextIcon,
  QuestionMarkCircleIcon,
  CheckCircleIcon,
  ArrowPathIcon,
} from '@heroicons/vue/24/outline'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  course: Object,
})

const getStatusClass = (status) => {
  const classes = {
    active: 'bg-green-100 text-green-800',
    completed: 'bg-blue-100 text-blue-800',
    draft: 'bg-gray-100 text-gray-800',
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getOutlineIcon = (type) => {
  const icons = {
    module: AcademicCapIcon,
    topic: DocumentTextIcon,
    quiz: QuestionMarkCircleIcon,
  }
  return icons[type] || DocumentTextIcon
}

const getOutlineIconClass = (type) => {
  const classes = {
    module: 'bg-blue-500',
    topic: 'bg-green-500',
    quiz: 'bg-purple-500',
  }
  return classes[type] || 'bg-gray-500'
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

const regenerateOutline = () => {
  if (confirm('Are you sure you want to regenerate the course outline? This will replace the current outline.')) {
    router.post(route('admin.courses.regenerate-outline', props.course.id))
  }
}

const markAsCompleted = () => {
  if (confirm('Mark this course as completed?')) {
    router.patch(route('admin.courses.mark-completed', props.course.id))
  }
}
</script>
