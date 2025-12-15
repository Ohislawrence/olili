<template>
  <AdminLayout>
    <Head title="Create Notification" />

    <div class="py-6">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-3xl font-bold text-gray-900">Create Notification</h1>
              <p class="mt-2 text-gray-600">
                Send notifications to users with optional email
              </p>
            </div>
            <Link
              :href="route('admin.notifications.index')"
              class="text-sm text-gray-500 hover:text-gray-700"
            >
              ← Back to Notifications
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
                <div class="grid grid-cols-1 gap-4">
                  <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">
                      Title *
                    </label>
                    <input
                      v-model="form.title"
                      type="text"
                      id="title"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                      required
                    />
                    <p v-if="form.errors.title" class="mt-1 text-sm text-red-600">
                      {{ form.errors.title }}
                    </p>
                  </div>

                  <div>
                    <label for="message" class="block text-sm font-medium text-gray-700">
                      Message *
                    </label>
                    <textarea
                      v-model="form.message"
                      id="message"
                      rows="4"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                      required
                    ></textarea>
                    <p v-if="form.errors.message" class="mt-1 text-sm text-red-600">
                      {{ form.errors.message }}
                    </p>
                  </div>

                  <div>
                    <label for="type" class="block text-sm font-medium text-gray-700">
                      Type *
                    </label>
                    <select
                      v-model="form.type"
                      id="type"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                      required
                    >
                      <option v-for="type in notificationtypes" :key="type.value" :value="type.value">
                        {{ type.label }}
                      </option>
                    </select>
                  </div>
                </div>
              </div>

              <!-- Delivery Options -->
              <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Delivery Options</h3>
                <div class="space-y-4">
                  <div class="flex items-center">
                    <input
                      v-model="form.send_email"
                      id="send_email"
                      type="checkbox"
                      class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                    />
                    <label for="send_email" class="ml-2 block text-sm text-gray-900">
                      Send email notification
                    </label>
                  </div>

                  <div>
                    <label for="scheduled_at" class="block text-sm font-medium text-gray-700">
                      Schedule (optional)
                    </label>
                    <input
                      v-model="form.scheduled_at"
                      type="datetime-local"
                      id="scheduled_at"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                      :min="new Date().toISOString().slice(0, 16)"
                    />
                    <p class="mt-1 text-sm text-gray-500">
                      Leave empty to send immediately
                    </p>
                  </div>
                </div>
              </div>

              <!-- Recipients -->
              <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Recipients</h3>
                <div class="space-y-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Select Recipients
                    </label>
                    <div class="space-y-2">
                      <div v-for="option in userselectionoptions" :key="option.value" class="flex items-center">
                        <input
                          v-model="form.user_selection"
                          :value="option.value"
                          type="radio"
                          :id="`selection_${option.value}`"
                          class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                        />
                        <label :for="`selection_${option.value}`" class="ml-2 block text-sm text-gray-900">
                          {{ option.label }}
                        </label>
                      </div>
                    </div>
                  </div>

                  <!-- Role Selection -->
                  <div v-if="form.user_selection === 'roles'" class="border-t pt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Select Roles
                    </label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                      <div v-for="role in roles" :key="role" class="flex items-center">
                        <input
                          v-model="form.role_names"
                          :value="role"
                          type="checkbox"
                          :id="`role_${role}`"
                          class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        />
                        <label :for="`role_${role}`" class="ml-2 block text-sm text-gray-900 capitalize">
                          {{ role }}
                        </label>
                      </div>
                    </div>
                    <p v-if="form.errors.role_names" class="mt-1 text-sm text-red-600">
                      {{ form.errors.role_names }}
                    </p>
                  </div>

                  <!-- Specific Users -->
                  <div v-if="form.user_selection === 'specific'" class="border-t pt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Select Users
                    </label>
                    <div class="max-h-60 overflow-y-auto border rounded-md p-2">
                      <div v-for="user in users" :key="user.id" class="flex items-center py-1">
                        <input
                          v-model="form.user_ids"
                          :value="user.id"
                          type="checkbox"
                          :id="`user_${user.id}`"
                          class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        />
                        <label :for="`user_${user.id}`" class="ml-2 block text-sm text-gray-900">
                          {{ user.name }} ({{ user.email }})
                        </label>
                      </div>
                    </div>
                    <p v-if="form.errors.user_ids" class="mt-1 text-sm text-red-600">
                      {{ form.errors.user_ids }}
                    </p>
                  </div>

                  <!-- Recipient Count -->
                  <div class="bg-gray-50 p-4 rounded-md">
                    <p class="text-sm text-gray-600">
                      This notification will be sent to approximately
                      <span class="font-semibold">{{ estimatedRecipients }}</span> users.
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
              <Link
                :href="route('admin.notifications.index')"
                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                Cancel
              </Link>
              <button
                type="submit"
                :disabled="form.processing"
                class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
              >
                {{ form.scheduled_at ? 'Schedule Notification' : 'Send Now' }}
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
import { computed, ref } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  roles: Array,
  users: Array,
  notificationtypes: Array,
  userselectionoptions: Array,
})

const form = useForm({
  title: '',
  message: '',
  type: 'info',
  send_email: false,
  scheduled_at: '',
  user_selection: 'all',
  user_ids: [],
  role_names: [],
})

const estimatedRecipients = computed(() => {
  if (form.user_selection === 'all') {
    return props.users.length
  } else if (form.user_selection === 'roles' && form.role_names.length > 0) {
    // This would be better calculated server-side, but we'll estimate here
    return Math.floor(props.users.length / props.roles.length) * form.role_names.length
  } else if (form.user_selection === 'specific') {
    return form.user_ids.length
  }
  return 0
})

const submit = () => {
  form.post(route('admin.notifications.store'))
}
</script>
