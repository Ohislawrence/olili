<!-- resources/js/Pages/Admin/ExamPreps/Edit.vue -->
<template>
  <AdminLayout>
    <Head :title="`Edit Exam Prep: ${examPrep.name}`" />

    <div class="py-6">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-2xl font-bold text-gray-900">Edit Exam Preparation</h1>
              <p class="mt-1 text-sm text-gray-600">
                Update exam prep details and configuration
              </p>
            </div>
            <div class="flex items-center space-x-3">
              <Link
                :href="route('admin.exam-preps.show', examPrep.id)"
                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
              >
                <EyeIcon class="h-4 w-4 mr-2" />
                Preview
              </Link>
              <Link
                :href="route('admin.exam-preps.index')"
                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
              >
                <ArrowLeftIcon class="h-4 w-4 mr-2" />
                Back to List
              </Link>
            </div>
          </div>

          <!-- Status Badges -->
          <div class="mt-4 flex flex-wrap gap-2">
            <span
              :class="[
                'inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold',
                examPrep.status === 'active' ? 'bg-emerald-100 text-emerald-800' :
                examPrep.status === 'draft' ? 'bg-gray-100 text-gray-800' :
                'bg-amber-100 text-amber-800'
              ]"
            >
              {{ statusLabels[examPrep.status] }}
            </span>
            <span
              v-if="examPrep.is_public"
              class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800"
            >
              Public
            </span>
            <span
              v-else
              class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-gray-100 text-gray-800"
            >
              Private
            </span>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-emerald-100 text-emerald-800">
              {{ examPrep.total_questions }} Questions
            </span>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-purple-100 text-purple-800">
              {{ examPrep.enrolled_count }} Enrolled
            </span>
          </div>
        </div>

        <!-- Form -->
        <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
          <form @submit.prevent="submit">
            <div class="p-6 space-y-8">
              <!-- Basic Information Section -->
              <div class="space-y-6">
                <div class="border-b border-gray-200 pb-4">
                  <h2 class="text-lg font-semibold text-gray-900">Basic Information</h2>
                  <p class="mt-1 text-sm text-gray-500">General information about the exam prep</p>
                </div>

                <!-- Exam Prep Name -->
                <div>
                  <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                    Exam Prep Name *
                  </label>
                  <input
                    id="name"
                    v-model="form.name"
                    type="text"
                    required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                    placeholder="e.g., WAEC Mathematics Practice Exam"
                  />
                  <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                    {{ form.errors.name }}
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
                    rows="3"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                    placeholder="Describe what this exam prep covers..."
                  />
                  <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                    {{ form.errors.description }}
                  </p>
                </div>

                <!-- Exam Board Selection -->
                <div>
                  <label for="exam_board_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Exam Board *
                  </label>
                  <select
                    id="exam_board_id"
                    v-model="form.exam_board_id"
                    required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                  >
                    <option value="">Select an exam board</option>
                    <option
                      v-for="examBoard in examBoards"
                      :key="examBoard.id"
                      :value="examBoard.id"
                    >
                      {{ examBoard.name }}
                    </option>
                  </select>
                  <p v-if="form.errors.exam_board_id" class="mt-1 text-sm text-red-600">
                    {{ form.errors.exam_board_id }}
                  </p>
                </div>

                <!-- Subject Selection -->
                <div>
                  <label for="subject_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Subject (Optional)
                  </label>
                  <select
                    id="subject_id"
                    v-model="form.subject_id"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                  >
                    <option value="">Select a subject (optional)</option>
                    <option
                      v-for="subject in subjects"
                      :key="subject.id"
                      :value="subject.id"
                    >
                      {{ subject.name }}
                    </option>
                  </select>
                  <p class="mt-1 text-sm text-gray-500">
                    Select a specific subject to filter questions
                  </p>
                </div>

                <!-- Course Selection -->
                <div>
                  <label for="course_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Course (Optional)
                  </label>
                  <select
                    id="course_id"
                    v-model="form.course_id"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                  >
                    <option value="">Select a course (optional)</option>
                    <option
                      v-for="course in courses"
                      :key="course.id"
                      :value="course.id"
                    >
                      {{ course.title }}
                    </option>
                  </select>
                  <p class="mt-1 text-sm text-gray-500">
                    Select a specific course to pull questions from
                  </p>
                </div>
              </div>

              <!-- Exam Configuration Section -->
              <div class="space-y-6">
                <div class="border-b border-gray-200 pb-4">
                  <h2 class="text-lg font-semibold text-gray-900">Exam Configuration</h2>
                  <p class="mt-1 text-sm text-gray-500">Configure the exam settings</p>
                </div>

                <!-- Total Questions -->
                <div>
                  <label for="total_questions" class="block text-sm font-medium text-gray-700 mb-1">
                    Total Questions *
                  </label>
                  <input
                    id="total_questions"
                    v-model="form.total_questions"
                    type="number"
                    min="10"
                    max="200"
                    required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                    placeholder="e.g., 50"
                  />
                  <p v-if="form.errors.total_questions" class="mt-1 text-sm text-red-600">
                    {{ form.errors.total_questions }}
                  </p>
                  <p class="mt-1 text-sm text-gray-500">
                    Number of questions in the exam (10-200)
                  </p>
                </div>

                <!-- Time Limit -->
                <div>
                  <label for="time_limit_minutes" class="block text-sm font-medium text-gray-700 mb-1">
                    Time Limit (Minutes) *
                  </label>
                  <input
                    id="time_limit_minutes"
                    v-model="form.time_limit_minutes"
                    type="number"
                    min="10"
                    max="300"
                    required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                    placeholder="e.g., 60"
                  />
                  <p v-if="form.errors.time_limit_minutes" class="mt-1 text-sm text-red-600">
                    {{ form.errors.time_limit_minutes }}
                  </p>
                  <p class="mt-1 text-sm text-gray-500">
                    Total time allowed for the exam (10-300 minutes)
                  </p>
                </div>

                <!-- Passing Score -->
                <div>
                  <label for="passing_score" class="block text-sm font-medium text-gray-700 mb-1">
                    Passing Score (%) *
                  </label>
                  <input
                    id="passing_score"
                    v-model="form.passing_score"
                    type="number"
                    min="1"
                    max="100"
                    required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                    placeholder="e.g., 70"
                  />
                  <p v-if="form.errors.passing_score" class="mt-1 text-sm text-red-600">
                    {{ form.errors.passing_score }}
                  </p>
                  <p class="mt-1 text-sm text-gray-500">
                    Minimum score required to pass the exam (1-100%)
                  </p>
                </div>

                <!-- Max Attempts -->
                <div>
                  <label for="max_attempts" class="block text-sm font-medium text-gray-700 mb-1">
                    Maximum Attempts *
                  </label>
                  <input
                    id="max_attempts"
                    v-model="form.max_attempts"
                    type="number"
                    min="0"
                    required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                    placeholder="e.g., 3 (0 for unlimited)"
                  />
                  <p v-if="form.errors.max_attempts" class="mt-1 text-sm text-red-600">
                    {{ form.errors.max_attempts }}
                  </p>
                  <p class="mt-1 text-sm text-gray-500">
                    Maximum number of attempts allowed (0 = unlimited)
                  </p>
                </div>

                <!-- Exam Settings -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <!-- Randomize Questions -->
                  <div class="flex items-start">
                    <div class="flex items-center h-5">
                      <input
                        id="randomize_questions"
                        v-model="form.randomize_questions"
                        type="checkbox"
                        class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded"
                      />
                    </div>
                    <div class="ml-3">
                      <label for="randomize_questions" class="text-sm font-medium text-gray-700">
                        Randomize Questions
                      </label>
                      <p class="text-sm text-gray-500">
                        Shuffle questions for each attempt
                      </p>
                    </div>
                  </div>

                  <!-- Allow Pause -->
                  <div class="flex items-start">
                    <div class="flex items-center h-5">
                      <input
                        id="allow_pause"
                        v-model="form.allow_pause"
                        type="checkbox"
                        class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded"
                      />
                    </div>
                    <div class="ml-3">
                      <label for="allow_pause" class="text-sm font-medium text-gray-700">
                        Allow Pause
                      </label>
                      <p class="text-sm text-gray-500">
                        Allow students to pause and resume the exam
                      </p>
                    </div>
                  </div>

                  <!-- Show Results Immediately -->
                  <div class="flex items-start">
                    <div class="flex items-center h-5">
                      <input
                        id="show_results_immediately"
                        v-model="form.show_results_immediately"
                        type="checkbox"
                        class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded"
                      />
                    </div>
                    <div class="ml-3">
                      <label for="show_results_immediately" class="text-sm font-medium text-gray-700">
                        Show Results Immediately
                      </label>
                      <p class="text-sm text-gray-500">
                        Show results right after submission
                      </p>
                    </div>
                  </div>

                  <!-- Public Access -->
                  <div class="flex items-start">
                    <div class="flex items-center h-5">
                      <input
                        id="is_public"
                        v-model="form.is_public"
                        type="checkbox"
                        class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded"
                      />
                    </div>
                    <div class="ml-3">
                      <label for="is_public" class="text-sm font-medium text-gray-700">
                        Public Access
                      </label>
                      <p class="text-sm text-gray-500">
                        Make this exam prep available to all students
                      </p>
                    </div>
                  </div>
                </div>

                <!-- Status -->
                <div>
                  <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                    Status *
                  </label>
                  <select
                    id="status"
                    v-model="form.status"
                    required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                  >
                    <option
                      v-for="(label, value) in statusLabels"
                      :key="value"
                      :value="value"
                    >
                      {{ label }}
                    </option>
                  </select>
                  <p v-if="form.errors.status" class="mt-1 text-sm text-red-600">
                    {{ form.errors.status }}
                  </p>
                </div>
              </div>

              <!-- Question Criteria Section -->
              <div class="space-y-6">
                <div class="border-b border-gray-200 pb-4">
                  <h2 class="text-lg font-semibold text-gray-900">Question Criteria</h2>
                  <p class="mt-1 text-sm text-gray-500">Filter and distribute questions</p>
                </div>

                <!-- Question Criteria -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-3">
                    Question Filters
                  </label>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Difficulty Filter -->
                    <div>
                      <label for="difficulty_filter" class="block text-sm text-gray-600 mb-1">
                        Difficulty Level
                      </label>
                      <select
                        id="difficulty_filter"
                        v-model="form.question_criteria.difficulty"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                      >
                        <option value="">All Difficulties</option>
                        <option
                          v-for="(label, value) in difficultyLevels"
                          :key="value"
                          :value="value"
                        >
                          {{ label }}
                        </option>
                      </select>
                    </div>

                    <!-- Question Type Filter -->
                    <div>
                      <label for="question_type_filter" class="block text-sm text-gray-600 mb-1">
                        Question Type
                      </label>
                      <select
                        id="question_type_filter"
                        v-model="form.question_criteria.question_type"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                      >
                        <option value="">All Types</option>
                        <option
                          v-for="(label, value) in questionTypes"
                          :key="value"
                          :value="value"
                        >
                          {{ label }}
                        </option>
                      </select>
                    </div>
                  </div>
                </div>

                <!-- Question Distribution -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-3">
                    Question Distribution (Optional)
                  </label>
                  <p class="text-sm text-gray-500 mb-4">
                    Set the number of questions for each difficulty level. Leave empty for random selection.
                  </p>

                  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div
                      v-for="(label, value) in difficultyLevels"
                      :key="value"
                      class="space-y-2"
                    >
                      <label :for="`distribution_${value}`" class="block text-sm text-gray-600">
                        {{ label }}
                      </label>
                      <input
                        :id="`distribution_${value}`"
                        v-model="form.question_distribution[value]"
                        type="number"
                        min="0"
                        :max="form.total_questions"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2 px-3"
                        placeholder="0"
                      />
                    </div>
                  </div>

                  <div class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                    <p class="text-sm text-amber-700">
                      <strong>Note:</strong> Total distributed questions: {{ distributedTotal }}/{{ form.total_questions }}
                      {{ distributionWarning }}
                    </p>
                  </div>
                </div>

                <!-- Regenerate Questions Option -->
                <div class="flex items-start">
                  <div class="flex items-center h-5">
                    <input
                      id="regenerate_questions"
                      v-model="form.regenerate_questions"
                      type="checkbox"
                      class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded"
                    />
                  </div>
                  <div class="ml-3">
                    <label for="regenerate_questions" class="text-sm font-medium text-gray-700">
                      Regenerate Questions
                    </label>
                    <p class="text-sm text-gray-500">
                      Regenerate all questions based on current criteria. Existing attempts will not be affected.
                    </p>
                    <div v-if="form.regenerate_questions" class="mt-2 p-3 bg-emerald-50 border border-emerald-200 rounded-lg">
                      <p class="text-sm text-emerald-700">
                        <strong>Warning:</strong> This will replace all current questions. Students with active attempts will need to start over.
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Current Questions Preview -->
              <div v-if="examPrep.questions && examPrep.questions.length > 0" class="space-y-6">
                <div class="border-b border-gray-200 pb-4">
                  <div class="flex items-center justify-between">
                    <div>
                      <h2 class="text-lg font-semibold text-gray-900">Current Questions</h2>
                      <p class="mt-1 text-sm text-gray-500">
                        {{ examPrep.questions.length }} questions loaded
                      </p>
                    </div>
                    <button
                      type="button"
                      @click="showAllQuestions = !showAllQuestions"
                      class="inline-flex items-center text-sm text-emerald-600 hover:text-emerald-700 font-medium"
                    >
                      {{ showAllQuestions ? 'Hide Questions' : 'Show All Questions' }}
                      <ChevronDownIcon
                        class="ml-1 h-4 w-4 transition-transform"
                        :class="{ 'rotate-180': showAllQuestions }"
                      />
                    </button>
                  </div>
                </div>

                <!-- Question Stats -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                  <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-4">
                    <div class="text-center">
                      <div class="text-2xl font-bold text-emerald-700">{{ easyQuestions }}</div>
                      <div class="text-sm text-emerald-600">Easy</div>
                    </div>
                  </div>
                  <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                    <div class="text-center">
                      <div class="text-2xl font-bold text-amber-700">{{ mediumQuestions }}</div>
                      <div class="text-sm text-amber-600">Medium</div>
                    </div>
                  </div>
                  <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="text-center">
                      <div class="text-2xl font-bold text-red-700">{{ hardQuestions }}</div>
                      <div class="text-sm text-red-600">Hard</div>
                    </div>
                  </div>
                  <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="text-center">
                      <div class="text-2xl font-bold text-blue-700">{{ totalQuestions }}</div>
                      <div class="text-sm text-blue-600">Total</div>
                    </div>
                  </div>
                </div>

                <!-- Questions List -->
                <Transition
                  enter-active-class="duration-300 ease-out"
                  enter-from-class="opacity-0"
                  enter-to-class="opacity-100"
                  leave-active-class="duration-200 ease-in"
                  leave-from-class="opacity-100"
                  leave-to-class="opacity-0"
                >
                  <div v-if="showAllQuestions" class="space-y-3">
                    <div
                      v-for="(question, index) in examPrep.questions"
                      :key="question.id"
                      class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm"
                    >
                      <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center space-x-3">
                          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                            :class="getDifficultyClass(question.difficulty)">
                            {{ question.difficulty }}
                          </span>
                          <span class="text-sm text-gray-500">
                            {{ question.question_type?.replace('_', ' ') }}
                          </span>
                        </div>
                        <div class="text-sm text-gray-500">
                          #{{ index + 1 }}
                        </div>
                      </div>

                      <p class="text-gray-700 mb-3 line-clamp-2">{{ question.question_text }}</p>

                      <div class="flex items-center justify-between text-sm text-gray-500">
                        <div class="flex items-center space-x-4">
                          <span v-if="question.quiz_id" class="flex items-center">
                            <QuestionMarkCircleIcon class="h-3 w-3 mr-1" />
                            From Quiz
                          </span>
                          <span v-if="question.times_used > 0" class="flex items-center">
                            <AcademicCapIcon class="h-3 w-3 mr-1" />
                            {{ question.times_used }} uses
                          </span>
                        </div>
                        <div v-if="question.times_used > 0" class="text-right">
                          <div class="text-xs">
                            Success: {{ Math.round((question.times_correct / question.times_used) * 100) || 0 }}%
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </Transition>

                <!-- Manage Questions -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                  <div class="flex items-center justify-between">
                    <div>
                      <h4 class="text-sm font-medium text-gray-900">Question Management</h4>
                      <p class="text-sm text-gray-500">Manage individual questions</p>
                    </div>
                    <div class="flex space-x-2">
                      <button
                        type="button"
                        @click="regenerateQuestions"
                        :disabled="regenerating"
                        class="inline-flex items-center px-4 py-2 border border-emerald-300 text-emerald-700 font-medium rounded-lg hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 disabled:opacity-50"
                      >
                        <ArrowPathIcon class="h-4 w-4 mr-2" :class="{ 'animate-spin': regenerating }" />
                        Regenerate All
                      </button>
                      <Link
                        :href="route('admin.exam-preps.show', examPrep.id)"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
                      >
                        Manage Questions
                      </Link>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Form Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-xl flex justify-between items-center">
              <div class="flex space-x-3">
                <button
                  type="button"
                  @click="archiveExamPrep"
                  v-if="examPrep.status !== 'archived'"
                  class="inline-flex items-center px-4 py-2.5 border border-red-300 text-red-700 font-medium rounded-lg hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200"
                >
                  <ArchiveBoxIcon class="h-4 w-4 mr-2" />
                  Archive
                </button>
                <button
                  type="button"
                  @click="publishExamPrep"
                  v-if="examPrep.status === 'draft'"
                  class="inline-flex items-center px-4 py-2.5 border border-emerald-300 text-emerald-700 font-medium rounded-lg hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-200"
                >
                  <CheckCircleIcon class="h-4 w-4 mr-2" />
                  Publish
                </button>
              </div>

              <div class="flex space-x-3">
                <Link
                  :href="route('admin.exam-preps.show', examPrep.id)"
                  class="inline-flex items-center px-4 py-2.5 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-200"
                >
                  Cancel
                </Link>
                <button
                  type="submit"
                  :disabled="form.processing"
                  class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 border border-transparent rounded-lg font-semibold text-white shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50"
                >
                  <template v-if="form.processing">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Saving...
                  </template>
                  <template v-else>
                    <CheckCircleIcon class="h-4 w-4 mr-2" />
                    Save Changes
                  </template>
                </button>
              </div>
            </div>
          </form>
        </div>

        <!-- Danger Zone -->
        <div class="mt-8 bg-red-50 border border-red-200 rounded-xl overflow-hidden">
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
                class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors"
              >
                <TrashIcon class="h-4 w-4 mr-2" />
                Delete Exam Prep
              </button>
            </div>
          </div>
        </div>

        <!-- Stats Summary -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <h4 class="text-sm font-medium text-gray-900 mb-3">Attempt Statistics</h4>
            <div class="space-y-3">
              <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600">Total Attempts</span>
                <span class="text-sm font-semibold text-gray-900">{{ examPrep.attempts?.length || 0 }}</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600">Average Score</span>
                <span class="text-sm font-semibold text-gray-900">{{ parseFloat(examPrep.average_score || 0).toFixed(1) }}%</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600">Pass Rate</span>
                <span class="text-sm font-semibold text-gray-900">
                  {{ calculatePassRate() }}%
                </span>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <h4 class="text-sm font-medium text-gray-900 mb-3">Enrollment</h4>
            <div class="space-y-3">
              <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600">Total Enrolled</span>
                <span class="text-sm font-semibold text-gray-900">{{ examPrep.enrolled_count }}</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600">Active Students</span>
                <span class="text-sm font-semibold text-gray-900">{{ getActiveStudentsCount() }}</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600">Completion Rate</span>
                <span class="text-sm font-semibold text-gray-900">
                  {{ calculateCompletionRate() }}%
                </span>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <h4 class="text-sm font-medium text-gray-900 mb-3">Performance</h4>
            <div class="space-y-3">
              <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600">Best Score</span>
                <span class="text-sm font-semibold text-gray-900">{{ getBestScore() }}%</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600">Worst Score</span>
                <span class="text-sm font-semibold text-gray-900">{{ getWorstScore() }}%</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600">Median Score</span>
                <span class="text-sm font-semibold text-gray-900">{{ getMedianScore() }}%</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <Transition
      enter-active-class="duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
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
              This action cannot be undone and will permanently delete:
            </p>
            <ul class="text-left text-sm text-gray-600 mb-6 space-y-2">
              <li class="flex items-center">
                <XCircleIcon class="h-4 w-4 text-red-500 mr-2" />
                All {{ examPrep.attempts?.length || 0 }} student attempts
              </li>
              <li class="flex items-center">
                <XCircleIcon class="h-4 w-4 text-red-500 mr-2" />
                All {{ examPrep.questions?.length || 0 }} questions
              </li>
              <li class="flex items-center">
                <XCircleIcon class="h-4 w-4 text-red-500 mr-2" />
                All enrollment records
              </li>
              <li class="flex items-center">
                <XCircleIcon class="h-4 w-4 text-red-500 mr-2" />
                All performance statistics
              </li>
            </ul>
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
                <template v-if="deleting">
                  <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Deleting...
                </template>
                <template v-else>
                  Yes, Delete Permanently
                </template>
              </button>
            </div>
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
import {
  ArrowLeftIcon,
  EyeIcon,
  ChevronDownIcon,
  ArrowPathIcon,
  ArchiveBoxIcon,
  CheckCircleIcon,
  TrashIcon,
  ExclamationTriangleIcon,
  XCircleIcon,
  QuestionMarkCircleIcon,
  AcademicCapIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  examPrep: Object,
  examBoards: Array,
  subjects: Array,
  courses: Array,
  difficultyLevels: Object,
  questionTypes: Object,
})

