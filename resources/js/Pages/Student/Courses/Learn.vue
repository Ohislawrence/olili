<template>
  <StudentLayout>
    <Head :title="`Learning: ${course.title}`" />
    <div class="h-screen flex overflow-hidden bg-gradient-to-br from-slate-50 to-emerald-50">
      <!-- Sidebar - Enhanced with Module/Topic Hierarchy -->
      <div class="hidden lg:flex lg:flex-shrink-0 lg:w-80 xl:w-96">
        <div class="flex flex-col w-full border-r border-gray-200 bg-white shadow-sm">
          <!-- Course Header - Improved -->
          <div class="p-6 bg-gradient-to-r from-emerald-600 to-teal-600 text-white">
            <Link
              :href="route('student.courses.show', course.id)"
              class="inline-flex items-center text-sm font-medium text-emerald-100 hover:text-white transition-colors mb-3"
            >
              <ChevronLeftIcon class="h-4 w-4 mr-1" />
              Back to Course Overview
            </Link>
            <h1 class="text-xl font-bold leading-tight line-clamp-2 mb-2">
              {{ course.title }}
            </h1>
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-2">
                <div class="flex items-center text-emerald-100 text-sm">
                  <AcademicCapIcon class="h-4 w-4 mr-1" />
                  {{ courseStats.completed_modules || 0 }}/{{ courseStats.total_modules || 0 }} Modules
                </div>
              </div>
              <span
                :class="[
                  'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold',
                  getStatusClass(course.status)
                ]"
              >
                {{ course.status }}
              </span>
            </div>
          </div>
          <!-- Enhanced Progress Overview -->
          <div class="p-4 border-b border-gray-200 bg-white">
            <div class="space-y-4">
              <!-- Overall Progress -->
              <div>
                <div class="flex justify-between text-sm text-gray-600 mb-2">
                  <span class="font-medium">Overall Progress</span>
                  <span class="font-semibold">{{ Math.round(courseStats.overall_completion_percentage || 0) }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                  <div
                    class="bg-gradient-to-r from-emerald-500 to-teal-500 h-3 rounded-full transition-all duration-500 ease-out"
                    :style="{ width: `${courseStats.overall_completion_percentage || 0}%` }"
                  ></div>
                </div>
              </div>
            </div>
          </div>
          <!-- Course Structure - Hierarchical with Dropdowns -->
          <div class="flex-1 overflow-y-auto">
            <nav class="p-4 space-y-2">
              <div
                v-for="module in course_structure"
                :key="module.id"
                class="border border-gray-200 rounded-xl overflow-hidden"
              >
                <!-- Module Header -->
                <div
                  @click="toggleModule(module.id)"
                  :class="[
                    'flex items-center justify-between p-4 cursor-pointer transition-colors',
                    expandedModules[module.id] ? 'bg-emerald-50 border-b border-emerald-100' : 'bg-white hover:bg-gray-50'
                  ]"
                >
                  <div class="flex items-center flex-1 min-w-0">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white font-bold text-sm mr-3 flex-shrink-0">
                      {{ module.order }}
                    </div>
                    <div class="flex-1 min-w-0">
                      <h3 class="font-semibold text-gray-900 truncate">{{ module.title }}</h3>
                      <p class="text-xs text-gray-500 mt-1">
                        {{ getModuleProgress(module) }}/{{ getModuleTopicCount(module) }} topics • {{ module.estimated_duration_minutes }}min
                      </p>
                    </div>
                  </div>
                  <div class="flex items-center space-x-2 flex-shrink-0">
                    <div class="w-20 bg-gray-200 rounded-full h-2">
                      <div
                        class="bg-emerald-500 h-2 rounded-full transition-all duration-300"
                        :style="{ width: `${calculateModuleCompletion(module)}%` }"
                      ></div>
                    </div>
                    <span class="text-xs text-gray-500 font-medium">
                      {{ calculateModuleCompletion(module) }}%
                    </span>
                    <ChevronDownIcon
                      class="h-4 w-4 text-gray-400 transition-transform duration-200"
                      :class="{ 'rotate-180': expandedModules[module.id] }"
                    />
                  </div>
                </div>
                <!-- Module Topics - Collapsible -->
                <div v-if="expandedModules[module.id]" class="bg-gray-50 border-t border-gray-100">
                  <div
                    v-for="topic in module.topics"
                    :key="topic.id"
                    @click="selectTopic(topic)"
                    :class="[
                      'group flex items-center px-4 py-3 cursor-pointer transition-all duration-200',
                      current_topic.id === topic.id
                        ? 'bg-white border-l-4 border-emerald-500 shadow-sm'
                        : 'hover:bg-white hover:border-l-4 hover:border-emerald-200',
                      topic.is_completed ? 'opacity-100' : 'opacity-100'
                    ]"
                  >
                    <div class="flex items-center flex-1 min-w-0">
                      <div
                        :class="[
                          'w-6 h-6 rounded-full flex items-center justify-center text-xs font-medium mr-3 flex-shrink-0 border-2',
                          current_topic.id === topic.id
                            ? 'bg-emerald-500 text-white border-emerald-500'
                            : topic.is_completed
                            ? 'bg-emerald-500 text-white border-emerald-500'
                            : 'bg-white text-gray-400 border-gray-300'
                        ]"
                      >
                        <CheckCircleIcon v-if="topic.is_completed" class="h-3 w-3" />
                        <span v-else>{{ getTopicNumber(topic) }}</span>
                      </div>
                      <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-medium text-gray-900 truncate">{{ topic.title }}</h4>
                        <p class="text-xs text-gray-500 mt-0.5 flex items-center">
                          <ClockIcon class="h-3 w-3 mr-1" />
                          {{ topic.estimated_duration_minutes }}min
                          <span v-if="topic.has_quiz" class="ml-2 inline-flex items-center">
                            <QuestionMarkCircleIcon class="h-3 w-3 mr-1" />
                            Quiz
                          </span>
                          <span v-if="topic.has_project" class="ml-2 inline-flex items-center">
                            <BriefcaseIcon class="h-3 w-3 mr-1" />
                            Project
                          </span>
                        </p>
                      </div>
                    </div>
                    <!-- Topic Status Indicator -->
                    <div class="flex items-center space-x-2 flex-shrink-0 ml-2">
                      <div
                        v-if="current_topic.id === topic.id"
                        class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"
                      ></div>
                      <PlayCircleIcon
                        v-if="!topic.is_completed && current_topic.id !== topic.id"
                        class="h-4 w-4 text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity"
                      />
                    </div>
                  </div>
                </div>
              </div>
            </nav>
          </div>
          <!-- Quick Actions - Enhanced -->
          <div class="p-4 border-t border-gray-200 bg-white space-y-3">
            <Link
              v-if="course.chat_sessions && course.chat_sessions.length > 0"
              :href="route('student.chat.show', { chatSession: course.chat_sessions[0].id})"
              class="w-full flex items-center justify-center px-4 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
            >
              <ChatBubbleLeftRightIcon class="h-4 w-4 mr-2" />
              Ask AI Tutor
            </Link>
            <div class="grid grid-cols-2 gap-2">
              <button
                v-if="hasPreviousTopic"
                @click="goToPreviousTopic"
                class="flex items-center justify-center px-3 py-2.5 border border-emerald-300 text-emerald-700 font-medium rounded-lg hover:bg-emerald-50 transition-colors text-sm"
              >
                <ChevronLeftIcon class="h-4 w-4 mr-1" />
                Previous
              </button>
              <button
                v-if="hasNextTopic"
                @click="goToNextTopic"
                class="flex items-center justify-center px-3 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-medium rounded-lg hover:from-emerald-700 hover:to-teal-700 transition-all duration-200 shadow-sm hover:shadow-md text-sm"
              >
                Next
                <ChevronRightIcon class="h-4 w-4 ml-1" />
              </button>
            </div>
          </div>
        </div>
      </div>
      <!-- Main Content Area - Enhanced with Tabs -->
      <div class="flex flex-col w-0 flex-1 overflow-hidden">
        <!-- Mobile Header -->
        <div class="lg:hidden bg-white border-b border-gray-200 shadow-sm">
          <div class="flex items-center justify-between px-4 py-3">
            <div class="flex items-center space-x-3">
              <button
                @click="mobileSidebarOpen = true"
                class="p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-colors"
              >
                <Bars3Icon class="h-5 w-5" />
              </button>
              <div class="flex-1 min-w-0">
                <h1 class="text-sm font-semibold text-gray-900 truncate">{{ course.title }}</h1>
                <p class="text-xs text-gray-500 truncate">{{ current_module?.title }}</p>
              </div>
            </div>
            <div class="flex items-center space-x-2">
              <div class="text-right">
                <div class="text-sm font-medium text-gray-900">{{ Math.round(courseStats.overall_completion_percentage || 0) }}%</div>
                <div class="text-xs text-gray-500">Complete</div>
              </div>
            </div>
          </div>
        </div>
        <!-- Content Area with Tabs -->
        <main class="flex-1 relative overflow-y-auto focus:outline-none bg-transparent">
          <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-6xl mx-auto">
            <!-- Current Topic Header -->
            <div class="mb-6">
              <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                  <!-- Breadcrumb -->
                  <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-3">
                    <Link
                      :href="route('student.courses.show', course.id)"
                      class="hover:text-gray-700 transition-colors"
                    >
                      {{ course.title }}
                    </Link>
                    <ChevronRightIcon class="h-4 w-4" />
                    <span class="text-gray-900 font-medium">{{ current_module?.title }}</span>
                  </nav>
                  <!-- Topic Title and Info -->
                  <div class="flex items-start justify-between">
                    <div class="flex-1">
                      <div class="flex items-center space-x-3 mb-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                          {{ getOutlineTypeLabel(current_topic.type) }} {{ getTopicNumber(current_topic) }}
                        </span>
                        <span class="text-sm text-gray-500 flex items-center">
                          <ClockIcon class="h-4 w-4 mr-1" />
                          {{ current_topic.estimated_duration_minutes }} minutes
                        </span>
                      </div>
                      <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 leading-tight">
                        {{ current_topic.title }}
                      </h1>
                      <p v-if="current_topic.description" class="mt-2 text-lg text-gray-600 leading-relaxed">
                        {{ current_topic.description }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Module Progress -->
              <div v-if="current_module" class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                  <span class="text-sm font-medium text-gray-900">Module Progress</span>
                  <span class="text-sm text-gray-500">
                    {{ getModuleProgress(current_module) }}/{{ getModuleTopicCount(current_module) }} topics
                  </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                  <div
                    class="bg-gradient-to-r from-emerald-500 to-teal-500 h-2.5 rounded-full transition-all duration-500"
                    :style="{ width: `${(getModuleProgress(current_module) / getModuleTopicCount(current_module)) * 100}%` }"
                  ></div>
                </div>
              </div>
            </div>
            <!-- Content Tabs -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
              <!-- Tab Headers -->
              <div class="border-b border-gray-200">
                <nav class="flex space-x-8 px-6" aria-label="Tabs">
                  <button
                    v-for="tab in filteredTabs"
                    :key="tab.id"
                    @click="activeTab = tab.id"
                    :class="[
                      'py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200',
                      activeTab === tab.id
                        ? 'border-emerald-500 text-emerald-600'
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                    ]"
                  >
                    <div class="flex items-center">
                      <component :is="tab.icon" class="h-4 w-4 mr-2" />
                      {{ tab.name }}
                      <span
                        v-if="tab.badge"
                        class="ml-2 py-0.5 px-2 text-xs rounded-full"
                        :class="tab.badgeClass"
                      >
                        {{ tab.badge }}
                      </span>
                    </div>
                  </button>
                </nav>
              </div>
              <!-- Tab Content -->
              <div class="p-6">
                <!-- Content Tab -->
                <div v-if="activeTab === 'content'" class="prose prose-lg max-w-none">
                  <!-- Audio Player Header -->
                  <div v-if="current_topic.contents?.length" class="mb-6">
                    <div class="flex items-center justify-between p-4 bg-emerald-50 rounded-lg border border-emerald-200">
                      <div class="flex items-center space-x-4">
                        <button
                          @click="toggleAudioPlayback"
                          :disabled="!hasAudioContent"
                          class="flex items-center justify-center w-12 h-12 bg-emerald-500 hover:bg-emerald-600 text-white rounded-full transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                          <PlayCircleIcon v-if="!isPlaying" class="h-6 w-6" />
                          <PauseCircleIcon v-else class="h-6 w-6" />
                        </button>
                        <div>
                          <h3 class="font-semibold text-gray-900">Listen to Content</h3>
                          <p class="text-sm text-gray-600">AI-generated audio narration</p>
                        </div>
                      </div>
                      <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-500">{{ audioProgress }} / {{ audioDuration }}</span>
                        <button
                          @click="stopAudioPlayback"
                          :disabled="!isPlaying"
                          class="text-gray-500 hover:text-gray-700 disabled:opacity-50"
                        >
                          <StopCircleIcon class="h-5 w-5" />
                        </button>
                      </div>
                    </div>
                    <!-- Progress Bar -->
                    <div v-if="hasAudioContent" class="w-full bg-gray-200 rounded-full h-2 mt-2">
                      <div
                        class="bg-emerald-500 h-2 rounded-full transition-all duration-300"
                        :style="{ width: `${audioProgressPercentage}%` }"
                      ></div>
                    </div>
                  </div>
                  <div v-if="current_topic.contents?.length" class="space-y-6">
                    <div
                      v-for="content in current_topic.contents"
                      :key="content.id"
                      class="prose prose-emerald max-w-none"
                    >
                      <div v-html="formatContent(content.content)"></div>
                      <!-- Audio Playback for Individual Content -->
                      <div class="mt-4 flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <button
                          @click="playContentAudio(content)"
                          class="flex items-center space-x-2 text-emerald-600 hover:text-emerald-700 font-medium"
                        >
                          <PlayCircleIcon class="h-5 w-5" />
                          <span>Listen to this section</span>
                        </button>
                        <span class="text-sm text-gray-500">{{ estimateReadTime(content.content) }} min read/listen</span>
                      </div>
                    </div>
                  </div>
                  <!-- Generate Content Button -->
                  <div v-else class="text-center py-12">
                    <AcademicCapIcon class="mx-auto h-16 w-16 text-gray-300 mb-4" />
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Ready to Learn?</h3>
                    <p class="text-gray-500 mb-6 max-w-md mx-auto">
                      Generate personalized learning content for this topic. Olilearn AI will create engaging materials tailored to your learning style.
                    </p>
                    <button
                      @click="generateContent(current_topic)"
                      :disabled="contentLoading"
                      class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                      <AcademicCapIcon class="h-5 w-5 mr-2" />
                      {{ contentLoading ? 'Generating Content...' : 'Start Learning' }}
                    </button>
                  </div>
                </div>

                <!-- Learning Objectives Tab -->
                <div v-if="activeTab === 'objectives'" class="space-y-6">
                  <div v-if="current_topic.learning_objectives?.length">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">What You'll Learn</h3>
                    <div class="grid gap-3">
                      <div
                        v-for="(objective, index) in current_topic.learning_objectives"
                        :key="index"
                        class="flex items-start p-4 bg-emerald-50 rounded-lg border border-emerald-100"
                      >
                        <CheckCircleIcon class="h-5 w-5 text-emerald-600 mr-3 mt-0.5 flex-shrink-0" />
                        <span class="text-emerald-800">{{ objective }}</span>
                      </div>
                    </div>
                  </div>
                  <div v-if="current_topic.key_concepts?.length">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Key Concepts</h3>
                    <div class="flex flex-wrap gap-2">
                      <span
                        v-for="(concept, index) in current_topic.key_concepts"
                        :key="index"
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-purple-100 text-purple-800"
                      >
                        <TagIcon class="h-3 w-3 mr-1" />
                        {{ concept }}
                      </span>
                    </div>
                  </div>
                  <div v-if="current_topic.resources?.length">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Resources</h3>
                    <div class="space-y-2">
                      <div
                        v-for="(resource, index) in current_topic.resources"
                        :key="index"
                        class="flex items-center p-3 bg-gray-50 rounded-lg border border-gray-200"
                      >
                        <LinkIcon class="h-4 w-4 text-gray-400 mr-3" />
                        <span class="text-gray-700">{{ resource }}</span>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Quiz Tab -->
                <div v-if="activeTab === 'quiz'" class="space-y-6">
                  <!-- Quiz content remains the same as in your original code -->
                  <!-- Quiz Overview State -->
                  <div v-if="quizState === 'overview' && current_topic.quiz" class="space-y-6">
                    <!-- Quiz Header -->
                    <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-xl p-6">
                      <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                          <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ current_topic.quiz.title }}</h3>
                          <p class="text-gray-600 mb-4">{{ current_topic.quiz.description }}</p>
                          <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div class="text-center p-3 bg-white rounded-lg border border-amber-100">
                              <div class="font-bold text-amber-700">{{ current_topic.quiz.questions?.length || 0 }}</div>
                              <div class="text-amber-600">Questions</div>
                            </div>
                            <div class="text-center p-3 bg-white rounded-lg border border-amber-100">
                              <div class="font-bold text-amber-700">{{ current_topic.quiz.time_limit_minutes }}</div>
                              <div class="text-amber-600">Minutes</div>
                            </div>
                            <div class="text-center p-3 bg-white rounded-lg border border-amber-100">
                              <div class="font-bold text-amber-700">{{ current_topic.quiz.passing_score }}%</div>
                              <div class="text-amber-600">Passing Score</div>
                            </div>
                            <div class="text-center p-3 bg-white rounded-lg border border-amber-100">
                              <div class="font-bold text-amber-700">{{ currentAttemptsCount }}/{{ current_topic.quiz.max_attempts }}</div>
                              <div class="text-amber-600">Attempts</div>
                            </div>
                          </div>
                        </div>
                        <QuestionMarkCircleIcon class="h-12 w-12 text-amber-400 ml-4 flex-shrink-0" />
                      </div>
                      <!-- Start Quiz Button -->
                      <button
                        @click="startQuiz"
                        :disabled="!canAttemptQuiz || quizLoading"
                        class="w-full flex items-center justify-center px-6 py-3 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed"
                      >
                        <PlayCircleIcon class="h-5 w-5 mr-2" />
                        {{ getQuizButtonText }}
                      </button>
                    </div>
                    <!-- Previous Attempts -->
                    <div v-if="current_topic.quiz.attempts?.length" class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                      <h4 class="text-lg font-semibold text-gray-900 mb-4">Previous Attempts</h4>
                      <div class="space-y-3">
                        <div
                          v-for="attempt in current_topic.quiz.attempts"
                          :key="attempt.id"
                          class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
                        >
                          <div class="flex items-center space-x-4">
                            <div
                              :class="[
                                'w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold text-sm',
                                attempt.is_passed ? 'bg-emerald-500' : 'bg-red-500'
                              ]"
                            >
                              {{ Math.round(attempt.percentage) }}%
                            </div>
                            <div>
                              <div class="font-medium text-gray-900">
                                Attempt {{ attempt.attempt_number }}
                              </div>
                              <div class="text-sm text-gray-500">
                                {{ formatDate(attempt.completed_at) }}
                              </div>
                            </div>
                          </div>
                          <div class="flex items-center space-x-2">
                            <span
                              :class="[
                                'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold',
                                attempt.is_passed ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800'
                              ]"
                            >
                              {{ attempt.is_passed ? 'Passed' : 'Failed' }}
                            </span>
                            <button
                              @click="viewAttemptResults(attempt)"
                              class="text-amber-600 hover:text-amber-700 font-medium text-sm"
                            >
                              View Results
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- Active Quiz State -->
                  <div v-if="quizState === 'active' && currentQuizAttempt" class="space-y-6">
                    <!-- Quiz Header with Timer -->
                    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                      <div class="flex items-center justify-between mb-4">
                        <div>
                          <h3 class="text-xl font-semibold text-gray-900">{{ current_topic.quiz.title }}</h3>
                          <p class="text-gray-600">Question {{ currentQuestionIndex + 1 }} of {{ current_topic.quiz.questions.length }}</p>
                        </div>
                        <div class="text-right">
                          <div class="text-2xl font-bold text-gray-900" :class="{'text-red-600': timeRemaining < 60}">
                            {{ formatTime(timeRemaining) }}
                          </div>
                          <div class="text-sm text-gray-500">Time Remaining</div>
                        </div>
                      </div>
                      <!-- Progress Bar -->
                      <div class="w-full bg-gray-200 rounded-full h-2.5 mb-4">
                        <div
                          class="bg-amber-500 h-2.5 rounded-full transition-all duration-300"
                          :style="{ width: `${((currentQuestionIndex + 1) / current_topic.quiz.questions.length) * 100}%` }"
                        ></div>
                      </div>
                    </div>
                    <!-- Current Question -->
                    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                      <h4 class="text-lg font-semibold text-gray-900 mb-4">
                        {{ currentQuestion.question }}
                      </h4>
                      <!-- Multiple Choice Options -->
                      <div v-if="currentQuestion.type === 'multiple_choice'" class="space-y-3">
                        <button
                          v-for="(option, index) in shuffledOptions"
                          :key="index"
                          @click="selectAnswer(option)"
                          :class="[
                            'w-full text-left p-4 border rounded-xl transition-all duration-200',
                            selectedAnswer === option
                              ? 'border-amber-500 bg-amber-50 ring-2 ring-amber-200'
                              : 'border-gray-200 hover:border-amber-300 hover:bg-amber-25'
                          ]"
                        >
                          <div class="flex items-center">
                            <div
                              :class="[
                                'w-6 h-6 rounded-full border-2 flex items-center justify-center mr-3 flex-shrink-0',
                                selectedAnswer === option
                                  ? 'border-amber-500 bg-amber-500 text-white'
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
                      <div v-if="currentQuestion.type === 'true_false'" class="grid grid-cols-2 gap-4">
                        <button
                          v-for="option in ['True', 'False']"
                          :key="option"
                          @click="selectAnswer(option)"
                          :class="[
                            'p-4 border rounded-xl transition-all duration-200 text-center',
                            selectedAnswer === option
                              ? 'border-amber-500 bg-amber-50 ring-2 ring-amber-200'
                              : 'border-gray-200 hover:border-amber-300 hover:bg-amber-25'
                          ]"
                        >
                          <span class="font-medium text-gray-700">{{ option }}</span>
                        </button>
                      </div>
                      <!-- Navigation Buttons -->
                      <div class="flex justify-between items-center mt-6 pt-6 border-t border-gray-200">
                        <button
                          @click="previousQuestion"
                          :disabled="currentQuestionIndex === 0"
                          class="flex items-center px-4 py-2.5 border border-emerald-300 text-emerald-700 font-medium rounded-lg hover:bg-emerald-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                          <ChevronLeftIcon class="h-4 w-4 mr-2" />
                          Previous
                        </button>
                        <div class="flex items-center space-x-3">
                          <button
                            v-if="currentQuestionIndex < current_topic.quiz.questions.length - 1"
                            @click="nextQuestion"
                            :disabled="!selectedAnswer"
                            class="flex items-center px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                          >
                            Next
                            <ChevronRightIcon class="h-4 w-4 ml-2" />
                          </button>
                          <button
                            v-else
                            @click="submitQuiz"
                            :disabled="!selectedAnswer"
                            class="flex items-center px-6 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                          >
                            <CheckIcon class="h-4 w-4 mr-2" />
                            Submit Quiz
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- Quiz Results State -->
                  <div v-if="quizState === 'results' && quizResults" class="space-y-6">
                    <!-- Results Header -->
                    <div class="bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 rounded-xl p-6">
                      <div class="text-center">
                        <div
                          :class="[
                            'w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4 text-white text-2xl font-bold',
                            quizResults.is_passed ? 'bg-emerald-500' : 'bg-red-500'
                          ]"
                        >
                          {{ Math.round(quizResults.percentage) }}%
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">
                          {{ quizResults.is_passed ? 'Congratulations!' : 'Keep Practicing!' }}
                        </h3>
                        <p class="text-gray-600 mb-4">
                          You scored {{ quizResults.score }} out of {{ quizResults.total_points }} points
                          ({{ Math.round(quizResults.percentage) }}%)
                        </p>
                        <div class="flex justify-center space-x-4">
                          <button
                            @click="quizState = 'overview'"
                            class="px-6 py-2.5 border border-emerald-300 text-emerald-700 font-medium rounded-lg hover:bg-emerald-50 transition-colors"
                          >
                            Back to Overview
                          </button>
                          <button
                            v-if="canRetakeQuiz"
                            @click="retakeQuiz"
                            class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-medium rounded-lg transition-colors"
                          >
                            Retake Quiz
                          </button>
                        </div>
                      </div>
                    </div>
                    <!-- Detailed Results -->
                    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                      <h4 class="text-lg font-semibold text-gray-900 mb-4">Question Review</h4>
                      <div class="space-y-4">
                        <div
                          v-for="(result, index) in quizResults.detailed_results"
                          :key="index"
                          :class="[
                            'p-4 border rounded-xl',
                            result.is_correct ? 'border-emerald-200 bg-emerald-50' : 'border-red-200 bg-red-50'
                          ]"
                        >
                          <div class="flex items-start justify-between mb-2">
                            <h5 class="font-medium text-gray-900">Question {{ index + 1 }}</h5>
                            <span
                              :class="[
                                'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold',
                                result.is_correct ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800'
                              ]"
                            >
                              {{ result.is_correct ? 'Correct' : 'Incorrect' }}
                            </span>
                          </div>
                          <p class="text-gray-700 mb-3">{{ result.question }}</p>
                          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                              <span class="font-medium text-gray-500">Your answer:</span>
                              <p :class="['mt-1', result.is_correct ? 'text-emerald-700' : 'text-red-700']">
                                {{ result.user_answer || 'No answer' }}
                              </p>
                            </div>
                            <div v-if="!result.is_correct">
                              <span class="font-medium text-gray-500">Correct answer:</span>
                              <p class="mt-1 text-emerald-700">{{ result.correct_answer }}</p>
                            </div>
                          </div>
                          <div v-if="result.explanation" class="mt-3 p-3 bg-blue-50 rounded-lg">
                            <span class="font-medium text-blue-700">Explanation:</span>
                            <p class="mt-1 text-blue-600">{{ result.explanation }}</p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- Generate Quiz State -->
                  <div v-else-if="!current_topic.quiz" class="text-center py-12">
                    <QuestionMarkCircleIcon class="mx-auto h-16 w-16 text-gray-300 mb-4" />
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Ready to start this quiz?</h3>
                    <p class="text-gray-500 mb-6 max-w-md mx-auto">
                      Get the quiz for this topic to test your understanding.
                    </p>
                    <button
                      @click="generateQuiz(current_topic)"
                      :disabled="quizLoading"
                      class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                      <QuestionMarkCircleIcon class="h-5 w-5 mr-2" />
                      {{ quizLoading ? 'Generating Quiz...' : 'Generate Quiz' }}
                    </button>
                  </div>
                </div>

                <!-- Project Tab -->
                <div v-if="activeTab === 'project'" class="space-y-6">
                  <!-- Project content remains the same as in your original code -->
                  <div class="bg-gradient-to-r from-purple-50 to-indigo-50 border border-purple-200 rounded-xl p-6">
                    <div class="flex items-start justify-between mb-4">
                      <div class="flex-1">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Project Assignment</h3>
                        <p class="text-gray-600 mb-4">
                          This topic includes a hands-on project to apply what you've learned.
                        </p>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                          <div class="text-center p-3 bg-white rounded-lg border border-purple-100">
                            <div class="font-bold text-purple-700">{{ current_topic.estimated_duration_minutes }}min</div>
                            <div class="text-purple-600">Estimated Time</div>
                          </div>
                          <div class="text-center p-3 bg-white rounded-lg border border-purple-100">
                            <div class="font-bold text-purple-700">Hands-on</div>
                            <div class="text-purple-600">Project Type</div>
                          </div>
                          <div class="text-center p-3 bg-white rounded-lg border border-purple-100">
                            <div class="font-bold text-purple-700">Required</div>
                            <div class="text-purple-600">Completion</div>
                          </div>
                        </div>
                      </div>
                      <BriefcaseIcon class="h-12 w-12 text-purple-400 ml-4 flex-shrink-0" />
                    </div>
                    <!-- Project Requirements -->
                    <div v-if="course.capstone_project.requirements" class="bg-white rounded-xl border border-purple-100 p-4 mb-4 shadow-sm">
                      <h4 class="font-semibold text-gray-900 mb-3">Project Requirements</h4>
                      <div class="space-y-2">
                        <div
                          v-for="(requirement, index) in course.capstone_project.requirements"
                          :key="index"
                          class="flex items-start"
                        >
                          <CheckCircleIcon class="h-4 w-4 text-purple-500 mr-2 mt-1 flex-shrink-0" />
                          <span class="text-gray-700">{{ requirement }}</span>
                        </div>
                      </div>
                    </div>
                    <!-- Project Actions -->
                    <div class="flex space-x-4">
                      <button
                        @click="startProject"
                        class="flex-1 flex items-center justify-center px-6 py-3 bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md"
                      >
                        <PlayCircleIcon class="h-5 w-5 mr-2" />
                        Start Project
                      </button>
                      <button
                        v-if="current_topic.project_resources"
                        @click="downloadProjectResources"
                        class="flex items-center px-4 py-3 border border-purple-300 text-purple-700 font-medium rounded-lg hover:bg-purple-50 transition-colors"
                      >
                        <ArrowDownTrayIcon class="h-5 w-5 mr-2" />
                        Resources
                      </button>
                    </div>
                  </div>
                  <!-- Project Submission Area -->
                  <div v-if="projectState === 'active'" class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Submit Your Project</h4>
                    <div class="space-y-4">
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Project Submission</label>
                        <textarea
                          v-model="projectSubmission"
                          rows="6"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                          placeholder="Describe your project implementation, include code snippets, screenshots, or any relevant details..."
                        ></textarea>
                      </div>
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Attachment (Optional)</label>
                        <input
                          type="file"
                          @change="handleProjectFileUpload"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                        />
                      </div>
                      <div class="flex justify-end space-x-3">
                        <button
                          @click="cancelProject"
                          class="px-4 py-2.5 border border-emerald-300 text-emerald-700 font-medium rounded-lg hover:bg-emerald-50 transition-colors"
                        >
                          Cancel
                        </button>
                        <button
                          @click="submitProject"
                          :disabled="!projectSubmission.trim()"
                          class="px-6 py-2.5 bg-purple-500 hover:bg-purple-600 text-white font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                          Submit Project
                        </button>
                      </div>
                    </div>
                  </div>
                  <!-- Previous Submissions -->
                  <div v-if="current_topic.project_submissions?.length" class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Previous Submissions</h4>
                    <div class="space-y-3">
                      <div
                        v-for="submission in current_topic.project_submissions"
                        :key="submission.id"
                        class="p-4 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors"
                      >
                        <div class="flex items-center justify-between mb-2">
                          <div class="font-medium text-gray-900">
                            Submitted {{ formatDate(submission.submitted_at) }}
                          </div>
                          <span
                            v-if="submission.status"
                            :class="[
                              'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold',
                              submission.status === 'approved' ? 'bg-emerald-100 text-emerald-800' :
                              submission.status === 'rejected' ? 'bg-red-100 text-red-800' :
                              'bg-amber-100 text-amber-800'
                            ]"
                          >
                            {{ submission.status }}
                          </span>
                        </div>
                        <p class="text-gray-600 text-sm mb-3">{{ submission.description }}</p>
                        <div v-if="submission.feedback" class="p-3 bg-blue-50 rounded-lg">
                          <span class="font-medium text-blue-700">Instructor Feedback:</span>
                          <p class="mt-1 text-blue-600">{{ submission.feedback }}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Requirements Tab -->
                <div v-if="activeTab === 'requirements'" class="space-y-6">
                <div class="bg-gradient-to-r from-blue-50 to-cyan-50 border border-blue-200 rounded-xl p-6">
                    <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Project Requirements</h3>
                        <p class="text-gray-600 mb-4">
                        Review the project requirements to understand what needs to be delivered for this project.
                        </p>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                        <div class="text-center p-3 bg-white rounded-lg border border-blue-100">
                            <div class="font-bold text-blue-700">{{ current_topic.estimated_duration_minutes }}min</div>
                            <div class="text-blue-600">Estimated Time</div>
                        </div>
                        <div class="text-center p-3 bg-white rounded-lg border border-blue-100">
                            <div class="font-bold text-blue-700">Hands-on</div>
                            <div class="text-blue-600">Project Type</div>
                        </div>
                        <div class="text-center p-3 bg-white rounded-lg border border-blue-100">
                            <div class="font-bold text-blue-700">Required</div>
                            <div class="text-blue-600">Completion</div>
                        </div>
                        </div>
                    </div>
                    <ClipboardDocumentListIcon class="h-12 w-12 text-blue-400 ml-4 flex-shrink-0" />
                    </div>
                    
                    <!-- Project Requirements Content -->
                    <div v-if="current_topic.project_requirements && current_topic.project_requirements.length" class="bg-white rounded-xl border border-blue-100 p-4 mb-4 shadow-sm">
                    <h4 class="font-semibold text-gray-900 mb-3">Project Requirements</h4>
                    <div class="space-y-3">
                        <div
                        v-for="(requirement, index) in current_topic.project_requirements"
                        :key="index"
                        class="flex items-start p-3 bg-blue-50 rounded-lg"
                        >
                        <CheckCircleIcon class="h-4 w-4 text-blue-500 mr-2 mt-1 flex-shrink-0" />
                        <div>
                            <span class="font-medium text-gray-900">{{ requirement }}</span>
                        </div>
                        </div>
                    </div>
                    </div>

                    <!-- Generate Requirements Button -->
                    <div v-else class="text-center py-8">
                    <ClipboardDocumentListIcon class="mx-auto h-16 w-16 text-gray-300 mb-4" />
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No Requirements Available</h3>
                    <p class="text-gray-500 mb-6 max-w-md mx-auto">
                        Generate project requirements to understand what needs to be delivered for this project.
                    </p>
                    <button
                        @click="generateRequirements(current_topic)"
                        :disabled="requirementsLoading"
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <ClipboardDocumentListIcon class="h-5 w-5 mr-2" />
                        {{ requirementsLoading ? 'Generating Requirements...' : 'Generate Requirements' }}
                    </button>
                    </div>
                </div>
                </div>
              </div>
            </div>
            <!-- Navigation Footer - Moved Mark as Complete button here -->
            <div class="mt-8 flex justify-between items-center">
            <button
                v-if="hasPreviousTopic"
                @click="goToPreviousTopic"
                class="inline-flex items-center px-6 py-3 border border-emerald-300 text-emerald-700 font-medium rounded-lg hover:bg-emerald-50 transition-colors"
            >
                <ChevronLeftIcon class="h-4 w-4 mr-2" />
                Previous Topic
            </button>
            <div v-else></div>
            <div class="flex items-center space-x-4">
                <!-- Mark as Complete Button - Moved here -->
                <button
                v-if="!current_topic.is_completed"
                @click="markAsComplete"
                :disabled="!canMarkAsComplete || markCompleteLoading"
                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed relative group"
                :title="!canMarkAsComplete ? getDisabledReason : ''"
                >
                <CheckCircleIcon class="h-4 w-4 mr-2" />
                {{ markCompleteLoading ? 'Marking...' : 'Mark as Complete' }}
                </button>
                <button
                v-else
                disabled
                class="inline-flex items-center px-6 py-3 bg-gray-400 text-white font-medium rounded-lg cursor-not-allowed"
                >
                <CheckCircleIcon class="h-4 w-4 mr-2" />
                Completed
                </button>
                <button
                v-if="hasNextTopic"
                @click="goToNextTopic"
                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md"
                >
                Next Topic
                <ChevronRightIcon class="h-4 w-4 ml-2" />
                </button>
            </div>
            </div>
          </div>
        </main>
      </div>
      <!-- Mobile Sidebar Overlay -->
      <MobileSidebar
        :open="mobileSidebarOpen"
        @close="mobileSidebarOpen = false"
        :course="course"
        :course-structure="course_structure"
        :current-topic="current_topic"
        :course-stats="courseStats"
        @select-topic="selectTopic"
        @toggle-module="toggleModule"
        :expanded-modules="expandedModules"
      />
    </div>
  </StudentLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { marked } from "marked"
