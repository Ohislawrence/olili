<template>
  <StudentLayout>
    <Head title="Start New Chat" />

    <div class="py-6">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8 text-center">
          <div class="flex items-center justify-center gap-3 mb-4">
            <Link
              :href="route('student.chat.index')"
              class="inline-flex items-center text-sm font-medium text-emerald-600 hover:text-emerald-500 transition-colors"
            >
              <ArrowLeftIcon class="h-4 w-4 mr-1" />
              Back to Chats
            </Link>
          </div>
          <div class="flex items-center justify-center mb-4">
            <div class="h-16 w-16 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full flex items-center justify-center shadow-lg">
              <ChatBubbleLeftRightIcon class="h-8 w-8 text-white" />
            </div>
          </div>
          <h1 class="text-3xl font-bold text-gray-900 mb-3">Start New Chat with AI Tutor</h1>
          <p class="text-lg text-gray-600 max-w-2xl mx-auto">
            Begin an intelligent conversation about your courses. Get personalized explanations, study help, and learning guidance.
          </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Quick Start Options -->
          <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 sticky top-6">
              <div class="p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                  <BoltIcon class="h-5 w-5 text-amber-500 mr-2" />
                  Quick Start
                </h2>
                <p class="text-sm text-gray-600 mb-4">
                  Choose a common starting point for your conversation
                </p>

                <div class="space-y-3">
                  <button
                    v-for="quickOption in quickStartOptions"
                    :key="quickOption.title"
                    @click="setQuickStart(quickOption)"
                    class="w-full p-4 bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md hover:border-emerald-300 transition-all duration-200 text-left group"
                    :class="{
                      'border-emerald-300 bg-emerald-50': form.initial_message === quickOption.message
                    }"
                  >
                    <div class="flex items-start space-x-3">
                      <div class="flex-shrink-0 h-8 w-8 bg-emerald-100 rounded-lg flex items-center justify-center group-hover:bg-emerald-200 transition-colors">
                        <component :is="quickOption.icon" class="h-4 w-4 text-emerald-600" />
                      </div>
                      <div class="flex-1">
                        <h3 class="text-sm font-semibold text-gray-900 mb-1">
                          {{ quickOption.title }}
                        </h3>
                        <p class="text-xs text-gray-500 leading-relaxed">
                          {{ quickOption.description }}
                        </p>
                      </div>
                    </div>
                  </button>
                </div>

                <!-- Recent Courses -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                  <h3 class="text-sm font-semibold text-gray-900 mb-3">Recent Courses</h3>
                  <div class="space-y-2">
                    <button
                      v-for="course in recentCourses"
                      :key="course.id"
                      @click="selectRecentCourse(course)"
                      class="w-full text-left p-3 text-sm text-gray-700 bg-gray-50 hover:bg-emerald-50 hover:text-emerald-700 rounded-lg transition-colors flex items-center"
                    >
                      <AcademicCapIcon class="h-4 w-4 mr-2 text-gray-400" />
                      <span class="truncate">{{ course.title }}</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Chat Configuration -->
          <div class="lg:col-span-2">
            <div class="bg-white shadow-sm rounded-xl border border-gray-100">
              <div class="p-6">
                <form @submit.prevent="submit">
                  <!-- Course Selection -->
                  <div class="mb-6">
                    <label for="course_id" class="block text-sm font-medium text-gray-700 mb-3">
                      <div class="flex items-center">
                        <AcademicCapIcon class="h-5 w-5 text-emerald-600 mr-2" />
                        Select Course
                      </div>
                    </label>
                    <div class="relative">
                      <select
                        id="course_id"
                        v-model="form.course_id"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-3 px-4 pr-10 appearance-none bg-white transition-colors"
                        @change="loadCourseTopics"
                      >
                        <option :value="null">Choose a course (optional)</option>
                        <option
                          v-for="course in courses"
                          :key="course.id"
                          :value="course.id"
                        >
                          {{ course.title }}
                        </option>
                      </select>
                      <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <ChevronDownIcon class="h-5 w-5 text-gray-400" />
                      </div>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">
                      Selecting a course helps the AI provide more relevant and contextual assistance
                    </p>
                  </div>

                  <!-- Topic Selection -->
                  <div class="mb-6" v-if="available_topics.length">
                    <label for="outline_id" class="block text-sm font-medium text-gray-700 mb-3">
                      <div class="flex items-center">
                        <BookOpenIcon class="h-5 w-5 text-emerald-600 mr-2" />
                        Focus on Specific Topic
                      </div>
                    </label>
                    <div class="relative">
                      <select
                        id="outline_id"
                        v-model="form.outline_id"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-3 px-4 pr-10 appearance-none bg-white transition-colors"
                      >
                        <option :value="null">Choose a topic (optional)</option>
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
                      <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <ChevronDownIcon class="h-5 w-5 text-gray-400" />
                      </div>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">
                      Narrow down the conversation to a specific learning topic for more focused assistance
                    </p>
                  </div>

                  <!-- Initial Message -->
                  <div class="mb-6">
                    <label for="initial_message" class="block text-sm font-medium text-gray-700 mb-3">
                      <div class="flex items-center">
                        <ChatBubbleLeftRightIcon class="h-5 w-5 text-emerald-600 mr-2" />
                        Your Message
                      </div>
                    </label>
                    <div class="relative">
                      <textarea
                        id="initial_message"
                        v-model="form.initial_message"
                        rows="5"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-3 px-4 resize-none transition-colors"
                        placeholder="What would you like to learn about today? You can ask questions about course content, request explanations, seek guidance on specific topics, or ask for practice problems..."
                        :class="{
                          'border-emerald-300': form.initial_message.length > 0
                        }"
                      ></textarea>
                      <div class="absolute bottom-3 right-3">
                        <span class="text-xs" :class="form.initial_message.length > 1000 ? 'text-red-500' : 'text-gray-400'">
                          {{ form.initial_message.length }}/1000
                        </span>
                      </div>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">
                      Start the conversation with your specific question or learning goal
                    </p>
                  </div>

                  <!-- Context Preview -->
                  <div
                    v-if="selected_course || selected_outline"
                    class="mb-6 p-4 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-lg border border-emerald-200"
                  >
                    <h3 class="text-sm font-semibold text-emerald-900 mb-3 flex items-center">
                      <InformationCircleIcon class="h-4 w-4 mr-2" />
                      Chat Context Preview
                    </h3>
                    <div class="space-y-2 text-sm text-emerald-800">
                      <div v-if="selected_course" class="flex items-center">
                        <AcademicCapIcon class="h-4 w-4 mr-2" />
                        <span><strong>Course:</strong> {{ selected_course.title }}</span>
                      </div>
                      <div v-if="selected_outline" class="flex items-center">
                        <BookOpenIcon class="h-4 w-4 mr-2" />
                        <span><strong>Topic:</strong> {{ selected_outline.title }}</span>
                        <span class="text-emerald-600 ml-2">
                          (Module: {{ selected_outline.module?.title }})
                        </span>
                      </div>
                      <div v-else-if="selected_course && !form.outline_id" class="flex items-center">
                        <GlobeAltIcon class="h-4 w-4 mr-2" />
                        <span><strong>Scope:</strong> General course discussion</span>
                      </div>
                      <div v-else class="flex items-center">
                        <GlobeAltIcon class="h-4 w-4 mr-2" />
                        <span><strong>Scope:</strong> General learning assistance</span>
                      </div>
                    </div>
                  </div>

                  <!-- Form Actions -->
                  <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <Link
                      :href="route('student.chat.index')"
                      class="inline-flex items-center px-6 py-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200"
                    >
                      Cancel
                    </Link>
                    <button
                      type="submit"
                      :disabled="form.processing || form.initial_message.length > 1000"
                      class="inline-flex items-center px-6 py-3 text-sm font-semibold text-white bg-gradient-to-r from-emerald-600 to-teal-600 border border-transparent rounded-lg shadow-sm hover:from-emerald-700 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 hover:shadow-md"
                    >
                      <ChatBubbleLeftRightIcon class="h-4 w-4 mr-2" />
                      {{ form.processing ? 'Starting Chat...' : 'Start Chat Session' }}
                    </button>
                  </div>
                </form>
              </div>
            </div>

            <!-- Tips Section -->
            <div class="mt-6 bg-blue-50 rounded-xl border border-blue-200 p-6">
              <h3 class="text-sm font-semibold text-blue-900 mb-3 flex items-center">
                <LightBulbIcon class="h-4 w-4 mr-2" />
                Pro Tips for Better AI Assistance
              </h3>
              <ul class="text-sm text-blue-800 space-y-2">
                <li class="flex items-start">
                  <CheckIcon class="h-4 w-4 text-blue-600 mr-2 mt-0.5 flex-shrink-0" />
                  <span>Be specific about what you're struggling with</span>
                </li>
                <li class="flex items-start">
                  <CheckIcon class="h-4 w-4 text-blue-600 mr-2 mt-0.5 flex-shrink-0" />
                  <span>Mention your current understanding level</span>
                </li>
                <li class="flex items-start">
                  <CheckIcon class="h-4 w-4 text-blue-600 mr-2 mt-0.5 flex-shrink-0" />
                  <span>Ask for examples if concepts are unclear</span>
                </li>
                <li class="flex items-start">
                  <CheckIcon class="h-4 w-4 text-blue-600 mr-2 mt-0.5 flex-shrink-0" />
                  <span>Request practice problems to test your knowledge</span>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </StudentLayout>
