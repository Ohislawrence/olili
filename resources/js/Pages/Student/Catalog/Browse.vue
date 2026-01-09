<template>
  <StudentLayout>
    <Head title="Course Catalog" />

    <!-- Main Content -->
    <section class="py-8 md:py-12 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header & Stats -->
        <div class="mb-8">
          <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
            <div>
              <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 mb-2">
                Discover
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-teal-600">
                  AI-Powered Courses
                </span>
              </h1>
              <p class="text-gray-600 max-w-2xl">
                Browse and enroll in public courses created by expert instructors. Transform your skills with intelligent learning.
              </p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
              <div class="bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-100 rounded-xl p-4">
                <div class="text-2xl font-bold text-emerald-600">{{ total_courses }}</div>
                <div class="text-sm text-gray-600 mt-1">Total Courses</div>
              </div>
              <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100 rounded-xl p-4">
                <div class="text-2xl font-bold text-blue-600">{{ enrolledCoursesCount }}</div>
                <div class="text-sm text-gray-600 mt-1">Enrolled</div>
              </div>
              <div class="bg-gradient-to-br from-purple-50 to-pink-50 border border-purple-100 rounded-xl p-4">
                <div class="text-2xl font-bold text-purple-600">{{ availableCoursesCount }}</div>
                <div class="text-sm text-gray-600 mt-1">Available</div>
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
                placeholder="Search courses by title, description, or subject..."
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
              <h2 class="text-lg font-semibold text-gray-900">Filter courses</h2>
              <p class="text-sm text-gray-500">Tap to refine results</p>
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

            <!-- Level Filter -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-3">
                Level
              </label>
              <div class="relative">
                <button
                  @click.stop="toggleLevelDropdown"
                  class="w-full flex items-center justify-between px-4 py-3 text-left bg-white border border-gray-300 rounded-xl hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"
                >
                  <span class="text-sm text-gray-700">
                    {{ filters.level ? formatLevel(filters.level) : 'All levels' }}
                  </span>
                  <svg
                    class="w-4 h-4 text-gray-500 transition-transform"
                    :class="{ 'rotate-180': showLevelDropdown }"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </button>

                <!-- Level Dropdown Menu -->
                <div
                  v-if="showLevelDropdown"
                  class="absolute z-50 mt-2 w-full bg-white border border-gray-200 rounded-xl shadow-lg"
                >
                  <div class="py-1">
                    <button
                      @click="filters.level = ''; applyFilters()"
                      class="w-full px-4 py-2.5 text-sm text-left hover:bg-gray-50 transition-colors flex items-center justify-between"
                      :class="{ 'text-emerald-700 bg-emerald-50': !filters.level }"
                    >
                      <span>All levels</span>
                      <svg v-if="!filters.level" class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                    </button>

                    <button
                      v-for="level in levelOptions"
                      :key="level.value"
                      @click="filters.level = level.value; applyFilters()"
                      class="w-full px-4 py-2.5 text-sm text-left hover:bg-gray-50 transition-colors flex items-center justify-between"
                      :class="{ 'text-emerald-700 bg-emerald-50': filters.level === level.value }"
                    >
                      <span>{{ level.label }}</span>
                      <svg v-if="filters.level === level.value" class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Exam Board Filter -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-3">
                Exam Board
              </label>
              <div class="relative">
                <button
                  @click.stop="toggleExamBoardDropdown"
                  class="w-full flex items-center justify-between px-4 py-3 text-left bg-white border border-gray-300 rounded-xl hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"
                >
                  <span class="text-sm text-gray-700">
                    {{ filters.exam_board_id ? getExamBoardName(filters.exam_board_id) : 'All boards' }}
                  </span>
                  <svg
                    class="w-4 h-4 text-gray-500 transition-transform"
                    :class="{ 'rotate-180': showExamBoardDropdown }"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </button>

                <!-- Exam Board Dropdown Menu -->
                <div
                  v-if="showExamBoardDropdown"
                  class="absolute z-50 mt-2 w-full bg-white border border-gray-200 rounded-xl shadow-lg max-h-64 overflow-y-auto"
                >
                  <div class="p-2">
                    <button
                      @click="filters.exam_board_id = ''; applyFilters()"
                      class="w-full px-3 py-2 text-sm text-left rounded-lg hover:bg-gray-50 transition-colors flex items-center justify-between mb-1"
                      :class="{ 'text-emerald-700 bg-emerald-50': !filters.exam_board_id }"
                    >
                      <span>All exam boards</span>
                      <svg v-if="!filters.exam_board_id" class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                    </button>

                    <div class="space-y-1">
                      <button
                        v-for="board in exam_boards"
                        :key="board.id"
                        @click="filters.exam_board_id = board.id; applyFilters()"
                        class="w-full px-3 py-2 text-sm text-left rounded-lg hover:bg-gray-50 transition-colors flex items-center justify-between"
                        :class="{ 'text-emerald-700 bg-emerald-50': filters.exam_board_id == board.id }"
                      >
                        <span>{{ board.name }}</span>
                        <svg v-if="filters.exam_board_id == board.id" class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                v-if="filters.subject"
                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm bg-blue-50 text-blue-700 border border-blue-200"
              >
                Subject: {{ filters.subject }}
                <button @click.stop="filters.subject = ''; applyFilters()" class="text-blue-600 hover:text-blue-800 hover:bg-blue-100 rounded-full p-0.5">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </span>

              <span
                v-if="filters.level"
                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm bg-purple-50 text-purple-700 border border-purple-200"
              >
                Level: {{ formatLevel(filters.level) }}
                <button @click.stop="filters.level = ''; applyFilters()" class="text-purple-600 hover:text-purple-800 hover:bg-purple-100 rounded-full p-0.5">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </span>

              <span
                v-if="filters.exam_board_id"
                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm bg-amber-50 text-amber-700 border border-amber-200"
              >
                Board: {{ getExamBoardName(filters.exam_board_id) }}
                <button @click.stop="filters.exam_board_id = ''; applyFilters()" class="text-amber-600 hover:text-amber-800 hover:bg-amber-100 rounded-full p-0.5">
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
              <span class="text-emerald-600">{{ courses.data?.length || 0 }}</span>
              {{ (courses.data?.length || 0) === 1 ? 'Course' : 'Courses' }} Available
            </h2>
            <p v-if="hasActiveFilters" class="text-sm text-gray-600 mt-1">
              Filtered by: <span class="font-medium">{{ activeFiltersText }}</span>
            </p>
          </div>
          <div class="text-sm text-gray-600">
            Showing {{ courses.data?.length || 0 }} of {{ courses.total || 0 }} courses
          </div>
        </div>

        <!-- Courses Grid -->
        <div v-if="courses.data && courses.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div
            v-for="course in courses.data"
            :key="course.id"
            class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 group"
          >
            <!-- Course Header -->
            <div class="h-48 relative overflow-hidden bg-gradient-to-br from-emerald-500 to-teal-600">
              <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>

              <!-- Enrollment Status Badge -->
              <div class="absolute top-4 left-4">
                <span v-if="isEnrolled(course.id)" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                  <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  Enrolled
                </span>
                <span v-else-if="course.current_enrollment >= (course.enrollment_limit || Infinity)" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                  <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.798-.833-2.732 0L4.346 16.5c-.77.833.192 2.5 1.732 2.5z" />
                  </svg>
                  Full
                </span>
                <span v-else class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                  <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
                  {{ course.current_enrollment || 0 }} enrolled
                </span>
              </div>

              <!-- Level Badge -->
              <div class="absolute top-4 right-4">
                <span :class="getLevelBadgeClass(course.level)" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold capitalize">
                  {{ course.level }}
                </span>
              </div>

              <!-- Course Title -->
              <div class="absolute inset-0 flex items-end p-6">
                <div>
                  <span v-if="course.exam_board" class="inline-block px-3 py-1 rounded-full text-xs bg-white/30 backdrop-blur-sm text-white mb-3 font-medium">{{ course.exam_board.name }}</span>
                  <h3 class="text-white font-bold text-xl line-clamp-2">{{ course.title }}</h3>
                </div>
              </div>
            </div>

            <!-- Course Body -->
            <div class="p-6">
              <!-- Description -->
              <p class="text-gray-700 text-sm mb-5 line-clamp-3">{{ course.description }}</p>

              <!-- Course Details -->
              <div class="space-y-4 mb-5">
                <div class="flex items-center justify-between text-xs text-gray-600">
                  <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    {{ course.estimated_duration_hours || 0 }} hours
                  </div>
                  <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                    </svg>
                    {{ course.modules_count || 0 }} modules
                  </div>
                </div>

                <div class="flex items-center text-xs text-gray-500">
                  <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                  <span>By {{ course.creator?.name || 'OliLearn' }}</span>
                </div>

                <!-- Enrollment Progress -->
                <div v-if="course.enrollment_limit" class="text-xs text-gray-500">
                  <div class="flex justify-between mb-1">
                    <span>Enrollment</span>
                    <span class="font-medium">{{ course.current_enrollment || 0 }} / {{ course.enrollment_limit }} spots</span>
                  </div>
                  <div class="w-full bg-gray-200 rounded-full h-2">
                    <div
                      class="h-2 rounded-full transition-all duration-300"
                      :class="getEnrollmentProgressClass(course)"
                      :style="{ width: `${Math.min((course.current_enrollment || 0) / course.enrollment_limit * 100, 100)}%` }"
                    ></div>
                  </div>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="space-y-3">
                <!-- Enrolled State -->
                <div v-if="isEnrolled(course.id)" class="space-y-2">
                  <Link
                    :href="route('student.courses.learn', {
                      course: course.id,
                      topic: enrolled_courses[course.id]?.lastTopic
                    })"
                    class="w-full block text-center bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white py-3 rounded-xl font-medium transition-all duration-300 group-hover:shadow-lg"
                  >
                    Continue Learning
                  </Link>
                  <div class="flex gap-2">
                    <Link
                      :href="route('student.courses.show', course.id)"
                      class="flex-1 text-center px-3 py-2 text-sm text-gray-700 hover:text-emerald-700 hover:bg-emerald-50 border border-gray-300 hover:border-emerald-300 rounded-lg transition-colors"
                    >
                      View Details
                    </Link>
                    <button
                      @click="unenrollCourse(course)"
                      class="flex-1 text-center px-3 py-2 text-sm text-red-700 hover:text-red-800 hover:bg-red-50 border border-red-300 hover:border-red-400 rounded-lg transition-colors"
                    >
                      Unenroll
                    </button>
                  </div>
                </div>

                <!-- Not Enrolled State -->
                <div v-else class="space-y-2">
                  <Link
                    :href="route('student.courses.preview', course.id)"
                    class="w-full block text-center border border-gray-300 text-gray-700 hover:text-emerald-700 hover:border-emerald-300 hover:bg-emerald-50 py-3 rounded-xl font-medium transition-all duration-300"
                  >
                    Preview Course
                  </Link>

                  <button
                    v-if="course.current_enrollment < (course.enrollment_limit || Infinity)"
                    @click="enrollCourse(course)"
                    class="w-full block text-center bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white py-3 rounded-xl font-medium transition-all duration-300 group-hover:shadow-lg flex items-center justify-center"
                    :disabled="enrollingCourseId === course.id"
                  >
                    <svg v-if="enrollingCourseId !== course.id" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    <svg v-else class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ enrollingCourseId === course.id ? 'Enrolling...' : 'Enroll Now' }}
                  </button>
                  <div v-else class="text-center py-2">
                    <span class="text-sm text-red-600 font-medium">Course is full - enrollment closed</span>
                  </div>
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
            {{ hasActiveFilters ? 'No courses match your filters' : 'No courses available' }}
          </h3>
          <p class="text-gray-600 max-w-md mx-auto mb-6">
            {{ hasActiveFilters
              ? 'Try adjusting your filters or search terms to find what you\'re looking for.'
              : 'We\'re constantly adding new courses. Check back soon!'
            }}
          </p>
          <div class="space-x-3">
            <button
              v-if="hasActiveFilters"
              @click="clearFilters"
              class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white rounded-lg font-medium transition-all hover:shadow-md"
            >
              Clear All Filters
            </button>
            <Link
              :href="route('student.courses.index')"
              class="inline-flex items-center px-5 py-2.5 border border-gray-300 rounded-lg font-medium text-gray-700 hover:bg-gray-50 transition-colors"
            >
              View My Courses
            </Link>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="courses.data && courses.data.length > 0 && courses.links && courses.links.length > 3" class="mt-8">
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
  </StudentLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'