import DOMPurify from "dompurify"
import MobileSidebar from '@/Components/LearnMobileSidebar.vue'
import {
  AcademicCapIcon,
  QuestionMarkCircleIcon,
  CheckCircleIcon,
  CheckIcon,
  ChevronLeftIcon,
  ChevronRightIcon,
  ChevronDownIcon,
  Bars3Icon,
  ClockIcon,
  PlayCircleIcon,
  ChatBubbleLeftRightIcon,
  TagIcon,
  LinkIcon,
  BookOpenIcon,
  ListBulletIcon,
  ClipboardDocumentListIcon,
  BriefcaseIcon,
  ArrowDownTrayIcon,
  PauseCircleIcon,
  StopCircleIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  course: Object,
  course_structure: Array,
  current_topic: Object,
  course_stats: Object,
})

// Reactive state
const mobileSidebarOpen = ref(false)
const markCompleteLoading = ref(false)
const contentLoading = ref(false)
const quizLoading = ref(false)
const activeTab = ref('content')
const expandedModules = ref({})

// Quiz state
const quizState = ref('overview') // 'overview', 'active', 'results'
const currentQuizAttempt = ref(null)
const currentQuestionIndex = ref(0)
const selectedAnswer = ref(null)
const userAnswers = ref([])
const timeRemaining = ref(0)
const quizResults = ref(null)
const timerInterval = ref(null)

