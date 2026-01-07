<template>
  <StudentLayout>
    <Head :title="course.title" />

    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Enhanced Header -->
        <div class="mb-8">
          <div class="flex justify-between items-start">
            <div class="flex-1">
              <div class="flex items-center gap-2 mb-3">
                <span v-if="enrollment" :class="['inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold', getStatusClass(enrollment.status)]">
                  {{ enrollment.status }}
                </span>
                <span v-else :class="['inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold', getStatusClass(course.status)]">
                  {{ course.status }}
                </span>
                <span
                  v-if="course.exam_board"
                  class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-emerald-100 text-emerald-800"
                >
                  {{ course.exam_board.name }}
                </span>
                <span class="text-sm text-gray-500">
                  <template v-if="enrollment?.started_at">
                    Started {{ formatRelativeDate(enrollment.started_at) }}
                  </template>
                  <template v-else>
                    Not started yet
                  </template>
                </span>
              </div>
              <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ course.title }}</h1>
              <p class="text-lg text-gray-600 mb-4">
                {{ course.subject }} • {{ course.level }}
                <span v-if="course.code" class="ml-2 text-sm font-mono bg-gray-100 px-2 py-1 rounded">
                  {{ course.code }}
                </span>
              </p>

              <!-- Enrollment Status Banner -->
              <div v-if="!isEnrolled" class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 mb-4 border border-blue-100">
                <div class="flex items-center justify-between">
                  <div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">
                      <template v-if="was_dropped">
                        Ready to re-enroll?
                      </template>
                      <template v-else>
                        Ready to enroll?
                      </template>
                    </h3>
                    <p class="text-sm text-gray-600">
                      {{ course.is_paid ? `Price: $${course.price}` : 'This course is free' }}
                      • {{ course.estimated_duration_hours || 0 }} hours
                      • {{ course.current_enrollment || 0 }} students enrolled
                      <template v-if="was_dropped">
                        <span class="ml-2 text-amber-600 font-semibold">(Previously dropped)</span>
                      </template>
                    </p>
                  </div>
                  <div>
                    <button
                      @click="enrollInCourse"
                      :disabled="enrollLoading || !can_enroll"
                      :class="[
                        'px-6 py-3 rounded-lg font-semibold text-white transition-all duration-200',
                        enrollLoading
                          ? 'bg-blue-400 cursor-not-allowed'
                          : can_enroll
                            ? was_dropped
                              ? 'bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-700 hover:to-orange-700 shadow-md hover:shadow-lg'
                              : 'bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 shadow-md hover:shadow-lg'
                            : 'bg-gray-400 cursor-not-allowed'
                      ]"
                    >
                      <span v-if="enrollLoading">
                        <svg class="animate-spin h-5 w-5 mr-2 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Processing...
                      </span>
                      <span v-else>
                        <template v-if="was_dropped">
                          Re-enroll Now
                        </template>
                        <template v-else>
                          {{ course.is_paid ? `Enroll Now - $${course.price}` : 'Enroll for Free' }}
                        </template>
                      </span>
                    </button>

                    <div v-if="!can_enroll && !isEnrolled" class="mt-2 text-sm text-amber-600">
                      <span v-if="course.isFull">Course is full</span>
                      <span v-else-if="course.status !== 'active'">Course is not available for enrollment</span>
                      <span v-else>Cannot enroll at this time</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Enhanced Progress Overview (Only for enrolled students) -->
              <div v-else class="bg-gradient-to-r from-emerald-50 to-teal-50 rounded-xl p-6 mb-4 border border-emerald-100">
                <div class="flex items-center justify-between mb-4">
                  <div>
                    <h3 class="text-lg font-bold text-gray-900">Course Progress</h3>
                    <p class="text-sm text-gray-600">Your journey to mastery</p>
                  </div>
                  <div class="text-right">
                    <div class="text-3xl font-bold text-emerald-600">
                      {{ overallCompletionPercentage }}%
                    </div>
                    <div class="text-sm text-gray-500">Complete</div>
                  </div>
                </div>

                <!-- Progress Bars -->
                <div class="space-y-3">
                  <div>
                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                      <span>Overall Progress</span>
                      <span class="font-semibold">{{ overallCompletionPercentage }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                      <div
                        class="bg-gradient-to-r from-emerald-500 to-teal-600 h-3 rounded-full transition-all duration-500 shadow-sm"
                        :style="{ width: `${overallCompletionPercentage}%` }"
                      ></div>
                    </div>
                  </div>

                  <div class="grid grid-cols-2 gap-4 text-xs">
                    <div class="text-center">
                      <div class="font-bold text-gray-900">{{ completedModules }}/{{ totalModules }}</div>
                      <div class="text-gray-500">Modules</div>
                    </div>
                    <div class="text-center">
                      <div class="font-bold text-gray-900">{{ completedTopics }}/{{ totalTopics }}</div>
                      <div class="text-gray-500">Topics</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Course Information for Non-Enrolled Students -->
        <div v-if="!isEnrolled" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
          <!-- Course Details Card -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center mb-4">
              <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center mr-3">
                <AcademicCapIcon class="h-5 w-5 text-blue-600" />
              </div>
              <div>
                <h3 class="font-semibold text-gray-900">Course Details</h3>
                <p class="text-sm text-gray-500">What you'll learn</p>
              </div>
            </div>
            <div class="space-y-2">
              <div class="flex items-center text-sm text-gray-600">
                <CheckCircleIcon class="h-4 w-4 text-emerald-500 mr-2" />
                <span>{{ course.estimated_duration_hours || 0 }} hours of content</span>
              </div>
              <div class="flex items-center text-sm text-gray-600">
                <CheckCircleIcon class="h-4 w-4 text-emerald-500 mr-2" />
                <span>{{ course.modules?.length || 0 }} modules</span>
              </div>
              <div class="flex items-center text-sm text-gray-600">
                <CheckCircleIcon class="h-4 w-4 text-emerald-500 mr-2" />
                <span>{{ getTotalTopics() }} topics</span>
              </div>
              <div class="flex items-center text-sm text-gray-600">
                <CheckCircleIcon class="h-4 w-4 text-emerald-500 mr-2" />
                <span>{{ getTotalQuizzes() }} quizzes</span>
              </div>
            </div>
          </div>

          <!-- Prerequisites Card -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" v-if="course.prerequisites?.length">
            <div class="flex items-center mb-4">
              <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center mr-3">
                <ClipboardDocumentListIcon class="h-5 w-5 text-amber-600" />
              </div>
              <div>
                <h3 class="font-semibold text-gray-900">Prerequisites</h3>
                <p class="text-sm text-gray-500">Recommended knowledge</p>
              </div>
            </div>
            <div class="space-y-2">
              <div
                v-for="(prereq, index) in course.prerequisites.slice(0, 3)"
                :key="index"
                class="text-sm text-gray-600"
              >
                • {{ prereq }}
              </div>
            </div>
          </div>

          <!-- Learning Objectives Card -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" v-if="course.learning_objectives?.length">
            <div class="flex items-center mb-4">
              <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center mr-3">
                <ChartBarIcon class="h-5 w-5 text-emerald-600" />
              </div>
              <div>
                <h3 class="font-semibold text-gray-900">Learning Objectives</h3>
                <p class="text-sm text-gray-500">What you'll achieve</p>
              </div>
            </div>
            <div class="space-y-2">
              <div
                v-for="(objective, index) in course.learning_objectives.slice(0, 3)"
                :key="index"
                class="text-sm text-gray-600 line-clamp-2"
              >
                • {{ objective }}
              </div>
            </div>
          </div>
        </div>

        <!-- Enhanced Course Stats (Only for enrolled students) -->
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <!-- Progress Card -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center">
                <ChartBarIcon class="h-6 w-6 text-emerald-600" />
              </div>
              <div class="text-right">
                <div class="text-2xl font-bold text-gray-900">{{ overallCompletionPercentage }}%</div>
                <div class="text-sm text-gray-500">Complete</div>
              </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2.5">
              <div
                class="bg-emerald-600 h-2.5 rounded-full transition-all duration-300"
                :style="{ width: `${overallCompletionPercentage}%` }"
              ></div>
            </div>
            <p class="text-xs text-gray-500 mt-2">
              {{ completedTopics }} of {{ totalTopics }} topics
            </p>
          </div>

          <!-- Time Spent Card -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 rounded-xl bg-teal-100 flex items-center justify-center">
                <ClockIcon class="h-6 w-6 text-teal-600" />
              </div>
              <div class="text-right">
                <div class="text-2xl font-bold text-gray-900">{{ formatStudyTime(totalTimeMinutes) }}</div>
                <div class="text-sm text-gray-500">Time Spent</div>
              </div>
            </div>
            <div class="flex justify-between text-xs text-gray-500">
              <span>Est: {{ course.estimated_duration_hours || 0 }}h</span>
              <span>Remaining: {{ formatStudyTime(estimatedRemainingTime) }}</span>
            </div>
          </div>

          <!-- Performance Card -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center">
                <AcademicCapIcon class="h-6 w-6 text-purple-600" />
              </div>
              <div class="text-right">
                <div class="text-2xl font-bold text-gray-900">{{ averageScore }}%</div>
                <div class="text-sm text-gray-500">Avg Score</div>
              </div>
            </div>
            <div class="text-xs text-gray-500">
              Based on {{ completedTopics }} completed topics
            </div>
          </div>

          <!-- Next Topic Card -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center">
                <CalendarIcon class="h-6 w-6 text-amber-600" />
              </div>
              <div class="text-right">
                <div class="text-sm font-semibold text-gray-900" v-if="next_topic">
                  Next Up
                </div>
                <div class="text-sm text-gray-500" v-else>All Done!</div>
              </div>
            </div>
            <div v-if="next_topic">
              <p class="text-sm font-medium text-gray-900 line-clamp-2 mb-2">
                {{ next_topic.title }}
              </p>
              <p class="text-xs text-gray-500">
                Module {{ getModuleNumber(next_topic.module_id) }} • {{ next_topic.estimated_duration_minutes || 0 }}min
              </p>
            </div>
            <div v-else class="text-center py-2">
              <CheckCircleIcon class="h-8 w-8 text-green-500 mx-auto mb-2" />
              <p class="text-sm text-gray-600">Course Completed!</p>
            </div>
          </div>
        </div>

        <!-- Completion Timeline (Only for enrolled students) -->
        <div v-if="isEnrolled" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
          <h3 class="text-lg font-bold text-gray-900 mb-4">Completion Timeline</h3>
          <div class="flex items-center justify-between mb-4">
            <div class="text-sm text-gray-600">
              <div>Start: {{ formatDate(enrollment?.started_at) }}</div>
              <div class="text-xs text-gray-400">Started {{ formatRelativeDate(enrollment?.started_at) }}</div>
            </div>
            <div class="flex-1 mx-4">
              <div class="relative">
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                  <div
                    class="bg-gradient-to-r from-emerald-500 to-teal-500 h-2.5 rounded-full transition-all duration-500"
                    :style="{ width: `${overallCompletionPercentage}%` }"
                  ></div>
                </div>
                <div
                  class="absolute top-0 w-3 h-3 bg-emerald-600 rounded-full border-2 border-white shadow-lg -mt-0.5 transition-all duration-500"
                  :style="{ left: `${overallCompletionPercentage}%` }"
                ></div>
              </div>
            </div>
            <div class="text-sm text-gray-600 text-right">
              <div>Target: {{ formatDate(course.target_completion_date) }}</div>
              <div class="text-xs" :class="getDaysRemainingClass(course.target_completion_date)">
                {{ getDaysRemaining(course.target_completion_date) }}
              </div>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Course Content -->
          <div :class="['lg:col-span-2', !isEnrolled ? 'opacity-50' : '']">
            <div class="bg-white shadow-sm rounded-xl border border-gray-100">
              <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-t-xl">
                <div class="flex items-center justify-between">
                  <div>
                    <h2 class="text-lg font-bold text-gray-900">Course Content</h2>
                    <p class="text-sm text-gray-500 mt-1">
                      {{ course.modules?.length || 0 }} modules • {{ getTotalTopics() }} topics
                    </p>
                  </div>
                  <div class="text-right" v-if="isEnrolled">
                    <div class="text-sm text-gray-600">
                      {{ completedTopics }}/{{ getTotalTopics() }} completed
                    </div>
                    <div class="text-xs text-gray-400">
                      {{ Math.round((completedTopics / getTotalTopics() * 100) || 0) }}% complete
                    </div>
                  </div>
                </div>
              </div>
              <div class="p-6">
                <!-- Course Description -->
                <div class="mb-6 p-4 bg-emerald-50 rounded-lg border border-emerald-100">
                  <h3 class="text-sm font-semibold text-emerald-900 mb-2">Course Description</h3>
                  <p class="text-sm text-emerald-800">{{ course.description || 'No description available' }}</p>
                </div>

                <!-- Learning Objectives -->
                <div class="mb-6" v-if="course.learning_objectives?.length">
                  <h3 class="text-sm font-semibold text-gray-700 mb-3">Learning Objectives</h3>
                  <div class="grid gap-2">
                    <div
                      v-for="(objective, index) in course.learning_objectives"
                      :key="index"
                      class="flex items-start p-3 bg-gray-50 rounded-lg border border-gray-200"
                    >
                      <CheckCircleIcon class="h-4 w-4 text-emerald-500 mr-2 mt-0.5 flex-shrink-0" />
                      <span class="text-sm text-gray-700">{{ objective }}</span>
                    </div>
                  </div>
                </div>

                <!-- Enrollment Overlay for Non-Enrolled Students -->
                <div v-if="!isEnrolled" class="absolute inset-0 bg-white/80 backdrop-blur-sm z-10 flex items-center justify-center rounded-xl">
                  <div class="text-center p-8 max-w-md">
                    <AcademicCapIcon class="h-12 w-12 text-blue-500 mx-auto mb-4" />
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Enroll to Access Content</h3>
                    <p class="text-gray-600 mb-6">
                      Enroll in this course to access all modules, topics, and learning materials.
                    </p>
                    <button
                      @click="enrollInCourse"
                      :disabled="enrollLoading"
                      :class="[
                        'px-8 py-3 rounded-lg font-semibold text-white transition-all duration-200',
                        enrollLoading
                          ? 'bg-blue-400 cursor-not-allowed'
                          : 'bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 shadow-md hover:shadow-lg'
                      ]"
                    >
                      {{ course.is_paid ? `Enroll for $${course.price}` : 'Enroll for Free' }}
                    </button>
                  </div>
                </div>

                <!-- Enhanced Course Structure (Only visible to enrolled students) -->
                <div v-if="isEnrolled">
                  <h3 class="text-sm font-semibold text-gray-700 mb-4">Course Structure</h3>
                  <div class="space-y-3">
                    <div
                      v-for="module in course.modules"
                      :key="module.id"
                      class="border border-gray-200 rounded-xl overflow-hidden hover:border-emerald-300 transition-colors"
                    >
                      <!-- Enhanced Module Header -->
                      <div
                        class="p-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 cursor-pointer"
                        @click="toggleModule(module.id)"
                      >
                        <div class="flex items-center justify-between">
                          <div class="flex items-center flex-1">
                            <div class="relative">
                              <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-semibold mr-3 bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-sm">
                                {{ module.order }}
                              </div>
                              <div
                                v-if="module.is_completed"
                                class="absolute -top-1 -right-1 w-5 h-5 bg-emerald-500 rounded-full flex items-center justify-center"
                              >
                                <CheckCircleIcon class="h-3 w-3 text-white" />
                              </div>
                            </div>
                            <div class="flex-1">
                              <div class="flex items-center gap-2 mb-1">
                                <h4 class="text-base font-bold text-gray-900">
                                  {{ module.title }}
                                </h4>
                                <span
                                  v-if="module.is_completed"
                                  class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800"
                                >
                                  Completed
                                </span>
                              </div>
                              <div class="flex items-center gap-4 text-xs text-gray-500">
                                <span>{{ module.topics?.length || 0 }} topics</span>
                                <span>{{ module.estimated_duration_minutes || 0 }}min</span>
                                <span>{{ calculateModuleCompletion(module) }}% complete</span>
                              </div>
                            </div>
                          </div>
                          <div class="flex items-center gap-3">
                            <div class="text-right">
                              <div class="text-sm font-bold text-gray-900">
                                {{ calculateModuleCompletion(module) }}%
                              </div>
                              <div class="text-xs text-gray-500">Complete</div>
                            </div>
                            <button
                              class="p-2 rounded-lg hover:bg-gray-100 transition-colors"
                            >
                              <ChevronDownIcon
                                class="h-4 w-4 text-gray-500 transition-transform duration-200"
                                :class="{ 'rotate-180': expandedModules[module.id] }"
                              />
                            </button>
                          </div>
                        </div>

                        <!-- Module Progress Bar -->
                        <div class="mt-3">
                          <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div
                              class="bg-gradient-to-r from-emerald-500 to-teal-600 h-2.5 rounded-full transition-all duration-300"
                              :style="{ width: `${calculateModuleCompletion(module)}%` }"
                            ></div>
                          </div>
                        </div>
                      </div>

                      <!-- Enhanced Module Topics -->
                      <div v-if="expandedModules[module.id] && module.topics?.length" class="bg-white">
                        <div
                          v-for="topic in module.topics"
                          :key="topic.id"
                          class="p-4 border-b border-gray-100 last:border-b-0 hover:bg-emerald-50 transition-colors group"
                        >
                          <div class="flex items-center justify-between">
                            <div class="flex items-center flex-1">
                              <div
                                :class="[
                                  'w-8 h-8 rounded-full flex items-center justify-center text-xs font-semibold mr-3 flex-shrink-0 border-2 transition-colors',
                                  topic.is_completed
                                    ? 'bg-emerald-500 border-emerald-500 text-white'
                                    : 'bg-white border-gray-300 text-gray-400 group-hover:border-emerald-300'
                                ]"
                              >
                                <CheckCircleIcon v-if="topic.is_completed" class="h-3 w-3" />
                                <span v-else>{{ topic.order }}</span>
                              </div>
                              <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                  <h5 class="text-sm font-medium text-gray-900 group-hover:text-emerald-600 transition-colors">
                                    {{ topic.title }}
                                  </h5>
                                  <span
                                    v-if="topic.has_quiz"
                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800"
                                  >
                                    Quiz
                                  </span>
                                </div>
                                <p class="text-xs text-gray-500">
                                  {{ topic.estimated_duration_minutes || 0 }}min
                                  <span v-if="topic.description" class="ml-2">• {{ topic.description }}</span>
                                </p>
                              </div>
                            </div>
                            <div class="flex items-center gap-2 ml-4">
                              <CheckCircleIcon
                                v-if="topic.is_completed"
                                class="h-5 w-5 text-emerald-600 flex-shrink-0"
                              />
                              <Link
                                v-else
                                :href="route('student.courses.learn', { course: course.id, topic: topic.id })"
                                class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition-colors shadow-sm hover:shadow-md"
                              >
                                Start
                              </Link>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Enhanced Sidebar -->
          <div class="space-y-6">
            <!-- Action Buttons -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
              <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-t-xl">
                <h2 class="text-lg font-bold text-gray-900">
                  {{ isEnrolled ? 'Quick Actions' : 'Enrollment Options' }}
                </h2>
              </div>
              <div class="p-6 space-y-3">
                <!-- Enrollment Button for Non-Enrolled Students -->
                <div v-if="!isEnrolled">
                  <button
                    @click="enrollInCourse"
                    :disabled="enrollLoading || !can_enroll"
                    :class="[
                      'w-full flex items-center justify-center px-4 py-3 font-semibold rounded-lg transition-all duration-200 shadow-sm hover:shadow-md',
                      enrollLoading || !can_enroll
                        ? 'bg-gray-400 text-white cursor-not-allowed'
                        : course.is_paid
                          ? 'bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white'
                          : 'bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white'
                    ]"
                  >
                    <AcademicCapIcon class="h-4 w-4 mr-2" />
                    {{ course.is_paid ? `Enroll for $${course.price}` : 'Enroll for Free' }}
                  </button>

                  <div v-if="!can_enroll" class="mt-3 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                    <div class="flex items-center">
                      <ExclamationTriangleIcon class="h-5 w-5 text-amber-500 mr-2" />
                      <div class="text-sm text-amber-700">
                        <span v-if="course.isFull">This course is currently full</span>
                        <span v-else-if="course.status !== 'active'">Course is not available for enrollment</span>
                        <span v-else>Enrollment is currently closed</span>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Actions for Enrolled Students -->
                <template v-else>
                  <Link
                    v-if="enrollment?.status === 'active' && next_topic"
                    :href="route('student.courses.learn', { course: course.id, topic: next_topic.id })"
                    class="w-full flex items-center justify-center px-4 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-semibold rounded-lg transition-all duration-200 shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                  >
                    <PlayIcon class="h-4 w-4 mr-2" />
                    Continue Learning
                  </Link>
                  <Link
                    v-else-if="enrollment?.status === 'active'"
                    :href="route('student.courses.learn', course.id)"
                    class="w-full flex items-center justify-center px-4 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-semibold rounded-lg transition-all duration-200 shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                  >
                    <AcademicCapIcon class="h-4 w-4 mr-2" />
                    Review Course
                  </Link>

                  <button
                    @click="openShareModal"
                    class="w-full flex items-center justify-center px-4 py-2.5 border border-emerald-300 text-sm font-semibold rounded-lg text-emerald-700 bg-white hover:bg-emerald-50 transition-colors disabled:opacity-50"
                  >
                    Share
                  </button>

                  <div class="pt-3 border-t border-gray-200">
                    <Link
                      v-if="course.capstone_project"
                      :href="route('student.capstone-projects.show', course.capstone_project.id)"
                      class="flex items-center justify-center px-3 py-2.5 border border-emerald-300 text-sm font-semibold rounded-lg text-emerald-700 bg-white hover:bg-emerald-50 transition-colors"
                    >
                      <ClipboardDocumentListIcon class="h-4 w-4 mr-2" />
                      Capstone
                    </Link>
                  </div>

                  <!-- Course Status Actions -->
                  <div class="pt-3 border-t border-gray-200">
                    <button
                      v-if="enrollment?.status === 'active'"
                      @click="pauseCourse"
                      :disabled="pauseLoading"
                      class="w-full flex items-center justify-center px-4 py-2.5 border border-emerald-300 text-sm font-semibold rounded-lg text-emerald-700 bg-white hover:bg-emerald-50 transition-colors disabled:opacity-50"
                    >
                      <PauseIcon class="h-4 w-4 mr-2" />
                      {{ pauseLoading ? 'Pausing...' : 'Pause Course' }}
                    </button>
                    <button
                      v-if="enrollment?.status === 'paused'"
                      @click="resumeCourse"
                      :disabled="resumeLoading"
                      class="w-full flex items-center justify-center px-4 py-2.5 border border-transparent text-sm font-semibold rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 transition-colors disabled:opacity-50"
                    >
                      <PlayIcon class="h-4 w-4 mr-2" />
                      {{ resumeLoading ? 'Resuming...' : 'Resume Course' }}
                    </button>
                  </div>
                </template>
              </div>
            </div>

            <!-- Course Information Sidebar -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
              <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-t-xl">
                <h2 class="text-lg font-bold text-gray-900">Course Information</h2>
              </div>
              <div class="p-6 space-y-4">
                <div>
                  <div class="text-sm text-gray-500 mb-1">Subject</div>
                  <div class="font-medium text-gray-900">{{ course.subject }}</div>
                </div>
                <div>
                  <div class="text-sm text-gray-500 mb-1">Level</div>
                  <div class="font-medium text-gray-900">{{ course.level }}</div>
                </div>
                <div v-if="course.exam_board">
                  <div class="text-sm text-gray-500 mb-1">Exam Board</div>
                  <div class="font-medium text-gray-900">{{ course.exam_board.name }}</div>
                </div>
                <div>
                  <div class="text-sm text-gray-500 mb-1">Duration</div>
                  <div class="font-medium text-gray-900">{{ course.estimated_duration_hours || 0 }} hours</div>
                </div>
                <div>
                  <div class="text-sm text-gray-500 mb-1">Enrollment</div>
                  <div class="font-medium text-gray-900">
                    {{ course.current_enrollment || 0 }} students
                    <span v-if="course.enrollment_limit">/ {{ course.enrollment_limit }} limit</span>
                  </div>
                </div>
                <div>
                  <div class="text-sm text-gray-500 mb-1">Status</div>
                  <div class="font-medium">
                    <span :class="getStatusTextClass(course.status)">{{ course.status }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Module Progress (Only for enrolled students) -->
            <div v-if="isEnrolled && progressByModule.length" class="bg-white rounded-xl shadow-sm border border-gray-100">
              <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-t-xl">
                <h2 class="text-lg font-bold text-gray-900">Module Progress</h2>
              </div>
              <div class="p-6 space-y-4">
                <div
                  v-for="moduleProgress in progressByModule"
                  :key="moduleProgress.module_id"
                  class="space-y-2 p-3 rounded-lg hover:bg-emerald-50 transition-colors cursor-pointer"
                  @click="toggleModule(moduleProgress.module_id)"
                >
                  <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-700 truncate">{{ moduleProgress.module_title }}</span>
                    <span class="text-sm font-bold" :class="getProgressColor(moduleProgress.completion_percentage)">
                      {{ Math.round(moduleProgress.completion_percentage) }}%
                    </span>
                  </div>
                  <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div
                      class="h-2.5 rounded-full transition-all duration-300"
                      :class="getProgressBarColor(moduleProgress.completion_percentage)"
                      :style="{ width: `${moduleProgress.completion_percentage}%` }"
                    ></div>
                  </div>
                  <div class="flex justify-between text-xs text-gray-500">
                    <span>{{ moduleProgress.completed_topics }}/{{ moduleProgress.total_topics }} topics</span>
                    <span>{{ formatStudyTime(moduleProgress.time_spent_minutes) }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Share Modal -->
    <div v-if="showShareModal" class="fixed inset-0 z-50 overflow-y-auto">
      <!-- Modal content remains the same -->
    </div>
  </StudentLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import {
  AcademicCapIcon,
  ChartBarIcon,
  ClockIcon,
  CalendarIcon,
  CheckCircleIcon,
  PauseIcon,
  PlayIcon,
  ChevronDownIcon,
  ClipboardDocumentListIcon,
  ExclamationTriangleIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  course: Object,
  next_topic: Object,
  course_stats: {
    type: Object,
    default: () => ({
      completed_modules: 0,
      total_modules: 0,
      completed_topics: 0,
      total_topics: 0,
      module_completion_percentage: 0,
      topic_completion_percentage: 0,
      overall_completion_percentage: 0,
      total_time_minutes: 0,
      completed_time_minutes: 0,
      estimated_total_minutes: 0,
      average_score: 0,
      estimated_remaining_time: 0,
      progress_by_module: [],
    })
  },
  is_enrolled: Boolean,
  was_dropped: Boolean,
  dropped_enrollment: Object,
  enrollment: Object,
  can_enroll: Boolean,
  lastViewedTopic: Number,
})

const enrollLoading = ref(false)
const pauseLoading = ref(false)
const resumeLoading = ref(false)
const expandedModules = ref({})
const showShareModal = ref(false)

// Computed property for enrollment status
const isEnrolled = computed(() => props.is_enrolled || false)

const canReenroll = computed(() => {
  return props.was_dropped && props.can_enroll
})

// Computed properties for safe access
const overallCompletionPercentage = computed(() => {
  return Math.round(props.course_stats?.overall_completion_percentage || 0)
})

const completedTopics = computed(() => {
  return props.course_stats?.completed_topics || 0
})

const totalTopics = computed(() => {
  return props.course_stats?.total_topics || 0
})

const completedModules = computed(() => {
  return props.course_stats?.completed_modules || 0
})

const totalModules = computed(() => {
  return props.course_stats?.total_modules || 0
})

const totalTimeMinutes = computed(() => {
  return props.course_stats?.total_time_minutes || 0
})

const estimatedRemainingTime = computed(() => {
  return props.course_stats?.estimated_remaining_time || 0
})

const averageScore = computed(() => {
  return props.course_stats?.average_score || 0
})

const progressByModule = computed(() => {
  return props.course_stats?.progress_by_module || []
})

// Initialize expanded modules
onMounted(() => {
  if (props.course.modules?.length) {
    expandedModules.value[props.course.modules[0].id] = true
  }
})

// Helper functions
const getStatusClass = (status) => {
  const classes = {
    draft: 'bg-gray-100 text-gray-800',
    active: 'bg-emerald-100 text-emerald-800',
    paused: 'bg-amber-100 text-amber-800',
    completed: 'bg-teal-100 text-teal-800',
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getStatusTextClass = (status) => {
  const classes = {
    draft: 'text-gray-600',
    active: 'text-emerald-600',
    paused: 'text-amber-600',
    completed: 'text-teal-600',
  }
  return classes[status] || 'text-gray-600'
}

const formatDate = (dateString) => {
  if (!dateString) return 'Not set'
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const formatRelativeDate = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  const now = new Date()
  const diffTime = Math.abs(now - date)
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))

  if (diffDays === 1) return 'today'
  if (diffDays === 2) return 'yesterday'
  if (diffDays < 7) return `${diffDays - 1} days ago`
  if (diffDays < 30) return `${Math.floor(diffDays / 7)} weeks ago`
  return `${Math.floor(diffDays / 30)} months ago`
}

const formatStudyTime = (minutes) => {
  if (!minutes || minutes === 0) return '0m'
  const hours = Math.floor(minutes / 60)
  const mins = Math.round(minutes % 60)
  if (hours > 0) {
    return `${hours}h ${mins}m`
  }
  return `${mins}m`
}

const getDaysRemaining = (targetDate) => {
  if (!targetDate) return ''
  const target = new Date(targetDate)
  const now = new Date()
  const diffTime = target - now
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))

  if (diffDays < 0) return `${Math.abs(diffDays)} days overdue`
  if (diffDays === 0) return 'Due today'
  if (diffDays === 1) return '1 day left'
  return `${diffDays} days left`
}