import { debounce } from 'lodash-es'
import StudentLayout from '@/Layouts/StudentLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
  courses: {
    type: Object,
    default: () => ({ data: [] })
  },
  enrolled_course_ids: {
    type: Array,
    default: () => []
  },
  enrolled_courses: {
    type: Object,
    default: () => ({})
  },
  subjects: {
    type: Array,
    default: () => []
  },
  levels: {
    type: Object,
    default: () => ({})
  },
  exam_boards: {
    type: Array,
    default: () => []
  },
  total_courses: {
    type: Number,
    default: 0
  },
  filters: {
    type: Object,
    default: () => ({})
  },
})

// Reactive state
const filters = ref({
  search: props.filters?.search || '',
  subject: props.filters?.subject || '',
  level: props.filters?.level || '',
  exam_board_id: props.filters?.exam_board_id || '',
  sort: props.filters?.sort || 'recent',
})

// Dropdown states
const showSubjectDropdown = ref(false)
const showLevelDropdown = ref(false)
const showExamBoardDropdown = ref(false)
const showSortDropdown = ref(false)
const subjectSearch = ref('')

// Enrollment state
const enrollingCourseId = ref(null)

// Options
const levelOptions = computed(() => {
  return Object.entries(props.levels || {}).map(([value, label]) => ({
    value,
    label
  }))
})

