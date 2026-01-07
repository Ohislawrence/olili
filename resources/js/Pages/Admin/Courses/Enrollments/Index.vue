<!-- resources/js/Pages/Admin/Courses/Enrollments/Index.vue -->
<template>
  <AdminLayout>
    <Head :title="`Enrollments - ${course.title}`" />

    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex justify-between items-center">
            <div>
              <div class="flex items-center space-x-3">
                <Link
                  :href="route('admin.courses.show', course.id)"
                  class="text-gray-400 hover:text-gray-600"
                >
                  <ArrowLeftIcon class="h-5 w-5" />
                </Link>
                <div>
                  <h1 class="text-2xl font-bold text-gray-900">Enrollments</h1>
                  <p class="mt-1 text-sm text-gray-600">
                    {{ course.title }} • {{ course.current_enrollment }} enrolled students
                  </p>
                </div>
              </div>
            </div>
            <div class="flex space-x-3">
              <Link
                :href="route('admin.courses.enrollments.create', course.id)"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                <PlusIcon class="h-4 w-4 mr-2" />
                Add Enrollment
              </Link>
              <Link
                :href="route('admin.courses.enrollments.export', course.id)"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                <ArrowDownTrayIcon class="h-4 w-4 mr-2" />
                Export CSV
              </Link>
            </div>
          </div>
        </div>

        <!-- Filters and Bulk Actions -->
        <div class="mb-6 bg-white shadow-sm rounded-lg border border-gray-200 p-4">
          <div class="flex flex-col sm:flex-row gap-4">
            <!-- Search -->
            <div class="flex-1">
              <input
                v-model="filters.search"
                type="text"
                placeholder="Search by name or email..."
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3"
                @input="search"
              />
            </div>

            <!-- Status Filter -->
            <div class="w-full sm:w-48">
              <select
                v-model="filters.status"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3"
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

            <!-- Bulk Actions -->
            <div class="w-full sm:w-48" v-if="selectedEnrollments.length > 0">
              <select
                v-model="bulkAction"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3"
                @change="applyBulkAction"
              >
                <option value="">Bulk Actions</option>
                <option value="active">Mark as Active</option>
                <option value="paused">Mark as Paused</option>
                <option value="completed">Mark as Completed</option>
                <option value="dropped">Mark as Dropped</option>
                <option value="delete">Remove Enrollments</option>
              </select>
            </div>
          </div>

          <!-- Import CSV -->
          <div class="mt-4 pt-4 border-t border-gray-200">
            <form @submit.prevent="importCSV" class="flex items-center space-x-3">
              <input
                type="file"
                ref="csvFile"
                accept=".csv,.txt"
                class="flex-1 text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
              />
              <button
                type="submit"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                <ArrowUpTrayIcon class="h-4 w-4 mr-2" />
                Import CSV
              </button>
              <Link
                :href="route('admin.courses.enrollments.export-template', course.id)"
                class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
              >
                Get Template
              </Link>
            </form>
          </div>
        </div>

        <!-- Enrollment Statistics -->
        <div class="mb-6 grid grid-cols-2 md:grid-cols-5 gap-4">
          <div
            v-for="status in enrollmentStats"
            :key="status.name"
            class="bg-white rounded-lg border border-gray-200 p-4 text-center hover:shadow-md transition-shadow"
          >
            <div class="text-2xl font-bold text-gray-900">{{ status.count }}</div>
            <div class="text-sm" :class="status.colorClass">{{ status.name }}</div>
          </div>
        </div>

        <!-- Enrollments Table -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <input
                      type="checkbox"
                      v-model="selectAll"
                      @change="toggleSelectAll"
                      class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                    />
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Student
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Status
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Progress
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Dates
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr
                  v-for="enrollment in enrollments.data"
                  :key="enrollment.id"
                  class="hover:bg-gray-50"
                >
                  <td class="px-6 py-4 whitespace-nowrap">
                    <input
                      type="checkbox"
                      :value="enrollment.id"
                      v-model="selectedEnrollments"
                      class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                    />
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10">
                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                          <span class="text-sm font-medium text-blue-800">
                            {{ enrollment.user.name.charAt(0).toUpperCase() }}
                          </span>
                        </div>
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">
                          {{ enrollment.user.name }}
                        </div>
                        <div class="text-sm text-gray-500">
                          {{ enrollment.user.email }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
                      :class="getStatusClass(enrollment.status)"
                    >
                      {{ enrollment.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center space-x-2">
                      <div class="w-32">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                          <div
                            class="h-2 rounded-full transition-all duration-300"
                            :class="getProgressColor(enrollment.progress_percentage)"
                            :style="{ width: `${enrollment.progress_percentage}%` }"
                          ></div>
                        </div>
                      </div>
                      <span class="text-sm text-gray-900">
                        {{ Math.round(enrollment.progress_percentage) }}%
                      </span>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <div>Enrolled: {{ formatDate(enrollment.enrolled_at) }}</div>
                    <div v-if="enrollment.started_at">Started: {{ formatDate(enrollment.started_at) }}</div>
                    <div v-if="enrollment.completed_at">Completed: {{ formatDate(enrollment.completed_at) }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex space-x-2">
                      <Link
                        :href="route('admin.courses.enrollments.show', { course: course.id, enrollment: enrollment.id })"
                        class="text-blue-600 hover:text-blue-900"
                      >
                        View
                      </Link>
                      <Link
                        :href="route('admin.courses.enrollments.edit', { course: course.id, enrollment: enrollment.id })"
                        class="text-green-600 hover:text-green-900"
                      >
                        Edit
                      </Link>
                      <button
                        @click="confirmDelete(enrollment)"
                        class="text-red-600 hover:text-red-900"
                      >
                        Remove
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Empty State -->
          <div v-if="enrollments.data.length === 0" class="text-center py-12">
            <UserGroupIcon class="h-12 w-12 mx-auto text-gray-400" />
            <h3 class="mt-2 text-sm font-semibold text-gray-900">No enrollments</h3>
            <p class="mt-1 text-sm text-gray-500">
              {{ filters.search || filters.status ? 'No enrollments match your filters.' : 'No students are enrolled in this course yet.' }}
            </p>
            <div class="mt-6">
              <Link
                :href="route('admin.courses.enrollments.create', course.id)"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700"
              >
                <PlusIcon class="h-4 w-4 mr-2" />
                Add Enrollment
              </Link>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="enrollments.data.length" class="mt-6">
          <Pagination :links="enrollments.links" />
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <ConfirmationModal
      :show="showDeleteModal"
      @close="showDeleteModal = false"
      @confirm="deleteEnrollment"
    >
      <template #title>
        Remove Enrollment
      </template>
      <template #content>
        Are you sure you want to remove
        <strong>{{ enrollmentToDelete?.user?.name || 'this student' }}</strong>
        from this course? This action cannot be undone.
      </template>
    </ConfirmationModal>
  </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import ConfirmationModal from '@/Components/ConfirmationModal.vue'
import {
  ArrowLeftIcon,
  PlusIcon,
  ArrowDownTrayIcon,
  ArrowUpTrayIcon,
  UserGroupIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  course: Object,
  enrollments: Object,
  statuses: Object,
  filters: { // Make sure this prop is defined
    type: Object,
    default: () => ({})
  }
})

// Initialize filters with safe defaults
const filters = ref({
  search: props.filters?.search || '', // Use optional chaining
  status: props.filters?.status || '',
})

const selectedEnrollments = ref([])
const selectAll = ref(false)
const bulkAction = ref('')
const showDeleteModal = ref(false)
const enrollmentToDelete = ref(null)
const csvFile = ref(null)

// Computed properties
const enrollmentStats = computed(() => {
  // Initialize with default structure
  const stats = {
    enrolled: { name: 'Enrolled', count: 0, colorClass: 'text-blue-600' },
    active: { name: 'Active', count: 0, colorClass: 'text-green-600' },
    paused: { name: 'Paused', count: 0, colorClass: 'text-amber-600' },
    completed: { name: 'Completed', count: 0, colorClass: 'text-teal-600' },
    dropped: { name: 'Dropped', count: 0, colorClass: 'text-red-600' },
  }

  // Safely calculate counts
  if (props.enrollments?.data) {
    props.enrollments.data.forEach(enrollment => {
      if (enrollment?.status && stats[enrollment.status]) {
        stats[enrollment.status].count++
      }
    })
  }

  return Object.values(stats)
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
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
  })
}

// Filtering
let searchTimeout = null
const search = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    filter()
  }, 300)
}