// Project state
const projectState = ref('overview')
const projectSubmission = ref('')
const projectFile = ref(null)

// Time tracking
const startTime = ref(null)
const currentSessionTime = ref(0)

// Audio playback state
const isPlaying = ref(false)
const audioProgress = ref('0:00')
const audioDuration = ref('0:00')
const audioProgressPercentage = ref(0)
const currentAudio = ref(null)
const speechSynthesis = ref(null)

// Check if browser supports speech synthesis
const hasAudioSupport = computed(() => {
  return 'speechSynthesis' in window
})

// Initialize expanded modules - expand current module
onMounted(() => {
  if (props.current_topic?.module_id) {
    expandedModules.value[props.current_topic.module_id] = true
  }
  // Start time tracking
  startTimeTracking()
})

// Clean up timer on unmount
onUnmounted(() => {
  if (timerInterval.value) {
    clearInterval(timerInterval.value)
  }
  recordTimeSpent()
})

// Get outline type label
const getOutlineTypeLabel = (type) => {
  const labels = {
    topic: 'Topic',
    quiz: 'Quiz',
    project: 'Project'
  }
  return labels[type] || 'Topic'
}

// Filtered tabs based on topic type
const filteredTabs = computed(() => {
  const outlineType = props.current_topic.type || 'topic'
  
  // Start with an empty array instead of baseTabs
  const tabs = []

  // Add tabs based on outline type
  if (outlineType === 'topic') {
    tabs.push({
      id: 'content',
      name: 'Content',
      icon: BookOpenIcon,
      badge: props.current_topic.contents?.length || '0',
      badgeClass: 'bg-emerald-100 text-emerald-800'
    })
    
    tabs.push({
      id: 'objectives',
      name: 'Learning Guide',
      icon: ListBulletIcon,
      badge: (props.current_topic.learning_objectives?.length || 0) + (props.current_topic.key_concepts?.length || 0),
      badgeClass: 'bg-purple-100 text-purple-800'
    })
    
    // Add quiz tab only if topic has quiz and is not a capstone
    if (props.current_topic.has_quiz && !props.current_topic.is_capstone) {
      tabs.push({
        id: 'quiz',
        name: 'Quiz',
        icon: ClipboardDocumentListIcon,
        badge: props.current_topic.has_quiz ? 'Available' : 'None',
        badgeClass: props.current_topic.has_quiz ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-800'
      })
    }
    
    // Add project tab only if topic has project
    if (props.current_topic.has_project) {
      tabs.push({
        id: 'project',
        name: 'Project',
        icon: BriefcaseIcon,
        badge: props.current_topic.has_project ? 'Available' : 'None',
        badgeClass: props.current_topic.has_project ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800'
      })
    }
  } 
  else if (outlineType === 'quiz') {
    // For quiz type, only show quiz tab (no learning guide)
    tabs.push({
      id: 'quiz',
      name: 'Quiz',
      icon: ClipboardDocumentListIcon,
      badge: 'Available',
      badgeClass: props.current_topic.has_quiz ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-800'
    })
  } 
  else if (outlineType === 'project') {
    // For project type, show project tab, learning guide, and requirements tab
    tabs.push({
      id: 'project',
      name: 'Project',
      icon: BriefcaseIcon,
      badge: props.current_topic.has_project ? 'Available' : 'None',
      badgeClass: props.current_topic.has_project ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800'
    })
    
    tabs.push({
      id: 'objectives',
      name: 'Learning Guide',
      icon: ListBulletIcon,
      badge: (props.current_topic.learning_objectives?.length || 0) + (props.current_topic.key_concepts?.length || 0),
      badgeClass: 'bg-purple-100 text-purple-800'
    })
    
    
  }

  return tabs
})

