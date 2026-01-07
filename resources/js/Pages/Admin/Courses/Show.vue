<!-- resources/js/Pages/Admin/Courses/Show.vue -->
<template>
  <AdminLayout>
    <Head :title="`Course - ${course.title}`" />

    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex justify-between items-start">
            <div class="flex-1">
              <div class="flex items-center space-x-3">
                <Link
                  :href="route('admin.courses.index')"
                  class="text-gray-400 hover:text-gray-600"
                >
                  <ArrowLeftIcon class="h-5 w-5" />
                </Link>
                <div>
                  <h1 class="text-3xl font-bold text-gray-900">{{ course.title }}</h1>
                  <p class="mt-2 text-gray-600">
                    {{ course.description }}
                  </p>
                </div>
              </div>
              <div class="mt-2 flex items-center space-x-4">
                <span
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
                  :class="getStatusClass(course.status)"
                >
                  {{ course.status }}
                </span>
                <span class="text-sm text-gray-500 capitalize">{{ course.subject }} • {{ course.level }}</span>
                <span v-if="course.exam_board" class="text-sm text-gray-500">
                  Exam Board: {{ course.exam_board.name }}
                </span>
              </div>
            </div>
            <div class="flex space-x-3">
              <Link
                :href="route('admin.courses.edit', course.id)"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                <PencilIcon class="h-4 w-4 mr-2" />
                Edit Course
              </Link>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Main Content -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Course Stats -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Course Statistics</h3>
              </div>
              <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                  <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600">{{ course.current_enrollment || 0 }}</div>
                    <div class="text-sm text-blue-800">Enrolled Students</div>
                  </div>
                  <div class="text-center p-4 bg-green-50 rounded-lg">
                    <div class="text-2xl font-bold text-green-600">{{ completedEnrollmentsCount }}</div>
                    <div class="text-sm text-green-800">Completed</div>
                  </div>
                  <div class="text-center p-4 bg-amber-50 rounded-lg">
                    <div class="text-2xl font-bold text-amber-600">{{ activeEnrollmentsCount }}</div>
                    <div class="text-sm text-amber-800">Active</div>
                  </div>
                  <div class="text-center p-4 bg-purple-50 rounded-lg">
                    <div class="text-2xl font-bold text-purple-600">{{ course.modules?.length || 0 }}</div>
                    <div class="text-sm text-purple-800">Modules</div>
                  </div>
                </div>

                <!-- Enrollment Progress Chart -->
                <div v-if="enrollments.length > 0" class="mt-6">
                  <h4 class="text-sm font-medium text-gray-900 mb-4">Enrollment Progress Distribution</h4>
                  <div class="space-y-3">
                    <div
                      v-for="enrollment in enrollments.slice(0, 5)"
                      :key="enrollment.id"
                      class="flex items-center"
                    >
                      <div class="w-32 flex-shrink-0">
                        <div class="text-sm font-medium text-gray-900 truncate">
                          {{ enrollment.user?.name || 'Unknown Student' }}
                        </div>
                      </div>
                      <div class="flex-1 ml-4">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                          <div
                            class="h-2 rounded-full transition-all duration-300"
                            :class="getProgressColor(enrollment.progress_percentage)"
                            :style="{ width: `${enrollment.progress_percentage}%` }"
                          ></div>
                        </div>
                      </div>
                      <div class="w-16 text-right">
                        <span class="text-sm font-medium text-gray-900">
                          {{ Math.round(enrollment.progress_percentage) }}%
                        </span>
                      </div>
                    </div>

                    <div v-if="enrollments.length > 5" class="text-center">
                      <button
                        @click="viewAllEnrollments"
                        class="text-sm text-blue-600 hover:text-blue-800"
                      >
                        View all {{ enrollments.length }} enrollments →
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Course Structure -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                  <h3 class="text-lg font-semibold text-gray-900">Course Structure</h3>
                  <span class="text-sm text-gray-500">
                    {{ totalTopics }} topics • {{ totalEstimatedHours }} hours estimated
                  </span>
                </div>
              </div>
              <div class="p-6">
                <div
                  v-for="module in course.modules"
                  :key="module.id"
                  class="mb-6 last:mb-0"
                >
                  <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg mb-3">
                    <div>
                      <h4 class="font-semibold text-gray-900 flex items-center">
                        <span class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-800 rounded-full mr-3">
                          {{ module.order }}
                        </span>
                        {{ module.title }}
                      </h4>
                      <p class="text-sm text-gray-600 mt-1">{{ module.description }}</p>
                    </div>
                    <div class="text-right">
                      <div class="text-sm font-medium text-gray-900">
                        {{ module.topics?.length || 0 }} topics
                      </div>
                      <div class="text-xs text-gray-500">
                        {{ module.estimated_duration_minutes || 0 }} min
                      </div>
                    </div>
                  </div>

                  <div class="space-y-3 ml-12">
                    <div
                      v-for="topic in module.topics"
                      :key="topic.id"
                      class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50"
                    >
                      <div class="flex items-center space-x-3">
                        <div
                          class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center"
                          :class="getOutlineIconClass(topic.type)"
                        >
                          <component
                            :is="getOutlineIcon(topic.type)"
                            class="h-3 w-3 text-white"
                          />
                        </div>
                        <div>
                          <h4 class="text-sm font-medium text-gray-900">{{ topic.title }}</h4>
                          <p class="text-xs text-gray-500">{{ topic.description }}</p>
                          <div class="flex items-center space-x-3 mt-1">
                            <span class="text-xs text-gray-400 capitalize">{{ topic.type }}</span>
                            <span class="text-xs text-gray-400">
                              {{ topic.estimated_duration_minutes }} minutes
                            </span>
                            <span v-if="topic.has_quiz" class="text-xs text-amber-600">
                              <QuestionMarkCircleIcon class="h-3 w-3 inline" /> Quiz
                            </span>
                            <span v-if="topic.has_project" class="text-xs text-purple-600">
                              <BriefcaseIcon class="h-3 w-3 inline" /> Project
                            </span>
                          </div>
                        </div>
                      </div>
                      <div class="flex items-center space-x-2">
                        <span
                          v-if="topic.is_completed"
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

                <!-- Empty state if no modules -->
                <div v-if="!course.modules || course.modules.length === 0" class="text-center py-8">
                  <AcademicCapIcon class="h-12 w-12 mx-auto text-gray-400" />
                  <h4 class="mt-2 text-sm font-medium text-gray-900">No Modules Created</h4>
                  <p class="mt-1 text-sm text-gray-500">
                    This course doesn't have any modules yet.
                  </p>
                  <div class="mt-4">
                    <Link
                      :href="route('admin.courses.modules.create', course.id)"
                      class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700"
                    >
                      <PlusIcon class="h-4 w-4 mr-2" />
                      Add First Module
                    </Link>
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

            <!-- Recent Student Activity -->
            <div v-if="recentActivity.length > 0" class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Recent Student Activity</h3>
              </div>
              <div class="p-6">
                <div class="space-y-4">
                  <div
                    v-for="activity in recentActivity"
                    :key="activity.id"
                    class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50"
                  >
                    <div class="flex items-center space-x-3">
                      <div class="flex-shrink-0">
                        <div class="h-8 w-8 bg-blue-100 rounded-full flex items-center justify-center">
                          <span class="text-xs font-medium text-blue-800">
                            {{ activity.user?.name?.charAt(0).toUpperCase() || 'U' }}
                          </span>
                        </div>
                      </div>
                      <div>
                        <div class="text-sm font-medium text-gray-900">
                          {{ activity.user?.name || 'Unknown Student' }}
                        </div>
                        <div class="text-xs text-gray-500">
                          {{ activity.activity_type || 'activity' }}
                          <span v-if="activity.course_outline">
                            • {{ activity.course_outline.title }}
                          </span>
                        </div>
                      </div>
                    </div>
                    <div class="text-right">
                      <div class="text-xs text-gray-500">
                        {{ formatTimeAgo(activity.created_at) }}
                      </div>
                      <div class="text-xs text-gray-700">
                        {{ activity.time_spent_minutes || 0 }} min
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="space-y-6">
            <!-- Course Details -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Course Details</h3>
              </div>
              <div class="p-6">
                <dl class="space-y-3">
                  <div>
                    <dt class="text-xs text-gray-500">Created By</dt>
                    <dd class="text-sm text-gray-900">
                      {{ course.creator?.name || 'Unknown' }}
                    </dd>
                  </div>
                  <div>
                    <dt class="text-xs text-gray-500">Created On</dt>
                    <dd class="text-sm text-gray-900">
                      {{ formatDate(course.created_at) }}
                    </dd>
                  </div>
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
                  <div v-if="course.ai_model_used">
                    <dt class="text-xs text-gray-500">AI Model Used</dt>
                    <dd class="text-sm text-gray-900">
                      {{ course.ai_model_used }}
                    </dd>
                  </div>
                  <div>
                    <dt class="text-xs text-gray-500">Visibility</dt>
                    <dd class="text-sm text-gray-900">
                      <span :class="course.is_public ? 'text-green-600' : 'text-amber-600'">
                        {{ course.is_public ? 'Public' : 'Private' }}
                      </span>
                    </dd>
                  </div>
                  <div v-if="course.enrollment_limit">
                    <dt class="text-xs text-gray-500">Enrollment Limit</dt>
                    <dd class="text-sm text-gray-900">
                      {{ course.current_enrollment }}/{{ course.enrollment_limit }}
                    </dd>
                  </div>
                </dl>
              </div>
            </div>

            <!-- Enrollment Status -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Enrollment Status</h3>
              </div>
              <div class="p-6">
                <div class="space-y-4">
                  <div v-for="status in enrollmentStatus" :key="status.name" class="flex items-center justify-between">
                    <div class="flex items-center">
                      <div class="w-3 h-3 rounded-full mr-3" :class="status.color"></div>
                      <span class="text-sm text-gray-700">{{ status.name }}</span>
                    </div>
                    <span class="text-sm font-medium text-gray-900">{{ status.count }}</span>
                  </div>
                </div>

                <div class="mt-6">
                  <Link
                    :href="route('admin.courses.enrollments.index', course.id)"
                    class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                  >
                    <UserGroupIcon class="h-4 w-4 mr-2" />
                    Manage Enrollments
                  </Link>
                </div>
              </div>
            </div>

            <!-- Quick Actions -->
