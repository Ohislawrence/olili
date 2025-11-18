<!-- resources/js/Pages/Admin/Users/Create.vue -->
<template>
  <AdminLayout>
    <Head title="Create New User" />

    <div class="py-6">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex justify-between items-center">
            <div>
              <h1 class="text-3xl font-bold text-gray-900">Create New User</h1>
              <p class="mt-2 text-gray-600">
                Add a new user to the AI Tutor platform
              </p>
            </div>
            <Link
              :href="route('admin.users.index')"
              class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
            >
              Back to Users
            </Link>
          </div>
        </div>

        <!-- Form -->
        <div class="bg-white shadow rounded-lg">
          <form @submit.prevent="submit">
            <div class="p-6 space-y-6">
              <!-- Basic Information -->
              <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                  <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">
                      Full Name *
                    </label>
                    <input
                      id="name"
                      v-model="form.name"
                      type="text"
                      required
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                      :class="{ 'border-red-300': errors.name }"
                    />
                    <p v-if="errors.name" class="mt-1 text-sm text-red-600">
                      {{ errors.name }}
                    </p>
                  </div>

                  <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">
                      Email Address *
                    </label>
                    <input
                      id="email"
                      v-model="form.email"
                      type="email"
                      required
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                      :class="{ 'border-red-300': errors.email }"
                    />
                    <p v-if="errors.email" class="mt-1 text-sm text-red-600">
                      {{ errors.email }}
                    </p>
                  </div>

                  <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">
                      Password *
                    </label>
                    <input
                      id="password"
                      v-model="form.password"
                      type="password"
                      required
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                      :class="{ 'border-red-300': errors.password }"
                    />
                    <p v-if="errors.password" class="mt-1 text-sm text-red-600">
                      {{ errors.password }}
                    </p>
                  </div>

                  <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                      Confirm Password *
                    </label>
                    <input
                      id="password_confirmation"
                      v-model="form.password_confirmation"
                      type="password"
                      required
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    />
                  </div>

                  <div>
                    <label for="role" class="block text-sm font-medium text-gray-700">
                      Role *
                    </label>
                    <select
                      id="role"
                      v-model="form.role"
                      required
                      class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                    >
                      <option value="">Select a role</option>
                      <option value="admin">Administrator</option>
                      <option value="tutor">Tutor</option>
                      <option value="student">Student</option>
                    </select>
                    <p v-if="errors.role" class="mt-1 text-sm text-red-600">
                      {{ errors.role }}
                    </p>
                  </div>

                  <div class="flex items-center">
                    <input
                      id="is_active"
                      v-model="form.is_active"
                      type="checkbox"
                      class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                    />
                    <label for="is_active" class="ml-2 block text-sm text-gray-900">
                      Active User
                    </label>
                  </div>

                  <div class="flex items-center">
                    <input
                      id="email_verified"
                      v-model="form.email_verified"
                      type="checkbox"
                      class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                    />
                    <label for="email_verified" class="ml-2 block text-sm text-gray-900">
                      Email Verified
                    </label>
                  </div>
                </div>
              </div>

              <!-- Additional Information -->
              <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Information</h3>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                  <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">
                      Phone Number
                    </label>
                    <input
                      id="phone"
                      v-model="form.phone"
                      type="tel"
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    />
                  </div>

                  <div>
                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700">
                      Date of Birth
                    </label>
                    <input
                      id="date_of_birth"
                      v-model="form.date_of_birth"
                      type="date"
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    />
                  </div>

                  <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700">
                      Gender
                    </label>
                    <select
                      id="gender"
                      v-model="form.gender"
                      class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                    >
                      <option value="">Select gender</option>
                      <option value="male">Male</option>
                      <option value="female">Female</option>
                      <option value="other">Other</option>
                    </select>
                  </div>
                </div>

                <div class="mt-6">
                  <label for="bio" class="block text-sm font-medium text-gray-700">
                    Bio
                  </label>
                  <textarea
                    id="bio"
                    v-model="form.bio"
                    rows="3"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    placeholder="Brief description about the user..."
                  ></textarea>
                </div>
              </div>

              <!-- Student Profile (Conditional) -->
              <div v-if="form.role === 'student'">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Student Profile</h3>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
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

                  <div>
                    <label for="current_level" class="block text-sm font-medium text-gray-700">
                      Current Level *
                    </label>
                    <select
                      id="current_level"
                      v-model="form.current_level"
                      :required="form.role === 'student'"
                      class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                    >
                      <option value="">Select level</option>
                      <option value="beginner">Beginner</option>
                      <option value="intermediate">Intermediate</option>
                      <option value="advanced">Advanced</option>
                      <option value="expert">Expert</option>
                    </select>
                    <p v-if="errors.current_level" class="mt-1 text-sm text-red-600">
                      {{ errors.current_level }}
                    </p>
                  </div>

                  <div>
                    <label for="target_level" class="block text-sm font-medium text-gray-700">
                      Target Level *
                    </label>
                    <select
                      id="target_level"
                      v-model="form.target_level"
                      :required="form.role === 'student'"
                      class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                    >
                      <option value="">Select level</option>
                      <option value="beginner">Beginner</option>
                      <option value="intermediate">Intermediate</option>
                      <option value="advanced">Advanced</option>
                      <option value="expert">Expert</option>
                    </select>
                    <p v-if="errors.target_level" class="mt-1 text-sm text-red-600">
                      {{ errors.target_level }}
                    </p>
                  </div>

                  <div>
                    <label for="weekly_study_hours" class="block text-sm font-medium text-gray-700">
                      Weekly Study Hours *
                    </label>
                    <select
                      id="weekly_study_hours"
                      v-model="form.weekly_study_hours"
                      :required="form.role === 'student'"
                      class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                    >
                      <option value="2">2-4 hours</option>
                      <option value="5">5-7 hours</option>
                      <option value="10">10+ hours</option>
                    </select>
                    <p v-if="errors.weekly_study_hours" class="mt-1 text-sm text-red-600">
                      {{ errors.weekly_study_hours }}
                    </p>
                  </div>

                  <div>
                    <label for="target_completion_date" class="block text-sm font-medium text-gray-700">
                      Target Completion Date *
                    </label>
                    <input
                      id="target_completion_date"
                      v-model="form.target_completion_date"
                      type="date"
                      :required="form.role === 'student'"
                      :min="minDate"
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    />
                    <p v-if="errors.target_completion_date" class="mt-1 text-sm text-red-600">
                      {{ errors.target_completion_date }}
                    </p>
                  </div>

                  <div>
                    <label for="current_grade" class="block text-sm font-medium text-gray-700">
                      Current Grade (%)
                    </label>
                    <input
                      id="current_grade"
                      v-model="form.current_grade"
                      type="number"
                      min="0"
                      max="100"
                      step="0.1"
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    />
                  </div>

                  <div>
                    <label for="target_grade" class="block text-sm font-medium text-gray-700">
                      Target Grade (%)
                    </label>
                    <input
                      id="target_grade"
                      v-model="form.target_grade"
                      type="number"
                      min="0"
                      max="100"
                      step="0.1"
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    />
                  </div>
                </div>

                <!-- Learning Goals -->
                <div class="mt-6">
                  <label class="block text-sm font-medium text-gray-700 mb-3">
                    Learning Goals
                  </label>
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label
                      v-for="goal in learningGoals"
                      :key="goal.value"
                      class="flex items-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer"
                      :class="{ 'border-blue-500 bg-blue-50': form.learning_goals.includes(goal.value) }"
                    >
                      <input
                        type="checkbox"
                        :value="goal.value"
                        v-model="form.learning_goals"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                      />
                      <span class="ml-3 text-sm text-gray-900">
                        {{ goal.label }}
                      </span>
                    </label>
                  </div>
                </div>

                <!-- Learning Preferences -->
                <div class="mt-6">
                  <label class="block text-sm font-medium text-gray-700 mb-3">
                    Learning Preferences
                  </label>
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label
                      v-for="preference in learningPreferences"
                      :key="preference.value"
                      class="flex items-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer"
                      :class="{ 'border-blue-500 bg-blue-50': form.learning_preferences.includes(preference.value) }"
                    >
                      <input
                        type="checkbox"
                        :value="preference.value"
                        v-model="form.learning_preferences"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                      />
                      <div class="ml-3">
                        <span class="text-sm font-medium text-gray-900">
                          {{ preference.label }}
                        </span>
                        <p class="text-xs text-gray-500">
                          {{ preference.description }}
                        </p>
                      </div>
                    </label>
                  </div>
                </div>
              </div>
            </div>

            <!-- Form Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
              <Link
                :href="route('admin.users.index')"
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
                  Create User
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
import { ArrowPathIcon } from '@heroicons/vue/24/outline'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  roles: Array,
  exam_boards: Array,
  levels: Array,
  errors: Object,
})