// Form state
const form = reactive({
  name: props.examPrep.name || '',
  description: props.examPrep.description || '',
  exam_board_id: props.examPrep.exam_board_id || null,
  subject_id: props.examPrep.subject_id || null,
  course_id: props.examPrep.course_id || null,
  total_questions: props.examPrep.total_questions || 50,
  time_limit_minutes: props.examPrep.time_limit_minutes || 60,
  passing_score: props.examPrep.passing_score || 70,
  max_attempts: props.examPrep.max_attempts || 3,
  randomize_questions: props.examPrep.randomize_questions || true,
  allow_pause: props.examPrep.allow_pause || false,
  show_results_immediately: props.examPrep.show_results_immediately || true,
  is_public: props.examPrep.is_public || false,
  status: props.examPrep.status || 'draft',
  question_criteria: props.examPrep.question_criteria || {
    difficulty: '',
    question_type: ''
  },
  question_distribution: props.examPrep.question_distribution || {
    easy: null,
    medium: null,
    hard: null
  },
  regenerate_questions: false,
  errors: {},
  processing: false,
})

// UI state
const showAllQuestions = ref(false)
const showDeleteModal = ref(false)
const regenerating = ref(false)
const deleting = ref(false)

// Status labels
const statusLabels = {
  draft: 'Draft',
  active: 'Active',
  archived: 'Archived'
}

