<!-- resources/js/Pages/Admin/Courses/Edit.vue -->
<template>
  <AdminLayout>
    <Head :title="`Edit Course - ${course.title}`" />

    <div class="py-6">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex justify-between items-center">
            <div>
              <h1 class="text-3xl font-bold text-gray-900">Edit Course</h1>
              <p class="mt-2 text-gray-600">
                Update course information and settings
              </p>
            </div>
            <Link
              :href="route('admin.courses.show', course.id)"
              class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
            >
              Back to Course
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
                      <option value="mathematics">Mathematics</option>
                      <option value="science">Science</option>
                      <option value="english">English</option>
                      <option value="history">History</option>
                      <option value="geography">Geography</option>
                      <option value="computer_science">Computer Science</option>
                    </select>
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
                      <option value="beginner">Beginner</option>
                      <option value="intermediate">Intermediate</option>
                      <option value="advanced">Advanced</option>
                      <option value="expert">Expert</option>
                    </select>
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
                  </div>
                </div>
              </div>

              <!-- Status -->
              <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Status</h3>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                  <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">
                      Course Status
                    </label>
                    <select
                      id="status"
                      v-model="form.status"
                      class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                    >
                      <option value="draft">Draft</option>
                      <option value="active">Active</option>
                      <option value="completed">Completed</option>
                      <option value="archived">Archived</option>
                    </select>
                  </div>

                  <div v-if="form.status === 'completed'" class="flex items-end">
                    <div class="flex-1">
                      <label for="actual_completion_date" class="block text-sm font-medium text-gray-700">
                        Actual Completion Date
                      </label>
                      <input
                        id="actual_completion_date"
                        v-model="form.actual_completion_date"
                        type="date"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                      />
                    </div>
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
            </div>

            <!-- Form Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
              <Link
                :href="route('admin.courses.show', course.id)"
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
                  Update Course
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
import { PlusIcon, ArrowPathIcon, TrashIcon } from '@heroicons/vue/24/outline'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  course: Object,
  errors: Object,
})

const processing = ref(false)

const form = useForm({
  title: props.course.title,
  subject: props.course.subject,
  description: props.course.description || '',
  level: props.course.level,
  start_date: props.course.start_date?.split('T')[0] || new Date().toISOString().split('T')[0],
  target_completion_date: props.course.target_completion_date?.split('T')[0] || '',
  actual_completion_date: props.course.actual_completion_date?.split('T')[0] || '',
  status: props.course.status,
  learning_objectives: props.course.learning_objectives?.length ? [...props.course.learning_objectives] : [''],
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

  form.put(route('admin.courses.update', props.course.id), {
    onFinish: () => {
      processing.value = false
    },
  })
}
</script>
