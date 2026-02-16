<template>
  <StudentLayout>
    <Head :title="`Exam: ${examPrep.name}`" />

    <div class="h-screen flex flex-col bg-gradient-to-br from-slate-50 to-emerald-50">
      <!-- Exam Header - Mobile Optimized -->
      <div class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8">
          <div class="py-3 sm:py-4">
            <!-- Top Row with Timer and Title -->
            <div class="flex items-center justify-between">
              <div class="flex-1 min-w-0">
                <h1 class="text-sm sm:text-lg font-semibold text-gray-900 truncate pr-2">
                  {{ examPrep.name }}
                </h1>
                <p class="text-xs sm:text-sm text-gray-500">
                  Q{{ currentQuestionIndex + 1 }}/{{ totalQuestions }}
                </p>
              </div>

              <!-- Timer -->
              <div class="text-right flex-shrink-0">
                <div
                  class="text-xl sm:text-2xl font-bold text-gray-900 tabular-nums"
                  :class="{'text-red-600': timeRemaining < 300}"
                >
                  {{ formatTime(timeRemaining) }}
                </div>
                <div class="text-xs sm:text-sm text-gray-500">Remaining</div>
              </div>
            </div>

            <!-- Progress Bar -->
            <div class="mt-2 sm:mt-4">
              <div class="flex justify-between text-xs text-gray-600 mb-1">
                <span>Progress</span>
                <span>{{ Math.round(((currentQuestionIndex + 1) / totalQuestions) * 100) }}%</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-1.5 sm:h-2">
                <div
                  class="bg-emerald-500 h-1.5 sm:h-2 rounded-full transition-all duration-300"
                  :style="{ width: `${((currentQuestionIndex + 1) / totalQuestions) * 100}%` }"
                ></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content - Scrollable -->
      <div class="flex-1 overflow-y-auto overscroll-contain">
        <div class="max-w-4xl mx-auto px-3 sm:px-4 lg:px-8 py-4 sm:py-8">
          <!-- Current Question -->
          <div class="bg-white rounded-lg sm:rounded-xl border border-gray-200 shadow-sm p-4 sm:p-8 mb-4 sm:mb-8">
            <!-- Question Header -->
            <div class="flex flex-wrap items-center justify-between gap-2 mb-4 sm:mb-6">
              <div class="flex items-center space-x-2">
                <span class="inline-flex items-center px-2 py-1 sm:px-3 sm:py-1 rounded-full text-xs sm:text-sm font-semibold bg-emerald-100 text-emerald-800">
                  Q{{ currentQuestionIndex + 1 }}
                </span>
                <span class="text-xs sm:text-sm text-gray-500">
                  {{ currentQuestion.points }} pt{{ currentQuestion.points > 1 ? 's' : '' }}
                </span>
              </div>
              <div class="text-xs sm:text-sm text-gray-500 capitalize bg-gray-100 px-2 py-1 rounded-full">
                {{ currentQuestion.difficulty || 'Medium' }}
              </div>
            </div>

            <!-- Question Text -->
            <div class="mb-6 sm:mb-8">
              <h3 class="text-base sm:text-xl font-semibold text-gray-900 mb-3 sm:mb-4 leading-relaxed">
                {{ currentQuestion.question_text }}
              </h3>

              <!-- Multiple Choice Options -->
              <div v-if="currentQuestion.question_type === 'multiple_choice'" class="space-y-2 sm:space-y-3">
                <button
                  v-for="(option, index) in currentQuestion.options"
                  :key="index"
                  @click="selectAnswer(option)"
                  :class="[
                    'w-full text-left p-3 sm:p-4 border rounded-lg sm:rounded-xl transition-all duration-200 min-h-[44px]',
                    selectedAnswer === option
                      ? 'border-emerald-500 bg-emerald-50 ring-2 ring-emerald-200'
                      : 'border-gray-200 hover:border-emerald-300 hover:bg-emerald-25'
                  ]"
                >
                  <div class="flex items-center">
                    <div
                      :class="[
                        'w-5 h-5 sm:w-6 sm:h-6 rounded-full border-2 flex items-center justify-center mr-2 sm:mr-3 flex-shrink-0',
                        selectedAnswer === option
                          ? 'border-emerald-500 bg-emerald-500 text-white'
                          : 'border-gray-300'
                      ]"
                    >
                      <CheckIcon v-if="selectedAnswer === option" class="h-3 w-3" />
                    </div>
                    <span class="text-sm sm:text-base text-gray-700 break-words flex-1">{{ option }}</span>
                  </div>
                </button>
              </div>

              <!-- True/False Options -->
              <div v-if="currentQuestion.question_type === 'true_false'" class="grid grid-cols-2 gap-2 sm:gap-4">
                <button
                  v-for="option in ['True', 'False']"
                  :key="option"
                  @click="selectAnswer(option)"
                  :class="[
                    'p-3 sm:p-4 border rounded-lg sm:rounded-xl transition-all duration-200 text-center min-h-[44px]',
                    selectedAnswer === option
                      ? 'border-emerald-500 bg-emerald-50 ring-2 ring-emerald-200'
                      : 'border-gray-200 hover:border-emerald-300 hover:bg-emerald-25'
                  ]"
                >
                  <span class="text-sm sm:text-base font-medium text-gray-700">{{ option }}</span>
                </button>
              </div>

              <!-- Short Answer -->
              <div v-if="currentQuestion.question_type === 'short_answer'" class="space-y-3">
                <textarea
                  v-model="selectedAnswer"
                  rows="4"
                  class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 resize-none"
                  placeholder="Type your answer here..."
                  @input="saveAnswer"
                ></textarea>
                <p class="text-xs sm:text-sm text-gray-500 flex items-center">
                  <InformationCircleIcon class="h-4 w-4 mr-1 flex-shrink-0" />
                  Press Enter or save before moving to next question
                </p>
              </div>

              <!-- Multiple Answer -->
              <div v-if="currentQuestion.question_type === 'multiple_answer'" class="space-y-2 sm:space-y-3">
                <div
                  v-for="(option, index) in currentQuestion.options"
                  :key="index"
                  class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 min-h-[44px]"
                >
                  <input
                    :id="`option-${index}`"
                    v-model="selectedAnswers"
                    :value="option"
                    type="checkbox"
                    @change="saveMultipleAnswers"
                    class="h-4 w-4 sm:h-5 sm:w-5 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded flex-shrink-0"
                  />
                  <label :for="`option-${index}`" class="ml-2 sm:ml-3 text-sm sm:text-base text-gray-700 cursor-pointer break-words flex-1">
                    {{ option }}
                  </label>
                </div>
              </div>
            </div>

            <!-- Answer Status -->
            <div v-if="isAnswered" class="mt-4 p-3 bg-emerald-50 border border-emerald-200 rounded-lg">
              <div class="flex items-center">
                <CheckCircleIcon class="h-4 w-4 sm:h-5 sm:w-5 text-emerald-600 mr-2 flex-shrink-0" />
                <span class="text-xs sm:text-sm text-emerald-700 font-medium">Answer saved</span>
              </div>
            </div>
          </div>

          <!-- Navigation Buttons -->
          <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-4 sm:mb-8">
            <button
              @click="previousQuestion"
              :disabled="currentQuestionIndex === 0 || submitting"
              class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-2.5 sm:py-3 border border-emerald-300 text-emerald-700 text-sm font-medium rounded-lg hover:bg-emerald-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed min-h-[44px]"
            >
              <ChevronLeftIcon class="h-4 w-4 mr-2" />
              Previous
            </button>

            <div class="flex flex-col xs:flex-row gap-2 w-full sm:w-auto">
              <!-- Mark for Review -->
              <button
                v-if="!isAnswered"
                @click="markForReview"
                class="flex-1 inline-flex items-center justify-center px-3 sm:px-4 py-2.5 border border-amber-300 text-amber-700 text-sm font-medium rounded-lg hover:bg-amber-50 transition-colors min-h-[44px]"
              >
                <BookmarkIcon class="h-4 w-4 mr-2" />
                <span class="hidden xs:inline">Mark for Review</span>
                <span class="xs:hidden">Review</span>
              </button>

              <!-- Next/Submit Button -->
              <button
                v-if="currentQuestionIndex < totalQuestions - 1"
                @click="nextQuestion"
                :disabled="submitting"
                class="flex-1 inline-flex items-center justify-center px-4 sm:px-6 py-2.5 sm:py-3 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium rounded-lg transition-colors disabled:opacity-50 min-h-[44px]"
              >
                Next
                <ChevronRightIcon class="h-4 w-4 ml-2" />
              </button>

              <button
                v-else
                @click="submitExam"
                :disabled="submitting"
                class="flex-1 inline-flex items-center justify-center px-4 sm:px-6 py-2.5 sm:py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm disabled:opacity-50 min-h-[44px]"
              >
                <CheckIcon class="h-4 w-4 mr-2" />
                {{ submitting ? 'Submitting...' : 'Submit' }}
              </button>
            </div>
          </div>

          <!-- Question Navigation Grid -->
          <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm mb-4">
            <h4 class="text-sm sm:text-base font-semibold text-gray-900 mb-3">Quick Navigation</h4>
            <div class="grid grid-cols-6 sm:grid-cols-10 gap-1.5 sm:gap-2">
              <button
                v-for="(question, index) in questions"
                :key="index"
                @click="goToQuestion(index)"
                :class="[
                  'w-8 h-8 sm:w-10 sm:h-10 rounded-lg flex items-center justify-center text-xs sm:text-sm font-medium transition-all duration-200',
                  index === currentQuestionIndex
                    ? 'bg-emerald-500 text-white ring-2 ring-emerald-200'
                    : userAnswers[index] !== undefined && userAnswers[index] !== null && userAnswers[index] !== ''
                    ? 'bg-emerald-100 text-emerald-800'
                    : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                ]"
              >
                {{ index + 1 }}
              </button>
            </div>

            <!-- Legend - Mobile Optimized -->
            <div class="mt-3 flex flex-wrap items-center gap-3 text-xs text-gray-600">
              <div class="flex items-center">
                <div class="w-3 h-3 rounded bg-emerald-500 mr-1.5"></div>
                <span>Current</span>
              </div>
              <div class="flex items-center">
                <div class="w-3 h-3 rounded bg-emerald-100 mr-1.5"></div>
                <span>Answered</span>
              </div>
              <div class="flex items-center">
                <div class="w-3 h-3 rounded bg-gray-100 mr-1.5"></div>
                <span>Not Answered</span>
              </div>
            </div>
          </div>

          <!-- Warning Messages -->
          <div v-if="timeRemaining < 300" class="p-3 sm:p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex items-start">
              <ExclamationTriangleIcon class="h-4 w-4 sm:h-5 sm:w-5 text-red-600 mr-2 flex-shrink-0 mt-0.5" />
              <div>
                <h4 class="text-xs sm:text-sm font-semibold text-red-800">Time Warning</h4>
                <p class="text-xs text-red-700 mt-0.5">
                  Less than 5 minutes remaining! Please submit soon.
                </p>
              </div>
            </div>
          </div>

          <!-- Error Message -->
          <div v-if="submitError" class="mt-4 p-3 sm:p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex items-start">
              <ExclamationTriangleIcon class="h-4 w-4 sm:h-5 sm:w-5 text-red-600 mr-2 flex-shrink-0 mt-0.5" />
              <div class="flex-1">
                <h4 class="text-xs sm:text-sm font-semibold text-red-800">Submission Failed</h4>
                <p class="text-xs text-red-700 mt-0.5">{{ submitError }}</p>
              </div>
              <button
                @click="retrySubmit"
                class="ml-2 px-3 py-1.5 text-xs bg-red-600 text-white rounded-lg hover:bg-red-700 whitespace-nowrap min-h-[36px]"
              >
                Retry
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Bottom Bar - Mobile Optimized -->
      <div class="bg-white border-t border-gray-200 py-2 px-3 sm:py-3 sm:px-4">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
          <div class="text-xs sm:text-sm text-gray-600">
            <span class="font-medium">{{ answeredCount }}</span>/{{ totalQuestions }} answered
          </div>
          <div class="flex items-center space-x-2 sm:space-x-4">
            <button
              @click="showExitModal = true"
              class="inline-flex items-center px-3 sm:px-4 py-1.5 sm:py-2 border border-gray-300 text-gray-700 text-xs sm:text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors min-h-[36px] sm:min-h-[44px]"
            >
              Exit
            </button>
            <button
              @click="submitExam"
              :disabled="submitting"
              class="inline-flex items-center px-4 sm:px-6 py-1.5 sm:py-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-xs sm:text-sm font-medium rounded-lg transition-colors shadow-sm disabled:opacity-50 min-h-[36px] sm:min-h-[44px]"
            >
              <CheckIcon class="h-3 w-3 sm:h-4 sm:w-4 mr-1 sm:mr-2" />
              Submit
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Exit Confirmation Modal - Mobile Optimized -->
    <Transition
      enter-active-class="duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="showExitModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
        @click.self="showExitModal = false"
      >
        <div class="bg-white rounded-xl p-4 sm:p-6 max-w-md w-full mx-4">
          <div class="text-center">
            <div class="mx-auto h-12 w-12 bg-amber-100 rounded-full flex items-center justify-center mb-3">
              <ExclamationTriangleIcon class="h-6 w-6 text-amber-600" />
            </div>
            <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-2">Exit Exam?</h3>
            <p class="text-sm text-gray-600 mb-4">
              Your progress is saved. The timer will continue running.
            </p>
            <div class="flex flex-col xs:flex-row gap-2">
              <button
                @click="showExitModal = false"
                class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors min-h-[44px]"
              >
                Continue Exam
              </button>
              <Link
                :href="route('student.exam-preps.show', examPrep.id)"
                class="flex-1 px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-sm font-medium rounded-lg hover:from-emerald-700 hover:to-teal-700 transition-colors text-center min-h-[44px]"
              >
                Exit Exam
              </Link>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </StudentLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import {
  ChevronLeftIcon,
  ChevronRightIcon,
  CheckIcon,
  BookmarkIcon,
  CheckCircleIcon,
  ExclamationTriangleIcon,
  InformationCircleIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  examPrep: {
    type: Object,
    required: true
  },
  attempt: {
    type: Object,
    required: true
  },
  currentQuestionIndex: {
    type: Number,
    default: 0
  },
})

