<!-- resources/js/Pages/Admin/Courses/Create.vue -->
<template>
  <AdminLayout>
    <Head title="Create Public Course" />

    <div class="py-6">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-2xl font-bold text-gray-900">Create Public Course</h1>
              <p class="mt-1 text-sm text-gray-600">
                Create a course that students can enroll in. AI will generate the course structure.
              </p>
            </div>
            <Link
              :href="route('admin.catalog.courses.index')"
              class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
            >
              <ArrowLeftIcon class="h-4 w-4 mr-2" />
              Back to Courses
            </Link>
          </div>
        </div>

        <!-- Course Generation Progress Overlay -->
        <Transition
          enter-active-class="duration-300 ease-out"
          enter-from-class="opacity-0"
          enter-to-class="opacity-100"
          leave-active-class="duration-200 ease-in"
          leave-from-class="opacity-100"
          leave-to-class="opacity-0"
        >
          <div
            v-if="isGenerating"
            class="fixed inset-0 z-50 flex items-center justify-center bg-white/95 backdrop-blur-sm"
          >
            <div class="max-w-md mx-auto px-6 text-center">
              <!-- Loading Animation -->
              <div class="relative mb-8">
                <div class="h-2 w-full bg-emerald-100 rounded-full overflow-hidden">
                  <div
                    class="h-full bg-gradient-to-r from-emerald-500 to-teal-600 rounded-full transition-all duration-300 ease-linear"
                    :style="{ width: `${generationProgress}%` }"
                  ></div>
                </div>
                <div class="mt-2 text-sm text-emerald-600 font-medium">
                  {{ Math.round(generationProgress) }}% Complete
                </div>
              </div>

              <!-- AI Animation -->
              <div class="mb-6">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-br from-emerald-100 to-teal-100 border-4 border-white shadow-lg">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-emerald-600 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                  </svg>
                </div>
              </div>

              <!-- Step Display -->
              <div class="mb-8">
                <div class="inline-flex items-center px-4 py-2 bg-emerald-100 rounded-full mb-4">
                  <span class="text-sm font-semibold text-emerald-800">
                    Step {{ currentStepIndex + 1 }} of {{ generationSteps.length }}
                  </span>
                </div>

                <h2 class="text-2xl font-bold text-gray-900 mb-3">
                  {{ currentStep.title }}
                </h2>

                <p class="text-gray-600">
                  {{ currentStep.description }}
                </p>
              </div>

              <!-- Estimated Time -->
              <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mb-6">
                <div class="flex items-center justify-center">
                  <ClockIcon class="h-5 w-5 text-emerald-500 mr-2" />
                  <span class="text-sm text-emerald-700">
                    Estimated time: {{ estimatedTimeRemaining }}
                  </span>
                </div>
              </div>

              <!-- Status Message -->
              <div class="text-sm text-gray-500 italic">
                <span class="text-emerald-600 font-medium">Status:</span>
                {{ currentStatus }}
              </div>
            </div>
          </div>
        </Transition>

        <!-- Form -->
        <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
          <form @submit.prevent="submit">
            <div class="p-6 space-y-8">
              <!-- Course Basics Section -->
              <div class="space-y-6">
                <div class="border-b border-gray-200 pb-4">
                  <h2 class="text-lg font-semibold text-gray-900">Course Basics</h2>
                  <p class="mt-1 text-sm text-gray-500">Basic information about your course</p>
                </div>

                <!-- Course Title -->
                <div>
                  <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                    Course Title *
                  </label>
                  <input
                    id="title"
                    v-model="form.title"
                    type="text"
                    required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                    placeholder="e.g., Introduction to Web Development"
                  />
                  <p v-if="form.errors.title" class="mt-1 text-sm text-red-600">
                    {{ form.errors.title }}
                  </p>
                </div>

                <!-- Subject -->
                <div>
                  <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">
                    Subject *
                  </label>
                  <div class="mt-1">
                    <input
                      id="subject"
                      v-model="form.subject"
                      type="text"
                      required
                      list="subjects"
                      class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                      placeholder="e.g., Computer Science, Mathematics"
                    />
                    <datalist id="subjects">
                      <option v-for="subject in subjects" :key="subject" :value="subject" />
                    </datalist>
                  </div>
                  <p v-if="form.errors.subject" class="mt-1 text-sm text-red-600">
                    {{ form.errors.subject }}
                  </p>
                </div>

                <!-- Exam Board -->
                <div>
                  <label for="exam_board_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Exam Board (Optional)
                  </label>
                  <select
                    id="exam_board_id"
                    v-model="form.exam_board_id"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                  >
                    <option value="">No specific exam board</option>
                    <option
                      v-for="examBoard in exam_boards"
                      :key="examBoard.id"
                      :value="examBoard.id"
                    >
                      {{ examBoard.name }}
                    </option>
                  </select>
                  <p class="mt-1 text-sm text-gray-500">
                    Selecting an exam board will tailor content to specific syllabus requirements
                  </p>
                </div>
              </div>

              <!-- Course Details Section -->
              <div class="space-y-6">
                <div class="border-b border-gray-200 pb-4">
                  <h2 class="text-lg font-semibold text-gray-900">Course Details</h2>
                  <p class="mt-1 text-sm text-gray-500">Configure course settings and requirements</p>
                </div>

                <!-- Level -->
                <div>
                  <label for="level" class="block text-sm font-medium text-gray-700 mb-1">
                    Course Level *
                  </label>
                  <select
                    id="level"
                    v-model="form.level"
                    required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                  >
                    <option value="">Select course level</option>
                    <option
                      v-for="(label, value) in levels"
                      :key="value"
                      :value="value"
                    >
                      {{ label }}
                    </option>
                  </select>
                  <p v-if="form.errors.level" class="mt-1 text-sm text-red-600">
                    {{ form.errors.level }}
                  </p>
                </div>

                <!-- Visibility -->
                <div>
                  <label for="visibility" class="block text-sm font-medium text-gray-700 mb-1">
                    Visibility *
                  </label>
                  <div class="mt-1 grid grid-cols-1 md:grid-cols-3 gap-3">
                    <label
                      v-for="option in visibilityOptions"
                      :key="option.value"
                      :class="[
                        'relative flex cursor-pointer rounded-lg border p-4 focus:outline-none',
                        form.visibility === option.value
                          ? 'border-emerald-500 bg-emerald-50 ring-2 ring-emerald-500'
                          : 'border-gray-300'
                      ]"
                    >
                      <input
                        type="radio"
                        :value="option.value"
                        v-model="form.visibility"
                        class="sr-only"
                      />
                      <div class="flex flex-1">
                        <div class="flex flex-col">
                          <span class="flex items-center text-sm font-medium text-gray-900">
                            {{ option.label }}
                            <CheckCircleIcon
                              v-if="form.visibility === option.value"
                              class="ml-2 h-5 w-5 text-emerald-600"
                            />
                          </span>
                          <span class="mt-1 text-xs text-gray-500">
                            {{ option.description }}
                          </span>
                        </div>
                      </div>
                    </label>
                  </div>
                  <p v-if="form.errors.visibility" class="mt-1 text-sm text-red-600">
                    {{ form.errors.visibility }}
                  </p>
                </div>

                <!-- Enrollment Limit -->
                <div>
                  <label for="enrollment_limit" class="block text-sm font-medium text-gray-700 mb-1">
                    Enrollment Limit (Optional)
                  </label>
                  <input
                    id="enrollment_limit"
                    v-model="form.enrollment_limit"
                    type="number"
                    min="1"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                    placeholder="e.g., 50 (leave empty for unlimited)"
                  />
                  <p class="mt-1 text-sm text-gray-500">
                    Maximum number of students who can enroll. Leave empty for unlimited enrollment.
                  </p>
                </div>

                <!-- Estimated Duration -->
                <div>
                  <label for="estimated_duration_hours" class="block text-sm font-medium text-gray-700 mb-1">
                    Estimated Duration (Hours)
                  </label>
                  <input
                    id="estimated_duration_hours"
                    v-model="form.estimated_duration_hours"
                    type="number"
                    min="1"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                    placeholder="e.g., 40"
                  />
                  <p class="mt-1 text-sm text-gray-500">
                    Approximate total study time for students to complete the course
                  </p>
                </div>

                <!-- Target Completion Date -->
                <div>
                  <label for="target_completion_date" class="block text-sm font-medium text-gray-700 mb-1">
                    Suggested Completion Timeline
                  </label>
                  <input
                    id="target_completion_date"
                    v-model="form.target_completion_date"
                    type="date"
                    :min="minDate"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                  />
                  <p class="mt-1 text-sm text-gray-500">
                    Suggested date for students to aim for completion (optional)
                  </p>
                </div>
              </div>

              <!-- Course Content Section -->
              <div class="space-y-6">
                <div class="border-b border-gray-200 pb-4">
                  <h2 class="text-lg font-semibold text-gray-900">Course Content</h2>
                  <p class="mt-1 text-sm text-gray-500">Define what students will learn</p>
                </div>

                <!-- Thumbnail Upload -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Course Thumbnail
                  </label>
                  <div class="mt-1 flex items-center">
                    <div class="flex-shrink-0 h-32 w-48 border-2 border-dashed border-gray-300 rounded-lg overflow-hidden bg-gray-50 flex items-center justify-center">
                      <div v-if="thumbnailPreview" class="h-full w-full">
                        <img :src="thumbnailPreview" alt="Thumbnail preview" class="h-full w-full object-cover" />
                      </div>
                      <div v-else class="text-center p-4">
                        <PhotographIcon class="mx-auto h-12 w-12 text-gray-400" />
                        <p class="mt-2 text-xs text-gray-500">Upload thumbnail</p>
                        <p class="text-xs text-gray-400">PNG, JPG up to 2MB</p>
                      </div>
                    </div>
                    <div class="ml-4">
                      <input
                        id="thumbnail"
                        ref="thumbnailInput"
                        type="file"
                        accept="image/*"
                        class="sr-only"
                        @change="handleThumbnailChange"
                      />
                      <button
                        type="button"
                        @click="$refs.thumbnailInput.click()"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
                      >
                        <UploadIcon class="h-4 w-4 mr-2" />
                        {{ thumbnailPreview ? 'Change' : 'Upload' }} Thumbnail
                      </button>
                      <button
                        v-if="thumbnailPreview"
                        type="button"
                        @click="removeThumbnail"
                        class="ml-2 inline-flex items-center px-4 py-2 border border-red-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                      >
                        <TrashIcon class="h-4 w-4 mr-2" />
                        Remove
                      </button>
                    </div>
                  </div>
                  <p class="mt-1 text-sm text-gray-500">
                    Recommended size: 800x450 pixels. This will be displayed in the course catalog.
                  </p>
                </div>

                <!-- Description -->
                <div>
                  <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                    Course Description *
                  </label>
                  <textarea
                    id="description"
                    v-model="form.description"
                    rows="4"
                    required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                    placeholder="Describe what students will learn, course objectives, and any prerequisites..."
                  />
                  <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                    {{ form.errors.description }}
                  </p>
                  <p class="mt-1 text-sm text-gray-500">
                    A clear description helps students understand what to expect from the course.
                  </p>
                </div>

                <!-- Learning Objectives -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Learning Objectives
                  </label>
                  <div class="space-y-3">
                    <div
                      v-for="(objective, index) in form.learning_objectives"
                      :key="index"
                      class="flex gap-3"
                    >
                      <div class="flex-1">
                        <input
                          v-model="form.learning_objectives[index]"
                          type="text"
                          class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                          :placeholder="`Learning objective ${index + 1}`"
                        />
                      </div>
                      <button
                        type="button"
                        @click="removeObjective(index)"
                        class="flex-shrink-0 px-3 py-2.5 text-sm text-red-600 hover:text-red-800 border border-red-200 rounded-lg hover:bg-red-50 transition-colors"
                        :disabled="form.learning_objectives.length <= 1"
                      >
                        <TrashIcon class="h-4 w-4" />
                      </button>
                    </div>
                  </div>
                  <button
                    type="button"
                    @click="addObjective"
                    class="mt-3 inline-flex items-center px-4 py-2.5 border border-emerald-300 shadow-sm text-sm font-medium rounded-lg text-emerald-700 bg-white hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-200"
                  >
                    <PlusIcon class="h-4 w-4 mr-1" />
                    Add Learning Objective
                  </button>
                  <p class="mt-1 text-sm text-gray-500">
                    What specific skills or knowledge will students gain from this course?
                  </p>
                </div>

                <!-- Prerequisites -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Prerequisites (Optional)
                  </label>
                  <div class="space-y-3">
                    <div
                      v-for="(prerequisite, index) in form.prerequisites"
                      :key="index"
                      class="flex gap-3"
                    >
                      <div class="flex-1">
                        <input
                          v-model="form.prerequisites[index]"
                          type="text"
                          class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                          :placeholder="`Prerequisite ${index + 1}`"
                        />
                      </div>
                      <button
                        type="button"
                        @click="removePrerequisite(index)"
                        class="flex-shrink-0 px-3 py-2.5 text-sm text-red-600 hover:text-red-800 border border-red-200 rounded-lg hover:bg-red-50 transition-colors"
                        :disabled="form.prerequisites.length <= 1"
                      >
                        <TrashIcon class="h-4 w-4" />
                      </button>
                    </div>
                  </div>
                  <button
                    type="button"
                    @click="addPrerequisite"
                    class="mt-3 inline-flex items-center px-4 py-2.5 border border-emerald-300 shadow-sm text-sm font-medium rounded-lg text-emerald-700 bg-white hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-200"
                  >
                    <PlusIcon class="h-4 w-4 mr-1" />
                    Add Prerequisite
                  </button>
                  <p class="mt-1 text-sm text-gray-500">
                    What knowledge or skills should students have before taking this course?
                  </p>
                </div>
              </div>
            </div>

            <!-- Form Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-xl flex justify-between items-center">
              <div>
                <Link
                  :href="route('admin.catalog.courses.index')"
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
                    Create Course with AI
                  </template>
                </button>
              </div>
            </div>
          </form>
        </div>

        <!-- AI Generation Notice -->
        <div class="mt-6 bg-emerald-50 border border-emerald-200 rounded-xl p-4">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
              </svg>
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-semibold text-emerald-800">
                AI-Powered Course Generation
              </h3>
              <div class="mt-2 text-sm text-emerald-700">
                <p>
                  Our AI will generate a complete course structure including:
                </p>
                <ul class="list-disc list-inside mt-1 space-y-1">
                  <li>Comprehensive modules and topics</li>
                  <li>Learning content and explanations</li>
                  <li>Practice exercises and quizzes</li>
                  <li>Progress tracking structure</li>
                </ul>
                <p class="mt-2 font-semibold">
                  This may take 1-2 minutes. Please don't close this window.
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
import { ref, reactive, computed, onUnmounted } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import {
  ArrowLeftIcon,
  CheckCircleIcon,
  ClockIcon,
  TrashIcon,
  PlusIcon,
  SparklesIcon,
} from '@heroicons/vue/24/outline'

