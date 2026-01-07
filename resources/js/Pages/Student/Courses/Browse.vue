<!-- resources/js/Pages/Student/Catalog/Show.vue -->
<template>
  <StudentLayout>
    <Head title="Browse" />

    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Course header with enroll button -->
        <div class="md:flex md:items-center md:justify-between mb-8">
          <div class="flex-1 min-w-0">
            <h1 class="text-2xl font-bold text-gray-900">{{ course.title }}</h1>
            <p class="mt-1 text-sm text-gray-600">
              {{ course.subject }} • {{ course.level }} • {{ course.estimated_duration_hours }} hours
            </p>
          </div>
          <div class="mt-4 md:mt-0 md:ml-4">
            <button
              v-if="can_enroll"
              @click="enrollCourse"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
              </svg>
              Enroll Now
            </button>
            <Link
              :href="route('student.catalog.browse')"
              class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
            >
              Back to Catalog
            </Link>
          </div>
        </div>

        <!-- Course content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Left column: Course details -->
          <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
              <h2 class="text-lg font-semibold text-gray-900 mb-4">Course Description</h2>
              <p class="text-gray-600 whitespace-pre-line">{{ course.description }}</p>
            </div>

            <!-- Modules preview -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
              <h2 class="text-lg font-semibold text-gray-900 mb-4">Course Content</h2>
              <div class="space-y-4">
                <div v-for="(module, index) in course.modules" :key="module.id" class="border border-gray-200 rounded-lg p-4">
                  <h3 class="font-medium text-gray-900">
                    Module {{ index + 1 }}: {{ module.title }}
                  </h3>
                  <p class="text-sm text-gray-600 mt-1">{{ module.description }}</p>
                  <div class="mt-3">
                    <div class="text-xs text-gray-500">
                      {{ module.topics.length }} topics • {{ module.estimated_duration_minutes }} minutes
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Right column: Course info -->
          <div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-6">
              <h2 class="text-lg font-semibold text-gray-900 mb-4">Course Details</h2>

              <div class="space-y-4">
                <div>
                  <h3 class="text-sm font-medium text-gray-500">Instructor</h3>
                  <p class="mt-1 text-sm text-gray-900">{{ course.creator?.name || 'Admin' }}</p>
                </div>

                <div>
                  <h3 class="text-sm font-medium text-gray-500">Level</h3>
                  <p class="mt-1 text-sm text-gray-900 capitalize">{{ course.level }}</p>
                </div>

                <div>
                  <h3 class="text-sm font-medium text-gray-500">Duration</h3>
                  <p class="mt-1 text-sm text-gray-900">{{ course.estimated_duration_hours }} hours</p>
                </div>

                <div v-if="course.exam_board">
                  <h3 class="text-sm font-medium text-gray-500">Exam Board</h3>
                  <p class="mt-1 text-sm text-gray-900">{{ course.exam_board.name }}</p>
                </div>

                <div>
                  <h3 class="text-sm font-medium text-gray-500">Enrollment</h3>
                  <p class="mt-1 text-sm text-gray-900">
                    {{ course.current_enrollment }} enrolled
                    <span v-if="course.enrollment_limit">
                      / {{ course.enrollment_limit }} spots
                    </span>
                  </p>
                </div>

                <!-- Enroll button for mobile -->
                <div class="pt-4 border-t border-gray-200 lg:hidden">
                  <button
                    v-if="can_enroll"
                    @click="enrollCourse"
                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    Enroll Now
                  </button>
                  <div v-else class="text-center py-2">
                    <span class="text-sm text-red-600 font-medium">Enrollment not available</span>
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
import { router } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
  course: Object,
  can_enroll: Boolean,
})

const enrollCourse = async () => {
  try {
    await router.post(route('student.courses.enroll', props.course.id), {}, {
      preserveScroll: true,
      onSuccess: () => {
        router.visit(route('student.courses.show', props.course.id))
      },
      onError: (errors) => {
        alert(errors.message || 'Failed to enroll in the course')
      }
    })
  } catch (error) {
    console.error('Enrollment failed:', error)
  }
}
</script>
