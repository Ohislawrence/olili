<!-- resources/js/Components/Student/OutlineSidebar.vue -->
<template>
  <div class="w-80 bg-white border-r border-gray-200 flex flex-col">
    <!-- Course Header -->
    <div class="p-4 border-b border-gray-200">
      <h2 class="text-lg font-semibold text-gray-900 truncate">
        {{ course.title }}
      </h2>
      <p class="text-sm text-gray-600 mt-1 truncate">
        {{ course.subject }}
      </p>
      <div class="mt-3">
        <ProgressRing :progress="course.progress_percentage" size="sm" />
        <p class="text-xs text-gray-500 mt-1">
          {{ course.progress_percentage }}% Complete
        </p>
      </div>
    </div>

    <!-- Outline List -->
    <div class="flex-1 overflow-auto">
      <nav class="p-4 space-y-2">
        <div
          v-for="outline in outlines"
          :key="outline.id"
          @click="navigateToOutline(outline)"
          class="group flex items-center p-3 rounded-lg cursor-pointer transition-colors"
          :class="getOutlineClasses(outline)"
        >
          <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center">
            <div
              class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-medium"
              :class="getOutlineIconClasses(outline)"
            >
              <template v-if="outline.is_completed">
                <CheckIcon class="h-3 w-3" />
              </template>
              <template v-else>
                {{ outline.order }}
              </template>
            </div>
          </div>
          <div class="ml-3 flex-1 min-w-0">
            <p
              class="text-sm font-medium truncate"
              :class="getTitleClasses(outline)"
            >
              {{ outline.title }}
            </p>
            <p class="text-xs text-gray-500 capitalize">
              {{ outline.type }}
            </p>
          </div>
          <div
            v-if="outline.has_quiz"
            class="opacity-0 group-hover:opacity-100 transition-opacity"
          >
            <ClipboardDocumentListIcon class="h-4 w-4 text-gray-400" />
          </div>
        </div>
      </nav>
    </div>

    <!-- Quick Actions -->
    <div class="p-4 border-t border-gray-200 space-y-2">
      <Link
        :href="route('student.chat.create', { course_id: course.id, outline_id: current_outline.id })"
        class="w-full flex items-center justify-center px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors"
      >
        <ChatBubbleLeftRightIcon class="h-4 w-4 mr-2" />
        Ask AI Tutor
      </Link>
      <Link
        :href="route('student.courses.show', course.id)"
        class="w-full flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-600 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 transition-colors"
      >
        <ArrowLeftIcon class="h-4 w-4 mr-2" />
        Back to Course
      </Link>
    </div>
  </div>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import {
  CheckIcon,
  ClipboardDocumentListIcon,
  ChatBubbleLeftRightIcon,
  ArrowLeftIcon,
} from '@heroicons/vue/24/outline'
import ProgressRing from './ProgressRing.vue'

const props = defineProps({
  course: Object,
  outlines: Array,
  current_outline: Object,
})

const getOutlineClasses = (outline) => {
  if (outline.id === props.current_outline.id) {
    return 'bg-blue-50 border border-blue-200'
  }
  if (outline.is_completed) {
    return 'hover:bg-gray-50'
  }
  return 'hover:bg-gray-50'
}

const getOutlineIconClasses = (outline) => {
  if (outline.id === props.current_outline.id) {
    return 'bg-blue-600 text-white'
  }
  if (outline.is_completed) {
    return 'bg-green-100 text-green-800'
  }
  return 'bg-gray-100 text-gray-600'
}

const getTitleClasses = (outline) => {
  if (outline.id === props.current_outline.id) {
    return 'text-blue-900'
  }
  if (outline.is_completed) {
    return 'text-green-900'
  }
  return 'text-gray-900'
}

const navigateToOutline = (outline) => {
  if (outline.id !== props.current_outline.id) {
    router.visit(
      route('student.courses.learn', {
        course: props.course.id,
        outline: outline.id,
      })
    )
  }
}
</script>