const sortOptions = [
  { value: 'recent', label: 'Recently Added' },
  { value: 'popular', label: 'Most Popular' },
  { value: 'duration', label: 'Shortest First' },
  { value: 'enrollment', label: 'Most Available' },
]

// Close dropdowns when clicking outside
const closeAllDropdowns = () => {
  showSubjectDropdown.value = false
  showLevelDropdown.value = false
  showExamBoardDropdown.value = false
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
const toggleSubjectDropdown = () => {
  showSubjectDropdown.value = !showSubjectDropdown.value
  showLevelDropdown.value = false
  showExamBoardDropdown.value = false
  showSortDropdown.value = false
}

const toggleLevelDropdown = () => {
  showLevelDropdown.value = !showLevelDropdown.value
  showSubjectDropdown.value = false
  showExamBoardDropdown.value = false
  showSortDropdown.value = false
}

const toggleExamBoardDropdown = () => {
  showExamBoardDropdown.value = !showExamBoardDropdown.value
  showSubjectDropdown.value = false
  showLevelDropdown.value = false
  showSortDropdown.value = false
}

const toggleSortDropdown = () => {
  showSortDropdown.value = !showSortDropdown.value
  showSubjectDropdown.value = false
  showLevelDropdown.value = false
  showExamBoardDropdown.value = false
}

// Computed properties
const enrolledCoursesCount = computed(() => {
  return props.enrolled_course_ids?.length || 0
})

const availableCoursesCount = computed(() => {
  return props.total_courses - enrolledCoursesCount.value
})

const hasActiveFilters = computed(() => {
  return filters.value.search ||
         filters.value.subject ||
         filters.value.level ||
         filters.value.exam_board_id
})

const getTotalFilters = () => {
  let count = 0
  if (filters.value.search) count++
  if (filters.value.subject) count++
  if (filters.value.level) count++
  if (filters.value.exam_board_id) count++
  return count
}

const activeFiltersText = computed(() => {
  const parts = []
  if (filters.value.search) parts.push(`"${filters.value.search}"`)
  if (filters.value.subject) parts.push(filters.value.subject)
  if (filters.value.level) parts.push(formatLevel(filters.value.level))
  if (filters.value.exam_board_id) parts.push(getExamBoardName(filters.value.exam_board_id))
  return parts.join(' • ')
})

const filteredSubjects = computed(() => {
  if (!subjectSearch.value.trim()) return props.subjects
  const searchTerm = subjectSearch.value.toLowerCase()
  return props.subjects.filter(subject =>
    subject.toLowerCase().includes(searchTerm)
  )
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

const formatLevel = (level) => {
  return props.levels?.[level] || level
}

const getExamBoardName = (boardId) => {
  const board = props.exam_boards?.find(b => b.id == boardId)
  return board?.name || 'Unknown'
}

const getEnrollmentProgressClass = (course) => {
  const percentage = ((course.current_enrollment || 0) / course.enrollment_limit) * 100
  if (percentage >= 90) return 'bg-red-500'
  if (percentage >= 75) return 'bg-amber-500'
  return 'bg-emerald-500'
}

const isEnrolled = (courseId) => {
  return props.enrolled_course_ids.includes(courseId)
}

const getSortLabel = () => {
  const option = sortOptions.find(opt => opt.value === filters.value.sort)
  return option ? option.label : 'Sort By'
}

// Methods
const debouncedSearch = debounce(() => {
  applyFilters()
}, 500)

const applyFilters = () => {
  router.get(route('student.catalog.browse'), filters.value, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  })
  closeAllDropdowns()
}

const applySorting = () => {
  router.get(route('student.catalog.browse'), filters.value, {
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
    subject: '',
    level: '',
    exam_board_id: '',
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

// Enrollment Actions
const enrollCourse = async (course) => {
  if (enrollingCourseId.value) return

  enrollingCourseId.value = course.id

  try {
    await router.post(route('student.courses.enroll', course.id), {}, {
      preserveScroll: true,
      preserveState: true,
      onSuccess: () => {
        router.reload({ only: ['courses', 'enrolled_course_ids', 'enrolled_courses'] })
      },
      onError: (errors) => {
        alert(errors.message || 'Failed to enroll in the course')
      },
      onFinish: () => {
        enrollingCourseId.value = null
      }
    })
  } catch (error) {
    console.error('Enrollment failed:', error)
    enrollingCourseId.value = null
  }
}

const unenrollCourse = async (course) => {
  if (!confirm('Are you sure you want to unenroll from this course? All your progress will be lost.')) {
    return
  }

  try {
    await router.post(route('student.courses.drop', course.id), {
      preserveScroll: true,
      preserveState: true,
      onSuccess: () => {
        router.reload({ only: ['courses', 'enrolled_course_ids', 'enrolled_courses'] })
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
