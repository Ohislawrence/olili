<!-- resources/js/Pages/Admin/Courses/Show.vue -->
<template>
  <AdminLayout>
    <Head :title="`Course - ${course.title}`" />

    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex justify-between items-center">
            <div>
              <h1 class="text-3xl font-bold text-gray-900">{{ course.title }}</h1>
              <p class="mt-2 text-gray-600">
                {{ course.description }}
              </p>
              <div class="mt-2 flex items-center space-x-4">
                <span
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
                  :class="getStatusClass(course.status)"
                >
                  {{ course.status }}
                </span>
                <span class="text-sm text-gray-500 capitalize">{{ course.subject }} • {{ course.level }}</span>
              </div>
            </div>
            <div class="flex space-x-3">
              <Link
                :href="route('admin.courses.edit', course.id)"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
              >
                Edit Course
              </Link>
              <Link
                :href="route('admin.courses.index')"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
              >
                Back to Courses
              </Link>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Main Content -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Progress Overview -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Progress Overview</h3>
              </div>
              <div class="p-6">
                <!-- Update all stat references to use optional chaining -->
<div class="flex items-center justify-between mb-4">
  <div>
    <h4 class="text-sm font-medium text-gray-500">Overall Progress</h4>
    <p class="text-2xl font-bold text-gray-900">
      {{ Math.round(course.progress_percentage) }}%
    </p>
  </div>
  <div class="text-right">
    <h4 class="text-sm font-medium text-gray-500">Completed</h4>
    <p class="text-2xl font-bold text-gray-900">
      {{ course.completed_outlines_count || 0 }}/{{ course.outlines_count || 0 }}
    </p>
  </div>
</div>

<!-- Update the flashcards section to be safer -->
<div class="bg-white shadow rounded-lg">
  <div class="px-6 py-4 border-b border-gray-200">
    <div class="flex justify-between items-center">
      <h3 class="text-lg font-semibold text-gray-900">Flashcards</h3>
      <span class="text-sm text-gray-500">
        {{ course.flashcards?.length || 0 }} cards • 
        {{ stats?.due_flashcards || 0 }} due for review
      </span>
    </div>
  </div>
  
  <div class="p-6">
    <!-- Flashcard Sets -->
    <div v-if="course.flashcard_sets?.length" class="mb-6">
      <h4 class="text-md font-medium text-gray-900 mb-3">Flashcard Sets</h4>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div
          v-for="set in course.flashcard_sets"
          :key="set.id"
          class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50"
        >
          <div class="flex justify-between items-start">
            <div>
              <h5 class="font-medium text-gray-900">{{ set.title }}</h5>
              <p class="text-sm text-gray-500 mt-1">{{ set.description }}</p>
            </div>
            <span
              class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
              :class="set.is_public ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
            >
              {{ set.is_public ? 'Public' : 'Private' }}
            </span>
          </div>
          <div class="mt-3 flex items-center text-sm text-gray-500">
            <AcademicCapIcon class="h-4 w-4 mr-1" />
            <span>{{ set.flashcards_count || 0 }} cards</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Individual Flashcards -->
    <div v-if="course.flashcards?.length" class="mb-6">
      <h4 class="text-md font-medium text-gray-900 mb-3">Recent Flashcards</h4>
      <div class="space-y-3">
        <div
          v-for="flashcard in course.flashcards.slice(0, 5)"
          :key="flashcard.id"
          class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50"
        >
          <div class="flex justify-between items-start">
            <div class="flex-1">
              <div class="flex items-center space-x-2 mb-2">
                <span
                  class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium capitalize"
                  :class="getDifficultyClass(flashcard.difficulty_level)"
                >
                  {{ flashcard.difficulty_level || 'medium' }}
                </span>
                <span v-if="flashcard.course_outline" class="text-xs text-gray-500">
                  {{ flashcard.course_outline.title }}
                </span>
              </div>
              
              <div class="mb-3">
                <p class="text-sm font-medium text-gray-900 mb-1">
                  <span class="text-gray-500">Q:</span> {{ flashcard.question || 'No question' }}
                </p>
                <p class="text-sm text-gray-600">
                  <span class="text-gray-500">A:</span> {{ flashcard.answer || 'No answer' }}
                </p>
                <p v-if="flashcard.explanation" class="text-sm text-gray-500 mt-2">
                  {{ flashcard.explanation }}
                </p>
              </div>
            </div>
            
            <div class="ml-4 text-right">
              <div v-if="flashcard.next_review_date" class="text-xs text-gray-500">
                Next review:
                <span
                  :class="isFlashcardDue(flashcard) ? 'text-red-600 font-medium' : 'text-gray-700'"
                >
                  {{ formatDate(flashcard.next_review_date) }}
                </span>
              </div>
              <div v-else class="text-xs text-gray-500">Not studied yet</div>
              
              <div class="mt-2 text-xs text-gray-500">
                <span>Repetitions: {{ flashcard.repetitions || 0 }}</span>
              </div>
            </div>
          </div>
          
          <div class="mt-3 pt-3 border-t border-gray-100 flex justify-between items-center">
            <div class="text-xs text-gray-500">
              Created by: {{ flashcard.user?.name || 'Unknown' }}
            </div>
            <div class="flex space-x-2">
              <span class="text-xs text-gray-400">
                Ease: {{ flashcard.ease_factor || 2.5 }}
              </span>
            </div>
          </div>
        </div>
      </div>
      
      <div v-if="course.flashcards.length > 5" class="mt-4 text-center">
        <button
          @click="viewAllFlashcards"
          class="text-sm text-blue-600 hover:text-blue-800"
        >
          View all {{ course.flashcards.length }} flashcards →
        </button>
      </div>
    </div>

    <!-- No Flashcards Message -->
    <div v-else class="text-center py-8">
      <AcademicCapIcon class="h-12 w-12 mx-auto text-gray-400" />
      <h4 class="mt-2 text-sm font-medium text-gray-900">No Flashcards Yet</h4>
      <p class="mt-1 text-sm text-gray-500">
        Flashcards will appear here once created by students or tutors.
      </p>
    </div>
  </div>
