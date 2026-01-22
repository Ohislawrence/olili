<!-- resources/js/Pages/Student/ExamPreps/ViewAttempt.vue -->
<template>
  <StudentLayout>
    <Head :title="`Attempt Results: ${attempt.exam_prep?.name || 'Exam'}`" />

    <div class="py-6">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-6">
          <Link
            :href="route('student.exam-preps.index')"
            class="hover:text-gray-700 transition-colors"
          >
            Exam Preps
          </Link>
          <ChevronRightIcon class="h-4 w-4" />
          <Link
            :href="route('student.exam-preps.my-attempts')"
            class="hover:text-gray-700 transition-colors"
          >
            My Attempts
          </Link>
          <ChevronRightIcon class="h-4 w-4" />
          <span class="text-gray-900 font-medium">Attempt Details</span>
        </nav>

        <!-- Header -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-8">
          <div class="p-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-6">
              <div class="flex-1">
                <div class="flex items-center space-x-3 mb-4">
                  <h1 class="text-2xl font-bold text-gray-900">{{ attempt.exam_prep?.name }}</h1>
                  <span
                    :class="[
                      'inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold',
                      attempt.is_passed ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800'
                    ]"
                  >
                    {{ attempt.is_passed ? 'Passed' : 'Failed' }}
                  </span>
                </div>
                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600">
                  <div class="flex items-center">
                    <AcademicCapIcon class="h-4 w-4 mr-2" />
                    <span>{{ attempt.exam_prep?.exam_board?.name }}</span>
                  </div>
                  <div class="flex items-center">
                    <QuestionMarkCircleIcon class="h-4 w-4 mr-2" />
                    <span>Attempt {{ attempt.attempt_number }}</span>
                  </div>
                  <div class="flex items-center">
                    <CalendarIcon class="h-4 w-4 mr-2" />
                    <span>{{ formatDate(attempt.completed_at) }}</span>
                  </div>
                  <div class="flex items-center">
                    <ClockIcon class="h-4 w-4 mr-2" />
                    <span>{{ formatTime(attempt.time_spent_seconds) }}</span>
                  </div>
                </div>
              </div>

              <!-- Score Badge -->
              <div class="flex flex-col items-center">
                <div
                  :class="[
                    'w-24 h-24 rounded-full flex items-center justify-center text-white text-3xl font-bold',
                    attempt.is_passed ? 'bg-gradient-to-r from-emerald-500 to-teal-600' : 'bg-gradient-to-r from-red-500 to-pink-600'
                  ]"
                >
                  {{ parseFloat(attempt.percentage).toFixed(0) }}%
                </div>
                <div class="mt-2 text-sm text-gray-600">
                  {{ attempt.score }}/{{ totalPoints }} points
                </div>
              </div>
            </div>

            <!-- Progress Bar -->
            <div class="mb-6">
              <div class="flex items-center justify-between text-sm text-gray-600 mb-2">
                <span>Performance</span>
                <span>
                  Required: {{ attempt.exam_prep?.passing_score }}% •
                  Your Score: {{ parseFloat(attempt.percentage).toFixed(1) }}%
                </span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-3">
                <div
                  class="h-3 rounded-full transition-all duration-500"
                  :class="attempt.is_passed ? 'bg-emerald-500' : 'bg-red-500'"
                  :style="{ width: `${Math.min(parseFloat(attempt.percentage), 100)}%` }"
                ></div>
              </div>
              <div class="mt-1 flex justify-between text-xs text-gray-500">
                <span>0%</span>
                <span>{{ attempt.exam_prep?.passing_score }}% (Pass)</span>
                <span>100%</span>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-3">
              <Link
                :href="route('student.exam-preps.show', attempt.exam_prep_id)"
                class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors"
              >
                <ArrowLeftIcon class="h-4 w-4 mr-2" />
                Back to Exam
              </Link>
              <Link
                :href="route('student.exam-preps.my-attempts')"
                class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors"
              >
                <AcademicCapIcon class="h-4 w-4 mr-2" />
                All Attempts
              </Link>
              <button
                v-if="canRetake"
                @click="retakeExam"
                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-medium rounded-lg hover:from-emerald-600 hover:to-teal-700 transition-all duration-200 shadow-sm hover:shadow-md"
              >
                <ArrowPathIcon class="h-4 w-4 mr-2" />
                Retake Exam
              </button>
              <button
                @click="downloadResults"
                class="inline-flex items-center px-4 py-2 border border-emerald-300 text-emerald-700 font-medium rounded-lg hover:bg-emerald-50 transition-colors"
              >
                <ArrowDownTrayIcon class="h-4 w-4 mr-2" />
                Download Results
              </button>
            </div>
          </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center">
                  <CheckCircleIcon class="h-6 w-6 text-emerald-600" />
                </div>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Correct Answers</p>
                <p class="text-2xl font-bold text-gray-900">{{ correctAnswers }}</p>
              </div>
            </div>
            <div class="mt-4">
              <div class="text-sm text-gray-500">
                {{ Math.round((correctAnswers / totalQuestions) * 100) }}% of questions
              </div>
            </div>
          </div>

          <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                  <XCircleIcon class="h-6 w-6 text-red-600" />
                </div>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Incorrect Answers</p>
                <p class="text-2xl font-bold text-gray-900">{{ incorrectAnswers }}</p>
              </div>
            </div>
            <div class="mt-4">
              <div class="text-sm text-gray-500">
                {{ Math.round((incorrectAnswers / totalQuestions) * 100) }}% of questions
              </div>
            </div>
          </div>

          <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center">
                  <QuestionMarkCircleIcon class="h-6 w-6 text-gray-600" />
                </div>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Skipped Questions</p>
                <p class="text-2xl font-bold text-gray-900">{{ skippedQuestions }}</p>
              </div>
            </div>
            <div class="mt-4">
              <div class="text-sm text-gray-500">
                {{ Math.round((skippedQuestions / totalQuestions) * 100) }}% of questions
              </div>
            </div>
          </div>

          <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                  <ClockIcon class="h-6 w-6 text-blue-600" />
                </div>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Time Spent</p>
                <p class="text-2xl font-bold text-gray-900">{{ formatTimeShort(attempt.time_spent_seconds) }}</p>
              </div>
            </div>
            <div class="mt-4">
              <div class="text-sm text-gray-500">
                {{ timeEfficiency }}% efficiency
              </div>
            </div>
          </div>
        </div>

        <!-- Question Review Section -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
          <!-- Header -->
          <div class="px-8 py-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
              <div>
                <h2 class="text-xl font-bold text-gray-900">Question Review</h2>
                <p class="text-sm text-gray-600 mt-1">
                  Review your answers and learn from mistakes
                </p>
              </div>
              <div class="flex items-center space-x-4">
                <div class="flex items-center text-sm">
                  <div class="w-3 h-3 rounded-full bg-emerald-500 mr-2"></div>
                  <span class="text-gray-600">Correct</span>
                </div>
                <div class="flex items-center text-sm">
                  <div class="w-3 h-3 rounded-full bg-red-500 mr-2"></div>
                  <span class="text-gray-600">Incorrect</span>
                </div>
                <div class="flex items-center text-sm">
                  <div class="w-3 h-3 rounded-full bg-gray-300 mr-2"></div>
                  <span class="text-gray-600">Skipped</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Questions -->
          <div class="divide-y divide-gray-200">
            <div
              v-for="(result, index) in detailedResults"
              :key="index"
              :class="[
                'p-8 transition-colors duration-150',
                result.is_correct ? 'bg-emerald-50/50 hover:bg-emerald-50' :
                result.user_answer ? 'bg-red-50/50 hover:bg-red-50' :
                'bg-gray-50/50 hover:bg-gray-50'
              ]"
            >
              <div class="flex flex-col lg:flex-row lg:items-start gap-8">
                <!-- Left Column: Question & Status -->
                <div class="flex-1">
                  <div class="flex items-start justify-between mb-4">
                    <div>
                      <div class="flex items-center space-x-3 mb-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold"
                          :class="result.is_correct ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800'">
                          Question {{ index + 1 }}
                        </span>
                        <span class="text-sm text-gray-500">
                          {{ result.points }} point{{ result.points > 1 ? 's' : '' }}
                        </span>
                        <span class="text-sm text-gray-500">
                          {{ formatQuestionType(result.question_type) }}
                        </span>
                      </div>
                      <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        {{ result.question_text }}
                      </h3>
                    </div>
                    <div class="flex items-center space-x-2">
                      <span
                        :class="[
                          'inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold',
                          result.is_correct ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800'
                        ]"
                      >
                        {{ result.is_correct ? '✓ Correct' : result.user_answer ? '✗ Incorrect' : '⏭ Skipped' }}
                      </span>
                      <span class="text-sm text-gray-500">
                        {{ result.points_earned }}/{{ result.points }} pts
                      </span>
                    </div>
                  </div>

                  <!-- Options (for multiple choice) -->
                  <div v-if="result.options && result.options.length > 0" class="mb-6">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Options:</h4>
                    <div class="space-y-2">
                      <div
                        v-for="(option, optIndex) in result.options"
                        :key="optIndex"
                        :class="[
                          'p-3 border rounded-lg',
                          option === result.correct_answer ? 'border-emerald-500 bg-emerald-50' :
                          option === result.user_answer && !result.is_correct ? 'border-red-500 bg-red-50' :
                          'border-gray-200 bg-white'
                        ]"
                      >
                        <div class="flex items-center">
                          <div
                            :class="[
                              'w-6 h-6 rounded-full border-2 flex items-center justify-center mr-3 flex-shrink-0',
                              option === result.correct_answer ? 'border-emerald-500 bg-emerald-500 text-white' :
                              option === result.user_answer && !result.is_correct ? 'border-red-500 bg-red-500 text-white' :
                              'border-gray-300'
                            ]"
                          >
                            <span v-if="option === result.correct_answer" class="text-xs">✓</span>
                            <span v-else-if="option === result.user_answer && !result.is_correct" class="text-xs">✗</span>
                            <span v-else class="text-xs text-gray-500">{{ String.fromCharCode(65 + optIndex) }}</span>
                          </div>
                          <span class="text-gray-700">{{ option }}</span>
                          <span
                            v-if="option === result.correct_answer"
                            class="ml-auto text-xs font-medium text-emerald-600"
                          >
                            Correct Answer
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Your Answer -->
                  <div class="mb-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Your Answer:</h4>
                    <div
                      :class="[
                        'p-4 rounded-lg border',
                        result.is_correct ? 'border-emerald-300 bg-emerald-50 text-emerald-700' :
                        result.user_answer ? 'border-red-300 bg-red-50 text-red-700' :
                        'border-gray-300 bg-gray-50 text-gray-700'
                      ]"
                    >
                      <p v-if="result.user_answer" class="font-medium">
                        {{ formatAnswer(result.user_answer, result.question_type) }}
                      </p>
                      <p v-else class="italic">No answer provided (skipped)</p>
                    </div>
                  </div>

                  <!-- Correct Answer (if incorrect) -->
                  <div v-if="!result.is_correct && result.correct_answer" class="mb-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Correct Answer:</h4>
                    <div class="p-4 rounded-lg border border-emerald-300 bg-emerald-50 text-emerald-700">
                      <p class="font-medium">
                        {{ formatAnswer(result.correct_answer, result.question_type) }}
                      </p>
                    </div>
                  </div>

                  <!-- Explanation -->
                  <div v-if="result.explanation" class="mt-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Explanation:</h4>
                    <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-blue-700">{{ result.explanation }}</p>
                    </div>
                    </div>

                    <!-- Course/Topic Link -->
                    <div v-if="result.course_id && result.course_title" class="mt-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Related Learning:</h4>
                    <div class="flex items-center p-3 bg-emerald-50 border border-emerald-200 rounded-lg">
                        <AcademicCapIcon class="h-4 w-4 text-emerald-600 mr-2" />
                        <div class="flex-1">
                        <p class="text-sm text-emerald-800">
                            This question relates to
                            <span v-if="result.topic_name" class="font-semibold">{{ result.topic_name }}</span>
                            in
                            <Link
                            v-if="result.course_slug"
                            :href="route('student.courses.show', result.course_slug)"
                            class="font-semibold text-emerald-700 hover:text-emerald-800 underline"
                            >
                            {{ result.course_title }}
                            </Link>
                            <span v-else class="font-semibold">{{ result.course_title }}</span>
                        </p>
                        <p v-if="result.topic_name" class="text-xs text-emerald-600 mt-1">
                            Review this topic to strengthen your understanding
                        </p>
                        </div>
                        <Link
                        v-if="result.course_slug"
                        :href="route('student.courses.show', result.course_slug)"
                        class="inline-flex items-center text-sm font-medium text-emerald-600 hover:text-emerald-700"
                        >
                        View Course
                        <ArrowRightIcon class="h-3 w-3 ml-1" />
                        </Link>
                    </div>
                </div>
                </div>

                <!-- Right Column: Stats & Notes -->
                <div class="lg:w-80 space-y-6">
                  <!-- Performance Stats -->
                  <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Question Stats</h4>
                    <div class="space-y-3">
                      <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Difficulty:</span>
                        <span class="font-medium text-gray-900 capitalize">
                          {{ getQuestionDifficulty(index) }}
                        </span>
                      </div>
                      <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Question Type:</span>
                        <span class="font-medium text-gray-900">
                          {{ formatQuestionType(result.question_type) }}
                        </span>
                      </div>
                      <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Time Spent:</span>
                        <span class="font-medium text-gray-900">
                          {{ estimateTimePerQuestion(index) }}
                        </span>
                      </div>
                    </div>
                  </div>

                  <!-- Add Note Section -->
                  <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Add Note</h4>
                    <textarea
                      v-model="questionNotes[index]"
                      rows="3"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm"
                      placeholder="Add a note about this question for future review..."
                      @input="saveNote(index)"
                    ></textarea>
                    <p class="text-xs text-gray-500 mt-2">
                      Notes are saved automatically
                    </p>
                  </div>

                  <!-- Improvement Tips -->
                  <div v-if="!result.is_correct" class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-amber-700 mb-2">Improvement Tip</h4>
                    <p class="text-sm text-amber-600">
                      Review this topic: <span class="font-medium">{{ getRelatedTopic(index) }}</span>
                    </p>
                    <button
                      v-if="getRelatedTopic(index)"
                      @click="findRelatedExams(getRelatedTopic(index))"
                      class="mt-2 text-xs font-medium text-amber-700 hover:text-amber-800"
                    >
                      Find related practice questions →
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Footer -->
          <div class="px-8 py-6 bg-gray-50 border-t border-gray-200">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
              <div class="text-sm text-gray-600">
                Showing {{ detailedResults.length }} of {{ totalQuestions }} questions
              </div>
              <div class="flex space-x-3">
                <button
                  @click="scrollToTop"
                  class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors"
                >
                  <ArrowUpIcon class="h-4 w-4 mr-2" />
                  Back to Top
                </button>
                <button
                  @click="downloadReview"
                  class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-medium rounded-lg hover:from-emerald-600 hover:to-teal-700 transition-all duration-200 shadow-sm hover:shadow-md"
                >
                  <DocumentArrowDownIcon class="h-4 w-4 mr-2" />
                  Download Review
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Summary Card -->
        <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Performance Insights -->
          <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Performance Insights</h3>
            <div class="space-y-4">
              <div v-if="strengths.length > 0">
                <h4 class="text-sm font-medium text-emerald-700 mb-2">Strengths</h4>
                <div class="space-y-2">
                  <div
                    v-for="strength in strengths"
                    :key="strength"
                    class="flex items-center p-3 bg-emerald-50 rounded-lg"
                  >
                    <CheckCircleIcon class="h-4 w-4 text-emerald-600 mr-3" />
                    <span class="text-sm text-emerald-700">{{ strength }}</span>
                  </div>
                </div>
              </div>
              <div v-if="weaknesses.length > 0">
                <h4 class="text-sm font-medium text-red-700 mb-2">Areas for Improvement</h4>
                <div class="space-y-2">
                  <div
                    v-for="weakness in weaknesses"
                    :key="weakness"
                    class="flex items-center p-3 bg-red-50 rounded-lg"
                  >
                    <ExclamationTriangleIcon class="h-4 w-4 text-red-600 mr-3" />
                    <span class="text-sm text-red-700">{{ weakness }}</span>
                  </div>
                </div>
              </div>
              <div v-if="examTips.length > 0">
                <h4 class="text-sm font-medium text-blue-700 mb-2">Exam Tips</h4>
                <div class="space-y-2">
                  <div
                    v-for="tip in examTips"
                    :key="tip"
                    class="flex items-start p-3 bg-blue-50 rounded-lg"
                  >
                    <LightBulbIcon class="h-4 w-4 text-blue-600 mr-3 mt-0.5" />
                    <span class="text-sm text-blue-700">{{ tip }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Next Steps -->
          <div class="bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-200 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Next Steps</h3>
            <div class="space-y-4">
              <div class="flex items-start">
                <ArrowPathIcon class="h-5 w-5 text-purple-600 mt-0.5 mr-3" />
                <div>
                  <h4 class="text-sm font-medium text-purple-900">Retake Exam</h4>
                  <p class="text-sm text-purple-700 mt-1">
                    Practice makes perfect. Retake this exam to improve your score.
                  </p>
                  <button
                    v-if="canRetake"
                    @click="retakeExam"
                    class="mt-2 text-sm font-medium text-purple-600 hover:text-purple-700"
                  >
                    Start Retake →
                  </button>
                </div>
              </div>
              <div class="flex items-start">
                <AcademicCapIcon class="h-5 w-5 text-purple-600 mt-0.5 mr-3" />
                <div>
                  <h4 class="text-sm font-medium text-purple-900">Related Practice</h4>
                  <p class="text-sm text-purple-700 mt-1">
                    Find similar exams to practice weak areas.
                  </p>
                  <Link
                    :href="route('student.exam-preps.index')"
                    class="mt-2 text-sm font-medium text-purple-600 hover:text-purple-700"
                  >
                    Browse Exams →
                  </Link>
                </div>
              </div>
              <div class="flex items-start">
                <ChartBarIcon class="h-5 w-5 text-purple-600 mt-0.5 mr-3" />
                <div>
                  <h4 class="text-sm font-medium text-purple-900">Track Progress</h4>
                  <p class="text-sm text-purple-700 mt-1">
                    Monitor your improvement over time with detailed analytics.
                  </p>
                  <Link
                    :href="route('student.exam-preps.my-attempts')"
                    class="mt-2 text-sm font-medium text-purple-600 hover:text-purple-700"
                  >
                    View Progress →
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </StudentLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import {
  ChevronRightIcon,
  ArrowLeftIcon,
  ArrowPathIcon,
  ArrowDownTrayIcon,
  ArrowUpIcon,
  AcademicCapIcon,
  QuestionMarkCircleIcon,
  CalendarIcon,
  ClockIcon,
  CheckCircleIcon,
  XCircleIcon,
  DocumentArrowDownIcon,
  LightBulbIcon,
  ChartBarIcon,
  ExclamationTriangleIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  attempt: Object,
  results: Array,
})