<div class="bg-white shadow rounded-lg">
  <div class="px-6 py-4 border-b border-gray-200">
    <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
  </div>
  <div class="p-6 space-y-3">
    <Link
      :href="route('admin.courses.modules.create', course.id)"
      class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
    >
      <PlusIcon class="h-4 w-4 mr-2" />
      Add Module
    </Link>
    <Link
  :href="route('admin.courses.outline', course.id)"
  class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
>
  <DocumentTextIcon class="h-4 w-4 mr-2" />
  View Outline
</Link>

    <!-- Publish/Unpublish Button -->
    <template v-if="course.is_public && course.visibility === 'public'">
      <Link
        :href="route('admin.courses.unpublish', course.id)"
        method="post"
        as="button"
        class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-amber-600 hover:bg-amber-700"
        onclick="return confirm('Are you sure you want to unpublish this course? Students will no longer be able to enroll.')"
      >
        <EyeSlashIcon class="h-4 w-4 mr-2" />
        Unpublish Course
      </Link>
    </template>
    <template v-else>
      <Link
        :href="route('admin.courses.publish', course.id)"
        method="post"
        as="button"
        class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-green-600 hover:bg-green-700"
        onclick="return confirm('Are you sure you want to publish this course? It will be available for student enrollment.')"
      >
        <EyeIcon class="h-4 w-4 mr-2" />
        Publish Course
      </Link>
    </template>

    <button
      @click="toggleCourseStatus"
      class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white"
      :class="course.status === 'active' ? 'bg-amber-600 hover:bg-amber-700' : 'bg-green-600 hover:bg-green-700'"
    >
      <ArrowPathIcon class="h-4 w-4 mr-2" />
      {{ course.status === 'active' ? 'Deactivate Course' : 'Activate Course' }}
    </button>

    <button
      v-if="course.status !== 'completed'"
      @click="markAsCompleted"
      class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700"
    >
      <CheckCircleIcon class="h-4 w-4 mr-2" />
      Mark as Completed
    </button>

    <button
      @click="regenerateOutline"
      class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
    >
      <SparklesIcon class="h-4 w-4 mr-2" />
      Regenerate with AI
    </button>
  </div>
