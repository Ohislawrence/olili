<!-- resources/js/Pages/Admin/ExamPreps/Show.vue -->
<template>
  <AdminLayout>
    <Head :title="examPrep.name" />

    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex items-center justify-between">
            <div class="flex-1 min-w-0">
              <div class="flex items-center space-x-3 mb-2">
                <h1 class="text-2xl font-bold text-gray-900">{{ examPrep.name }}</h1>
                <StatusBadge :status="examPrep.status" :statuses="statusLabels" />
                <GenerationStatusBadge :status="examPrep.content_generation_status" />
                <span
                  v-if="examPrep.is_public"
                  class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800"
                >
                  Public
                </span>
              </div>
              <p class="text-gray-600 mb-4">{{ examPrep.description || 'No description provided.' }}</p>

              <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500">
                <div class="flex items-center">
                  <AcademicCapIcon class="h-4 w-4 mr-1" />
                  <span>{{ examPrep.exam_board?.name || 'No Exam Board' }}</span>
                </div>
                <div class="flex items-center">
                  <QuestionMarkCircleIcon class="h-4 w-4 mr-1" />
                  <span>{{ examPrep.total_questions }} Total Questions</span>
                </div>
                <div class="flex items-center">
                  <ClockIcon class="h-4 w-4 mr-1" />
                  <span>{{ examPrep.time_limit_minutes }} Minutes</span>
                </div>
                <div class="flex items-center">
                  <CheckCircleIcon class="h-4 w-4 mr-1" />
                  <span>{{ examPrep.passing_score }}% Passing Score</span>
                </div>
                <div class="flex items-center">
                  <UserGroupIcon class="h-4 w-4 mr-1" />
                  <span>{{ examPrep.enrolled_count || 0 }} Enrolled</span>
                </div>
                <div v-if="examPrep.subject" class="flex items-center">
                  <BookOpenIcon class="h-4 w-4 mr-1" />
                  <span>{{ examPrep.subject.name }}</span>
                </div>
              </div>
            </div>

            <div class="flex items-center space-x-3 ml-4">
              <Link
                :href="route('admin.exam-preps.edit', examPrep.id)"
                class="inline-flex items-center px-4 py-2 border border-emerald-300 shadow-sm text-sm font-medium rounded-lg text-emerald-700 bg-white hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
              >
                <PencilIcon class="h-4 w-4 mr-2" />
                Edit
              </Link>
              <button
                v-if="examPrep.status === 'draft' && examPrep.content_generation_status === 'completed'"
                @click="publishExamPrep"
                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
              >
                <CheckCircleIcon class="h-4 w-4 mr-2" />
                Publish
              </button>
            </div>
          </div>

          <!-- Generation Progress -->
          <div v-if="examPrep.content_generation_status === 'processing'" class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-center justify-between mb-3">
              <div class="flex items-center">
                <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-600 mr-3"></div>
                <div>
                  <span class="text-sm font-medium text-blue-800">AI is generating your questions...</span>
                  <p class="text-xs text-blue-600 mt-1">This may take 1-2 minutes. You can leave this page and come back later.</p>
                </div>
              </div>
              <button
                @click="checkStatus"
                class="text-sm text-blue-600 hover:text-blue-800 font-medium"
              >
                Refresh Status
              </button>
            </div>
          </div>

          <!-- Generation Summary -->
          <div v-else-if="examPrep.content_generation_status === 'completed' && examPrep.generation_summary" class="mt-6 p-4 bg-emerald-50 border border-emerald-200 rounded-lg">
            <div class="flex items-start">
              <CheckCircleIcon class="h-5 w-5 text-emerald-600 mr-3 mt-0.5 flex-shrink-0" />
              <div class="flex-1">
                <span class="text-sm font-medium text-emerald-800">
                  AI Generation Completed
                </span>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-3">
                  <div class="bg-white rounded-lg p-3 border border-emerald-200">
                    <div class="text-xs text-gray-500">Questions Generated</div>
                    <div class="text-lg font-bold text-emerald-700">
                      {{ examPrep.generation_summary.questions_generated || examPrep.questions?.length || 0 }}
                    </div>
                  </div>
                  <div class="bg-white rounded-lg p-3 border border-emerald-200">
                    <div class="text-xs text-gray-500">Easy / Medium / Hard</div>
                    <div class="text-sm font-medium">
                      {{ examPrep.generation_summary.difficulty_distribution?.easy || 0 }} /
                      {{ examPrep.generation_summary.difficulty_distribution?.medium || 0 }} /
                      {{ examPrep.generation_summary.difficulty_distribution?.hard || 0 }}
                    </div>
                  </div>
                  <div class="bg-white rounded-lg p-3 border border-emerald-200">
                    <div class="text-xs text-gray-500">AI Model</div>
                    <div class="text-sm font-medium">{{ examPrep.ai_model_used || 'GPT-4' }}</div>
                  </div>
                  <div class="bg-white rounded-lg p-3 border border-emerald-200">
                    <div class="text-xs text-gray-500">Generated</div>
                    <div class="text-sm font-medium">{{ formatDateTime(examPrep.content_generation_completed_at) }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Tabs -->
        <div class="mb-6 border-b border-gray-200">
          <nav class="-mb-px flex space-x-8">
            <button
              v-for="tab in tabs"
              :key="tab.id"
              @click="activeTab = tab.id"
              :class="[
                'py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200 flex items-center',
                activeTab === tab.id
                  ? 'border-emerald-500 text-emerald-600'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
              ]"
            >
              <component :is="tab.icon" class="h-5 w-5 mr-2" />
              {{ tab.name }}
              <span
                v-if="tab.id === 'questions' && examPrep.questions?.length"
                class="ml-2 bg-gray-100 text-gray-600 py-0.5 px-2 rounded-full text-xs"
              >
                {{ examPrep.questions.length }}
              </span>
            </button>
          </nav>
        </div>

        <!-- Tab Content -->
        <div>
          <!-- Overview Tab -->
          <div v-if="activeTab === 'overview'" class="space-y-6">
            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
              <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-sm text-gray-500">Total Attempts</p>
                    <p class="text-2xl font-bold text-gray-900">{{ statistics.total_attempts }}</p>
                  </div>
                  <div class="p-3 bg-emerald-100 rounded-lg">
                    <ChartBarIcon class="h-6 w-6 text-emerald-600" />
                  </div>
                </div>
              </div>

              <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-sm text-gray-500">Average Score</p>
                    <p class="text-2xl font-bold text-gray-900">{{ statistics.average_score }}%</p>
                  </div>
                  <div class="p-3 bg-blue-100 rounded-lg">
                    <TrophyIcon class="h-6 w-6 text-blue-600" />
                  </div>
                </div>
              </div>

              <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-sm text-gray-500">Pass Rate</p>
                    <p class="text-2xl font-bold text-gray-900">{{ statistics.pass_rate }}%</p>
                  </div>
                  <div class="p-3 bg-green-100 rounded-lg">
                    <CheckCircleIcon class="h-6 w-6 text-green-600" />
                  </div>
                </div>
              </div>

              <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-sm text-gray-500">Unique Students</p>
                    <p class="text-2xl font-bold text-gray-900">{{ statistics.unique_students }}</p>
                  </div>
                  <div class="p-3 bg-purple-100 rounded-lg">
                    <UserGroupIcon class="h-6 w-6 text-purple-600" />
                  </div>
                </div>
              </div>
            </div>

            <!-- Difficulty Distribution -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Question Difficulty Distribution</h3>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                  <div class="relative pt-1">
                    <div class="flex mb-2 items-center justify-between">
                      <div>
                        <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-green-600 bg-green-200">
                          Easy
                        </span>
                      </div>
                      <div class="text-right">
                        <span class="text-xs font-semibold inline-block text-green-600">
                          {{ statistics.difficulty_distribution?.easy || 0 }} questions
                        </span>
                      </div>
                    </div>
                    <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-green-200">
                      <div
                        :style="{ width: (statistics.difficulty_distribution?.easy / examPrep.total_questions * 100) + '%' }"
                        class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-green-500"
                      ></div>
                    </div>
                  </div>
                </div>

                <div class="text-center">
                  <div class="relative pt-1">
                    <div class="flex mb-2 items-center justify-between">
                      <div>
                        <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-amber-600 bg-amber-200">
                          Medium
                        </span>
                      </div>
                      <div class="text-right">
                        <span class="text-xs font-semibold inline-block text-amber-600">
                          {{ statistics.difficulty_distribution?.medium || 0 }} questions
                        </span>
                      </div>
                    </div>
                    <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-amber-200">
                      <div
                        :style="{ width: (statistics.difficulty_distribution?.medium / examPrep.total_questions * 100) + '%' }"
                        class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-amber-500"
                      ></div>
                    </div>
                  </div>
                </div>

                <div class="text-center">
                  <div class="relative pt-1">
                    <div class="flex mb-2 items-center justify-between">
                      <div>
                        <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-red-600 bg-red-200">
                          Hard
                        </span>
                      </div>
                      <div class="text-right">
                        <span class="text-xs font-semibold inline-block text-red-600">
                          {{ statistics.difficulty_distribution?.hard || 0 }} questions
                        </span>
                      </div>
                    </div>
                    <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-red-200">
                      <div
                        :style="{ width: (statistics.difficulty_distribution?.hard / examPrep.total_questions * 100) + '%' }"
                        class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-red-500"
                      ></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Recent Attempts -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Recent Attempts</h3>
                <Link
                  :href="route('admin.exam-preps.attempts', examPrep.id)"
                  class="text-sm text-emerald-600 hover:text-emerald-700 font-medium"
                >
                  View All
                </Link>
              </div>

              <div v-if="recentAttempts?.length" class="space-y-3">
                <div
                  v-for="attempt in recentAttempts"
                  :key="attempt.id"
                  class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
                >
                  <div>
                    <p class="text-sm font-medium text-gray-900">{{ attempt.user.name }}</p>
                    <p class="text-xs text-gray-500">{{ formatDateTime(attempt.completed_at) }}</p>
                  </div>
                  <div class="text-right">
                    <p :class="[
                      'text-sm font-semibold',
                      attempt.is_passed ? 'text-green-600' : 'text-red-600'
                    ]">
                      {{ attempt.percentage }}%
                    </p>
                    <p class="text-xs text-gray-500">
                      {{ Math.round(attempt.time_spent_seconds / 60) }} min
                    </p>
                  </div>
                </div>
              </div>

              <div v-else class="text-center py-8">
                <UserGroupIcon class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-medium text-gray-900">No attempts yet</h3>
                <p class="mt-1 text-sm text-gray-500">
                  Students haven't attempted this exam yet.
                </p>
              </div>
            </div>
          </div>

          <!-- Questions Tab -->
          <div v-if="activeTab === 'questions'" class="space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
              <div class="flex items-center justify-between mb-6">
                <div>
                  <h3 class="text-lg font-semibold text-gray-900">Question Bank</h3>
                  <p class="text-sm text-gray-500 mt-1">
                    {{ examPrep.questions?.length || 0 }} of {{ examPrep.total_questions }} questions generated
                  </p>
                </div>
                <button
                  @click="regenerateQuestions"
                  :disabled="regenerating || examPrep.content_generation_status === 'processing'"
                  class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-sm font-medium rounded-lg hover:shadow-lg disabled:opacity-50"
                >
                  <ArrowPathIcon class="h-4 w-4 mr-2" :class="{ 'animate-spin': regenerating }" />
                  {{ regenerating ? 'Regenerating...' : 'Regenerate All' }}
                </button>
              </div>

              <!-- Question Filters -->
              <div class="flex items-center space-x-4 mb-6">
                <div class="flex-1">
                  <input
                    v-model="questionSearch"
                    type="search"
                    placeholder="Search questions..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500"
                  />
                </div>
                <select
                  v-model="difficultyFilter"
                  class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500"
                >
                  <option value="">All Difficulties</option>
                  <option value="easy">Easy</option>
                  <option value="medium">Medium</option>
                  <option value="hard">Hard</option>
                </select>
              </div>

              <!-- Questions List -->
              <div v-if="filteredQuestions.length" class="space-y-4">
                <div
                  v-for="(question, index) in filteredQuestions"
                  :key="question.id"
                  class="border border-gray-200 rounded-lg p-5 hover:shadow-md transition-shadow"
                >
                  <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center space-x-2">
                      <span class="text-sm text-gray-500">#{{ index + 1 }}</span>
                      <span
                        :class="[
                          'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                          getDifficultyClass(question.difficulty)
                        ]"
                      >
                        {{ question.difficulty }}
                      </span>
                    </div>
                    <div class="flex items-center space-x-2">
                      <button
                        @click="viewQuestionDetails(question)"
                        class="text-xs text-emerald-600 hover:text-emerald-700 font-medium"
                      >
                        View Details
                      </button>
                    </div>
                  </div>

                  <p class="text-gray-900 font-medium mb-3">{{ question.question_text }}</p>

                  <div class="space-y-2 mb-3">
                    <div
                      v-for="(option, idx) in question.options"
                      :key="idx"
                      class="flex items-center text-sm"
                    >
                      <span class="w-6 text-gray-500">{{ String.fromCharCode(65 + idx) }}.</span>
                      <span
                        :class="[
                          'flex-1',
                          option === question.correct_answer ? 'text-emerald-700 font-medium' : 'text-gray-700'
                        ]"
                      >
                        {{ option }}
                        <span v-if="option === question.correct_answer" class="ml-2 text-emerald-600">
                          ✓ Correct
                        </span>
                      </span>
                    </div>
                  </div>

                  <div v-if="question.explanation" class="bg-blue-50 border border-blue-200 rounded-lg p-3 mt-2">
                    <p class="text-xs text-blue-800 mb-1 font-medium">Explanation:</p>
                    <p class="text-sm text-blue-900">{{ question.explanation }}</p>
                  </div>

                  <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100">
                    <div class="flex items-center space-x-4">
                      <span class="text-xs text-gray-500">
                        Topic: {{ question.metadata?.topic || 'General' }}
                      </span>
                      <span v-if="question.times_used > 0" class="text-xs text-gray-500">
                        Used {{ question.times_used }} times •
                        {{ calculateSuccessRate(question) }}% correct
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <div v-else class="text-center py-12">
                <QuestionMarkCircleIcon class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-medium text-gray-900">No questions found</h3>
                <p class="mt-1 text-sm text-gray-500">
                  {{ examPrep.questions?.length ? 'Try adjusting your search filters' : 'Generate questions using AI' }}
                </p>
                <button
                  v-if="!examPrep.questions?.length"
                  @click="regenerateQuestions"
                  class="mt-4 inline-flex items-center px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700"
                >
                  <SparklesIcon class="h-4 w-4 mr-2" />
                  Generate Questions
                </button>
              </div>
            </div>
          </div>

          <!-- Settings Tab -->
          <div v-if="activeTab === 'settings'" class="space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Exam Settings</h3>

              <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <dt class="text-sm font-medium text-gray-500">Exam Name</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ examPrep.name }}</dd>
                </div>

                <div>
                  <dt class="text-sm font-medium text-gray-500">Exam Board</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ examPrep.exam_board?.name || 'Not set' }}</dd>
                </div>

                <div>
                  <dt class="text-sm font-medium text-gray-500">Subject</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ examPrep.subject?.name || 'Not set' }}</dd>
                </div>

                <div>
                  <dt class="text-sm font-medium text-gray-500">Related Course</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ examPrep.course?.title || 'None' }}</dd>
                </div>

                <div>
                  <dt class="text-sm font-medium text-gray-500">Total Questions</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ examPrep.total_questions }}</dd>
                </div>

                <div>
                  <dt class="text-sm font-medium text-gray-500">Time Limit</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ examPrep.time_limit_minutes }} minutes</dd>
                </div>

                <div>
                  <dt class="text-sm font-medium text-gray-500">Passing Score</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ examPrep.passing_score }}%</dd>
                </div>

                <div>
                  <dt class="text-sm font-medium text-gray-500">Max Attempts</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ examPrep.max_attempts === 0 ? 'Unlimited' : examPrep.max_attempts }}</dd>
                </div>

                <div>
                  <dt class="text-sm font-medium text-gray-500">Randomize Questions</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ examPrep.randomize_questions ? 'Yes' : 'No' }}</dd>
                </div>

                <div>
                  <dt class="text-sm font-medium text-gray-500">Allow Pause</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ examPrep.allow_pause ? 'Yes' : 'No' }}</dd>
                </div>

                <div>
                  <dt class="text-sm font-medium text-gray-500">Show Results Immediately</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ examPrep.show_results_immediately ? 'Yes' : 'No' }}</dd>
                </div>

                <div>
                  <dt class="text-sm font-medium text-gray-500">Visibility</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ examPrep.is_public ? 'Public' : 'Private' }}</dd>
                </div>

                <div>
                  <dt class="text-sm font-medium text-gray-500">AI Model</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ examPrep.ai_model_used || 'Not specified' }}</dd>
                </div>

                <div>
                  <dt class="text-sm font-medium text-gray-500">Created</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ formatDateTime(examPrep.created_at) }}</dd>
                </div>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Question Details Modal -->
    <Transition name="fade">
      <div
        v-if="showQuestionModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
        @click.self="showQuestionModal = false"
      >
        <div class="bg-white rounded-xl p-6 max-w-3xl w-full mx-4 max-h-[90vh] overflow-y-auto">
          <div v-if="selectedQuestion" class="space-y-4">
            <div class="flex items-center justify-between">
              <h3 class="text-lg font-semibold text-gray-900">Question Details</h3>
              <button @click="showQuestionModal = false" class="text-gray-400 hover:text-gray-600">
                <XMarkIcon class="h-5 w-5" />
              </button>
            </div>

            <div class="space-y-4">
              <div>
                <span
                  :class="[
                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                    getDifficultyClass(selectedQuestion.difficulty)
                  ]"
                >
                  {{ selectedQuestion.difficulty }}
                </span>
              </div>

              <div>
                <h4 class="text-sm font-medium text-gray-700 mb-1">Question</h4>
                <p class="text-gray-900">{{ selectedQuestion.question_text }}</p>
              </div>

              <div>
                <h4 class="text-sm font-medium text-gray-700 mb-2">Options</h4>
                <div class="space-y-2">
                  <div
                    v-for="(option, idx) in selectedQuestion.options"
                    :key="idx"
                    class="flex items-center p-2 rounded-lg"
                    :class="option === selectedQuestion.correct_answer ? 'bg-green-50 border border-green-200' : 'bg-gray-50'"
                  >
                    <span class="w-6 text-gray-500">{{ String.fromCharCode(65 + idx) }}.</span>
                    <span class="flex-1">{{ option }}</span>
                    <span v-if="option === selectedQuestion.correct_answer" class="text-green-600 text-sm font-medium">
                      Correct Answer
                    </span>
                  </div>
                </div>
              </div>

              <div v-if="selectedQuestion.explanation">
                <h4 class="text-sm font-medium text-gray-700 mb-1">Explanation</h4>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                  <p class="text-sm text-blue-900">{{ selectedQuestion.explanation }}</p>
                </div>
              </div>

              <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                <div>
                  <h4 class="text-xs font-medium text-gray-500">Topic</h4>
                  <p class="text-sm text-gray-900">{{ selectedQuestion.metadata?.topic || 'General' }}</p>
                </div>
                <div>
                  <h4 class="text-xs font-medium text-gray-500">Points</h4>
                  <p class="text-sm text-gray-900">{{ selectedQuestion.points }}</p>
                </div>
                <div>
                  <h4 class="text-xs font-medium text-gray-500">Times Used</h4>
                  <p class="text-sm text-gray-900">{{ selectedQuestion.times_used || 0 }}</p>
                </div>
                <div>
                  <h4 class="text-xs font-medium text-gray-500">Success Rate</h4>
                  <p class="text-sm text-gray-900">{{ calculateSuccessRate(selectedQuestion) }}%</p>
                </div>
              </div>

              <div v-if="selectedQuestion.metadata?.concepts_tested?.length">
                <h4 class="text-sm font-medium text-gray-700 mb-1">Concepts Tested</h4>
                <div class="flex flex-wrap gap-2">
                  <span
                    v-for="concept in selectedQuestion.metadata.concepts_tested"
                    :key="concept"
                    class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded-full"
                  >
                    {{ concept }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import StatusBadge from '@/Components/ExamPrep/StatusBadge.vue'
import GenerationStatusBadge from '@/Components/ExamPrep/GenerationStatusBadge.vue'
import {
  AcademicCapIcon,
  QuestionMarkCircleIcon,
  ClockIcon,
  CheckCircleIcon,
  UserGroupIcon,
  BookOpenIcon,
  PencilIcon,
  ChartBarIcon,
  TrophyIcon,
  ArrowPathIcon,
  SparklesIcon,
  XMarkIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  examPrep: Object,
  statistics: Object,
  recentAttempts: Array,
  topPerformers: Array,
})