</template>

<script setup>
import { ref, reactive, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import {
  ArrowLeftIcon,
  ChatBubbleLeftRightIcon,
  AcademicCapIcon,
  BookOpenIcon,
  BoltIcon,
  InformationCircleIcon,
  GlobeAltIcon,
  LightBulbIcon,
  CheckIcon,
  ChevronDownIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  courses: Array,
  selected_course: Object,
  selected_outline: Object,
  available_modules: Array,
  available_topics: Array,
})

// Initialize form with proper null values for empty selections
const form = reactive({
  course_id: props.selected_course?.id || null,
  outline_id: props.selected_outline?.id || null,
  initial_message: '',
  processing: false,
})

// Get recent courses (last 3 accessed)
const recentCourses = computed(() => {
  return props.courses.slice(0, 3)
})

const quickStartOptions = [
  {
    title: 'Concept Explanation',
    description: 'Get clear explanations of difficult topics',
    message: "I'm having trouble understanding this concept. Can you break it down for me with simple examples and analogies?",
    icon: BookOpenIcon
  },
  {
    title: 'Study Strategy',
    description: 'Get personalized study techniques and plans',
    message: "I need help developing an effective study strategy for this course. What learning methods would work best for this material?",
    icon: LightBulbIcon
  },
  {
    title: 'Practice Problems',
    description: 'Request exercises to test your understanding',
    message: "Can you provide me with some practice problems or exercises to test my understanding of this topic? Please include explanations for the solutions.",
    icon: AcademicCapIcon
  },
  {
    title: 'Project Guidance',
    description: 'Get help with assignments and projects',
    message: "I'm working on a project/assignment and need guidance on the best approach. Can you help me break down the requirements and suggest a plan?",
    icon: ChatBubbleLeftRightIcon
  }
]

