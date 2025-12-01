<template>
  <StudentLayout :disable-global-blur="isGenerating">
    <Head title="Create Course" />

    <div class="py-6">
      <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-2xl font-bold text-gray-900">Create New Course</h1>
          <p class="mt-1 text-sm text-gray-600">
            Create a personalized learning path with Oli AI assistance
          </p>
        </div>

        <!-- Course Generation Progress Overlay -->
        <div
          v-if="isGenerating"
          class="fixed inset-0 z-50 flex items-center justify-center bg-white/95 backdrop-blur-sm transition-all duration-300"
        >
          <div class="max-w-md mx-auto px-6 text-center">
            <!-- Loading Animation -->
            <div class="relative mb-8">
              <div class="h-2 w-full bg-emerald-100 rounded-full overflow-hidden">
                <div 
                  class="h-full bg-gradient-to-r from-emerald-500 to-teal-600 rounded-full transition-all duration-500"
                  :style="{ width: `${generationProgress}%` }"
                ></div>
              </div>
              <div class="mt-2 text-sm text-emerald-600 font-medium">
                {{ Math.round(generationProgress) }}% Complete
              </div>
            </div>

            <!-- AI Icon with subtle animation -->
            <div class="mb-6">
              <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-br from-emerald-100 to-teal-100 border-4 border-white shadow-lg animate-pulse">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                </svg>
              </div>
            </div>

            <!-- Current Step Display -->
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

            <!-- Dynamic Step Indicator (Smaller, sequential display) -->
            <div class="mb-8">
              <div class="flex items-center justify-center space-x-2 mb-4">
                <div 
                  v-for="(step, index) in generationSteps" 
                  :key="index"
                  class="transition-all duration-500"
                  :class="{
                    'opacity-100': step.status === 'current',
                    'opacity-40': step.status !== 'current'
                  }"
                >
                  <div 
                    class="w-3 h-3 rounded-full"
                    :class="{
                      'bg-emerald-500': step.status === 'current',
                      'bg-emerald-300': step.status === 'completed',
                      'bg-gray-300': step.status === 'pending'
                    }"
                  ></div>
                </div>
              </div>
              
              <!-- Current Step Card -->
              <div 
                class="bg-white border border-emerald-200 rounded-xl p-4 shadow-sm transition-all duration-300"
                :class="{
                  'animate-pulse': generationProgress < 100
                }"
              >
                <div class="flex items-center justify-between">
                  <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center mr-3">
                      <span class="text-emerald-600 font-semibold text-sm">
                        {{ currentStepIndex + 1 }}
                      </span>
                    </div>
                    <div>
                      <h3 class="font-semibold text-gray-900">{{ currentStep.title }}</h3>
                      <p class="text-sm text-gray-500">{{ currentStep.estimatedTime }}</p>
                    </div>
                  </div>
                  <div v-if="currentStep.status === 'completed'" class="text-emerald-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                  </div>
                </div>
              </div>
            </div>

            <!-- Previous Completed Steps (Collapsible) -->
            <div class="mb-6">
              <button 
                @click="showCompletedSteps = !showCompletedSteps"
                class="text-sm text-gray-500 hover:text-gray-700 flex items-center justify-center mx-auto"
              >
                <span>{{ showCompletedSteps ? 'Hide' : 'Show' }} completed steps</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 transition-transform duration-300" :class="{ 'transform rotate-180': showCompletedSteps }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>
              
              <div 
                v-if="showCompletedSteps"
                class="mt-3 space-y-2 max-h-40 overflow-y-auto transition-all duration-300"
              >
                <div 
                  v-for="(step, index) in completedSteps" 
                  :key="index"
                  class="bg-gray-50 border border-gray-200 rounded-lg p-3 opacity-75"
                >
                  <div class="flex items-center justify-between">
                    <div class="flex items-center">
                      <div class="w-6 h-6 rounded-full bg-emerald-100 flex items-center justify-center mr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                      </div>
                      <span class="text-sm font-medium text-gray-700">{{ step.title }}</span>
                    </div>
                    <span class="text-xs text-gray-500">{{ step.estimatedTime }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Important Notice -->
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6">
              <div class="flex">
                <div class="flex-shrink-0">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.998-.833-2.732 0L4.252 16.5c-.77.833.192 2.5 1.732 2.5z" />
                  </svg>
                </div>
                <div class="ml-3">
                  <h3 class="text-sm font-semibold text-amber-800">
                    Please stay on this page
                  </h3>
                  <p class="mt-1 text-sm text-amber-700">
                    The course is being generated with Oli AI. Leaving this page will interrupt the process.
                  </p>
                </div>
              </div>
            </div>

            <!-- Fun Fact with fade animation -->
            <div 
              class="text-sm text-gray-500 italic transition-opacity duration-500"
              :class="{ 'opacity-0': funFactChanging }"
            >
              <span class="text-emerald-600 font-medium">Did you know:</span>
              {{ currentFunFact }}
            </div>
          </div>
        </div>

        <!-- Form -->
        <div class="bg-white shadow-sm rounded-xl border border-gray-100">
          <form @submit.prevent="submit">
            <div class="p-6 space-y-6">
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
                  placeholder="e.g., Advanced Calculus for Engineering"
                />
                <p v-if="form.errors.title" class="mt-1 text-sm text-red-600">
                  {{ form.errors.title }}
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
                  @change="onExamBoardChange"
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
                  Selecting an exam board will show relevant subjects for that board
                </p>
              </div>

              <!-- Subject - Dynamic Dropdown based on Exam Board -->
              <div>
                <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">
                  Subject *
                </label>
                <div class="mt-1 relative">
                  <div class="relative">
                    <input
                      id="subject"
                      v-model="subjectInput"
                      type="text"
                      required
                      class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 pr-10 py-2.5 px-3"
                      :placeholder="subjectPlaceholder"
                      @focus="showDropdown = true"
                      @input="filterSubjects"
                      @blur="onSubjectBlur"
                      :disabled="availableSubjects.length === 0"
                    />
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 cursor-pointer" fill="none" viewBox="0 0 24 24" stroke="currentColor" :class="{ 'transform rotate-180': showDropdown }" @click="showDropdown = !showDropdown">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                      </svg>
                    </div>
                  </div>

                  <!-- Dropdown for subjects -->
                  <div
                    v-if="showDropdown && filteredSubjects.length > 0"
                    class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-lg py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm scrollbar-custom"
                  >
                    <div
                      v-for="subject in filteredSubjects"
                      :key="subject"
                      class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-emerald-50"
                      :class="{ 'bg-emerald-50 text-emerald-700': subjectInput === subject }"
                      @click="selectSubject(subject)"
                    >
                      <span class="block truncate font-medium">{{ subject }}</span>
                      <span
                        v-if="subjectInput === subject"
                        class="absolute inset-y-0 right-0 flex items-center pr-4 text-emerald-600"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                      </span>
                    </div>

                    <!-- Custom subject option (only shown when no exam board is selected) -->
                    <div
                      v-if="!form.exam_board_id && subjectInput && !filteredSubjects.includes(subjectInput)"
                      class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-emerald-50 border-t border-gray-100"
                      @click="selectCustomSubject"
                    >
                      <span class="block truncate font-medium text-emerald-600">
                        Use "{{ subjectInput }}" as custom subject
                      </span>
                    </div>
                  </div>

                  <!-- No subjects available message -->
                  <div
                    v-if="showDropdown && availableSubjects.length === 0"
                    class="absolute z-10 mt-1 w-full bg-white shadow-lg rounded-lg py-3 px-3 text-sm text-gray-500 border border-gray-200"
                  >
                    Please select an exam board first to see available subjects
                  </div>
                </div>
                <p v-if="form.errors.subject" class="mt-1 text-sm text-red-600">
                  {{ form.errors.subject }}
                </p>
                <p class="mt-1 text-sm text-gray-500" v-if="selectedExamBoardName">
                  Subjects for: {{ selectedExamBoardName }}
                </p>
              </div>

              <!-- Target Level -->
              <div>
                <label for="target_level" class="block text-sm font-medium text-gray-700 mb-1">
                  Target Level *
                </label>
                <select
                  id="target_level"
                  v-model="form.target_level"
                  required
                  class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                >
                  <option value="">Select target level</option>
                  <option
                    v-for="(label, value) in student_levels"
                    :key="value"
                    :value="value"
                  >
                    {{ label }}
                  </option>
                </select>
                <p class="mt-1 text-sm text-gray-500">
                  Current level: {{ student_profile?.current_level }}
                </p>
              </div>

              <!-- Weekly Study Hours -->
              <div>
                <label for="weekly_study_hours" class="block text-sm font-medium text-gray-700 mb-1">
                  Weekly Study Hours *
                </label>
                <input
                  id="weekly_study_hours"
                  v-model="form.weekly_study_hours"
                  type="number"
                  min="1"
                  max="40"
                  required
                  class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                  placeholder="e.g., 10"
                />
                <p class="mt-1 text-sm text-gray-500">
                  How many hours per week can you dedicate to this course?
                </p>
              </div>

              <!-- Target Completion Date -->
              <div>
                <label for="target_completion_date" class="block text-sm font-medium text-gray-700 mb-1">
                  Target Completion Date *
                </label>
                <input
                  id="target_completion_date"
                  v-model="form.target_completion_date"
                  type="date"
                  required
                  :min="minDate"
                  class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                />
                <p class="mt-1 text-sm text-gray-500">
                  When do you aim to complete this course?
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
                  rows="4"
                  class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                  placeholder="Describe what you want to learn and your goals..."
                />
                <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                  {{ form.errors.description }}
                </p>
              </div>

              <!-- Learning Objectives -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Learning Objectives (Optional)
                </label>
                <div
                  v-for="(objective, index) in form.learning_objectives"
                  :key="index"
                  class="flex gap-2 mb-2"
                >
                  <input
                    v-model="form.learning_objectives[index]"
                    type="text"
                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                    placeholder="What do you want to achieve?"
                  />
                  <button
                    type="button"
                    @click="removeObjective(index)"
                    class="flex-shrink-0 px-3 py-2.5 text-sm text-red-600 hover:text-red-800 border border-red-200 rounded-lg hover:bg-red-50 transition-colors"
                  >
                    Remove
                  </button>
                </div>
                <button
                  type="button"
                  @click="addObjective"
                  class="inline-flex items-center px-4 py-2.5 border border-emerald-300 shadow-sm text-sm font-medium rounded-lg text-emerald-700 bg-white hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                  </svg>
                  Add Objective
                </button>
              </div>


            </div>

            <!-- Form Actions -->
            <div class="px-6 py-4 bg-gray-50 rounded-b-xl flex justify-between items-center">
              <Link
                :href="route('student.courses.index')"
                class="inline-flex items-center px-4 py-2.5 border border-emerald-300 shadow-sm text-sm font-medium rounded-lg text-emerald-700 bg-white hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200"
              >
                Cancel
              </Link>
              <button
                type="submit"
                :disabled="form.processing || !form.subject"
                class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 border border-transparent rounded-lg font-semibold text-white shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50"
              >
                <span v-if="form.processing">
                  <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Creating...
                </span>
                <span v-else>Create Course</span>
              </button>
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
                Olilearn Course Generation
              </h3>
              <div class="mt-2 text-sm text-emerald-700">
                <p>
                  Olilearn AI will create a personalized learning path based on:
                </p>
                <ul class="list-disc list-inside mt-1 space-y-1">
                  <li>Your current and target levels</li>
                  <li>Weekly study hours and completion timeline</li>
                  <li>Learning objectives and preferences</li>
                  <li>Subject-specific requirements</li>
                </ul>
                <p class="mt-2 font-semibold">
                  This may take a few moments as we generate your personalized course outline.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </StudentLayout>
</template>

<script setup>
import { reactive, ref, computed, onMounted, watch, onUnmounted } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import axios from 'axios'

const page = usePage()

const props = defineProps({
  student_profile: Object,
  exam_boards: Array,
  subjects: Array,
  student_levels: Object,
})

const form = reactive({
  title: '',
  subject: '',
  description: '',
  exam_board_id: '',
  target_level: props.student_profile?.target_level || 'intermediate',
  weekly_study_hours: props.student_profile?.weekly_study_hours || 10,
  target_completion_date: '',
  learning_objectives: [''],
  errors: {},
  processing: false,
})

// Course generation state
const isGenerating = ref(false)
const generationProgress = ref(0)
const currentStepIndex = ref(0)
const showCompletedSteps = ref(false)
const funFactChanging = ref(false)

// Fun facts to show during generation
const funFacts = ref([
  "AI is analyzing your learning preferences...",
  "Generating personalized modules based on your level...",
  "Creating interactive content optimized for retention...",
  "Structuring topics in the most effective learning sequence...",
  "Adding quizzes and assessments for better reinforcement...",
  "Finalizing your personalized learning journey..."
])

const currentFunFact = ref(funFacts.value[0])

// Generation steps (shorter, more focused)
const generationSteps = ref([
  {
    title: "Analyzing Requirements",
    description: "Understanding your learning goals and preferences",
    status: "pending",
    estimatedTime: "30s",
    progress: 20
  },
  {
    title: "Designing Course Structure",
    description: "Creating modules and topics for optimal learning flow",
    status: "pending",
    estimatedTime: "30s",
    progress: 40
  },
  {
    title: "Generating Learning Content",
    description: "Creating engaging text, examples, and explanations",
    status: "pending",
    estimatedTime: "60s",
    progress: 60
  },
  {
    title: "Adding Assessments",
    description: "Creating quizzes and practice exercises",
    status: "pending",
    estimatedTime: "30s",
    progress: 80
  },
  {
    title: "Finalizing Course",
    description: "Reviewing and optimizing your learning path",
    status: "pending",
    estimatedTime: "30s",
    progress: 100
  }
])

// Computed properties
const currentStep = computed(() => {
  return generationSteps.value[currentStepIndex.value] || generationSteps.value[0]
})

const completedSteps = computed(() => {
  return generationSteps.value.filter(step => step.status === 'completed')
})

// Simulate progress (this would be replaced with actual progress from your API)
let progressInterval

const startGenerationSimulation = () => {
  isGenerating.value = true
  generationProgress.value = 0
  currentStepIndex.value = 0
  currentFunFact.value = funFacts.value[0]
  showCompletedSteps.value = false
  
  // Reset all steps to pending
  generationSteps.value.forEach(step => {
    step.status = "pending"
  })
  
  // Mark first step as current
  if (generationSteps.value[0]) {
    generationSteps.value[0].status = "current"
  }

  let factIndex = 0
  
  progressInterval = setInterval(() => {
    // Update progress
    if (generationProgress.value < 100) {
      generationProgress.value += 0.7 // Slightly faster progress
      
      // Update fun facts with fade effect
      if (generationProgress.value % 20 < 0.7 && factIndex < funFacts.value.length - 1) {
        factIndex++
        funFactChanging.value = true
        setTimeout(() => {
          currentFunFact.value = funFacts.value[factIndex]
          funFactChanging.value = false
        }, 300)
      }
      
      // Update steps based on progress
      generationSteps.value.forEach((step, index) => {
        if (generationProgress.value >= step.progress) {
          // Mark all previous steps as completed
          for (let i = 0; i <= index; i++) {
            generationSteps.value[i].status = "completed"
          }
          
          // Mark next step as current if exists
          if (index + 1 < generationSteps.value.length) {
            generationSteps.value[index + 1].status = "current"
            currentStepIndex.value = index + 1
          } else {
            currentStepIndex.value = index
          }
        }
      })
    } else {
      // Generation complete
      generationSteps.value.forEach(step => {
        step.status = "completed"
      })
      currentStepIndex.value = generationSteps.value.length - 1
      
      // Show completion message briefly before redirect
      setTimeout(() => {
        if (progressInterval) {
          clearInterval(progressInterval)
        }
      }, 1500)
    }
  }, 100)
}

// Update the submit function to use axios to prevent Inertia navigation blur
const submit = () => {
  form.processing = true
  
  // Start the generation simulation
  startGenerationSimulation()

  // Use axios to prevent Inertia's loading blur
  axios.post(route('student.courses.store'), {
    title: form.title,
    subject: form.subject,
    description: form.description,
    exam_board_id: form.exam_board_id,
    target_level: form.target_level,
    weekly_study_hours: form.weekly_study_hours,
    target_completion_date: form.target_completion_date,
    learning_objectives: form.learning_objectives.filter(obj => obj.trim() !== ''),
  })
  .then(response => {
    // Complete the generation progress
    generationProgress.value = 100
    
    // Show completion for a moment, then redirect
    setTimeout(() => {
      if (response.data.redirect) {
        router.visit(response.data.redirect)
      } else if (response.data.course_id) {
        router.visit(route('student.courses.show', response.data.course_id))
      }
    }, 1500)
  })
  .catch(error => {
    // Handle errors
    form.errors = error.response?.data?.errors || {}
    form.processing = false
    isGenerating.value = false
    clearInterval(progressInterval)
    
    if (error.response?.data?.error) {
      page.props.flash.error = error.response.data.error
    } else if (error.response?.data?.message) {
      page.props.flash.error = error.response.data.message
    }
  })
}

// Clean up interval on unmount
onUnmounted(() => {
  if (progressInterval) {
    clearInterval(progressInterval)
  }
})

// Searchable dropdown state
const subjectInput = ref('')
const showDropdown = ref(false)
const searchQuery = ref('')

// Available subjects based on exam board selection
const availableSubjects = ref([...props.subjects])

// Set minimum date for completion date
const minDate = computed(() => {
  const tomorrow = new Date()
  tomorrow.setDate(tomorrow.getDate() + 1)
  return tomorrow.toISOString().split('T')[0]
})

// Get selected exam board name
const selectedExamBoardName = computed(() => {
  if (!form.exam_board_id) return ''
  const examBoard = props.exam_boards.find(eb => eb.id == form.exam_board_id)
  return examBoard ? examBoard.name : ''
})

// Dynamic placeholder for subject input
const subjectPlaceholder = computed(() => {
  if (form.exam_board_id) {
    return `Select a subject for ${selectedExamBoardName.value}`
  }
  return "Search or select a subject"
})

// Filter subjects based on search input
const filteredSubjects = computed(() => {
  if (!searchQuery.value) {
    return availableSubjects.value
  }

  const query = searchQuery.value.toLowerCase()
  return availableSubjects.value.filter(subject =>
    subject.toLowerCase().includes(query)
  )
})

// Handle exam board change
const onExamBoardChange = () => {
  // Reset subject when exam board changes
  form.subject = ''
  subjectInput.value = ''
  showDropdown.value = false

  if (form.exam_board_id) {
    // Get subjects for the selected exam board
    const examBoard = props.exam_boards.find(eb => eb.id == form.exam_board_id)
    availableSubjects.value = examBoard?.subjects || []
  } else {
    // No exam board selected, show all general subjects
    availableSubjects.value = [...props.subjects]
  }
}

// Filter subjects when input changes
const filterSubjects = (event) => {
  searchQuery.value = event.target.value
  showDropdown.value = true
}

// Select a subject from the dropdown
const selectSubject = (subject) => {
  form.subject = subject
  subjectInput.value = subject
  showDropdown.value = false
  searchQuery.value = ''
}

// Use custom subject (only allowed when no exam board is selected)
const selectCustomSubject = () => {
  if (!form.exam_board_id) {
    form.subject = subjectInput.value
    showDropdown.value = false
  }
}

// Handle blur event with a delay to allow for click events
const onSubjectBlur = () => {
  setTimeout(() => {
    showDropdown.value = false
  }, 200)
}

// Close dropdown when clicking outside
onMounted(() => {
  document.addEventListener('click', (event) => {
    const subjectInput = document.getElementById('subject')
    if (subjectInput && !subjectInput.contains(event.target)) {
      showDropdown.value = false
    }
  })
})

const addObjective = () => {
  form.learning_objectives.push('')
}

const removeObjective = (index) => {
  form.learning_objectives.splice(index, 1)
}
</script>

<style scoped>
.scrollbar-custom {
  scrollbar-width: thin;
  scrollbar-color: #a3d9a5 #f0fdf4;
}

.scrollbar-custom::-webkit-scrollbar {
  width: 6px;
}

.scrollbar-custom::-webkit-scrollbar-track {
  background: #f0fdf4;
  border-radius: 3px;
}

.scrollbar-custom::-webkit-scrollbar-thumb {
  background-color: #a3d9a5;
  border-radius: 3px;
}

/* Custom animations */
@keyframes pulse-subtle {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.8; }
}

.animate-pulse {
  animation: pulse-subtle 2s ease-in-out infinite;
}
</style>