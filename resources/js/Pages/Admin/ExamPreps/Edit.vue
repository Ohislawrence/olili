<!-- resources/js/Pages/Admin/ExamPreps/Edit.vue -->
<template>
  <AdminLayout>
    <Head :title="`Edit: ${examPrep.name}`" />

    <div class="py-6">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex items-center justify-between">
            <div>
              <div class="flex items-center space-x-2">
                <h1 class="text-2xl font-bold text-gray-900">Edit Exam Preparation</h1>
                <GenerationStatusBadge :status="examPrep.content_generation_status" />
              </div>
              <p class="mt-1 text-sm text-gray-600">
                Update exam settings and manage AI-generated questions
              </p>
            </div>
            <div class="flex items-center space-x-3">
              <Link
                :href="route('admin.exam-preps.show', examPrep.id)"
                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
              >
                <EyeIcon class="h-4 w-4 mr-2" />
                Preview
              </Link>
              <Link
                :href="route('admin.exam-preps.index')"
                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
              >
                <ArrowLeftIcon class="h-4 w-4 mr-2" />
                Back to List
              </Link>
            </div>
          </div>

          <!-- Generation Progress -->
          <div v-if="examPrep.content_generation_status === 'processing'" class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-center justify-between mb-3">
              <div class="flex items-center">
                <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-600 mr-3"></div>
                <span class="text-sm font-medium text-blue-800">AI is generating questions...</span>
              </div>
              <button
                @click="checkGenerationStatus"
                class="text-sm text-blue-600 hover:text-blue-800 font-medium"
              >
                Refresh Status
              </button>
            </div>
            <div class="w-full bg-blue-200 rounded-full h-2">
              <div
                class="bg-blue-600 h-2 rounded-full transition-all duration-500"
                :style="{ width: generationProgress + '%' }"
              ></div>
            </div>
            <p class="mt-2 text-xs text-blue-600">
              Started: {{ formatDateTime(examPrep.content_generation_started_at) }}
            </p>
          </div>

          <!-- Generation Success -->
          <div v-else-if="examPrep.content_generation_status === 'completed'" class="mt-6 p-4 bg-emerald-50 border border-emerald-200 rounded-lg">
            <div class="flex items-center">
              <CheckCircleIcon class="h-5 w-5 text-emerald-600 mr-3" />
              <div>
                <span class="text-sm font-medium text-emerald-800">
                  AI generation completed successfully
                </span>
                <p class="text-xs text-emerald-600 mt-1">
                  {{ examPrep.generation_summary?.questions_generated || examPrep.questions?.length || 0 }} questions generated
                </p>
              </div>
            </div>
          </div>

          <!-- Generation Failed -->
          <div v-else-if="examPrep.content_generation_status === 'failed'" class="mt-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <ExclamationTriangleIcon class="h-5 w-5 text-red-600 mr-3" />
                <div>
                  <span class="text-sm font-medium text-red-800">
                    AI generation failed
                  </span>
                  <p class="text-xs text-red-600 mt-1">
                    {{ examPrep.generation_summary?.error || 'Unknown error occurred' }}
                  </p>
                </div>
              </div>
              <button
                @click="regenerateQuestions"
                class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
              >
                Retry Generation
              </button>
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
          <!-- Settings Tab -->
          <div v-if="activeTab === 'settings'" class="space-y-6">
            <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
              <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                  <Cog6ToothIcon class="h-5 w-5 mr-2 text-emerald-600" />
                  Exam Settings
                </h2>

                <form @submit.prevent="updateExamPrep">
                  <div class="space-y-6">
                    <!-- Basic Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                          Exam Prep Name *
                        </label>
                        <input
                          v-model="form.name"
                          type="text"
                          required
                          class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                        />
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                          Exam Board *
                        </label>
                        <select
                          v-model="form.exam_board_id"
                          required
                          class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                        >
                          <option value="">Select exam board</option>
                          <option
                            v-for="board in examBoards"
                            :key="board.id"
                            :value="board.id"
                          >
                            {{ board.name }}
                          </option>
                        </select>
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                          Subject
                        </label>
                        <select
                          v-model="form.subject_id"
                          class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                        >
                          <option value="">Select subject</option>
                          <option
                            v-for="subject in subjects"
                            :key="subject.id"
                            :value="subject.id"
                          >
                            {{ subject.name }}
                          </option>
                        </select>
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                          Related Course
                        </label>
                        <select
                          v-model="form.course_id"
                          class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                        >
                          <option value="">Select course</option>
                          <option
                            v-for="course in courses"
                            :key="course.id"
                            :value="course.id"
                          >
                            {{ course.title }}
                          </option>
                        </select>
                      </div>
                    </div>

                    <!-- Description -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">
                        Description
                      </label>
                      <textarea
                        v-model="form.description"
                        rows="3"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                      ></textarea>
                    </div>

                    <!-- Exam Configuration -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                          Total Questions
                        </label>
                        <input
                          v-model.number="form.total_questions"
                          type="number"
                          min="5"
                          max="100"
                          class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                        />
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                          Time Limit (min)
                        </label>
                        <input
                          v-model.number="form.time_limit_minutes"
                          type="number"
                          min="10"
                          max="300"
                          class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                        />
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                          Passing Score (%)
                        </label>
                        <input
                          v-model.number="form.passing_score"
                          type="number"
                          min="1"
                          max="100"
                          class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                        />
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                          Max Attempts
                        </label>
                        <input
                          v-model.number="form.max_attempts"
                          type="number"
                          min="0"
                          class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                        />
                        <p class="mt-1 text-xs text-gray-500">0 = unlimited</p>
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                          Status
                        </label>
                        <select
                          v-model="form.status"
                          class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                        >
                          <option value="draft">Draft</option>
                          <option value="active">Active</option>
                          <option value="archived">Archived</option>
                        </select>
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                          Visibility
                        </label>
                        <div class="flex items-center mt-2">
                          <input
                            id="is_public"
                            v-model="form.is_public"
                            type="checkbox"
                            class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded"
                          />
                          <label for="is_public" class="ml-2 text-sm text-gray-700">
                            Public access
                          </label>
                        </div>
                      </div>
                    </div>

                    <!-- Exam Features -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                      <div class="flex items-center">
                        <input
                          id="randomize_questions"
                          v-model="form.randomize_questions"
                          type="checkbox"
                          class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded"
                        />
                        <label for="randomize_questions" class="ml-2 text-sm text-gray-700">
                          Randomize questions
                        </label>
                      </div>

                      <div class="flex items-center">
                        <input
                          id="allow_pause"
                          v-model="form.allow_pause"
                          type="checkbox"
                          class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded"
                        />
                        <label for="allow_pause" class="ml-2 text-sm text-gray-700">
                          Allow pause & resume
                        </label>
                      </div>

                      <div class="flex items-center">
                        <input
                          id="show_results_immediately"
                          v-model="form.show_results_immediately"
                          type="checkbox"
                          class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded"
                        />
                        <label for="show_results_immediately" class="ml-2 text-sm text-gray-700">
                          Show results immediately
                        </label>
                      </div>
                    </div>
                  </div>

                  <div class="mt-6 flex justify-end space-x-3">
                    <button
                      type="button"
                      @click="activeTab = 'questions'"
                      class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                    >
                      Cancel
                    </button>
                    <button
                      type="submit"
                      :disabled="form.processing"
                      class="px-4 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-sm font-medium rounded-lg hover:shadow-lg disabled:opacity-50"
                    >
                      {{ form.processing ? 'Saving...' : 'Save Settings' }}
                    </button>
                  </div>
                </form>
              </div>
            </div>

            <!-- Danger Zone -->
            <div class="bg-red-50 border border-red-200 rounded-xl overflow-hidden">
              <div class="px-6 py-4 border-b border-red-200">
                <h3 class="text-lg font-semibold text-red-800">Danger Zone</h3>
              </div>
              <div class="p-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between">
                  <div>
                    <h4 class="text-sm font-medium text-red-800">Delete Exam Prep</h4>
                    <p class="text-sm text-red-600 mt-1">
                      Once deleted, all attempts, questions, and enrollment data will be permanently removed.
                    </p>
                  </div>
                  <button
                    type="button"
                    @click="confirmDelete"
                    class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                  >
                    <TrashIcon class="h-4 w-4 mr-2" />
                    Delete Exam Prep
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Questions Tab -->
          <div v-if="activeTab === 'questions'" class="space-y-6">
            <!-- AI Generation Controls -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
              <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                  <div>
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                      <SparklesIcon class="h-5 w-5 mr-2 text-emerald-600" />
                      AI Question Management
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                      Generate, regenerate, or fine-tune your exam questions
                    </p>
                  </div>
                  <div class="flex space-x-3">
                    <button
                      @click="showDistributionModal = true"
                      class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                    >
                      <AdjustmentsHorizontalIcon class="h-4 w-4 mr-2" />
                      Adjust Distribution
                    </button>
                    <button
                      @click="regenerateQuestions"
                      :disabled="regenerating"
                      class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-sm font-medium rounded-lg hover:shadow-lg disabled:opacity-50"
                    >
                      <ArrowPathIcon class="h-4 w-4 mr-2" :class="{ 'animate-spin': regenerating }" />
                      {{ regenerating ? 'Generating...' : 'Regenerate All' }}
                    </button>
                  </div>
                </div>

                <!-- Question Stats -->
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                  <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <div class="text-2xl font-bold text-gray-900">{{ examPrep.questions?.length || 0 }}</div>
                    <div class="text-xs text-gray-500 mt-1">Total Questions</div>
                  </div>
                  <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                    <div class="text-2xl font-bold text-green-700">{{ questionStats.easy }}</div>
                    <div class="text-xs text-green-600 mt-1">Easy</div>
                  </div>
                  <div class="bg-amber-50 rounded-lg p-4 border border-amber-200">
                    <div class="text-2xl font-bold text-amber-700">{{ questionStats.medium }}</div>
                    <div class="text-xs text-amber-600 mt-1">Medium</div>
                  </div>
                  <div class="bg-red-50 rounded-lg p-4 border border-red-200">
                    <div class="text-2xl font-bold text-red-700">{{ questionStats.hard }}</div>
                    <div class="text-xs text-red-600 mt-1">Hard</div>
                  </div>
                  <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                    <div class="text-2xl font-bold text-purple-700">{{ questionStats.successRate }}%</div>
                    <div class="text-xs text-purple-600 mt-1">Success Rate</div>
                  </div>
                </div>

                <!-- Questions List -->
                <div class="space-y-4">
                  <div class="flex items-center justify-between">
                    <h3 class="text-md font-medium text-gray-900">Question Bank</h3>
                    <div class="flex items-center space-x-2">
                      <input
                        v-model="questionSearch"
                        type="search"
                        placeholder="Search questions..."
                        class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500"
                      />
                      <select
                        v-model="difficultyFilter"
                        class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500"
                      >
                        <option value="">All Difficulties</option>
                        <option value="easy">Easy</option>
                        <option value="medium">Medium</option>
                        <option value="hard">Hard</option>
                      </select>
                    </div>
                  </div>

                  <div v-if="filteredQuestions.length === 0" class="text-center py-12">
                    <QuestionMarkCircleIcon class="mx-auto h-12 w-12 text-gray-400" />
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No questions found</h3>
                    <p class="mt-1 text-sm text-gray-500">
                      {{ examPrep.questions?.length ? 'Try adjusting your filters' : 'Generate questions using AI' }}
                    </p>
                  </div>

                  <div v-else class="space-y-3">
                    <div
                      v-for="question in filteredQuestions"
                      :key="question.id"
                      class="bg-white border border-gray-200 rounded-lg p-5 hover:shadow-md transition-shadow"
                    >
                      <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                          <div class="flex items-center space-x-2 mb-2">
                            <span
                              :class="[
                                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                getDifficultyClass(question.difficulty)
                              ]"
                            >
                              {{ question.difficulty }}
                            </span>
                            <span class="text-xs text-gray-500">
                              #{{ question.order }}
                            </span>
                            <span v-if="question.times_used > 0" class="text-xs text-gray-500">
                              • Used {{ question.times_used }} times •
                              {{ question.getSuccessRate?.() || 0 }}% correct
                            </span>
                          </div>
                          <p class="text-gray-900 font-medium mb-3">{{ question.question_text }}</p>

                          <!-- Options -->
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

                          <!-- Explanation -->
                          <div v-if="question.explanation" class="bg-blue-50 border border-blue-200 rounded-lg p-3 mt-2">
                            <p class="text-xs text-blue-800 mb-1 font-medium">Explanation:</p>
                            <p class="text-sm text-blue-900">{{ question.explanation }}</p>
                          </div>

                          <!-- Metadata -->
                          <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100">
                            <div class="flex items-center space-x-4">
                              <span class="text-xs text-gray-500">
                                Topic: {{ question.metadata?.topic || 'General' }}
                              </span>
                              <span v-if="question.metadata?.concepts_tested?.length" class="text-xs text-gray-500">
                                Concepts: {{ question.metadata.concepts_tested.join(', ') }}
                              </span>
                            </div>
                            <div class="flex space-x-2">
                              <button
                                @click="editQuestion(question)"
                                class="text-xs text-emerald-600 hover:text-emerald-700 font-medium"
                              >
                                Edit
                              </button>
                              <button
                                @click="regenerateQuestion(question.id)"
                                class="text-xs text-blue-600 hover:text-blue-700 font-medium"
                              >
                                Regenerate
                              </button>
                              <button
                                @click="deleteQuestion(question.id)"
                                class="text-xs text-red-600 hover:text-red-700 font-medium"
                              >
                                Delete
                              </button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Attempts Tab -->
          <div v-if="activeTab === 'attempts'" class="space-y-6">
            <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
              <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Student Attempts</h2>
                <!-- Attempts list component would go here -->
                <p class="text-gray-500 text-center py-8">
                  Attempts tracking will be displayed here
                </p>
              </div>
            </div>
          </div>

          <!-- Analytics Tab -->
          <div v-if="activeTab === 'analytics'" class="space-y-6">
            <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
              <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Performance Analytics</h2>
                <!-- Analytics component would go here -->
                <p class="text-gray-500 text-center py-8">
                  Detailed analytics will be displayed here
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <Transition name="fade">
      <div
        v-if="showDeleteModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
        @click.self="showDeleteModal = false"
      >
        <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4">
          <div class="text-center">
            <ExclamationTriangleIcon class="h-12 w-12 text-red-500 mx-auto mb-4" />
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Delete Exam Prep?</h3>
            <p class="text-gray-600 mb-6">
              Are you sure you want to delete "<strong>{{ examPrep.name }}</strong>"?
              This action cannot be undone.
            </p>
            <div class="flex space-x-3">
              <button
                @click="showDeleteModal = false"
                class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors"
              >
                Cancel
              </button>
              <button
                @click="deleteExamPrep"
                :disabled="deleting"
                class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors disabled:opacity-50"
              >
                {{ deleting ? 'Deleting...' : 'Yes, Delete' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Distribution Modal -->
    <Transition name="fade">
      <div
        v-if="showDistributionModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
        @click.self="showDistributionModal = false"
      >
        <div class="bg-white rounded-xl p-6 max-w-lg w-full mx-4">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Adjust Question Distribution</h3>
            <button @click="showDistributionModal = false" class="text-gray-400 hover:text-gray-600">
              <XMarkIcon class="h-5 w-5" />
            </button>
          </div>

          <p class="text-sm text-gray-500 mb-6">
            Set the number of questions for each difficulty level. The AI will regenerate questions based on this distribution.
          </p>

          <div class="space-y-4 mb-6">
            <div class="flex items-center justify-between">
              <label class="text-sm font-medium text-gray-700">Easy Questions</label>
              <input
                v-model.number="form.question_distribution.easy"
                type="number"
                min="0"
                :max="form.total_questions"
                class="w-24 px-3 py-2 border border-gray-300 rounded-lg"
              />
            </div>
            <div class="flex items-center justify-between">
              <label class="text-sm font-medium text-gray-700">Medium Questions</label>
              <input
                v-model.number="form.question_distribution.medium"
                type="number"
                min="0"
                :max="form.total_questions"
                class="w-24 px-3 py-2 border border-gray-300 rounded-lg"
              />
            </div>
            <div class="flex items-center justify-between">
              <label class="text-sm font-medium text-gray-700">Hard Questions</label>
              <input
                v-model.number="form.question_distribution.hard"
                type="number"
                min="0"
                :max="form.total_questions"
                class="w-24 px-3 py-2 border border-gray-300 rounded-lg"
              />
            </div>
          </div>

          <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <div class="flex justify-between text-sm">
              <span class="text-gray-600">Total:</span>
              <span :class="distributionTotal === form.total_questions ? 'text-emerald-600 font-medium' : 'text-red-600 font-medium'">
                {{ distributionTotal }} / {{ form.total_questions }}
              </span>
            </div>
          </div>

          <div class="flex space-x-3">
            <button
              @click="showDistributionModal = false"
              class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50"
            >
              Cancel
            </button>
            <button
              @click="applyDistribution"
              :disabled="distributionTotal !== form.total_questions"
              class="flex-1 px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-medium rounded-lg hover:shadow-lg disabled:opacity-50"
            >
              Apply & Regenerate
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import GenerationStatusBadge from '@/Components/ExamPrep/GenerationStatusBadge.vue'
import {
  ArrowLeftIcon,
  EyeIcon,
  Cog6ToothIcon,
  SparklesIcon,
  AdjustmentsHorizontalIcon,
  ArrowPathIcon,
  CheckCircleIcon,
  ExclamationTriangleIcon,
  TrashIcon,
  QuestionMarkCircleIcon,
  XMarkIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  examPrep: Object,
  examBoards: Array,
  subjects: Array,
  courses: Array,
  difficultyLevels: Object,
  aiModels: Object,
})

// State
const activeTab = ref('settings')
const regenerating = ref(false)
const showDeleteModal = ref(false)
const showDistributionModal = ref(false)
const deleting = ref(false)
const questionSearch = ref('')
const difficultyFilter = ref('')

const tabs = [
  { id: 'settings', name: 'Settings', icon: Cog6ToothIcon },
  { id: 'questions', name: 'Questions', icon: QuestionMarkCircleIcon },
  { id: 'attempts', name: 'Attempts', icon: ArrowPathIcon },
  { id: 'analytics', name: 'Analytics', icon: AdjustmentsHorizontalIcon },
]

// Form state
const form = reactive({
  name: props.examPrep.name,
  description: props.examPrep.description,
  exam_board_id: props.examPrep.exam_board_id,
  subject_id: props.examPrep.subject_id,
  course_id: props.examPrep.course_id,
  total_questions: props.examPrep.total_questions,
  time_limit_minutes: props.examPrep.time_limit_minutes,
  passing_score: props.examPrep.passing_score,
  max_attempts: props.examPrep.max_attempts,
  randomize_questions: props.examPrep.randomize_questions,
  allow_pause: props.examPrep.allow_pause,
  show_results_immediately: props.examPrep.show_results_immediately,
  is_public: props.examPrep.is_public,
  status: props.examPrep.status,
  question_distribution: {
    easy: props.examPrep.question_distribution?.easy || null,
    medium: props.examPrep.question_distribution?.medium || null,
    hard: props.examPrep.question_distribution?.hard || null,
  },
  processing: false,
  errors: {},
})

// Computed
const questionStats = computed(() => {
  const questions = props.examPrep.questions || []
  const easy = questions.filter(q => q.difficulty === 'easy').length
  const medium = questions.filter(q => q.difficulty === 'medium').length
  const hard = questions.filter(q => q.difficulty === 'hard').length
  const totalAttempts = questions.reduce((sum, q) => sum + (q.times_used || 0), 0)
  const correctAttempts = questions.reduce((sum, q) => sum + (q.times_correct || 0), 0)
  const successRate = totalAttempts > 0 ? Math.round((correctAttempts / totalAttempts) * 100) : 0

  return { easy, medium, hard, totalAttempts, successRate }
})

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

const distributionTotal = computed(() => {
  const { easy, medium, hard } = form.question_distribution
  return (parseInt(easy) || 0) + (parseInt(medium) || 0) + (parseInt(hard) || 0)
})

const generationProgress = computed(() => {
  // Calculate estimated progress
  return 50 // Placeholder
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
  return new Date(datetime).toLocaleString()
}

const updateExamPrep = async () => {
  form.processing = true

  try {
    await router.put(route('admin.exam-preps.update', props.examPrep.id), form, {
      preserveScroll: true,
      onError: (errors) => {
        form.processing = false
        form.errors = errors
      },
      onSuccess: () => {
        form.processing = false
      },
    })
  } catch (error) {
    form.processing = false
    console.error('Update failed:', error)
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

const regenerateQuestion = async (questionId) => {
  if (!confirm('Regenerate this question?')) {
    return
  }

  // Implement single question regeneration
  console.log('Regenerate question:', questionId)
}

const editQuestion = (question) => {
  // Implement question editing
  console.log('Edit question:', question)
}

const deleteQuestion = async (questionId) => {
  if (!confirm('Delete this question?')) {
    return
  }

  // Implement question deletion
  console.log('Delete question:', questionId)
}

const applyDistribution = async () => {
  if (distributionTotal.value !== form.total_questions) {
    alert('Total distributed questions must equal total questions.')
    return
  }

  showDistributionModal.value = false
  form.regenerate_questions = true
  await regenerateQuestions()
}

const checkGenerationStatus = () => {
  router.reload({ only: ['examPrep'] })
}

const confirmDelete = () => {
  showDeleteModal.value = true
}

const deleteExamPrep = async () => {
  deleting.value = true

  try {
    await router.delete(route('admin.exam-preps.destroy', props.examPrep.id), {
      onFinish: () => {
        deleting.value = false
        showDeleteModal.value = false
      },
    })
  } catch (error) {
    deleting.value = false
    console.error('Delete failed:', error)
  }
}

// Initialize
onMounted(() => {
  // Parse question criteria if needed
})
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