const submit = async () => {
  if (form.initial_message.length > 1000) {
    return
  }

  form.processing = true

  try {
    // Prepare data for submission - ensure proper null values
    const submitData = {
      course_id: form.course_id || null,
      outline_id: form.outline_id || null,
      initial_message: form.initial_message || null,
    }

    console.log('Submitting form data:', submitData) // Debug log

    await router.post(route('student.chat.store'), submitData, {
      onError: (errors) => {
        console.error('Form submission errors:', errors)
        form.processing = false
      },
      onFinish: () => {
        form.processing = false
      }
    })
  } catch (error) {
    console.error('Form submission failed:', error)
    form.processing = false
  }
}

const loadCourseTopics = () => {
  // Reset topic selection when course changes
  form.outline_id = null

  if (form.course_id) {
    console.log('Course selected:', form.course_id)
    // The backend will handle loading the topics via the component props
    // You might want to add an AJAX call here to load topics dynamically
  }
}

const setQuickStart = (option) => {
  form.initial_message = option.message
}

const selectRecentCourse = (course) => {
  form.course_id = course.id
  loadCourseTopics()
}

// Watch for form changes for debugging
watch(form, (newValue) => {
  console.log('Form updated:', newValue)
}, { deep: true })
</script>

<style scoped>
select {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
  background-position: right 0.5rem center;
  background-repeat: no-repeat;
  background-size: 1.5em 1.5em;
  padding-right: 2.5rem;
  -webkit-print-color-adjust: exact;
  print-color-adjust: exact;
}
</style>