// Computed properties
const distributedTotal = computed(() => {
  const values = Object.values(form.question_distribution)
    .filter(v => v !== null && v !== '')
    .map(v => parseInt(v) || 0)
  return values.reduce((sum, val) => sum + val, 0)
})

const distributionWarning = computed(() => {
  if (distributedTotal.value === 0) return ''

  if (distributedTotal.value < form.total_questions) {
    const remaining = form.total_questions - distributedTotal.value
    return ` (${remaining} questions will be filled randomly)`
  } else if (distributedTotal.value > form.total_questions) {
    return ' - Warning: Distribution exceeds total questions'
  }

  return ' - Perfect distribution'
})

const easyQuestions = computed(() => {
  return props.examPrep.questions?.filter(q => q.difficulty === 'easy').length || 0
})

const mediumQuestions = computed(() => {
  return props.examPrep.questions?.filter(q => q.difficulty === 'medium').length || 0
})

const hardQuestions = computed(() => {
  return props.examPrep.questions?.filter(q => q.difficulty === 'hard').length || 0
})

const totalQuestions = computed(() => {
  return props.examPrep.questions?.length || 0
})

// Helper functions
const getDifficultyClass = (difficulty) => {
  const classes = {
    easy: 'bg-green-100 text-green-800',
    medium: 'bg-amber-100 text-amber-800',
    hard: 'bg-red-100 text-red-800',
  }
  return classes[difficulty] || 'bg-gray-100 text-gray-800'
}

