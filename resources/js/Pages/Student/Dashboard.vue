<template>
    <StudentLayout>
      <div class="min-h-screen bg-gradient-to-br from-slate-50 to-white">
        <Head title="Student Dashboard" />

        <div class="py-6">
          <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
              <h1 class="text-3xl font-bold text-gray-900">
                Welcome back, {{ $page.props.auth.user.name }}!
              </h1>
              <p class="mt-2 text-gray-600">
                Continue your learning journey and track your progress.
              </p>
            </div>

            <!-- Current Subscription Banner -->
            <div v-if="current_subscription" class="mb-8">
              <div class="bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                  <div>
                    <h3 class="text-lg font-bold mb-1">
                      {{ current_subscription.plan?.name || 'Current Plan' }}
                    </h3>
                    <p class="text-emerald-100 text-sm">
                      Status: <span class="font-semibold capitalize">{{ current_subscription.status }}</span> •
                      Renews: {{ formatDate(current_subscription.ends_at) }}
                    </p>
                  </div>
                  <div class="text-right">
                    <p class="text-2xl font-bold">
                      {{ current_subscription.plan?.price ? `${current_subscription.plan.currency} ${current_subscription.plan.price}` : 'Free' }}
                    </p>
                    <p class="text-emerald-100 text-sm">
                      per {{ getBillingPeriod(current_subscription.plan) }}
                    </p>
                  </div>
                </div>
                <div class="mt-4 flex items-center space-x-4">
                  <Link
                    :href="route('payment.pricing')"
                    class="bg-white text-emerald-600 hover:bg-emerald-50 px-4 py-2 rounded-lg font-semibold text-sm transition-colors"
                  >
                    Change Plan
                  </Link>
                  <Link
                    :href="route('payment.history')"
                    class="text-white hover:text-emerald-100 font-medium text-sm transition-colors"
                  >
                    View Billing
                  </Link>
                </div>
              </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
              <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                  <div class="p-3 bg-blue-100 rounded-xl">
                    <BookOpenIcon class="h-6 w-6 text-blue-600" />
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Active Courses</p>
                    <p class="text-2xl font-bold text-gray-900">
                      {{ stats.active_courses }}
                    </p>
                  </div>
                </div>
              </div>

              <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                  <div class="p-3 bg-green-100 rounded-xl">
                    <CheckCircleIcon class="h-6 w-6 text-green-600" />
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Completion Rate</p>
                    <p class="text-2xl font-bold text-gray-900">
                      {{ stats.completion_rate }}%
                    </p>
                  </div>
                </div>
              </div>

              <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                  <div class="p-3 bg-purple-100 rounded-xl">
                    <ClockIcon class="h-6 w-6 text-purple-600" />
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Study Time</p>
                    <p class="text-2xl font-bold text-gray-900">
                      {{ stats.total_study_time }}h
                    </p>
                  </div>
                </div>
              </div>

              <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                  <div class="p-3 bg-yellow-100 rounded-xl">
                    <AcademicCapIcon class="h-6 w-6 text-yellow-600" />
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Avg Quiz Score</p>
                    <p class="text-2xl font-bold text-gray-900">
                      {{ stats.average_quiz_score }}%
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
              <!-- Left Column -->
              <div class="lg:col-span-2 space-y-8">
                <!-- Active Course -->
                <div v-if="active_course" class="bg-white rounded-xl shadow-sm border border-gray-100">
                  <div class="px-6 py-4 border-b border-gray-200/70">
                    <h2 class="text-lg font-bold text-gray-900">
                      Continue Learning
                    </h2>
                  </div>
                  <div class="p-6">
                    <div class="flex items-start justify-between">
                      <div>
                        <h3 class="text-xl font-bold text-gray-900">
                          {{ active_course.title }}
                        </h3>
                        <p class="mt-1 text-gray-600">
                          {{ active_course.subject }}
                        </p>
                        <div class="mt-4 flex items-center">
                          <ProgressRing
                            :progress="active_course.progress?.overall_completion_percentage || 0"
                            size="md"
                          />
                          <span class="ml-3 text-sm text-gray-600">
                            {{ active_course.progress?.completed_modules || 0 }} of
                            {{ active_course.progress?.total_modules || 0 }} modules completed
                          </span>
                        </div>
                      </div>
                      <Link
                        :href="route('student.courses.learn', active_course.id)"
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 border border-transparent rounded-lg font-semibold text-white shadow-md hover:shadow-lg transition-all duration-200 hover:scale-[1.02]"
                      >
                        Continue
                      </Link>
                    </div>

                    <!-- Next Topics -->
                    <div v-if="active_course.next_topic" class="mt-6">
                      <h4 class="text-sm font-semibold text-gray-900 mb-3">
                        Next Topic
                      </h4>
                      <div class="flex items-center p-3 bg-gray-50 rounded-lg border border-gray-100">
                        <div
                          class="flex-shrink-0 w-8 h-8 bg-white border border-gray-300 rounded-full flex items-center justify-center"
                        >
                          <BookOpenIcon class="h-4 w-4 text-gray-700" />
                        </div>
                        <div class="ml-3 flex-1">
                          <p class="text-sm font-medium text-gray-900">
                            {{ active_course.next_topic.title }}
                          </p>
                          <p class="text-xs text-gray-500">
                            {{ active_course.next_topic.module_title }} •
                            {{ active_course.next_topic.estimated_duration_minutes }} min
                          </p>
                        </div>
                        <Link
                          :href="route('student.courses.learn', { course: active_course.id, topic: active_course.next_topic.id })"
                          class="text-xs text-emerald-600 hover:text-emerald-800 font-semibold"
                        >
                          Start
                        </Link>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                  <div class="px-6 py-4 border-b border-gray-200/70">
                    <h2 class="text-lg font-bold text-gray-900">
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
                        <div
                          class="flex-shrink-0 w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center"
                        >
                          <BookOpenIcon class="h-5 w-5 text-emerald-600" />
                        </div>
                        <div class="ml-4 flex-1">
                          <p class="text-sm font-semibold text-gray-900">
                            {{ activity.topic_title }}
                          </p>
                          <p class="text-sm text-gray-500">
                            {{ activity.course_title }} •
                            {{ activity.module_title }} •
                            {{ activity.time_spent }} minutes
                          </p>
                        </div>
                        <div class="text-sm text-gray-500">
                          {{ activity.created_at }}
                        </div>
                      </div>
                    </div>
                    <div v-if="recent_activity.length === 0" class="px-6 py-8 text-center">
                      <p class="text-gray-500">No recent activity</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Right Column -->
              <div class="space-y-8">
                <!-- Learning Analytics -->
                <LearningStats :analytics="learning_analytics" />

                <!-- Upcoming Deadlines -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                  <div class="px-6 py-4 border-b border-gray-200/70">
                    <h2 class="text-lg font-bold text-gray-900">
                      Upcoming Deadlines
                    </h2>
                  </div>
                  <div class="p-6">
                    <div class="space-y-4">
                      <!-- Course Deadlines -->
                      <div
                        v-for="course in upcoming_deadlines.courses"
                        :key="course.id"
                        class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:border-emerald-300 transition-colors"
                        :class="{
                          'border-red-200 bg-red-50': course.is_urgent,
                        }"
                      >
                        <div>
                          <p class="text-sm font-medium text-gray-900">
                            {{ course.title }}
                          </p>
                          <p class="text-xs text-gray-500">
                            Due {{ course.deadline }} •
                            {{ course.days_remaining }} days left
                          </p>
                        </div>
                        <ProgressRing
                          :progress="course.progress_percentage"
                          size="sm"
                        />
                      </div>

                      <!-- Quiz Deadlines -->
                      <div
                        v-for="quiz in upcoming_deadlines.quizzes"
                        :key="quiz.id"
                        class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:border-emerald-300 transition-colors"
                        :class="{
                          'border-red-200 bg-red-50': quiz.is_overdue,
                        }"
                      >
                        <div>
                          <p class="text-sm font-medium text-gray-900">
                            {{ quiz.quiz_title }}
                          </p>
                          <p class="text-xs text-gray-500">
                            {{ quiz.course_title }} •
                            Due {{ quiz.due_at }}
                          </p>
                        </div>
                        <Link
                          :href="route('student.quizzes.start', quiz.id)"
                          class="text-xs text-emerald-600 hover:text-emerald-800 font-semibold"
                        >
                          Take Quiz
                        </Link>
                      </div>

                      <div
                        v-if="
                          upcoming_deadlines.courses.length === 0 &&
                          upcoming_deadlines.quizzes.length === 0
                        "
                        class="text-center py-4"
                      >
                        <p class="text-sm text-gray-500">
                          No upcoming deadlines
                        </p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                  <div class="px-6 py-4 border-b border-gray-200/70">
                    <h2 class="text-lg font-bold text-gray-900">
                      Quick Actions
                    </h2>
                  </div>
                  <div class="p-6">
                    <div class="grid grid-cols-2 gap-4">
                      <Link
                        :href="route('student.courses.create')"
                        class="flex flex-col items-center p-4 border border-gray-200 rounded-xl hover:bg-emerald-50 transition-colors group"
                      >
                        <div class="p-2 bg-emerald-100 rounded-lg group-hover:bg-emerald-200 transition-colors">
                          <PlusCircleIcon class="h-8 w-8 text-emerald-600" />
                        </div>
                        <span class="mt-2 text-sm font-semibold text-gray-900">
                          New Course
                        </span>
                      </Link>

                      <Link
                        :href="route('student.chat.create')"
                        class="flex flex-col items-center p-4 border border-gray-200 rounded-xl hover:bg-emerald-50 transition-colors group"
                      >
                        <div class="p-2 bg-emerald-100 rounded-lg group-hover:bg-emerald-200 transition-colors">
                          <ChatBubbleLeftRightIcon class="h-8 w-8 text-emerald-600" />
                        </div>
                        <span class="mt-2 text-sm font-semibold text-gray-900">
                          Ask AI Tutor
                        </span>
                      </Link>

                      <Link
                        :href="route('student.quizzes.index')"
                        class="flex flex-col items-center p-4 border border-gray-200 rounded-xl hover:bg-emerald-50 transition-colors group"
                      >
                        <div class="p-2 bg-emerald-100 rounded-lg group-hover:bg-emerald-200 transition-colors">
                          <ClipboardDocumentListIcon
                            class="h-8 w-8 text-emerald-600"
                          />
                        </div>
                        <span class="mt-2 text-sm font-semibold text-gray-900">
                          Practice Quizzes
                        </span>
                      </Link>

                      <Link
                        :href="route('student.profile.show')"
                        class="flex flex-col items-center p-4 border border-gray-200 rounded-xl hover:bg-emerald-50 transition-colors group"
                      >
                        <div class="p-2 bg-emerald-100 rounded-lg group-hover:bg-emerald-200 transition-colors">
                          <UserCircleIcon class="h-8 w-8 text-emerald-600" />
                        </div>
                        <span class="mt-2 text-sm font-semibold text-gray-900">
                          My Profile
                        </span>
                      </Link>
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
import { Head, Link } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'
import {
  BookOpenIcon,
  CheckCircleIcon,
  ClockIcon,
  AcademicCapIcon,
  PlusCircleIcon,
  ChatBubbleLeftRightIcon,
  ClipboardDocumentListIcon,
  UserCircleIcon,
} from '@heroicons/vue/24/outline'
import ProgressRing from '@/Components/Student/ProgressRing.vue'
import LearningStats from '@/Components/Student/LearningStats.vue'

const props = defineProps({
  stats: Object,
  active_course: Object,
  recent_activity: Array,
  upcoming_deadlines: Object,
  learning_analytics: Object,
  current_subscription: Object,
})

const getBillingPeriod = (plan) => {
  if (!plan) return 'month'

  if (plan.billing_cycle_days >= 365) return 'year'
  if (plan.billing_cycle_days >= 90) return 'quarter'
  if (plan.billing_cycle_days >= 30) return 'month'
  return 'week'
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}
</script>
