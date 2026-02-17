<template>
    <StudentLayout>
      <div class="min-h-screen bg-gradient-to-br from-slate-50 to-white">
        <Head title="Student Dashboard" />

        <div class="py-6">
          <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header with Welcome and Quick Actions -->
            <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between">
              <div>
                <h1 class="text-3xl font-bold text-gray-900">
                  Welcome back, {{ $page.props.auth.user.name }}! 👋
                </h1>
                <p class="mt-2 text-gray-600">
                  {{ getGreeting() }} Ready to continue your learning journey?
                </p>
              </div>

              <!-- Quick Actions -->
              <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                <Link
                  :href="route('student.catalog.browse')"
                  class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-colors shadow-sm"
                >
                  <MagnifyingGlassIcon class="h-4 w-4 mr-2" />
                  Browse Courses
                </Link>
                <Link
                  :href="route('student.catalog.browse')"
                  class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-emerald-700 transition-colors shadow-sm"
                >
                  <CalendarIcon class="h-4 w-4 mr-2" />
                  View Schedule
                </Link>
              </div>
            </div>

            <!-- Subscription Status - Subtle Chip -->
            <div v-if="current_subscription" class="mb-6">
              <div class="inline-flex items-center px-3 py-1.5 bg-emerald-50 border border-emerald-200 rounded-full">
                <SparklesIcon class="h-4 w-4 text-emerald-600 mr-2" />
                <span class="text-sm text-emerald-700 font-medium">
                  {{ current_subscription.plan?.name || 'Active' }} Plan
                </span>
                <span class="mx-2 text-emerald-300">•</span>
                <span class="text-sm text-emerald-600">
                  Renews {{ formatDate(current_subscription.ends_at) }}
                </span>
                <Link
                  :href="route('payment.pricing')"
                  class="ml-3 text-xs text-emerald-700 hover:text-emerald-800 font-semibold underline underline-offset-2"
                >
                  Manage
                </Link>
              </div>
            </div>

            <!-- Recently Dropped Courses Banner -->
            <div v-if="recently_dropped_courses?.length > 0" class="mb-8">
              <div class="bg-amber-50 rounded-xl border border-amber-200 p-4">
                <div class="flex items-center justify-between">
                  <div class="flex items-center">
                    <div class="p-2 bg-amber-100 rounded-lg mr-3">
                      <ArrowPathIcon class="h-5 w-5 text-amber-600" />
                    </div>
                    <div>
                      <h3 class="text-sm font-semibold text-gray-900">
                        Ready to re-enroll?
                      </h3>
                      <p class="text-xs text-amber-700">
                        You dropped {{ recently_dropped_courses.length }} course{{ recently_dropped_courses.length > 1 ? 's' : '' }} recently
                      </p>
                    </div>
                  </div>
                  <button
                    @click="showAllDroppedCourses"
                    class="text-xs text-amber-700 hover:text-amber-800 font-medium"
                  >
                    View all
                  </button>
                </div>

                <!-- Dropped Courses Pills -->
                <div class="mt-3 flex flex-wrap gap-2">
                  <div
                    v-for="course in recently_dropped_courses.slice(0, 3)"
                    :key="course.id"
                    class="inline-flex items-center bg-white border border-amber-200 rounded-full px-3 py-1.5 shadow-sm"
                  >
                    <BookOpenIcon class="h-3.5 w-3.5 text-amber-600 mr-1.5" />
                    <span class="text-xs font-medium text-gray-700">{{ course.title }}</span>
                    <span class="mx-1.5 text-gray-300">•</span>
                    <span class="text-xs text-gray-500">{{ course.dropped_at }}</span>
                    <button
                      v-if="course.can_reenroll"
                      @click="reenrollCourse(course)"
                      :disabled="reenrollLoading[course.id]"
                      class="ml-2 text-xs text-emerald-600 hover:text-emerald-700 font-medium disabled:opacity-50"
                    >
                      {{ reenrollLoading[course.id] ? '...' : 'Re-enroll' }}
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Stats Grid - Enhanced -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
              <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition-all hover:scale-[1.02] group">
                <div class="flex items-center">
                  <div class="p-3 bg-blue-50 rounded-xl group-hover:bg-blue-100 transition-colors">
                    <BookOpenIcon class="h-6 w-6 text-blue-600" />
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Active Courses</p>
                    <p class="text-2xl font-bold text-gray-900">{{ stats.active_courses }}</p>
                  </div>
                </div>
                <div class="mt-2 text-xs text-gray-500">
                  {{ stats.completed_courses }} completed
                </div>
              </div>

              <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition-all hover:scale-[1.02] group">
                <div class="flex items-center">
                  <div class="p-3 bg-green-50 rounded-xl group-hover:bg-green-100 transition-colors">
                    <ChartPieIcon class="h-6 w-6 text-green-600" />
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Completion Rate</p>
                    <p class="text-2xl font-bold text-gray-900">{{ stats.completion_rate }}%</p>
                  </div>
                </div>
                <div class="mt-2 text-xs text-gray-500">
                  Across {{ stats.total_courses }} courses
                </div>
              </div>

              <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition-all hover:scale-[1.02] group">
                <div class="flex items-center">
                  <div class="p-3 bg-purple-50 rounded-xl group-hover:bg-purple-100 transition-colors">
                    <ClockIcon class="h-6 w-6 text-purple-600" />
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Study Time</p>
                    <p class="text-2xl font-bold text-gray-900">{{ stats.total_study_time }}h</p>
                  </div>
                </div>
                <div class="mt-2 text-xs text-gray-500">
                  Total hours learned
                </div>
              </div>

              <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition-all hover:scale-[1.02] group">
                <div class="flex items-center">
                  <div class="p-3 bg-yellow-50 rounded-xl group-hover:bg-yellow-100 transition-colors">
                    <AcademicCapIcon class="h-6 w-6 text-yellow-600" />
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Avg Quiz Score</p>
                    <p class="text-2xl font-bold text-gray-900">{{ stats.average_quiz_score }}%</p>
                  </div>
                </div>
                <div class="mt-2 text-xs text-gray-500">
                      Last 30 days
                </div>
              </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
              <!-- Left Column - Main Content -->
              <div class="lg:col-span-2 space-y-8">
                <!-- Active Course - Enhanced -->
                <div v-if="active_course" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                  <div class="px-6 py-4 bg-gradient-to-r from-emerald-50 to-teal-50 border-b border-emerald-100">
                    <div class="flex items-center justify-between">
                      <h2 class="text-lg font-bold text-gray-900 flex items-center">
                        <PlayCircleIcon class="h-5 w-5 text-emerald-600 mr-2" />
                        Continue Learning
                      </h2>
                      <Link
                        :href="route('student.courses.learn', active_course.id)"
                        class="text-sm text-emerald-600 hover:text-emerald-700 font-medium flex items-center"
                      >
                        Go to course
                        <ChevronRightIcon class="h-4 w-4 ml-1" />
                      </Link>
                    </div>
                  </div>

                  <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between">
                      <div class="flex-1">
                        <h3 class="text-xl font-bold text-gray-900 mb-1">
                          {{ active_course.title }}
                        </h3>
                        <div class="flex items-center text-sm text-gray-600 mb-4">
                          <span class="bg-gray-100 px-2 py-1 rounded-full text-xs">
                            {{ active_course.subject }}
                          </span>
                          <span class="mx-2 text-gray-300">•</span>
                          <span>{{ active_course.progress?.completed_modules || 0 }}/{{ active_course.progress?.total_modules || 0 }} modules</span>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mb-4">
                          <div class="flex items-center justify-between text-sm mb-1">
                            <span class="font-medium text-gray-700">Overall Progress</span>
                            <span class="text-emerald-600 font-semibold">{{ parseFloat(active_course.progress?.overall_completion_percentage || 0).toFixed(2) }}%</span>
                          </div>
                          <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div
                              class="bg-emerald-600 h-2.5 rounded-full transition-all duration-500"
                              :style="{ width: `${parseFloat(active_course.progress?.overall_completion_percentage || 0).toFixed(2)}%` }"
                            ></div>
                          </div>
                        </div>

                        <!-- Module Progress -->
                        <div v-if="active_course.progress?.module_completion_percentage" class="grid grid-cols-3 gap-2 mb-4">
                          <div v-for="(module, index) in active_course.progress.module_completion_percentage.slice(0, 3)" :key="index" class="text-center">
                            <div class="text-xs font-medium text-gray-600 mb-1">Module {{ index + 1 }}</div>
                            <ProgressRing :progress="module" size="sm" />
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Next Topic -->
                    <div v-if="active_course.next_topic" class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                      <div class="flex items-center justify-between">
                        <div class="flex items-center">
                          <div class="p-2 bg-white rounded-lg border border-gray-200 mr-3">
                            <PlayIcon class="h-4 w-4 text-emerald-600" />
                          </div>
                          <div>
                            <p class="text-xs text-gray-500 mb-0.5">Next up</p>
                            <p class="text-sm font-semibold text-gray-900">{{ active_course.next_topic.title }}</p>
                            <p class="text-xs text-gray-500">{{ active_course.next_topic.module_title }} • {{ active_course.next_topic.estimated_duration_minutes }} min</p>
                          </div>
                        </div>
                        <Link
                          :href="route('student.courses.learn', { course: active_course.id, topic: active_course.next_topic.id })"
                          class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors shadow-sm"
                        >
                          Start
                          <ArrowRightIcon class="h-4 w-4 ml-1" />
                        </Link>
                      </div>
                    </div>

                    <!-- Upcoming Topics -->
                    <div v-if="active_course.upcoming_topics?.length > 0" class="mt-4">
                      <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Coming up next</p>
                      <div class="space-y-2">
                        <div v-for="topic in active_course.upcoming_topics.slice(0, 3)" :key="topic.id" class="flex items-center text-sm">
                          <div class="w-1 h-1 bg-gray-300 rounded-full mr-2"></div>
                          <span class="text-gray-600">{{ topic.title }}</span>
                          <span class="mx-2 text-gray-300">•</span>
                          <span class="text-xs text-gray-500">{{ topic.estimated_duration_minutes }} min</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Recent Activity - Enhanced -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                  <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center">
                      <ClockIcon class="h-5 w-5 text-gray-600 mr-2" />
                      Recent Activity
                    </h2>
                  </div>
                  <div class="divide-y divide-gray-100">
                    <div
                      v-for="activity in recent_activity"
                      :key="activity.id"
                      class="px-6 py-4 hover:bg-gray-50 transition-colors"
                    >
                      <div class="flex items-center">
                        <div class="flex-shrink-0">
                          <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                            <DocumentTextIcon class="h-5 w-5 text-emerald-600" />
                          </div>
                        </div>
                        <div class="ml-4 flex-1">
                          <div class="flex items-center justify-between">
                            <p class="text-sm font-semibold text-gray-900">
                              {{ activity.topic_title }}
                            </p>
                            <span class="text-xs text-gray-500">{{ activity.created_at }}</span>
                          </div>
                          <div class="flex items-center mt-1">
                            <span class="text-xs text-gray-600">{{ activity.course_title }}</span>
                            <span class="mx-1.5 text-gray-300">•</span>
                            <span class="text-xs text-gray-600">{{ activity.module_title }}</span>
                            <span v-if="activity.score" class="ml-2 px-1.5 py-0.5 bg-green-100 text-green-700 text-xs rounded-full">
                              Score: {{ activity.score }}%
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div v-if="recent_activity.length === 0" class="px-6 py-12 text-center">
                      <DocumentTextIcon class="h-12 w-12 mx-auto text-gray-300 mb-3" />
                      <p class="text-gray-500 text-sm">No recent activity yet</p>
                      <p class="text-xs text-gray-400 mt-1">Start learning to see your progress here</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Right Column - Sidebar -->
              <div class="space-y-8">
                <!-- Learning Stats Card -->
                <LearningStats :analytics="learning_analytics" />

                <!-- Upcoming Deadlines - Redesigned -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                  <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center">
                      <CalendarIcon class="h-5 w-5 text-gray-600 mr-2" />
                      Upcoming Deadlines
                    </h2>
                  </div>

                  <div class="p-6">
                    <!-- Course Deadlines -->
                    <div v-if="upcoming_deadlines.courses?.length > 0" class="space-y-3">
                      <div
                        v-for="course in upcoming_deadlines.courses"
                        :key="course.id"
                        class="relative"
                      >
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg border border-gray-200 hover:border-emerald-300 transition-colors">
                          <div class="flex-1">
                            <div class="flex items-center justify-between mb-1">
                              <p class="text-sm font-medium text-gray-900">{{ course.title }}</p>
                              <span
                                class="text-xs px-2 py-0.5 rounded-full"
                                :class="course.is_urgent ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600'"
                              >
                                {{ course.days_remaining }}d left
                              </span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                              <span class="text-gray-500">Due {{ course.deadline }}</span>
                              <span class="text-gray-700 font-medium">{{ course.progress_percentage }}% complete</span>
                            </div>
                            <div class="mt-2 w-full bg-gray-200 rounded-full h-1.5">
                              <div
                                class="bg-emerald-600 h-1.5 rounded-full"
                                :style="{ width: `${course.progress_percentage}%` }"
                              ></div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Quiz Deadlines -->
                    <div v-if="upcoming_deadlines.quizzes?.length > 0" class="mt-4 space-y-3">
                      <div class="relative">
                        <span class="absolute -top-2 left-3 bg-white px-2 text-xs font-medium text-gray-500">Quizzes</span>
                        <div class="border border-gray-200 rounded-lg divide-y divide-gray-200 mt-3">
                          <div
                            v-for="quiz in upcoming_deadlines.quizzes.slice(0, 2)"
                            :key="quiz.id"
                            class="p-3 hover:bg-gray-50"
                          >
                            <div class="flex items-center justify-between">
                              <div>
                                <p class="text-sm font-medium text-gray-900">{{ quiz.quiz_title }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ quiz.course_title }}</p>
                              </div>
                              <Link
                                :href="route('student.quizzes.start', quiz.id)"
                                class="text-xs text-emerald-600 hover:text-emerald-700 font-medium"
                              >
                                Start
                              </Link>
                            </div>
                            <div class="mt-2 flex items-center text-xs text-gray-500">
                              <ClockIcon class="h-3 w-3 mr-1" />
                              Due {{ quiz.due_at }}
                              <span v-if="quiz.is_overdue" class="ml-2 text-red-600">Overdue</span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div
                      v-if="!upcoming_deadlines.courses?.length && !upcoming_deadlines.quizzes?.length"
                      class="text-center py-8"
                    >
                      <CalendarIcon class="h-12 w-12 mx-auto text-gray-300 mb-3" />
                      <p class="text-sm text-gray-500">No upcoming deadlines</p>
                      <p class="text-xs text-gray-400 mt-1">Take a break or start a new course</p>
                    </div>
                  </div>
                </div>

                <!-- Recommended Next Steps -->
                <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl p-6 border border-emerald-100">
                  <h3 class="text-sm font-semibold text-gray-900 mb-3 flex items-center">
                    <LightBulbIcon class="h-4 w-4 text-emerald-600 mr-2" />
                    Recommended Next Steps
                  </h3>
                  <div class="space-y-3">
                    <div v-if="!active_course" class="flex items-start">
                      <div class="flex-shrink-0 w-5 h-5 bg-emerald-200 rounded-full flex items-center justify-center mt-0.5">
                        <span class="text-xs font-bold text-emerald-700">1</span>
                      </div>
                      <p class="ml-3 text-sm text-gray-700">Browse courses and start learning</p>
                    </div>
                    <div v-if="active_course?.next_topic" class="flex items-start">
                      <div class="flex-shrink-0 w-5 h-5 bg-emerald-200 rounded-full flex items-center justify-center mt-0.5">
                        <span class="text-xs font-bold text-emerald-700">2</span>
                      </div>
                      <p class="ml-3 text-sm text-gray-700">Complete your next topic: "{{ active_course.next_topic.title }}"</p>
                    </div>
                    <div v-if="upcoming_deadlines.quizzes?.length" class="flex items-start">
                      <div class="flex-shrink-0 w-5 h-5 bg-emerald-200 rounded-full flex items-center justify-center mt-0.5">
                        <span class="text-xs font-bold text-emerald-700">3</span>
                      </div>
                      <p class="ml-3 text-sm text-gray-700">Take pending quizzes before they expire</p>
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
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { Head, Link } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'
import {
  BookOpenIcon,
  CheckCircleIcon,
  ClockIcon,
  AcademicCapIcon,
  ChartPieIcon,
  CalendarIcon,
  DocumentTextIcon,
  PlayIcon,
  ArrowRightIcon,
  ChevronRightIcon,
  MagnifyingGlassIcon,
  SparklesIcon,
  LightBulbIcon,
  PlayCircleIcon,
  ArrowPathIcon,
} from '@heroicons/vue/24/outline'
import ProgressRing from '@/Components/Student/ProgressRing.vue'
import LearningStats from '@/Components/Student/LearningStats.vue'

