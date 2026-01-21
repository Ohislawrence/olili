<!-- resources/js/Pages/Student/ExamPreps/Instructions.vue -->
<template>
  <StudentLayout>
    <Head :title="`Instructions: ${examPrep.name}`" />

    <div class="py-6">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-2xl font-bold text-gray-900">Exam Instructions</h1>
              <p class="mt-1 text-sm text-gray-600">
                Read the instructions carefully before starting your exam
              </p>
            </div>
            <Link
              :href="route('student.exam-preps.show', examPrep.id)"
              class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
            >
              <ArrowLeftIcon class="h-4 w-4 mr-2" />
              Back to Exam
            </Link>
          </div>
        </div>

        <!-- Exam Info Card -->
        <div class="bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl p-8 text-white mb-8">
          <div class="text-center mb-6">
            <h2 class="text-3xl font-bold mb-2">{{ examPrep.name }}</h2>
            <p class="opacity-90">{{ examPrep.exam_board?.name }}</p>
          </div>

          <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="text-center">
              <div class="text-2xl font-bold">{{ examPrep.total_questions }}</div>
              <div class="text-sm opacity-90">Questions</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold">{{ examPrep.time_limit_minutes }}</div>
              <div class="text-sm opacity-90">Minutes</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold">{{ examPrep.passing_score }}%</div>
              <div class="text-sm opacity-90">To Pass</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold">{{ nextAttemptNumber }}</div>
              <div class="text-sm opacity-90">Your Attempt</div>
            </div>
          </div>

          <div class="text-center">
            <p v-if="attemptCount > 0" class="opacity-90">
              Your best score so far: <span class="font-bold">{{ bestScore.toFixed(1) }}%</span>
            </p>
            <p v-else class="opacity-90">This is your first attempt</p>
          </div>
        </div>

        <!-- Instructions -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-8 mb-8">
          <h3 class="text-xl font-bold text-gray-900 mb-6">Important Instructions</h3>

          <div class="space-y-6">
            <!-- Time Management -->
            <div class="flex items-start">
              <div class="flex-shrink-0">
                <ClockIcon class="h-6 w-6 text-emerald-600 mt-1" />
              </div>
              <div class="ml-4">
                <h4 class="font-semibold text-gray-900 mb-2">Time Management</h4>
                <ul class="list-disc list-inside text-gray-600 space-y-1">
                  <li>You have {{ examPrep.time_limit_minutes }} minutes to complete the exam</li>
                  <li>The timer starts when you click "Start Exam"</li>
                  <li>Time continues even if you close the browser window</li>
                  <li>You cannot pause the timer</li>
                  <li>Submit your answers before time runs out</li>
                </ul>
              </div>
            </div>

            <!-- Navigation -->
            <div class="flex items-start">
              <div class="flex-shrink-0">
                <ArrowsRightLeftIcon class="h-6 w-6 text-emerald-600 mt-1" />
              </div>
              <div class="ml-4">
                <h4 class="font-semibold text-gray-900 mb-2">Navigation & Submission</h4>
                <ul class="list-disc list-inside text-gray-600 space-y-1">
                  <li>Use "Next" and "Previous" buttons to navigate between questions</li>
                  <li>You can skip questions and return to them later</li>
                  <li>Answer all questions before submitting</li>
                  <li>Review your answers before final submission</li>
                  <li>Once submitted, you cannot change your answers</li>
                </ul>
              </div>
            </div>

            <!-- Question Types -->
            <div class="flex items-start">
              <div class="flex-shrink-0">
                <QuestionMarkCircleIcon class="h-6 w-6 text-emerald-600 mt-1" />
              </div>
              <div class="ml-4">
                <h4 class="font-semibold text-gray-900 mb-2">Question Types</h4>
                <div class="space-y-3">
                  <div>
                    <span class="font-medium text-gray-700">Multiple Choice:</span>
                    <p class="text-gray-600">Select one correct answer from several options</p>
                  </div>
                  <div>
                    <span class="font-medium text-gray-700">True/False:</span>
                    <p class="text-gray-600">Choose either True or False for each statement</p>
                  </div>
                  <div>
                    <span class="font-medium text-gray-700">Short Answer:</span>
                    <p class="text-gray-600">Type your answer in the provided text box</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Technical Requirements -->
            <div class="flex items-start">
              <div class="flex-shrink-0">
                <ComputerDesktopIcon class="h-6 w-6 text-emerald-600 mt-1" />
              </div>
              <div class="ml-4">
                <h4 class="font-semibold text-gray-900 mb-2">Technical Requirements</h4>
                <ul class="list-disc list-inside text-gray-600 space-y-1">
                  <li>Stable internet connection is required</li>
                  <li>Use a modern browser (Chrome, Firefox, Safari, Edge)</li>
                  <li>Do not refresh the page during the exam</li>
                  <li>Do not open multiple tabs/windows</li>
                  <li>Ensure your device has sufficient battery/power</li>
                </ul>
              </div>
            </div>

            <!-- Academic Integrity -->
            <div class="flex items-start">
              <div class="flex-shrink-0">
                <ShieldCheckIcon class="h-6 w-6 text-emerald-600 mt-1" />
              </div>
              <div class="ml-4">
                <h4 class="font-semibold text-gray-900 mb-2">Academic Integrity</h4>
                <ul class="list-disc list-inside text-gray-600 space-y-1">
                  <li>This exam is for practice purposes only</li>
                  <li>Complete the exam independently</li>
                  <li>Do not use external resources unless allowed</li>
                  <li>Your results help identify areas for improvement</li>
                </ul>
              </div>
            </div>
          </div>
        </div>

        <!-- Readiness Checklist -->
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-6 mb-8">
          <h3 class="text-lg font-semibold text-emerald-900 mb-4">Readiness Checklist</h3>
          <div class="space-y-3">
            <div class="flex items-center">
              <CheckCircleIcon class="h-5 w-5 text-emerald-600 mr-3" />
              <span class="text-emerald-800">I have read and understood all instructions</span>
            </div>
            <div class="flex items-center">
              <CheckCircleIcon class="h-5 w-5 text-emerald-600 mr-3" />
              <span class="text-emerald-800">I have a stable internet connection</span>
            </div>
            <div class="flex items-center">
              <CheckCircleIcon class="h-5 w-5 text-emerald-600 mr-3" />
              <span class="text-emerald-800">I have allocated {{ examPrep.time_limit_minutes }} minutes of uninterrupted time</span>
            </div>
            <div class="flex items-center">
              <CheckCircleIcon class="h-5 w-5 text-emerald-600 mr-3" />
              <span class="text-emerald-800">I understand that I cannot pause the timer</span>
            </div>
            <div class="flex items-center">
              <CheckCircleIcon class="h-5 w-5 text-emerald-600 mr-3" />
              <span class="text-emerald-800">I am ready to begin the exam</span>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
          <Link
            :href="route('student.exam-preps.show', examPrep.id)"
            class="inline-flex items-center px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors w-full sm:w-auto justify-center"
          >
            <ArrowLeftIcon class="h-4 w-4 mr-2" />
            Back to Exam Details
          </Link>

          <div class="flex items-center space-x-3 w-full sm:w-auto">
            <button
              @click="startExam"
              :disabled="starting"
              class="inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl text-lg w-full sm:w-auto"
            >
              <template v-if="starting">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Starting...
              </template>
              <template v-else>
                <PlayCircleIcon class="h-5 w-5 mr-3" />
                Start Exam Now
              </template>
            </button>
          </div>
        </div>

        <!-- Important Notice -->
        <div class="mt-8 p-4 bg-amber-50 border border-amber-200 rounded-lg">
          <div class="flex">
            <ExclamationTriangleIcon class="h-5 w-5 text-amber-500 mt-0.5 mr-3" />
            <div>
              <h4 class="text-sm font-semibold text-amber-800">Important Notice</h4>
              <p class="text-sm text-amber-700 mt-1">
                Once you start the exam, the timer will begin and cannot be paused.
                Ensure you are ready before clicking "Start Exam Now".
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </StudentLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import {
  ArrowLeftIcon,
  PlayCircleIcon,
  ClockIcon,
  ArrowsRightLeftIcon,
  QuestionMarkCircleIcon,
  ComputerDesktopIcon,
  ShieldCheckIcon,
  CheckCircleIcon,
  ExclamationTriangleIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  examPrep: Object,
  attemptCount: Number,
  nextAttemptNumber: Number,
  bestScore: Number,
})

const starting = ref(false)

// Start exam
const startExam = async () => {
  starting.value = true

  try {
    await router.get(route('student.exam-preps.start', props.examPrep.id))
  } catch (error) {
    console.error('Failed to start exam:', error)
    starting.value = false
  }
}
</script>
