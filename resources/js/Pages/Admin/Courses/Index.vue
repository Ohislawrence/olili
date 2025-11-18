<!-- resources/js/Pages/Admin/Courses/Index.vue -->
<template>
  <AdminLayout>
    <Head title="Course Management" />

    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex justify-between items-center">
            <div>
              <h1 class="text-3xl font-bold text-gray-900">Course Management</h1>
              <p class="mt-2 text-gray-600">
                Manage all courses and track student progress
              </p>
            </div>
            <Link
              :href="route('admin.courses.create')"
              class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
            >
              <PlusIcon class="h-4 w-4 mr-2" />
              Create Course
            </Link>
          </div>
        </div>

        <!-- Filters -->
        <div class="mb-6 bg-white rounded-lg shadow p-4">
          <div class="flex flex-wrap gap-4 items-center">
            <div class="flex-1 min-w-[300px]">
              <input
                v-model="filters.search"
                type="text"
                placeholder="Search courses..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                @input="search"
              />
            </div>
            <select
              v-model="filters.status"
              class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              @change="filter"
            >
              <option value="">All Status</option>
              <option value="active">Active</option>
              <option value="completed">Completed</option>
              <option value="draft">Draft</option>
            </select>
            <select
              v-model="filters.subject"
              class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              @change="filter"
            >
              <option value="">All Subjects</option>
              <option value="mathematics">Mathematics</option>
              <option value="science">Science</option>
              <option value="english">English</option>
              <option value="history">History</option>
            </select>
            <select
              v-model="filters.level"
              class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              @change="filter"
            >
              <option value="">All Levels</option>
              <option value="beginner">Beginner</option>
              <option value="intermediate">Intermediate</option>
              <option value="advanced">Advanced</option>
              <option value="expert">Expert</option>
            </select>
          </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <StatsCard
            title="Total Courses"
            :value="safeStats.total_courses"
            icon="academic-cap"
            color="blue"
          />
          <StatsCard
            title="Active Courses"
            :value="safeStats.active_courses"
            icon="chart-bar"
            color="green"
          />
          <StatsCard
            title="Completion Rate"
            :value="`${safeStats.completion_rate}%`"
            icon="trophy"
            color="purple"
          />
          <StatsCard
            title="Avg Progress"
            :value="`${safeStats.avg_progress}%`"
            icon="check-circle"
            color="orange"
          />
        </div>

        <!-- Courses Table -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Course
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Student
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Subject & Level
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Progress
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Status
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Timeline
                  </th>
                  <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr
                  v-for="course in courses.data"
                  :key="course.id"
                  class="hover:bg-gray-50 transition-colors duration-150"
                >
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10 bg-blue-500 rounded-lg flex items-center justify-center">
                        <AcademicCapIcon class="h-6 w-6 text-white" />
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">
                          {{ course.title }}
                        </div>
                        <div class="text-sm text-gray-500">
                          {{ course.outlines_count }} modules
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ course.student_profile?.user?.name }}</div>
                    <div class="text-sm text-gray-500">{{ course.student_profile?.user?.email }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900 capitalize">{{ course.subject }}</div>
                    <div class="text-sm text-gray-500 capitalize">{{ course.level }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                        <div
                          class="bg-blue-600 h-2 rounded-full"
                          :style="{ width: `${course.progress_percentage || 0}%` }"
                        ></div>
                      </div>
                      <span class="text-sm text-gray-700">{{ Math.round(course.progress_percentage || 0) }}%</span>
                    </div>
                    <div class="text-xs text-gray-500 mt-1">
                      {{ course.completed_outlines_count || 0 }}/{{ course.outlines_count || 0 }} completed
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
                      :class="getStatusClass(course.status)"
                    >
                      {{ course.status || 'draft' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <div>Start: {{ formatDate(course.start_date) }}</div>
                    <div>Target: {{ formatDate(course.target_completion_date) }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex justify-end space-x-2">
                      <Link
                        :href="route('admin.courses.show', course.id)"
                        class="text-blue-600 hover:text-blue-900 transition-colors duration-150"
                      >
                        View
                      </Link>
                      <Link
                        :href="route('admin.courses.edit', course.id)"
                        class="text-indigo-600 hover:text-indigo-900 transition-colors duration-150"
                      >
                        Edit
                      </Link>
                      <button
                        @click="deleteCourse(course)"
                        class="text-red-600 hover:text-red-900 transition-colors duration-150"
                      >
                        Delete
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Empty State -->
          <div
            v-if="courses.data.length === 0"
            class="text-center py-12"
          >
            <AcademicCapIcon class="mx-auto h-12 w-12 text-gray-400" />
            <h3 class="mt-2 text-sm font-medium text-gray-900">No courses</h3>
            <p class="mt-1 text-sm text-gray-500">
              Get started by creating a new course.
            </p>
            <div class="mt-6">
              <Link
                :href="route('admin.courses.create')"
                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                <PlusIcon class="h-4 w-4 mr-2" />
                New Course
              </Link>
            </div>
          </div>

          <!-- Pagination -->
          <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <Pagination :links="courses.links" />
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch, computed } from 'vue'
import { PlusIcon, AcademicCapIcon } from '@heroicons/vue/24/outline'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import StatsCard from '@/Components/Admin/StatsCard.vue'
import Pagination from '@/Components/Pagination.vue'

const props = defineProps({
  courses: Object,
  filters: Object,
  stats: Object,
})

// Safe computed property for stats with fallback values
const safeStats = computed(() => {
  return {
    total_courses: props.stats?.total_courses || 0,
    active_courses: props.stats?.active_courses || 0,
    completion_rate: props.stats?.completion_rate || 0,
    avg_progress: props.stats?.avg_progress || 0,
  }
})

const filters = ref({
  search: props.filters?.search || '',
  status: props.filters?.status || '',
  subject: props.filters?.subject || '',
  level: props.filters?.level || '',
})

let searchTimeout = null

const search = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    filter()
  }, 300)
}

const filter = () => {
  router.get(route('admin.courses.index'), filters.value, {
    preserveState: true,
    replace: true,
  })
}

const getStatusClass = (status) => {
  const classes = {
    active: 'bg-green-100 text-green-800',
    completed: 'bg-blue-100 text-blue-800',
    draft: 'bg-gray-100 text-gray-800',
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const formatDate = (dateString) => {
  if (!dateString) return 'Not set'
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
  })
}

const deleteCourse = (course) => {
  if (confirm(`Are you sure you want to delete "${course.title}"? This action cannot be undone.`)) {
    router.delete(route('admin.courses.destroy', course.id))
  }
}

watch(
  () => [filters.value.status, filters.value.subject, filters.value.level],
  () => {
    filter()
  }
)
</script>