</div>
                <div class="w-full bg-gray-200 rounded-full h-4">
                  <div
                    class="bg-blue-600 h-4 rounded-full transition-all duration-300"
                    :style="{ width: `${course.progress_percentage}%` }"
                  ></div>
                </div>
              </div>
            </div>

            <!-- Course Outline -->
            <div
              v-for="module in course.modules"
              :key="module.id"
              class="mb-6"
            >
              <div class="bg-gray-50 p-4 rounded-lg mb-2">
                <h4 class="font-semibold text-gray-900">{{ module.title }}</h4>
                <p class="text-sm text-gray-600">{{ module.description }}</p>
              </div>
              
              <div class="space-y-4 ml-4">
                <div
                  v-for="topic in module.topics"
                  :key="topic.id"
                  class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50"
                >
                  <div class="flex items-center space-x-4">
                    <div
                      class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center"
                      :class="getOutlineIconClass(topic.type)"
                    >
                      <component
                        :is="getOutlineIcon(topic.type)"
                        class="h-4 w-4 text-white"
                      />
                    </div>
                    <div>
                      <h4 class="text-sm font-medium text-gray-900">{{ topic.title }}</h4>
                      <p class="text-sm text-gray-500">{{ topic.description }}</p>
                      <div class="flex items-center space-x-4 mt-1">
                        <span class="text-xs text-gray-400 capitalize">{{ topic.type }}</span>
                        <span class="text-xs text-gray-400">
                          {{ topic.estimated_duration_minutes }} minutes
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="flex items-center space-x-3">
                    <span
                      v-if="topic.is_completed"
                      class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800"
                    >
                      Completed
                    </span>
                    <span
                      v-else
                      class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                    >
                      Pending
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Learning Objectives -->
            <div v-if="course.learning_objectives?.length" class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Learning Objectives</h3>
              </div>
              <div class="p-6">
                <ul class="space-y-2">
                  <li
                    v-for="(objective, index) in course.learning_objectives"
                    :key="index"
                    class="flex items-start"
                  >
                    <CheckCircleIcon class="h-5 w-5 text-green-500 mr-3 mt-0.5 flex-shrink-0" />
                    <span class="text-sm text-gray-700">{{ objective }}</span>
                  </li>
                </ul>
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="space-y-6">
            <!-- Student Information -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Student Information</h3>
              </div>
              <div class="p-6">
                <div class="flex items-center space-x-3 mb-4">
                  <div class="flex-shrink-0">
                    <div class="h-10 w-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                      {{ course.student_profile.user.name.charAt(0).toUpperCase() }}
                    </div>
                  </div>
                  <div>
                    <h4 class="text-sm font-medium text-gray-900">
                      {{ course.student_profile.user.name }}
                    </h4>
                    <p class="text-sm text-gray-500">
                      {{ course.student_profile.user.email }}
                    </p>
                  </div>
                </div>
                <dl class="space-y-2">
                  <div>
                    <dt class="text-xs text-gray-500">Current Level</dt>
                    <dd class="text-sm text-gray-900 capitalize">
                      {{ course.student_profile.current_level }}
                    </dd>
                  </div>
                  <div>
                    <dt class="text-xs text-gray-500">Target Level</dt>
                    <dd class="text-sm text-gray-900 capitalize">
                      {{ course.student_profile.target_level }}
                    </dd>
                  </div>
                  <div>
                    <dt class="text-xs text-gray-500">Weekly Study Hours</dt>
                    <dd class="text-sm text-gray-900">
                      {{ course.student_profile.weekly_study_hours }} hours
                    </dd>
                  </div>
                </dl>
              </div>
            </div>

            <!-- Course Details -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Course Details</h3>
              </div>
              <div class="p-6">
                <dl class="space-y-3">
                  <div>
                    <dt class="text-xs text-gray-500">Start Date</dt>
                    <dd class="text-sm text-gray-900">
                      {{ formatDate(course.start_date) }}
                    </dd>
                  </div>
                  <div>
                    <dt class="text-xs text-gray-500">Target Completion</dt>
                    <dd class="text-sm text-gray-900">
                      {{ formatDate(course.target_completion_date) }}
                    </dd>
                  </div>
                  <div>
                    <dt class="text-xs text-gray-500">Estimated Duration</dt>
                    <dd class="text-sm text-gray-900">
                      {{ course.estimated_duration_hours }} hours
                    </dd>
                  </div>
                  <div>
                    <dt class="text-xs text-gray-500">AI Model Used</dt>
                    <dd class="text-sm text-gray-900">
                      {{ course.ai_model_used }}
                    </dd>
                  </div>
                  <div v-if="course.exam_board">
                    <dt class="text-xs text-gray-500">Exam Board</dt>
                    <dd class="text-sm text-gray-900">
                      {{ course.exam_board.name }}
                    </dd>
                  </div>
                </dl>
              </div>
            </div>
  
            <!-- Quick Actions -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
              </div>
              <div class="p-6 space-y-3">
                <button
                  @click="regenerateOutline"
                  class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                >
                  <ArrowPathIcon class="h-4 w-4 mr-2" />
                  Regenerate Outline
                </button>
                <button
                  v-if="course.status !== 'completed'"
                  @click="markAsCompleted"
                  class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700"
                >
                  <CheckCircleIcon class="h-4 w-4 mr-2" />
                  Mark as Completed
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import {
  AcademicCapIcon,
  DocumentTextIcon,
  QuestionMarkCircleIcon,
  CheckCircleIcon,
  ArrowPathIcon,
} from '@heroicons/vue/24/outline'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  course: Object,
})