const getDaysRemainingClass = (targetDate) => {
  if (!targetDate) return 'text-gray-400'
  const target = new Date(targetDate)
  const now = new Date()
  const diffTime = target - now
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))

  if (diffDays < 0) return 'text-red-500'
  if (diffDays <= 7) return 'text-amber-500'
  return 'text-emerald-500'
}

const calculateModuleCompletion = (module) => {
  if (!isEnrolled.value) return 0

  // Use the ProgressTrackingService data if available
  if (progressByModule.value.length > 0) {
    const moduleProgress = progressByModule.value.find(
      mp => mp.module_id === module.id
    )
    if (moduleProgress) {
      return moduleProgress.completion_percentage
    }
  }

  // Fallback to topic completion checking
  const completedTopics = module.topics?.filter(topic => topic.is_completed).length || 0
  const totalTopics = module.topics?.length || 0
  return totalTopics > 0 ? Math.round((completedTopics / totalTopics) * 100) : 0
}

const getModuleNumber = (moduleId) => {
  const module = props.course.modules?.find(m => m.id === moduleId)
  return module?.order || 1
}

const getProgressColor = (percentage) => {
  if (percentage >= 90) return 'text-emerald-600'
  if (percentage >= 70) return 'text-teal-600'
  if (percentage >= 50) return 'text-amber-600'
  return 'text-red-600'
}

