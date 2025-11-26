<template>
  <!-- Mobile Sidebar Overlay -->
  <div
    v-if="open"
    class="lg:hidden fixed inset-0 flex z-50"
  >
    <!-- Backdrop -->
    <div
      class="fixed inset-0 bg-gray-600 bg-opacity-75 transition-opacity"
      @click="$emit('close')"
    ></div>

    <!-- Sidebar Panel -->
    <div class="relative flex-1 flex flex-col max-w-xs w-full bg-white shadow-xl">
      <!-- Close Button -->
      <div class="absolute top-0 right-0 -mr-12 pt-4">
        <button
          @click="$emit('close')"
          class="ml-1 flex items-center justify-center h-10 w-10 bg-emerald-500 hover:bg-emerald-600 text-white rounded-full focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-colors"
        >
          <XMarkIcon class="h-6 w-6" />
        </button>
      </div>

      <!-- Sidebar Content -->
      <div class="flex flex-col h-full">
        <!-- Course Header -->
        <div class="p-6 bg-gradient-to-r from-emerald-600 to-teal-600 text-white">
          <div class="flex items-center justify-between mb-3">
            <h2 class="text-lg font-bold truncate">{{ course.title }}</h2>
          </div>
          <div class="flex items-center justify-between text-sm text-emerald-100">
            <div class="flex items-center">
              <AcademicCapIcon class="h-4 w-4 mr-1" />
              {{ courseStats.completed_modules || 0 }}/{{ courseStats.total_modules || 0 }} Modules
            </div>
            <span
              :class="[
                'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold',
                getStatusClass(course.status)
              ]"
            >
              {{ course.status }}
            </span>
          </div>
        </div>

        <!-- Progress Overview -->
        <div class="p-4 border-b border-gray-200 bg-white">
          <div class="space-y-3">
            <div>
              <div class="flex justify-between text-sm text-gray-600 mb-1">
                <span>Overall Progress</span>
                <span class="font-semibold">{{ courseStats.overall_completion_percentage || 0 }}%</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2.5">
                <div
                  class="bg-gradient-to-r from-emerald-500 to-teal-500 h-2.5 rounded-full transition-all duration-500"
                  :style="{ width: `${courseStats.overall_completion_percentage || 0}%` }"
                ></div>
              </div>
            </div>

            <div class="grid grid-cols-2 gap-3 text-xs">
              <div class="text-center p-2 bg-emerald-50 rounded-lg">
                <div class="font-bold text-emerald-700">{{ courseStats.completed_modules || 0 }}/{{ courseStats.total_modules || 0 }}</div>
                <div class="text-emerald-600">Modules</div>
              </div>
              <div class="text-center p-2 bg-teal-50 rounded-lg">
                <div class="font-bold text-teal-700">{{ courseStats.completed_topics || 0 }}/{{ courseStats.total_topics || 0 }}</div>
                <div class="text-teal-600">Topics</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Course Structure -->
        <div class="flex-1 overflow-y-auto">
          <nav class="p-4 space-y-2">
            <div
              v-for="module in courseStructure"
              :key="module.id"
              class="border border-gray-200 rounded-xl overflow-hidden"
            >
              <!-- Module Header -->
              <div
                @click="$emit('toggleModule', module.id)"
                :class="[
                  'flex items-center justify-between p-4 cursor-pointer transition-colors',
                  expandedModules[module.id] ? 'bg-emerald-50 border-b border-emerald-100' : 'bg-white hover:bg-gray-50'
                ]"
              >
                <div class="flex items-center flex-1 min-w-0">
                  <div class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white font-bold text-sm mr-3 flex-shrink-0">
                    {{ module.order }}
                  </div>
                  <div class="flex-1 min-w-0">
                    <h3 class="font-bold text-gray-900 truncate text-sm">{{ module.title }}</h3>
                    <p class="text-xs text-gray-500 mt-1">
                      {{ module.completed_topics }}/{{ module.total_topics }} topics • {{ module.estimated_duration_minutes }}min
                    </p>
                  </div>
                </div>
                <div class="flex items-center space-x-2 flex-shrink-0">
                  <div class="w-12 bg-gray-200 rounded-full h-1.5">
                    <div
                      class="bg-emerald-500 h-1.5 rounded-full transition-all duration-300"
                      :style="{ width: `${(module.completed_topics / module.total_topics) * 100}%` }"
                    ></div>
                  </div>
                  <ChevronDownIcon
                    class="h-4 w-4 text-gray-400 transition-transform duration-200"
                    :class="{ 'rotate-180': expandedModules[module.id] }"
                  />
                </div>
              </div>

              <!-- Module Topics -->
              <div v-if="expandedModules[module.id]" class="bg-gray-50 border-t border-gray-100">
                <div
                  v-for="topic in module.topics"
                  :key="topic.id"
                  @click="selectTopic(topic)"
                  :class="[
                    'group flex items-center px-4 py-3 cursor-pointer transition-all duration-200 border-l-4',
                    currentTopic.id === topic.id
                      ? 'bg-white border-emerald-500 shadow-sm'
                      : 'hover:bg-white hover:border-emerald-200 border-transparent',
                    topic.is_completed ? 'opacity-100' : 'opacity-100'
                  ]"
                >
                  <div class="flex items-center flex-1 min-w-0">
                    <div
                      :class="[
                        'w-6 h-6 rounded-full flex items-center justify-center text-xs font-medium mr-3 flex-shrink-0 border-2',
                        currentTopic.id === topic.id
                          ? 'bg-emerald-500 text-white border-emerald-500'
                          : topic.is_completed
                          ? 'bg-emerald-500 text-white border-emerald-500'
                          : 'bg-white text-gray-400 border-gray-300'
                      ]"
                    >
                      <CheckCircleIcon v-if="topic.is_completed" class="h-3 w-3" />
                      <span v-else>{{ topic.order }}</span>
                    </div>

                    <div class="flex-1 min-w-0">
                      <h4 class="text-sm font-medium text-gray-900 truncate">{{ topic.title }}</h4>
                      <p class="text-xs text-gray-500 mt-0.5 flex items-center">
                        <ClockIcon class="h-3 w-3 mr-1" />
                        {{ topic.estimated_duration_minutes }}min
                        <span v-if="topic.has_quiz" class="ml-2 inline-flex items-center">
                          <QuestionMarkCircleIcon class="h-3 w-3 mr-1" />
                          Quiz
                        </span>
                      </p>
                    </div>
                  </div>

                  <!-- Topic Status Indicator -->
                  <div class="flex items-center space-x-2 flex-shrink-0 ml-2">
                    <div
                      v-if="currentTopic.id === topic.id"
                      class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"
                    ></div>
                    <PlayCircleIcon
                      v-if="!topic.is_completed && currentTopic.id !== topic.id"
                      class="h-4 w-4 text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity"
                    />
                  </div>
                </div>
              </div>
            </div>
          </nav>
        </div>

        <!-- Quick Actions -->
        <div class="p-4 border-t border-gray-200 bg-white space-y-3">


          <div class="grid grid-cols-2 gap-2">
            <button
              v-if="hasPreviousTopic"
              @click="goToPreviousTopic"
              class="flex items-center justify-center px-3 py-2.5 border border-emerald-300 text-emerald-700 font-medium rounded-lg hover:bg-emerald-50 transition-colors text-sm"
            >
              <ChevronLeftIcon class="h-4 w-4 mr-1" />
              Previous
            </button>
            <button
              v-if="hasNextTopic"
              @click="goToNextTopic"
              class="flex items-center justify-center px-3 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-medium rounded-lg hover:from-emerald-700 hover:to-teal-700 transition-all duration-200 shadow-sm hover:shadow-md text-sm"
            >
              Next
              <ChevronRightIcon class="h-4 w-4 ml-1" />
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'
import {
  AcademicCapIcon,
  QuestionMarkCircleIcon,
  CheckCircleIcon,
  ChevronLeftIcon,
  ChevronRightIcon,
  ChevronDownIcon,
  XMarkIcon,
  ClockIcon,
  PlayCircleIcon,
  ChatBubbleLeftRightIcon,
} from '@heroicons/vue/24/outline'

