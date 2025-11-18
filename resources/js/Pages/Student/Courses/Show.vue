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
                <span
                  :class="[
                    'inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold',
                    getStatusClass(course.status)
                  ]"
                >
                  {{ course.status }}
                </span>
                <span
                  v-if="course.exam_board"
                  class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-emerald-100 text-emerald-800"
                >
                  {{ course.exam_board.name }}
                </span>
                <span class="text-sm text-gray-500">
                  Started {{ formatRelativeDate(course.start_date) }}
                </span>
              </div>
              <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ course.title }}</h1>
              <p class="text-lg text-gray-600 mb-4">
                {{ course.subject }} • {{ course.level }}
              </p>

              <!-- Enhanced Progress Overview -->
              <div class="bg-gradient-to-r from-emerald-50 to-teal-50 rounded-xl p-6 mb-4 border border-emerald-100">
                <div class="flex items-center justify-between mb-4">
                  <div>
                    <h3 class="text-lg font-bold text-gray-900">Course Progress</h3>
                    <p class="text-sm text-gray-600">Your journey to mastery</p>
                  </div>
                  <div class="text-right">
                    <div class="text-3xl font-bold text-emerald-600">
                      {{ Math.round(courseStats.overall_completion_percentage || 0) }}%
                    </div>
                    <div class="text-sm text-gray-500">Complete</div>
                  </div>
                </div>

                <!-- Progress Bars -->
                <div class="space-y-3">
                  <div>
                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                      <span>Overall Progress</span>
                      <span class="font-semibold">{{ Math.round(courseStats.overall_completion_percentage || 0) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                      <div
                        class="bg-gradient-to-r from-emerald-500 to-teal-600 h-3 rounded-full transition-all duration-500 shadow-sm"
                        :style="{ width: `${courseStats.overall_completion_percentage || 0}%` }"
                      ></div>
                    </div>
                  </div>

                  <div class="grid grid-cols-2 gap-4 text-xs">
                    <div class="text-center">
                      <div class="font-bold text-gray-900">{{ courseStats.completed_modules || 0 }}/{{ courseStats.total_modules || 0 }}</div>
                      <div class="text-gray-500">Modules</div>
                    </div>
                    <div class="text-center">
                      <div class="font-bold text-gray-900">{{ courseStats.completed_topics || 0 }}/{{ courseStats.total_topics || 0 }}</div>
                      <div class="text-gray-500">Topics</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Enhanced Course Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <!-- Progress Card -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center">
                <ChartBarIcon class="h-6 w-6 text-emerald-600" />
              </div>
              <div class="text-right">
                <div class="text-2xl font-bold text-gray-900">{{ Math.round(courseStats.overall_completion_percentage || 0) }}%</div>
                <div class="text-sm text-gray-500">Complete</div>
              </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2.5">
              <div
                class="bg-emerald-600 h-2.5 rounded-full transition-all duration-300"
                :style="{ width: `${courseStats.overall_completion_percentage || 0}%` }"
              ></div>
            </div>
            <p class="text-xs text-gray-500 mt-2">
              {{ courseStats.completed_topics || 0 }} of {{ courseStats.total_topics || 0 }} topics
            </p>
          </div>

          <!-- Time Spent Card -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 rounded-xl bg-teal-100 flex items-center justify-center">
                <ClockIcon class="h-6 w-6 text-teal-600" />
              </div>
              <div class="text-right">
                <div class="text-2xl font-bold text-gray-900">{{ formatStudyTime(courseStats.total_time_minutes || 0) }}</div>
                <div class="text-sm text-gray-500">Time Spent</div>
              </div>
            </div>
            <div class="flex justify-between text-xs text-gray-500">
              <span>Est: {{ course.estimated_duration_hours || 0 }}h</span>
              <span>Remaining: {{ formatStudyTime(courseStats.estimated_remaining_time || 0) }}</span>
            </div>
          </div>

          <!-- Performance Card -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center">
                <AcademicCapIcon class="h-6 w-6 text-purple-600" />
              </div>
              <div class="text-right">
                <div class="text-2xl font-bold text-gray-900">{{ courseStats.average_score || 0 }}%</div>
                <div class="text-sm text-gray-500">Avg Score</div>
              </div>
            </div>
            <div class="text-xs text-gray-500">
              Based on {{ courseStats.completed_topics || 0 }} completed topics
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

        <!-- Completion Timeline -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
          <h3 class="text-lg font-bold text-gray-900 mb-4">Completion Timeline</h3>
          <div class="flex items-center justify-between mb-4">
            <div class="text-sm text-gray-600">
              <div>Start: {{ formatDate(course.start_date) }}</div>
              <div class="text-xs text-gray-400">Started {{ formatRelativeDate(course.start_date) }}</div>
            </div>
            <div class="flex-1 mx-4">
              <div class="relative">
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                  <div
                    class="bg-gradient-to-r from-emerald-500 to-teal-500 h-2.5 rounded-full transition-all duration-500"
                    :style="{ width: `${courseStats.overall_completion_percentage || 0}%` }"
                  ></div>
                </div>
                <div
                  class="absolute top-0 w-3 h-3 bg-emerald-600 rounded-full border-2 border-white shadow-lg -mt-0.5 transition-all duration-500"
                  :style="{ left: `${courseStats.overall_completion_percentage || 0}%` }"
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
          <div class="lg:col-span-2">
            <div class="bg-white shadow-sm rounded-xl border border-gray-100">
              <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-t-xl">
                <div class="flex items-center justify-between">
                  <div>
                    <h2 class="text-lg font-bold text-gray-900">Course Content</h2>
                    <p class="text-sm text-gray-500 mt-1">
                      {{ courseStats.total_modules || 0 }} modules • {{ courseStats.total_topics || 0 }} topics
                    </p>
                  </div>
                  <div class="text-right">
                    <div class="text-sm text-gray-600">
                      {{ courseStats.completed_topics || 0 }}/{{ courseStats.total_topics || 0 }} completed
                    </div>
                    <div class="text-xs text-gray-400">
                      {{ Math.round(courseStats.topic_completion_percentage || 0) }}% complete
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

                <!-- Enhanced Course Structure -->
                <div>
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
                <h2 class="text-lg font-bold text-gray-900">Quick Actions</h2>
              </div>
              <div class="p-6 space-y-3">
                <Link
                  v-if="course.status === 'active' && next_topic"
                  :href="route('student.courses.learn', { course: course.id, topic: next_topic.id })"
                  class="w-full flex items-center justify-center px-4 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-semibold rounded-lg transition-all duration-200 shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                >
                  <PlayIcon class="h-4 w-4 mr-2" />
                  Continue Learning
                </Link>
                <Link
                  v-else-if="course.status === 'active'"
                  :href="route('student.courses.learn', course.id)"
                  class="w-full flex items-center justify-center px-4 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-semibold rounded-lg transition-all duration-200 shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                >
                  <AcademicCapIcon class="h-4 w-4 mr-2" />
                  Review Course
                </Link>

                <div class="grid grid-cols-2 gap-3">
                  <Link
                    :href="course.chat_sessions?.length > 0
                        ? route('student.chat.show', { chatSession: course.chat_sessions[0].id })
                        : route('student.chat.create', { course: course.id })"
                    :method="course.chat_sessions?.length > 0 ? 'get' : 'post'"
                    as="button"
                    class="flex items-center justify-center px-3 py-2.5 border border-emerald-300 text-sm font-semibold rounded-lg text-emerald-700 bg-white hover:bg-emerald-50 transition-colors"
                    >
                    <ChatBubbleLeftRightIcon class="h-4 w-4 mr-2" />
                    {{ course.chat_sessions?.length > 0 ? 'AI Tutor' : 'Start Chat' }}
                    </Link>
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
                    v-if="course.status === 'active'"
                    @click="pauseCourse"
                    :disabled="pauseLoading"
                    class="w-full flex items-center justify-center px-4 py-2.5 border border-emerald-300 text-sm font-semibold rounded-lg text-emerald-700 bg-white hover:bg-emerald-50 transition-colors disabled:opacity-50"
                  >
                    <PauseIcon class="h-4 w-4 mr-2" />
                    {{ pauseLoading ? 'Pausing...' : 'Pause Course' }}
                  </button>
                  <button
                    v-if="course.status === 'paused'"
                    @click="resumeCourse"
                    :disabled="resumeLoading"
                    class="w-full flex items-center justify-center px-4 py-2.5 border border-transparent text-sm font-semibold rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 transition-colors disabled:opacity-50"
                  >
                    <PlayIcon class="h-4 w-4 mr-2" />
                    {{ resumeLoading ? 'Resuming...' : 'Resume Course' }}
                  </button>
                </div>
              </div>
            </div>

            <!-- Enhanced Module Progress -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100" v-if="courseStats.progress_by_module?.length">
              <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-t-xl">
                <h2 class="text-lg font-bold text-gray-900">Module Progress</h2>
              </div>
              <div class="p-6 space-y-4">
                <div
                  v-for="moduleProgress in courseStats.progress_by_module"
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

            <!-- Course Timeline -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
              <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-t-xl">
                <h2 class="text-lg font-bold text-gray-900">Timeline</h2>
              </div>
              <div class="p-6 space-y-4">
                <div class="flex justify-between items-center">
                  <div class="text-sm">
                    <div class="font-medium text-gray-900">Start Date</div>
                    <div class="text-gray-500">{{ formatDate(course.start_date) }}</div>
                  </div>
                  <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center">
                    <PlayIcon class="h-4 w-4 text-emerald-600" />
                  </div>
                </div>

                <div class="flex justify-between items-center">
                  <div class="text-sm">
                    <div class="font-medium text-gray-900">Target Completion</div>
                    <div class="text-gray-500">{{ formatDate(course.target_completion_date) }}</div>
                  </div>
                  <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center">
                    <CalendarIcon class="h-4 w-4 text-amber-600" />
                  </div>
                </div>

                <div v-if="course.actual_completion_date" class="flex justify-between items-center">
                  <div class="text-sm">
                    <div class="font-medium text-gray-900">Completed On</div>
                    <div class="text-gray-500">{{ formatDate(course.actual_completion_date) }}</div>
                  </div>
                  <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center">
                    <CheckCircleIcon class="h-4 w-4 text-emerald-600" />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
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
  ChatBubbleLeftRightIcon,
  ClipboardDocumentListIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  course: Object,
  next_topic: Object,
  course_stats: Object,
})

const pauseLoading = ref(false)
const resumeLoading = ref(false)
const expandedModules = ref({})

// Initialize expanded modules - expand first module by default
onMounted(() => {
  if (props.course.modules?.length) {
    expandedModules.value[props.course.modules[0].id] = true
  }
})

// Use computed property for consistent naming
const courseStats = computed(() => props.course_stats || {})

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
  expandedModules.value[moduleId] = !expandedModules.value[moduleId]
}

const pauseCourse = async () => {
  pauseLoading.value = true
  try {
    await router.put(route('student.courses.pause', props.course.id))
  } finally {
    pauseLoading.value = false
  }
}

const resumeCourse = async () => {
  resumeLoading.value = true
  try {
    await router.put(route('student.courses.resume', props.course.id))
  } finally {
    resumeLoading.value = false
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
