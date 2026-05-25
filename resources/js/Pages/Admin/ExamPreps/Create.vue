<!-- resources/js/Pages/Admin/ExamPreps/Create.vue -->
<template>
  <AdminLayout>
    <Head title="Create Exam Prep - AI Powered" />

    <div class="py-6">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex items-center justify-between">
            <div>
              <div class="flex items-center space-x-2">
                <h1 class="text-2xl font-bold text-gray-900">Create AI-Powered Exam Preparation</h1>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gradient-to-r from-purple-100 to-blue-100 text-purple-800 border border-purple-200">
                  <SparklesIcon class="h-3 w-3 mr-1" />
                  AI Generated
                </span>
              </div>
              <p class="mt-1 text-sm text-gray-600">
                Generate custom exam questions using AI based on your specifications
              </p>
            </div>
            <Link
              :href="route('admin.exam-preps.index')"
              class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all"
            >
              <ArrowLeftIcon class="h-4 w-4 mr-2" />
              Back to Exam Preps
            </Link>
          </div>
        </div>

        <!-- Form -->
        <div class="bg-white shadow-xl rounded-xl border border-gray-200 overflow-hidden">
          <form @submit.prevent="submit">
            <!-- Progress Steps -->
            <div class="px-6 pt-6 pb-4 bg-gradient-to-r from-emerald-50 to-teal-50 border-b border-emerald-100">
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                  <div v-for="(step, index) in steps" :key="index" class="flex items-center">
                    <div
                      :class="[
                        'flex items-center justify-center w-8 h-8 rounded-full text-sm font-semibold transition-all',
                        currentStep > index + 1 ? 'bg-emerald-600 text-white' :
                        currentStep === index + 1 ? 'bg-emerald-600 text-white ring-4 ring-emerald-100' :
                        'bg-gray-200 text-gray-600'
                      ]"
                    >
                      <CheckIcon v-if="currentStep > index + 1" class="h-4 w-4" />
                      <span v-else>{{ index + 1 }}</span>
                    </div>
                    <span v-if="index < steps.length - 1" class="mx-4 text-gray-400">
                      <ChevronRightIcon class="h-4 w-4" />
                    </span>
                  </div>
                </div>
                <span class="text-sm text-emerald-700 font-medium">
                  Step {{ currentStep }} of {{ steps.length }}: {{ steps[currentStep - 1] }}
                </span>
              </div>
            </div>

            <div class="p-6 space-y-8">
              <!-- Step 1: Basic Information -->
              <div v-show="currentStep === 1" class="space-y-6 animate-fadeIn">
                <div>
                  <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                    <InformationCircleIcon class="h-5 w-5 mr-2 text-emerald-600" />
                    Basic Information
                  </h2>
                  <p class="mt-1 text-sm text-gray-500">Define the core details of your exam</p>
                </div>

                <!-- Exam Prep Name -->
                <div>
                  <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                    Exam Prep Name <span class="text-red-500">*</span>
                  </label>
                  <input
                    id="name"
                    v-model="form.name"
                    type="text"
                    required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                    placeholder="e.g., WAEC Mathematics 2024 Practice Exam"
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
                    placeholder="Describe what this exam covers, who it's for, and what students can expect..."
                  />
                </div>

                <!-- Exam Board Selection -->
                <div>
                  <label for="exam_board_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Exam Board <span class="text-red-500">*</span>
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

                <!-- Subject & Course -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label for="subject_id" class="block text-sm font-medium text-gray-700 mb-1">
                      Subject
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
                    <p class="mt-1 text-xs text-gray-500">
                      AI will generate subject-specific questions
                    </p>
                  </div>

                  <div>
                    <label for="course_id" class="block text-sm font-medium text-gray-700 mb-1">
                      Related Course
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
                        {{ course.title }} ({{ course.level }})
                      </option>
                    </select>
                    <p class="mt-1 text-xs text-gray-500">
                      AI will align questions with course learning objectives
                    </p>
                  </div>
                </div>

                <!-- AI Model Selection -->
                <div>
                  <label for="ai_model" class="block text-sm font-medium text-gray-700 mb-1">
                    AI Model
                  </label>
                  <select
                    id="ai_model"
                    v-model="form.ai_model"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                  >
                    <option value="gpt-4">GPT-4 (Recommended - Highest Quality)</option>
                    <option value="gpt-3.5-turbo">GPT-3.5 Turbo (Faster)</option>
                    <option value="claude-2">Claude 2 (Excellent for complex topics)</option>
                    <option value="llama2">Llama 2 (Open source)</option>
                  </select>
                  <p class="mt-1 text-xs text-gray-500">
                    Choose the AI model for question generation
                  </p>
                </div>
              </div>

              <!-- Step 2: Exam Configuration -->
              <div v-show="currentStep === 2" class="space-y-6 animate-fadeIn">
                <div>
                  <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                    <Cog6ToothIcon class="h-5 w-5 mr-2 text-emerald-600" />
                    Exam Configuration
                  </h2>
                  <p class="mt-1 text-sm text-gray-500">Configure the exam settings and requirements</p>
                </div>

                <!-- Total Questions -->
                <div>
                  <label for="total_questions" class="block text-sm font-medium text-gray-700 mb-1">
                    Total Questions <span class="text-red-500">*</span>
                  </label>
                  <div class="relative">
                    <input
                      id="total_questions"
                      v-model="form.total_questions"
                      type="number"
                      min="5"
                      max="100"
                      required
                      class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                      placeholder="e.g., 50"
                    />
                    <div class="mt-2 flex items-center space-x-2">
                      <span class="text-xs text-gray-500">Quick select:</span>
                      <button
                        type="button"
                        @click="form.total_questions = 20"
                        class="px-2 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded-md transition-colors"
                      >
                        20
                      </button>
                      <button
                        type="button"
                        @click="form.total_questions = 50"
                        class="px-2 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded-md transition-colors"
                      >
                        50
                      </button>
                      <button
                        type="button"
                        @click="form.total_questions = 75"
                        class="px-2 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded-md transition-colors"
                      >
                        75
                      </button>
                      <button
                        type="button"
                        @click="form.total_questions = 100"
                        class="px-2 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded-md transition-colors"
                      >
                        100
                      </button>
                    </div>
                  </div>
                  <p v-if="form.errors.total_questions" class="mt-1 text-sm text-red-600">
                    {{ form.errors.total_questions }}
                  </p>
                  <p class="mt-1 text-sm text-gray-500">
                    Number of questions AI will generate (5-100)
                  </p>
                </div>

                <!-- Time Limit & Passing Score -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label for="time_limit_minutes" class="block text-sm font-medium text-gray-700 mb-1">
                      Time Limit (Minutes) <span class="text-red-500">*</span>
                    </label>
                    <input
                      id="time_limit_minutes"
                      v-model="form.time_limit_minutes"
                      type="number"
                      min="10"
                      max="300"
                      required
                      class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                    />
                    <p class="mt-1 text-xs text-gray-500">
                      Recommended: {{ Math.ceil(form.total_questions * 1.5) }} minutes
                    </p>
                  </div>

                  <div>
                    <label for="passing_score" class="block text-sm font-medium text-gray-700 mb-1">
                      Passing Score (%) <span class="text-red-500">*</span>
                    </label>
                    <div class="flex items-center space-x-2">
                      <input
                        id="passing_score"
                        v-model="form.passing_score"
                        type="number"
                        min="1"
                        max="100"
                        required
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                      />
                      <span class="text-gray-500">%</span>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">
                      Default: 70%
                    </p>
                  </div>
                </div>

                <!-- Max Attempts -->
                <div>
                  <label for="max_attempts" class="block text-sm font-medium text-gray-700 mb-1">
                    Maximum Attempts <span class="text-red-500">*</span>
                  </label>
                  <input
                    id="max_attempts"
                    v-model="form.max_attempts"
                    type="number"
                    min="0"
                    required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                  />
                  <p class="mt-1 text-xs text-gray-500">
                    0 = unlimited attempts
                  </p>
                </div>

                <!-- Exam Settings -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-3">
                    Exam Features
                  </label>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex items-start p-3 border rounded-lg hover:bg-gray-50 transition-colors">
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
                        <p class="text-xs text-gray-500">
                          Shuffle questions order for each attempt
                        </p>
                      </div>
                    </div>

                    <div class="flex items-start p-3 border rounded-lg hover:bg-gray-50 transition-colors">
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
                          Allow Pause & Resume
                        </label>
                        <p class="text-xs text-gray-500">
                          Students can pause and continue later
                        </p>
                      </div>
                    </div>

                    <div class="flex items-start p-3 border rounded-lg hover:bg-gray-50 transition-colors">
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
                          Instant Results
                        </label>
                        <p class="text-xs text-gray-500">
                          Show scores and answers immediately
                        </p>
                      </div>
                    </div>

                    <div class="flex items-start p-3 border rounded-lg hover:bg-gray-50 transition-colors">
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
                        <p class="text-xs text-gray-500">
                          Make available to all students
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Step 3: AI Question Criteria -->
              <div v-show="currentStep === 3" class="space-y-6 animate-fadeIn">
                <div>
                  <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                    <AdjustmentsHorizontalIcon class="h-5 w-5 mr-2 text-emerald-600" />
                    AI Question Criteria
                  </h2>
                  <p class="mt-1 text-sm text-gray-500">
                    Customize how AI generates questions for your exam
                  </p>
                </div>

                <!-- Difficulty Distribution -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-3">
                    Question Difficulty Distribution
                  </label>
                  <p class="text-sm text-gray-500 mb-4">
                    Set the number of questions for each difficulty level. AI will generate questions accordingly.
                  </p>

                  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-2 p-4 bg-green-50 rounded-lg border border-green-200">
                      <label class="block text-sm font-medium text-green-700">
                        Easy Questions
                      </label>
                      <div class="flex items-center space-x-2">
                        <input
                          v-model="form.question_distribution.easy"
                          type="number"
                          min="0"
                          :max="form.total_questions"
                          class="block w-full rounded-lg border-green-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2 px-3"
                          placeholder="0"
                        />
                        <span class="text-sm text-green-600">questions</span>
                      </div>
                      <p class="text-xs text-green-600">
                        Basic recall and fundamental concepts
                      </p>
                    </div>

                    <div class="space-y-2 p-4 bg-amber-50 rounded-lg border border-amber-200">
                      <label class="block text-sm font-medium text-amber-700">
                        Medium Questions
                      </label>
                      <div class="flex items-center space-x-2">
                        <input
                          v-model="form.question_distribution.medium"
                          type="number"
                          min="0"
                          :max="form.total_questions"
                          class="block w-full rounded-lg border-amber-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2 px-3"
                          placeholder="0"
                        />
                        <span class="text-sm text-amber-600">questions</span>
                      </div>
                      <p class="text-xs text-amber-600">
                        Application and critical thinking
                      </p>
                    </div>

                    <div class="space-y-2 p-4 bg-red-50 rounded-lg border border-red-200">
                      <label class="block text-sm font-medium text-red-700">
                        Hard Questions
                      </label>
                      <div class="flex items-center space-x-2">
                        <input
                          v-model="form.question_distribution.hard"
                          type="number"
                          min="0"
                          :max="form.total_questions"
                          class="block w-full rounded-lg border-red-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2 px-3"
                          placeholder="0"
                        />
                        <span class="text-sm text-red-600">questions</span>
                      </div>
                      <p class="text-xs text-red-600">
                        Complex problem-solving and synthesis
                      </p>
                    </div>
                  </div>

                  <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-start">
                      <InformationCircleIcon class="h-5 w-5 text-blue-500 mr-2 flex-shrink-0 mt-0.5" />
                      <div>
                        <p class="text-sm text-blue-700">
                          <span class="font-semibold">Distribution Summary:</span>
                          {{ distributedTotal }} of {{ form.total_questions }} questions assigned
                        </p>
                        <p v-if="distributedTotal < form.total_questions" class="text-sm text-blue-600 mt-1">
                          ⚡ {{ form.total_questions - distributedTotal }} unassigned questions will be randomly distributed by AI
                        </p>
                        <p v-else-if="distributedTotal > form.total_questions" class="text-sm text-red-600 mt-1">
                          ⚠️ Total exceeds question limit. Please adjust.
                        </p>
                        <p v-else class="text-sm text-green-600 mt-1">
                          ✓ Perfect distribution! All questions assigned.
                        </p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Topic Focus (Optional) -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-3">
                    Topic Focus (Optional)
                  </label>
                  <p class="text-sm text-gray-500 mb-4">
                    Specify specific topics or areas to focus on. Leave empty for general coverage.
                  </p>
                  <div class="space-y-3">
                    <div
                      v-for="(topic, index) in form.question_criteria.topics"
                      :key="index"
                      class="flex items-center space-x-2"
                    >
                      <input
                        v-model="form.question_criteria.topics[index]"
                        type="text"
                        class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2 px-3"
                        :placeholder="`Topic ${index + 1}`"
                      />
                      <button
                        type="button"
                        @click="removeTopic(index)"
                        class="p-2 text-gray-400 hover:text-red-500 transition-colors"
                      >
                        <XMarkIcon class="h-5 w-5" />
                      </button>
                    </div>
                    <button
                      type="button"
                      @click="addTopic"
                      class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
                    >
                      <PlusIcon class="h-4 w-4 mr-1" />
                      Add Topic
                    </button>
                  </div>
                </div>

                <!-- Additional AI Instructions -->
                <div>
                  <label for="ai_instructions" class="block text-sm font-medium text-gray-700 mb-1">
                    Additional AI Instructions (Optional)
                  </label>
                  <textarea
                    id="ai_instructions"
                    v-model="form.question_criteria.custom_instructions"
                    rows="3"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                    placeholder="e.g., Focus on real-world applications, include diagrams in explanations, emphasize recent developments..."
                  />
                  <p class="mt-1 text-xs text-gray-500">
                    Provide specific guidance for AI question generation
                  </p>
                </div>
              </div>

              <!-- Step 4: Review & Generate -->
              <div v-show="currentStep === 4" class="space-y-6 animate-fadeIn">
                <div>
                  <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                    <CheckCircleIcon class="h-5 w-5 mr-2 text-emerald-600" />
                    Review & Generate
                  </h2>
                  <p class="mt-1 text-sm text-gray-500">
                    Review your settings before generating questions with AI
                  </p>
                </div>

                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div class="bg-gray-50 rounded-lg p-5 border border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                      <AcademicCapIcon class="h-4 w-4 mr-1 text-emerald-600" />
                      Exam Details
                    </h3>
                    <dl class="space-y-2">
                      <div class="flex justify-between">
                        <dt class="text-xs text-gray-500">Name:</dt>
                        <dd class="text-xs font-medium text-gray-900">{{ form.name || 'Not set' }}</dd>
                      </div>
                      <div class="flex justify-between">
                        <dt class="text-xs text-gray-500">Exam Board:</dt>
                        <dd class="text-xs font-medium text-gray-900">
                          {{ getExamBoardName(form.exam_board_id) }}
                        </dd>
                      </div>
                      <div class="flex justify-between">
                        <dt class="text-xs text-gray-500">Subject:</dt>
                        <dd class="text-xs font-medium text-gray-900">
                          {{ getSubjectName(form.subject_id) || 'Any' }}
                        </dd>
                      </div>
                      <div class="flex justify-between">
                        <dt class="text-xs text-gray-500">Course:</dt>
                        <dd class="text-xs font-medium text-gray-900">
                          {{ getCourseTitle(form.course_id) || 'None' }}
                        </dd>
                      </div>
                      <div class="flex justify-between">
                        <dt class="text-xs text-gray-500">AI Model:</dt>
                        <dd class="text-xs font-medium text-gray-900">
                          {{ getAiModelName(form.ai_model) }}
                        </dd>
                      </div>
                    </dl>
                  </div>

                  <div class="bg-gray-50 rounded-lg p-5 border border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                      <Cog6ToothIcon class="h-4 w-4 mr-1 text-emerald-600" />
                      Exam Settings
                    </h3>
                    <dl class="space-y-2">
                      <div class="flex justify-between">
                        <dt class="text-xs text-gray-500">Questions:</dt>
                        <dd class="text-xs font-medium text-gray-900">{{ form.total_questions }}</dd>
                      </div>
                      <div class="flex justify-between">
                        <dt class="text-xs text-gray-500">Time Limit:</dt>
                        <dd class="text-xs font-medium text-gray-900">{{ form.time_limit_minutes }} minutes</dd>
                      </div>
                      <div class="flex justify-between">
                        <dt class="text-xs text-gray-500">Passing Score:</dt>
                        <dd class="text-xs font-medium text-gray-900">{{ form.passing_score }}%</dd>
                      </div>
                      <div class="flex justify-between">
                        <dt class="text-xs text-gray-500">Max Attempts:</dt>
                        <dd class="text-xs font-medium text-gray-900">
                          {{ form.max_attempts === 0 ? 'Unlimited' : form.max_attempts }}
                        </dd>
                      </div>
                      <div class="flex justify-between">
                        <dt class="text-xs text-gray-500">Visibility:</dt>
                        <dd class="text-xs font-medium text-gray-900">
                          {{ form.is_public ? 'Public' : 'Private' }}
                        </dd>
                      </div>
                    </dl>
                  </div>

                  <div class="bg-gray-50 rounded-lg p-5 border border-gray-200 md:col-span-2">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                      <AdjustmentsHorizontalIcon class="h-4 w-4 mr-1 text-emerald-600" />
                      Question Distribution
                    </h3>
                    <div class="grid grid-cols-3 gap-4">
                      <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">
                          {{ form.question_distribution.easy || 0 }}
                        </div>
                        <div class="text-xs text-gray-500">Easy</div>
                      </div>
                      <div class="text-center">
                        <div class="text-2xl font-bold text-amber-600">
                          {{ form.question_distribution.medium || 0 }}
                        </div>
                        <div class="text-xs text-gray-500">Medium</div>
                      </div>
                      <div class="text-center">
                        <div class="text-2xl font-bold text-red-600">
                          {{ form.question_distribution.hard || 0 }}
                        </div>
                        <div class="text-xs text-gray-500">Hard</div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Generation Options -->
                <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-5">
                  <div class="flex items-start">
                    <div class="flex items-center h-5">
                      <input
                        id="generate_now"
                        v-model="form.generate_now"
                        type="checkbox"
                        class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded"
                      />
                    </div>
                    <div class="ml-3">
                      <label for="generate_now" class="text-sm font-medium text-emerald-800">
                        Generate questions immediately
                      </label>
                      <p class="text-xs text-emerald-700 mt-1">
                        AI will start generating {{ form.total_questions }} questions right away.
                        This may take 1-2 minutes. You can continue using the system while generation runs in the background.
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Navigation Buttons -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-xl flex justify-between items-center">
              <button
                v-if="currentStep > 1"
                type="button"
                @click="currentStep--"
                class="inline-flex items-center px-4 py-2.5 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all"
              >
                <ArrowLeftIcon class="h-4 w-4 mr-2" />
                Previous
              </button>
              <div v-else></div>

              <div class="flex space-x-3">
                <Link
                  :href="route('admin.exam-preps.index')"
                  class="inline-flex items-center px-4 py-2.5 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all"
                >
                  Cancel
                </Link>

                <button
                  v-if="currentStep < steps.length"
                  type="button"
                  @click="nextStep"
                  :disabled="!canProceed"
                  class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 border border-transparent rounded-lg font-semibold text-white shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  Next Step
                  <ChevronRightIcon class="h-4 w-4 ml-2" />
                </button>

                <button
                  v-else
                  type="submit"
                  :disabled="form.processing || !isValid"
                  class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 border border-transparent rounded-lg font-semibold text-white shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <template v-if="form.processing">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Creating...
                  </template>
                  <template v-else>
                    <SparklesIcon class="h-4 w-4 mr-2" />
                    Generate with AI
                  </template>
                </button>
              </div>
            </div>
          </form>
        </div>

        <!-- AI Info Card -->
        <div class="mt-6 bg-gradient-to-r from-purple-50 to-blue-50 border border-purple-200 rounded-xl p-5">
          <div class="flex items-start">
            <div class="flex-shrink-0">
              <div class="p-2 bg-purple-100 rounded-lg">
                <SparklesIcon class="h-5 w-5 text-purple-600" />
              </div>
            </div>
            <div class="ml-4">
              <h3 class="text-sm font-semibold text-purple-900">
                AI-Powered Question Generation
              </h3>
              <div class="mt-2 text-sm text-purple-800 space-y-1">
                <p>✨ Questions are generated fresh for each exam prep - no recycling</p>
                <p>🎯 AI adapts difficulty and content based on your criteria</p>
                <p>⚡ Generation runs in background - you can continue working</p>
                <p>📊 Each question includes detailed explanations</p>
                <p>🔄 You can regenerate questions anytime</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import {
  ArrowLeftIcon,
  SparklesIcon,
  InformationCircleIcon,
  CheckIcon,
  ChevronRightIcon,
  Cog6ToothIcon,
  AdjustmentsHorizontalIcon,
  CheckCircleIcon,
  AcademicCapIcon,
  PlusIcon,
  XMarkIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  examBoards: Array,
  subjects: Array,
  courses: Array,
  difficultyLevels: Object,
  aiModels: Object,
})

