<template>
  <MetaTags
    :title="`${pageTitle} | OliLearn Exam Preparation`"
    :description="pageDescription"
    image="/images/olilearn-exam-prep.png"
    type="website"
  />
  <AppLayout>
    <Head :title="`${pageTitle} | Exam Preparation`" />


    <!-- Main Content -->
    <section ref="examPrepSection" class="py-12 md:py-16 bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header & Search -->
        <div class="mb-10">
          <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
            <div>
              <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">
                Browse
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-teal-600">
                  Practice Exams
                </span>
              </h2>
              <p class="text-gray-600 max-w-2xl">
                Choose from our collection of exam-specific practice tests with AI-powered feedback.
              </p>
            </div>

            <!-- Search Bar -->
            <div class="flex-1 max-w-xl">
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                  </svg>
                </div>
                <input
                  type="text"
                  v-model="search"
                  placeholder="Search by exam board, subject, or name..."
                  class="w-full pl-10 pr-12 py-3 rounded-xl border border-gray-300 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"
                  @input="debouncedSearch"
                />
                <button
                  v-if="search"
                  @click="search = ''; applyFilters()"
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

          <!-- Quick Filter Tags -->
          <div class="flex flex-wrap items-center gap-2 mb-6">
            <span class="text-sm text-gray-600 font-medium">Popular:</span>
            <div class="flex flex-wrap gap-2">
              <button
                v-for="tag in popularTags"
                :key="tag"
                @click="applyTagSearch(tag)"
                class="px-4 py-2 text-sm bg-white border border-gray-300 hover:border-emerald-400 text-gray-700 hover:text-emerald-700 rounded-lg transition-colors shadow-sm hover:shadow"
              >
                {{ tag }}
              </button>
            </div>
          </div>

          <!-- Filter Bar -->
          <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4">
            <div class="flex flex-wrap items-center gap-4">
              <!-- Exam Board Filter -->
              <div class="relative">
                <button
                  @click="toggleExamBoardDropdown"
                  class="flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 rounded-lg hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"
                >
                  <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                  </svg>
                  <span class="text-sm text-gray-700">
                    {{ selectedExamBoards.length > 0 ? `${selectedExamBoards.length} boards` : 'All Exam Boards' }}
                  </span>
                  <svg class="w-4 h-4 text-gray-500" :class="{ 'rotate-180': showExamBoardDropdown }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </button>

                <!-- Exam Board Dropdown -->
                <div
                  v-if="showExamBoardDropdown"
                  class="absolute z-50 mt-2 w-64 bg-white border border-gray-200 rounded-xl shadow-lg max-h-80 overflow-y-auto"
                >
                  <div class="p-3">
                    <div class="relative mb-2">
                      <input
                        v-model="examBoardSearch"
                        type="text"
                        placeholder="Search exam boards..."
                        class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500"
                        @click.stop
                      />
                      <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                      </svg>
                    </div>

                    <div class="space-y-1">
                      <label
                        v-for="board in filteredExamBoards"
                        :key="board.id"
                        class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-50 cursor-pointer"
                      >
                        <input
                          type="checkbox"
                          :value="board.id"
                          v-model="selectedExamBoards"
                          @change="applyFilters"
                          class="h-4 w-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500"
                        />
                        <span class="ml-3 text-sm text-gray-700">{{ board.name }}</span>
                      </label>

                      <div v-if="filteredExamBoards.length === 0" class="px-3 py-2 text-sm text-gray-500 text-center">
                        No exam boards found
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Subject Filter -->
              <div class="relative">
                <button
                  @click="toggleSubjectDropdown"
                  class="flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 rounded-lg hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"
                >
                  <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M12 14l9-5-9-5-9 5 9 5z" />
                    <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                  </svg>
                  <span class="text-sm text-gray-700">
                    {{ selectedSubjects.length > 0 ? `${selectedSubjects.length} subjects` : 'All Subjects' }}
                  </span>
                  <svg class="w-4 h-4 text-gray-500" :class="{ 'rotate-180': showSubjectDropdown }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </button>

                <!-- Subject Dropdown -->
                <div
                  v-if="showSubjectDropdown"
                  class="absolute z-50 mt-2 w-64 bg-white border border-gray-200 rounded-xl shadow-lg max-h-80 overflow-y-auto"
                >
                  <div class="p-3">
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

                    <div class="space-y-1">
                      <label
                        v-for="subject in filteredSubjects"
                        :key="subject.id"
                        class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-50 cursor-pointer"
                      >
                        <input
                          type="checkbox"
                          :value="subject.id"
                          v-model="selectedSubjects"
                          @change="applyFilters"
                          class="h-4 w-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500"
                        />
                        <span class="ml-3 text-sm text-gray-700">{{ subject.name }}</span>
                      </label>

                      <div v-if="filteredSubjects.length === 0" class="px-3 py-2 text-sm text-gray-500 text-center">
                        No subjects found
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Sort By -->
              <div class="relative">
                <button
                  @click="toggleSortDropdown"
                  class="flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 rounded-lg hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"
                >
                  <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                  </svg>
                  <span class="text-sm text-gray-700">{{ getSortLabel() }}</span>
                  <svg class="w-4 h-4 text-gray-500" :class="{ 'rotate-180': showSortDropdown }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </button>

                <!-- Sort Dropdown -->
                <div
                  v-if="showSortDropdown"
                  class="absolute z-50 mt-2 w-48 bg-white border border-gray-200 rounded-xl shadow-lg"
                >
                  <div class="py-1">
                    <button
                      v-for="option in sortOptions"
                      :key="option.value"
                      @click="sortBy = option.value; applyFilters()"
                      class="w-full px-4 py-2.5 text-sm text-left hover:bg-emerald-50 hover:text-emerald-700 transition-colors flex items-center justify-between"
                      :class="{ 'text-emerald-700 bg-emerald-50': sortBy === option.value }"
                    >
                      <span>{{ option.label }}</span>
                      <svg v-if="sortBy === option.value" class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                    </button>
                  </div>
                </div>
              </div>

              <!-- Active Filters -->
              <div v-if="hasActiveFilters" class="flex-1 flex items-center justify-end gap-2">
                <span class="text-sm text-gray-500">
                  {{ getTotalFilters() }} active
                </span>
                <button
                  @click="clearFilters"
                  class="text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 px-3 py-1.5 rounded-lg transition-colors"
                >
                  Clear all
                </button>
              </div>
            </div>
          </div>

          <!-- Active Filter Tags -->
          <div v-if="hasActiveFilters" class="flex flex-wrap gap-2 mt-4">
            <span
              v-if="search"
              class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm bg-emerald-50 text-emerald-700 border border-emerald-200"
            >
              Search: "{{ search }}"
              <button @click="search = ''; applyFilters()" class="text-emerald-600 hover:text-emerald-800">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </span>

            <span
              v-for="boardId in selectedExamBoards"
              :key="`board-${boardId}`"
              class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm bg-blue-50 text-blue-700 border border-blue-200"
            >
              {{ getExamBoardName(boardId) }}
              <button @click="removeFilter('exam_board', boardId)" class="text-blue-600 hover:text-blue-800">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </span>

            <span
              v-for="subjectId in selectedSubjects"
              :key="`subject-${subjectId}`"
              class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm bg-purple-50 text-purple-700 border border-purple-200"
            >
              {{ getSubjectName(subjectId) }}
              <button @click="removeFilter('subject', subjectId)" class="text-purple-600 hover:text-purple-800">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </span>
          </div>
        </div>

        <!-- Results Header -->
        <div class="flex items-center justify-between mb-6">
          <div>
            <h3 class="text-lg font-semibold text-gray-900">
              <span class="text-emerald-600">{{ examPreps.total }}</span>
              {{ examPreps.data.length === 1 ? 'Practice Exam' : 'Practice Exams' }} Available
            </h3>
            <p v-if="hasActiveFilters" class="text-sm text-gray-600 mt-1">
              Filtered by: <span class="font-medium">{{ activeFiltersText }}</span>
            </p>
          </div>
        </div>

        <!-- Exam Preps Grid -->
        <div v-if="allExamPreps.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div
            v-for="examPrep in allExamPreps"
            :key="examPrep.id"
            class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 group"
          >
            <!-- Card Header with Gradient -->
            <div class="h-48 relative overflow-hidden bg-gradient-to-br from-emerald-600 to-teal-700">
              <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>

              <!-- Exam Board Badge -->
              <div class="absolute top-4 left-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white/20 backdrop-blur-sm text-white border border-white/30">
                  {{ examPrep.exam_board?.name || 'General' }}
                </span>
              </div>

              <!-- Question Count -->
              <div class="absolute top-4 right-4">
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-black/30 backdrop-blur-sm text-white">
                  {{ examPrep.total_questions }} Questions
                </span>
              </div>

              <!-- Title and Subject -->
              <div class="absolute bottom-0 left-0 right-0 p-6">
                <span class="text-xs text-emerald-200 font-medium mb-1 block">
                  {{ examPrep.subject?.name || 'General Subject' }}
                </span>
                <h3 class="text-white font-bold text-xl line-clamp-2 group-hover:text-emerald-100 transition-colors">
                  {{ examPrep.name }}
                </h3>
              </div>
            </div>

            <!-- Card Body -->
            <div class="p-6">
              <!-- Description -->
              <p class="text-gray-600 text-sm mb-5 line-clamp-3">
                {{ examPrep.description || 'Comprehensive practice test with detailed explanations and AI-powered feedback.' }}
              </p>

              <!-- Meta Info -->
              <div class="flex items-center justify-between text-xs text-gray-600 mb-5">
                <div class="flex items-center space-x-4">
                  <!-- Time Limit -->
                  <span class="flex items-center">
                    <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ examPrep.time_limit_minutes || 30 }} min
                  </span>

                  <!-- Difficulty Mix -->
                  <span class="flex items-center">
                    <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    {{ getDifficultyLabel(examPrep) }}
                  </span>
                </div>

                <!-- Passing Score -->
                <span class="text-emerald-700 font-medium">
                  Pass: {{ examPrep.passing_score || 70 }}%
                </span>
              </div>

              <!-- Action Button -->
              <button
                @click="handleExamPrepClick(examPrep)"
                class="w-full block text-center bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white py-3 rounded-xl font-medium transition-all duration-300 group-hover:shadow-lg"
              >
                {{ $page.props.auth.user ? 'Start Practice' : 'Sign in to Access' }}
              </button>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-16 bg-white rounded-2xl border border-gray-200 shadow-sm">
          <div class="w-20 h-20 mx-auto mb-6 text-gray-400 bg-gray-100 rounded-full flex items-center justify-center">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </div>
          <h3 class="text-xl font-bold text-gray-900 mb-3">No practice exams found</h3>
          <p class="text-gray-600 max-w-md mx-auto mb-6">
            {{ hasActiveFilters
              ? 'Try adjusting your filters or search terms to find what you\'re looking for.'
              : 'We\'re constantly adding new exam preps. Check back soon!'
            }}
          </p>
          <button
            v-if="hasActiveFilters"
            @click="clearFilters"
            class="px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white rounded-xl font-medium transition-all hover:shadow-md"
          >
            Clear All Filters
          </button>
        </div>

        <!-- Infinite Scroll Sentinel -->
        <div ref="scrollSentinel" class="mt-12 py-4 flex justify-center items-center">
          <div v-if="isLoadingMore" class="flex items-center space-x-3 text-emerald-600">
            <svg class="animate-spin h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="font-medium">Loading more exams...</span>
          </div>
          <div v-else-if="!nextPageUrl && allExamPreps.length > 0" class="text-gray-500 text-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            You've reached the end of the list
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 bg-white border-t border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-12">
          <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">
            Why Choose Our
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-teal-600">
              Exam Preparation
            </span>
          </h2>
          <p class="text-gray-600">
            Our AI-powered platform provides everything you need to ace your exams with confidence.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div class="text-center p-6">
            <div class="w-16 h-16 bg-emerald-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
              </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">AI-Powered Insights</h3>
            <p class="text-gray-600 text-sm">
              Get personalized feedback and recommendations based on your performance.
            </p>
          </div>

          <div class="text-center p-6">
            <div class="w-16 h-16 bg-emerald-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
              </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Exam-Specific Content</h3>
            <p class="text-gray-600 text-sm">
              Questions designed specifically for WAEC, NECO, JAMB, and international exams.
            </p>
          </div>

          <div class="text-center p-6">
            <div class="w-16 h-16 bg-emerald-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
              </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Detailed Explanations</h3>
            <p class="text-gray-600 text-sm">
              Learn from comprehensive explanations for every question, right or wrong.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Banner -->
    <section class="bg-gradient-to-br from-emerald-900 via-emerald-800 to-teal-900 text-white">
      <div class="max-w-4xl mx-auto px-4 py-16 text-center">
        <h2 class="text-2xl md:text-3xl font-bold mb-4">Ready to Ace Your Exams?</h2>
        <p class="text-emerald-100 mb-8 text-lg">
          Join thousands of successful students who improved their scores with our practice tests.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <Link
            :href="route('register')"
            class="inline-flex items-center justify-center px-6 py-3 bg-white text-emerald-700 font-semibold rounded-lg hover:bg-gray-100 transition-all hover:shadow-xl"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Create Free Account
          </Link>
          <Link
            :href="route('pricing')"
            class="inline-flex items-center justify-center px-6 py-3 bg-transparent border border-white text-white font-semibold rounded-lg hover:bg-white/10 transition-all"
          >
            View Pricing Plans
          </Link>
        </div>
      </div>
    </section>
  </AppLayout>