// Reactive state
const questions = ref(props.attempt.questions || [])
const userAnswers = ref(props.attempt.answers || {})
const selectedAnswer = ref(null)
const selectedAnswers = ref([])
const currentQuestionIndex = ref(props.currentQuestionIndex || 0)
const timeRemaining = ref(props.examPrep.time_limit_minutes * 60)
const submitting = ref(false)
const showExitModal = ref(false)
const timerInterval = ref(null)
const autoSaveInterval = ref(null)
const submitError = ref(null)

// Computed properties
const totalQuestions = computed(() => questions.value.length)
const currentQuestion = computed(() => questions.value[currentQuestionIndex.value] || {})
const isAnswered = computed(() => {
  const answer = userAnswers.value[currentQuestionIndex.value]
  return answer !== undefined && answer !== null && answer !== ''
})
const answeredCount = computed(() => {
  return Object.values(userAnswers.value).filter(answer =>
    answer !== undefined && answer !== null && answer !== ''
  ).length
})

// Initialize
onMounted(() => {
  startTimer()
  loadCurrentAnswer()
  autoSaveInterval.value = setInterval(autoSaveProgress, 30000)
})

onUnmounted(() => {
  if (timerInterval.value) {
    clearInterval(timerInterval.value)
  }
  if (autoSaveInterval.value) {
    clearInterval(autoSaveInterval.value)
  }
})

