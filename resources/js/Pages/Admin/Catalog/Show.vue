<!-- resources/js/Pages/Admin/Courses/Show.vue -->
<template>
  <AdminLayout>
    <Head :title="`Course: ${course.title}`" />

    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header with Actions -->
        <div class="md:flex md:items-center md:justify-between mb-6">
          <div class="flex-1 min-w-0">
            <div class="flex items-center">
              <button
                @click="$inertia.visit(route('admin.catalog.courses.index'))"
                class="mr-4 text-gray-400 hover:text-gray-600"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
              </button>
              <div>
                <div class="flex items-center">
                  <h1 class="text-2xl font-bold text-gray-900 truncate mr-3">
                    {{ course.title }}
                  </h1>
                  <span :class="getVisibilityBadgeClass(course.visibility)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                    {{ course.visibility }}
                  </span>
                  <span v-if="course.status" :class="getStatusBadgeClass(course.status)" class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                    {{ course.status }}
                  </span>
                </div>
                <p class="mt-1 text-sm text-gray-600">
                  {{ course.subject }} • {{ course.level }} • {{ course.estimated_duration_hours }} hours
                </p>
              </div>
            </div>
          </div>
          <div class="mt-4 md:mt-0 md:ml-4 flex space-x-3">
            <Link
              :href="route('admin.catalog.courses.edit', course.id)"
              class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
              Edit Course
            </Link>
            <button
              @click="deleteCourse"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
              Delete
            </button>
          </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
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
                      {{ enrollment_stats.total }}
                      <span v-if="course.enrollment_limit" class="text-sm text-gray-500">
                        / {{ course.enrollment_limit }}
                      </span>
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
                      Active Students
                    </dt>
                    <dd class="text-lg font-medium text-gray-900">
                      {{ enrollment_stats.active }}
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                  </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">
                      Completed
                    </dt>
                    <dd class="text-lg font-medium text-gray-900">
                      {{ enrollment_stats.completed }}
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">
                      Created
                    </dt>
                    <dd class="text-lg font-medium text-gray-900">
                      {{ formatDate(course.created_at) }}
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Course Details Tabs -->
        <div class="mb-8">
          <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8">
              <button
                @click="activeTab = 'overview'"
                :class="activeTab === 'overview' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
              >
                Overview
              </button>
              <button
                @click="activeTab = 'modules'"
                :class="activeTab === 'modules' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
              >
                Course Content
              </button>
              <button
                @click="activeTab = 'enrollments'"
                :class="activeTab === 'enrollments' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
              >
                Enrollments
                <span class="ml-2 bg-gray-200 text-gray-800 text-xs font-semibold px-2 py-0.5 rounded-full">
                  {{ enrollment_stats.total }}
                </span>
              </button>
              <button
                @click="activeTab = 'settings'"
                :class="activeTab === 'settings' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
              >
                Settings
              </button>
            </nav>
          </div>
        </div>

        <!-- Tab Content -->
        <div class="bg-white shadow rounded-lg overflow-hidden">

          <!-- Overview Tab -->
          <div v-if="activeTab === 'overview'" class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
              <!-- Left Column: Course Info -->
              <div class="lg:col-span-2">
                <!-- Thumbnail -->
                <div v-if="course.thumbnail_url" class="mb-6">
                  <img :src="course.thumbnail_url" :alt="course.title" class="w-full h-64 object-cover rounded-lg shadow-md">
                </div>

                <!-- Description -->
                <div class="mb-8">
                  <h3 class="text-lg font-medium text-gray-900 mb-3">Description</h3>
                  <div class="prose max-w-none">
                    <p class="text-gray-700 whitespace-pre-wrap">{{ course.description }}</p>
                  </div>
                </div>

                <!-- Learning Objectives -->
                <div class="mb-8" v-if="course.learning_objectives && course.learning_objectives.length">
                  <h3 class="text-lg font-medium text-gray-900 mb-3">Learning Objectives</h3>
                  <ul class="space-y-2">
                    <li v-for="(objective, index) in course.learning_objectives" :key="index" class="flex items-start">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-500 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      <span class="text-gray-700">{{ objective }}</span>
                    </li>
                  </ul>
                </div>

                <!-- Prerequisites -->
                <div v-if="course.prerequisites && course.prerequisites.length">
                  <h3 class="text-lg font-medium text-gray-900 mb-3">Prerequisites</h3>
                  <div class="flex flex-wrap gap-2">
                    <span v-for="(prereq, index) in course.prerequisites" :key="index" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                      {{ prereq }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Right Column: Course Details -->
              <div>
                <div class="bg-gray-50 rounded-lg p-5">
                  <h3 class="text-lg font-medium text-gray-900 mb-4">Course Details</h3>

                  <div class="space-y-4">
                    <!-- Creator -->
                    <div v-if="course.creator">
                      <dt class="text-sm font-medium text-gray-500">Created By</dt>
                      <dd class="mt-1 text-sm text-gray-900 flex items-center">
                        <div class="h-8 w-8 rounded-full bg-emerald-100 flex items-center justify-center mr-2">
                          <span class="text-emerald-800 font-medium text-sm">
                            {{ course.creator.name.charAt(0).toUpperCase() }}
                          </span>
                        </div>
                        {{ course.creator.name }}
                      </dd>
                    </div>

                    <!-- Exam Board -->
                    <div v-if="course.exam_board">
                      <dt class="text-sm font-medium text-gray-500">Exam Board</dt>
                      <dd class="mt-1 text-sm text-gray-900">{{ course.exam_board.name }}</dd>
                    </div>

                    <!-- Level -->
                    <div>
                      <dt class="text-sm font-medium text-gray-500">Level</dt>
                      <dd class="mt-1 text-sm text-gray-900">{{ course.level }}</dd>
                    </div>

                    <!-- Duration -->
                    <div>
                      <dt class="text-sm font-medium text-gray-500">Estimated Duration</dt>
                      <dd class="mt-1 text-sm text-gray-900">{{ course.estimated_duration_hours }} hours</dd>
                    </div>

                    <!-- Target Date -->
                    <div v-if="course.target_completion_date">
                      <dt class="text-sm font-medium text-gray-500">Suggested Completion</dt>
                      <dd class="mt-1 text-sm text-gray-900">{{ formatDate(course.target_completion_date) }}</dd>
                    </div>

                    <!-- Created Date -->
                    <div>
                      <dt class="text-sm font-medium text-gray-500">Created On</dt>
                      <dd class="mt-1 text-sm text-gray-900">{{ formatDate(course.created_at) }}</dd>
                    </div>

                    <!-- Status -->
                    <div>
                      <dt class="text-sm font-medium text-gray-500">Status</dt>
                      <dd class="mt-1">
                        <span :class="getStatusBadgeClass(course.status)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                          {{ course.status }}
                        </span>
                      </dd>
                    </div>

                    <!-- Enrollment Status -->
                    <div>
                      <dt class="text-sm font-medium text-gray-500">Enrollment</dt>
                      <dd class="mt-1">
                        <span :class="getEnrollmentStatusClass()" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                          {{ enrollment_stats.total }} enrolled
                          <span v-if="course.enrollment_limit">/ {{ course.enrollment_limit }}</span>
                        </span>
                      </dd>
                    </div>

                    <!-- Share Link -->
                    <div v-if="course.is_public && course.visibility === 'public'">
                      <dt class="text-sm font-medium text-gray-500 mb-1">Course Link</dt>
                      <dd class="mt-1">
                        <div class="flex">
                          <input
                            type="text"
                            readonly
                            :value="coursePublicUrl"
                            class="flex-1 block w-full min-w-0 rounded-l-md border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm"
                          />
                          <button
                            @click="copyCourseLink"
                            class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 rounded-r-md bg-gray-50 text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500"
                          >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                            </svg>
                          </button>
                        </div>
                      </dd>
                    </div>
                  </div>
                </div>

                <!-- Quick Actions -->
                <div class="mt-4 bg-gray-50 rounded-lg p-5">
                  <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>

                  <div class="space-y-3">
                    <Link
                      :href="route('admin.catalog.courses.enrollments', course.id)"
                      class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-1.205a9 9 0 01-13.5 2.201" />
                      </svg>
                      Manage Enrollments
                    </Link>

                    <button
                      @click="toggleCourseStatus"
                      :class="course.status === 'active' ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-emerald-600 hover:bg-emerald-700'"
                      class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path v-if="course.status === 'active'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                      </svg>
                      {{ course.status === 'active' ? 'Pause Course' : 'Activate Course' }}
                    </button>

                    <button
                      @click="toggleVisibility"
                      :class="course.visibility === 'public' ? 'bg-gray-600 hover:bg-gray-700' : 'bg-blue-600 hover:bg-blue-700'"
                      class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path v-if="course.visibility === 'public'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L6.59 6.59m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />

                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                      {{ course.visibility === 'public' ? 'Make Private' : 'Make Public' }}
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Modules Tab -->
          <div v-else-if="activeTab === 'modules'" class="p-6">
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-lg font-medium text-gray-900">Course Content</h3>
              <div class="text-sm text-gray-500">
                {{ course.modules.length }} modules • {{ totalTopics }} topics
              </div>
            </div>

            <div class="space-y-6">
              <div v-for="module in course.modules" :key="module.id" class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <!-- Module Header -->
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                  <div class="flex items-center justify-between">
                    <div>
                      <h4 class="text-lg font-medium text-gray-900">{{ module.title }}</h4>
                      <p class="mt-1 text-sm text-gray-600">{{ module.description }}</p>
                    </div>
                    <div class="flex items-center text-sm text-gray-500">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      {{ module.estimated_duration_minutes }} minutes
                      <span class="mx-2">•</span>
                      {{ module.topics.length }} topics
                    </div>
                  </div>
                </div>

                <!-- Topics List -->
                <div class="divide-y divide-gray-200">
                  <div v-for="topic in module.topics" :key="topic.id" class="px-6 py-4 hover:bg-gray-50">
                    <div class="flex items-start">
                      <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center mr-3 mt-1">
                        <span class="text-gray-600 text-sm font-medium">{{ topic.order }}</span>
                      </div>
                      <div class="flex-1 min-w-0">
                        <div class="flex items-center">
                          <h5 class="text-sm font-medium text-gray-900">{{ topic.title }}</h5>
                          <span v-if="topic.type" class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium" :class="getTopicTypeClass(topic.type)">
                            {{ topic.type }}
                          </span>
                        </div>
                        <p class="mt-1 text-sm text-gray-600">{{ topic.description }}</p>
                        <div class="mt-2 flex items-center text-xs text-gray-500">
                          <div v-if="topic.has_quiz" class="flex items-center mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Quiz
                          </div>
                          <div v-if="topic.has_project" class="flex items-center mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                            </svg>
                            Project
                          </div>
                          <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ topic.estimated_duration_minutes }} min
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Enrollments Tab -->
          <div v-else-if="activeTab === 'enrollments'" class="p-6">
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-lg font-medium text-gray-900">Student Enrollments</h3>
              <div class="flex space-x-3">
                <!-- Enrollment Stats -->
                <div class="flex items-center text-sm text-gray-500">
                  <span class="font-medium text-gray-900">{{ enrollment_stats.total }}</span>
                  <span class="mx-1">total</span>
                  <span class="mx-2">•</span>
                  <span class="font-medium text-emerald-600">{{ enrollment_stats.active }}</span>
                  <span class="mx-1">active</span>
                  <span class="mx-2">•</span>
                  <span class="font-medium text-green-600">{{ enrollment_stats.completed }}</span>
                  <span class="mx-1">completed</span>
                </div>
              </div>
            </div>

            <!-- Recent Enrollments Table -->
            <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
              <table class="min-w-full divide-y divide-gray-300">
                <thead class="bg-gray-50">
                  <tr>
                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                      Student
                    </th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                      Enrollment Date
                    </th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                      Progress
                    </th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                      Status
                    </th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                      Last Accessed
                    </th>
                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                      <span class="sr-only">Actions</span>
                    </th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                  <tr v-for="enrollment in recent_enrollments" :key="enrollment.id">
                    <td class="whitespace-nowrap py-4 pl-4 pr-3 sm:pl-6">
                      <div class="flex items-center">
                        <div class="h-10 w-10 flex-shrink-0">
                          <div class="h-10 w-10 rounded-full bg-emerald-100 flex items-center justify-center">
                            <span class="text-emerald-800 font-medium text-sm">
                              {{ enrollment.user.name.charAt(0).toUpperCase() }}
                            </span>
                          </div>
                        </div>
                        <div class="ml-4">
                          <div class="font-medium text-gray-900">{{ enrollment.user.name }}</div>
                          <div class="text-gray-500">{{ enrollment.user.email }}</div>
                        </div>
                      </div>
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                      {{ formatDate(enrollment.enrolled_at) }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm">
                      <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-emerald-600 h-2 rounded-full" :style="{ width: `${enrollment.progress_percentage || 0}%` }"></div>
                      </div>
                      <div class="text-xs text-gray-500 mt-1">{{ enrollment.progress_percentage || 0 }}% complete</div>
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm">
                      <span :class="getEnrollmentStatusBadgeClass(enrollment.status)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                        {{ enrollment.status }}
                      </span>
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                      {{ enrollment.last_accessed_at ? formatDate(enrollment.last_accessed_at) : 'Never' }}
                    </td>
                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                      <button
                        @click="unenrollStudent(enrollment.user_id)"
                        class="text-red-600 hover:text-red-900"
                      >
                        Unenroll
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- View All Enrollments Link -->
            <div class="mt-6 text-center">
              <Link
                :href="route('admin.catalog.courses.enrollments', course.id)"
                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
              >
                View All Enrollments
              </Link>
            </div>
          </div>

          <!-- Settings Tab -->
          <div v-else-if="activeTab === 'settings'" class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
              <!-- Course Settings -->
              <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Course Settings</h3>

                <div class="space-y-6">
                  <!-- Visibility Settings -->
                  <div class="border border-gray-200 rounded-lg p-4">
                    <h4 class="font-medium text-gray-900 mb-3">Visibility Settings</h4>
                    <div class="space-y-3">
                      <div class="flex items-center">
                        <input
                          id="visibility-public"
                          v-model="visibilitySetting"
                          type="radio"
                          value="public"
                          class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300"
                        />
                        <label for="visibility-public" class="ml-3 block text-sm font-medium text-gray-700">
                          Public
                          <p class="text-gray-500 text-xs mt-1">Visible to all students in course catalog</p>
                        </label>
                      </div>
                      <div class="flex items-center">
                        <input
                          id="visibility-private"
                          v-model="visibilitySetting"
                          type="radio"
                          value="private"
                          class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300"
                        />
                        <label for="visibility-private" class="ml-3 block text-sm font-medium text-gray-700">
                          Private
                          <p class="text-gray-500 text-xs mt-1">Only visible to you (for testing)</p>
                        </label>
                      </div>
                      <div class="flex items-center">
                        <input
                          id="visibility-unlisted"
                          v-model="visibilitySetting"
                          type="radio"
                          value="unlisted"
                          class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300"
                        />
                        <label for="visibility-unlisted" class="ml-3 block text-sm font-medium text-gray-700">
                          Unlisted
                          <p class="text-gray-500 text-xs mt-1">Only accessible via direct link</p>
                        </label>
                      </div>
                    </div>
                    <button
                      @click="updateVisibility"
                      :disabled="visibilitySetting === course.visibility"
                      class="mt-4 w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 disabled:opacity-50"
                    >
                      Update Visibility
                    </button>
                  </div>

                  <!-- Enrollment Settings -->
                  <div class="border border-gray-200 rounded-lg p-4">
                    <h4 class="font-medium text-gray-900 mb-3">Enrollment Settings</h4>
                    <div class="space-y-4">
                      <div>
                        <label for="enrollment_limit" class="block text-sm font-medium text-gray-700 mb-1">
                          Enrollment Limit
                        </label>
                        <input
                          id="enrollment_limit"
                          v-model="enrollmentLimitSetting"
                          type="number"
                          min="0"
                          class="block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm"
                          placeholder="Leave empty for unlimited"
                        />
                        <p class="mt-1 text-xs text-gray-500">
                          Maximum number of students who can enroll. Current: {{ enrollment_stats.total }} enrolled
                        </p>
                      </div>
                      <button
                        @click="updateEnrollmentLimit"
                        :disabled="enrollmentLimitSetting === (course.enrollment_limit || '')"
                        class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 disabled:opacity-50"
                      >
                        Update Enrollment Limit
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Danger Zone -->
              <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Danger Zone</h3>

                <div class="space-y-6">
                  <!-- Course Status -->
                  <div class="border border-red-200 rounded-lg p-4 bg-red-50">
                    <h4 class="font-medium text-red-800 mb-3">Course Status</h4>
                    <p class="text-sm text-red-700 mb-4">
                      Changing course status will affect all enrolled students. Archived courses cannot be modified.
                    </p>
                    <div class="space-y-3">
                      <div class="flex items-center">
                        <input
                          id="status-active"
                          v-model="statusSetting"
                          type="radio"
                          value="active"
                          class="h-4 w-4 text-red-600 focus:ring-red-500 border-red-300"
                        />
                        <label for="status-active" class="ml-3 block text-sm font-medium text-red-800">
                          Active
                          <p class="text-red-600 text-xs mt-1">Course is open for enrollment and active</p>
                        </label>
                      </div>
                      <div class="flex items-center">
                        <input
                          id="status-paused"
                          v-model="statusSetting"
                          type="radio"
                          value="paused"
                          class="h-4 w-4 text-red-600 focus:ring-red-500 border-red-300"
                        />
                        <label for="status-paused" class="ml-3 block text-sm font-medium text-red-800">
                          Paused
                          <p class="text-red-600 text-xs mt-1">No new enrollments, existing students can continue</p>
                        </label>
                      </div>
                      <div class="flex items-center">
                        <input
                          id="status-archived"
                          v-model="statusSetting"
                          type="radio"
                          value="archived"
                          class="h-4 w-4 text-red-600 focus:ring-red-500 border-red-300"
                        />
                        <label for="status-archived" class="ml-3 block text-sm font-medium text-red-800">
                          Archived
                          <p class="text-red-600 text-xs mt-1">Course is read-only, no changes allowed</p>
                        </label>
                      </div>
                    </div>
                    <button
                      @click="updateStatus"
                      :disabled="statusSetting === course.status"
                      class="mt-4 w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50"
                    >
                      Update Course Status
                    </button>
                  </div>

                  <!-- Delete Course -->
                  <div class="border border-red-200 rounded-lg p-4 bg-red-50">
                    <h4 class="font-medium text-red-800 mb-3">Delete Course</h4>
                    <p class="text-sm text-red-700 mb-4">
                      This action cannot be undone. All course data, modules, topics, and enrollments will be permanently deleted.
                    </p>
                    <button
                      @click="confirmDelete = true"
                      class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                    >
                      Delete Course Permanently
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <Transition
      enter-active-class="duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="confirmDelete" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
          <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="confirmDelete = false"></div>

          <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

          <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div class="sm:flex sm:items-start">
              <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.798-.833-2.732 0L4.346 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
              </div>
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                  Delete Course
                </h3>
                <div class="mt-2">
                  <p class="text-sm text-gray-500">
                    Are you sure you want to delete "{{ course.title }}"? This action cannot be undone. All course data, modules, topics, and enrollments will be permanently deleted.
                  </p>
                  <p v-if="enrollment_stats.total > 0" class="mt-2 text-sm text-red-600 font-medium">
                    Warning: {{ enrollment_stats.total }} students are currently enrolled in this course!
                  </p>
                </div>
              </div>
            </div>
            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
              <button
                type="button"
                @click="confirmDeleteCourse"
                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
              >
                Delete Course
              </button>
              <button
                type="button"
                @click="confirmDelete = false"
                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 sm:mt-0 sm:w-auto sm:text-sm"
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
import { ref, computed, onMounted } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

const page = usePage()

const props = defineProps({
  course: Object,
  enrollment_stats: Object,
  recent_enrollments: Array,
})

const activeTab = ref('overview')
const confirmDelete = ref(false)
const visibilitySetting = ref(props.course.visibility)
const statusSetting = ref(props.course.status)
const enrollmentLimitSetting = ref(props.course.enrollment_limit || '')

// Computed properties
const totalTopics = computed(() => {
  return props.course.modules.reduce((total, module) => total + module.topics.length, 0)
})

const coursePublicUrl = computed(() => {
  return window.location.origin + route('courses.show', props.course.id)
})

// Methods
const getVisibilityBadgeClass = (visibility) => {
  const classes = {
    public: 'bg-green-100 text-green-800',
    private: 'bg-gray-100 text-gray-800',
    unlisted: 'bg-yellow-100 text-yellow-800',
  }
  return classes[visibility] || 'bg-gray-100 text-gray-800'
}

const getStatusBadgeClass = (status) => {
  const classes = {
    active: 'bg-green-100 text-green-800',
    draft: 'bg-yellow-100 text-yellow-800',
    paused: 'bg-red-100 text-red-800',
    archived: 'bg-gray-100 text-gray-800',
    completed: 'bg-blue-100 text-blue-800',
  }
  return classes[status?.toLowerCase()] || 'bg-gray-100 text-gray-800'
}

const getEnrollmentStatusBadgeClass = (status) => {
  const classes = {
    active: 'bg-green-100 text-green-800',
    completed: 'bg-blue-100 text-blue-800',
    dropped: 'bg-red-100 text-red-800',
    pending: 'bg-yellow-100 text-yellow-800',
  }
  return classes[status?.toLowerCase()] || 'bg-gray-100 text-gray-800'
}

const getEnrollmentStatusClass = () => {
  if (!props.course.enrollment_limit) {
    return 'bg-green-100 text-green-800'
  }

  const percentage = (props.enrollment_stats.total / props.course.enrollment_limit) * 100
  if (percentage >= 90) {
    return 'bg-red-100 text-red-800'
  } else if (percentage >= 70) {
    return 'bg-yellow-100 text-yellow-800'
  } else {
    return 'bg-green-100 text-green-800'
  }
}

const getTopicTypeClass = (type) => {
  const classes = {
    text: 'bg-blue-100 text-blue-800',
    video: 'bg-purple-100 text-purple-800',
    quiz: 'bg-yellow-100 text-yellow-800',
    project: 'bg-green-100 text-green-800',
  }
  return classes[type] || 'bg-gray-100 text-gray-800'
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

// Actions
const copyCourseLink = async () => {
  try {
    await navigator.clipboard.writeText(coursePublicUrl.value)
    alert('Course link copied to clipboard!')
  } catch (err) {
    console.error('Failed to copy:', err)
  }
}

const toggleCourseStatus = async () => {
  if (!confirm(`Are you sure you want to ${props.course.status === 'active' ? 'pause' : 'activate'} this course?`)) {
    return
  }

  try {
    await router.put(route('admin.catalog.courses.update', props.course.id), {
      status: props.course.status === 'active' ? 'paused' : 'active'
    }, {
      preserveScroll: true,
      onSuccess: () => {
        page.props.flash.success = `Course ${props.course.status === 'active' ? 'paused' : 'activated'} successfully`
      }
    })
  } catch (error) {
    console.error('Failed to toggle course status:', error)
  }
}

const toggleVisibility = async () => {
  const newVisibility = props.course.visibility === 'public' ? 'private' : 'public'

  if (!confirm(`Are you sure you want to make this course ${newVisibility}?`)) {
    return
  }

  try {
    await router.put(route('admin.catalog.courses.update', props.course.id), {
      visibility: newVisibility,
      is_public: newVisibility === 'public'
    }, {
      preserveScroll: true,
      onSuccess: () => {
        page.props.flash.success = `Course visibility updated to ${newVisibility}`
      }
    })
  } catch (error) {
    console.error('Failed to toggle visibility:', error)
  }
}

const unenrollStudent = async (userId) => {
  if (!confirm('Are you sure you want to unenroll this student? They will lose access to the course.')) {
    return
  }

  try {
    await router.post(route('admin.catalog.courses.toggle-enrollment', props.course.id), {
      user_id: userId,
      action: 'unenroll'
    }, {
      preserveScroll: true,
      onSuccess: () => {
        page.props.flash.success = 'Student unenrolled successfully'
      }
    })
  } catch (error) {
    console.error('Failed to unenroll student:', error)
  }
}

const updateVisibility = async () => {
  if (visibilitySetting.value === props.course.visibility) return

  try {
    await router.put(route('admin.catalog.courses.update', props.course.id), {
      visibility: visibilitySetting.value,
      is_public: visibilitySetting.value === 'public'
    }, {
      preserveScroll: true,
      onSuccess: () => {
        page.props.flash.success = 'Visibility updated successfully'
      }
    })
  } catch (error) {
    console.error('Failed to update visibility:', error)
  }
}

const updateStatus = async () => {
  if (statusSetting.value === props.course.status) return

  try {
    await router.put(route('admin.catalog.courses.update', props.course.id), {
      status: statusSetting.value
    }, {
      preserveScroll: true,
      onSuccess: () => {
        page.props.flash.success = 'Course status updated successfully'
      }
    })
  } catch (error) {
    console.error('Failed to update status:', error)
  }
}

const updateEnrollmentLimit = async () => {
  const newLimit = enrollmentLimitSetting.value === '' ? null : parseInt(enrollmentLimitSetting.value)

  if (newLimit !== null && newLimit < props.enrollment_stats.total) {
    alert(`Cannot set enrollment limit below current enrollment (${props.enrollment_stats.total})`)
    return
  }

  try {
    await router.put(route('admin.catalog.courses.update', props.course.id), {
      enrollment_limit: newLimit
    }, {
      preserveScroll: true,
      onSuccess: () => {
        page.props.flash.success = 'Enrollment limit updated successfully'
      }
    })
  } catch (error) {
    console.error('Failed to update enrollment limit:', error)
  }
}

const deleteCourse = () => {
  confirmDelete.value = true
}

const confirmDeleteCourse = async () => {
  try {
    await router.delete(route('admin.catalog.courses.destroy', props.course.id), {
      onSuccess: () => {
        page.props.flash.success = 'Course deleted successfully'
      },
      onError: (errors) => {
        if (errors.message) {
          alert(errors.message)
        }
      }
    })
  } catch (error) {
    console.error('Failed to delete course:', error)
  }
}

// Initialize settings
onMounted(() => {
  visibilitySetting.value = props.course.visibility
  statusSetting.value = props.course.status
  enrollmentLimitSetting.value = props.course.enrollment_limit || ''
})
</script>

<style scoped>
/* Add smooth transitions for modal */
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
</style>
