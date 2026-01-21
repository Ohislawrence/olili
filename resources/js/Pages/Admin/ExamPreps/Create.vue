
<template>
  <AdminLayout>
    <Head title="Create Exam Prep" />

    <div class="py-6">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-2xl font-bold text-gray-900">Create Exam Preparation</h1>
              <p class="mt-1 text-sm text-gray-600">
                Create a practice exam for students to prepare for their exams
              </p>
            </div>
            <Link
              :href="route('admin.exam-preps.index')"
              class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
            >
              <ArrowLeftIcon class="h-4 w-4 mr-2" />
              Back to Exam Preps
            </Link>
          </div>
        </div>

        <!-- Form -->
        <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
          <form @submit.prevent="submit">
            <div class="p-6 space-y-8">
              <!-- Basic Information Section -->
              <div class="space-y-6">
                <div class="border-b border-gray-200 pb-4">
                  <h2 class="text-lg font-semibold text-gray-900">Basic Information</h2>
                  <p class="mt-1 text-sm text-gray-500">General information about the exam prep</p>
                </div>

                <!-- Exam Prep Name -->
                <div>
                  <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                    Exam Prep Name *
                  </label>
                  <input
                    id="name"
                    v-model="form.name"
                    type="text"
                    required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                    placeholder="e.g., WAEC Mathematics Practice Exam"
                  />
                  <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                    {{ form.errors.name }}
                  </p>
                </div>

                <!-- Description -->
                <div>
                  <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                    Description
                  </label>
                  <textarea
                    id="description"
                    v-model="form.description"
                    rows="3"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                    placeholder="Describe what this exam prep covers..."
                  />
                  <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                    {{ form.errors.description }}
                  </p>
                </div>

                <!-- Exam Board Selection -->
                <div>
                  <label for="exam_board_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Exam Board *
                  </label>
                  <select
                    id="exam_board_id"
                    v-model="form.exam_board_id"
                    required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                  >
                    <option value="">Select an exam board</option>
                    <option
                      v-for="examBoard in examBoards"
                      :key="examBoard.id"
                      :value="examBoard.id"
                    >
                      {{ examBoard.name }}
                    </option>
                  </select>
                  <p v-if="form.errors.exam_board_id" class="mt-1 text-sm text-red-600">
                    {{ form.errors.exam_board_id }}
                  </p>
                </div>

                <!-- Subject Selection -->
                <div>
                  <label for="subject_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Subject (Optional)
                  </label>
                  <select
                    id="subject_id"
                    v-model="form.subject_id"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                  >
                    <option value="">Select a subject (optional)</option>
                    <option
                      v-for="subject in subjects"
                      :key="subject.id"
                      :value="subject.id"
                    >
                      {{ subject.name }}
                    </option>
                  </select>
                  <p class="mt-1 text-sm text-gray-500">
                    Select a specific subject to filter questions
                  </p>
                </div>

                <!-- Course Selection -->
                <div>
                  <label for="course_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Course (Optional)
                  </label>
                  <select
                    id="course_id"
                    v-model="form.course_id"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                  >
                    <option value="">Select a course (optional)</option>
                    <option
                      v-for="course in courses"
                      :key="course.id"
                      :value="course.id"
                    >
                      {{ course.title }}
                    </option>
                  </select>
                  <p class="mt-1 text-sm text-gray-500">
                    Select a specific course to pull questions from
                  </p>
                </div>
              </div>

              <!-- Exam Configuration Section -->
              <div class="space-y-6">
                <div class="border-b border-gray-200 pb-4">
                  <h2 class="text-lg font-semibold text-gray-900">Exam Configuration</h2>
                  <p class="mt-1 text-sm text-gray-500">Configure the exam settings</p>
                </div>

                <!-- Total Questions -->
                <div>
                  <label for="total_questions" class="block text-sm font-medium text-gray-700 mb-1">
                    Total Questions *
                  </label>
                  <input
                    id="total_questions"
                    v-model="form.total_questions"
                    type="number"
                    min="10"
                    max="200"
                    required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                    placeholder="e.g., 50"
                  />
                  <p v-if="form.errors.total_questions" class="mt-1 text-sm text-red-600">
                    {{ form.errors.total_questions }}
                  </p>
                  <p class="mt-1 text-sm text-gray-500">
                    Number of questions in the exam (10-200)
                  </p>
                </div>

                <!-- Time Limit -->
                <div>
                  <label for="time_limit_minutes" class="block text-sm font-medium text-gray-700 mb-1">
                    Time Limit (Minutes) *
                  </label>
                  <input
                    id="time_limit_minutes"
                    v-model="form.time_limit_minutes"
                    type="number"
                    min="10"
                    max="300"
                    required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                    placeholder="e.g., 60"
                  />
                  <p v-if="form.errors.time_limit_minutes" class="mt-1 text-sm text-red-600">
                    {{ form.errors.time_limit_minutes }}
                  </p>
                  <p class="mt-1 text-sm text-gray-500">
                    Total time allowed for the exam (10-300 minutes)
                  </p>
                </div>

                <!-- Passing Score -->
                <div>
                  <label for="passing_score" class="block text-sm font-medium text-gray-700 mb-1">
                    Passing Score (%) *
                  </label>
                  <input
                    id="passing_score"
                    v-model="form.passing_score"
                    type="number"
                    min="1"
                    max="100"
                    required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                    placeholder="e.g., 70"
                  />
                  <p v-if="form.errors.passing_score" class="mt-1 text-sm text-red-600">
                    {{ form.errors.passing_score }}
                  </p>
                  <p class="mt-1 text-sm text-gray-500">
                    Minimum score required to pass the exam (1-100%)
                  </p>
                </div>

                <!-- Max Attempts -->
                <div>
                  <label for="max_attempts" class="block text-sm font-medium text-gray-700 mb-1">
                    Maximum Attempts *
                  </label>
                  <input
                    id="max_attempts"
                    v-model="form.max_attempts"
                    type="number"
                    min="0"
                    required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                    placeholder="e.g., 3 (0 for unlimited)"
                  />
                  <p v-if="form.errors.max_attempts" class="mt-1 text-sm text-red-600">
                    {{ form.errors.max_attempts }}
                  </p>
                  <p class="mt-1 text-sm text-gray-500">
                    Maximum number of attempts allowed (0 = unlimited)
                  </p>
                </div>

                <!-- Exam Settings -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <!-- Randomize Questions -->
                  <div class="flex items-start">
                    <div class="flex items-center h-5">
                      <input
                        id="randomize_questions"
                        v-model="form.randomize_questions"
                        type="checkbox"
                        class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded"
                      />
                    </div>
                    <div class="ml-3">
                      <label for="randomize_questions" class="text-sm font-medium text-gray-700">
                        Randomize Questions
                      </label>
                      <p class="text-sm text-gray-500">
                        Shuffle questions for each attempt
                      </p>
                    </div>
                  </div>

                  <!-- Allow Pause -->
                  <div class="flex items-start">
                    <div class="flex items-center h-5">
                      <input
                        id="allow_pause"
                        v-model="form.allow_pause"
                        type="checkbox"
                        class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded"
                      />
                    </div>
                    <div class="ml-3">
                      <label for="allow_pause" class="text-sm font-medium text-gray-700">
                        Allow Pause
                      </label>
                      <p class="text-sm text-gray-500">
                        Allow students to pause and resume the exam
                      </p>
                    </div>
                  </div>

                  <!-- Show Results Immediately -->
                  <div class="flex items-start">
                    <div class="flex items-center h-5">
                      <input
                        id="show_results_immediately"
                        v-model="form.show_results_immediately"
                        type="checkbox"
                        class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded"
                      />
                    </div>
                    <div class="ml-3">
                      <label for="show_results_immediately" class="text-sm font-medium text-gray-700">
                        Show Results Immediately
                      </label>
                      <p class="text-sm text-gray-500">
                        Show results right after submission
                      </p>
                    </div>
                  </div>

                  <!-- Public Access -->
                  <div class="flex items-start">
                    <div class="flex items-center h-5">
                      <input
                        id="is_public"
                        v-model="form.is_public"
                        type="checkbox"
                        class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded"
                      />
                    </div>
                    <div class="ml-3">
                      <label for="is_public" class="text-sm font-medium text-gray-700">
                        Public Access
                      </label>
                      <p class="text-sm text-gray-500">
                        Make this exam prep available to all students
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Question Criteria Section -->
              <div class="space-y-6">
                <div class="border-b border-gray-200 pb-4">
                  <h2 class="text-lg font-semibold text-gray-900">Question Criteria</h2>
                  <p class="mt-1 text-sm text-gray-500">Filter and distribute questions</p>
                </div>

                <!-- Question Criteria -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-3">
                    Question Filters
                  </label>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Difficulty Filter -->
                    <div>
                      <label for="difficulty_filter" class="block text-sm text-gray-600 mb-1">
                        Difficulty Level
                      </label>
                      <select
                        id="difficulty_filter"
                        v-model="form.question_criteria.difficulty"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                      >
                        <option value="">All Difficulties</option>
                        <option
                          v-for="(label, value) in difficultyLevels"
                          :key="value"
                          :value="value"
                        >
                          {{ label }}
                        </option>
                      </select>
                    </div>

                    <!-- Question Type Filter -->
                    <div>
                      <label for="question_type_filter" class="block text-sm text-gray-600 mb-1">
                        Question Type
                      </label>
                      <select
                        id="question_type_filter"
                        v-model="form.question_criteria.question_type"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                      >
                        <option value="">All Types</option>
                        <option
                          v-for="(label, value) in questionTypes"
                          :key="value"
                          :value="value"
                        >
                          {{ label }}
                        </option>
                      </select>
                    </div>
                  </div>
                </div>

                <!-- Question Distribution -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-3">
                    Question Distribution (Optional)
                  </label>
                  <p class="text-sm text-gray-500 mb-4">
                    Set the number of questions for each difficulty level. Leave empty for random selection.
                  </p>

                  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div
                      v-for="(label, value) in difficultyLevels"
                      :key="value"
                      class="space-y-2"
                    >
                      <label :for="`distribution_${value}`" class="block text-sm text-gray-600">
                        {{ label }}
                      </label>
                      <input
                        :id="`distribution_${value}`"
                        v-model="form.question_distribution[value]"
                        type="number"
                        min="0"
                        :max="form.total_questions"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2 px-3"
                        placeholder="0"
                      />
                    </div>
                  </div>

                  <div class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                    <p class="text-sm text-amber-700">
                      <strong>Note:</strong> Total distributed questions: {{ distributedTotal }}/{{ form.total_questions }}
                      {{ distributionWarning }}
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Form Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-xl flex justify-between items-center">
              <div>
                <Link
                  :href="route('admin.exam-preps.index')"
                  class="inline-flex items-center px-4 py-2.5 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-200"
                >
                  Cancel
                </Link>
              </div>
              <div class="flex space-x-3">
                <button
                  type="button"
                  @click="saveAsDraft"
                  :disabled="form.processing"
                  class="inline-flex items-center px-4 py-2.5 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-200 disabled:opacity-50"
                >
                  Save as Draft
                </button>
                <button
                  type="submit"
                  :disabled="form.processing"
                  class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 border border-transparent rounded-lg font-semibold text-white shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50"
                >
                  <template v-if="form.processing">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Creating...
                  </template>
                  <template v-else>
                    <SparklesIcon class="h-4 w-4 mr-2" />
                    Create Exam Prep
                  </template>
                </button>
              </div>
            </div>
          </form>
        </div>

        <!-- Information Card -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-4">
          <div class="flex">
            <div class="flex-shrink-0">
              <InformationCircleIcon class="h-5 w-5 text-blue-500" />
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-semibold text-blue-800">
                How Questions Are Selected
              </h3>
              <div class="mt-2 text-sm text-blue-700">
                <p class="mb-2">Questions are automatically pulled from your quiz database based on:</p>
                <ul class="list-disc list-inside space-y-1">
                  <li>Selected exam board</li>
                  <li>Subject and course filters (if specified)</li>
                  <li>Question criteria (difficulty, type)</li>
                  <li>Distribution settings (if configured)</li>
                </ul>
                <p class="mt-2">
                  <strong>Note:</strong> The system will generate the exam prep first, then you can review and edit questions before publishing.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import {
  ArrowLeftIcon,
  SparklesIcon,
  InformationCircleIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  examBoards: Array,
  subjects: Array,
  courses: Array,
  difficultyLevels: Object,
  questionTypes: Object,
})