const filter = () => {
  router.get(route('admin.courses.enrollments.index', props.course.id), filters.value, {
    preserveState: true,
    replace: true,
  })
}

// Bulk actions
const toggleSelectAll = () => {
  if (selectAll.value) {
    selectedEnrollments.value = props.enrollments.data.map(e => e.id)
  } else {
    selectedEnrollments.value = []
  }
}

const applyBulkAction = async () => {
  if (!bulkAction.value || selectedEnrollments.value.length === 0) return

  if (bulkAction.value === 'delete') {
    if (confirm(`Are you sure you want to remove ${selectedEnrollments.value.length} enrollment(s)?`)) {
      await router.post(route('admin.courses.enrollments.bulk-destroy', props.course.id), {
        enrollment_ids: selectedEnrollments.value
      }, {
        preserveScroll: true,
        onSuccess: () => {
          selectedEnrollments.value = []
          selectAll.value = false
        }
      })
    }
  } else {
    await router.post(route('admin.courses.enrollments.bulk-update', props.course.id), {
      enrollment_ids: selectedEnrollments.value,
      status: bulkAction.value
    }, {
      preserveScroll: true,
      onSuccess: () => {
        selectedEnrollments.value = []
        selectAll.value = false
        bulkAction.value = ''
      }
    })
  }
}

// Individual enrollment actions
const confirmDelete = (enrollment) => {
  enrollmentToDelete.value = enrollment
  showDeleteModal.value = true
}

const deleteEnrollment = async () => {
  if (!enrollmentToDelete.value) return

  await router.delete(route('admin.courses.enrollments.destroy', {
    course: props.course.id,
    enrollment: enrollmentToDelete.value.id
  }), {
    preserveScroll: true,
    onSuccess: () => {
      showDeleteModal.value = false
      enrollmentToDelete.value = null
    }
  })
}

// CSV Import
const importCSV = async () => {
  if (!csvFile.value.files.length) {
    alert('Please select a CSV file to import')
    return
  }

  const formData = new FormData()
  formData.append('csv_file', csvFile.value.files[0])

  await router.post(route('admin.courses.enrollments.import', props.course.id), formData, {
    preserveScroll: true,
    onSuccess: () => {
      csvFile.value.value = ''
    }
  })
}
</script>
