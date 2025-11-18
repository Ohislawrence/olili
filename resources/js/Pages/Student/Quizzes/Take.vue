<template>
  <StudentLayout>
    <Head :title="`Taking Quiz: ${quiz.title}`" />

    <div class="min-h-screen bg-gradient-to-br from-slate-50 to-emerald-50">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Quiz Header -->
        <div class="bg-white shadow-sm rounded-xl border border-gray-100 mb-6">
          <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
              <div>
                <h1 class="text-xl font-bold text-gray-900">{{ quiz.title }}</h1>
                <p class="text-sm text-gray-600">
                  {{ quiz.course_outline.module.course.title }} • Attempt #{{ attempt.attempt_number }}
                </p>
              </div>
              <div class="text-right">
                <div v-if="time_limit > 0" class="text-lg font-bold text-gray-900 bg-emerald-50 px-3 py-1 rounded-lg">
                  Time: {{ formatTime(remainingTime) }}
                </div>
                <div class="text-sm text-gray-500">
                  Question {{ currentQuestion + 1 }} of {{ questions.length }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Progress Bar -->
        <div class="mb-6">
          <div class="flex justify-between text-sm text-gray-600 mb-1">
            <span>Progress</span>
            <span>{{ Math.round((currentQuestion + 1) / questions.length * 100) }}%</span>
          </div>
          <div class="w-full bg-gray-200 rounded-full h-2.5">
            <div
              class="bg-gradient-to-r from-emerald-500 to-emerald-600 h-2.5 rounded-full transition-all duration-300"
              :style="{ width: `${(currentQuestion + 1) / questions.length * 100}%` }"
            ></div>
          </div>
        </div>

        <!-- Quiz Content -->
        <div class="bg-white shadow-sm rounded-xl border border-gray-100">
          <form @submit.prevent="submitQuiz">
            <!-- Current Question -->
            <div class="p-6 border-b border-gray-200">
              <h2 class="text-lg font-bold text-gray-900 mb-4">
                Question {{ currentQuestion + 1 }}
              </h2>
              <p class="text-gray-700 mb-6 text-lg">{{ currentQuestionData.question }}</p>

              <!-- Multiple Choice Options -->
              <div v-if="currentQuestionData.type === 'multiple_choice'" class="space-y-3">
                <div
                  v-for="(option, index) in currentQuestionData.options"
                  :key="index"
                  class="flex items-center p-4 border border-gray-200 rounded-xl hover:bg-emerald-50 cursor-pointer transition-colors duration-200"
                  :class="{ 'bg-emerald-50 border-emerald-300 shadow-sm': answers[currentQuestion] === option }"
                  @click="answers[currentQuestion] = option"
                >
                  <input
                    type="radio"
                    :name="`question-${currentQuestion}`"
                    :value="option"
                    v-model="answers[currentQuestion]"
                    class="focus:ring-emerald-500 h-4 w-4 text-emerald-600 border-gray-300"
                  />
                  <label class="ml-3 block text-sm font-medium text-gray-700 cursor-pointer flex-1">
                    {{ option }}
                  </label>
                </div>
              </div>

              <!-- Multiple Answer Options -->
              <div v-if="currentQuestionData.type === 'multiple_answer'" class="space-y-3">
                <div
                  v-for="(option, index) in currentQuestionData.options"
                  :key="index"
                  class="flex items-center p-4 border border-gray-200 rounded-xl hover:bg-emerald-50 cursor-pointer transition-colors duration-200"
                  :class="{ 'bg-emerald-50 border-emerald-300 shadow-sm': isOptionSelected(option) }"
                  @click="toggleMultipleAnswer(option)"
                >
                  <input
                    type="checkbox"
                    :value="option"
                    v-model="answers[currentQuestion]"
                    class="focus:ring-emerald-500 h-4 w-4 text-emerald-600 border-gray-300 rounded"
                  />
                  <label class="ml-3 block text-sm font-medium text-gray-700 cursor-pointer flex-1">
                    {{ option }}
                  </label>
                </div>
              </div>
            </div>

            <!-- Navigation -->
            <div class="px-6 py-4 flex flex-col sm:flex-row justify-between gap-3">
              <button
                type="button"
                @click="previousQuestion"
                :disabled="currentQuestion === 0"
                class="inline-flex items-center px-4 py-2.5 border border-emerald-300 text-sm font-semibold rounded-lg text-emerald-700 bg-white hover:bg-emerald-50 shadow-sm hover:shadow-md transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Previous
              </button>

              <button
                v-if="currentQuestion < questions.length - 1"
                type="button"
                @click="nextQuestion"
                class="inline-flex items-center px-4 py-2.5 border border-transparent text-sm font-semibold rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 shadow-md hover:shadow-lg transition-all duration-200"
              >
                Next Question
              </button>

              <button
                v-else
                type="submit"
                class="inline-flex items-center px-4 py-2.5 border border-transparent text-sm font-semibold rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 shadow-md hover:shadow-lg transition-all duration-200"
              >
                Submit Quiz
              </button>
            </div>
          </form>
        </div>

        <!-- Question Navigation -->
        <div class="mt-6 bg-white shadow-sm rounded-xl border border-gray-100 p-6">
          <h3 class="text-sm font-semibold text-gray-900 mb-4">Question Navigation</h3>
          <div class="grid grid-cols-5 sm:grid-cols-10 gap-2">
            <button
              v-for="(question, index) in questions"
              :key="index"
              @click="goToQuestion(index)"
              :class="[
                'w-9 h-9 rounded-full text-sm font-medium flex items-center justify-center transition-colors duration-200',
                index === currentQuestion
                  ? 'bg-emerald-600 text-white shadow-md'
                  : answers[index]
                  ? 'bg-emerald-100 text-emerald-800 border border-emerald-300'
                  : 'bg-gray-100 text-gray-600 border border-gray-300 hover:bg-gray-200'
              ]"
            >
              {{ index + 1 }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </StudentLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'
import { Head } from '@inertiajs/vue3'

const props = defineProps({
  quiz: Object,
  attempt: Object,
  questions: Array,
  time_limit: Number,
})

const currentQuestion = ref(0)
const answers = ref(Array(props.questions.length).fill(null))
const remainingTime = ref(props.time_limit * 60) // Convert to seconds
const timer = ref(null)

const currentQuestionData = computed(() => {
  return props.questions[currentQuestion.value]
})

const isOptionSelected = (option) => {
  return answers.value[currentQuestion.value]?.includes(option) || false
}

const toggleMultipleAnswer = (option) => {
  if (!answers.value[currentQuestion.value]) {
    answers.value[currentQuestion.value] = []
  }

  const currentAnswers = answers.value[currentQuestion.value]
  const index = currentAnswers.indexOf(option)

  if (index > -1) {
    currentAnswers.splice(index, 1)
  } else {
    currentAnswers.push(option)
  }
}

const nextQuestion = () => {
  if (currentQuestion.value < props.questions.length - 1) {
    currentQuestion.value++
  }
}

const previousQuestion = () => {
  if (currentQuestion.value > 0) {
    currentQuestion.value--
  }
}

const goToQuestion = (index) => {
  currentQuestion.value = index
}

const formatTime = (seconds) => {
  const minutes = Math.floor(seconds / 60)
  const remainingSeconds = seconds % 60
  return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`
}

const submitQuiz = () => {
  router.post(route('student.quiz-attempts.submit', props.attempt.id), {
    answers: answers.value,
  })
}

// Timer functionality
onMounted(() => {
  if (props.time_limit > 0) {
    timer.value = setInterval(() => {
      if (remainingTime.value > 0) {
        remainingTime.value--
      } else {
        clearInterval(timer.value)
        // Auto-submit when time runs out
        submitQuiz()
      }
    }, 1000)
  }
})

onUnmounted(() => {
  if (timer.value) {
    clearInterval(timer.value)
  }
})
</script>
