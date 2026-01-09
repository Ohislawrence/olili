<template>
  <StudentLayout>
    <Head title="My Courses" />

    <!-- Main Content -->
    <section class="py-8 md:py-12 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header & Stats -->
        <div class="mb-8">
          <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
            <div>
              <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 mb-2">
                My
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-teal-600">
                  Learning Dashboard
                </span>
              </h1>
              <p class="text-gray-600 max-w-2xl">
                Track your progress and continue learning with AI-powered courses.
              </p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
              <div class="bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-100 rounded-xl p-4">
                <div class="text-2xl font-bold text-emerald-600">{{ activeCoursesCount }}</div>
                <div class="text-sm text-gray-600 mt-1">Active</div>
              </div>
              <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100 rounded-xl p-4">
                <div class="text-2xl font-bold text-blue-600">{{ enrolledCoursesCount }}</div>
                <div class="text-sm text-gray-600 mt-1">Enrolled</div>
              </div>
              <div class="bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-100 rounded-xl p-4">
                <div class="text-2xl font-bold text-amber-600">{{ pausedCoursesCount }}</div>
                <div class="text-sm text-gray-600 mt-1">Paused</div>
              </div>
              <div class="bg-gradient-to-br from-teal-50 to-cyan-50 border border-teal-100 rounded-xl p-4">
                <div class="text-2xl font-bold text-teal-600">{{ completedCoursesCount }}</div>
                <div class="text-sm text-gray-600 mt-1">Completed</div>
              </div>
            </div>
          </div>

          <!-- Search Bar -->
          <div class="flex-1 max-w-2xl mb-6">
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </div>
              <input
                type="text"
                v-model="filters.search"
                placeholder="Search your courses..."
                class="w-full pl-10 pr-12 py-3 rounded-xl border border-gray-300 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"
                @input="debouncedSearch"
              />
              <button
                v-if="filters.search"
                @click="clearSearch"
                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                type="button"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Filter Card -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm mb-10">
          <!-- Header -->
          <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 px-6 py-5 border-b">
            <div>
              <h2 class="text-lg font-semibold text-gray-900">Filter your courses</h2>
            </div>

            <div class="flex items-center gap-3">
              <span v-if="hasActiveFilters" class="text-sm text-gray-500">
                {{ getTotalFilters() }} active
              </span>

              <button
                v-if="hasActiveFilters"
                @click="clearFilters"
                class="text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 px-3 py-1.5 rounded-lg transition-colors"
              >
                Clear all
              </button>
            </div>
          </div>

          <!-- Filters -->
          <div class="grid grid-cols-1 md:grid-cols-4 gap-6 p-6">
            <!-- Status Filter -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-3">
                Status
              </label>
              <div class="relative">
                <button
                  @click.stop="toggleStatusDropdown"
                  class="w-full flex items-center justify-between px-4 py-3 text-left bg-white border border-gray-300 rounded-xl hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"
                >
                  <span class="text-sm text-gray-700">
                    {{ filters.status ? formatStatus(filters.status) : 'All statuses' }}
                  </span>
                  <svg
                    class="w-4 h-4 text-gray-500 transition-transform"
                    :class="{ 'rotate-180': showStatusDropdown }"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </button>

                <!-- Status Dropdown Menu -->
                <div
                  v-if="showStatusDropdown"
                  class="absolute z-50 mt-2 w-full bg-white border border-gray-200 rounded-xl shadow-lg"
                >
                  <div class="py-1">
                    <button
                      @click="filters.status = ''; applyFilters()"
                      class="w-full px-4 py-2.5 text-sm text-left hover:bg-gray-50 transition-colors flex items-center justify-between"
                      :class="{ 'text-emerald-700 bg-emerald-50': !filters.status }"
                    >
                      <span>All statuses</span>
                      <svg v-if="!filters.status" class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                    </button>
                    <button
                      v-for="status in statusOptions"
                      :key="status.value"
                      @click="filters.status = status.value; applyFilters()"
                      class="w-full px-4 py-2.5 text-sm text-left hover:bg-gray-50 transition-colors flex items-center justify-between"
                      :class="{ 'text-emerald-700 bg-emerald-50': filters.status === status.value }"
                    >
                      <span>{{ status.label }}</span>
                      <svg v-if="filters.status === status.value" class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Progress Filter -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-3">
                Progress
              </label>
              <div class="relative">
                <button
                  @click.stop="toggleProgressDropdown"
                  class="w-full flex items-center justify-between px-4 py-3 text-left bg-white border border-gray-300 rounded-xl hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"
                >
                  <span class="text-sm text-gray-700">
                    {{ filters.progress ? progressOptions.find(p => p.value === filters.progress)?.label : 'All progress' }}
                  </span>
                  <svg
                    class="w-4 h-4 text-gray-500 transition-transform"
                    :class="{ 'rotate-180': showProgressDropdown }"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </button>

                <!-- Progress Dropdown Menu -->
                <div
                  v-if="showProgressDropdown"
                  class="absolute z-50 mt-2 w-full bg-white border border-gray-200 rounded-xl shadow-lg"
                >
                  <div class="py-1">
                    <button
                      @click="filters.progress = ''; applyFilters()"
                      class="w-full px-4 py-2.5 text-sm text-left hover:bg-gray-50 transition-colors flex items-center justify-between"
                      :class="{ 'text-emerald-700 bg-emerald-50': !filters.progress }"
                    >
                      <span>All progress</span>
                      <svg v-if="!filters.progress" class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                    </button>
                    <button
                      v-for="progress in progressOptions"
                      :key="progress.value"
                      @click="filters.progress = progress.value; applyFilters()"
                      class="w-full px-4 py-2.5 text-sm text-left hover:bg-gray-50 transition-colors flex items-center justify-between"
                      :class="{ 'text-emerald-700 bg-emerald-50': filters.progress === progress.value }"
                    >
                      <span>{{ progress.label }}</span>
                      <svg v-if="filters.progress === progress.value" class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Subject Filter -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-3">
                Subject
              </label>
              <div class="relative">
                <button
                  @click.stop="toggleSubjectDropdown"
                  class="w-full flex items-center justify-between px-4 py-3 text-left bg-white border border-gray-300 rounded-xl hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"
                >
                  <span class="text-sm text-gray-700">
                    {{ filters.subject || 'All subjects' }}
                  </span>
                  <svg
                    class="w-4 h-4 text-gray-500 transition-transform"
                    :class="{ 'rotate-180': showSubjectDropdown }"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </button>

                <!-- Subject Dropdown Menu -->
                <div
                  v-if="showSubjectDropdown"
                  class="absolute z-50 mt-2 w-full bg-white border border-gray-200 rounded-xl shadow-lg max-h-64 overflow-y-auto"
                >
                  <div class="p-2">
                    <!-- Search within subjects -->
                    <div class="relative mb-2">
                      <input
                        v-model="subjectSearch"
                        type="text"
                        placeholder="Search subjects..."
                        class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500"
                        @click.stop
                      />
                      <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                      </svg>
                    </div>

                    <!-- Subject Options -->
                    <div class="space-y-1">
                      <button
                        @click="filters.subject = ''; applyFilters()"
                        class="w-full px-3 py-2 text-sm text-left rounded-lg hover:bg-gray-50 transition-colors flex items-center justify-between"
                        :class="{ 'text-emerald-700 bg-emerald-50': !filters.subject }"
                      >
                        <span>All subjects</span>
                        <svg v-if="!filters.subject" class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                      </button>

                      <button
                        v-for="subject in filteredSubjects"
                        :key="subject"
                        @click="filters.subject = subject; applyFilters()"
                        class="w-full px-3 py-2 text-sm text-left rounded-lg hover:bg-gray-50 transition-colors flex items-center justify-between"
                        :class="{ 'text-emerald-700 bg-emerald-50': filters.subject === subject }"
                      >
                        <span>{{ subject }}</span>
                        <svg v-if="filters.subject === subject" class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Sort Dropdown -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-3">
                Sort by
              </label>
              <div class="relative">
                <button
                  @click.stop="toggleSortDropdown"
                  class="w-full flex items-center justify-between px-4 py-3 text-left bg-white border border-gray-300 rounded-xl hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"
                >
                  <span class="text-sm text-gray-700">
                    {{ getSortLabel() }}
                  </span>
                  <svg
                    class="w-4 h-4 text-gray-500 transition-transform"
                    :class="{ 'rotate-180': showSortDropdown }"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </button>

                <!-- Sort Dropdown Menu -->
                <div
                  v-if="showSortDropdown"
                  class="absolute z-50 mt-2 w-full bg-white border border-gray-200 rounded-xl shadow-lg"
                >
                  <div class="py-1">
                    <button
                      v-for="option in sortOptions"
                      :key="option.value"
                      @click="filters.sort = option.value; applySorting()"
                      class="w-full px-4 py-2.5 text-sm text-left hover:bg-emerald-50 hover:text-emerald-700 transition-colors flex items-center justify-between"
                      :class="{ 'text-emerald-700 bg-emerald-50': filters.sort === option.value }"
                    >
                      <span>{{ option.label }}</span>
                      <svg v-if="filters.sort === option.value" class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Active Filters -->
          <div v-if="hasActiveFilters" class="px-6 pb-6">
            <div class="flex flex-wrap gap-2">
              <span
                v-if="filters.search"
                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm bg-emerald-50 text-emerald-700 border border-emerald-200"
              >
                Search: "{{ filters.search }}"
                <button @click.stop="clearSearch" class="text-emerald-600 hover:text-emerald-800 hover:bg-emerald-100 rounded-full p-0.5">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </span>

              <span
                v-if="filters.status"
                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm bg-blue-50 text-blue-700 border border-blue-200"
              >
                Status: {{ formatStatus(filters.status) }}
                <button @click.stop="filters.status = ''; applyFilters()" class="text-blue-600 hover:text-blue-800 hover:bg-blue-100 rounded-full p-0.5">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </span>

              <span
                v-if="filters.subject"
                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm bg-purple-50 text-purple-700 border border-purple-200"
              >
                Subject: {{ filters.subject }}
                <button @click.stop="filters.subject = ''; applyFilters()" class="text-purple-600 hover:text-purple-800 hover:bg-purple-100 rounded-full p-0.5">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </span>

              <span
                v-if="filters.progress"
                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm bg-amber-50 text-amber-700 border border-amber-200"
              >
                Progress: {{ progressOptions.find(p => p.value === filters.progress)?.label }}
                <button @click.stop="filters.progress = ''; applyFilters()" class="text-amber-600 hover:text-amber-800 hover:bg-amber-100 rounded-full p-0.5">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </span>
            </div>
          </div>
        </div>

        <!-- Results Header -->
        <div class="flex items-center justify-between mb-6">
          <div>
            <h2 class="text-lg font-semibold text-gray-900">
              <span class="text-emerald-600">{{ courses.data.length }}</span>
              {{ courses.data.length === 1 ? 'Course' : 'Courses' }}
            </h2>
            <p v-if="hasActiveFilters" class="text-sm text-gray-600 mt-1">
              Filtered by: <span class="font-medium">{{ activeFiltersText }}</span>
            </p>
          </div>
          <div class="text-sm text-gray-600">
            Total learning: {{ totalLearningHours }}+ hours
          </div>
        </div>

        <!-- Courses Grid -->
        <div v-if="courses.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div
            v-for="course in courses.data"
            :key="course.id"
            class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 group"
          >
            <!-- Course Header with Progress -->
            <div class="h-48 relative overflow-hidden bg-gradient-to-br from-emerald-500 to-teal-600">
              <!-- Progress Overlay -->
              <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>

              <!-- Status Badge -->
              <div class="absolute top-4 left-4">
                <span :class="[
                  'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold',
                  getStatusClass(course.status)
                ]">
                  {{ formatStatus(course.status) }}
                </span>
              </div>

              <!-- Progress Circle -->
              <div class="absolute top-4 right-4">
                <div class="relative w-12 h-12">
                  <svg class="w-12 h-12 transform -rotate-90">
                    <!-- Background Circle -->
                    <circle
                      cx="24"
                      cy="24"
                      r="18"
                      stroke="rgba(255,255,255,0.3)"
                      stroke-width="4"
                      fill="transparent"
                    />
                    <!-- Progress Circle -->
                    <circle
                      cx="24"
                      cy="24"
                      r="18"
                      :stroke="getProgressColor(course.progress_percentage)"
                      stroke-width="4"
                      fill="transparent"
                      :stroke-dasharray="113.097"
                      :stroke-dashoffset="113.097 - (113.097 * course.progress_percentage) / 100"
                      stroke-linecap="round"
                    />
                  </svg>
                  <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-xs font-bold text-white">{{ Math.round(course.progress_percentage) }}%</span>
                  </div>
                </div>
              </div>

              <!-- Course Title -->
              <div class="absolute inset-0 flex items-end p-6">
                <div>
                  <h3 class="text-white font-bold text-xl line-clamp-2">{{ course.title }}</h3>
                  <p class="text-white/80 text-sm mt-1">{{ course.subject }} • {{ course.level }}</p>
                </div>
              </div>
            </div>

            <!-- Course Body -->
            <div class="p-6">
              <!-- Quick Stats -->
              <div class="grid grid-cols-3 gap-4 mb-5">
                <div class="text-center">
                  <div class="text-lg font-bold text-gray-900">{{ course.completed_modules_count || 0 }}</div>
                  <div class="text-xs text-gray-500">Modules</div>
                </div>
                <div class="text-center">
                  <div class="text-lg font-bold text-gray-900">{{ course.completed_topics_count || 0 }}</div>
                  <div class="text-xs text-gray-500">Topics</div>
                </div>
                <div class="text-center">
                  <div class="text-lg font-bold text-gray-900">{{ course.estimated_duration_hours || 0 }}</div>
                  <div class="text-xs text-gray-500">Hours</div>
                </div>
              </div>

              <!-- Description -->
              <p class="text-gray-700 text-sm mb-5 line-clamp-3">{{ course.description || 'No description available' }}</p>

              <!-- Dates -->
              <div class="space-y-2 mb-5">
                <div class="flex items-center text-xs text-gray-500">
                  <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  <span>Enrolled: {{ formatDate(course.enrolled_at) }}</span>
                </div>
                <div v-if="course.last_activity" class="flex items-center text-xs text-gray-500">
                  <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <span>Last active: {{ formatDate(course.last_activity) }}</span>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="space-y-2">
                <!-- Primary Action -->
                <Link
                  v-if="course.status === 'enrolled'"
                  :href="route('student.courses.start', { course: course.id })"
                  class="w-full block text-center bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white py-3 rounded-xl font-medium transition-all duration-300 group-hover:shadow-lg"
                >
                  Start Learning
                </Link>

                <Link
                  v-else-if="course.status === 'active'"
                  :href="route('student.courses.learn', { course: course.id, topic: course.lastViewedTopic })"
                  class="w-full block text-center bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white py-3 rounded-xl font-medium transition-all duration-300 group-hover:shadow-lg"
                >
                  Continue Learning
                </Link>

                <button
                  v-else-if="course.status === 'paused'"
                  @click="resumeCourse(course)"
                  class="w-full block text-center bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-700 hover:to-orange-700 text-white py-3 rounded-xl font-medium transition-all duration-300 group-hover:shadow-lg"
                >
                  Resume Course
                </button>

                <Link
                  v-else-if="course.status === 'completed'"
                  :href="route('student.courses.show', course.id)"
                  class="w-full block text-center bg-gradient-to-r from-teal-600 to-cyan-600 hover:from-teal-700 hover:to-cyan-700 text-white py-3 rounded-xl font-medium transition-all duration-300 group-hover:shadow-lg"
                >
                  View Certificate
                </Link>

                <!-- Secondary Actions -->
                <div class="flex gap-2">
                  <Link
                    :href="route('student.courses.show', course.id)"
                    class="flex-1 text-center px-3 py-2 text-sm text-gray-700 hover:text-emerald-700 hover:bg-emerald-50 border border-gray-300 hover:border-emerald-300 rounded-lg transition-colors"
                  >
                    View Details
                  </Link>

                  <button
                    v-if="course.status === 'active'"
                    @click="pauseCourse(course)"
                    class="flex-1 text-center px-3 py-2 text-sm text-gray-700 hover:text-amber-700 hover:bg-amber-50 border border-gray-300 hover:border-amber-300 rounded-lg transition-colors"
                  >
                    Pause
                  </button>

                  <button
                    v-if="course.status === 'paused'"
                    @click="resumeCourse(course)"
                    class="flex-1 text-center px-3 py-2 text-sm text-gray-700 hover:text-emerald-700 hover:bg-emerald-50 border border-gray-300 hover:border-emerald-300 rounded-lg transition-colors"
                  >
                    Resume
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-12 bg-gray-50 rounded-xl border border-gray-200">
          <div class="w-16 h-16 mx-auto mb-4 text-gray-400">
            <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
            </svg>
          </div>
          <h3 class="text-xl font-bold text-gray-900 mb-2">
            {{ hasActiveFilters ? 'No courses match your filters' : 'No courses enrolled yet' }}
          </h3>
          <p class="text-gray-600 max-w-md mx-auto mb-6">
            {{ hasActiveFilters
              ? 'Try adjusting your filters or search terms to find your courses.'
              : 'Start your learning journey by browsing our AI-powered courses.'
            }}
          </p>
          <div class="space-x-3">
            <Link
              :href="route('student.catalog.browse')"
              class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white rounded-lg font-medium transition-all hover:shadow-md"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
              Browse Courses
            </Link>
            <button
              v-if="hasActiveFilters"
              @click="clearFilters"
              class="inline-flex items-center px-5 py-2.5 border border-gray-300 rounded-lg font-medium text-gray-700 hover:bg-gray-50 transition-colors"
            >
              Clear All Filters
            </button>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="courses.data.length > 0 && courses.links.length > 3" class="mt-8">
          <nav class="flex items-center justify-center space-x-2">
            <button
              v-for="(link, index) in courses.links"
              :key="index"
              @click="loadPage(link.url)"
              v-html="link.label"
              :disabled="!link.url || link.active"
              :class="[
                'px-3 py-1.5 rounded-lg text-sm font-medium transition-all',
                link.active
                  ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow'
                  : link.url
                  ? 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 hover:border-gray-400'
                  : 'bg-gray-100 border border-gray-200 text-gray-400 cursor-not-allowed'
              ]"
            ></button>
          </nav>
        </div>
      </div>
    </section>

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
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'
import { debounce } from 'lodash-es'
import StudentLayout from '@/Layouts/StudentLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import ConfirmationModal from '@/Components/ConfirmationModal.vue'

