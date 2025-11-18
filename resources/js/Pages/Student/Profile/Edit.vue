<template>
  <StudentLayout>
    <Head title="Edit Profile" />

    <div class="py-6">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex justify-between items-center">
            <div>
              <h1 class="text-2xl font-bold text-gray-900">Edit Profile</h1>
              <p class="mt-1 text-sm text-gray-600">
                Update your personal information and learning preferences
              </p>
            </div>
            <Link
              :href="route('student.profile.show')"
              class="inline-flex items-center px-4 py-2.5 border border-emerald-300 shadow-sm text-sm font-medium rounded-lg text-emerald-700 bg-white hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200"
            >
              Cancel
            </Link>
          </div>
        </div>

        <div class="space-y-6">
          <!-- Personal Information -->
          <div class="bg-white shadow-sm rounded-xl border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-200">
              <h2 class="text-lg font-bold text-gray-900">Personal Information</h2>
            </div>
            <div class="p-6 space-y-6">
              <!-- Name and Email -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                    Full Name *
                  </label>
                  <input
                    id="name"
                    v-model="form.name"
                    type="text"
                    required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                  />
                  <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                    {{ form.errors.name }}
                  </p>
                </div>

                <div>
                  <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                    Email Address *
                  </label>
                  <input
                    id="email"
                    v-model="form.email"
                    type="email"
                    required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                  />
                  <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                    {{ form.errors.email }}
                  </p>
                </div>
              </div>

              <!-- Phone and Date of Birth -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                    Phone Number
                  </label>
                  <input
                    id="phone"
                    v-model="form.phone"
                    type="tel"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                  />
                </div>

                <div>
                  <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1">
                    Date of Birth
                  </label>
                  <input
                    id="date_of_birth"
                    v-model="form.date_of_birth"
                    type="date"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                  />
                </div>
              </div>

              <!-- Gender and Bio -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">
                    Gender
                  </label>
                  <select
                    id="gender"
                    v-model="form.gender"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                  >
                    <option value="">Select gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                  </select>
                </div>

                <div class="md:col-span-2">
                  <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">
                    Bio
                  </label>
                  <textarea
                    id="bio"
                    v-model="form.bio"
                    rows="4"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                    placeholder="Tell us a bit about yourself and your learning journey..."
                  />
                  <p class="mt-1 text-sm text-gray-500">
                    Brief description for your profile. URLs are hyperlinked.
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Learning Preferences -->
          <div class="bg-white shadow-sm rounded-xl border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-200">
              <h2 class="text-lg font-bold text-gray-900">Learning Preferences</h2>
            </div>
            <div class="p-6 space-y-6">
              <!-- Levels -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label for="current_level" class="block text-sm font-medium text-gray-700 mb-1">
                    Current Level *
                  </label>
                  <select
                    id="current_level"
                    v-model="form.current_level"
                    required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                  >
                    <option value="">Select current level</option>
                    <option
                      v-for="level in levels"
                      :key="level"
                      :value="level"
                    >
                      {{ level.charAt(0).toUpperCase() + level.slice(1) }}
                    </option>
                  </select>
                </div>

                <div>
                  <label for="target_level" class="block text-sm font-medium text-gray-700 mb-1">
                    Target Level *
                  </label>
                  <select
                    id="target_level"
                    v-model="form.target_level"
                    required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                  >
                    <option value="">Select target level</option>
                    <option
                      v-for="level in levels"
                      :key="level"
                      :value="level"
                    >
                      {{ level.charAt(0).toUpperCase() + level.slice(1) }}
                    </option>
                  </select>
                </div>
              </div>

              <!-- Study Plan -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label for="weekly_study_hours" class="block text-sm font-medium text-gray-700 mb-1">
                    Weekly Study Hours *
                  </label>
                  <input
                    id="weekly_study_hours"
                    v-model="form.weekly_study_hours"
                    type="number"
                    min="1"
                    max="40"
                    required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                  />
                  <p class="mt-1 text-sm text-gray-500">
                    How many hours can you dedicate per week?
                  </p>
                </div>

                <div>
                  <label for="target_completion_date" class="block text-sm font-medium text-gray-700 mb-1">
                    Target Completion Date *
                  </label>
                  <input
                    id="target_completion_date"
                    v-model="form.target_completion_date"
                    type="date"
                    required
                    :min="minDate"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                  />
                </div>
              </div>

              <!-- Grades -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label for="current_grade" class="block text-sm font-medium text-gray-700 mb-1">
                    Current Grade (%)
                  </label>
                  <input
                    id="current_grade"
                    v-model="form.current_grade"
                    type="number"
                    min="0"
                    max="100"
                    step="0.1"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                  />
                </div>

                <div>
                  <label for="target_grade" class="block text-sm font-medium text-gray-700 mb-1">
                    Target Grade (%)
                  </label>
                  <input
                    id="target_grade"
                    v-model="form.target_grade"
                    type="number"
                    min="0"
                    max="100"
                    step="0.1"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                  />
                </div>
              </div>

              <!-- Exam Board -->
              <div>
                <label for="exam_board_id" class="block text-sm font-medium text-gray-700 mb-1">
                  Preferred Exam Board
                </label>
                <select
                  id="exam_board_id"
                  v-model="form.exam_board_id"
                  class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                >
                  <option value="">No specific exam board</option>
                  <option
                    v-for="examBoard in exam_boards"
                    :key="examBoard.id"
                    :value="examBoard.id"
                  >
                    {{ examBoard.name }}
                  </option>
                </select>
              </div>

              <!-- Learning Goals -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Learning Goals
                </label>
                <div
                  v-for="(goal, index) in form.learning_goals"
                  :key="index"
                  class="flex gap-2 mb-2"
                >
                  <input
                    v-model="form.learning_goals[index]"
                    type="text"
                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                    placeholder="What do you want to achieve?"
                  />
                  <button
                    type="button"
                    @click="removeGoal(index)"
                    class="flex-shrink-0 px-3 py-2.5 text-sm text-red-600 hover:text-red-800 border border-red-200 rounded-lg hover:bg-red-50 transition-colors"
                  >
                    Remove
                  </button>
                </div>
                <button
                  type="button"
                  @click="addGoal"
                  class="inline-flex items-center px-4 py-2.5 border border-emerald-300 shadow-sm text-sm font-medium rounded-lg text-emerald-700 bg-white hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                  </svg>
                  Add Goal
                </button>
                <p class="mt-1 text-sm text-gray-500">
                  Define your learning objectives and what you hope to achieve.
                </p>
              </div>
            </div>
          </div>

          <!-- Form Actions -->
          <div class="flex justify-end space-x-3">
            <Link
              :href="route('student.profile.show')"
              class="inline-flex items-center px-4 py-2.5 border border-emerald-300 shadow-sm text-sm font-medium rounded-lg text-emerald-700 bg-white hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200"
            >
              Cancel
            </Link>
            <button
              type="submit"
              @click="submit"
              :disabled="form.processing"
              class="inline-flex items-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50"
            >
              <span v-if="form.processing">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Saving...
              </span>
              <span v-else>
                Save Changes
              </span>
            </button>
          </div>
        </div>

        <!-- Password Change Section -->
        <div class="mt-8 bg-white shadow-sm rounded-xl border border-gray-100">
          <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-900">Change Password</h2>
          </div>
          <div class="p-6">
            <form @submit.prevent="updatePassword">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">
                    Current Password *
                  </label>
                  <input
                    id="current_password"
                    v-model="passwordForm.current_password"
                    type="password"
                    required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                  />
                  <p v-if="passwordForm.errors.current_password" class="mt-1 text-sm text-red-600">
                    {{ passwordForm.errors.current_password }}
                  </p>
                </div>

                <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                      New Password *
                    </label>
                    <input
                      id="password"
                      v-model="passwordForm.password"
                      type="password"
                      required
                      class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                    />
                    <p v-if="passwordForm.errors.password" class="mt-1 text-sm text-red-600">
                      {{ passwordForm.errors.password }}
                    </p>
                  </div>

                  <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                      Confirm New Password *
                    </label>
                    <input
                      id="password_confirmation"
                      v-model="passwordForm.password_confirmation"
                      type="password"
                      required
                      class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                    />
                  </div>
                </div>
              </div>

              <div class="mt-6 flex justify-end">
                <button
                  type="submit"
                  :disabled="passwordForm.processing"
                  class="inline-flex items-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50"
                >
                  <span v-if="passwordForm.processing">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Updating...
                  </span>
                  <span v-else>
                    Update Password
                  </span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </StudentLayout>