const page = usePage()

const props = defineProps({
  exam_boards: Array,
  subjects: Array,
  levels: Object,
})

const form = reactive({
  title: '',
  subject: '',
  description: '',
  exam_board_id: null,
  level: 'intermediate',
  visibility: 'public',
  enrollment_limit: null,
  target_completion_date: '',
  estimated_duration_hours: 40,
  learning_objectives: [''],
  prerequisites: [''],
  thumbnail: null,
  errors: {},
  processing: false,
})

// Course generation state
const isGenerating = ref(false)
const generationProgress = ref(0)
const currentStepIndex = ref(0)
const currentStatus = ref('Initializing course generation...')
const thumbnailPreview = ref(null)
const thumbnailInput = ref(null)

// Generation steps
const generationSteps = ref([
  {
    title: "Analyzing Course Requirements",
    description: "Processing your course specifications and learning objectives",
    status: "pending",
    estimatedTime: 20,
    progress: 20
  },
  {
    title: "Designing Course Curriculum",
    description: "Creating an optimal learning path with modules and topics",
    status: "pending",
    estimatedTime: 30,
    progress: 40
  },
  {
    title: "Generating Learning Materials",
    description: "Creating engaging content, examples, and explanations",
    status: "pending",
    estimatedTime: 45,
    progress: 60
  },
  {
    title: "Creating Assessments",
    description: "Developing quizzes, exercises, and practice materials",
    status: "pending",
    estimatedTime: 25,
    progress: 80
  },
  {
    title: "Finalizing Course Structure",
    description: "Optimizing and validating the complete learning experience",
    status: "pending",
    estimatedTime: 15,
    progress: 100
  }
])

