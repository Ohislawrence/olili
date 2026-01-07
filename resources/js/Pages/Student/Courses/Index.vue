<template>
  <StudentLayout>
    <Head title="My Courses" />

    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex justify-between items-center">
            <div>
              <h1 class="text-2xl font-bold text-gray-900">My Courses</h1>
              <p class="mt-1 text-sm text-gray-600">
                Manage and track your learning progress
              </p>
            </div>

            <!-- Quick Stats -->
            <div class="hidden sm:flex items-center space-x-4">
              <div class="text-center">
                <div class="text-2xl font-bold text-emerald-600">{{ activeCoursesCount }}</div>
                <div class="text-xs text-gray-500">Active</div>
              </div>
              <div class="text-center">
                <div class="text-2xl font-bold text-teal-600">{{ completedCoursesCount }}</div>
                <div class="text-xs text-gray-500">Completed</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Filters and Search -->
        <div class="mb-6 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
          <div class="flex flex-col sm:flex-row gap-4">
            <!-- Search -->
            <div class="flex-1">
              <input
                v-model="filters.search"
                type="text"
                placeholder="Search courses..."
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                @input="search"
              />
            </div>

            <!-- Status Filter -->
            <div class="w-full sm:w-48">
              <select
                v-model="filters.status"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                @change="filter"
              >
                <option value="">All Status</option>
                <option value="enrolled">Enrolled</option>
                <option value="active">Active</option>
                <option value="paused">Paused</option>
                <option value="completed">Completed</option>
                <option value="dropped">Dropped</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Courses List/Grid -->
        <div v-if="courses.data.length" class="space-y-4">
          <div
            v-for="course in courses.data"
            :key="course.id"
            class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300 hover:-translate-y-1"
          >
            <!-- Course Content -->
            <div class="p-6 flex flex-col lg:flex-row lg:items-start lg:gap-6">
              <!-- Course Details -->
              <div class="flex-1 flex flex-col min-w-0">
                <!-- Top Section: Header & Progress -->
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between lg:mb-4">
                  <!-- Course Info -->
                  <div class="lg:flex-1 lg:pr-6">
                    <!-- Course Header -->
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between mb-4 lg:mb-3 gap-2 sm:gap-0">
                      <div class="flex flex-wrap gap-2">
                        <span
                          :class="[
                            'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold',
                            getStatusClass(course.status)
                          ]"
                        >
                          {{ formatStatus(course.status) }}
                        </span>
                        <span
                          v-if="course.exam_board"
                          class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800"
                        >
                          {{ course.exam_board.name }}
                        </span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                          {{ course.level }}
                        </span>
                      </div>

                      <!-- Progress for mobile -->
                      <div class="sm:hidden text-right mt-2">
                        <span class="text-sm font-semibold text-gray-900">{{ Math.round(course.progress_percentage ) }}%</span>
                        <div class="text-xs text-gray-500">Complete</div>
                      </div>
                    </div>

                    <!-- Course Title & Description -->
                    <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2">
                      {{ course.title }}
                    </h3>
                    <p class="text-sm text-gray-600 mb-2">
                      {{ course.subject }} • Enrolled {{ formatDate(course.enrolled_at) }}
                    </p>
                    <p class="text-sm text-gray-500 mb-4 line-clamp-2">
                      {{ course.description || 'No description available' }}
                    </p>
                  </div>

                  <!-- Progress for desktop -->
                  <div class="hidden lg:flex lg:flex-col lg:items-end lg:flex-shrink-0 lg:w-32">
                    <div class="text-right mb-2">
                      <span class="text-2xl font-bold text-gray-900">{{ Math.round(course.progress_percentage) }}%</span>
                      <div class="text-sm text-gray-500">Complete</div>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                      <div
                        class="h-2.5 rounded-full transition-all duration-300"
                        :class="getProgressColor(course.progress_percentage)"
                        :style="{ width: `${course.progress_percentage}%` }"
                      ></div>
                    </div>
                  </div>
                </div>

                <!-- Progress Bar for mobile -->
                <div class="mb-4 lg:hidden">
                  <div class="flex justify-between text-sm text-gray-600 mb-1">
                    <span>Progress</span>
                    <span class="font-semibold">{{ Math.round(course.progress_percentage) }}%</span>
                  </div>
                  <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div
                      class="h-2.5 rounded-full transition-all duration-300"
                      :class="getProgressColor(course.progress_percentage)"
                      :style="{ width: `${course.progress_percentage}%` }"
                    ></div>
                  </div>
                  <div class="text-xs text-gray-500 mt-1 font-medium">
                    {{ getCompletedItemsText(course) }}
                  </div>
                </div>

                <!-- Bottom Section: Dates & Actions -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between sm:mt-auto gap-4 sm:gap-0">
                  <!-- Stats -->
                  <div class="text-xs text-gray-500 flex flex-wrap gap-4 sm:gap-6 sm:items-center">
                    <div class="flex items-center gap-1">
                      <AcademicCapIcon class="h-3 w-3" />
                      <span>{{ course.completed_modules_count }}/{{ course.modules_count }} modules</span>
                    </div>
                    <div class="flex items-center gap-1">
                      <BookOpenIcon class="h-3 w-3" />
                      <span>{{ course.completed_topics_count }}/{{ course.topics_count }} topics</span>
                    </div>
                    <div v-if="course.started_at" class="flex items-center gap-1">
                      <ClockIcon class="h-3 w-3" />
                      <span>Started: {{ formatDate(course.started_at) }}</span>
                    </div>

                    <div v-if="course.est_completion_time" class="flex items-center gap-1">
                      <ClockIcon class="h-3 w-3" />
                      <span>Est. Completion Time: {{ formatDate(course.est_completion_time) }}</span>
                    </div>
                  </div>

                  <!-- Actions -->
                  <div class="flex space-x-2">
                    <Link
                      :href="route('student.courses.show', course.id)"
                      class="flex-1 text-center px-4 py-2 text-sm font-semibold text-emerald-700 bg-white border border-emerald-300 rounded-lg hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-colors"
                    >
                      View
                    </Link>

                    <Link
                      v-if="course.status === 'enrolled'"
                      :href="route('student.courses.start', {
                        course: course.id})"
                      class="flex-1 text-center px-4 py-2 text-sm font-semibold text-white bg-gradient-to-r from-emerald-600 to-teal-600 border border-transparent rounded-lg hover:from-emerald-700 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200"
                    >
                      Start
                    </Link>

                    <Link
                      v-else-if="course.status === 'active'"
                      :href="route('student.courses.learn', {
                        course: course.id,
                        topic: course.lastViewedTopic
                        })"
                      class="flex-1 text-center px-4 py-2 text-sm font-semibold text-white bg-gradient-to-r from-emerald-600 to-teal-600 border border-transparent rounded-lg hover:from-emerald-700 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200"
                    >
                      Continue
                    </Link>

                    <button
                      v-else-if="course.status === 'paused'"
                      @click="resumeCourse(course)"
                      class="flex-1 text-center px-4 py-2 text-sm font-semibold text-white bg-gradient-to-r from-amber-600 to-amber-700 border border-transparent rounded-lg hover:from-amber-700 hover:to-amber-800 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition-all duration-200"
                    >
                      Resume
                    </button>

                    <button
                      v-if="course.status !== 'completed' && course.status !== 'dropped'"
                      @click="pauseCourse(course)"
                      class="flex-1 text-center px-4 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors"
                    >
                      Pause
                    </button>

                    <button
                      v-if="course.status !== 'completed' && course.status !== 'dropped'"
                      @click="dropCourse(course)"
                      class="flex-1 text-center px-4 py-2 text-sm font-semibold text-red-700 bg-white border border-red-300 rounded-lg hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors"
                    >
                      Drop
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-12">
          <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
            <AcademicCapIcon class="mx-auto h-12 w-12 text-gray-400" />
            <h3 class="mt-2 text-sm font-semibold text-gray-900">No courses enrolled</h3>
            <p class="mt-1 text-sm text-gray-500">
              {{ filters.search || filters.status ? 'No courses match your filters.' : 'Browse courses to start learning.' }}
            </p>
            <div class="mt-6">
              <Link
                :href="route('student.catalog.browse')"
                class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 border border-transparent rounded-lg font-semibold text-white shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200"
              >
                <MagnifyingGlassIcon class="h-4 w-4 mr-2" />
                Browse Courses
              </Link>
              <button
                v-if="filters.search || filters.status"
                @click="clearFilters"
                class="ml-3 inline-flex items-center px-4 py-2.5 border border-emerald-300 rounded-lg font-semibold text-emerald-700 bg-white hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200"
              >
                Clear Filters
              </button>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="courses.data.length" class="mt-8">
          <Pagination :links="courses.links" />
        </div>
      </div>
    </div>

    <!-- Confirmation Modals -->
    <ConfirmationModal
      :show="showPauseModal"
      @close="showPauseModal = false"
      @confirm="confirmPauseCourse"
    >
      <template #title>
        Pause Course
      </template>
      <template #content>
        Are you sure you want to pause <strong>{{ selectedCourse?.title }}</strong>?
        You can resume it anytime from your dashboard.
      </template>
    </ConfirmationModal>

    <ConfirmationModal
      :show="showDropModal"
      @close="showDropModal = false"
      @confirm="confirmDropCourse"
    >
      <template #title>
        Drop Course
      </template>
      <template #content>
        Are you sure you want to drop <strong>{{ selectedCourse?.title }}</strong>?
        This action cannot be undone and you will lose all progress.
      </template>
    </ConfirmationModal>
  </StudentLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import Pagination from '@/Components/Pagination.vue'
