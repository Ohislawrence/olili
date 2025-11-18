<template>
  <StudentLayout>
    <Head title="Start New Chat" />

    <div class="py-6">
      <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex items-center gap-3 mb-4">
            <Link
              :href="route('student.chat.index')"
              class="inline-flex items-center text-sm font-medium text-emerald-600 hover:text-emerald-500"
            >
              <ArrowLeftIcon class="h-4 w-4 mr-1" />
              Back to Chats
            </Link>
          </div>
          <h1 class="text-2xl font-bold text-gray-900">Start New Chat</h1>
          <p class="mt-1 text-sm text-gray-600">
            Begin a conversation with your AI tutor about any course or topic
          </p>
        </div>

        <!-- Chat Form -->
        <div class="bg-white shadow-sm rounded-xl border border-gray-100">
          <div class="p-6">
            <form @submit.prevent="submit">
              <!-- Course Selection -->
              <div class="mb-6">
                <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">
                  Course (Optional)
                </label>
                <select
                  id="course_id"
                  v-model="form.course_id"
                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                  @change="loadCourseTopics"
                >
                  <option value="">Select a course...</option>
                  <option
                    v-for="course in courses"
                    :key="course.id"
                    :value="course.id"
                  >
                    {{ course.title }}
                  </option>
                </select>
                <p class="mt-1 text-xs text-gray-500">
                  Selecting a course helps the AI tutor provide more relevant assistance
                </p>
              </div>

              <!-- Topic Selection -->
              <div class="mb-6" v-if="available_topics.length">
                <label for="outline_id" class="block text-sm font-medium text-gray-700 mb-2">
                  Specific Topic (Optional)
                </label>
                <select
                  id="outline_id"
                  v-model="form.outline_id"
                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                >
                  <option value="">Select a topic...</option>
                  <optgroup
                    v-for="module in available_modules"
                    :key="module.id"
                    :label="`Module ${module.order}: ${module.title}`"
                  >
                    <option
                      v-for="topic in module.topics"
                      :key="topic.id"
                      :value="topic.id"
                    >
                      Topic {{ topic.order }}: {{ topic.title }}
                    </option>
                  </optgroup>
                </select>
                <p class="mt-1 text-xs text-gray-500">
                  Focus the conversation on a specific learning topic
                </p>
              </div>

              <!-- Initial Message -->
              <div class="mb-6">
                <label for="initial_message" class="block text-sm font-medium text-gray-700 mb-2">
                  Initial Message (Optional)
                </label>
                <textarea
                  id="initial_message"
                  v-model="form.initial_message"
                  rows="4"
                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                  placeholder="What would you like to learn about today? You can ask questions about course content, request explanations, or seek guidance on specific topics..."
                ></textarea>
                <p class="mt-1 text-xs text-gray-500">
                  {{ form.initial_message?.length || 0 }}/1000 characters
                </p>
              </div>

              <!-- Selected Context Preview -->
              <div
                v-if="selected_course || selected_outline"
                class="mb-6 p-4 bg-emerald-50 rounded-lg border border-emerald-200"
              >
                <h3 class="text-sm font-semibold text-emerald-900 mb-2">Chat Context</h3>
                <div class="text-sm text-emerald-800">
                  <div v-if="selected_course">
                    <strong>Course:</strong> {{ selected_course.title }}
                  </div>
                  <div v-if="selected_outline">
                    <strong>Topic:</strong> {{ selected_outline.title }}
                    <span class="text-emerald-600">
                      (Module: {{ selected_outline.module?.title }})
                    </span>
                  </div>
                  <div v-else-if="selected_course && !form.outline_id">
                    <strong>Scope:</strong> General course discussion
                  </div>
                  <div v-else>
                    <strong>Scope:</strong> General learning assistance
                  </div>
                </div>
              </div>

              <!-- Form Actions -->
              <div class="flex justify-end space-x-3">
                <Link
                  :href="route('student.chat.index')"
                  class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-emerald-300 rounded-lg shadow-sm hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                >
                  Cancel
                </Link>
                <button
                  type="submit"
                  :disabled="form.processing"
                  class="inline-flex items-center px-4 py-2.5 text-sm font-semibold text-white bg-emerald-600 border border-transparent rounded-lg shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
                >
                  <ChatBubbleLeftRightIcon class="h-4 w-4 mr-2" />
                  {{ form.processing ? 'Starting Chat...' : 'Start Chat' }}
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Quick Start Options -->
        <div class="mt-8">
          <h2 class="text-lg font-bold text-gray-900 mb-4">Quick Start</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <button
              v-for="quickOption in quickStartOptions"
              :key="quickOption.title"
              @click="setQuickStart(quickOption)"
              class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md hover:border-emerald-300 transition-all duration-200 text-left"
            >
              <h3 class="text-sm font-medium text-gray-900 mb-1">
                {{ quickOption.title }}
              </h3>
              <p class="text-xs text-gray-500">
                {{ quickOption.description }}
              </p>
            </button>
          </div>
        </div>
      </div>
    </div>
  </StudentLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { router } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import {
  ArrowLeftIcon,
  ChatBubbleLeftRightIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  courses: Array,
  selected_course: Object,
  selected_outline: Object,
  available_modules: Array,
  available_topics: Array,
})

const form = reactive({
  course_id: props.selected_course?.id || '',
  outline_id: props.selected_outline?.id || '',
  initial_message: '',
})

const quickStartOptions = [
  {
    title: 'General Learning Help',
    description: 'Get assistance with general learning strategies and study techniques',
    message: "I'd like some general learning assistance. Can you help me with effective study techniques and learning strategies?"
  },
  {
    title: 'Concept Explanation',
    description: 'Ask for explanations of difficult concepts you\'re struggling with',
    message: "I'm having trouble understanding a concept. Can you break it down for me with simple examples?"
  },
  {
    title: 'Practice Questions',
    description: 'Request practice questions to test your understanding',
    message: "Can you provide me with some practice questions to test my understanding of this topic?"
  },
  {
    title: 'Study Plan Help',
    description: 'Get help creating or optimizing your study schedule',
    message: "I need help creating an effective study plan. Can you guide me through setting up a productive learning schedule?"
  }
]

const submit = () => {
  router.post(route('student.chat.store'), form)
}

const loadCourseTopics = () => {
  // Reset topic selection when course changes
  form.outline_id = ''

  if (form.course_id) {
    // In a real implementation, you might want to fetch topics via AJAX
    // For now, we rely on the backend to provide available_modules and available_topics
    console.log('Course selected:', form.course_id)
  }
}

const setQuickStart = (option) => {
  form.initial_message = option.message
}
</script>