const props = defineProps({
  stats: {
    type: Object,
    default: () => ({
      total_courses: 0,
      completed_courses: 0,
      active_courses: 0,
      total_study_time: 0,
      average_quiz_score: 0,
      completion_rate: 0
    })
  },
  active_course: {
    type: Object,
    default: null
  },
  recent_activity: {
    type: Array,
    default: () => []
  },
  upcoming_deadlines: {
    type: Object,
    default: () => ({ courses: [], quizzes: [] })
  },
  learning_analytics: {
    type: Object,
    default: () => ({})
  },
  current_subscription: {
    type: Object,
    default: null
  },
  recently_dropped_courses: {
    type: Array,
    default: () => []
  }
})

const reenrollLoading = ref({})

const getGreeting = () => {
  const hour = new Date().getHours()
  if (hour < 12) return 'Good morning!'
  if (hour < 18) return 'Good afternoon!'
  return 'Good evening!'
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric'
  })
}

const reenrollCourse = async (course) => {
  if (reenrollLoading.value[course.id]) return

  reenrollLoading.value[course.id] = true

  try {
    await router.post(route('student.courses.enroll', course.id), {}, {
      preserveScroll: true,
      onSuccess: () => {
        const index = props.recently_dropped_courses.findIndex(c => c.id === course.id)
        if (index !== -1) {
          props.recently_dropped_courses.splice(index, 1)
        }
      },
      onFinish: () => {
        reenrollLoading.value[course.id] = false
      }
    })
  } catch (error) {
    console.error('Error re-enrolling in course:', error)
    reenrollLoading.value[course.id] = false
  }
}

const showAllDroppedCourses = () => {
  router.visit(route('student.courses.index', { status: 'dropped' }))
}
</script>