const props = defineProps({
  courses: Object,
  filters: Object,
  subjects: Array,
})

// Reactive state
const filters = ref({
  search: props.filters.search || '',
  status: props.filters.status || '',
  subject: props.filters.subject || '',
  progress: props.filters.progress || '',
  sort: props.filters.sort || 'recent',
})

// Dropdown states
const showStatusDropdown = ref(false)
const showProgressDropdown = ref(false)
const showSubjectDropdown = ref(false)
const showSortDropdown = ref(false)
const subjectSearch = ref('')

// Modal states
const showPauseModal = ref(false)
const showDropModal = ref(false)
const selectedCourse = ref(null)

// Options
const statusOptions = [
  { value: 'enrolled', label: 'Enrolled' },
  { value: 'active', label: 'Active' },
  { value: 'paused', label: 'Paused' },
  { value: 'completed', label: 'Completed' },
  { value: 'dropped', label: 'Dropped' },
]

const progressOptions = [
  { value: 'not_started', label: 'Not Started' },
  { value: 'beginner', label: 'Beginner (0-25%)' },
  { value: 'intermediate', label: 'Intermediate (26-75%)' },
  { value: 'advanced', label: 'Advanced (76-99%)' },
  { value: 'completed', label: 'Completed (100%)' },
]

const sortOptions = [
  { value: 'recent', label: 'Recently Active' },
  { value: 'progress', label: 'Highest Progress' },
  { value: 'enrollment', label: 'Recently Enrolled' },
  { value: 'alphabetical', label: 'Alphabetical' },
]

