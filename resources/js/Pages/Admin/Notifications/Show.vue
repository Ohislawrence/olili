<template>
  <AdminLayout>
    <Head :title="`Notification: ${notification.title}`" />

    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex items-center justify-between">
            <div>
              <div class="flex items-center">
                <Link
                  :href="route('admin.notifications.index')"
                  class="text-gray-400 hover:text-gray-600 mr-3"
                >
                  <ArrowLeftIcon class="h-5 w-5" />
                </Link>
                <div>
                  <h1 class="text-3xl font-bold text-gray-900">{{ notification.title }}</h1>
                  <div class="mt-2 flex flex-wrap items-center gap-3">
                    <span
                      :class="[
                        getStatusClasses(notification.status),
                        'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium'
                      ]"
                    >
                      <span class="flex w-2 h-2 mr-2">
                        <span
                          :class="[
                            getStatusDotClasses(notification.status),
                            'animate-pulse w-2 h-2 rounded-full'
                          ]"
                        ></span>
                      </span>
                      {{ notification.status_label }}
                    </span>
                    <span
                      :class="[
                        getTypeClasses(notification.type),
                        'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium capitalize'
                      ]"
                    >
                      {{ notification.type }}
                    </span>
                    <span
                      v-if="notification.send_email"
                      class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800"
                    >
                      <EnvelopeIcon class="h-4 w-4 mr-1" />
                      Email + In-app
                    </span>
                    <span
                      v-else
                      class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800"
                    >
                      <BellIcon class="h-4 w-4 mr-1" />
                      In-app only
                    </span>
                  </div>
                </div>
              </div>
            </div>
            <div class="flex items-center space-x-3">
              <!-- Send Now Button -->
              <button
                v-if="notification.status !== 'sent' && notification.status !== 'sending'"
                @click="sendNow"
                :disabled="sending"
                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <PaperAirplaneIcon class="h-4 w-4 mr-2" />
                {{ sending ? 'Sending...' : 'Send Now' }}
              </button>

              <!-- Edit Button -->
              <Link
                v-if="notification.status === 'draft'"
                :href="route('admin.notifications.edit', notification.id)"
                class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-800 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150"
              >
                <PencilIcon class="h-4 w-4 mr-2" />
                Edit
              </Link>

              <!-- Preview Button -->
              <Link
                :href="route('admin.notifications.preview', notification.id)"
                class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150"
              >
                <EyeIcon class="h-4 w-4 mr-2" />
                Preview
              </Link>

              <!-- Delete Button -->
              <button
                v-if="notification.status !== 'sent'"
                @click="confirmDelete"
                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
              >
                <TrashIcon class="h-4 w-4 mr-2" />
                Delete
              </button>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Left Column: Notification Details -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Notification Card -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-6 py-5 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Notification Details</h3>
              </div>
              <div class="p-6 space-y-6">
                <!-- Message -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                  <div class="bg-gray-50 p-4 rounded-md border border-gray-200 whitespace-pre-line">
                    {{ notification.message }}
                  </div>
                </div>

                <!-- Metadata Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <!-- Created Info -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Created</label>
                    <div class="space-y-1">
                      <div class="flex items-center text-sm text-gray-600">
                        <UserIcon class="h-4 w-4 mr-2 text-gray-400" />
                        By: {{ notification.sender?.name || 'System' }}
                      </div>
                      <div class="flex items-center text-sm text-gray-600">
                        <CalendarIcon class="h-4 w-4 mr-2 text-gray-400" />
                        {{ formatDateTime(notification.created_at) }}
                      </div>
                    </div>
                  </div>

                  <!-- Timing Info -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Timing</label>
                    <div class="space-y-1">
                      <div v-if="notification.scheduled_at" class="flex items-center text-sm text-gray-600">
                        <ClockIcon class="h-4 w-4 mr-2 text-gray-400" />
                        Scheduled: {{ formatDateTime(notification.scheduled_at) }}
                      </div>
                      <div v-if="notification.sent_at" class="flex items-center text-sm text-gray-600">
                        <CheckCircleIcon class="h-4 w-4 mr-2 text-green-400" />
                        Sent: {{ formatDateTime(notification.sent_at) }}
                      </div>
                      <div v-if="!notification.scheduled_at && !notification.sent_at" class="text-sm text-gray-600">
                        To be sent immediately
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Recipient Selection -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Recipient Selection</label>
                  <div class="bg-gray-50 p-4 rounded-md border border-gray-200">
                    <div class="flex items-center justify-between mb-3">
                      <div class="flex items-center">
                        <UserGroupIcon class="h-5 w-5 mr-2 text-gray-400" />
                        <span class="font-medium">Target: {{ formatUserSelection(notification.user_selection) }}</span>
                      </div>
                      <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                        {{ notification.recipients_count }} recipients
                      </span>
                    </div>

                    <!-- Role Details -->
                    <div v-if="notification.role_names && notification.role_names.length > 0" class="mt-3">
                      <h4 class="text-sm font-medium text-gray-700 mb-2">Selected Roles:</h4>
                      <div class="flex flex-wrap gap-2">
                        <span
                          v-for="role in notification.role_names"
                          :key="role"
                          class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 capitalize"
                        >
                          <TagIcon class="h-3 w-3 mr-1" />
                          {{ role }}
                        </span>
                      </div>
                    </div>

                    <!-- Specific Users -->
                    <div v-if="notification.user_ids && notification.user_ids.length > 0" class="mt-3">
                      <h4 class="text-sm font-medium text-gray-700 mb-2">Specific Users:</h4>
                      <div class="text-sm text-gray-600">
                        {{ notification.user_ids.length }} user(s) selected
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Data (if any) -->
                <div v-if="notification.data">
                  <label class="block text-sm font-medium text-gray-700 mb-2">Additional Data</label>
                  <div class="bg-gray-900 text-gray-100 p-4 rounded-md overflow-x-auto">
                    <pre class="text-sm whitespace-pre-wrap">{{ JSON.stringify(notification.data, null, 2) }}</pre>
                  </div>
                </div>
              </div>
            </div>

            <!-- Stats Card -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-6 py-5 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Delivery Statistics</h3>
              </div>
              <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                  <div class="bg-gray-50 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ stats.total }}</div>
                    <div class="text-sm text-gray-600">Total Recipients</div>
                  </div>
                  <div class="bg-green-50 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-green-900">{{ stats.sent }}</div>
                    <div class="text-sm text-green-600">Successfully Sent</div>
                  </div>
                  <div class="bg-yellow-50 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-yellow-900">{{ stats.pending }}</div>
                    <div class="text-sm text-yellow-600">Pending</div>
                  </div>
                  <div class="bg-red-50 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-red-900">{{ stats.failed }}</div>
                    <div class="text-sm text-red-600">Failed</div>
                  </div>
                </div>
                <div v-if="stats.email_sent > 0" class="mt-6 bg-blue-50 rounded-lg p-4">
                  <div class="flex items-center">
                    <EnvelopeIcon class="h-5 w-5 text-blue-500 mr-2" />
                    <span class="text-sm font-medium text-blue-900">
                      {{ stats.email_sent }} email(s) sent successfully
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Right Column: Timeline & Actions -->
          <div class="space-y-6">
            <!-- Quick Actions Card -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-6 py-5 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
              </div>
              <div class="p-6 space-y-4">
                <!-- Resend Failed -->
                <button
                  v-if="stats.failed > 0"
                  @click="resendFailed"
                  :disabled="resending"
                  class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <ArrowPathIcon class="h-4 w-4 mr-2" />
                  {{ resending ? 'Resending...' : `Resend Failed (${stats.failed})` }}
                </button>

                <!-- Clone Notification -->
                <Link
                  :href="route('admin.notifications.create')"
                  class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                  <DocumentDuplicateIcon class="h-4 w-4 mr-2" />
                  Clone as New
                </Link>

                <!-- View Logs -->
                <button
                  @click="showLogs = !showLogs"
                  class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                  <ChartBarIcon class="h-4 w-4 mr-2" />
                  {{ showLogs ? 'Hide Logs' : 'View Detailed Logs' }}
                </button>
              </div>
            </div>

            <!-- Status Timeline Card -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-6 py-5 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Status Timeline</h3>
              </div>
              <div class="p-6">
                <div class="flow-root">
                  <ul role="list" class="-mb-8">
                    <!-- Created -->
                    <li class="relative pb-8">
                      <div class="relative flex items-start space-x-3">
                        <div>
                          <div class="relative px-1">
                            <div class="h-8 w-8 bg-blue-100 rounded-full ring-8 ring-white flex items-center justify-center">
                              <PlusIcon class="h-4 w-4 text-blue-600" />
                            </div>
                          </div>
                        </div>
                        <div class="min-w-0 flex-1 py-1.5">
                          <div class="text-sm text-gray-900">
                            <span class="font-medium">Created</span> by {{ notification.sender?.name || 'System' }}
                          </div>
                          <p class="mt-0.5 text-sm text-gray-500">
                            {{ formatDateTime(notification.created_at) }}
                          </p>
                        </div>
                      </div>
                    </li>

                    <!-- Scheduled -->
                    <li v-if="notification.scheduled_at" class="relative pb-8">
                      <div class="relative flex items-start space-x-3">
                        <div>
                          <div class="relative px-1">
                            <div class="h-8 w-8 bg-yellow-100 rounded-full ring-8 ring-white flex items-center justify-center">
                              <ClockIcon class="h-4 w-4 text-yellow-600" />
                            </div>
                          </div>
                        </div>
                        <div class="min-w-0 flex-1 py-1.5">
                          <div class="text-sm text-gray-900">
                            <span class="font-medium">Scheduled</span> for delivery
                          </div>
                          <p class="mt-0.5 text-sm text-gray-500">
                            {{ formatDateTime(notification.scheduled_at) }}
                          </p>
                        </div>
                      </div>
                    </li>

                    <!-- Sent -->
                    <li v-if="notification.sent_at" class="relative pb-8">
                      <div class="relative flex items-start space-x-3">
                        <div>
                          <div class="relative px-1">
                            <div class="h-8 w-8 bg-green-100 rounded-full ring-8 ring-white flex items-center justify-center">
                              <CheckCircleIcon class="h-4 w-4 text-green-600" />
                            </div>
                          </div>
                        </div>
                        <div class="min-w-0 flex-1 py-1.5">
                          <div class="text-sm text-gray-900">
                            <span class="font-medium">Sent</span> to recipients
                          </div>
                          <p class="mt-0.5 text-sm text-gray-500">
                            {{ formatDateTime(notification.sent_at) }}
                          </p>
                        </div>
                      </div>
                    </li>

                    <!-- Current Status -->
                    <li class="relative">
                      <div class="relative flex items-start space-x-3">
                        <div>
                          <div class="relative px-1">
                            <div :class="[
                              getStatusColorClasses(notification.status),
                              'h-8 w-8 rounded-full ring-8 ring-white flex items-center justify-center'
                            ]">
                              <BellIcon class="h-4 w-4 text-white" />
                            </div>
                          </div>
                        </div>
                        <div class="min-w-0 flex-1 py-1.5">
                          <div class="text-sm text-gray-900">
                            <span class="font-medium">Current Status:</span> {{ notification.status_label }}
                          </div>
                          <p class="mt-0.5 text-sm text-gray-500">
                            Updated {{ formatRelativeTime(notification.updated_at) }}
                          </p>
                        </div>
                      </div>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Logs Section (Collapsible) -->
        <div v-if="showLogs" class="mt-6">
          <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-5 border-b border-gray-200">
              <h3 class="text-lg font-medium text-gray-900">Delivery Logs</h3>
              <p class="mt-1 text-sm text-gray-500">Detailed logs for each recipient</p>
            </div>
            <div class="p-6">
              <!-- Logs Filters -->
              <div class="mb-6">
                <div class="flex flex-wrap gap-4 items-center">
                  <div class="flex-1 min-w-[200px]">
                    <input
                      v-model="logFilters.search"
                      type="text"
                      placeholder="Search by user..."
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                      @input="searchLogs"
                    />
                  </div>
                  <select
                    v-model="logFilters.status"
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    @change="filterLogs"
                  >
                    <option value="">All Status</option>
                    <option value="sent">Sent</option>
                    <option value="failed">Failed</option>
                    <option value="pending">Pending</option>
                  </select>
                  <select
                    v-model="logFilters.channel"
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    @change="filterLogs"
                  >
                    <option value="">All Channels</option>
                    <option value="database">In-app</option>
                    <option value="email">Email</option>
                    <option value="both">Both</option>
                  </select>
                </div>
              </div>

              <!-- Logs Table -->
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50">
                    <tr>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        User
                      </th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Channel
                      </th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                      </th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Sent At
                      </th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Error
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="log in logs.data" :key="log.id" class="hover:bg-gray-50">
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                          <div class="flex-shrink-0 h-10 w-10">
                            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                              <UserIcon class="h-6 w-6 text-gray-500" />
                            </div>
                          </div>
                          <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">
                              {{ log.user?.name || 'Unknown User' }}
                            </div>
                            <div class="text-sm text-gray-500">
                              {{ log.user?.email || 'No email' }}
                            </div>
                          </div>
                        </div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span :class="[
                          log.channel === 'email' ? 'bg-blue-100 text-blue-800' :
                          log.channel === 'both' ? 'bg-purple-100 text-purple-800' :
                          'bg-gray-100 text-gray-800',
                          'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize'
                        ]">
                          <EnvelopeIcon v-if="log.channel === 'email'" class="h-3 w-3 mr-1" />
                          <BellIcon v-else-if="log.channel === 'database'" class="h-3 w-3 mr-1" />
                          <div v-else class="flex items-center">
                            <EnvelopeIcon class="h-3 w-3 mr-0.5" />
                            <BellIcon class="h-3 w-3 ml-0.5" />
                          </div>
                          {{ log.channel }}
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span :class="[
                          log.status === 'sent' ? 'bg-green-100 text-green-800' :
                          log.status === 'failed' ? 'bg-red-100 text-red-800' :
                          'bg-yellow-100 text-yellow-800',
                          'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium'
                        ]">
                          <span class="flex w-2 h-2 mr-1">
                            <span :class="[
                              log.status === 'sent' ? 'bg-green-400' :
                              log.status === 'failed' ? 'bg-red-400' :
                              'bg-yellow-400',
                              'w-2 h-2 rounded-full'
                            ]"></span>
                          </span>
                          {{ log.status_label || log.status }}
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ log.sent_at ? formatDateTime(log.sent_at) : 'Not sent yet' }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span v-if="log.error_message" class="text-xs text-red-600 max-w-xs truncate block">
                          {{ log.error_message }}
                        </span>
                        <span v-else class="text-xs text-gray-400">-</span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <!-- Pagination -->
              <div v-if="logs.links && logs.links.length > 3" class="mt-6 px-6 py-4 border-t border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                  <div class="text-sm text-gray-700">
                    Showing <span class="font-medium">{{ logs.from }}</span> to
                    <span class="font-medium">{{ logs.to }}</span> of
                    <span class="font-medium">{{ logs.total }}</span> results
                  </div>
                  <div class="flex space-x-2">
                    <Link
                      v-for="link in logs.links"
                      :key="link.label"
                      :href="link.url"
                      :class="[
                        link.active
                          ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                          : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                        'relative inline-flex items-center px-4 py-2 border text-sm font-medium'
                      ]"
                      v-html="link.label"
                    />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

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
        <div class="mt-4 p-3 bg-gray-50 rounded-md">
          <div class="font-medium text-gray-900">{{ notification.title }}</div>
          <div class="text-sm text-gray-600 mt-1">{{ notification.message.substring(0, 100) }}...</div>
        </div>
      </template>
    </ConfirmationModal>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import {
  ArrowLeftIcon,
  BellIcon,
  EnvelopeIcon,
  PaperAirplaneIcon,
  PencilIcon,
  TrashIcon,
  EyeIcon,
  UserIcon,
  CalendarIcon,
  ClockIcon,
  CheckCircleIcon,
  UserGroupIcon,
  TagIcon,
  ArrowPathIcon,
  DocumentDuplicateIcon,
  ChartBarIcon,
  PlusIcon,
} from '@heroicons/vue/24/outline'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import ConfirmationModal from '@/Components/ConfirmationModal.vue'