// Helper Functions
const formatStudyTime = (minutes) => {
  if (!minutes || minutes === 0) return '0m'
  const hours = Math.floor(minutes / 60)
  const mins = Math.round(minutes % 60)
  if (hours > 0) {
    return `${hours}h ${mins}m`
  }
  return `${mins}m`
}

const formatTime = (seconds) => {
  const minutes = Math.floor(seconds / 60)
  const remainingSeconds = seconds % 60
  return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const getStatusClass = (status) => {
  const classes = {
    draft: 'bg-gray-100 text-gray-800',
    active: 'bg-emerald-100 text-emerald-800',
    paused: 'bg-amber-100 text-amber-800',
    completed: 'bg-teal-100 text-teal-800',
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getTopicNumber = (topic) => {
  const module = props.course_structure.find(m =>
    m.topics.some(t => t.id === topic.id)
  )
  if (module) {
    const topicIndex = module.topics.findIndex(t => t.id === topic.id)
    return `${module.order}.${topicIndex + 1}`
  }
  return '1.1'
}

const getModuleTopicCount = (module) => {
  return module.topics?.length || 0
}

const getModuleProgress = (module) => {
  return module.topics?.filter(topic => topic.is_completed).length || 0
}

const calculateModuleCompletion = (module) => {
  const completedTopics = module.topics.filter(topic => topic.is_completed).length
  const totalTopics = module.topics.length
  return totalTopics > 0 ? Math.round((completedTopics / totalTopics) * 100) : 0
}

// Time tracking functions
const startTimeTracking = () => {
  startTime.value = new Date()
  // Update time every minute
  timerInterval.value = setInterval(() => {
    if (startTime.value) {
      const currentTime = new Date()
      currentSessionTime.value = Math.round((currentTime - startTime.value) / 1000 / 60) // minutes
    }
  }, 60000) // Update every minute
}

const recordTimeSpent = async () => {
  if (currentSessionTime.value > 0) {
    try {
      await fetch(route('student.courses.update-progress', props.course.id), {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
          topic_id: props.current_topic.id,
          activity_type: 'content_view',
          time_spent: currentSessionTime.value,
          completed: false
        })
      })
    } catch (error) {
      console.error('Error recording time:', error)
    }
  }
}

// Computed properties
const courseStats = computed(() => props.course_stats || {})
const current_module = computed(() => {
  return props.course_structure.find(module =>
    module.topics.some(topic => topic.id === props.current_topic.id)
  )
})
const allTopics = computed(() => {
  return props.course_structure.flatMap(module => module.topics)
})
const currentTopicIndex = computed(() =>
  allTopics.value.findIndex(topic => topic.id === props.current_topic.id)
)
const hasPreviousTopic = computed(() => currentTopicIndex.value > 0)
const hasNextTopic = computed(() => currentTopicIndex.value < allTopics.value.length - 1)

// Progress computed properties
const progressByActivity = computed(() => {
  if (!props.course.progress_tracking) return null
  const activities = {}
  props.course.progress_tracking.forEach(track => {
    if (!activities[track.activity_type]) {
      activities[track.activity_type] = { completed: 0, total: 0 }
    }
    activities[track.activity_type].total++
    if (track.is_completed) {
      activities[track.activity_type].completed++
    }
  })
  return Object.entries(activities).map(([type, stats]) => ({
    type,
    ...stats
  }))
})

// Quiz computed properties
const currentAttemptsCount = computed(() => {
  return props.current_topic.quiz?.attempts?.length || 0
})
const canAttemptQuiz = computed(() => {
  const quiz = props.current_topic.quiz
  if (!quiz || !quiz.is_active) return false
  return quiz.max_attempts === 0 || currentAttemptsCount.value < quiz.max_attempts
})
const canRetakeQuiz = computed(() => {
  return canAttemptQuiz.value && quizState.value === 'results'
})
const getQuizButtonText = computed(() => {
  if (!canAttemptQuiz.value) return 'No Attempts Left'
  if (currentAttemptsCount.value === 0) return 'Start Quiz'
  return `Attempt ${currentAttemptsCount.value + 1}`
})
const currentQuestion = computed(() => {
  if (!props.current_topic.quiz || !props.current_topic.quiz.questions) return null
  return props.current_topic.quiz.questions[currentQuestionIndex.value]
})

// Navigation methods
const selectTopic = (topic) => {
  // Record time spent on current topic before navigating
  recordTimeSpent().then(() => {
    router.get(route('student.courses.learn', {
      course: props.course.id,
      topic: topic.id
    }))
    mobileSidebarOpen.value = false
  })
}

const toggleModule = (moduleId) => {
  expandedModules.value[moduleId] = !expandedModules.value[moduleId]
}

const goToPreviousTopic = () => {
  if (hasPreviousTopic.value) {
    const previousTopic = allTopics.value[currentTopicIndex.value - 1]
    selectTopic(previousTopic)
  }
}

const goToNextTopic = () => {
  if (hasNextTopic.value) {
    const nextTopic = allTopics.value[currentTopicIndex.value + 1]
    selectTopic(nextTopic)
  }
}

const markAsComplete = async () => {
  markCompleteLoading.value = true
  try {
    // Include time spent in completion
    const totalTimeSpent = currentSessionTime.value
    await router.post(route('student.courses.completeTopic', {
      course: props.course.id,
      outline: props.current_topic.id
    }), {
      time_spent: totalTimeSpent
    }, {
      onSuccess: () => {
        // Reset timer for next topic
        startTime.value = new Date()
        currentSessionTime.value = 0
      }
    })
  } finally {
    markCompleteLoading.value = false
  }
}

const generateContent = async (topic) => {
  contentLoading.value = true
  try {
    await router.post(route('student.outlines.generate-content', {
      course: props.course.id,
      outline: topic.id
    }), {}, {
      onSuccess: () => {
        router.reload()
      },
      onError: (errors) => {
        console.error('Failed to generate content:', errors)
        alert('Failed to generate content. Please try again.')
      }
    })
  } catch (error) {
    console.error('Error generating content:', error)
    alert('An error occurred while generating content.')
  } finally {
    contentLoading.value = false
  }
}

function formatContent(content) {
  if (!content) return ""
  marked.setOptions({
    breaks: true,
    gfm: true,
    headerIds: true,
    langPrefix: "language-",
  })
  const cleaned = content.replace(/<\｜begin▁of▁sentence\｜>/g, '')
  const html = marked.parse(cleaned)
  return DOMPurify.sanitize(html)
}

// Quiz methods
const startQuiz = async () => {
  if (!canAttemptQuiz.value) return
  quizLoading.value = true
  try {
    console.log('Starting quiz for:', props.current_topic.quiz.id)
    const response = await fetch(route('student.quizzes.start', props.current_topic.quiz.id), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    })
    console.log('Response status:', response.status)
    if (response.ok) {
      const data = await response.json()
      console.log('Quiz start response:', data)
      currentQuizAttempt.value = data.attempt
      userAnswers.value = new Array(props.current_topic.quiz.questions.length).fill(null)
      currentQuestionIndex.value = 0
      selectedAnswer.value = null
      quizState.value = 'active'
      // Start timer if time limit exists
      if (props.current_topic.quiz.time_limit_minutes) {
        startTimer(props.current_topic.quiz.time_limit_minutes * 60)
      }
    } else {
      const errorData = await response.json()
      console.error('Server error:', errorData)
      throw new Error(errorData.error || 'Failed to start quiz')
    }
  } catch (error) {
    console.error('Failed to start quiz:', error)
    alert('Failed to start quiz: ' + error.message)
  } finally {
    quizLoading.value = false
  }
}