import ConfirmationModal from '@/Components/ConfirmationModal.vue'
import {
  AcademicCapIcon,
  BookOpenIcon,
  ClockIcon,
  MagnifyingGlassIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  courses: Object,
  filters: Object,
})

const filters = ref({
  search: props.filters.search || '',
  status: props.filters.status || '',
})

const showPauseModal = ref(false)
const showDropModal = ref(false)
const selectedCourse = ref(null)

// Computed properties
const activeCoursesCount = computed(() => {
  return props.courses.data.filter(course =>
    ['enrolled', 'active'].includes(course.status)
  ).length
})

const completedCoursesCount = computed(() => {
  return props.courses.data.filter(course =>
    course.status === 'completed'
  ).length
})

// Debounced search
let searchTimeout = null
const search = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    filter()
  }, 300)
}

const filter = () => {
  router.get(route('student.courses.index'), filters.value, {
    preserveState: true,
    replace: true,
  })
}

const clearFilters = () => {
  filters.value = { search: '', status: '' }
  filter()
}

// Course actions
const pauseCourse = (course) => {
  selectedCourse.value = course
  showPauseModal.value = true
}

const confirmPauseCourse = async () => {
  if (!selectedCourse.value) return

  try {
    await router.post(route('student.courses.pause', selectedCourse.value.id), {}, {
      preserveScroll: true,
      onSuccess: () => {
        router.reload({ preserveScroll: true })
      }
    })
  } catch (error) {
    console.error('Error pausing course:', error)
    alert('Failed to pause course. Please try again.')
  } finally {
    showPauseModal.value = false
    selectedCourse.value = null
  }
}

