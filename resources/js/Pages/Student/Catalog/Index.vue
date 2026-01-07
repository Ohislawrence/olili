<!-- resources/js/Pages/Student/Catalog/Index.vue - Updated template section -->
<template>
  <StudentLayout>
    <Head title="Course Catalog" />

    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="md:flex md:items-center md:justify-between mb-8">
          <div class="flex-1 min-w-0">
            <h1 class="text-2xl font-bold text-gray-900">Course Catalog</h1>
            <p class="mt-1 text-sm text-gray-600">
              Browse and enroll in public courses created by expert instructors
            </p>
          </div>
          <div class="mt-4 md:mt-0 md:ml-4">
            <Link
              :href="route('student.courses.shared')"
              class="inline-flex items-center px-4 py-2 border border-emerald-300 shadow-sm text-sm font-medium rounded-md text-emerald-700 bg-white hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
              Shared Courses
            </Link>
          </div>
        </div>

        <!-- Stats -->
        <div class="mb-6">
          <div class="bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-100 rounded-xl p-4">
            <div class="flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
              </svg>
              <div>
                <p class="text-sm font-medium text-emerald-800">
                  Discover {{ total_courses }} expertly crafted courses
                </p>
                <p class="text-xs text-emerald-600">
                  {{ enrolled_course_ids.length }} courses already enrolled • Browse all available courses
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Filters -->
        <div class="bg-white shadow rounded-lg p-4 mb-6">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
              <input
                v-model="filters.search"
                type="text"
                placeholder="Search courses..."
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm"
                @input="debouncedSearch"
              />
            </div>

            <!-- Subject -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
              <select
                v-model="filters.subject"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm"
                @change="updateFilters"
              >
                <option value="">All Subjects</option>
                <option v-for="subject in subjects" :key="subject" :value="subject">
                  {{ subject }}
                </option>
              </select>
            </div>

            <!-- Level -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Level</label>
              <select
                v-model="filters.level"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm"
                @change="updateFilters"
              >
                <option value="">All Levels</option>
                <option v-for="(label, value) in levels" :key="value" :value="value">
                  {{ label }}
                </option>
              </select>
            </div>

            <!-- Exam Board -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Exam Board</label>
              <select
                v-model="filters.exam_board_id"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm"
                @change="updateFilters"
              >
                <option value="">All Exam Boards</option>
                <option v-for="examBoard in exam_boards" :key="examBoard.id" :value="examBoard.id">
                  {{ examBoard.name }}
                </option>
              </select>
            </div>
          </div>

          <!-- Filter Actions -->
          <div class="mt-4 flex justify-between items-center">
            <div class="text-sm text-gray-500">
              Showing {{ courses.data.length }} of {{ courses.total }} courses
            </div>
            <button
              @click="resetFilters"
              class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
            >
              Clear All Filters
            </button>
          </div>
        </div>

        <!-- Courses Grid -->
        <div v-if="courses.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="course in courses.data"
            :key="course.id"
            class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200"
          >
            <!-- Course Thumbnail -->
            <div class="h-48 bg-gradient-to-br from-emerald-100 to-teal-100 relative">
              <div v-if="course.thumbnail_url" class="h-full w-full">
                <img :src="course.thumbnail_url" :alt="course.title" class="h-full w-full object-cover" />
              </div>
              <div v-else class="h-full flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
              </div>

              <!-- Enrollment Status Badge -->
              <div class="absolute top-3 right-3">
                <template v-if="isEnrolled(course.id)">
                  <div class="flex flex-col items-end space-y-1">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      Enrolled
                    </span>
                  </div>
                </template>
                <template v-else-if="course.current_enrollment >= (course.enrollment_limit || Infinity)">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.798-.833-2.732 0L4.346 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                    Full
                  </span>
                </template>
                <template v-else>
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    {{ course.current_enrollment }} enrolled
                  </span>
                </template>
              </div>

              <!-- Level Badge -->
              <div class="absolute bottom-3 left-3">
                <span :class="getLevelBadgeClass(course.level)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                  {{ course.level }}
                </span>
              </div>
            </div>

            <!-- Course Content -->
            <div class="p-5">
              <div class="flex justify-between items-start mb-3">
                <h3 class="text-lg font-semibold text-gray-900 line-clamp-2">
                  {{ course.title }}
                </h3>
                <div class="flex-shrink-0 ml-2">
                  <span v-if="course.exam_board" class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-1 rounded">
                    {{ course.exam_board.abbreviation || course.exam_board.name }}
                  </span>
                </div>
              </div>

              <p class="text-sm text-gray-600 mb-4 line-clamp-2">
                {{ course.description }}
              </p>

              <!-- Course Details -->
              <div class="space-y-3">
                <div class="flex items-center text-sm text-gray-500">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  {{ course.estimated_duration_hours }} hours
                </div>

                <div class="flex items-center text-sm text-gray-500">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                  <span>By {{ course.creator?.name || 'Admin' }}</span>
                </div>

                <div v-if="course.enrollment_limit" class="flex items-center text-sm text-gray-500">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
                  <span class="flex-1">
                    {{ course.current_enrollment }} / {{ course.enrollment_limit }} spots
                  </span>
                  <div class="w-16 h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div
                      class="h-full bg-emerald-500 rounded-full"
                      :style="{ width: `${(course.current_enrollment / course.enrollment_limit) * 100}%` }"
                    ></div>
                  </div>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="mt-6">
                <!-- Enrolled State -->
                <div v-if="isEnrolled(course.id)" class="space-y-2">
                  <Link
                    :href="route('student.courses.learn', getEnrolledCourseId(course.id))"
                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Continue Learning
                  </Link>
                  <div class="flex space-x-2">
                    <Link
                      :href="route('student.courses.show', course.id)"
                      class="flex-1 inline-flex justify-center items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
                    >
                      View Details
                    </Link>
                    <button
                      @click="unenrollCourse(course)"
                      class="flex-1 inline-flex justify-center items-center px-3 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                    >
                      Unenroll
                    </button>
                  </div>
                </div>

                <!-- Not Enrolled State -->
                <div v-else>
                  <div class="space-y-2">
                    <Link
                      :href="route('student.courses.show', course.id)"
                      class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                      View Course Details
                    </Link>

                    <button
                      v-if="course.current_enrollment < (course.enrollment_limit || Infinity)"
                      @click="enrollCourse(course)"
                      class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                      </svg>
                      Enroll Now
                    </button>
                    <div v-else class="text-center py-2">
                      <span class="text-sm text-red-600 font-medium">Course is full - enrollment closed</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-12 bg-white rounded-xl shadow-sm border border-gray-200">
          <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">No courses found</h3>
          <p class="mt-1 text-sm text-gray-500">
            {{ filters.search || filters.subject || filters.level || filters.exam_board_id
              ? 'Try adjusting your filters'
              : 'No courses are currently available in the catalog.' }}
          </p>
          <div v-if="filters.search || filters.subject || filters.level || filters.exam_board_id" class="mt-6">
            <button
              @click="resetFilters"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-emerald-700 bg-emerald-100 hover:bg-emerald-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
            >
              Clear all filters
            </button>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="courses.links.length > 3" class="mt-8">
          <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
            <Link
              v-for="link in courses.links"
              :key="link.label"
              :href="link.url"
              preserve-scroll
              class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50"
              :class="{
                'bg-emerald-50 border-emerald-500 text-emerald-600': link.active,
                'rounded-l-md': link.label === '&laquo; Previous',
                'rounded-r-md': link.label === 'Next &raquo;'
              }"
              v-html="link.label"
            />
          </nav>
        </div>
      </div>
    </div>
  </StudentLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

