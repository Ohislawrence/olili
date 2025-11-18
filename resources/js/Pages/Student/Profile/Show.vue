<template>
  <StudentLayout>
    <Head title="My Profile" />

    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-2xl font-bold text-gray-900">My Profile</h1>
          <p class="mt-1 text-sm text-gray-600">
            Manage your personal information and learning preferences
          </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Left Column - Profile Info -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Personal Information -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100">
              <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">Personal Information</h2>
              </div>
              <div class="p-6">
                <div class="flex items-center space-x-6">
                  <!-- Avatar -->
                  <div class="flex-shrink-0">
                    <div class="h-20 w-20 rounded-full bg-gradient-to-r from-emerald-500 to-teal-600 flex items-center justify-center text-white text-2xl font-bold shadow-md">
                      {{ userInitials }}
                    </div>
                  </div>

                  <!-- User Info -->
                  <div class="flex-1">
                    <h3 class="text-xl font-bold text-gray-900">{{ user.name }}</h3>
                    <p class="text-sm text-gray-600">{{ user.email }}</p>
                    <p v-if="user.phone" class="text-sm text-gray-600">{{ user.phone }}</p>

                    <div class="mt-2 flex flex-wrap gap-2">
                      <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                        {{ student_profile.current_level }}
                      </span>
                      <span v-if="student_profile.exam_board" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-teal-100 text-teal-800">
                        {{ student_profile.exam_board.name }}
                      </span>
                    </div>
                  </div>
                </div>

                <!-- Additional Info -->
                <div class="mt-6 grid grid-cols-2 gap-4 text-sm">
                  <div v-if="user.date_of_birth">
                    <span class="font-medium text-gray-700">Date of Birth:</span>
                    <p class="text-gray-600">{{ formatDate(user.date_of_birth) }}</p>
                  </div>
                  <div v-if="user.gender">
                    <span class="font-medium text-gray-700">Gender:</span>
                    <p class="text-gray-600 capitalize">{{ user.gender }}</p>
                  </div>
                  <div v-if="user.bio" class="col-span-2">
                    <span class="font-medium text-gray-700">Bio:</span>
                    <p class="text-gray-600 mt-1">{{ user.bio }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Learning Goals -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100">
              <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">Learning Goals</h2>
              </div>
              <div class="p-6">
                <div v-if="student_profile.learning_goals?.length" class="space-y-3">
                  <div
                    v-for="(goal, index) in student_profile.learning_goals"
                    :key="index"
                    class="flex items-center p-4 bg-emerald-50 rounded-xl border border-emerald-100"
                  >
                    <CheckCircleIcon class="h-5 w-5 text-emerald-500 mr-3 flex-shrink-0" />
                    <span class="text-gray-700">{{ goal }}</span>
                  </div>
                </div>
                <div v-else class="text-center py-4">
                  <p class="text-gray-500">No learning goals set yet.</p>
                  <Link
                    :href="route('student.profile.edit')"
                    class="mt-2 inline-flex items-center text-sm text-emerald-600 hover:text-emerald-500 font-medium"
                  >
                    Set your learning goals
                  </Link>
                </div>
              </div>
            </div>

            <!-- Current Courses Progress -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100">
              <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">Course Progress</h2>
              </div>
              <div class="p-6">
                <!-- Loading State -->
                <div v-if="loading" class="text-center py-8">
                  <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-emerald-600 mx-auto"></div>
                  <p class="mt-2 text-sm text-gray-500">Loading course progress...</p>
                </div>

                <!-- Error State -->
                <div v-else-if="error" class="text-center py-4">
                  <ExclamationTriangleIcon class="h-8 w-8 text-red-500 mx-auto mb-2" />
                  <p class="text-red-600 mb-2">Failed to load course progress</p>
                  <button
                    @click="loadCourseProgress"
                    class="text-sm text-emerald-600 hover:text-emerald-500 font-medium"
                  >
                    Try again
                  </button>
                </div>

                <!-- Success State -->
                <div v-else-if="courseProgress.length" class="space-y-4">
                  <div
                    v-for="course in courseProgress"
                    :key="course.id"
                    class="border border-gray-200 rounded-xl p-4 hover:border-emerald-300 transition-colors"
                  >
                    <div class="flex justify-between items-start mb-2">
                      <h4 class="font-bold text-gray-900">{{ course.title }}</h4>
                      <span
                        :class="[
                          'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold',
                          getStatusClass(course.status)
                        ]"
                      >
                        {{ course.status }}
                      </span>
                    </div>

                    <!-- Progress Bar -->
                    <div class="mb-3">
                      <div class="flex justify-between text-sm text-gray-600 mb-1">
                        <span>Overall Progress</span>
                        <span class="font-bold">{{ Math.round(course.progress_percentage) }}%</span>
                      </div>
                      <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div
                          class="h-2.5 rounded-full transition-all duration-300"
                          :class="getProgressColor(course.progress_percentage)"
                          :style="{ width: `${course.progress_percentage}%` }"
                        ></div>
                      </div>
                    </div>

                    <!-- Progress Details -->
                    <div class="grid grid-cols-2 gap-3 text-xs">
                      <div class="text-center p-3 bg-emerald-50 rounded-lg">
                        <div class="font-bold text-emerald-700">{{ course.completed_modules }}/{{ course.total_modules }}</div>
                        <div class="text-emerald-600">Modules</div>
                        <div class="text-emerald-500 font-bold mt-1">
                          {{ calculatePercentage(course.completed_modules, course.total_modules) }}%
                        </div>
                      </div>
                      <div class="text-center p-3 bg-teal-50 rounded-lg">
                        <div class="font-bold text-teal-700">{{ course.completed_topics }}/{{ course.total_topics }}</div>
                        <div class="text-teal-600">Topics</div>
                        <div class="text-teal-500 font-bold mt-1">
                          {{ calculatePercentage(course.completed_topics, course.total_topics) }}%
                        </div>
                      </div>
                    </div>

                    <!-- Action Button -->
                    <div class="mt-3">
                      <Link
                        :href="route('student.courses.show', course.id)"
                        class="w-full text-center block px-3 py-2 text-sm font-medium text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition-colors"
                      >
                        View Course Details
                      </Link>
                    </div>
                  </div>
                </div>

                <!-- Empty State -->
                <div v-else class="text-center py-4">
                  <AcademicCapIcon class="h-8 w-8 text-gray-400 mx-auto mb-2" />
                  <p class="text-gray-500">No courses in progress.</p>
                  <Link
                    :href="route('student.courses.create')"
                    class="mt-2 inline-flex items-center text-sm text-emerald-600 hover:text-emerald-500 font-medium"
                  >
                    Create your first course
                  </Link>
                </div>
              </div>
            </div>
          </div>

          <!-- Right Column - Stats & Actions -->
          <div class="space-y-6">
            <!-- Quick Stats -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100">
              <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">Learning Statistics</h2>
              </div>
              <div class="p-6 space-y-4">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-sm font-medium text-gray-600">Total Courses</p>
                    <p class="text-2xl font-bold text-gray-900">{{ stats.total_courses || 0 }}</p>
                  </div>
                  <AcademicCapIcon class="h-8 w-8 text-emerald-500" />
                </div>

                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-sm font-medium text-gray-600">Completed</p>
                    <p class="text-2xl font-bold text-gray-900">{{ stats.completed_courses || 0 }}</p>
                  </div>
                  <CheckCircleIcon class="h-8 w-8 text-emerald-500" />
                </div>

                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-sm font-medium text-gray-600">Active</p>
                    <p class="text-2xl font-bold text-gray-900">{{ stats.active_courses || 0 }}</p>
                  </div>
                  <ClockIcon class="h-8 w-8 text-emerald-500" />
                </div>

                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-sm font-medium text-gray-600">Study Hours</p>
                    <p class="text-2xl font-bold text-gray-900">{{ Math.round(stats.total_study_hours || 0) }}</p>
                  </div>
                  <ChartBarIcon class="h-8 w-8 text-emerald-500" />
                </div>
              </div>
            </div>

            <!-- Study Plan -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100">
              <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">Study Plan</h2>
              </div>
              <div class="p-6 space-y-3">
                <div>
                  <p class="text-sm font-medium text-gray-700">Target Level</p>
                  <p class="text-lg font-bold text-gray-900 capitalize">
                    {{ student_profile.target_level || 'Not set' }}
                  </p>
                </div>

                <div>
                  <p class="text-sm font-medium text-gray-700">Weekly Study Hours</p>
                  <p class="text-lg font-bold text-gray-900">
                    {{ student_profile.weekly_study_hours || 0 }} hours
                  </p>
                </div>

                <div>
                  <p class="text-sm font-medium text-gray-700">Target Completion</p>
                  <p class="text-lg font-bold text-gray-900">
                    {{ formatDate(student_profile.target_completion_date) }}
                  </p>
                </div>

                <div v-if="student_profile.current_grade || student_profile.target_grade" class="pt-3 border-t border-gray-200">
                  <p class="text-sm font-medium text-gray-700 mb-2">Grade Progress</p>
                  <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Current</span>
                    <span class="text-sm text-gray-600">Target</span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="text-lg font-bold text-gray-900">
                      {{ student_profile.current_grade || '--' }}%
                    </span>
                    <ArrowRightIcon class="h-4 w-4 text-gray-400" />
                    <span class="text-lg font-bold text-emerald-600">
                      {{ student_profile.target_grade || '--' }}%
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100">
              <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">Quick Actions</h2>
              </div>
              <div class="p-6 space-y-3">
                <Link
                  :href="route('student.profile.edit')"
                  class="w-full flex items-center justify-center px-4 py-2.5 border border-emerald-300 text-sm font-semibold rounded-lg text-emerald-700 bg-white hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200"
                >
                  <PencilIcon class="h-4 w-4 mr-2" />
                  Edit Profile
                </Link>
                <Link
                  :href="route('student.courses.create')"
                  class="w-full flex items-center justify-center px-4 py-2.5 border border-transparent text-sm font-semibold rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200"
                >
                  <PlusIcon class="h-4 w-4 mr-2" />
                  New Course
                </Link>
                <Link
                  :href="route('student.courses.index')"
                  class="w-full flex items-center justify-center px-4 py-2.5 border border-emerald-300 text-sm font-semibold rounded-lg text-emerald-700 bg-white hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200"
                >
                  <AcademicCapIcon class="h-4 w-4 mr-2" />
                  View Courses
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
import { ref, onMounted, computed } from 'vue'
import StudentLayout from '@/Layouts/StudentLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import {
  AcademicCapIcon,
  CheckCircleIcon,
  ClockIcon,
  ChartBarIcon,
  PencilIcon,
  PlusIcon,
  ArrowRightIcon,
  ExclamationTriangleIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  user: Object,
  student_profile: Object,
  stats: Object,
})