// Define props
const props = defineProps({
  open: Boolean,
  course: Object,
  courseStructure: Array,
  currentTopic: Object,
  courseStats: Object,
  expandedModules: Object,
})

// Define emits
const emit = defineEmits(['close', 'selectTopic', 'toggleModule'])

// Computed properties
const allTopics = computed(() => {
  return props.courseStructure.flatMap(module => module.topics)
})

const currentTopicIndex = computed(() =>
  allTopics.value.findIndex(topic => topic.id === props.currentTopic.id)
)

const hasPreviousTopic = computed(() => currentTopicIndex.value > 0)
const hasNextTopic = computed(() => currentTopicIndex.value < allTopics.value.length - 1)

// Methods
const selectTopic = (topic) => {
  emit('selectTopic', topic)
  emit('close')
}

const goToPreviousTopic = () => {
  if (hasPreviousTopic.value) {
    const previousTopic = allTopics.value[currentTopicIndex.value - 1]
    selectTopic(previousTopic)
  }
}

const goToNextTopic = () => {
  if (hasNextTopic.value) {
    const nextTopic = allTopics.value[currentTopicIndex.value + 1]
    selectTopic(nextTopic)
  }
}

const getStatusClass = (status) => {
  const classes = {
    draft: 'bg-gray-100 text-gray-800',
    active: 'bg-emerald-100 text-emerald-800',
    paused: 'bg-amber-100 text-amber-800',
    completed: 'bg-teal-100 text-teal-800',
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}
</script>

<style scoped>
/* Smooth transitions for mobile */
.slide-enter-active,
.slide-leave-active {
  transition: transform 0.3s ease-in-out;
}

.slide-enter-from,
.slide-leave-to {
  transform: translateX(-100%);
}
</style>