const calculatePassRate = () => {
  if (!props.examPrep.attempts || props.examPrep.attempts.length === 0) return 0
  const passed = props.examPrep.attempts.filter(a => a.is_passed).length
  return ((passed / props.examPrep.attempts.length) * 100).toFixed(1)
}

const getActiveStudentsCount = () => {
  // This would require additional logic - for now return enrolled count
  return props.examPrep.enrolled_count || 0
}

const calculateCompletionRate = () => {
  if (props.examPrep.enrolled_count === 0) return 0
  const attempts = props.examPrep.attempts?.length || 0
  return ((attempts / props.examPrep.enrolled_count) * 100).toFixed(1)
}

const getBestScore = () => {
  if (!props.examPrep.attempts || props.examPrep.attempts.length === 0) return 0
  return Math.max(...props.examPrep.attempts.map(a => a.percentage)).toFixed(1)
}

const getWorstScore = () => {
  if (!props.examPrep.attempts || props.examPrep.attempts.length === 0) return 0
  return Math.min(...props.examPrep.attempts.map(a => a.percentage)).toFixed(1)
}

const getMedianScore = () => {
  if (!props.examPrep.attempts || props.examPrep.attempts.length === 0) return 0
  const scores = props.examPrep.attempts.map(a => a.percentage).sort((a, b) => a - b)
  const middle = Math.floor(scores.length / 2)
  if (scores.length % 2 === 0) {
    return ((scores[middle - 1] + scores[middle]) / 2).toFixed(1)
  }
  return scores[middle].toFixed(1)
}

