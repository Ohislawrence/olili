<!-- resources/js/Pages/Student/ExamPreps/Exam.vue -->
<template>
  <StudentLayout>
    <Head :title="`Exam: ${examPrep.name}`" />

    <div class="h-screen flex flex-col bg-gradient-to-br from-slate-50 to-emerald-50">
      <!-- Exam Header -->
      <div class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="py-4">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-4">
                <div>
                  <h1 class="text-lg font-semibold text-gray-900">{{ examPrep.name }}</h1>
                  <p class="text-sm text-gray-500">Question {{ currentQuestionIndex + 1 }} of {{ totalQuestions }}</p>
                </div>
              </div>

              <!-- Timer -->
              <div class="text-right">
                <div class="text-2xl font-bold text-gray-900" :class="{'text-red-600': timeRemaining < 300}">
                  {{ formatTime(timeRemaining) }}
                </div>
                <div class="text-sm text-gray-500">Time Remaining</div>
              </div>
            </div>

            <!-- Progress Bar -->
            <div class="mt-4">
              <div class="flex justify-between text-sm text-gray-600 mb-1">
                <span>Progress</span>
                <span>{{ Math.round(((currentQuestionIndex + 1) / totalQuestions) * 100) }}%</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div
                  class="bg-emerald-500 h-2 rounded-full transition-all duration-300"
                  :style="{ width: `${((currentQuestionIndex + 1) / totalQuestions) * 100}%` }"
                ></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <div class="flex-1 overflow-y-auto">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          <!-- Current Question -->
          <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-8 mb-8">
            <div class="flex items-center justify-between mb-6">
              <div class="flex items-center space-x-3">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-emerald-100 text-emerald-800">
                  Question {{ currentQuestionIndex + 1 }}
                </span>
                <span class="text-sm text-gray-500">
                  {{ currentQuestion.points }} point{{ currentQuestion.points > 1 ? 's' : '' }}
                </span>
              </div>
              <div class="text-sm text-gray-500">
                {{ currentQuestion.difficulty?.toUpperCase() || 'MEDIUM' }}
              </div>
            </div>

            <!-- Question Text -->
            <div class="mb-8">
              <h3 class="text-xl font-semibold text-gray-900 mb-4">{{ currentQuestion.question_text }}</h3>

              <!-- Multiple Choice Options -->
              <div v-if="currentQuestion.question_type === 'multiple_choice'" class="space-y-3">
                <button
                  v-for="(option, index) in currentQuestion.options"
                  :key="index"
                  @click="selectAnswer(option)"
                  :class="[
                    'w-full text-left p-4 border rounded-xl transition-all duration-200',
                    selectedAnswer === option
                      ? 'border-emerald-500 bg-emerald-50 ring-2 ring-emerald-200'
                      : 'border-gray-200 hover:border-emerald-300 hover:bg-emerald-25'
                  ]"
                >
                  <div class="flex items-center">
                    <div
                      :class="[
                        'w-6 h-6 rounded-full border-2 flex items-center justify-center mr-3 flex-shrink-0',
                        selectedAnswer === option
                          ? 'border-emerald-500 bg-emerald-500 text-white'
                          : 'border-gray-300'
                      ]"
                    >
                      <CheckIcon v-if="selectedAnswer === option" class="h-3 w-3" />
                    </div>
                    <span class="text-gray-700">{{ option }}</span>
                  </div>
                </button>
              </div>

              <!-- True/False Options -->
              <div v-if="currentQuestion.question_type === 'true_false'" class="grid grid-cols-2 gap-4">
                <button
                  v-for="option in ['True', 'False']"
                  :key="option"
                  @click="selectAnswer(option)"
                  :class="[
                    'p-4 border rounded-xl transition-all duration-200 text-center',
                    selectedAnswer === option
                      ? 'border-emerald-500 bg-emerald-50 ring-2 ring-emerald-200'
                      : 'border-gray-200 hover:border-emerald-300 hover:bg-emerald-25'
                  ]"
                >
                  <span class="font-medium text-gray-700">{{ option }}</span>
                </button>
              </div>

              <!-- Short Answer -->
              <div v-if="currentQuestion.question_type === 'short_answer'" class="space-y-4">
                <textarea
                  v-model="selectedAnswer"
                  rows="4"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 resize-none"
                  placeholder="Type your answer here..."
                  @input="saveAnswer"
                ></textarea>
                <p class="text-sm text-gray-500">Press Enter or save your answer before moving to the next question.</p>
              </div>

              <!-- Multiple Answer -->
              <div v-if="currentQuestion.question_type === 'multiple_answer'" class="space-y-3">
                <div
                  v-for="(option, index) in currentQuestion.options"
                  :key="index"
                  class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50"
                >
                  <input
                    :id="`option-${index}`"
                    v-model="selectedAnswers"
                    :value="option"
                    type="checkbox"
                    @change="saveMultipleAnswers"
                    class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded"
                  />
                  <label :for="`option-${index}`" class="ml-3 text-gray-700 cursor-pointer">
                    {{ option }}
                  </label>
                </div>
              </div>
            </div>

            <!-- Answer Status -->
            <div v-if="isAnswered" class="mt-6 p-4 bg-emerald-50 border border-emerald-200 rounded-lg">
              <div class="flex items-center">
                <CheckCircleIcon class="h-5 w-5 text-emerald-600 mr-2" />
                <span class="text-emerald-700 font-medium">Answer saved</span>
              </div>
            </div>
          </div>

          <!-- Navigation -->
          <div class="flex justify-between items-center">
            <button
              @click="previousQuestion"
              :disabled="currentQuestionIndex === 0 || submitting"
              class="inline-flex items-center px-6 py-3 border border-emerald-300 text-emerald-700 font-medium rounded-lg hover:bg-emerald-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <ChevronLeftIcon class="h-4 w-4 mr-2" />
              Previous
            </button>

            <div class="flex items-center space-x-4">
              <!-- Mark for Review -->
              <button
                v-if="!isAnswered"
                @click="markForReview"
                class="inline-flex items-center px-4 py-2 border border-amber-300 text-amber-700 font-medium rounded-lg hover:bg-amber-50 transition-colors"
              >
                <BookmarkIcon class="h-4 w-4 mr-2" />
                Mark for Review
              </button>

              <!-- Next/Submit Button -->
              <button
                v-if="currentQuestionIndex < totalQuestions - 1"
                @click="nextQuestion"
                :disabled="submitting"
                class="inline-flex items-center px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-medium rounded-lg transition-colors disabled:opacity-50"
              >
                Next
                <ChevronRightIcon class="h-4 w-4 ml-2" />
              </button>

              <button
                v-else
                @click="submitExam"
                :disabled="submitting"
                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-medium rounded-lg transition-colors shadow-sm hover:shadow-md disabled:opacity-50"
              >
                <CheckIcon class="h-4 w-4 mr-2" />
                {{ submitting ? 'Submitting...' : 'Submit Exam' }}
              </button>
            </div>
          </div>

          <!-- Question Navigation Grid -->
          <div class="mt-8 bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <h4 class="text-lg font-semibold text-gray-900 mb-4">Question Navigation</h4>
            <div class="grid grid-cols-5 md:grid-cols-10 gap-2">
              <button
                v-for="(question, index) in questions"
                :key="index"
                @click="goToQuestion(index)"
                :class="[
                  'w-10 h-10 rounded-lg flex items-center justify-center text-sm font-medium transition-all duration-200',
                  index === currentQuestionIndex
                    ? 'bg-emerald-500 text-white ring-2 ring-emerald-200'
                    : userAnswers[index] !== undefined
                    ? 'bg-emerald-100 text-emerald-800'
                    : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                ]"
              >
                {{ index + 1 }}
              </button>
            </div>
            <div class="mt-4 flex items-center space-x-4 text-sm text-gray-600">
              <div class="flex items-center">
                <div class="w-3 h-3 rounded bg-emerald-500 mr-2"></div>
                <span>Current</span>
              </div>
              <div class="flex items-center">
                <div class="w-3 h-3 rounded bg-emerald-100 mr-2"></div>
                <span>Answered</span>
              </div>
              <div class="flex items-center">
                <div class="w-3 h-3 rounded bg-gray-100 mr-2"></div>
                <span>Not Answered</span>
              </div>
            </div>
          </div>

          <!-- Warning Messages -->
          <div v-if="timeRemaining < 300" class="mt-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex items-center">
              <ExclamationTriangleIcon class="h-5 w-5 text-red-600 mr-3" />
              <div>
                <h4 class="text-sm font-semibold text-red-800">Time Warning</h4>
                <p class="text-sm text-red-700 mt-1">
                  Less than 5 minutes remaining! Please submit your exam before time runs out.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Bottom Bar -->
      <div class="bg-white border-t border-gray-200 py-3 px-4">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
          <div class="text-sm text-gray-600">
            <span class="font-medium">{{ answeredCount }}</span> of {{ totalQuestions }} questions answered
          </div>
          <div class="flex items-center space-x-4">
            <button
              @click="exitExam"
              class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors"
            >
              Exit Exam
            </button>
            <button
              @click="submitExam"
              :disabled="submitting"
              class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-medium rounded-lg transition-colors shadow-sm hover:shadow-md disabled:opacity-50"
            >
              <CheckIcon class="h-4 w-4 mr-2" />
              Submit Exam
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Exit Confirmation Modal -->
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
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
        @click.self="showExitModal = false"
      >
        <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4">
          <div class="text-center">
            <ExclamationTriangleIcon class="h-12 w-12 text-amber-500 mx-auto mb-4" />
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Exit Exam?</h3>
            <p class="text-gray-600 mb-6">
              If you exit now, your progress will be saved but the timer will continue running.
              You can return to complete the exam before time runs out.
            </p>
            <div class="flex space-x-3">
              <button
                @click="showExitModal = false"
                class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors"
              >
                Continue Exam
              </button>
              <Link
                :href="route('student.exam-preps.show', examPrep.id)"
                class="flex-1 px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-medium rounded-lg hover:from-emerald-700 hover:to-teal-700 transition-colors text-center"
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
} from '@heroicons/vue/24/outline'