// Local state
const questionNotes = ref({})

// Computed properties
const totalQuestions = computed(() => {
  return props.attempt.questions?.length || props.results?.length || 0
})

const totalPoints = computed(() => {
  if (props.results) {
    return props.results.reduce((sum, result) => sum + (result.points || 1), 0)
  }
  return props.attempt.score * (100 / parseFloat(props.attempt.percentage)) || 0
})

const correctAnswers = computed(() => {
  return props.results?.filter(r => r.is_correct).length || 0
})

const incorrectAnswers = computed(() => {
  return props.results?.filter(r => !r.is_correct && r.user_answer).length || 0
})

const skippedQuestions = computed(() => {
  return props.results?.filter(r => !r.user_answer).length || 0
})

const detailedResults = computed(() => {
  return props.results || []
})

const timeEfficiency = computed(() => {
  if (!props.attempt.time_spent_seconds || totalQuestions.value === 0) return 0
  const timePerQuestion = props.attempt.time_spent_seconds / totalQuestions.value
  const idealTimePerQuestion = 60 // 1 minute per question
  const efficiency = Math.max(0, Math.min(100, (idealTimePerQuestion / timePerQuestion) * 100))
  return Math.round(efficiency)
})

const canRetake = computed(() => {
  return props.attempt.exam_prep?.max_attempts === 0 ||
         props.attempt.attempt_number < props.attempt.exam_prep?.max_attempts
})