// Close dropdowns when clicking outside
const closeAllDropdowns = () => {
  showStatusDropdown.value = false
  showProgressDropdown.value = false
  showSubjectDropdown.value = false
  showSortDropdown.value = false
}

const handleClickOutside = (event) => {
  if (!event.target.closest('.relative')) {
    closeAllDropdowns()
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})

// Toggle methods
const toggleStatusDropdown = () => {
  showStatusDropdown.value = !showStatusDropdown.value
  showProgressDropdown.value = false
  showSubjectDropdown.value = false
  showSortDropdown.value = false
}

const toggleProgressDropdown = () => {
  showProgressDropdown.value = !showProgressDropdown.value
  showStatusDropdown.value = false
  showSubjectDropdown.value = false
  showSortDropdown.value = false
}

const toggleSubjectDropdown = () => {
  showSubjectDropdown.value = !showSubjectDropdown.value
  showStatusDropdown.value = false
  showProgressDropdown.value = false
  showSortDropdown.value = false
}

const toggleSortDropdown = () => {
  showSortDropdown.value = !showSortDropdown.value
  showStatusDropdown.value = false
  showProgressDropdown.value = false
  showSubjectDropdown.value = false
}

// Computed properties
const activeCoursesCount = computed(() => {
  return props.courses.data.filter(course => course.status === 'active').length
})

