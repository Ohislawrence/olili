<!-- resources/js/Components/Admin/NotificationTable.vue -->
<template>
  <div class="overflow-x-auto">
    <!-- Bulk Actions -->
    <div v-if="selectedNotifications.length > 0" class="bg-blue-50 border-b border-blue-200 px-6 py-3">
      <div class="flex items-center justify-between">
        <div class="text-sm text-blue-700">
          {{ selectedNotifications.length }} notification{{ selectedNotifications.length !== 1 ? 's' : '' }} selected
        </div>
        <div class="flex items-center space-x-3">
          <select
            v-model="bulkAction"
            class="rounded-md border-gray-300 py-1 pl-3 pr-8 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
            @change="performBulkAction"
          >
            <option value="">Bulk Actions</option>
            <option value="send_now">Send Now</option>
            <option value="delete">Delete</option>
          </select>
          <button
            @click="clearSelection"
            class="text-sm text-gray-600 hover:text-gray-900"
          >
            Clear
          </button>
        </div>
      </div>
    </div>

    <table class="min-w-full divide-y divide-gray-200">
      <thead class="bg-gray-50">
        <tr>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            <input
              v-model="selectAll"
              type="checkbox"
              class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
            />
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Title
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Type
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Status
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Recipients
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Delivery
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Scheduled/ Sent
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Actions
          </th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200">
        <tr
          v-for="notification in notifications"
          :key="notification.id"
          class="hover:bg-gray-50 transition-colors duration-150"
        >
          <!-- Selection Checkbox -->
          <td class="px-6 py-4 whitespace-nowrap">
            <input
              v-model="selectedNotifications"
              :value="notification.id"
              type="checkbox"
              class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
              :disabled="notification.status === 'sent'"
            />
          </td>

          <!-- Title -->
          <td class="px-6 py-4 whitespace-nowrap">
            <div class="flex flex-col">
              <div class="font-medium text-gray-900">
                {{ notification.title }}
              </div>
              <div class="text-sm text-gray-500 truncate max-w-xs">
                {{ notification.message }}
              </div>
            </div>
          </td>

          <!-- Type -->
          <td class="px-6 py-4 whitespace-nowrap">
            <span
              :class="[
                getTypeClasses(notification.type),
                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize'
              ]"
            >
              <svg
                v-if="notification.type === 'info'"
                class="-ml-0.5 mr-1 h-2 w-2"
                fill="currentColor"
                viewBox="0 0 8 8"
              >
                <circle cx="4" cy="4" r="3" />
              </svg>
              <svg
                v-else-if="notification.type === 'success'"
                class="-ml-0.5 mr-1 h-2 w-2"
                fill="currentColor"
                viewBox="0 0 8 8"
              >
                <path d="M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z" />
              </svg>
              <svg
                v-else-if="notification.type === 'warning'"
                class="-ml-0.5 mr-1 h-2 w-2"
                fill="currentColor"
                viewBox="0 0 8 8"
              >
                <path d="M4.4 5.324h-.8v-2.46h.8zm0 1.42h-.8V5.89h.8zM3.76.63L.04 7.075c-.115.2.016.425.26.426h7.397c.242 0 .372-.226.258-.426C6.726 4.924 5.47 2.79 4.253.63c-.113-.174-.39-.174-.494 0z" />
              </svg>
              <svg
                v-else-if="notification.type === 'alert'"
                class="-ml-0.5 mr-1 h-2 w-2"
                fill="currentColor"
                viewBox="0 0 8 8"
              >
                <path d="M4 0C1.8 0 0 1.8 0 4s1.8 4 4 4 4-1.8 4-4-1.8-4-4-4zm0 1c1.66 0 3 1.34 3 3S5.66 7 4 7 1 5.66 1 4s1.34-3 3-3zm-.5 1v3h1V2h-1zm0 4v1h1V6h-1z" />
              </svg>
              {{ notification.type }}
            </span>
          </td>

          <!-- Status -->
          <td class="px-6 py-4 whitespace-nowrap">
            <span
              :class="[
                getStatusClasses(notification.status),
                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium'
              ]"
            >
              <span class="flex w-2 h-2 mr-1">
                <span
                  :class="[
                    getStatusDotClasses(notification.status),
                    'animate-pulse w-2 h-2 rounded-full'
                  ]"
                ></span>
              </span>
              {{ notification.status_label }}
            </span>
          </td>

          <!-- Recipients -->
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            <div class="flex items-center">
              <UserGroupIcon class="h-4 w-4 mr-1 text-gray-400" />
              <span class="font-medium">{{ notification.recipients_count }}</span>
              <span class="mx-1 text-gray-400">•</span>
              <span class="text-xs capitalize">
                {{ formatUserSelection(notification.user_selection) }}
              </span>
            </div>
            <div v-if="notification.role_names" class="text-xs text-gray-400 mt-1">
              Roles: {{ formatRoles(notification.role_names) }}
            </div>
          </td>

          <!-- Delivery -->
          <td class="px-6 py-4 whitespace-nowrap">
            <div class="flex items-center space-x-2">
              <div
                :class="[
                  notification.send_email ? 'text-blue-600 bg-blue-100' : 'text-gray-600 bg-gray-100',
                  'flex items-center px-2 py-1 rounded text-xs font-medium'
                ]"
              >
                <EnvelopeIcon class="h-3 w-3 mr-1" />
                {{ notification.send_email ? 'Email + In-app' : 'In-app only' }}
              </div>
            </div>
          </td>

          <!-- Scheduled/Sent Time -->
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            <div v-if="notification.scheduled_at && notification.status !== 'sent'">
              <div class="font-medium text-gray-900">
                {{ formatDate(notification.scheduled_at) }}
              </div>
              <div class="text-xs text-gray-400">
                Scheduled
              </div>
            </div>
            <div v-else-if="notification.sent_at">
              <div class="font-medium text-gray-900">
                {{ formatDate(notification.sent_at) }}
              </div>
              <div class="text-xs text-gray-400">
                Sent
              </div>
            </div>
            <div v-else class="text-gray-400 italic">
              Immediate
            </div>
          </td>

          <!-- Actions -->
          <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
            <div class="flex items-center space-x-2">
              <!-- View/Show -->
              <Link
                :href="route('admin.notifications.show', notification.id)"
                class="text-blue-600 hover:text-blue-900"
                title="View Details"
              >
                <EyeIcon class="h-4 w-4" />
              </Link>

              <!-- Send Now (for draft/scheduled) -->
              <button
                v-if="notification.status !== 'sent' && notification.status !== 'sending'"
                @click="sendNow(notification)"
                class="text-green-600 hover:text-green-900"
                title="Send Now"
              >
                <PaperAirplaneIcon class="h-4 w-4" />
              </button>

              <!-- Edit (only for draft) -->
              <Link
                v-if="notification.status === 'draft'"
                :href="route('admin.notifications.edit', notification.id)"
                class="text-yellow-600 hover:text-yellow-900"
                title="Edit"
              >
                <PencilIcon class="h-4 w-4" />
              </Link>

              <!-- Preview -->
              <Link
                :href="route('admin.notifications.preview', notification.id)"
                class="text-purple-600 hover:text-purple-900"
                title="Preview"
              >
                <EyeIcon class="h-4 w-4" />
              </Link>

              <!-- Delete (not for sent) -->
              <button
                v-if="notification.status !== 'sent'"
                @click="confirmDelete(notification)"
                class="text-red-600 hover:text-red-900"
                title="Delete"
              >
                <TrashIcon class="h-4 w-4" />
              </button>

              <!-- View Logs -->
              <Link
                v-if="notification.status === 'sent'"
                :href="route('admin.notifications.show', notification.id)"
                class="text-gray-600 hover:text-gray-900"
                title="View Logs"
              >
                <ChartBarIcon class="h-4 w-4" />
              </Link>
            </div>
          </td>
        </tr>
      </tbody>

      <!-- Empty State -->
      <tbody v-if="notifications.length === 0">
        <tr>
          <td colspan="8" class="px-6 py-12 text-center">
            <div class="flex flex-col items-center justify-center">
              <BellIcon class="h-12 w-12 text-gray-400 mb-4" />
              <h3 class="text-lg font-medium text-gray-900 mb-2">No notifications found</h3>
              <p class="text-gray-500 max-w-sm">
                Create your first notification to send announcements, updates, or alerts to users.
              </p>
            </div>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Delete Confirmation Modal -->
    <ConfirmationModal
      :show="showDeleteModal"
      @close="showDeleteModal = false"
      @confirm="deleteNotification"
    >
      <template #title>
        Delete Notification
      </template>
      <template #content>
        Are you sure you want to delete this notification? This action cannot be undone.
        <div v-if="notificationToDelete" class="mt-4 p-3 bg-gray-50 rounded-md">
          <div class="font-medium text-gray-900">{{ notificationToDelete.title }}</div>
          <div class="text-sm text-gray-600 mt-1">{{ notificationToDelete.message.substring(0, 100) }}...</div>
        </div>
      </template>
    </ConfirmationModal>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import {
  BellIcon,
  UserGroupIcon,
  EnvelopeIcon,
  EyeIcon,
  PaperAirplaneIcon,
  PencilIcon,
  TrashIcon,
  ChartBarIcon,
} from '@heroicons/vue/24/outline'
import ConfirmationModal from '@/Components/ConfirmationModal.vue'

