<!-- resources/js/Pages/Admin/Email/Index.vue -->
<template>
  <AdminLayout>
    <Head title="Email Management" />

    <div class="py-6">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex justify-between items-center">
            <div>
              <h1 class="text-3xl font-bold text-gray-900">Email Management</h1>
              <p class="mt-2 text-gray-600">
                Send emails to users based on roles or individually
              </p>
            </div>
          </div>
        </div>

        <!-- Success/Error Messages -->
        <div v-if="$page.props.flash.success" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
          <div class="flex items-center">
            <CheckCircleIcon class="h-5 w-5 text-green-400 mr-2" />
            <span class="text-green-800">{{ $page.props.flash.success }}</span>
          </div>
        </div>

        <div v-if="$page.props.flash.error" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
          <div class="flex items-center">
            <ExclamationTriangleIcon class="h-5 w-5 text-red-400 mr-2" />
            <span class="text-red-800">{{ $page.props.flash.error }}</span>
          </div>
        </div>

        <!-- Email Form -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
          <form @submit.prevent="sendEmail">
            <div class="p-6 space-y-6">
              <!-- Email Type Selection -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Send To
                </label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <button
                    type="button"
                    @click="form.type = 'role'"
                    class="p-4 border rounded-lg text-left transition-colors duration-150"
                    :class="form.type === 'role' ? 'border-blue-500 bg-blue-50' : 'border-gray-300 hover:border-gray-400'"
                  >
                    <div class="flex items-center">
                      <UserGroupIcon class="h-5 w-5 text-gray-400 mr-3" />
                      <div>
                        <div class="font-medium text-gray-900">By Role</div>
                        <div class="text-sm text-gray-500">Send to all users with a specific role</div>
                      </div>
                    </div>
                  </button>

                  <button
                    type="button"
                    @click="form.type = 'user'"
                    class="p-4 border rounded-lg text-left transition-colors duration-150"
                    :class="form.type === 'user' ? 'border-blue-500 bg-blue-50' : 'border-gray-300 hover:border-gray-400'"
                  >
                    <div class="flex items-center">
                      <UserIcon class="h-5 w-5 text-gray-400 mr-3" />
                      <div>
                        <div class="font-medium text-gray-900">Single User</div>
                        <div class="text-sm text-gray-500">Send to a specific user</div>
                      </div>
                    </div>
                  </button>

                  <button
                    type="button"
                    @click="form.type = 'multiple'"
                    class="p-4 border rounded-lg text-left transition-colors duration-150"
                    :class="form.type === 'multiple' ? 'border-blue-500 bg-blue-50' : 'border-gray-300 hover:border-gray-400'"
                  >
                    <div class="flex items-center">
                      <UsersIcon class="h-5 w-5 text-gray-400 mr-3" />
                      <div>
                        <div class="font-medium text-gray-900">Multiple Users</div>
                        <div class="text-sm text-gray-500">Send to selected users</div>
                      </div>
                    </div>
                  </button>
                </div>
              </div>

              <!-- Role Selection -->
              <div v-if="form.type === 'role'">
                <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                  Select Role
                </label>
                <select
                  id="role"
                  v-model="form.role"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="">Select a role</option>
                  <option v-for="role in roles" :key="role" :value="role" class="capitalize">
                    {{ role }}
                  </option>
                </select>
              </div>

              <!-- User Selection -->
              <div v-if="form.type === 'user'">
                <label for="user" class="block text-sm font-medium text-gray-700 mb-2">
                  Select User
                </label>
                <UserSearch
                  v-model="form.user_id"
                  :error="errors.user_id"
                />
              </div>

              <!-- Multiple Users Selection -->
              <div v-if="form.type === 'multiple'">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Select Users
                </label>
                <UserMultiSelect
                  v-model="form.user_ids"
                  :error="errors.user_ids"
                />
              </div>

              <!-- From Details -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label for="from_email" class="block text-sm font-medium text-gray-700 mb-2">
                    From Email (Optional)
                  </label>
                  <input
                    id="from_email"
                    v-model="form.from_email"
                    type="email"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="noreply@example.com"
                  />
                </div>

                <div>
                  <label for="from_name" class="block text-sm font-medium text-gray-700 mb-2">
                    From Name (Optional)
                  </label>
                  <input
                    id="from_name"
                    v-model="form.from_name"
                    type="text"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="AI Course Platform"
                  />
                </div>
              </div>

              <!-- Subject -->
              <div>
                <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                  Subject
                </label>
                <input
                  id="subject"
                  v-model="form.subject"
                  type="text"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  placeholder="Enter email subject"
                />
              </div>

              <!-- Message -->
              <div>
                <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                  Message
                </label>
                <textarea
                  id="message"
                  v-model="form.message"
                  rows="10"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  placeholder="Enter your email message... (HTML supported)"
                ></textarea>
                <p class="mt-1 text-sm text-gray-500">
                  You can use HTML tags for formatting. Basic styling is supported.
                </p>
              </div>
            </div>

            <!-- Form Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
              <button
                type="button"
                @click="resetForm"
                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                Reset
              </button>
              <button
                type="submit"
                :disabled="sending"
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50"
              >
                <PaperAirplaneIcon v-if="!sending" class="h-4 w-4 mr-2" />
                <ArrowPathIcon v-else class="h-4 w-4 mr-2 animate-spin" />
                {{ sending ? 'Sending...' : 'Send Email' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import {
  PaperAirplaneIcon,
  ArrowPathIcon,
  UserGroupIcon,
  UserIcon,
  UsersIcon,
  CheckCircleIcon,
  ExclamationTriangleIcon,
} from '@heroicons/vue/24/outline'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import UserSearch from '@/Components/Admin/UserSearch.vue'
import UserMultiSelect from '@/Components/Admin/UserMultiSelect.vue'

const props = defineProps({
  roles: Array,
  errors: Object,
})

const sending = ref(false)

const form = useForm({
  type: 'role',
  role: '',
  user_id: null,
  user_ids: [],
  subject: '',
  message: '',
  from_email: '',
  from_name: '',
})

const sendEmail = () => {
  sending.value = true
  form.post(route('admin.email.send'), {
    preserveScroll: true,
    onFinish: () => {
      sending.value = false
      if (!Object.keys(form.errors).length) {
        resetForm()
      }
    },
  })
}

const resetForm = () => {
  form.reset()
  form.type = 'role'
}
</script>