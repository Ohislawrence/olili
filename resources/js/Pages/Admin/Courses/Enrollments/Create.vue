<!-- resources/js/Pages/Admin/Courses/Enrollments/Create.vue -->
<template>
  <AdminLayout>
    <Head :title="`Add Enrollment - ${course.title}`" />

    <div class="py-6">
      <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex items-center space-x-3">
            <Link
              :href="route('admin.courses.enrollments.index', course.id)"
              class="text-gray-400 hover:text-gray-600"
            >
              <ArrowLeftIcon class="h-5 w-5" />
            </Link>
            <div>
              <h1 class="text-2xl font-bold text-gray-900">Add Enrollment</h1>
              <p class="mt-1 text-sm text-gray-600">
                {{ course.title }}
              </p>
            </div>
          </div>
        </div>

        <!-- Form -->
        <div class="bg-white shadow rounded-lg">
          <form @submit.prevent="submit">
            <div class="px-6 py-4 border-b border-gray-200">
              <h3 class="text-lg font-semibold text-gray-900">Enrollment Details</h3>
            </div>
            <div class="p-6">
              <!-- Student Selection -->
              <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Select Student *
                </label>
                <select
                  v-model="form.user_id"
                  required
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3"
                  :class="{ 'border-red-300': errors.user_id }"
                >
                  <option value="">Choose a student...</option>
                  <option
                    v-for="student in availableStudents"
                    :key="student.id"
                    :value="student.id"
                  >
                    {{ student.name }} ({{ student.email }})
                    <template v-if="student.student_profile">
                      • Level: {{ student.student_profile.current_level }}
                    </template>
                  </option>
                </select>
                <p v-if="errors.user_id" class="mt-1 text-sm text-red-600">
                  {{ errors.user_id }}
                </p>
              </div>

              <!-- Status Selection -->
              <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Enrollment Status *
                </label>
                <select
                  v-model="form.status"
                  required
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3"
                  :class="{ 'border-red-300': errors.status }"
                >
                  <option value="enrolled">Enrolled (Not started)</option>
                  <option value="active">Active (Started learning)</option>
                  <option value="paused">Paused (Temporarily stopped)</option>
                </select>
                <p v-if="errors.status" class="mt-1 text-sm text-red-600">
                  {{ errors.status }}
                </p>
              </div>

              <!-- Additional Notes -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Notes (Optional)
                </label>
                <textarea
                  v-model="form.notes"
                  rows="3"
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3"
                  placeholder="Add any notes about this enrollment..."
                ></textarea>
              </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end space-x-3">
              <Link
                :href="route('admin.courses.enrollments.index', course.id)"
                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
              >
                Cancel
              </Link>
              <button
                type="submit"
                :disabled="form.processing"
                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
              >
                <span v-if="form.processing">Enrolling...</span>
                <span v-else>Enroll Student</span>
              </button>
            </div>
          </form>
        </div>

        <!-- Available Students List -->
        <div v-if="availableStudents.length > 0" class="mt-8">
          <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
              <h3 class="text-lg font-semibold text-gray-900">Available Students</h3>
              <p class="mt-1 text-sm text-gray-600">
                {{ availableStudents.length }} students not enrolled in this course
              </p>
            </div>
            <div class="p-6">
              <div class="space-y-3">
                <div
                  v-for="student in availableStudents"
                  :key="student.id"
                  class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50"
                >
                  <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                      <div class="h-8 w-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <span class="text-sm font-medium text-blue-800">
                          {{ student.name.charAt(0).toUpperCase() }}
                        </span>
                      </div>
                    </div>
                    <div>
                      <div class="text-sm font-medium text-gray-900">
                        {{ student.name }}
                      </div>
                      <div class="text-xs text-gray-500">
                        {{ student.email }}
                      </div>
                    </div>
                  </div>
                  <div class="text-right">
                    <div class="text-xs text-gray-500">
                      Level: {{ student.student_profile?.current_level || 'Not set' }}
                    </div>
                    <button
                      @click="quickEnroll(student.id)"
                      class="mt-1 text-xs text-blue-600 hover:text-blue-800"
                    >
                      Quick Enroll
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ArrowLeftIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  course: Object,
  availableStudents: Array,
  errors: Object,
})

const form = useForm({
  user_id: '',
  status: 'enrolled',
  notes: '',
})

const submit = () => {
  form.post(route('admin.courses.enrollments.store', props.course.id))
}

const quickEnroll = (userId) => {
  form.user_id = userId
  form.status = 'enrolled'
  submit()
}
</script>