// State
const activeTab = ref('overview')
const regenerating = ref(false)
const questionSearch = ref('')
const difficultyFilter = ref('')
const showQuestionModal = ref(false)
const selectedQuestion = ref(null)

const statusLabels = {
  draft: 'Draft',
  active: 'Active',
  archived: 'Archived'
}

const tabs = [
  { id: 'overview', name: 'Overview', icon: ChartBarIcon },
  { id: 'questions', name: 'Questions', icon: QuestionMarkCircleIcon },
  { id: 'settings', name: 'Settings', icon: AcademicCapIcon },
]

// Computed
const filteredQuestions = computed(() => {
  let questions = props.examPrep.questions || []

  if (difficultyFilter.value) {
    questions = questions.filter(q => q.difficulty === difficultyFilter.value)
  }

  if (questionSearch.value) {
    const search = questionSearch.value.toLowerCase()
    questions = questions.filter(q =>
      q.question_text.toLowerCase().includes(search) ||
      q.metadata?.topic?.toLowerCase().includes(search)
    )
  }

  return questions
})

// Methods
const getDifficultyClass = (difficulty) => {
  const classes = {
    easy: 'bg-green-100 text-green-800',
    medium: 'bg-amber-100 text-amber-800',
    hard: 'bg-red-100 text-red-800',
  }
  return classes[difficulty] || 'bg-gray-100 text-gray-800'
}