const enrolledCoursesCount = computed(() => {
  return props.courses.data.filter(course => course.status === 'enrolled').length
})

const pausedCoursesCount = computed(() => {
  return props.courses.data.filter(course => course.status === 'paused').length
})

const completedCoursesCount = computed(() => {
  return props.courses.data.filter(course => course.status === 'completed').length
})

const hasActiveFilters = computed(() => {
  return filters.value.search ||
         filters.value.status ||
         filters.value.subject ||
         filters.value.progress
})

const getTotalFilters = () => {
  let count = 0
  if (filters.value.search) count++
  if (filters.value.status) count++
  if (filters.value.subject) count++
  if (filters.value.progress) count++
  return count
}

const activeFiltersText = computed(() => {
  const parts = []
  if (filters.value.search) parts.push(`"${filters.value.search}"`)
  if (filters.value.status) parts.push(formatStatus(filters.value.status))
  if (filters.value.subject) parts.push(filters.value.subject)
  if (filters.value.progress) {
    const progressLabel = progressOptions.find(p => p.value === filters.value.progress)?.label
    if (progressLabel) parts.push(progressLabel)
  }
  return parts.join(' • ')
})

const totalLearningHours = computed(() => {
  return props.courses.data.reduce((sum, course) => sum + (course.estimated_duration_hours || 0), 0)
})