const strengths = computed(() => {
  const strengths = []
  const correctPercentage = (correctAnswers.value / totalQuestions.value) * 100

  if (correctPercentage >= 80) {
    strengths.push('Excellent overall accuracy')
  }
  if (props.attempt.is_passed) {
    strengths.push('Successfully passed the exam')
  }
  if (timeEfficiency.value >= 80) {
    strengths.push('Good time management')
  }
  if (skippedQuestions.value === 0) {
    strengths.push('Answered all questions')
  }

  return strengths
})

const weaknesses = computed(() => {
  const weaknesses = []

  if (!props.attempt.is_passed) {
    weaknesses.push('Below passing score')
  }
  if (incorrectAnswers.value / totalQuestions.value > 0.3) {
    weaknesses.push('High number of incorrect answers')
  }
  if (skippedQuestions.value / totalQuestions.value > 0.1) {
    weaknesses.push('Multiple questions skipped')
  }
  if (timeEfficiency.value < 60) {
    weaknesses.push('Could improve time management')
  }

  return weaknesses
})

const examTips = computed(() => {
  const tips = []

  if (incorrectAnswers.value > 0) {
    tips.push('Review incorrect answers to understand mistakes')
  }
  if (skippedQuestions.value > 0) {
    tips.push('Try to answer all questions - even if unsure')
  }
  if (!props.attempt.is_passed) {
    tips.push('Focus on weak areas before retaking')
  }
  if (timeEfficiency.value > 90) {
    tips.push('Take more time to review answers before submitting')
  } else if (timeEfficiency.value < 70) {
    tips.push('Practice time management for better efficiency')
  }

  return tips
})