const props = defineProps({
  notifications: {
    type: Array,
    required: true,
    default: () => []
  }
})

const selectedNotifications = ref([])
const bulkAction = ref('')
const showDeleteModal = ref(false)
const notificationToDelete = ref(null)

const selectAll = computed({
  get: () => {
    const selectableNotifications = props.notifications.filter(n => n.status !== 'sent')
    return selectableNotifications.length > 0 &&
           selectableNotifications.every(n => selectedNotifications.value.includes(n.id))
  },
  set: (value) => {
    if (value) {
      selectedNotifications.value = props.notifications
        .filter(n => n.status !== 'sent')
        .map(n => n.id)
    } else {
      selectedNotifications.value = []
    }
  }
})

const getTypeClasses = (type) => {
  const classes = {
    info: 'bg-blue-100 text-blue-800',
    success: 'bg-green-100 text-green-800',
    warning: 'bg-yellow-100 text-yellow-800',
    alert: 'bg-red-100 text-red-800',
    system: 'bg-purple-100 text-purple-800',
  }
  return classes[type] || 'bg-gray-100 text-gray-800'
}

const getStatusClasses = (status) => {
  const classes = {
    draft: 'bg-gray-100 text-gray-800',
    scheduled: 'bg-blue-100 text-blue-800',
    sending: 'bg-yellow-100 text-yellow-800',
    sent: 'bg-green-100 text-green-800',
    failed: 'bg-red-100 text-red-800',
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getStatusDotClasses = (status) => {
  const classes = {
    draft: 'bg-gray-400',
    scheduled: 'bg-blue-400',
    sending: 'bg-yellow-400',
    sent: 'bg-green-400',
    failed: 'bg-red-400',
  }
  return classes[status] || 'bg-gray-400'
}

const formatUserSelection = (selection) => {
  const labels = {
    all: 'All Users',
    roles: 'Specific Roles',
    specific: 'Specific Users',
  }
  return labels[selection] || selection
}

const formatRoles = (roles) => {
  if (!roles || !Array.isArray(roles)) return ''
  return roles.map(role => role.charAt(0).toUpperCase() + role.slice(1)).join(', ')
}

const formatDate = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

const clearSelection = () => {
  selectedNotifications.value = []
  bulkAction.value = ''
}

const performBulkAction = () => {
  if (!bulkAction.value || selectedNotifications.value.length === 0) {
    return
  }

  if (bulkAction.value === 'delete') {
    if (confirm(`Are you sure you want to delete ${selectedNotifications.value.length} notification(s)? This action cannot be undone.`)) {
      router.post(route('admin.notifications.bulk-action'), {
        action: 'delete',
        notifications: selectedNotifications.value,
      }, {
        preserveScroll: true,
        onSuccess: () => clearSelection()
      })
    }
  } else if (bulkAction.value === 'send_now') {
    router.post(route('admin.notifications.bulk-action'), {
      action: 'send_now',
      notifications: selectedNotifications.value,
    }, {
      preserveScroll: true,
      onSuccess: () => clearSelection()
    })
  }

  bulkAction.value = ''
}

const sendNow = (notification) => {
  if (confirm('Send this notification now?')) {
    router.post(route('admin.notifications.send-now', notification.id), {}, {
      preserveScroll: true,
    })
  }
}

const confirmDelete = (notification) => {
  notificationToDelete.value = notification
  showDeleteModal.value = true
}

const deleteNotification = () => {
  if (notificationToDelete.value) {
    router.delete(route('admin.notifications.destroy', notificationToDelete.value.id), {
      preserveScroll: true,
      onSuccess: () => {
        showDeleteModal.value = false
        notificationToDelete.value = null
      }
    })
  }
}
</script>

<style scoped>
.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: .5;
  }
}
</style>
