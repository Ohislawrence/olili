<!-- resources/js/Pages/Student/Courses/Shared.vue -->
<template>
  <StudentLayout>
    <Head title="Shared Courses" />

    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex justify-between items-center">
            <div>
              <h1 class="text-2xl font-bold text-gray-900">Shared Courses</h1>
              <p class="mt-1 text-sm text-gray-600">
                Courses shared with you by other users
              </p>
            </div>
          </div>
        </div>

        <!-- Flash Messages -->
        <div v-if="$page.props.flash.success" class="mb-4">
          <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            {{ $page.props.flash.success }}
          </div>
        </div>
        <div v-if="$page.props.flash.error" class="mb-4">
          <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            {{ $page.props.flash.error }}
          </div>
        </div>

        <!-- Pending Invitations -->
        <div v-if="pendingShares.length > 0" class="mb-8">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Pending Invitations</h2>
          <div class="space-y-4">
            <div
              v-for="share in pendingShares"
              :key="share.id"
              class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden"
            >
              <div class="p-6">
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between">
                  <div class="lg:flex-1 lg:pr-6">
                    <div class="flex items-center mb-2">
                      <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 mr-3">
                        Invitation
                      </span>
                      <span class="text-sm text-gray-500">
                        Expires {{ formatDate(share.expires_at) }}
                      </span>
                    </div>

                    <h3 class="text-lg font-bold text-gray-900 mb-2">
                      {{ share.course.title }}
                    </h3>

                    <div class="flex flex-wrap gap-4 text-sm text-gray-600 mb-4">
                      <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Shared by: {{ share.sharer.name }}
                      </div>
                      <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ share.course.estimated_duration_hours }} hours
                      </div>
                      <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        {{ share.course.subject }} • {{ share.course.level }}
                      </div>
                    </div>

                    <p class="text-gray-700 mb-4">
                      {{ share.course.description }}
                    </p>
                  </div>

                  <div class="lg:w-48 lg:flex-shrink-0 lg:ml-6 mt-4 lg:mt-0">
                    <div class="space-y-3">
                      <Link
                        :href="route('student.course.share.accept', share.token)"
                        method="get"
                        class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-emerald-600 text-white font-semibold rounded-lg hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-colors"
                      >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Accept Course
                      </Link>
                      <Link
                        :href="route('student.share.reject', share.token)"
                        method="get"
                        class="w-full inline-flex justify-center items-center px-4 py-2.5 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors"
                      >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Decline
                      </Link>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Accepted Shared Courses -->
        <div>
          <h2 class="text-lg font-semibold text-gray-900 mb-4">
            Accepted Courses {{ sharedCourses.data.length ? `(${sharedCourses.data.length})` : '' }}
          </h2>

          <div v-if="sharedCourses.data.length" class="space-y-4">
            <div
              v-for="share in sharedCourses.data"
              :key="share.id"
              class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow"
            >
              <div class="p-6">
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between">
                  <div class="lg:flex-1 lg:pr-6">
                    <div class="flex items-center mb-2">
                      <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 mr-3">
                        Accepted
                      </span>
                      <span class="text-sm text-gray-500">
                        Accepted on {{ formatDate(share.accepted_at) }}
                      </span>
                    </div>

                    <div class="flex flex-wrap items-center justify-between mb-4">
                      <h3 class="text-lg font-bold text-gray-900">
                        {{ share.course.title }}
                      </h3>

                      <span class="text-sm text-gray-500">
                        Shared by: {{ share.sharer.name }}
                      </span>
                    </div>

                    <div class="flex flex-wrap gap-4 text-sm text-gray-600 mb-4">
                      <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ share.course.estimated_duration_hours }} hours
                      </div>
                      <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        {{ share.course.subject }} • {{ share.course.level }}
                      </div>
                      <div v-if="share.course.exam_board" class="flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ share.course.exam_board.name }}
                      </div>
                    </div>

                    <p class="text-gray-700 mb-4 line-clamp-2">
                      {{ share.course.description }}
                    </p>
                  </div>

                  <div class="lg:w-48 lg:flex-shrink-0 lg:ml-6 mt-4 lg:mt-0">
                    <div class="space-y-3">
                      <Link
                        :href="route('student.courses.show', share.course.id)"
                        class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-emerald-600 text-white font-semibold rounded-lg hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-colors"
                      >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        View Course
                      </Link>

                      <div v-if="share.course.status === 'draft'" class="text-center">
                        <span class="text-xs text-gray-500">Ready to start learning</span>
                      </div>
                      <Link
                        v-else-if="share.course.status === 'active'"
                        :href="route('student.courses.learn', share.course.id)"
                        class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                      >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                        </svg>
                        Continue Learning
                      </Link>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Empty State for Accepted Courses -->
          <div v-else class="text-center py-12 bg-white border border-gray-200 rounded-xl">
            <div class="mx-auto max-w-md px-4">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
              </svg>
              <h3 class="mt-2 text-sm font-semibold text-gray-900">No accepted courses</h3>
              <p class="mt-1 text-sm text-gray-500">
                You haven't accepted any shared courses yet. Accept an invitation above to get started.
              </p>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="sharedCourses.data.length" class="mt-8">
          <Pagination :links="sharedCourses.links" />
        </div>
      </div>
    </div>
  </StudentLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'
import Pagination from '@/Components/Pagination.vue'

const props = defineProps({
  sharedCourses: Object,
  pendingShares: Array,
})

const formatDate = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
