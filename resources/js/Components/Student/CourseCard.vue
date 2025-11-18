<!-- resources/js/Components/Student/CourseCard.vue -->
<template>
  <div class="bg-white rounded-lg shadow hover:shadow-md transition-shadow">
    <div class="p-6">
      <!-- Header -->
      <div class="flex items-start justify-between mb-4">
        <div>
          <h3 class="text-lg font-semibold text-gray-900">
            {{ course.title }}
          </h3>
          <p class="text-sm text-gray-600 mt-1">
            {{ course.subject }}
          </p>
        </div>
        <span
          class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
          :class="statusClasses[course.status]"
        >
          {{ course.status }}
        </span>
      </div>

      <!-- Progress -->
      <div class="mb-4">
        <div class="flex items-center justify-between text-sm text-gray-600 mb-2">
          <span>Progress</span>
          <span>{{ course.progress_percentage }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
          <div
            class="h-2 rounded-full transition-all duration-300"
            :class="progressColor"
            :style="{ width: `${course.progress_percentage}%` }"
          ></div>
        </div>
        <p class="text-xs text-gray-500 mt-1">
          {{ course.completed_outlines_count }} of
          {{ course.outlines_count }} topics completed
        </p>
      </div>

      <!-- Details -->
      <div class="space-y-2 text-sm text-gray-600">
        <div class="flex items-center">
          <AcademicCapIcon class="h-4 w-4 mr-2" />
          <span>Level: {{ course.level }}</span>
        </div>
        <div class="flex items-center">
          <CalendarIcon class="h-4 w-4 mr-2" />
          <span>
            Due: {{ formatDate(course.target_completion_date) }}
          </span>
        </div>
        <div v-if="course.exam_board" class="flex items-center">
          <ClipboardDocumentListIcon class="h-4 w-4 mr-2" />
          <span>{{ course.exam_board.name }}</span>
        </div>
      </div>

      <!-- Actions -->
      <div class="mt-6 flex space-x-3">
        <Link
          :href="route('student.courses.show', course.id)"
          class="flex-1 text-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
        >
          View Details
        </Link>
        <Link
          v-if="course.status === 'active'"
          :href="route('student.courses.learn', course.id)"
          class="flex-1 text-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
        >
          Continue
        </Link>
        <Link
          v-else-if="course.status === 'draft'"
          :href="route('student.courses.start', course.id)"
          class="flex-1 text-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
        >
          Start Course
        </Link>
        <button
          v-else-if="course.status === 'paused'"
          @click="resumeCourse"
          class="flex-1 text-center px-4 py-2 text-sm font-medium text-white bg-orange-600 border border-transparent rounded-md hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2"
        >
          Resume
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import {
  AcademicCapIcon,
  CalendarIcon,
  ClipboardDocumentListIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  course: Object,
})

const statusClasses = {
  draft: 'bg-gray-100 text-gray-800',
  active: 'bg-blue-100 text-blue-800',
  completed: 'bg-green-100 text-green-800',
  paused: 'bg-orange-100 text-orange-800',
}

const progressColor = computed(() => {
  if (props.course.progress_percentage < 30) return 'bg-red-500'
  if (props.course.progress_percentage < 70) return 'bg-yellow-500'
  return 'bg-green-500'
})

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

const resumeCourse = () => {
  router.post(route('student.courses.resume', props.course.id))
}
</script>