const getStatusClass = (status) => {
  const classes = {
    active: 'bg-green-100 text-green-800',
    completed: 'bg-blue-100 text-blue-800',
    draft: 'bg-gray-100 text-gray-800',
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getOutlineIcon = (type) => {
  const icons = {
    module: AcademicCapIcon,
    topic: DocumentTextIcon,
    quiz: QuestionMarkCircleIcon,
  }
  return icons[type] || DocumentTextIcon
}

const getOutlineIconClass = (type) => {
  const classes = {
    module: 'bg-blue-500',
    topic: 'bg-green-500',
    quiz: 'bg-purple-500',
  }
  return classes[type] || 'bg-gray-500'
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

const regenerateOutline = () => {
  if (confirm('Are you sure you want to regenerate the course outline? This will replace the current outline.')) {
    router.post(route('admin.courses.regenerate-outline', props.course.id))
  }
}

const markAsCompleted = () => {
  if (confirm('Mark this course as completed?')) {
    router.patch(route('admin.courses.mark-completed', props.course.id))
  }
}

const getDifficultyClass = (level) => {
  const classes = {
    easy: 'bg-green-100 text-green-800',
    medium: 'bg-yellow-100 text-yellow-800',
    hard: 'bg-red-100 text-red-800',
  }
  return classes[level] || 'bg-gray-100 text-gray-800'
}

const isFlashcardDue = (flashcard) => {
  if (!flashcard.next_review_date) return true
  const nextReview = new Date(flashcard.next_review_date)
  return nextReview <= new Date()
}

const viewAllFlashcards = () => {
  // You might want to navigate to a flashcards page or show a modal
  alert(`Showing all flashcards for ${props.course.title}`)
  // Or use router to navigate to flashcards page:
  // router.visit(route('admin.courses.flashcards', props.course.id))
}
</script>