const resumeCourse = async (course) => {
  try {
    await router.post(route('student.courses.resume', course.id), {}, {
      preserveScroll: true,
      onSuccess: () => {
        router.reload({ preserveScroll: true })
      }
    })
  } catch (error) {
    console.error('Error resuming course:', error)
    alert('Failed to resume course. Please try again.')
  }
}

const dropCourse = (course) => {
  selectedCourse.value = course
  showDropModal.value = true
}

const confirmDropCourse = async () => {
  if (!selectedCourse.value) return

  try {
    await router.post(route('student.courses.drop', selectedCourse.value.id), {}, {
      preserveScroll: true,
      onSuccess: () => {
        router.reload({ preserveScroll: true })
      }
    })
  } catch (error) {
    console.error('Error dropping course:', error)
    alert('Failed to drop course. Please try again.')
  } finally {
    showDropModal.value = false
    selectedCourse.value = null
  }
}

// Helper functions
const getStatusClass = (status) => {
  const classes = {
    enrolled: 'bg-blue-100 text-blue-800',
    active: 'bg-emerald-100 text-emerald-800',
    paused: 'bg-amber-100 text-amber-800',
    completed: 'bg-teal-100 text-teal-800',
    dropped: 'bg-red-100 text-red-800',
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const formatStatus = (status) => {
  const statusMap = {
    enrolled: 'Enrolled',
    active: 'Active',
    paused: 'Paused',
    completed: 'Completed',
    dropped: 'Dropped',
  }
  return statusMap[status] || status
}

const getProgressColor = (percentage) => {
  if (percentage >= 80) return 'bg-emerald-500'
  if (percentage >= 50) return 'bg-teal-500'
  if (percentage >= 25) return 'bg-amber-500'
  return 'bg-red-500'
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  const now = new Date()
  const diffTime = Math.abs(now - date)
  const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24))

  if (diffDays === 0) return 'Today'
  if (diffDays === 1) return 'Yesterday'
  if (diffDays < 7) return `${diffDays} days ago`
  if (diffDays < 30) return `${Math.floor(diffDays / 7)} weeks ago`

  return date.toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  })
}

const getCompletedItemsText = (course) => {
  if (course.completed_modules_count !== undefined && course.modules_count !== undefined) {
    return `${course.completed_modules_count} of ${course.modules_count} modules completed`
  }
  return 'Progress not available'
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
