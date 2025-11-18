<!-- resources/js/Components/Student/Quiz/TakeQuiz.vue -->
<template>
  <div class="max-w-4xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-lg">
      <!-- Quiz Header -->
      <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ quiz.title }}</h1>
            <p class="text-gray-600">{{ quiz.description }}</p>
          </div>
          <div class="text-right">
            <div class="text-sm text-gray-500">Time Remaining</div>
            <div class="text-xl font-bold text-gray-900">{{ formattedTime }}</div>
          </div>
        </div>
      </div>

      <!-- Progress -->
      <div class="px-6 py-3 bg-gray-50 border-b border-gray-200">
        <div class="flex justify-between text-sm text-gray-600">
          <span>Question {{ currentQuestion + 1 }} of {{ quiz.questions.length }}</span>
          <span>{{ Math.round(((currentQuestion + 1) / quiz.questions.length) * 100) }}% Complete</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
          <div
            class="h-2 rounded-full bg-blue-600 transition-all duration-300"
            :style="{ width: `${((currentQuestion + 1) / quiz.questions.length) * 100}%` }"
          ></div>
        </div>
      </div>

      <!-- Questions -->
      <div class="p-6">
        <div v-for="(question, index) in quiz.questions" :key="index">
          <div v-if="index === currentQuestion" class="space-y-6">
            <!-- Question -->
            <div>
              <h3 class="text-lg font-semibold text-gray-900 mb-4">
                {{ question.question }}
              </h3>

              <!-- Multiple Choice -->
              <div v-if="question.type === 'multiple_choice'" class="space-y-3">
                <label
                  v-for="(option, optIndex) in question.options"
                  :key="optIndex"
                  class="flex items-center p-4 border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer"
                  :class="{ 'border-blue-500 bg-blue-50': answers[index] === option }"
                >
                  <input
                    type="radio"
                    v-model="answers[index]"
                    :value="option"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                  />
                  <span class="ml-3 text-gray-900">{{ option }}</span>
                </label>
              </div>

              <!-- True/False -->
              <div v-if="question.type === 'true_false'" class="space-y-3">
                <label class="flex items-center p-4 border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer">
                  <input
                    type="radio"
                    v-model="answers[index]"
                    value="true"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                  />
                  <span class="ml-3 text-gray-900">True</span>
                </label>
                <label class="flex items-center p-4 border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer">
                  <input
                    type="radio"
                    v-model="answers[index]"
                    value="false"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                  />
                  <span class="ml-3 text-gray-900">False</span>
                </label>
              </div>

              <!-- Short Answer -->
              <div v-if="question.type === 'short_answer'">
                <textarea
                  v-model="answers[index]"
                  rows="4"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  placeholder="Type your answer here..."
                ></textarea>
              </div>
            </div>

            <!-- Navigation -->
            <div class="flex justify-between pt-6 border-t border-gray-200">
              <button
                @click="previousQuestion"
                :disabled="currentQuestion === 0"
                class="px-6 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Previous
              </button>

              <button
                v-if="currentQuestion < quiz.questions.length - 1"
                @click="nextQuestion"
                class="px-6 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                Next Question
              </button>

              <button
                v-else
                @click="submitQuiz"
                class="px-6 py-2 bg-green-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"
              >
                Submit Quiz
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  quiz: Object,
  attempt: Object,
})

const emit = defineEmits(['submit'])

const currentQuestion = ref(0)
const answers = ref({})
const timeRemaining = ref(props.quiz.time_limit_minutes * 60)
let timer = null

const formattedTime = computed(() => {
  const minutes = Math.floor(timeRemaining.value / 60)
  const seconds = timeRemaining.value % 60
  return `${minutes}:${seconds.toString().padStart(2, '0')}`
})

const startTimer = () => {
  timer = setInterval(() => {
    if (timeRemaining.value > 0) {
      timeRemaining.value--
    } else {
      submitQuiz()
    }
  }, 1000)
}

const nextQuestion = () => {
  if (currentQuestion.value < props.quiz.questions.length - 1) {
    currentQuestion.value++
  }
}

const previousQuestion = () => {
  if (currentQuestion.value > 0) {
    currentQuestion.value--
  }
}

const submitQuiz = () => {
  clearInterval(timer)
  emit('submit', answers.value)
}

onMounted(() => {
  if (props.quiz.time_limit_minutes) {
    startTimer()
  }
})

onUnmounted(() => {
  if (timer) {
    clearInterval(timer)
  }
})
</script>