</div>
          </div>
        </div>
      </div>
    </div>
    <!-- Add this section to show generation status -->
  <div v-if="course.content_generation_status || course.quiz_generation_status" class="mb-6">
    <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
      <div class="flex">
        <div class="flex-shrink-0">
          <ClockIcon class="h-5 w-5 text-blue-400" />
        </div>
        <div class="ml-3">
          <h3 class="text-sm font-medium text-blue-800">Background Generation in Progress</h3>
          <div class="mt-2 text-sm text-blue-700">
            <div v-if="course.content_generation_status === 'processing'">
              ⏳ Generating course content...
              <span v-if="course.content_generation_summary">
                ({{ course.content_generation_summary.generated || 0 }} of {{ course.content_generation_summary.total || 0 }} topics)
              </span>
            </div>
            <div v-else-if="course.content_generation_status === 'completed'" class="text-green-700">
              ✅ Content generation completed
            </div>
            <div v-else-if="course.content_generation_status === 'failed'" class="text-red-700">
              ❌ Content generation failed
            </div>

            <div v-if="course.quiz_generation_status === 'processing'" class="mt-1">
              ⏳ Generating quizzes...
              <span v-if="course.quiz_generation_summary">
                ({{ course.quiz_generation_summary.generated || 0 }} of {{ course.quiz_generation_summary.total || 0 }} quizzes)
              </span>
            </div>
            <div v-else-if="course.quiz_generation_status === 'completed'" class="text-green-700">
              ✅ Quiz generation completed
            </div>
            <div v-else-if="course.quiz_generation_status === 'failed'" class="text-red-700">
              ❌ Quiz generation failed
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
import { ref, watch, computed } from 'vue'
import {
  AcademicCapIcon,
  DocumentTextIcon,
  QuestionMarkCircleIcon,
  CheckCircleIcon,
  ArrowPathIcon,
  ArrowLeftIcon,
  PencilIcon,
  PlusIcon,
  UserGroupIcon,
  BriefcaseIcon,
  SparklesIcon,
  EyeIcon,
  EyeSlashIcon,
  ClockIcon
} from '@heroicons/vue/24/outline'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  course: Object,
  enrollments: {
    type: Array,
    default: () => []
  },
  recentActivity: {
    type: Array,
    default: () => []
  }
})