</template>

<script setup>
import { Link, router, Head, usePage } from '@inertiajs/vue3';
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { debounce } from 'lodash-es';
import AppLayout from '@/Layouts/GuestLayout.vue';
import MetaTags from '@/Components/MetaTags.vue';
import axios from 'axios';

const page = usePage();

const props = defineProps({
  examPreps: Object,
  examBoards: Array,
  subjects: Array,
  filters: Object,
  stats: Object,
  pageTitle: {
    type: String,
    default: 'Exam Preparation'
  },
  pageDescription: {
    type: String,
    default: 'Prepare for your exams with AI-powered practice tests. Access thousands of exam-style questions with detailed explanations.'
  }
});

// --- State Management ---
const search = ref(props.filters?.search || '');
const selectedExamBoards = ref(props.filters?.exam_boards ? props.filters.exam_boards.split(',').map(Number) : []);
const selectedSubjects = ref(props.filters?.subjects ? props.filters.subjects.split(',').map(Number) : []);
const sortBy = ref(props.filters?.sort || 'latest');

// Dropdown states
const showExamBoardDropdown = ref(false);
const showSubjectDropdown = ref(false);
const showSortDropdown = ref(false);
const examBoardSearch = ref('');
const subjectSearch = ref('');

// Popular tags
const popularTags = [
  'WAEC', 'NECO', 'JAMB', 'IELTS', 'TOEFL',
  'Mathematics', 'English', 'Physics', 'Chemistry', 'Biology'
];

