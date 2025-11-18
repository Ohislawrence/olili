<!-- resources/js/Pages/Admin/Courses/Create.vue -->
<template>
  <AdminLayout>
    <Head title="Create Course" />

    <div class="py-6">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex justify-between items-center">
            <div>
              <h1 class="text-3xl font-bold text-gray-900">Create New Course</h1>
              <p class="mt-2 text-gray-600">
                Create a new AI-generated course for a student
              </p>
            </div>
            <Link
              :href="route('admin.courses.index')"
              class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
            >
              Back to Courses
            </Link>
          </div>
        </div>

        <!-- Form -->
        <div class="bg-white shadow rounded-lg">
          <form @submit.prevent="submit">
            <div class="p-6 space-y-6">
              <!-- Course Information -->
              <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Course Information</h3>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                  <div>
                    <label for="student_profile_id" class="block text-sm font-medium text-gray-700">
                      Student *
                    </label>
                    <select
                      id="student_profile_id"
                      v-model="form.student_profile_id"
                      required
                      class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                    >
                      <option value="">Select a student</option>
                      <option
                        v-for="student in students"
                        :key="student.id"
                        :value="student.id"
                      >
                        {{ student.name }} - {{ student.email }}
                      </option>
                    </select>
                    <p v-if="errors.student_profile_id" class="mt-1 text-sm text-red-600">
                      {{ errors.student_profile_id }}
                    </p>
                  </div>

                  <div>
                    <label for="exam_board_id" class="block text-sm font-medium text-gray-700">
                      Exam Board
                    </label>
                    <select
                      id="exam_board_id"
                      v-model="form.exam_board_id"
                      class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                    >
                      <option value="">Select an exam board</option>
                      <option
                        v-for="board in exam_boards"
                        :key="board.id"
                        :value="board.id"
                      >
                        {{ board.name }}
                      </option>
                    </select>
                  </div>

                  <div class="sm:col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700">
                      Course Title *
                    </label>
                    <input
                      id="title"
                      v-model="form.title"
                      type="text"
                      required
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                      :class="{ 'border-red-300': errors.title }"
                    />
                    <p v-if="errors.title" class="mt-1 text-sm text-red-600">
                      {{ errors.title }}
                    </p>
                  </div>

                  <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700">
                      Subject *
                    </label>
                    <select
                      id="subject"
                      v-model="form.subject"
                      required
                      class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                    >
                      <option value="">Select subject</option>
                      <option value="mathematics">Mathematics</option>
                      <option value="science">Science</option>
                      <option value="english">English</option>
                      <option value="history">History</option>
                      <option value="geography">Geography</option>
                      <option value="computer_science">Computer Science</option>
                    </select>
                    <p v-if="errors.subject" class="mt-1 text-sm text-red-600">
                      {{ errors.subject }}
                    </p>
                  </div>

                  <div>
                    <label for="level" class="block text-sm font-medium text-gray-700">
                      Level *
                    </label>
                    <select
                      id="level"
                      v-model="form.level"
                      required
                      class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                    >
                      <option value="">Select level</option>
                      <option value="beginner">Beginner</option>
                      <option value="intermediate">Intermediate</option>
                      <option value="advanced">Advanced</option>
                      <option value="expert">Expert</option>
                    </select>
                    <p v-if="errors.level" class="mt-1 text-sm text-red-600">
                      {{ errors.level }}
                    </p>
                  </div>

                  <div class="sm:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700">
                      Description
                    </label>
                    <textarea
                      id="description"
                      v-model="form.description"
                      rows="3"
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                      placeholder="Brief description of the course..."
                    ></textarea>
                  </div>
                </div>
              </div>

              <!-- Timeline -->
              <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Timeline</h3>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                  <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700">
                      Start Date
                    </label>
                    <input
                      id="start_date"
                      v-model="form.start_date"
                      type="date"
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    />
                  </div>

                  <div>
                    <label for="target_completion_date" class="block text-sm font-medium text-gray-700">
                      Target Completion Date *
                    </label>
                    <input
                      id="target_completion_date"
                      v-model="form.target_completion_date"
                      type="date"
                      required
                      :min="minDate"
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    />
                    <p v-if="errors.target_completion_date" class="mt-1 text-sm text-red-600">
                      {{ errors.target_completion_date }}
                    </p>
                  </div>
                </div>
              </div>

              <!-- Learning Objectives -->
              <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Learning Objectives</h3>
                <div class="space-y-3">
                  <div
                    v-for="(objective, index) in form.learning_objectives"
                    :key="index"
                    class="flex items-center space-x-3"
                  >
                    <input
                      v-model="form.learning_objectives[index]"
                      type="text"
                      class="flex-1 border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                      placeholder="Enter learning objective..."
                    />
                    <button
                      type="button"
                      @click="removeObjective(index)"
                      class="text-red-600 hover:text-red-800"
                    >
                      <TrashIcon class="h-5 w-5" />
                    </button>
                  </div>
                  <button
                    type="button"
                    @click="addObjective"
                    class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                  >
                    <PlusIcon class="h-4 w-4 mr-2" />
                    Add Objective
                  </button>
                </div>
              </div>

              <!-- AI Generation Options -->
              <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center">
                  <div class="flex-shrink-0">
                    <SparklesIcon class="h-5 w-5 text-blue-400" />
                  </div>
                  <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">
                      AI Course Generation
                    </h3>
                    <div class="mt-2 text-sm text-blue-700">
                      <p>This course will be automatically generated using AI based on the student's profile and learning goals.</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Form Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
              <Link
                :href="route('admin.courses.index')"
                class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                Cancel
              </Link>
              <button
                type="submit"
                :disabled="processing"
                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
              >
                <span v-if="processing">
                  <ArrowPathIcon class="h-4 w-4 animate-spin" />
                </span>
                <span v-else>
                  Generate Course
                </span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import { PlusIcon, ArrowPathIcon, TrashIcon, SparklesIcon } from '@heroicons/vue/24/outline'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  students: Array,
  exam_boards: Array,
  errors: Object,
})

const processing = ref(false)

const form = useForm({
  student_profile_id: '',
  exam_board_id: '',
  title: '',
  subject: '',
  description: '',
  level: '',
  start_date: new Date().toISOString().split('T')[0],
  target_completion_date: '',
  learning_objectives: [''],
})

const minDate = computed(() => {
  return new Date().toISOString().split('T')[0]
})

const addObjective = () => {
  form.learning_objectives.push('')
}

const removeObjective = (index) => {
  form.learning_objectives.splice(index, 1)
}

const submit = () => {
  processing.value = true

  // Filter out empty objectives
  form.learning_objectives = form.learning_objectives.filter(obj => obj.trim() !== '')

  form.post(route('admin.courses.store'), {
    onFinish: () => {
      processing.value = false
    },
  })
}
</script>