// Visibility options
const visibilityOptions = [
  {
    value: 'public',
    label: 'Public',
    description: 'Visible to all students in course catalog'
  },
  {
    value: 'private',
    label: 'Private',
    description: 'Only visible to you (for testing)'
  },
  {
    value: 'unlisted',
    label: 'Unlisted',
    description: 'Only accessible via direct link'
  }
]

// Computed properties
const currentStep = computed(() => {
  return generationSteps.value[currentStepIndex.value] || generationSteps.value[0]
})

const estimatedTimeRemaining = computed(() => {
  const remainingSteps = generationSteps.value.slice(currentStepIndex.value)
  const totalSeconds = remainingSteps.reduce((sum, step) => sum + step.estimatedTime, 0)

  if (totalSeconds < 60) {
    return `${totalSeconds} seconds`
  }

  const minutes = Math.floor(totalSeconds / 60)
  const seconds = totalSeconds % 60
  return `${minutes}:${seconds.toString().padStart(2, '0')} minutes`
})

const minDate = computed(() => {
  const tomorrow = new Date()
  tomorrow.setDate(tomorrow.getDate() + 1)
  return tomorrow.toISOString().split('T')[0]
})

// Smart progress simulation
let simulationInterval = null

const startSmartSimulation = () => {
  isGenerating.value = true
  generationProgress.value = 0
  currentStepIndex.value = 0
  currentStatus.value = 'Starting course generation...'

  // Reset all steps
  generationSteps.value.forEach(step => {
    step.status = "pending"
  })

  // Start first step
  if (generationSteps.value[0]) {
    generationSteps.value[0].status = "current"
  }

  simulationInterval = setInterval(() => {
    // Increment progress (capped at 90% until backend completes)
    if (generationProgress.value < 90) {
      generationProgress.value += 0.5
    }

    // Update steps based on progress
    generationSteps.value.forEach((step, index) => {
      if (generationProgress.value >= step.progress && step.status === 'pending') {
        // Complete previous steps
        for (let i = 0; i < index; i++) {
          if (generationSteps.value[i].status === 'current') {
            generationSteps.value[i].status = 'completed'
          }
        }

        // Set current step
        step.status = 'current'
        currentStepIndex.value = index
        currentStatus.value = step.description
      }
    })
  }, 100)
}