// Sort options
const sortOptions = [
  { value: 'latest', label: 'Latest First' },
  { value: 'popular', label: 'Most Popular' },
  { value: 'questions_desc', label: 'Most Questions' },
  { value: 'questions_asc', label: 'Fewest Questions' },
  { value: 'name_asc', label: 'Name (A-Z)' },
  { value: 'name_desc', label: 'Name (Z-A)' }
];

// --- Infinite Scroll ---
const allExamPreps = ref([...props.examPreps.data]);
const nextPageUrl = ref(props.examPreps.next_page_url);
const isLoadingMore = ref(false);
const scrollSentinel = ref(null);
let observer = null;

// --- Computed Properties ---
const filteredExamBoards = computed(() => {
  if (!examBoardSearch.value.trim()) return props.examBoards;
  const searchTerm = examBoardSearch.value.toLowerCase();
  return props.examBoards.filter(board =>
    board.name.toLowerCase().includes(searchTerm)
  );
});

const filteredSubjects = computed(() => {
  if (!subjectSearch.value.trim()) return props.subjects;
  const searchTerm = subjectSearch.value.toLowerCase();
  return props.subjects.filter(subject =>
    subject.name.toLowerCase().includes(searchTerm)
  );
});

const hasActiveFilters = computed(() => {
  return search.value ||
         selectedExamBoards.value.length > 0 ||
         selectedSubjects.value.length > 0;
});