const props = defineProps({
  examPrep: Object,
  attempt: Object,
  currentQuestionIndex: Number,
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

// Initialize answers
onMounted(() => {
  startTimer()
  loadCurrentAnswer()
  // Auto-save progress every 30 seconds
  autoSaveInterval = setInterval(autoSaveProgress, 30000)
})

onUnmounted(() => {
  if (timerInterval.value) {
    clearInterval(timerInterval.value)
  }
  if (autoSaveInterval) {
    clearInterval(autoSaveInterval)
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
  const minutes = Math.floor(seconds / 60)
  const remainingSeconds = seconds % 60
  return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`
}

// Answer handling
const loadCurrentAnswer = () => {
  const answer = userAnswers.value[currentQuestionIndex.value]
  if (currentQuestion.value.question_type === 'multiple_answer') {
    selectedAnswers.value = answer || []
  } else {
    selectedAnswer.value = answer
  }
}

const selectAnswer = (answer) => {
  selectedAnswer.value = answer
  userAnswers.value[currentQuestionIndex.value] = answer
  saveAnswer()
}

const saveAnswer = async () => {
  try {
    await fetch(route('student.exam-preps.save-answer', {
      examPrep: props.examPrep.id,
      attempt: props.attempt.id
    }), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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
let autoSaveInterval = null

const autoSaveProgress = async () => {
  try {
    await fetch(route('student.exam-preps.save-answer', {
      examPrep: props.examPrep.id,
      attempt: props.attempt.id
    }), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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

  try {
    const response = await fetch(route('student.exam-preps.submit', {
      examPrep: props.examPrep.id,
      attempt: props.attempt.id
    }), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        answers: userAnswers.value
      })
    })

    const data = await response.json()

    if (data.success) {
      router.visit(data.redirect)
    } else {
      throw new Error(data.error || 'Failed to submit exam')
    }
  } catch (error) {
    console.error('Exam submission failed:', error)
    submitting.value = false
  }
}

const exitExam = () => {
  showExitModal.value = true
}
</script>