const form = reactive({
  name: '',
  description: '',
  exam_board_id: null,
  subject_id: null,
  course_id: null,
  total_questions: 50,
  time_limit_minutes: 60,
  passing_score: 70,
  max_attempts: 3,
  randomize_questions: true,
  allow_pause: false,
  show_results_immediately: true,
  is_public: false,
  question_criteria: {
    difficulty: '',
    question_type: ''
  },
  question_distribution: {
    easy: null,
    medium: null,
    hard: null
  },
  errors: {},
  processing: false,
})

// Computed properties
const distributedTotal = computed(() => {
  const values = Object.values(form.question_distribution)
    .filter(v => v !== null && v !== '')
    .map(v => parseInt(v) || 0)
  return values.reduce((sum, val) => sum + val, 0)
})

const distributionWarning = computed(() => {
  if (distributedTotal.value === 0) return ''

  if (distributedTotal.value < form.total_questions) {
    const remaining = form.total_questions - distributedTotal.value
    return ` (${remaining} questions will be filled randomly)`
  } else if (distributedTotal.value > form.total_questions) {
    return ' - Warning: Distribution exceeds total questions'
  }

  return ' - Perfect distribution'
})

// Submit form
const submit = async () => {
  form.processing = true

  try {
    await router.post(route('admin.exam-preps.store'), form, {
      preserveScroll: true,
      onError: (errors) => {
        form.processing = false
        form.errors = errors
      },
    })
  } catch (error) {
    form.processing = false
    console.error('Exam prep creation failed:', error)
  }
}

// Save as draft
const saveAsDraft = async () => {
  form.processing = true

  try {
    await router.post(route('admin.exam-preps.store'), {
      ...form,
      is_public: false,
      status: 'draft'
    }, {
      preserveScroll: true,
      onError: (errors) => {
        form.processing = false
        form.errors = errors
      },
    })
  } catch (error) {
    form.processing = false
    console.error('Draft save failed:', error)
  }
}
</script>