</template>

<script setup>
import { reactive, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
  user: Object,
  student_profile: Object,
  exam_boards: Array,
  levels: Array,
  learning_styles: Array,
})

const form = reactive({
  name: props.user.name,
  email: props.user.email,
  phone: props.user.phone || '',
  date_of_birth: props.user.date_of_birth || '',
  gender: props.user.gender || '',
  bio: props.user.bio || '',
  exam_board_id: props.student_profile.exam_board_id || '',
  current_level: props.student_profile.current_level,
  target_level: props.student_profile.target_level,
  target_completion_date: props.student_profile.target_completion_date?.split('T')[0] || '',
  weekly_study_hours: props.student_profile.weekly_study_hours,
  current_grade: props.student_profile.current_grade || '',
  target_grade: props.student_profile.target_grade || '',
  learning_goals: props.student_profile.learning_goals?.length ? [...props.student_profile.learning_goals] : [''],
  errors: {},
  processing: false,
})

const passwordForm = reactive({
  current_password: '',
  password: '',
  password_confirmation: '',
  errors: {},
  processing: false,
})

const minDate = computed(() => {
  const tomorrow = new Date()
  tomorrow.setDate(tomorrow.getDate() + 1)
  return tomorrow.toISOString().split('T')[0]
})

const addGoal = () => {
  form.learning_goals.push('')
}

const removeGoal = (index) => {
  form.learning_goals.splice(index, 1)
}

const submit = () => {
  form.processing = true
  form.errors = {}

  router.put(route('student.profile.update'), {
    ...form,
    learning_goals: form.learning_goals.filter(goal => goal.trim() !== ''),
  }, {
    onFinish: () => {
      form.processing = false
    },
    onError: (errors) => {
      form.errors = errors
    },
  })
}

const updatePassword = () => {
  passwordForm.processing = true
  passwordForm.errors = {}

  router.put(route('student.profile.update-password'), {
    current_password: passwordForm.current_password,
    password: passwordForm.password,
    password_confirmation: passwordForm.password_confirmation,
  }, {
    onFinish: () => {
      passwordForm.processing = false
      passwordForm.current_password = ''
      passwordForm.password = ''
      passwordForm.password_confirmation = ''
    },
    onError: (errors) => {
      passwordForm.errors = errors
    },
  })
}
</script>