const startTimer = (duration) => {
  timeRemaining.value = duration
  timerInterval.value = setInterval(() => {
    timeRemaining.value--
    if (timeRemaining.value <= 0) {
      clearInterval(timerInterval.value)
      submitQuiz()
    }
  }, 1000)
}

const selectAnswer = (answer) => {
  selectedAnswer.value = answer
  userAnswers.value[currentQuestionIndex.value] = answer
}

const nextQuestion = () => {
  if (currentQuestionIndex.value < props.current_topic.quiz.questions.length - 1) {
    currentQuestionIndex.value++
    selectedAnswer.value = userAnswers.value[currentQuestionIndex.value] || null
  }
}

const previousQuestion = () => {
  if (currentQuestionIndex.value > 0) {
    currentQuestionIndex.value--
    selectedAnswer.value = userAnswers.value[currentQuestionIndex.value] || null
  }
}

const submitQuiz = async () => {
  if (timerInterval.value) {
    clearInterval(timerInterval.value);
  }
  quizLoading.value = true;
  try {
    console.log('Submitting quiz attempt:', {
      attemptId: currentQuizAttempt.value.id,
      answers: userAnswers.value,
      answersCount: userAnswers.value.filter(answer => answer !== null).length
    });
    const getCsrfToken = () => {
      const metaTag = document.querySelector('meta[name="csrf-token"]');
      return metaTag ? metaTag.getAttribute('content') : null;
    };
    const csrfToken = getCsrfToken();
    const headers = {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-Requested-With': 'XMLHttpRequest'
    };
    if (csrfToken) {
      headers['X-CSRF-TOKEN'] = csrfToken;
    }
    const response = await fetch(route('student.quizzes.submit', currentQuizAttempt.value.id), {
      method: 'POST',
      headers: headers,
      body: JSON.stringify({
        answers: userAnswers.value,
        _token: csrfToken
      })
    });
    console.log('Submit response status:', response.status);
    if (response.ok) {
      const data = await response.json();
      console.log('Quiz submitted successfully:', data);
      quizResults.value = data;
      quizState.value = 'results';
    } else {
      let errorData;
      try {
        errorData = await response.json();
      } catch (e) {
        errorData = { error: `Server error: ${response.status} ${response.statusText}` };
      }
      console.error('Server error response:', errorData);
      throw new Error(errorData.error || errorData.message || `Failed to submit quiz: ${response.status}`);
    }
  } catch (error) {
    console.error('Failed to submit quiz:', error);
    alert('Failed to submit quiz: ' + error.message);
  } finally {
    quizLoading.value = false;
  }
}