// Form state
const form = reactive({
  name: '',
  description: '',
  exam_board_id: null,
  subject_id: null,
  course_id: null,
  total_questions: 50,
  time_limit_minutes: 60,
  passing_score: 70,
  max_attempts: 3,
  randomize_questions: true,
  allow_pause: false,
  show_results_immediately: true,
  is_public: false,
  status: 'draft',
  ai_model: 'gpt-4',
  generate_now: true,
  question_criteria: {
    difficulty: '',
    topics: [''],
    custom_instructions: '',
  },
  question_distribution: {
    easy: null,
    medium: null,
    hard: null,
  },
  errors: {},
  processing: false,
})

// Step management
const currentStep = ref(1)
const steps = ['Basic Information', 'Exam Configuration', 'AI Question Criteria', 'Review & Generate']

// Computed properties
const distributedTotal = computed(() => {
  const values = Object.values(form.question_distribution)
    .filter(v => v !== null && v !== '')
    .map(v => parseInt(v) || 0)
  return values.reduce((sum, val) => sum + val, 0)
})

const canProceed = computed(() => {
  switch (currentStep.value) {
    case 1:
      return form.name && form.exam_board_id
    case 2:
      return form.total_questions >= 5 &&
             form.total_questions <= 100 &&
             form.time_limit_minutes >= 10 &&
             form.time_limit_minutes <= 300 &&
             form.passing_score >= 1 &&
             form.passing_score <= 100
    case 3:
      return distributedTotal.value <= form.total_questions
    default:
      return true
  }
})