// Helper functions
const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    month: 'long',
    day: 'numeric',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatTime = (seconds) => {
  if (!seconds) return '0 minutes'
  const secs = parseInt(seconds) || 0
  const hours = Math.floor(secs / 3600)
  const minutes = Math.floor((secs % 3600) / 60)
  const remainingSeconds = secs % 60

  if (hours > 0) {
    return `${hours}h ${minutes}m ${remainingSeconds}s`
  } else if (minutes > 0) {
    return `${minutes}m ${remainingSeconds}s`
  }
  return `${remainingSeconds}s`
}

const formatTimeShort = (seconds) => {
  if (!seconds) return '0m'
  const secs = parseInt(seconds) || 0
  const minutes = Math.floor(secs / 60)
  return `${minutes}m`
}

const formatQuestionType = (type) => {
  const types = {
    multiple_choice: 'Multiple Choice',
    true_false: 'True/False',
    short_answer: 'Short Answer',
    multiple_answer: 'Multiple Answer'
  }
  return types[type] || type || 'Question'
}

const formatAnswer = (answer, questionType) => {
  if (Array.isArray(answer)) {
    return answer.join(', ')
  }
  return answer
}

const getQuestionDifficulty = (index) => {
  const question = props.attempt.questions?.[index]
  return question?.difficulty || 'medium'
}

