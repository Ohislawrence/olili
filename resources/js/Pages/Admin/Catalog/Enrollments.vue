<!-- resources/js/Pages/Admin/Courses/Enrollments.vue -->
<template>
  <AdminLayout>
    <Head :title="`Enrollments - ${course.title}`" />

    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="md:flex md:items-center md:justify-between mb-6">
          <div class="flex-1 min-w-0">
            <div class="flex items-center">
              <button
                @click="$inertia.visit(route('admin.catalog.courses.show', course.id))"
                class="mr-4 text-gray-400 hover:text-gray-600"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
              </button>
              <div>
                <h2 class="text-2xl font-bold text-gray-900">
                  Enrollments
                </h2>
                <div class="mt-1 flex items-center text-sm text-gray-600">
                  <span class="truncate">{{ course.title }}</span>
                  <span class="mx-2">•</span>
                  <span>{{ course.subject }} • {{ course.level }}</span>
                </div>
              </div>
            </div>
          </div>
          <div class="mt-4 md:mt-0 md:ml-4 flex space-x-3">
            <button
              @click="showBulkEnrollModal = true"
              class="inline-flex items-center px-4 py-2 border border-emerald-300 shadow-sm text-sm font-medium rounded-md text-emerald-700 bg-white hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              Bulk Enroll
            </button>
            <button
              @click="showEnrollModal = true"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
              </svg>
              Enroll Student
            </button>
          </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-1.205a9 9 0 01-13.5 2.201" />
                  </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">
                      Total Enrolled
                    </dt>
                    <dd class="text-lg font-medium text-gray-900">
                      {{ enrollments.total }}
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">
                      Active
                    </dt>
                    <dd class="text-lg font-medium text-gray-900">
                      {{ enrollments.data.filter(e => e.status === 'active').length }}
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">
                      Completed
                    </dt>
                    <dd class="text-lg font-medium text-gray-900">
                      {{ enrollments.data.filter(e => e.status === 'completed').length }}
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">
                      Avg. Progress
                    </dt>
                    <dd class="text-lg font-medium text-gray-900">
                      {{ calculateAverageProgress() }}%
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">
                      Last Enrollment
                    </dt>
                    <dd class="text-lg font-medium text-gray-900">
                      {{ formatDate(getLastEnrollmentDate()) }}
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Filters and Search -->
        <div class="bg-white shadow rounded-lg p-4 mb-6">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Search Students</label>
              <input
                v-model="filters.search"
                type="text"
                placeholder="Search by name or email..."
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm"
                @input="debouncedSearch"
              />
            </div>

            <!-- Status Filter -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
              <select
                v-model="filters.status"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm"
                @change="updateFilters"
              >
                <option value="">All Statuses</option>
                <option value="active">Active</option>
                <option value="completed">Completed</option>
                <option value="dropped">Dropped</option>
                <option value="pending">Pending</option>
              </select>
            </div>

            <!-- Progress Filter -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Progress</label>
              <select
                v-model="filters.progress"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm"
                @change="updateFilters"
              >
                <option value="">All Progress</option>
                <option value="0-25">0-25%</option>
                <option value="26-50">26-50%</option>
                <option value="51-75">51-75%</option>
                <option value="76-100">76-100%</option>
              </select>
            </div>

            <!-- Actions -->
            <div class="flex items-end space-x-2">
              <button
                @click="resetFilters"
                class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
              >
                Clear Filters
              </button>
              <button
                @click="exportEnrollments"
                class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Export
              </button>
            </div>
          </div>
        </div>

        <!-- Enrollments Table -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Student
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Enrollment Date
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Progress
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Status
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Last Accessed
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="enrollment in enrollments.data" :key="enrollment.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10">
                        <div class="h-10 w-10 rounded-full bg-emerald-100 flex items-center justify-center">
                          <span class="text-emerald-800 font-medium text-sm">
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
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ formatDate(enrollment.enrolled_at) }}
                    <div class="text-xs text-gray-400">
                      {{ formatRelativeTime(enrollment.enrolled_at) }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="w-32 mr-3">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                          <div
                            class="bg-emerald-600 h-2 rounded-full transition-all duration-300"
                            :style="{ width: `${enrollment.progress_percentage || 0}%` }"
                          ></div>
                        </div>
                      </div>
                      <span class="text-sm font-medium text-gray-900">
                        {{ enrollment.progress_percentage || 0 }}%
                      </span>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getStatusBadgeClass(enrollment.status)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                      {{ enrollment.status }}
                    </span>
                    <div v-if="enrollment.completed_at" class="text-xs text-gray-500 mt-1">
                      Completed: {{ formatDate(enrollment.completed_at) }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <div v-if="enrollment.last_accessed_at">
                      {{ formatDate(enrollment.last_accessed_at) }}
                      <div class="text-xs text-gray-400">
                        {{ formatRelativeTime(enrollment.last_accessed_at) }}
                      </div>
                    </div>
                    <span v-else class="text-gray-400">Never</span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex space-x-2">
                      <button
                        @click="viewStudentProgress(enrollment.user_id)"
                        class="text-emerald-600 hover:text-emerald-900"
                        title="View Progress"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                      </button>
                      <button
                        @click="sendMessage(enrollment.user)"
                        class="text-blue-600 hover:text-blue-900"
                        title="Send Message"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                      </button>
                      <button
                        v-if="enrollment.status !== 'completed'"
                        @click="markAsCompleted(enrollment)"
                        class="text-green-600 hover:text-green-900"
                        title="Mark as Completed"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                      </button>
                      <button
                        @click="unenrollStudent(enrollment)"
                        class="text-red-600 hover:text-red-900"
                        title="Unenroll"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Empty State -->
          <div v-if="enrollments.data.length === 0" class="text-center py-12">
            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-1.205a9 9 0 01-13.5 2.201" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No enrollments found</h3>
            <p class="mt-1 text-sm text-gray-500">
              {{ filters.search || filters.status || filters.progress ? 'Try adjusting your filters' : 'Get started by enrolling students' }}
            </p>
            <div class="mt-6">
              <button
                @click="showEnrollModal = true"
                type="button"
                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                Enroll Student
              </button>
            </div>
          </div>

          <!-- Pagination -->
          <nav v-if="enrollments.links.length > 3" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            <div class="flex items-center justify-between">
              <div class="hidden sm:block">
                <p class="text-sm text-gray-700">
                  Showing
                  <span class="font-medium">{{ enrollments.from }}</span>
                  to
                  <span class="font-medium">{{ enrollments.to }}</span>
                  of
                  <span class="font-medium">{{ enrollments.total }}</span>
                  results
                </p>
              </div>
              <div class="flex-1 flex justify-between sm:justify-end">
                <Link
                  :href="enrollments.prev_page_url"
                  preserve-scroll
                  :disabled="!enrollments.prev_page_url"
                  class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
                >
                  Previous
                </Link>
                <Link
                  :href="enrollments.next_page_url"
                  preserve-scroll
                  :disabled="!enrollments.next_page_url"
                  class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
                >
                  Next
                </Link>
              </div>
            </div>
          </nav>
        </div>
      </div>
    </div>

    <!-- Enroll Student Modal -->
    <Transition
      enter-active-class="duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="showEnrollModal" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
          <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showEnrollModal = false"></div>

          <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

          <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full sm:p-6">
            <div class="sm:flex sm:items-start">
              <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-emerald-100 sm:mx-0 sm:h-10 sm:w-10">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
              </div>
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                  Enroll Student
                </h3>
                <div class="mt-4">
                  <div class="space-y-4">
                    <!-- Student Search -->
                    <div>
                      <label for="student-search" class="block text-sm font-medium text-gray-700 mb-1">
                        Search Students
                      </label>
                      <div class="relative">
                        <input
                          id="student-search"
                          v-model="studentSearch"
                          type="text"
                          class="block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm"
                          placeholder="Type to search students..."
                          @input="searchStudents"
                        />
                        <!-- Search Results -->
                        <div v-if="studentSearch && studentSearchResults.length > 0" class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                          <div
                            v-for="student in studentSearchResults"
                            :key="student.id"
                            class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-emerald-50"
                            @click="selectStudent(student)"
                          >
                            <div class="flex items-center">
                              <div class="flex-shrink-0 h-8 w-8 rounded-full bg-emerald-100 flex items-center justify-center mr-3">
                                <span class="text-emerald-800 font-medium text-xs">
                                  {{ student.name.charAt(0).toUpperCase() }}
                                </span>
                              </div>
                              <div class="min-w-0 flex-1">
                                <div class="text-sm font-medium text-gray-900 truncate">
                                  {{ student.name }}
                                </div>
                                <div class="text-sm text-gray-500 truncate">
                                  {{ student.email }}
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Selected Student -->
                    <div v-if="selectedStudent" class="bg-emerald-50 border border-emerald-200 rounded-lg p-4">
                      <div class="flex items-center justify-between">
                        <div class="flex items-center">
                          <div class="flex-shrink-0 h-10 w-10 rounded-full bg-emerald-100 flex items-center justify-center mr-3">
                            <span class="text-emerald-800 font-medium text-sm">
                              {{ selectedStudent.name.charAt(0).toUpperCase() }}
                            </span>
                          </div>
                          <div>
                            <div class="text-sm font-medium text-gray-900">
                              {{ selectedStudent.name }}
                            </div>
                            <div class="text-sm text-gray-500">
                              {{ selectedStudent.email }}
                            </div>
                          </div>
                        </div>
                        <button
                          @click="selectedStudent = null"
                          type="button"
                          class="text-gray-400 hover:text-gray-600"
                        >
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                          </svg>
                        </button>
                      </div>
                    </div>

                    <!-- Manual Enrollment -->
                    <div v-if="!selectedStudent" class="pt-4 border-t border-gray-200">
                      <div class="text-sm text-gray-600 mb-3">
                        Or enroll manually by student ID:
                      </div>
                      <input
                        v-model="manualStudentId"
                        type="text"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm"
                        placeholder="Enter student ID"
                      />
                    </div>

                    <!-- Enrollment Options -->
                    <div class="pt-4 border-t border-gray-200">
                      <label class="block text-sm font-medium text-gray-700 mb-2">
                        Enrollment Options
                      </label>
                      <div class="space-y-2">
                        <div class="flex items-center">
                          <input
                            id="status-active"
                            v-model="enrollmentStatus"
                            type="radio"
                            value="active"
                            class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300"
                          />
                          <label for="status-active" class="ml-3 block text-sm text-gray-700">
                            Active
                            <p class="text-gray-500 text-xs">Student can start learning immediately</p>
                          </label>
                        </div>
                        <div class="flex items-center">
                          <input
                            id="status-pending"
                            v-model="enrollmentStatus"
                            type="radio"
                            value="pending"
                            class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300"
                          />
                          <label for="status-pending" class="ml-3 block text-sm text-gray-700">
                            Pending
                            <p class="text-gray-500 text-xs">Student needs to confirm enrollment</p>
                          </label>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
              <button
                type="button"
                @click="enrollSelectedStudent"
                :disabled="!selectedStudent && !manualStudentId"
                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-emerald-600 text-base font-medium text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
              >
                Enroll Student
              </button>
              <button
                type="button"
                @click="showEnrollModal = false"
                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 sm:mt-0 sm:w-auto sm:text-sm"
              >
                Cancel
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Bulk Enroll Modal -->
    <Transition
      enter-active-class="duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="showBulkEnrollModal" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
          <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showBulkEnrollModal = false"></div>

          <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

          <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full sm:p-6">
            <div>
              <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
              </div>
              <div class="mt-3 text-center sm:mt-5">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                  Bulk Enroll Students
                </h3>
                <div class="mt-4">
                  <div class="space-y-4">
                    <!-- CSV Upload -->
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                      <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                      </svg>
                      <p class="mt-2 text-sm text-gray-600">
                        Upload a CSV file with student emails
                      </p>
                      <input
                        ref="csvInput"
                        type="file"
                        accept=".csv"
                        class="hidden"
                        @change="handleCsvUpload"
                      />
                      <button
                        @click="$refs.csvInput.click()"
                        class="mt-3 inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
                      >
                        Choose CSV File
                      </button>
                      <p class="mt-2 text-xs text-gray-500">
                        CSV format: email (one per line)
                      </p>
                    </div>

                    <!-- Manual Entry -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">
                        Or enter emails manually (one per line)
                      </label>
                      <textarea
                        v-model="bulkEmails"
                        rows="6"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm"
                        placeholder="student1@example.com&#10;student2@example.com"
                      ></textarea>
                    </div>

                    <!-- Preview -->
                    <div v-if="bulkEmailList.length > 0" class="bg-gray-50 rounded-lg p-4">
                      <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-700">
                          {{ bulkEmailList.length }} students ready to enroll
                        </span>
                        <button
                          @click="clearBulkEmails"
                          class="text-sm text-red-600 hover:text-red-800"
                        >
                          Clear All
                        </button>
                      </div>
                      <div class="max-h-40 overflow-y-auto">
                        <div v-for="(email, index) in bulkEmailList" :key="index" class="text-sm text-gray-600 py-1 border-b border-gray-200 last:border-0">
                          {{ email }}
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3">
              <button
                type="button"
                @click="processBulkEnrollment"
                :disabled="bulkEmailList.length === 0"
                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-emerald-600 text-base font-medium text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 sm:text-sm disabled:opacity-50"
              >
                Enroll {{ bulkEmailList.length }} Students
              </button>
              <button
                type="button"
                @click="showBulkEnrollModal = false"
                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 sm:mt-0 sm:text-sm"
              >
                Cancel
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

const page = usePage()

const props = defineProps({
  course: Object,
  enrollments: Object,
  filters: Object,
})

// State
const showEnrollModal = ref(false)
const showBulkEnrollModal = ref(false)
const studentSearch = ref('')
const studentSearchResults = ref([])
const selectedStudent = ref(null)
const manualStudentId = ref('')
const enrollmentStatus = ref('active')
const bulkEmails = ref('')
const csvInput = ref(null)

// Filters
const filters = reactive({
  search: '',
  status: '',
  progress: '',
})
onMounted(() => {
  if (props.filters) {
    filters.search = props.filters.search || ''
    filters.status = props.filters.status || ''
    filters.progress = props.filters.progress || ''
  }
})

// Computed
const bulkEmailList = computed(() => {
  return bulkEmails.value
    .split('\n')
    .map(email => email.trim())
    .filter(email => email && isValidEmail(email))
})

// Methods
const getStatusBadgeClass = (status) => {
  const classes = {
    active: 'bg-green-100 text-green-800',
    completed: 'bg-blue-100 text-blue-800',
    dropped: 'bg-red-100 text-red-800',
    pending: 'bg-yellow-100 text-yellow-800',
  }
  return classes[status?.toLowerCase()] || 'bg-gray-100 text-gray-800'
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const formatRelativeTime = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  const now = new Date()
  const diffInDays = Math.floor((now - date) / (1000 * 60 * 60 * 24))

  if (diffInDays === 0) return 'Today'
  if (diffInDays === 1) return 'Yesterday'
  if (diffInDays < 7) return `${diffInDays} days ago`
  if (diffInDays < 30) return `${Math.floor(diffInDays / 7)} weeks ago`
  if (diffInDays < 365) return `${Math.floor(diffInDays / 30)} months ago`
  return `${Math.floor(diffInDays / 365)} years ago`
}

const calculateAverageProgress = () => {
  const enrollments = props.enrollments.data
  if (enrollments.length === 0) return 0

  const total = enrollments.reduce((sum, enrollment) => {
    return sum + (enrollment.progress_percentage || 0)
  }, 0)

  return Math.round(total / enrollments.length)
}

const getLastEnrollmentDate = () => {
  if (props.enrollments.data.length === 0) return null
  return props.enrollments.data[0].enrolled_at // Assuming sorted by latest
}

const isValidEmail = (email) => {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return re.test(email)
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
  router.get(route('admin.catalog.search.student', props.course.id), filters, {
    preserveState: true,
    replace: true,
  })
}

const resetFilters = () => {
  filters.search = ''
  filters.status = ''
  filters.progress = ''
  updateFilters()
}

// Student Search
const searchStudents = async () => {
  if (!studentSearch.value.trim() || studentSearch.value.length < 2) {
    studentSearchResults.value = []
    return
  }

  try {
    const response = await fetch(route('admin.catalog.search.student'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
      body: JSON.stringify({
        search: studentSearch.value,
        exclude_enrolled: true,
        course_id: props.course.id,
      }),
    })

    if (response.ok) {
      const data = await response.json()
      studentSearchResults.value = data.students || []
    }
  } catch (error) {
    console.error('Student search failed:', error)
  }
}

const selectStudent = (student) => {
  selectedStudent.value = student
  studentSearch.value = ''
  studentSearchResults.value = []
}

// Enrollment Actions
const enrollSelectedStudent = async () => {
  let userId = selectedStudent.value?.id || manualStudentId.value

  if (!userId) {
    alert('Please select a student or enter a student ID')
    return
  }

  try {
    await router.post(route('admin.catalog.courses.toggle-enrollment', props.course.id), {
      user_id: userId,
      action: 'enroll',
      status: enrollmentStatus.value,
    }, {
      preserveScroll: true,
      onSuccess: () => {
        showEnrollModal.value = false
        selectedStudent.value = null
        manualStudentId.value = ''
        studentSearch.value = ''
        page.props.flash.success = 'Student enrolled successfully'
      },
      onError: (errors) => {
        alert(errors.message || 'Failed to enroll student')
      }
    })
  } catch (error) {
    console.error('Enrollment failed:', error)
  }
}

const unenrollStudent = async (enrollment) => {
  if (!confirm(`Are you sure you want to unenroll ${enrollment.user.name}? This action cannot be undone.`)) {
    return
  }

  try {
    await router.post(route('admin.catalog.courses.toggle-enrollment', props.course.id), {
      user_id: enrollment.user_id,
      action: 'unenroll',
    }, {
      preserveScroll: true,
      onSuccess: () => {
        page.props.flash.success = 'Student unenrolled successfully'
      }
    })
  } catch (error) {
    console.error('Unenrollment failed:', error)
  }
}

const markAsCompleted = async (enrollment) => {
  if (!confirm(`Mark ${enrollment.user.name}'s enrollment as completed?`)) {
    return
  }

  try {
    await router.put(route('admin.catalog.enrollments.update', enrollment.id), {
      status: 'completed',
      completed_at: new Date().toISOString(),
    }, {
      preserveScroll: true,
      onSuccess: () => {
        page.props.flash.success = 'Enrollment marked as completed'
      }
    })
  } catch (error) {
    console.error('Failed to mark as completed:', error)
  }
}

const viewStudentProgress = (userId) => {
  // Navigate to student progress page or open modal
  alert(`View progress for user ${userId}`)
  // You would implement this based on your progress tracking system
}

const sendMessage = (user) => {
  // Open messaging modal or redirect to messaging
  alert(`Send message to ${user.name}`)
  // Implement messaging functionality
}

// Bulk Enrollment
const handleCsvUpload = (event) => {
  const file = event.target.files[0]
  if (!file) return

  const reader = new FileReader()
  reader.onload = (e) => {
    const content = e.target.result
    const lines = content.split('\n')
    const emails = lines
      .map(line => line.trim())
      .filter(line => line && !line.startsWith('#'))
      .map(line => line.split(',')[0].trim()) // Take first column

    bulkEmails.value = [...new Set(emails)].join('\n')
  }
  reader.readAsText(file)
}

const clearBulkEmails = () => {
  bulkEmails.value = ''
}

const processBulkEnrollment = async () => {
  if (bulkEmailList.value.length === 0) return

  if (!confirm(`Enroll ${bulkEmailList.value.length} students?`)) {
    return
  }

  try {
    await router.post(route('admin.catalog.courses.bulk-enroll', props.course.id), {
      emails: bulkEmailList.value,
      status: enrollmentStatus.value,
    }, {
      preserveScroll: true,
      onSuccess: () => {
        showBulkEnrollModal.value = false
        bulkEmails.value = ''
        page.props.flash.success = `Successfully enrolled ${bulkEmailList.value.length} students`
      },
      onError: (errors) => {
        alert(errors.message || 'Bulk enrollment failed')
      }
    })
  } catch (error) {
    console.error('Bulk enrollment failed:', error)
  }
}

// Export
const exportEnrollments = () => {
  // Implement CSV export
  alert('Export functionality to be implemented')
  // You would generate and download a CSV file
}

// Initialize
onMounted(() => {
  // Any initialization code
})
</script>

<style scoped>
/* Smooth transitions */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

/* Progress bar animation */
.progress-bar {
  transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Custom scrollbar */
.max-h-60 {
  scrollbar-width: thin;
  scrollbar-color: #a3d9a5 #f0fdf4;
}

.max-h-60::-webkit-scrollbar {
  width: 6px;
}

.max-h-60::-webkit-scrollbar-track {
  background: #f0fdf4;
  border-radius: 3px;
}

.max-h-60::-webkit-scrollbar-thumb {
  background-color: #a3d9a5;
  border-radius: 3px;
}
</style>