// Form submission
const submit = async () => {
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
        // Refresh page to show updated data
        router.reload({ only: ['examPrep'] })
      },
    })
  } catch (error) {
    form.processing = false
    console.error('Failed to update exam prep:', error)
  }
}

// Regenerate questions
const regenerateQuestions = async () => {
  if (!confirm('Are you sure you want to regenerate all questions? This will replace all current questions.')) {
    return
  }

  regenerating.value = true

  try {
    const response = await fetch(route('admin.exam-preps.generate-questions', props.examPrep.id), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Accept': 'application/json'
      }
    })

    const data = await response.json()

    if (data.success) {
      alert(`Successfully generated ${data.question_count} questions!`)
      router.reload({ only: ['examPrep'] })
    } else {
      throw new Error(data.message || 'Failed to regenerate questions')
    }
  } catch (error) {
    console.error('Failed to regenerate questions:', error)
    alert('Failed to regenerate questions: ' + error.message)
  } finally {
    regenerating.value = false
  }
}

// Publish exam prep
const publishExamPrep = async () => {
  if (confirm(`Publish "${props.examPrep.name}"? This will make it available to students.`)) {
    try {
      const response = await fetch(route('admin.exam-preps.publish', props.examPrep.id), {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json'
        }
      })

      const data = await response.json()

      if (data.success) {
        alert('Exam prep published successfully!')
        router.reload()
      } else {
        throw new Error(data.message || 'Failed to publish exam prep')
      }
    } catch (error) {
      console.error('Failed to publish exam prep:', error)
      alert('Failed to publish exam prep: ' + error.message)
    }
  }
}