const estimateTimePerQuestion = (index) => {
  if (!props.attempt.time_spent_seconds || totalQuestions.value === 0) return 'N/A'
  const avgTime = props.attempt.time_spent_seconds / totalQuestions.value
  return `${Math.round(avgTime)}s`
}

const getRelatedTopic = (index) => {
  // This would typically come from question metadata
  const question = props.attempt.questions?.[index]
  const topic = question?.metadata?.topic ||
                question?.metadata?.module ||
                extractTopicFromQuestion(props.results[index]?.question_text)
  return topic || 'General Knowledge'
}

const extractTopicFromQuestion = (questionText) => {
  if (!questionText) return ''

  const keywords = ['limit', 'derivative', 'integral', 'function', 'equation', 'graph', 'probability']
  for (const keyword of keywords) {
    if (questionText.toLowerCase().includes(keyword)) {
      return keyword.charAt(0).toUpperCase() + keyword.slice(1)
    }
  }

  return 'General'
}

// Actions
const retakeExam = () => {
  router.visit(route('student.exam-preps.instructions', props.attempt.exam_prep_id))
}

const scrollToTop = () => {
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const downloadResults = () => {
  alert('Download feature coming soon!')
  // Implement PDF generation/download here
}

const downloadReview = () => {
  alert('Review download feature coming soon!')
  // Implement review PDF generation
}

const findRelatedExams = (topic) => {
  router.visit(route('student.exam-preps.index', { search: topic }))
}

const saveNote = (index) => {
  // Save note to local storage or API
  localStorage.setItem(`exam_note_${props.attempt.id}_${index}`, questionNotes.value[index])
}

const loadNotes = () => {
  if (!props.attempt?.id) return

  for (let i = 0; i < totalQuestions.value; i++) {
    const note = localStorage.getItem(`exam_note_${props.attempt.id}_${i}`)
    if (note) {
      questionNotes.value[i] = note
    }
  }
}

// Initialize
onMounted(() => {
  loadNotes()
})
</script>

<style scoped>
/* Custom styles */
.transition-colors {
  transition-property: background-color, border-color, color, fill, stroke;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 150ms;
}

/* Smooth scrolling */
html {
  scroll-behavior: smooth;
}

/* Improve readability */
.prose {
  max-width: none;
}

/* Question styling */
.question-enter-active,
.question-leave-active {
  transition: all 0.3s ease;
}

.question-enter-from,
.question-leave-to {
  opacity: 0;
  transform: translateY(10px);
}
</style>