const activeFiltersText = computed(() => {
  const parts = [];
  if (search.value) parts.push(`"${search.value}"`);
  if (selectedExamBoards.value.length) parts.push(`${selectedExamBoards.value.length} exam board${selectedExamBoards.value.length > 1 ? 's' : ''}`);
  if (selectedSubjects.value.length) parts.push(`${selectedSubjects.value.length} subject${selectedSubjects.value.length > 1 ? 's' : ''}`);
  return parts.join(' • ');
});

// --- Methods ---
const getExamBoardName = (id) => {
  const board = props.examBoards.find(b => b.id === id);
  return board?.name || 'Unknown';
};

const getSubjectName = (id) => {
  const subject = props.subjects.find(s => s.id === id);
  return subject?.name || 'Unknown';
};

const getDifficultyLabel = (examPrep) => {
  const distribution = examPrep.question_distribution || {};
  const easy = distribution.easy || 0;
  const medium = distribution.medium || 0;
  const hard = distribution.hard || 0;

  if (hard > medium && hard > easy) return 'Challenging';
  if (medium > hard && medium > easy) return 'Moderate';
  return 'Mixed Difficulty';
};

const getSortLabel = () => {
  const option = sortOptions.find(opt => opt.value === sortBy.value);
  return option ? option.label : 'Sort By';
};

const getTotalFilters = () => {
  let count = 0;
  if (search.value) count++;
  count += selectedExamBoards.value.length;
  count += selectedSubjects.value.length;
  return count;
};