// Timer functions
const startTimer = () => {
  timerInterval.value = setInterval(() => {
    timeRemaining.value--
    if (timeRemaining.value <= 0) {
      clearInterval(timerInterval.value)
      submitExam()
    }
  }, 1000)
}

const formatTime = (seconds) => {
  const mins = Math.floor(seconds / 60)
  const secs = seconds % 60
  return `${mins}:${secs.toString().padStart(2, '0')}`
}

// Answer handling
const loadCurrentAnswer = () => {
  const answer = userAnswers.value[currentQuestionIndex.value]
  if (currentQuestion.value.question_type === 'multiple_answer') {
    selectedAnswers.value = answer || []
  } else {
    selectedAnswer.value = answer || null
  }
}

const selectAnswer = (answer) => {
  selectedAnswer.value = answer
  userAnswers.value[currentQuestionIndex.value] = answer
  saveAnswer()
}

const saveAnswer = async () => {
  try {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    if (!csrfToken) return

    await fetch(route('student.exam-preps.save-answer', {
      examPrep: props.examPrep.id,
      attempt: props.attempt.id
    }), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        question_index: currentQuestionIndex.value,
        answer: currentQuestion.value.question_type === 'multiple_answer'
          ? selectedAnswers.value
          : selectedAnswer.value
      })
    })
  } catch (error) {
    console.error('Failed to save answer:', error)
  }
}