const courseProgress = ref([])
const loading = ref(false)
const error = ref(null)

const userInitials = computed(() => {
  return props.user.name
    .split(' ')
    .map(word => word[0])
    .join('')
    .toUpperCase()
    .slice(0, 2)
})

const getStatusClass = (status) => {
  const classes = {
    draft: 'bg-gray-100 text-gray-800',
    active: 'bg-emerald-100 text-emerald-800',
    paused: 'bg-yellow-100 text-yellow-800',
    completed: 'bg-teal-100 text-teal-800',
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getProgressColor = (percentage) => {
  if (percentage >= 80) return 'bg-emerald-500'
  if (percentage >= 50) return 'bg-teal-500'
  if (percentage >= 25) return 'bg-amber-500'
  return 'bg-red-500'
}

const formatDate = (dateString) => {
  if (!dateString) return 'Not set'
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const calculatePercentage = (completed, total) => {
  return total > 0 ? Math.round((completed / total) * 100) : 0
}

const loadCourseProgress = async () => {
  loading.value = true
  error.value = null

  try {
    const response = await fetch(route('student.profile.course-progress'))

    if (!response.ok) {
      throw new Error(`Failed to load course progress: ${response.status}`)
    }

    const data = await response.json()
    courseProgress.value = data.courses || []
  } catch (err) {
    console.error('Failed to load course progress:', err)
    error.value = err.message
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadCourseProgress()
})
</script>