const retakeQuiz = () => {
  quizState.value = 'overview'
  quizResults.value = null
  currentQuizAttempt.value = null
}

const viewAttemptResults = (attempt) => {
  alert(`Viewing results for attempt ${attempt.attempt_number}. This would show detailed results.`)
}

const generateQuiz = async (topic) => {
  quizLoading.value = true
  try {
    await router.post(route('student.outlines.generate-quiz', {
      course: props.course.id,
      outline: topic.id
    }), {}, {
      onSuccess: () => {
        router.reload()
      },
      onError: (errors) => {
        console.error('Failed to generate quiz:', errors)
        alert('Failed to generate quiz. Please try again.')
      }
    })
  } catch (error) {
    console.error('Error generating quiz:', error)
    alert('An error occurred while generating quiz.')
  } finally {
    quizLoading.value = false
  }
}

// Project methods
const startProject = () => {
  projectState.value = 'active'
}

const cancelProject = () => {
  projectState.value = 'overview'
  projectSubmission.value = ''
  projectFile.value = null
}

const handleProjectFileUpload = (event) => {
  projectFile.value = event.target.files[0]
}

const submitProject = async () => {
  try {
    const formData = new FormData()
    formData.append('description', projectSubmission.value)
    if (projectFile.value) {
      formData.append('file', projectFile.value)
    }
    const response = await fetch(route('student.projects.submit', {
      course: props.course.id,
      outline: props.current_topic.id
    }), {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: formData
    })
    if (response.ok) {
      projectState.value = 'overview'
      projectSubmission.value = ''
      projectFile.value = null
      router.reload()
    } else {
      throw new Error('Failed to submit project')
    }
  } catch (error) {
    console.error('Error submitting project:', error)
    alert('Failed to submit project. Please try again.')
  }
}