const isValid = computed(() => {
  return form.name &&
         form.exam_board_id &&
         form.total_questions >= 5 &&
         form.total_questions <= 100 &&
         distributedTotal.value <= form.total_questions
})

// Helper methods
const getExamBoardName = (id) => {
  if (!id) return 'Not selected'
  const board = props.examBoards.find(b => b.id === id)
  return board?.name || 'Unknown'
}

const getSubjectName = (id) => {
  if (!id) return null
  const subject = props.subjects.find(s => s.id === id)
  return subject?.name
}

const getCourseTitle = (id) => {
  if (!id) return null
  const course = props.courses.find(c => c.id === id)
  return course?.title
}

const getAiModelName = (model) => {
  const models = {
    'gpt-4': 'GPT-4 (Highest Quality)',
    'gpt-3.5-turbo': 'GPT-3.5 Turbo (Faster)',
    'claude-2': 'Claude 2',
    'llama2': 'Llama 2',
  }
  return models[model] || model
}

const addTopic = () => {
  form.question_criteria.topics.push('')
}

const removeTopic = (index) => {
  form.question_criteria.topics.splice(index, 1)
  if (form.question_criteria.topics.length === 0) {
    form.question_criteria.topics.push('')
  }
}

const nextStep = () => {
  if (canProceed.value) {
    currentStep.value++
  }
}

// Submit form
const submit = async () => {
  if (!isValid.value) return

  form.processing = true

  // Clean up empty topics
  form.question_criteria.topics = form.question_criteria.topics.filter(t => t.trim() !== '')

  try {
    await router.post(route('admin.exam-preps.store'), form, {
      preserveScroll: true,
      onError: (errors) => {
        form.processing = false
        form.errors = errors
        // Go back to the step with errors
        if (errors.name || errors.exam_board_id) {
          currentStep.value = 1
        } else if (errors.total_questions || errors.time_limit_minutes || errors.passing_score) {
          currentStep.value = 2
        }
      },
    })
  } catch (error) {
    form.processing = false
    console.error('Exam prep creation failed:', error)
  }
}

// Initialize empty topics
form.question_criteria.topics = ['']
</script>

<style scoped>
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fadeIn {
  animation: fadeIn 0.3s ease-out;
}
</style>