const stopSimulation = () => {
  if (simulationInterval) {
    clearInterval(simulationInterval)
    simulationInterval = null
  }
}

// Handle thumbnail upload
const handleThumbnailChange = (event) => {
  const file = event.target.files[0]
  if (!file) return

  // Validate file type
  if (!file.type.match('image.*')) {
    alert('Please select an image file')
    return
  }

  // Validate file size (2MB max)
  if (file.size > 2 * 1024 * 1024) {
    alert('Image size should be less than 2MB')
    return
  }

  form.thumbnail = file

  // Create preview
  const reader = new FileReader()
  reader.onload = (e) => {
    thumbnailPreview.value = e.target.result
  }
  reader.readAsDataURL(file)
}

const removeThumbnail = () => {
  form.thumbnail = null
  thumbnailPreview.value = null
  if (thumbnailInput.value) {
    thumbnailInput.value.value = ''
  }
}

// Form methods
const addObjective = () => {
  form.learning_objectives.push('')
}

const removeObjective = (index) => {
  if (form.learning_objectives.length > 1) {
    form.learning_objectives.splice(index, 1)
  }
}

const addPrerequisite = () => {
  form.prerequisites.push('')
}

const removePrerequisite = (index) => {
  if (form.prerequisites.length > 1) {
    form.prerequisites.splice(index, 1)
  }
}