const downloadProjectResources = () => {
  // Implementation for downloading project resources
  if (props.current_topic.project_resources) {
    // Trigger download logic here
    console.log('Downloading project resources')
  }
}

// Criteria methods
const generateCriteria = async (topic) => {
  criteriaLoading.value = true
  try {
    await router.post(route('student.outlines.generate-criteria', {
      course: props.course.id,
      outline: topic.id
    }), {}, {
      onSuccess: () => {
        router.reload()
      },
      onError: (errors) => {
        console.error('Failed to generate criteria:', errors)
        alert('Failed to generate criteria. Please try again.')
      }
    })
  } catch (error) {
    console.error('Error generating criteria:', error)
    alert('An error occurred while generating criteria.')
  } finally {
    criteriaLoading.value = false
  }
}

// Shuffle function
const shuffleArray = (array) => {
  const shuffled = [...array]
  for (let i = shuffled.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [shuffled[i], shuffled[j]] = [shuffled[j], shuffled[i]]
  }
  return shuffled
}

// Computed property for shuffled options
const shuffledOptions = computed(() => {
  if (!currentQuestion.value?.options) return []
  return shuffleArray(currentQuestion.value.options)
})

const hasAudioContent = computed(() => {
  return props.current_topic.contents?.length > 0 && hasAudioSupport.value
})