const getProgressBarColor = (percentage) => {
  if (percentage >= 90) return 'bg-emerald-500'
  if (percentage >= 70) return 'bg-teal-500'
  if (percentage >= 50) return 'bg-amber-500'
  return 'bg-red-500'
}

const toggleModule = (moduleId) => {
  if (isEnrolled.value) {
    expandedModules.value[moduleId] = !expandedModules.value[moduleId]
  }
}

const getTotalTopics = () => {
  return props.course.modules?.reduce((total, module) => total + (module.topics?.length || 0), 0) || 0
}

const getTotalQuizzes = () => {
  return props.course.modules?.reduce((total, module) => {
    return total + (module.topics?.filter(topic => topic.has_quiz).length || 0)
  }, 0) || 0
}

// Enrollment action
const enrollInCourse = async () => {
  if (enrollLoading.value || !props.can_enroll) return

  enrollLoading.value = true
  try {
    await router.post(route('student.courses.enroll', props.course.id), {}, {
      preserveScroll: true,
      onSuccess: () => {
        // Enrollment successful - page will reload
      },
      onFinish: () => {
        enrollLoading.value = false
      }
    })
  } catch (error) {
    console.error('Enrollment failed:', error)
    enrollLoading.value = false
  }
}

// Other actions remain the same
const pauseCourse = async () => {
  if (!isEnrolled.value) return
  pauseLoading.value = true
  try {
    await router.post(route('student.courses.pause', props.course.id))
  } finally {
    pauseLoading.value = false
  }
}

const resumeCourse = async () => {
  if (!isEnrolled.value) return
  resumeLoading.value = true
  try {
    await router.post(route('student.courses.resume', props.course.id))
  } finally {
    resumeLoading.value = false
  }
}

const openShareModal = () => {
  if (isEnrolled.value) {
    showShareModal.value = true
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

/* Smooth transitions for all interactive elements */
* {
  transition-property: color, background-color, border-color, transform, box-shadow;
  transition-duration: 200ms;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}
</style>