// Submit form
const submit = async () => {
  form.processing = true

  // Start the smart simulation
  startSmartSimulation()

  try {
    // Create FormData for file upload
    const formData = new FormData()

    // Add all form fields
    Object.keys(form).forEach(key => {
      if (key === 'thumbnail' && form[key]) {
        formData.append(key, form[key])
      } else if (key === 'learning_objectives' || key === 'prerequisites') {
        // Handle arrays
        const filteredArray = form[key].filter(item => item.trim() !== '')
        if (filteredArray.length > 0) {
          filteredArray.forEach((item, index) => {
            formData.append(`${key}[${index}]`, item)
          })
        }
      } else if (key !== 'errors' && key !== 'processing' && form[key] !== null && form[key] !== '') {
        formData.append(key, form[key])
      }
    })

    // Submit the form
    await router.post(route('admin.catalog.courses.store'), formData, {
      preserveScroll: true,
      onSuccess: (response) => {
        // Complete the simulation with smooth finish
        generationProgress.value = 100
        currentStatus.value = 'Course generation complete!'

        // Mark all steps as completed
        generationSteps.value.forEach(step => {
          step.status = 'completed'
        })

        // Brief success display before redirect
        setTimeout(() => {
          stopSimulation()
          isGenerating.value = false
        }, 800)
      },
      onError: (errors) => {
        stopSimulation()
        isGenerating.value = false
        form.processing = false
        form.errors = errors
      },
    })

  } catch (error) {
    // Handle errors gracefully
    stopSimulation()
    isGenerating.value = false
    form.processing = false

    console.error('Course creation failed:', error)

    page.props.flash.error = error.message || 'An error occurred while creating the course. Please try again.'
  }
}