const filteredSubjects = computed(() => {
  if (!subjectSearch.value.trim()) return props.subjects
  const searchTerm = subjectSearch.value.toLowerCase()
  return props.subjects.filter(subject =>
    subject.toLowerCase().includes(searchTerm)
  )
})

// Methods
const debouncedSearch = debounce(() => {
  applyFilters()
}, 500)

const applyFilters = () => {
  router.get(route('student.courses.index'), filters.value, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  })
  closeAllDropdowns()
}

const applySorting = () => {
  router.get(route('student.courses.index'), filters.value, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  })
  closeAllDropdowns()
}

const clearSearch = () => {
  filters.value.search = ''
  applyFilters()
}

const clearFilters = () => {
  filters.value = {
    search: '',
    status: '',
    subject: '',
    progress: '',
    sort: 'recent',
  }
  applyFilters()
}

const loadPage = (url) => {
  if (url) {
    router.visit(url, {
      preserveState: true,
      preserveScroll: true,
    })
  }
}

const getSortLabel = () => {
  const option = sortOptions.find(opt => opt.value === filters.value.sort)
  return option ? option.label : 'Sort By'
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

const getProgressColor = (percentage) => {
  if (percentage >= 80) return '#10b981'
  if (percentage >= 50) return '#0d9488'
  if (percentage >= 25) return '#f59e0b'
  return '#ef4444'
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
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.rotate-180 {
  transform: rotate(180deg);
}
</style>