const props = defineProps({
  notification: Object,
  stats: Object,
  logs: Object,
  filters: Object,
})

const sending = ref(false)
const resending = ref(false)
const showDeleteModal = ref(false)
const showLogs = ref(false)

const logFilters = reactive({
  search: props.filters?.search || '',
  status: props.filters?.status || '',
  channel: props.filters?.channel || '',
})

let logsTimeout = null

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

const getStatusColorClasses = (status) => {
  const classes = {
    draft: 'bg-gray-500',
    scheduled: 'bg-blue-500',
    sending: 'bg-yellow-500',
    sent: 'bg-green-500',
    failed: 'bg-red-500',
  }
  return classes[status] || 'bg-gray-500'
}

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

const formatUserSelection = (selection) => {
  const labels = {
    all: 'All Users',
    roles: 'Specific Roles',
    specific: 'Specific Users',
  }
  return labels[selection] || selection
}

const formatDateTime = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit',
  })
}

const formatRelativeTime = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  const now = new Date()
  const diffInSeconds = Math.floor((now - date) / 1000)

  if (diffInSeconds < 60) {
    return 'just now'
  } else if (diffInSeconds < 3600) {
    const minutes = Math.floor(diffInSeconds / 60)
    return `${minutes} minute${minutes !== 1 ? 's' : ''} ago`
  } else if (diffInSeconds < 86400) {
    const hours = Math.floor(diffInSeconds / 3600)
    return `${hours} hour${hours !== 1 ? 's' : ''} ago`
  } else {
    const days = Math.floor(diffInSeconds / 86400)
    return `${days} day${days !== 1 ? 's' : ''} ago`
  }
}

const sendNow = () => {
  if (confirm('Are you sure you want to send this notification now?')) {
    sending.value = true
    router.post(route('admin.notifications.send-now', props.notification.id), {}, {
      preserveScroll: true,
      onFinish: () => {
        sending.value = false
      }
    })
  }
}

const resendFailed = () => {
  if (confirm(`Resend to ${props.stats.failed} failed recipient(s)?`)) {
    resending.value = true
    router.post(route('admin.notifications.resend-failed', props.notification.id), {}, {
      preserveScroll: true,
      onFinish: () => {
        resending.value = false
      }
    })
  }
}

const confirmDelete = () => {
  showDeleteModal.value = true
}

const deleteNotification = () => {
  router.delete(route('admin.notifications.destroy', props.notification.id), {
    onSuccess: () => {
      router.visit(route('admin.notifications.index'))
    }
  })
}

const searchLogs = () => {
  clearTimeout(logsTimeout)
  logsTimeout = setTimeout(() => {
    filterLogs()
  }, 300)
}

const filterLogs = () => {
  router.get(route('admin.notifications.show', props.notification.id), {
    ...logFilters,
    page: props.logs.current_page,
  }, {
    preserveState: true,
    replace: true,
  })
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