const saveMultipleAnswers = () => {
  userAnswers.value[currentQuestionIndex.value] = [...selectedAnswers.value]
  saveAnswer()
}

const markForReview = () => {
  userAnswers.value[currentQuestionIndex.value] = null
  saveAnswer()
}

// Navigation
const previousQuestion = () => {
  if (currentQuestionIndex.value > 0) {
    currentQuestionIndex.value--
    loadCurrentAnswer()
  }
}

const nextQuestion = () => {
  if (currentQuestionIndex.value < totalQuestions.value - 1) {
    currentQuestionIndex.value++
    loadCurrentAnswer()
  }
}

const goToQuestion = (index) => {
  currentQuestionIndex.value = index
  loadCurrentAnswer()
}

// Auto-save
const autoSaveProgress = async () => {
  try {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    if (!csrfToken) return

    await fetch(route('student.exam-preps.save-answer', {
      examPrep: props.examPrep.id,
      attempt: props.attempt.id
    }), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        question_index: currentQuestionIndex.value,
        answer: currentQuestion.value.question_type === 'multiple_answer'
          ? selectedAnswers.value
          : selectedAnswer.value,
        auto_save: true
      })
    })
  } catch (error) {
    console.error('Auto-save failed:', error)
  }
}

// Exam submission
const submitExam = async () => {
  if (submitting.value) return

  submitting.value = true
  submitError.value = null

  try {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    if (!csrfToken) {
      throw new Error('CSRF token not found')
    }

    const response = await fetch(route('student.exam-preps.submit', {
      examPrep: props.examPrep.id,
      attempt: props.attempt.id
    }), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({
        answers: userAnswers.value,
        _token: csrfToken
      })
    })

    const data = await response.json()

    if (data.success) {
      window.location.href = route('student.exam-preps.results', {
        examPrep: props.examPrep.id,
        attempt: props.attempt.id
      })
    } else {
      throw new Error(data.message || data.error || 'Failed to submit exam')
    }
  } catch (error) {
    console.error('Exam submission failed:', error)
    submitError.value = error.message
    submitting.value = false
  }
}

