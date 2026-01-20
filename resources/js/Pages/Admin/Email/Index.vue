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
                Send personalized emails to users. Use
                <code class="bg-gray-100 px-1 rounded">&lbrace;&lbrace;name&rbrace;&rbrace;</code>
                to include user's name.
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
                  <span class="text-gray-500 text-xs ml-2">
                    Use &lbrace;&lbrace;name&rbrace;&rbrace; for personalization
                  </span>
                </label>
                <input
                  id="subject"
                  v-model="form.subject"
                  type="text"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  placeholder="'Hello ' + {{ name }}}' + ', important update!'"
                />
              </div>

              <!-- Message Editor -->
              <div>
                <div class="flex justify-between items-center mb-2">
                  <label class="block text-sm font-medium text-gray-700">
                    Message
                  </label>
                  <div class="text-sm text-gray-500 flex items-center space-x-2">
                    <div class="flex items-center">
                      <SparklesIcon class="h-4 w-4 text-blue-500 mr-1" />
                      <span>Personalization: Use <code class="bg-gray-100 px-1 rounded text-xs">&lbrace;&lbrace;name&rbrace;&rbrace;</code></span>
                    </div>
                    <button
                      type="button"
                      @click="insertTemplate"
                      class="text-blue-600 hover:text-blue-800 text-sm"
                    >
                      Insert Template
                    </button>
                  </div>
                </div>

                <RichTextEditor
                  v-model="form.message"
                  :error="errors.message"
                  height="400px"
                />

                <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                  <h4 class="text-sm font-medium text-gray-700 mb-2">Available Variables:</h4>
                  <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                    <button
                      type="button"
                      @click="insertVariable('{{name}}')"
                      class="text-xs px-2 py-1 bg-white border border-gray-300 rounded hover:bg-gray-50"
                    >
                      &lbrace;&lbrace;name&rbrace;&rbrace;
                    </button>
                    <button
                      type="button"
                      @click="insertVariable('{{email}}')"
                      class="text-xs px-2 py-1 bg-white border border-gray-300 rounded hover:bg-gray-50"
                    >
                      &lbrace;&lbrace;email&rbrace;&rbrace;
                    </button>
                    <button
                      type="button"
                      @click="insertVariable('{{role}}')"
                      class="text-xs px-2 py-1 bg-white border border-gray-300 rounded hover:bg-gray-50"
                    >
                      &lbrace;&lbrace;role&rbrace;&rbrace;
                    </button>
                    <button
                      type="button"
                      @click="insertVariable('{{app_name}}')"
                      class="text-xs px-2 py-1 bg-white border border-gray-300 rounded hover:bg-gray-50"
                    >
                      &lbrace;&lbrace;app_name&rbrace;&rbrace;
                    </button>
                  </div>
                </div>
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
                type="button"
                @click="previewEmail"
                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                Preview
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
  SparklesIcon,
} from '@heroicons/vue/24/outline'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import UserSearch from '@/Components/Admin/UserSearch.vue'
import UserMultiSelect from '@/Components/Admin/UserMultiSelect.vue'
//import RichTextEditor from '@/Components/Admin/RichTextEditorEmail.vue'
import RichTextEditor from '@/Components/RichTextEditor.vue'

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

const insertVariable = (variable) => {
  const currentMessage = form.message || ''
  // Insert at cursor position if we can determine it
  const textarea = document.querySelector('.rich-text-editor-email textarea')
  if (textarea && textarea === document.activeElement) {
    const start = textarea.selectionStart
    const end = textarea.selectionEnd
    const newText = currentMessage.substring(0, start) + variable + currentMessage.substring(end)
    form.message = newText

    // Set cursor position after inserted variable
    nextTick(() => {
      textarea.focus()
      textarea.setSelectionRange(start + variable.length, start + variable.length)
    })
  } else {
    form.message = currentMessage + variable
  }
}

const insertTemplate = () => {
  const template = `
<div style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 600px; margin: 0 auto;">
  <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center;">
    <h1 style="color: white; margin: 0; font-size: 28px;">{{app_name}}</h1>
  </div>

  <div style="padding: 40px 30px; background-color: #ffffff; border: 1px solid #e5e7eb;">
    <h2 style="color: #1f2937; margin-top: 0;">Hello {name},</h2>

    <div style="color: #4b5563; line-height: 1.6; font-size: 16px;">
      <!-- Your content here -->
      <p>We have some exciting news to share with you!</p>

      <div style="background-color: #f3f4f6; padding: 20px; border-radius: 8px; margin: 25px 0;">
        <p style="margin: 0;"> <strong>Special Update:</strong> Personalized content just for you!</p>
      </div>

      <p>We appreciate you being part of our community.</p>
    </div>

    <div style="text-align: center; margin: 40px 0;">
      <a href="{{app_url}}" style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 14px 32px; text-decoration: none; border-radius: 6px; font-weight: 600; letter-spacing: 0.5px;">
        Visit Your Dashboard
      </a>
    </div>
  </div>

  <div style="background-color: #f9fafb; padding: 25px; text-align: center; border-top: 1px solid #e5e7eb;">
    <p style="color: #6b7280; font-size: 14px; margin: 0;">
      © {{year}} {{app_name}}. All rights reserved.<br>
      <small>If you have any questions, please contact our support team.</small>
    </p>
  </div>
</div>
`
  form.message = template.trim()
}

const previewEmail = () => {
  if (!form.subject && !form.message) {
    alert('Please enter subject and message first')
    return
  }

  // Open preview in new tab
  const previewData = {
    subject: form.subject,
    message: form.message,
    from_name: form.from_name || 'AI Course Platform',
    from_email: form.from_email || 'noreply@example.com'
  }

  const newWindow = window.open('', '_blank')
  newWindow.document.write(`
    <!DOCTYPE html>
    <html>
    <head>
      <title>Email Preview</title>
      <style>
        body { font-family: system-ui, -apple-system, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
        .preview-container { max-width: 600px; margin: 0 auto; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .preview-header { background: #3b82f6; color: white; padding: 20px; text-align: center; }
        .preview-content { padding: 30px; }
        .preview-footer { background: #f8fafc; padding: 20px; text-align: center; border-top: 1px solid #e2e8f0; color: #64748b; }
        .variable { background: #e0f2fe; padding: 2px 6px; border-radius: 4px; font-family: monospace; }
      </style>
    </head>
    <body>
      <div class="preview-container">
        <div class="preview-header">
          <h2>Email Preview</h2>
          <p>From: ${previewData.from_name} &lt;${previewData.from_email}&gt;</p>
        </div>
        <div class="preview-content">
          <h3>Subject: ${previewData.subject}</h3>
          <hr style="margin: 20px 0;">
          ${previewData.message.replace(/\{\{(\w+)\}\}/g, '<span class="variable">{{$1}}</span>')}
        </div>
        <div class="preview-footer">
          <p>Note: Variables like <span class="variable">{name}</span> will be replaced with actual user data</p>
        </div>
      </div>
    </body>
    </html>
  `)
  newWindow.document.close()
}
</script>