// --- Dropdown Toggles ---
const closeAllDropdowns = () => {
  showExamBoardDropdown.value = false;
  showSubjectDropdown.value = false;
  showSortDropdown.value = false;
};

const toggleDropdown = (stateRef) => {
  const wasOpen = stateRef.value;
  closeAllDropdowns();
  stateRef.value = !wasOpen;
};

const toggleExamBoardDropdown = () => toggleDropdown(showExamBoardDropdown);
const toggleSubjectDropdown = () => toggleDropdown(showSubjectDropdown);
const toggleSortDropdown = () => toggleDropdown(showSortDropdown);

// --- Filter Actions ---
const debouncedSearch = debounce(() => {
  applyFilters();
}, 500);

const applyTagSearch = (tag) => {
  search.value = tag;
  applyFilters();
};

const removeFilter = (type, value) => {
  if (type === 'exam_board') {
    const index = selectedExamBoards.value.indexOf(value);
    if (index !== -1) selectedExamBoards.value.splice(index, 1);
  } else if (type === 'subject') {
    const index = selectedSubjects.value.indexOf(value);
    if (index !== -1) selectedSubjects.value.splice(index, 1);
  }
  applyFilters();
};

const applyFilters = () => {
  router.get(route('exam-preps.index'), {
    search: search.value || null,
    exam_boards: selectedExamBoards.value.length ? selectedExamBoards.value.join(',') : null,
    subjects: selectedSubjects.value.length ? selectedSubjects.value.join(',') : null,
    sort: sortBy.value
  }, {
    preserveState: true,
    preserveScroll: false,
    replace: true
  });
};

const clearFilters = () => {
  search.value = '';
  selectedExamBoards.value = [];
  selectedSubjects.value = [];
  sortBy.value = 'latest';
  applyFilters();
};

const scrollToExamPreps = () => {
  document.querySelector('.bg-gray-50')?.scrollIntoView({ behavior: 'smooth' });
};

// --- Exam Prep Click Handler ---
const handleExamPrepClick = (examPrep) => {
  if (page.props.auth?.user) {
    router.get(`/student/exam-preps/${examPrep.id}`);
  } else {
    const redirectUrl = `/student/exam-preps/${examPrep.id}`;
    window.location.href = `/login?redirect=${encodeURIComponent(redirectUrl)}`;
  }
};

// --- Infinite Scroll Logic ---
const loadMoreExamPreps = async () => {
  if (isLoadingMore.value || !nextPageUrl.value) return;

  isLoadingMore.value = true;
  try {
    const response = await axios.get(nextPageUrl.value, {
      headers: { 'Accept': 'application/json' }
    });

    const newExamPreps = response.data.data.filter(newPrep =>
      !allExamPreps.value.some(existing => existing.id === newPrep.id)
    );

    allExamPreps.value.push(...newExamPreps);
    nextPageUrl.value = response.data.next_page_url;
  } catch (error) {
    console.error("Failed to load more exam preps", error);
  } finally {
    isLoadingMore.value = false;
  }
};

// --- Lifecycle Hooks ---
onMounted(() => {
  observer = new IntersectionObserver((entries) => {
    if (entries[0].isIntersecting) {
      loadMoreExamPreps();
    }
  }, {
    root: null,
    threshold: 0.1,
    rootMargin: '100px'
  });

  if (scrollSentinel.value) {
    observer.observe(scrollSentinel.value);
  }

  // Close dropdowns when clicking outside
  document.addEventListener('click', closeAllDropdowns);
});

onUnmounted(() => {
  if (observer) observer.disconnect();
  document.removeEventListener('click', closeAllDropdowns);
});

// Watch for prop changes
watch(() => props.examPreps, (newExamPreps) => {
  allExamPreps.value = [...newExamPreps.data];
  nextPageUrl.value = newExamPreps.next_page_url;
}, { deep: true });

// Watch for search input
watch(search, () => {
  debouncedSearch();
});
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

/* Smooth scrolling */
html {
  scroll-behavior: smooth;
}

/* Card hover effects */
.group:hover .group-hover\:shadow-xl {
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.group:hover .group-hover\:text-emerald-100 {
  color: #d1fae5;
}

/* Gradient text */
.text-transparent {
  -webkit-background-clip: text;
  background-clip: text;
}

/* Dropdown animations */
.rotate-180 {
  transform: rotate(180deg);
  transition: transform 0.2s ease;
}

/* Custom scrollbar for dropdowns */
.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 10px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #a1a1a1;
}
</style>
