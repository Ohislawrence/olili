<template>
  <AdminLayout>
    <Head :title="`Course Outline - ${course.title}`" />

    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex justify-between items-start">
            <div>
              <div class="flex items-center space-x-3">
                <Link
                  :href="route('admin.courses.show', course.id)"
                  class="text-gray-400 hover:text-gray-600"
                >
                  <ArrowLeftIcon class="h-5 w-5" />
                </Link>
                <div>
                  <h1 class="text-2xl font-bold text-gray-900">Course Outline</h1>
                  <div class="mt-2 flex items-center space-x-4">
                    <p class="text-sm text-gray-600">
                      {{ course.title }}
                    </p>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                      :class="getStatusClass(course.status)">
                      {{ course.status }}
                    </span>
                    <span v-if="course.code" class="text-xs font-mono bg-gray-100 px-2 py-1 rounded">
                      {{ course.code }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
            <div class="flex space-x-3">
              <button
                @click="exportOutline"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
              >
                <DocumentArrowDownIcon class="h-4 w-4 mr-2" />
                Export PDF
              </button>

              <button
                v-if="hasPendingContent"
                @click="generateAllContent"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700"
              >
                <BoltIcon class="h-4 w-4 mr-2" />
                Generate All Content
              </button>

              <button
                @click="regenerateOutline"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
              >
                <ArrowPathIcon class="h-4 w-4 mr-2" />
                Regenerate Outline
              </button>

              <Link
                :href="route('admin.courses.edit', course.id)"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
              >
                <PencilIcon class="h-4 w-4 mr-2" />
                Edit Course
              </Link>
            </div>
          </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <FolderIcon class="h-6 w-6 text-gray-400" />
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">
                      Total Modules
                    </dt>
                    <dd class="text-lg font-semibold text-gray-900">
                      {{ stats.total_modules }}
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <DocumentTextIcon class="h-6 w-6 text-gray-400" />
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">
                      Total Topics
                    </dt>
                    <dd class="text-lg font-semibold text-gray-900">
                      {{ stats.total_topics }}
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <ClockIcon class="h-6 w-6 text-gray-400" />
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">
                      Total Duration
                    </dt>
                    <dd class="text-lg font-semibold text-gray-900">
                      {{ stats.total_duration_hours }} hours
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <QuestionMarkCircleIcon class="h-6 w-6 text-gray-400" />
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">
                      Total Quizzes
                    </dt>
                    <dd class="text-lg font-semibold text-gray-900">
                      {{ stats.total_quizzes }}
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Content Type Distribution -->
        <div v-if="Object.keys(contentByType).length > 0" class="mb-8">
          <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
              <h3 class="text-lg font-semibold text-gray-900">Content Distribution</h3>
            </div>
            <div class="p-6">
              <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div
                  v-for="(count, type) in contentByType"
                  :key="type"
                  class="text-center p-4 border border-gray-200 rounded-lg"
                >
                  <div class="text-2xl font-bold text-gray-900">{{ count }}</div>
                  <div class="text-sm text-gray-500 capitalize">{{ type }} Content</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Capstone Project Section -->
        <div v-if="hasCapstoneProject" class="bg-white shadow rounded-lg p-6 mb-6">
          <div class="flex justify-between items-center mb-4">
            <div>
              <h3 class="text-lg font-semibold text-gray-900">Capstone Project</h3>
              <p class="text-sm text-gray-600 mt-1" v-if="capstoneProject">
                {{ capstoneProject.description }}
              </p>
            </div>
            <button
              @click="generateCapstoneProject"
              :disabled="isGeneratingCapstone"
              class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <AcademicCapIcon class="h-4 w-4 mr-2" />
              <span v-if="isGeneratingCapstone">Generating...</span>
              <span v-else>Generate Project</span>
            </button>
          </div>
          <!-- Display capstone project requirements if generated -->
          <div v-if="capstoneProject && capstoneProject.requirements" class="mt-4">
            <div class="text-sm font-medium text-gray-700 mb-2">Requirements:</div>
            <div class="flex flex-wrap gap-2">
              <span
                v-for="(requirement, index) in capstoneProject.requirements"
                :key="index"
                class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-indigo-100 text-indigo-800"
              >
                {{ requirement }}
              </span>
            </div>
          </div>
        </div>

        <!-- Course Modules -->
        <div class="space-y-6">
          <div
            v-for="(module, moduleIndex) in course.modules"
            :key="module.id"
            class="bg-white shadow rounded-lg"
          >
            <!-- Module Header -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
              <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                  <div class="flex-shrink-0">
                    <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                      <span class="text-sm font-semibold text-blue-600">
                        {{ moduleIndex + 1 }}
                      </span>
                    </div>
                  </div>
                  <div>
                    <h3 class="text-lg font-semibold text-gray-900">
                      {{ module.title }}
                    </h3>
                    <div class="flex items-center space-x-2 mt-1">
                      <span class="text-sm text-gray-500">
                        {{ module.topics.length }} topics
                      </span>
                      <span class="text-sm text-gray-500">•</span>
                      <span class="text-sm text-gray-500">
                        {{ formatDuration(module.estimated_duration_minutes) }}
                      </span>
                      <span v-if="module.needs_content_generation" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                        Needs Content
                      </span>
                    </div>
                  </div>
                </div>
                <div class="flex space-x-2">
                  <Link
                    :href="route('admin.courses.modules.edit', { course: course.id, module: module.id })"
                    class="text-gray-400 hover:text-gray-600"
                  >
                    <PencilIcon class="h-4 w-4" />
                  </Link>
                  <Link
                    :href="route('admin.courses.modules.topics.create', { course: course.id, module: module.id })"
                    class="text-gray-400 hover:text-gray-600"
                  >
                    <PlusCircleIcon class="h-4 w-4" />
                  </Link>
                </div>
              </div>
              <div v-if="module.description" class="mt-2 text-sm text-gray-600">
                {{ module.description }}
              </div>
              <div v-if="module.learning_objectives && module.learning_objectives.length > 0" class="mt-2">
                <div class="text-sm font-medium text-gray-700">Learning Objectives:</div>
                <div class="mt-1 flex flex-wrap gap-1">
                  <span
                    v-for="(objective, index) in module.learning_objectives"
                    :key="index"
                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800"
                  >
                    {{ objective }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Module Topics -->
            <div class="divide-y divide-gray-200">
              <div
                v-for="(topic, topicIndex) in module.topics"
                :key="topic.id"
                class="p-6 hover:bg-gray-50"
              >
                <div class="flex justify-between items-start">
                  <div class="flex-1">
                    <div class="flex items-center space-x-3">
                      <div class="flex-shrink-0">
                        <div class="h-6 w-6 rounded-full bg-gray-100 flex items-center justify-center">
                          <span class="text-xs font-medium text-gray-600">
                            {{ topicIndex + 1 }}
                          </span>
                        </div>
                      </div>
                      <div class="flex-1">
                        <h4 class="text-md font-medium text-gray-900">
                          {{ topic.title }}
                        </h4>
                        <div class="mt-1 flex items-center space-x-4">
                          <span class="text-sm text-gray-500">
                            {{ formatDuration(topic.estimated_duration_minutes) }}
                          </span>
                          <span v-if="topic.type" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                            {{ topic.type }}
                          </span>
                          <span v-if="topic.has_quiz && topic.quiz" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                            <QuestionMarkCircleIcon class="h-3 w-3 mr-1" />
                            Quiz ({{ topic.quiz.question_count }} questions)
                          </span>
                          <span v-if="topic.needs_content_generation" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                            <BoltIcon class="h-3 w-3 mr-1" />
                            Needs Content
                          </span>
                          <span v-if="topic.needs_quiz_generation && topic.has_quiz" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800">
                            <QuestionMarkCircleIcon class="h-3 w-3 mr-1" />
                            Needs Quiz
                          </span>
                        </div>
                        <div v-if="topic.description" class="mt-2 text-sm text-gray-600">
                          {{ topic.description }}
                        </div>

                        <!-- Quiz Generation Button -->
                        <div v-if="topic.has_quiz" class="mt-2">
                          <button
                            v-if="topic.needs_quiz_generation || !topic.quiz"
                            @click="generateQuiz(topic)"
                            :disabled="isGeneratingQuiz === topic.id"
                            class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed"
                          >
                            <QuestionMarkCircleIcon class="h-3 w-3 mr-1" />
                            <span v-if="isGeneratingQuiz === topic.id">Generating...</span>
                            <span v-else>Generate Quiz</span>
                          </button>
                          <span v-else class="text-xs text-green-600">
                            ✓ Quiz Generated
                          </span>
                        </div>

                        <!-- Topic Content Items -->
                        <div v-if="topic.contents && topic.contents.length > 0" class="mt-4">
                          <div class="space-y-3">
                            <div
                              v-for="content in topic.contents"
                              :key="content.id"
                              class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
                            >
                              <div class="flex items-center space-x-3">
                                <div
                                  class="h-8 w-8 rounded-full flex items-center justify-center"
                                  :class="getContentTypeClass(content.content_type)"
                                >
                                  <component
                                    :is="getContentTypeIcon(content.content_type)"
                                    class="h-4 w-4 text-white"
                                  />
                                </div>
                                <div>
                                  <div class="text-sm font-medium text-gray-900 capitalize">
                                    {{ content.content_type }} Content
                                  </div>
                                  <div class="text-xs text-gray-500">
                                    {{ content.created_at ? formatDate(content.created_at) : 'Recently generated' }}
                                  </div>
                                </div>
                              </div>
                              <div class="flex items-center space-x-3">
                                <div class="text-right">
                                  <div class="text-xs text-gray-500">
                                    {{ getContentReadTime(content) }}
                                  </div>
                                </div>
                                <button
                                  @click="previewContent(content)"
                                  class="text-sm text-blue-600 hover:text-blue-800"
                                >
                                  Preview
                                </button>
                              </div>
                            </div>
                          </div>
                        </div>

                        <!-- No Content Message -->
                        <div v-else-if="topic.needs_content_generation" class="mt-4">
                          <div class="flex items-center justify-between p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex items-center">
                              <ExclamationTriangleIcon class="h-5 w-5 text-yellow-400 mr-2" />
                              <span class="text-sm text-yellow-700">
                                No content generated yet
                              </span>
                            </div>
                            <button
                              @click="generateContent(topic)"
                              :disabled="isGeneratingContent === topic.id"
                              class="text-sm text-yellow-700 hover:text-yellow-900 font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                              <span v-if="isGeneratingContent === topic.id">Generating...</span>
                              <span v-else>Generate Now</span>
                            </button>
                          </div>
                        </div>

                        <!-- No Content Message for general quiz -->
                        <div v-else-if="!topic.needs_content_generation && topic.type === 'quiz' && !topic.quiz" class="mt-4">
                          <div class="flex items-center justify-between p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex items-center">
                              <ExclamationTriangleIcon class="h-5 w-5 text-yellow-400 mr-2" />
                              <span class="text-sm text-yellow-700">
                                No content generated yet for general quiz
                              </span>
                            </div>
                            <button
                              @click="generateContentForGeneralQuiz(topic)"
                              :disabled="isGeneratingContent === topic.id"
                              class="text-sm text-yellow-700 hover:text-yellow-900 font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                              <span v-if="isGeneratingContent === topic.id">Generating...</span>
                              <span v-else>Generate General Quiz</span>
                            </button>
                          </div>
                        </div>

                        <!-- General quiz Generated Indicator -->
                        <div v-else-if="topic.quiz && topic.type === 'quiz'" class="mt-4">
                          <div class="flex items-center p-3 bg-green-50 border border-green-200 rounded-lg">
                            <CheckCircleIcon class="h-5 w-5 text-green-400 mr-2" />
                            <span class="text-sm text-green-700">
                              Content generated on {{ formatDate(topic.quiz.updated_at) }}
                            </span>
                          </div>
                        </div>

                        <!-- Content Generated Indicator -->
                        <div v-else-if="topic.content_generated_at" class="mt-4">
                          <div class="flex items-center p-3 bg-green-50 border border-green-200 rounded-lg">
                            <CheckCircleIcon class="h-5 w-5 text-green-400 mr-2" />
                            <span class="text-sm text-green-700">
                              Content generated on {{ formatDate(topic.content_generated_at) }}
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="ml-4 flex space-x-2">
                    <Link
                      :href="route('admin.courses.modules.topics.edit', { course: course.id, module: module.id, topic: topic.id })"
                      class="text-gray-400 hover:text-gray-600"
                    >
                      <PencilIcon class="h-4 w-4" />
                    </Link>
                    <Link
                      v-if="topic.has_quiz && !topic.quiz"
                      :href="route('admin.quizzes.create', { course: course.id, module: module.id, topic: topic.id })"
                      class="text-purple-400 hover:text-purple-600"
                    >
                      <PlusCircleIcon class="h-4 w-4" />
                    </Link>
                  </div>
                </div>

                <!-- Key Concepts & Resources -->
                <div v-if="topic.key_concepts && topic.key_concepts.length > 0" class="mt-4">
                  <div class="text-sm font-medium text-gray-700 mb-2">Key Concepts:</div>
                  <div class="flex flex-wrap gap-2">
                    <span
                      v-for="(concept, index) in topic.key_concepts"
                      :key="index"
                      class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800"
                    >
                      {{ concept }}
                    </span>
                  </div>
                </div>

                <div v-if="topic.resources && topic.resources.length > 0" class="mt-4">
                  <div class="text-sm font-medium text-gray-700 mb-2">Resources:</div>
                  <div class="flex flex-wrap gap-2">
                    <span
                      v-for="(resource, index) in topic.resources"
                      :key="index"
                      class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800"
                    >
                      {{ resource }}
                    </span>
                  </div>
                </div>

                <div v-if="topic.learning_objectives && topic.learning_objectives.length > 0" class="mt-4">
                  <div class="text-sm font-medium text-gray-700 mb-2">Learning Objectives:</div>
                  <div class="flex flex-wrap gap-2">
                    <span
                      v-for="(objective, index) in topic.learning_objectives"
                      :key="index"
                      class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-indigo-100 text-indigo-800"
                    >
                      {{ objective }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="course.modules.length === 0" class="text-center py-12">
          <FolderOpenIcon class="mx-auto h-12 w-12 text-gray-400" />
          <h3 class="mt-2 text-sm font-medium text-gray-900">No modules yet</h3>
          <p class="mt-1 text-sm text-gray-500">
            Start by creating the first module for this course.
          </p>
          <div class="mt-6">
            <Link
              :href="route('admin.courses.modules.create', course.id)"
              class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
            >
              <PlusIcon class="h-4 w-4 mr-2" />
              Create First Module
            </Link>
          </div>
        </div>

        <!-- Content Preview Modal -->
        <TransitionRoot as="template" :show="showContentPreview">
          <Dialog as="div" class="relative z-10" @close="showContentPreview = false">
            <TransitionChild
              as="template"
              enter="ease-out duration-300"
              enter-from="opacity-0"
              enter-to="opacity-100"
              leave="ease-in duration-200"
              leave-from="opacity-100"
              leave-to="opacity-0"
            >
              <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
            </TransitionChild>

            <div class="fixed inset-0 z-10 overflow-y-auto">
              <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <TransitionChild
                  as="template"
                  enter="ease-out duration-300"
                  enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                  enter-to="opacity-100 translate-y-0 sm:scale-100"
                  leave="ease-in duration-200"
                  leave-from="opacity-100 translate-y-0 sm:scale-100"
                  leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                >
                  <DialogPanel class="relative transform overflow-hidden rounded-lg bg-white px-4 pt-5 pb-4 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-4xl sm:p-6">
                    <div>
                      <div class="flex justify-between items-center mb-4">
                        <DialogTitle as="h3" class="text-lg font-medium text-gray-900">
                          Content Preview: {{ previewContentData?.content_type }}
                        </DialogTitle>
                        <button
                          type="button"
                          class="text-gray-400 hover:text-gray-500"
                          @click="showContentPreview = false"
                        >
                          <XMarkIcon class="h-6 w-6" />
                        </button>
                      </div>
                      <div class="mt-4">
                        <div v-if="previewContentData" class="prose max-w-none">
                          <div v-html="formatContent(previewContentData.content)" class="p-4 bg-gray-50 rounded-lg max-h-96 overflow-y-auto"></div>
                        </div>
                      </div>
                    </div>
                    <div class="mt-5 sm:mt-6">
                      <button
                        type="button"
                        class="inline-flex justify-center w-full rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:text-sm"
                        @click="showContentPreview = false"
                      >
                        Close Preview
                      </button>
                    </div>
                  </DialogPanel>
                </TransitionChild>
              </div>
            </div>
          </Dialog>
        </TransitionRoot>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import {
  Dialog,
  DialogPanel,
  DialogTitle,
  TransitionChild,
  TransitionRoot,
} from '@headlessui/vue'
import {
  ArrowLeftIcon,
  FolderIcon,
  DocumentTextIcon,
  ClockIcon,
  QuestionMarkCircleIcon,
  PencilIcon,
  PlusCircleIcon,
  PlusIcon,
  DocumentArrowDownIcon,
  ExclamationTriangleIcon,
  XMarkIcon,
  FolderOpenIcon,
  BookOpenIcon,
  VideoCameraIcon,
  PuzzlePieceIcon,
  AcademicCapIcon,
  BoltIcon, ArrowPathIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  course: Object,
  stats: Object,
  contentByType: Object,
})

const showContentPreview = ref(false)
const previewContentData = ref(null)

const hasPendingContent = computed(() => {
  return props.course.modules?.some(module =>
    module.topics?.some(topic => topic.needs_content_generation)
  ) || false;
})

const hasCapstoneProject = computed(() => {
  return props.course.modules?.some(module =>
    module.topics?.some(topic => topic.has_project)
  ) || false;
})
// Helper functions
const getStatusClass = (status) => {
  const classes = {
    draft: 'bg-gray-100 text-gray-800',
    active: 'bg-green-100 text-green-800',
    archived: 'bg-red-100 text-red-800',
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const formatDuration = (minutes) => {
  if (!minutes) return '0 min'
  if (minutes < 60) return `${minutes} min`
  const hours = Math.floor(minutes / 60)
  const mins = minutes % 60
  if (mins === 0) return `${hours} hr`
  return `${hours} hr ${mins} min`
}

const formatDate = (dateString) => {
  if (!dateString) return ''
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
  })
}


const getContentTypeIcon = (type) => {
  const icons = {
    text: BookOpenIcon,
    lesson: BookOpenIcon,
    video: VideoCameraIcon,
    interactive: PuzzlePieceIcon,
    exercise: AcademicCapIcon,
    quiz: QuestionMarkCircleIcon,
    default: DocumentTextIcon,
  }
  return icons[type] || icons.default
}

const getContentTypeClass = (type) => {
  const classes = {
    text: 'bg-blue-500',
    lesson: 'bg-blue-500',
    video: 'bg-red-500',
    interactive: 'bg-purple-500',
    exercise: 'bg-green-500',
    quiz: 'bg-yellow-500',
    default: 'bg-gray-500',
  }
  return classes[type] || classes.default
}

const getContentReadTime = (content) => {
  if (content.content_type === 'text') {
    const wordCount = content.content ? content.content.split(/\s+/).length : 0
    const minutes = Math.ceil(wordCount / 200)
    return `${minutes} min read`
  } else if (content.content_type === 'video') {
    return 'Video content'
  }
  return 'Interactive content'
}

const formatContent = (content) => {
  if (!content) return ''
  // Convert markdown to HTML (you might want to use a proper markdown parser)
  let html = content
    .replace(/\n/g, '<br>')
    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
    .replace(/\*(.*?)\*/g, '<em>$1</em>')
    .replace(/`(.*?)`/g, '<code>$1</code>')
    .replace(/^# (.*?)$/gm, '<h1>$1</h1>')
    .replace(/^## (.*?)$/gm, '<h2>$1</h2>')
    .replace(/^### (.*?)$/gm, '<h3>$1</h3>')
    .replace(/^- (.*?)$/gm, '<li>$1</li>')
  return html
}

// Actions
const previewContent = (content) => {
  previewContentData.value = content
  showContentPreview.value = true
}

const generateContent = (topic) => {
  if (confirm(`Generate content for "${topic.title}"?`)) {
    router.post(route('admin.courses.modules.topics.content.generate', {
      course: props.course.id,
      module: topic.module_id,
      topic: topic.id
    }), {}, {
      preserveScroll: true,
      onSuccess: (response) => {
        if (response.props.flash.success) {
          // Show success message and reload
          router.reload();
        }
      },
      onError: (errors) => {
        alert('Failed to generate content: ' + (errors.message || 'Unknown error'));
      }
    })
  }
}

const generateContentForGeneralQuiz = (topic) => {
  if (confirm(`Generate content for "${topic.title}"?`)) {
    router.post(route('admin.courses.modules.topics.content.general.quiz', {
      course: props.course.id,
      module: topic.module_id,
      topic: topic.id
    }), {}, {
      preserveScroll: true,
      onSuccess: (response) => {
        if (response.props.flash.success) {
          // Show success message and reload
          router.reload();
        }
      },
      onError: (errors) => {
        alert('Failed to generate content: ' + (errors.message || 'Unknown error'));
      }
    })
  }
}

// Add new function for generating quizzes
const generateQuiz = (topic) => {
  if (confirm(`Generate quiz for "${topic.title}"?`)) {
    router.post(route('admin.courses.modules.topics.quiz.generate', {
      course: props.course.id,
      module: topic.module_id,
      topic: topic.id
    }), {}, {
      preserveScroll: true,
      onSuccess: (response) => {
        if (response.props.flash.success) {
          router.reload();
        }
      },
      onError: (errors) => {
        alert('Failed to generate quiz: ' + (errors.message || 'Unknown error'));
      }
    })
  }
}

// Add new function for generating capstone project
const generateCapstoneProject = () => {
  if (confirm('Generate capstone project for this course?')) {
    router.post(route('admin.courses.capstone.generate', {
      course: props.course.id
    }), {}, {
      preserveScroll: true,
      onSuccess: (response) => {
        if (response.props.flash.success) {
          router.reload();
        }
      }
    })
  }
}

// Add new function for bulk content generation
const generateAllContent = () => {
  if (confirm('Generate all pending content for this course? This may take a while.')) {
    router.post(route('admin.courses.content.generate-all', {
      course: props.course.id
    }), {}, {
      preserveScroll: true,
      onSuccess: (response) => {
        if (response.props.flash.success) {
          router.reload();
        }
      }
    })
  }
}

// Add new function for regenerating outline
const regenerateOutline = () => {
  if (confirm('Regenerate the entire course outline? This will recreate all modules and topics.')) {
    router.post(route('admin.courses.outline.regenerate', {
      course: props.course.id
    }), {}, {
      preserveScroll: true,
      onSuccess: (response) => {
        if (response.props.flash.success) {
          router.reload();
        }
      }
    })
  }
}

const exportOutline = () => {
  // This would typically call a backend endpoint to generate PDF
  window.open(route('admin.courses.outline.export', props.course.id), '_blank')
}
</script>

<style scoped>
.prose {
  color: #374151;
}
.prose h1 {
  font-size: 1.5em;
  font-weight: 600;
  margin-top: 1em;
  margin-bottom: 0.5em;
}
.prose h2 {
  font-size: 1.25em;
  font-weight: 600;
  margin-top: 1em;
  margin-bottom: 0.5em;
}
.prose h3 {
  font-size: 1.125em;
  font-weight: 600;
  margin-top: 1em;
  margin-bottom: 0.5em;
}
.prose strong {
  font-weight: 600;
}
.prose em {
  font-style: italic;
}
.prose code {
  font-family: 'Courier New', monospace;
  background-color: #f3f4f6;
  padding: 0.125rem 0.25rem;
  border-radius: 0.25rem;
}
.prose li {
  margin-left: 1.5em;
  list-style-type: disc;
}
</style>