// Initialize speech synthesis
onMounted(() => {
  if (hasAudioSupport.value) {
    speechSynthesis.value = window.speechSynthesis
  }
})

// Clean up audio on unmount
onUnmounted(() => {
  stopAudioPlayback()
})

// Audio playback methods
const playContentAudio = (content) => {
  if (!hasAudioSupport.value) {
    alert('Your browser does not support text-to-speech functionality.')
    return
  }
  stopAudioPlayback() // Stop any current playback
  const text = extractTextFromHTML(formatContent(content.content))
  speakText(text)
}

const toggleAudioPlayback = () => {
  if (!hasAudioSupport.value) {
    alert('Your browser does not support text-to-speech functionality.')
    return
  }
  if (isPlaying.value) {
    pauseAudioPlayback()
  } else {
    playAllContentAudio()
  }
}

const playAllContentAudio = () => {
  if (!props.current_topic.contents?.length) return

  const allText = props.current_topic.contents
    .map(content => extractTextFromHTML(formatContent(content.content)))
    .join('\n')

  speakText(allText)
}

const speakText = (text) => {
  if (!text.trim()) return
  const utterance = new SpeechSynthesisUtterance(text)
  // Configure voice settings
  utterance.rate = 0.9 // Slightly slower than normal
  utterance.pitch = 1
  utterance.volume = 1
  // Get available voices and try to use a pleasant one
  const voices = speechSynthesis.value.getVoices()
  const preferredVoice = voices.find(voice =>
    voice.name.includes('Google') || voice.name.includes('Samantha') || voice.name.includes('Karen')
  )
  if (preferredVoice) {
    utterance.voice = preferredVoice
  }
  // Event listeners
  utterance.onstart = () => {
    isPlaying.value = true
    updateAudioProgress()
  }
  utterance.onend = () => {
    isPlaying.value = false
    audioProgressPercentage.value = 100
    audioProgress.value = audioDuration.value
  }
  utterance.onerror = (event) => {
    console.error('Speech synthesis error:', event)
    isPlaying.value = false
    alert('Error playing audio. Please try again.')
  }
  // Calculate estimated duration (rough estimate)
  const wordCount = text.split(/\s+/).length
  const estimatedSeconds = Math.ceil(wordCount / 3) // ~3 words per second
  audioDuration.value = formatTime(estimatedSeconds)
  currentAudio.value = utterance
  speechSynthesis.value.speak(utterance)
}

const pauseAudioPlayback = () => {
  if (speechSynthesis.value && isPlaying.value) {
    speechSynthesis.value.pause()
    isPlaying.value = false
  }
}

const resumeAudioPlayback = () => {
  if (speechSynthesis.value && currentAudio.value) {
    speechSynthesis.value.resume()
    isPlaying.value = true
  }
}

const stopAudioPlayback = () => {
  if (speechSynthesis.value) {
    speechSynthesis.value.cancel()
    isPlaying.value = false
    audioProgressPercentage.value = 0
    audioProgress.value = '0:00'
  }
}

const updateAudioProgress = () => {
  // This is a simplified progress update
  // In a real implementation, you might want more precise timing
  if (isPlaying.value) {
    const interval = setInterval(() => {
      if (!isPlaying.value) {
        clearInterval(interval)
        return
      }
      // Simulate progress (this is approximate)
      const current = audioProgressPercentage.value + 1
      if (current <= 100) {
        audioProgressPercentage.value = current
        audioProgress.value = formatTime(Math.floor(parseInt(audioDuration.value) * (current / 100)))
      } else {
        clearInterval(interval)
      }
    }, 1000)
  }
}

// Helper functions
const extractTextFromHTML = (html) => {
  const tempDiv = document.createElement('div')
  tempDiv.innerHTML = html
  return tempDiv.textContent || tempDiv.innerText || ''
}

const estimateReadTime = (content) => {
  const text = extractTextFromHTML(content)
  const wordCount = text.split(/\s+/).length
  const minutes = Math.ceil(wordCount / 200) // 200 words per minute
  return minutes
}

// Set default active tab based on available tabs
const setDefaultActiveTab = () => {
  const outlineType = props.current_topic.type || 'topic'
  const tabs = filteredTabs.value

  if (tabs.length > 0) {
    activeTab.value = tabs[0].id
  }
}

const canMarkAsComplete = computed(() => {
  const outlineType = props.current_topic.type || 'topic'

  if (outlineType === 'topic') {
    // For topics: require content to be generated and viewed
    return props.current_topic.contents?.length > 0
  }
  else if (outlineType === 'quiz') {
    // For quizzes: require a passing attempt
    const hasPassedAttempt = props.current_topic.quiz?.attempts?.some(attempt => attempt.is_passed)
    return hasPassedAttempt || false
  }
  else if (outlineType === 'project') {
    // For projects: require at least one approved submission
    const hasApprovedSubmission = props.current_topic.project_submissions?.some(submission => submission.status === 'approved')
    return hasApprovedSubmission || props.current_topic.project_submissions?.length > 0
  }

  return false
})

// Computed property to get reason why marking as complete is disabled
const getDisabledReason = computed(() => {
  const outlineType = props.current_topic.type || 'topic'
  
  if (outlineType === 'topic') {
    return 'Complete the content to mark as complete'
  } 
  else if (outlineType === 'quiz') {
    return 'Pass the quiz to mark as complete'
  } 
  else if (outlineType === 'project') {
    return 'Submit the project to mark as complete'
  }
  
  return 'Complete the requirements to mark as complete'
})

// Initialize expanded modules - expand current module
onMounted(() => {
  if (props.current_topic?.module_id) {
    expandedModules.value[props.current_topic.module_id] = true
  }
  // Set default active tab based on available tabs
  setDefaultActiveTab()
  // Start time tracking
  startTimeTracking()
})

// Watch for topic changes and update active tab
watch(() => props.current_topic, () => {
  setDefaultActiveTab()
}, { immediate: true })



</script>

<style scoped>
.prose {
  max-width: none;
}
.prose h1 {
  @apply text-2xl font-bold text-gray-900 mb-4;
}
.prose h2 {
  @apply text-xl font-semibold text-gray-900 mb-3 mt-6;
}
.prose h3 {
  @apply text-lg font-medium text-gray-900 mb-2 mt-4;
}
.prose p {
  @apply text-gray-700 leading-relaxed mb-4;
}
.prose ul, .prose ol {
  @apply text-gray-700 mb-4;
}
.prose li {
  @apply mb-1;
}
.prose code {
  @apply bg-gray-100 text-gray-800 px-1 py-0.5 rounded text-sm;
}
.prose pre {
  @apply bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto mb-4;
}
.prose blockquote {
  @apply border-l-4 border-emerald-500 pl-4 italic text-gray-600 my-4;
}
/* Audio player animations */
@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.7; }
}
.playing-animation {
  animation: pulse 2s infinite;
}
</style>