// Computed properties
const completedEnrollmentsCount = computed(() => {
  return props.enrollments.filter(e => e.status === 'completed').length
})

const activeEnrollmentsCount = computed(() => {
  return props.enrollments.filter(e => e.status === 'active').length
})

const totalTopics = computed(() => {
  if (!props.course.modules) return 0
  return props.course.modules.reduce((total, module) => {
    return total + (module.topics?.length || 0)
  }, 0)
})

const totalEstimatedHours = computed(() => {
  if (!props.course.modules) return 0
  const totalMinutes = props.course.modules.reduce((total, module) => {
    return total + (module.estimated_duration_minutes || 0)
  }, 0)
  return Math.round(totalMinutes / 60 * 10) / 10 // Round to 1 decimal
})

const enrollmentStatus = computed(() => {
  const statusCounts = {
    enrolled: props.course.current_enrollment,
    active: 0,
    paused: 0,
    completed: 0,
    dropped: 0
  }

  props.enrollments.forEach(enrollment => {
    if (statusCounts.hasOwnProperty(enrollment.status)) {
      statusCounts[enrollment.status]++
    }
  })

  return [
    { name: 'Enrolled', count: statusCounts.enrolled, color: 'bg-blue-500' },
    { name: 'Active', count: statusCounts.active, color: 'bg-green-500' },
    { name: 'Paused', count: statusCounts.paused, color: 'bg-amber-500' },
    { name: 'Completed', count: statusCounts.completed, color: 'bg-teal-500' },
    { name: 'Dropped', count: statusCounts.dropped, color: 'bg-red-500' },
  ]
})

// Helper functions
const getStatusClass = (status) => {
  const classes = {
    active: 'bg-green-100 text-green-800',
    completed: 'bg-blue-100 text-blue-800',
    draft: 'bg-gray-100 text-gray-800',
    archived: 'bg-red-100 text-red-800',
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getOutlineIcon = (type) => {
  const icons = {
    topic: DocumentTextIcon,
    quiz: QuestionMarkCircleIcon,
    project: BriefcaseIcon,
  }
  return icons[type] || DocumentTextIcon
}

const getOutlineIconClass = (type) => {
  const classes = {
    topic: 'bg-green-500',
    quiz: 'bg-purple-500',
    project: 'bg-amber-500',
  }
  return classes[type] || 'bg-gray-500'
}

const getProgressColor = (percentage) => {
  if (percentage >= 80) return 'bg-green-500'
  if (percentage >= 50) return 'bg-teal-500'
  if (percentage >= 25) return 'bg-amber-500'
  return 'bg-red-500'
}

const formatDate = (dateString) => {
  if (!dateString) return 'Not set'
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

const formatTimeAgo = (dateString) => {
  if (!dateString) return 'Unknown time'
  const date = new Date(dateString)
  const now = new Date()
  const diffInSeconds = Math.floor((now - date) / 1000)

  if (diffInSeconds < 60) return 'just now'
  if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} minutes ago`
  if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)} hours ago`
  if (diffInSeconds < 604800) return `${Math.floor(diffInSeconds / 86400)} days ago`
  return formatDate(dateString)
}

// Actions
const toggleCourseStatus = () => {
  const newStatus = props.course.status === 'active' ? 'draft' : 'active'
  const message = newStatus === 'active'
    ? 'Are you sure you want to activate this course? It will become available for enrollment.'
    : 'Are you sure you want to deactivate this course? It will no longer be available for enrollment.'

  if (confirm(message)) {
    router.patch(route('admin.courses.update-status', props.course.id), {
      status: newStatus
    }, {
      preserveScroll: true
    })
  }
}

const markAsCompleted = () => {
  if (confirm('Mark this course as completed? This will mark all active enrollments as completed.')) {
    router.patch(route('admin.courses.mark-completed', props.course.id), {}, {
      preserveScroll: true
    })
  }
}

const regenerateOutline = () => {
  if (confirm('Are you sure you want to regenerate the course outline with AI? This will replace the current structure.')) {
    router.post(route('admin.courses.regenerate-outline', props.course.id), {}, {
      preserveScroll: true
    })
  }
}

const viewAllEnrollments = () => {
  router.visit(route('admin.courses.enrollments.index', props.course.id))
}
</script>