const processing = ref(false)

const form = useForm({
  // Basic information
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  role: 'student',
  phone: '',
  date_of_birth: '',
  gender: '',
  bio: '',
  is_active: true,
  email_verified: false,

  // Student profile
  exam_board_id: '',
  current_level: 'beginner',
  target_level: 'intermediate',
  target_completion_date: '',
  weekly_study_hours: '5',
  current_grade: '',
  target_grade: '',
  learning_goals: [],
  learning_preferences: [],
})

const minDate = computed(() => {
  return new Date().toISOString().split('T')[0]
})

const learningGoals = [
  { value: 'exam_preparation', label: 'Prepare for an exam' },
  { value: 'career_advancement', label: 'Advance my career' },
  { value: 'skill_development', label: 'Learn new skills' },
  { value: 'academic_studies', label: 'Support academic studies' },
  { value: 'personal_interest', label: 'Personal interest and curiosity' },
]

const learningPreferences = [
  {
    value: 'visual',
    label: 'Visual',
    description: 'Diagrams, charts, and visual aids'
  },
  {
    value: 'interactive',
    label: 'Interactive',
    description: 'Hands-on exercises and activities'
  },
  {
    value: 'reading',
    label: 'Reading',
    description: 'Text-based materials and articles'
  },
  {
    value: 'video',
    label: 'Video',
    description: 'Video lectures and tutorials'
  },
]

const submit = () => {
  processing.value = true

  form.post(route('admin.users.store'), {
    onFinish: () => {
      processing.value = false
    },
  })
}
</script>
