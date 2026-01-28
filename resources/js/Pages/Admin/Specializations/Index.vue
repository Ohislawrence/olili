<!-- resources/js/Pages/Admin/Specializations/Index.vue -->
<template>
  <AdminLayout>
    <Head title="Specializations Management" />

    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
          <div class="flex justify-between items-center">
            <div>
              <h1 class="text-2xl font-bold text-gray-900">Specializations Management</h1>
              <p class="text-gray-600">Manage learning tracks and bundles</p>
            </div>
            <Link
              :href="route('admin.specializations.create')"
              class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-medium rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all duration-200"
            >
              <PlusCircleIcon class="h-5 w-5 mr-2" />
              Create Specialization
            </Link>
          </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
          <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-500">Total Tracks</p>
                <p class="text-2xl font-bold text-gray-900">{{ stats.total }}</p>
              </div>
              <div class="p-2 bg-blue-100 rounded-lg">
                <AcademicCapIcon class="h-6 w-6 text-blue-600" />
              </div>
            </div>
          </div>

          <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-500">Active Tracks</p>
                <p class="text-2xl font-bold text-green-600">{{ stats.active }}</p>
              </div>
              <div class="p-2 bg-green-100 rounded-lg">
                <CheckCircleIcon class="h-6 w-6 text-green-600" />
              </div>
            </div>
          </div>

          <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-500">Total Enrollments</p>
                <p class="text-2xl font-bold text-purple-600">{{ stats.total_enrollments }}</p>
              </div>
              <div class="p-2 bg-purple-100 rounded-lg">
                <UsersIcon class="h-6 w-6 text-purple-600" />
              </div>
            </div>
          </div>

          <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-500">Completion Rate</p>
                <p class="text-2xl font-bold text-amber-600">{{ stats.completion_rate }}%</p>
              </div>
              <div class="p-2 bg-amber-100 rounded-lg">
                <TrophyIcon class="h-6 w-6 text-amber-600" />
              </div>
            </div>
          </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6 shadow-sm">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
              <input
                v-model="filters.search"
                type="text"
                placeholder="Search tracks..."
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                @keyup.enter="applyFilters"
              />
            </div>

            <!-- Status Filter -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
              <select
                v-model="filters.status"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                @change="applyFilters"
              >
                <option value="">All Status</option>
                <option value="draft">Draft</option>
                <option value="active">Active</option>
                <option value="archived">Archived</option>
              </select>
            </div>

            <!-- Exam Filter -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Target Exam</label>
              <select
                v-model="filters.exam"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                @change="applyFilters"
              >
                <option value="">All Exams</option>
                <option v-for="exam in exams" :key="exam" :value="exam">{{ exam }}</option>
              </select>
            </div>

            <!-- Career Filter -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Career Path</label>
              <select
                v-model="filters.career"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                @change="applyFilters"
              >
                <option value="">All Careers</option>
                <option v-for="career in careers" :key="career" :value="career">{{ career }}</option>
              </select>
            </div>
          </div>

          <div class="flex justify-end mt-4">
            <button
              @click="resetFilters"
              class="px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors"
            >
              Reset Filters
            </button>
          </div>
        </div>

        <!-- Specializations Table -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Track
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Target
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Courses
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Enrollments
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Status
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="specialization in specializations.data" :key="specialization.id">
                  <td class="px-6 py-4">
                    <div class="flex items-center">
                      <div class="h-10 w-10 flex-shrink-0">
                        <img
                          v-if="specialization.thumbnail_url"
                          :src="specialization.thumbnail_url"
                          :alt="specialization.name"
                          class="h-10 w-10 rounded-lg object-cover"
                        />
                        <div v-else class="h-10 w-10 rounded-lg bg-gradient-to-r from-blue-100 to-purple-100 flex items-center justify-center">
                          <AcademicCapIcon class="h-5 w-5 text-blue-600" />
                        </div>
                      </div>
                      <div class="ml-4">
                        <div class="font-medium text-gray-900">{{ specialization.name }}</div>
                        <div class="text-sm text-gray-500 truncate max-w-xs">
                          {{ specialization.short_description }}
                        </div>
                        <div class="flex items-center mt-1">
                          <span
                            v-if="specialization.is_featured"
                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800 mr-2"
                          >
                            Featured
                          </span>
                          <span
                            v-if="!specialization.is_public"
                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800"
                          >
                            Private
                          </span>
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <div>
                      <div v-if="specialization.target_exam" class="text-sm font-medium text-gray-900">
                        {{ specialization.target_exam }}
                      </div>
                      <div v-if="specialization.target_career" class="text-sm text-gray-500">
                        {{ specialization.target_career }}
                      </div>
                      <div class="text-xs text-gray-400 mt-1">
                        {{ specialization.level }}
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ specialization.courses_count }} courses</div>
                    <div class="text-xs text-gray-500">
                      {{ specialization.estimated_duration_hours }}h total
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ specialization.enrollments_count }} enrolled</div>
                    <div class="text-xs text-gray-500">
                      {{ specialization.completed_enrollments_count }} completed
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span
                      :class="[
                        'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold',
                        getStatusClass(specialization.status)
                      ]"
                    >
                      {{ specialization.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex items-center space-x-2">
                      <Link
                        :href="route('admin.specializations.show', specialization.id)"
                        class="text-blue-600 hover:text-blue-900"
                      >
                        View
                      </Link>
                      <Link
                        :href="route('admin.specializations.edit', specialization.id)"
                        class="text-gray-600 hover:text-gray-900"
                      >
                        Edit
                      </Link>
                      <button
                        v-if="specialization.status === 'draft'"
                        @click="publishSpecialization(specialization)"
                        class="text-green-600 hover:text-green-900"
                      >
                        Publish
                      </button>
                      <button
                        v-else-if="specialization.status === 'active'"
                        @click="unpublishSpecialization(specialization)"
                        class="text-amber-600 hover:text-amber-900"
                      >
                        Unpublish
                      </button>
                      <button
                        @click="toggleFeatured(specialization)"
                        class="text-purple-600 hover:text-purple-900"
                      >
                        {{ specialization.is_featured ? 'Unfeature' : 'Feature' }}
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="px-6 py-4 border-t border-gray-200">
            <Pagination :links="specializations.links" />
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import Pagination from '@/Components/Pagination.vue'
import {
  AcademicCapIcon,
  PlusCircleIcon,
  CheckCircleIcon,
  UsersIcon,
  TrophyIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  specializations: Object,
  filters: Object,
  exams: Array,
  careers: Array,
  stats: Object,
})

const filters = ref(props.filters || {})

const applyFilters = () => {
  router.get(route('admin.specializations.index'), filters.value, {
    preserveState: true,
    replace: true,
  })
}

const resetFilters = () => {
  filters.value = {}
  applyFilters()
}

watch(filters, () => {
  applyFilters()
}, { deep: true })

const getStatusClass = (status) => {
  const classes = {
    draft: 'bg-gray-100 text-gray-800',
    active: 'bg-green-100 text-green-800',
    archived: 'bg-red-100 text-red-800',
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const publishSpecialization = (specialization) => {
  if (confirm('Are you sure you want to publish this specialization?')) {
    router.post(route('admin.specializations.publish', specialization.id), {}, {
      preserveScroll: true,
      onSuccess: () => {
        router.reload()
      },
    })
  }
}

const unpublishSpecialization = (specialization) => {
  if (confirm('Are you sure you want to unpublish this specialization?')) {
    router.post(route('admin.specializations.unpublish', specialization.id), {}, {
      preserveScroll: true,
      onSuccess: () => {
        router.reload()
      },
    })
  }
}

const toggleFeatured = (specialization) => {
  router.post(route('admin.specializations.toggle-featured', specialization.id), {}, {
    preserveScroll: true,
    onSuccess: () => {
      router.reload()
    },
  })
}
</script>