const page = usePage()

const props = defineProps({
  courses: Object,
  enrolled_course_ids: Array,
  enrolled_courses: Object, // Add enrolled_courses prop
  subjects: Array,
  levels: Object,
  exam_boards: Array,
  total_courses: Number,
  filters: {
    type: Object,
    default: () => ({})
  },
})

// Filters
const filters = reactive({
  search: props.filters?.search || '',
  subject: props.filters?.subject || '',
  level: props.filters?.level || '',
  exam_board_id: props.filters?.exam_board_id || '',
})

// Helper methods
const getLevelBadgeClass = (level) => {
  const classes = {
    beginner: 'bg-green-100 text-green-800',
    intermediate: 'bg-yellow-100 text-yellow-800',
    advanced: 'bg-orange-100 text-orange-800',
    expert: 'bg-red-100 text-red-800',
  }
  return classes[level] || 'bg-gray-100 text-gray-800'
}

const isEnrolled = (courseId) => {
  return props.enrolled_course_ids.includes(courseId)
}

const getEnrolledCourseProgress = (courseId) => {
  const enrolledCourse = props.enrolled_courses[courseId]
  return enrolledCourse ? enrolledCourse.progress_percentage : null
}

const getEnrolledCourseId = (courseId) => {
  const enrolledCourse = props.enrolled_courses[courseId]
  return enrolledCourse ? enrolledCourse.id : null
}

// Search and Filters
let searchTimeout = null

const debouncedSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    updateFilters()
  }, 300)
}

const updateFilters = () => {
  router.get(route('student.catalog.index'), filters, {
    preserveState: true,
    replace: true,
  })
}

const resetFilters = () => {
  filters.search = ''
  filters.subject = ''
  filters.level = ''
  filters.exam_board_id = ''
  updateFilters()
}

// Enrollment Actions
const enrollCourse = async (course) => {
  try {
    await router.post(route('student.catalog.enroll', course.id), {}, {
      preserveScroll: true,
      onSuccess: () => {
        page.props.flash.success = 'Successfully enrolled in the course!'
      },
      onError: (errors) => {
        alert(errors.message || 'Failed to enroll in the course')
      }
    })
  } catch (error) {
    console.error('Enrollment failed:', error)
  }
}

const unenrollCourse = async (course) => {
  if (!confirm('Are you sure you want to unenroll from this course? All your progress will be lost.')) {
    return
  }

  try {
    await router.delete(route('student.catalog.unenroll', course.id), {
      preserveScroll: true,
      onSuccess: () => {
        page.props.flash.success = 'Successfully unenrolled from the course'
      },
      onError: (errors) => {
        alert(errors.message || 'Failed to unenroll from the course')
      }
    })
  } catch (error) {
    console.error('Unenrollment failed:', error)
  }
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