// Archive exam prep
const archiveExamPrep = async () => {
  if (confirm(`Archive "${props.examPrep.name}"? This will hide it from students.`)) {
    try {
      const response = await fetch(route('admin.exam-preps.archive', props.examPrep.id), {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json'
        }
      })

      const data = await response.json()

      if (data.success) {
        alert('Exam prep archived successfully!')
        router.reload()
      } else {
        throw new Error(data.message || 'Failed to archive exam prep')
      }
    } catch (error) {
      console.error('Failed to archive exam prep:', error)
      alert('Failed to archive exam prep: ' + error.message)
    }
  }
}

// Delete exam prep
const confirmDelete = () => {
  showDeleteModal.value = true
}

const deleteExamPrep = async () => {
  deleting.value = true

  try {
    await router.delete(route('admin.exam-preps.destroy', props.examPrep.id), {
      preserveScroll: false,
      onFinish: () => {
        deleting.value = false
        showDeleteModal.value = false
      },
    })
  } catch (error) {
    deleting.value = false
    console.error('Failed to delete exam prep:', error)
  }
}

// Initialize form with exam prep data
onMounted(() => {
  // Ensure question_criteria is properly formatted
  if (typeof props.examPrep.question_criteria === 'string') {
    try {
      form.question_criteria = JSON.parse(props.examPrep.question_criteria)
    } catch {
      form.question_criteria = { difficulty: '', question_type: '' }
    }
  }

  // Ensure question_distribution is properly formatted
  if (typeof props.examPrep.question_distribution === 'string') {
    try {
      form.question_distribution = JSON.parse(props.examPrep.question_distribution)
    } catch {
      form.question_distribution = { easy: null, medium: null, hard: null }
    }
  }
})
</script>

<style scoped>
/* Custom styles for better UX */
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 150ms;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.bg-emerald-25 {
  background-color: rgba(16, 185, 129, 0.025);
}
</style>
