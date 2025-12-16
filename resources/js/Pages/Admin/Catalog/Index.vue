<template>
  <AdminLayout>
    <Head title="Admin Courses" />

    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="md:flex md:items-center md:justify-between mb-6">
          <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
              Public Courses
            </h2>
            <p class="mt-1 text-sm text-gray-500">
              Create and manage courses for students to enroll in
            </p>
          </div>
          <div class="mt-4 md:mt-0 md:ml-4">
            <Link
              :href="route('admin.catalog.courses.create')"
              class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
            >
              <PlusIcon class="h-4 w-4 mr-2" />
              Create Course
            </Link>
          </div>
        </div>

        <!-- Filters -->
        <div class="bg-white shadow rounded-lg p-4 mb-6">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Visibility</label>
              <select
                v-model="filters.visibility"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm"
                @change="updateFilters"
              >
                <option value="">All</option>
                <option value="public">Public</option>
                <option value="private">Private</option>
                <option value="unlisted">Unlisted</option>
              </select>
            </div>
            <div class="flex items-end">
              <button
                @click="resetFilters"
                class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
              >
                Reset Filters
              </button>
            </div>
          </div>
        </div>

        <!-- Courses Grid -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
          <ul class="divide-y divide-gray-200">
            <li v-for="course in courses.data" :key="course.id">
              <div class="px-4 py-4 sm:px-6">
                <div class="flex items-center justify-between">
                  <div class="flex-1 min-w-0">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-16 w-16 mr-4">
                        <img
                          v-if="course.thumbnail_url"
                          :src="course.thumbnail_url"
                          :alt="course.title"
                          class="h-16 w-16 rounded-lg object-cover"
                        />
                        <div
                          v-else
                          class="h-16 w-16 rounded-lg bg-gradient-to-br from-emerald-100 to-teal-100 flex items-center justify-center"
                        >
                          <BookOpenIcon class="h-8 w-8 text-emerald-600" />
                        </div>
                      </div>
                      <div>
                        <div class="flex items-center">
                          <h3 class="text-lg font-medium text-gray-900 truncate">
                            {{ course.title }}
                          </h3>
                          <span
                            :class="getVisibilityBadgeClass(course.visibility)"
                            class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                          >
                            {{ course.visibility }}
                          </span>
                        </div>
                        <div class="mt-1">
                          <p class="text-sm text-gray-600">
                            {{ course.subject }} • {{ course.level }}
                          </p>
                          <div class="flex items-center mt-1">
                            <UserIcon class="h-4 w-4 text-gray-400 mr-1" />
                            <span class="text-sm text-gray-500">
                              {{ course.current_enrollment }} enrolled
                              <span v-if="course.enrollment_limit">
                                / {{ course.enrollment_limit }} max
                              </span>
                            </span>
                            <span class="mx-2">•</span>
                            <ClockIcon class="h-4 w-4 text-gray-400 mr-1" />
                            <span class="text-sm text-gray-500">
                              {{ course.estimated_duration_hours }} hours
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
                    <Link
                      :href="route('admin.catalog.courses.show', course.id)"
                      class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
                    >
                      View
                    </Link>
                    <Link
                      :href="route('admin.catalog.courses.edit', course.id)"
                      class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-emerald-700 bg-emerald-100 hover:bg-emerald-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
                    >
                      Edit
                    </Link>
                  </div>
                </div>
              </div>
            </li>
          </ul>

          <!-- Pagination -->
          <div v-if="courses.links.length > 3" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            <div class="flex justify-between">
              <div>
                <p class="text-sm text-gray-700">
                  Showing
                  <span class="font-medium">{{ courses.from }}</span>
                  to
                  <span class="font-medium">{{ courses.to }}</span>
                  of
                  <span class="font-medium">{{ courses.total }}</span>
                  results
                </p>
              </div>
              <div>
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
import {
  PlusIcon,
  BookOpenIcon,
  UserIcon,
  ClockIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  courses: Object,
  filters: Object,
})

const filters = ref({
  search: props.filters.search || '',
  visibility: props.filters.visibility || '',
})

let searchTimeout = null

const debouncedSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    updateFilters()
  }, 300)
}

const updateFilters = () => {
  router.get(route('admin.catalog.courses.index'), filters.value, {
    preserveState: true,
    replace: true,
  })
}

const resetFilters = () => {
  filters.value = { search: '', visibility: '' }
  updateFilters()
}

const getVisibilityBadgeClass = (visibility) => {
  const classes = {
    public: 'bg-green-100 text-green-800',
    private: 'bg-gray-100 text-gray-800',
    unlisted: 'bg-yellow-100 text-yellow-800',
  }
  return classes[visibility] || 'bg-gray-100 text-gray-800'
}
</script>
