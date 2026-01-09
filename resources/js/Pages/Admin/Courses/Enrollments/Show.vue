<!-- resources/js/Pages/Admin/Courses/Enrollments/Show.vue -->
<template>
  <AdminLayout>
    <Head :title="`Enrollment - ${enrollment.user.name}`" />

    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex justify-between items-start">
            <div>
              <div class="flex items-center space-x-3">
                <Link
                  :href="route('admin.courses.enrollments.index', course.id)"
                  class="text-gray-400 hover:text-gray-600"
                >
                  <ArrowLeftIcon class="h-5 w-5" />
                </Link>
                <div>
                  <h1 class="text-2xl font-bold text-gray-900">Enrollment Details</h1>
                  <p class="mt-1 text-sm text-gray-600">
                    {{ course.title }} • {{ enrollment.user.name }}
                  </p>
                </div>
              </div>
            </div>
            <div class="flex space-x-3">
              <Link
                :href="route('admin.courses.enrollments.edit', { course: course.id, enrollment: enrollment.id })"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
              >
                <PencilIcon class="h-4 w-4 mr-2" />
                Edit Enrollment
              </Link>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Main Content -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Student Progress -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Student Progress</h3>
              </div>
              <div class="p-6">
                <!-- Overall Progress -->
                <div class="mb-6">
                  <div class="flex justify-between items-center mb-4">
                    <div>
                      <h4 class="text-sm font-medium text-gray-900">Overall Progress</h4>
                      <p class="text-3xl font-bold text-gray-900 mt-1">
                        {{ Math.round(enrollment.progress_percentage) }}%
                      </p>
                    </div>
                    <div class="text-right">
                      <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                        :class="getStatusClass(enrollment.status)"
                      >
                        {{ enrollment.status }}
                      </span>
                    </div>
                  </div>
                  <div class="w-full bg-gray-200 rounded-full h-3">
                    <div
                      class="h-3 rounded-full transition-all duration-300"
                      :class="getProgressColor(enrollment.progress_percentage)"
                      :style="{ width: `${enrollment.progress_percentage}%` }"
                    ></div>
                  </div>
                  <div class="mt-2 text-sm text-gray-500">
                    {{ progress.completed_topics }} of {{ progress.total_topics }} topics completed
                  </div>
                </div>

                <!-- Module Progress -->
                <div class="space-y-4">
                  <h4 class="text-sm font-medium text-gray-900">Module Progress</h4>
                  <div
                    v-for="module in course.modules"
                    :key="module.id"
                    class="border border-gray-200 rounded-lg p-4"
                  >
                    <div class="flex justify-between items-center mb-2">
                      <h5 class="font-medium text-gray-900">{{ module.title }}</h5>
                      <span class="text-sm text-gray-500">
                        {{ getModuleCompletedTopics(module) }}/{{ module.topics_count || module.topics?.length || 0 }} topics
                      </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                      <div
                        class="h-2 rounded-full bg-blue-500 transition-all duration-300"
                        :style="{ width: `${calculateModuleProgress(module)}%` }"
                      ></div>
                    </div>
                    <div class="mt-2 grid grid-cols-2 gap-2">
                      <div
                        v-for="topic in module.topics"
                        :key="topic.id"
                        class="flex items-center"
                      >
                        <div
                          class="w-3 h-3 rounded-full mr-2"
                          :class="getTopicCompletionStatus(topic) ? 'bg-green-500' : 'bg-gray-300'"
                        ></div>
                        <span class="text-xs text-gray-600 truncate">{{ topic.title }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Recent Activity -->
            <div v-if="recentActivity && recentActivity.length > 0" class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Recent Activity</h3>
              </div>
              <div class="p-6">
                <div class="space-y-4">
                  <div
                    v-for="activity in recentActivity"
                    :key="activity.id"
                    class="flex items-center justify-between p-3 border border-gray-200 rounded-lg"
                  >
                    <div class="flex items-center space-x-3">
                      <div
                        class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center"
                        :class="getActivityIconClass(activity.activity_type)"
                      >
                        <component
                          :is="getActivityIcon(activity.activity_type)"
                          class="h-4 w-4 text-white"
                        />
                      </div>
                      <div>
                        <div class="text-sm font-medium text-gray-900">
                          {{ formatActivityType(activity.activity_type) }}
                        </div>
                        <div class="text-xs text-gray-500">
                          {{ activity.topic_title || 'Activity' }}
                          <span v-if="activity.module_title">• {{ activity.module_title }}</span>
                        </div>
                      </div>
                    </div>
                    <div class="text-right">
                      <div class="text-xs text-gray-500">{{ formatRelativeDate(activity.created_at) }}</div>
                      <div v-if="activity.time_spent_minutes" class="text-xs text-gray-700">
                        {{ activity.time_spent_minutes }} min
                      </div>
                    </div>
                  </div>
                </div>
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
                    <div class="h-12 w-12 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold text-lg">
                      {{ enrollment.user?.name?.charAt(0).toUpperCase() || 'U' }}
                    </div>
                  </div>
                  <div>
                    <h4 class="text-sm font-medium text-gray-900">
                      {{ enrollment.user?.name || 'Unknown User' }}
                    </h4>
                    <p class="text-sm text-gray-500">
                      {{ enrollment.user?.email || 'No email' }}
                    </p>
                  </div>
                </div>
                <dl class="space-y-3">
                  <div>
                    <dt class="text-xs text-gray-500">Student Profile</dt>
                    <dd class="text-sm text-gray-900">
                      {{ enrollment.student_profile?.current_level || 'Not set' }}
                    </dd>
                  </div>
                  <div>
                    <dt class="text-xs text-gray-500">Weekly Study Hours</dt>
                    <dd class="text-sm text-gray-900">
                      {{ enrollment.student_profile?.weekly_study_hours || 0 }} hours
                    </dd>
                  </div>
                </dl>
              </div>
            </div>

            <!-- Enrollment Details -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Enrollment Details</h3>
              </div>
              <div class="p-6">
                <dl class="space-y-3">
                  <div>
                    <dt class="text-xs text-gray-500">Enrollment Date</dt>
                    <dd class="text-sm text-gray-900">
                      {{ formatDate(enrollment.enrolled_at) }}
                    </dd>
                  </div>
                  <div>
                    <dt class="text-xs text-gray-500">Start Date</dt>
                    <dd class="text-sm text-gray-900">
                      {{ formatDate(enrollment.started_at) || 'Not started' }}
                    </dd>
                  </div>
                  <div>
                    <dt class="text-xs text-gray-500">Completion Date</dt>
                    <dd class="text-sm text-gray-900">
                      {{ formatDate(enrollment.completed_at) || 'Not completed' }}
                    </dd>
                  </div>
                  <div>
                    <dt class="text-xs text-gray-500">Last Accessed</dt>
                    <dd class="text-sm text-gray-900">
                      {{ formatDate(enrollment.last_accessed_at) || 'Never' }}
                    </dd>
                  </div>
                  <div>
                    <dt class="text-xs text-gray-500">Progress</dt>
                    <dd class="text-sm text-gray-900">
                      {{ enrollment.progress_percentage || 0 }}%
                    </dd>
                  </div>
                  <div>
                    <dt class="text-xs text-gray-500">Time Spent</dt>
                    <dd class="text-sm text-gray-900">
                      {{ enrollment.actual_duration_hours || enrollment.time_spent_hours || 0 }} hours
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
                  v-if="enrollment.status !== 'active'"
                  @click="updateStatus('active')"
                  class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-green-600 hover:bg-green-700"
                >
                  <PlayIcon class="h-4 w-4 mr-2" />
                  Mark as Active
                </button>
                <button
                  v-if="enrollment.status !== 'paused'"
                  @click="updateStatus('paused')"
                  class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-amber-600 hover:bg-amber-700"
                >
                  <PauseIcon class="h-4 w-4 mr-2" />
                  Pause Enrollment
                </button>
                <button
                  v-if="enrollment.status !== 'completed'"
                  @click="updateStatus('completed')"
                  class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700"
                >
                  <CheckCircleIcon class="h-4 w-4 mr-2" />
                  Mark as Completed
                </button>
                <button
                  v-if="enrollment.status !== 'dropped'"
                  @click="updateStatus('dropped')"
                  class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-red-600 hover:bg-red-700"
                >
                  <TrashIcon class="h-4 w-4 mr-2" />
                  View Enrollments
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
import AdminLayout from '@/Layouts/AdminLayout.vue'
import {
  ArrowLeftIcon,
  PencilIcon,
  CheckCircleIcon,
  TrashIcon,
  DocumentTextIcon,
  QuestionMarkCircleIcon,
  PlayIcon,
  PauseIcon,
} from '@heroicons/vue/24/outline'
import { ref, computed } from 'vue'

const props = defineProps({
  course: Object,
  enrollment: Object,
  progress: Object,
  recentActivity: Array,
})

// Helper functions
const getStatusClass = (status) => {
  const classes = {
    enrolled: 'bg-blue-100 text-blue-800',
    active: 'bg-green-100 text-green-800',
    paused: 'bg-amber-100 text-amber-800',
    completed: 'bg-teal-100 text-teal-800',
    dropped: 'bg-red-100 text-red-800',
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getProgressColor = (percentage) => {
  if (percentage >= 80) return 'bg-green-500'
  if (percentage >= 50) return 'bg-teal-500'
  if (percentage >= 25) return 'bg-amber-500'
  return 'bg-red-500'
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  if (isNaN(date.getTime())) return 'Invalid date'
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

const formatRelativeDate = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  if (isNaN(date.getTime())) return ''

  const now = new Date()
  const diffMs = now - date
  const diffHours = Math.floor(diffMs / (1000 * 60 * 60))

  if (diffHours < 1) {
    const diffMinutes = Math.floor(diffMs / (1000 * 60))
    return `${diffMinutes} min ago`
  } else if (diffHours < 24) {
    return `${diffHours} hours ago`
  } else if (diffHours < 168) {
    const diffDays = Math.floor(diffHours / 24)
    return `${diffDays} days ago`
  } else {
    return formatDate(dateString)
  }
}

const getModuleCompletedTopics = (module) => {
  if (!module.topics || !Array.isArray(module.topics)) return 0
  return module.topics.filter(topic => getTopicCompletionStatus(topic)).length
}

const calculateModuleProgress = (module) => {
  const totalTopics = module.topics_count || module.topics?.length || 0
  if (totalTopics === 0) return 0
  const completedTopics = getModuleCompletedTopics(module)
  return (completedTopics / totalTopics) * 100
}

const getTopicCompletionStatus = (topic) => {
  // Check if topic has is_completed_for_user property
  if (topic.is_completed_for_user !== undefined) return topic.is_completed_for_user

  // Check if topic has is_completed property
  if (topic.is_completed !== undefined) return topic.is_completed

  // Check if there's a completion status in the progress object
  if (props.progress?.completed_topic_ids?.includes(topic.id)) return true

  return false
}

const getActivityIcon = (type) => {
  const icons = {
    content_view: DocumentTextIcon,
    quiz_attempt: QuestionMarkCircleIcon,
    topic_completion: CheckCircleIcon,
    lesson_completed: CheckCircleIcon,
    quiz_completed: QuestionMarkCircleIcon,
    assignment_submitted: DocumentTextIcon,
  }
  return icons[type] || DocumentTextIcon
}

const getActivityIconClass = (type) => {
  const classes = {
    content_view: 'bg-blue-500',
    quiz_attempt: 'bg-purple-500',
    topic_completion: 'bg-green-500',
    lesson_completed: 'bg-green-500',
    quiz_completed: 'bg-purple-500',
    assignment_submitted: 'bg-orange-500',
  }
  return classes[type] || 'bg-gray-500'
}

const formatActivityType = (type) => {
  const types = {
    content_view: 'Content Viewed',
    quiz_attempt: 'Quiz Attempted',
    topic_completion: 'Topic Completed',
    lesson_completed: 'Lesson Completed',
    quiz_completed: 'Quiz Completed',
    assignment_submitted: 'Assignment Submitted',
  }
  return types[type] || type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())
}

// Actions
const updateStatus = (status) => {
  const statusMessages = {
    active: 'Are you sure you want to mark this enrollment as active?',
    paused: 'Are you sure you want to pause this enrollment?',
    completed: 'Are you sure you want to mark this enrollment as completed?',
    dropped: 'Are you sure you want to drop this enrollment? This action cannot be undone.',
  }

  if (confirm(statusMessages[status])) {
    router.patch(route('admin.courses.enrollments.update', {
      course: props.course.id,
      enrollment: props.enrollment.id
    }), {
      status: status
    }, {
      preserveScroll: true,
      onSuccess: () => {
        router.reload({ only: ['enrollment'] })
      }
    })
  }
}
</script>