// Save as draft (simplified version without AI generation)
const saveAsDraft = async () => {
  form.processing = true

  try {
    const formData = new FormData()

    // Add basic fields for draft
    Object.keys(form).forEach(key => {
      if (key === 'thumbnail' && form[key]) {
        formData.append(key, form[key])
      } else if (key === 'learning_objectives' || key === 'prerequisites') {
        const filteredArray = form[key].filter(item => item.trim() !== '')
        if (filteredArray.length > 0) {
          filteredArray.forEach((item, index) => {
            formData.append(`${key}[${index}]`, item)
          })
        }
      } else if (key !== 'errors' && key !== 'processing' && form[key] !== null && form[key] !== '') {
        formData.append(key, form[key])
      }
    })

    // Add draft flag
    formData.append('is_draft', true)

    await router.post(route('admin.catalog.courses.store'), formData, {
      preserveScroll: true,
      onSuccess: () => {
        form.processing = false
      },
      onError: (errors) => {
        form.processing = false
        form.errors = errors
      },
    })

  } catch (error) {
    form.processing = false
    console.error('Draft save failed:', error)
    page.props.flash.error = 'Failed to save draft. Please try again.'
  }
}

// Clean up interval on unmount
onUnmounted(() => {
  stopSimulation()
})
</script>

<style scoped>
/* Smooth transitions */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

/* Progress bar animation */
.progress-bar {
  transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
</style>