const formatDateTime = (datetime) => {
  if (!datetime) return 'N/A'
  return new Date(datetime).toLocaleString('en-US', {
    dateStyle: 'medium',
    timeStyle: 'short',
  })
}

const calculateSuccessRate = (question) => {
  if (!question.times_used) return 0
  return Math.round((question.times_correct / question.times_used) * 100)
}

const publishExamPrep = async () => {
  if (confirm(`Publish "${props.examPrep.name}"? This will make it available to students.`)) {
    await router.post(route('admin.exam-preps.publish', props.examPrep.id), {}, {
      preserveScroll: true,
      onSuccess: () => {
        router.reload()
      },
    })
  }
}

const regenerateQuestions = async () => {
  if (!confirm('Regenerate all questions? This will replace all current questions.')) {
    return
  }

  regenerating.value = true

  try {
    const response = await fetch(route('admin.exam-preps.generate-questions', props.examPrep.id), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      },
    })

    const data = await response.json()

    if (data.success) {
      alert('Question generation started! This may take a few moments.')
      router.reload({ only: ['examPrep'] })
    } else {
      throw new Error(data.message)
    }
  } catch (error) {
    console.error('Failed to regenerate questions:', error)
    alert('Failed to start question generation: ' + error.message)
  } finally {
    regenerating.value = false
  }
}

const checkStatus = () => {
  router.reload({ only: ['examPrep'] })
}

const viewQuestionDetails = (question) => {
  selectedQuestion.value = question
  showQuestionModal.value = true
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