const retrySubmit = () => {
  submitError.value = null
  submitExam()
}

const exitExam = () => {
  showExitModal.value = true
}
</script>

<style scoped>
/* Touch-friendly tap targets */
@media (max-width: 640px) {
  button,
  [role="button"],
  select,
  input,
  textarea {
    min-height: 44px;
  }

  /* Better touch targets for option buttons */
  .border.rounded-lg {
    min-height: 44px;
  }

  /* Prevent zoom on input focus for iOS */
  input[type="text"],
  input[type="number"],
  textarea,
  select {
    font-size: 16px;
  }
}

/* Tabular numbers for timer */
.tabular-nums {
  font-variant-numeric: tabular-nums;
}

/* Custom scrollbar for content area */
.overflow-y-auto {
  scrollbar-width: thin;
  scrollbar-color: #cbd5e0 #f1f5f9;
}

.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: #f1f5f9;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background-color: #cbd5e0;
  border-radius: 6px;
}

/* Extra small devices */
@media (min-width: 480px) {
  .xs\:inline {
    display: inline;
  }
  .xs\:hidden {
    display: none;
  }
  .xs\:flex-row {
    flex-direction: row;
  }
}

/* Prevent text overflow */
.break-words {
  word-break: break-word;
  overflow-wrap: break-word;
}
</style>
