<template>
  <StudentLayout>
    <Head title="Chat Sessions" />

    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex justify-between items-center">
            <div>
              <h1 class="text-2xl font-bold text-gray-900">Chat Sessions</h1>
              <p class="mt-1 text-sm text-gray-600">
                Manage your conversations with the AI tutor for your courses
              </p>
            </div>
            <Link
              :href="route('student.chat.create')"
              class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 border border-transparent rounded-lg font-semibold text-white shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200"
            >
              <PlusIcon class="h-4 w-4 mr-2" />
              New Chat
            </Link>
          </div>
        </div>

        <!-- Filters -->
        <div class="mb-6 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
          <div class="flex flex-col sm:flex-row gap-4">
            <!-- Course Filter -->
            <div class="w-full sm:w-48">
              <label class="block text-xs font-medium text-gray-700 mb-1">
                Filter by Course:
              </label>
              <select
                v-model="filters.course_id"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5 px-3"
                @change="filter"
              >
                <option value="">All Courses</option>
                <option
                  v-for="course in courses"
                  :key="course.id"
                  :value="course.id"
                >
                  {{ course.title }}
                </option>
              </select>
            </div>

            <!-- Active Only Filter -->
            <div class="flex items-center">
              <input
                id="active_only"
                v-model="filters.active_only"
                type="checkbox"
                class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded"
                @change="filter"
              />
              <label for="active_only" class="ml-2 text-sm text-gray-700">
                Active sessions only
              </label>
            </div>

            <!-- Clear Filters -->
            <button
              v-if="filters.course_id || filters.active_only"
              @click="clearFilters"
              class="ml-auto text-sm text-emerald-600 hover:text-emerald-500 font-medium"
            >
              Clear filters
            </button>
          </div>
        </div>

        <!-- Chat Sessions Grid -->
        <div v-if="chat_sessions.data.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="session in chat_sessions.data"
            :key="session.id"
            class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300 hover:-translate-y-1"
          >
            <div class="p-6">
              <!-- Session Header -->
              <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                  <div class="flex items-center gap-2 mb-2">
                    <span
                      :class="[
                        'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold',
                        session.is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-800'
                      ]"
                    >
                      {{ session.is_active ? 'Active' : 'Closed' }}
                    </span>
                    <span
                      v-if="session.last_activity_at"
                      class="text-xs text-gray-500"
                    >
                      {{ formatRelativeTime(session.last_activity_at) }}
                    </span>
                  </div>

                  <!-- Context -->
                  <div class="mb-3">
                    <h3 class="text-sm font-bold text-gray-900 mb-1">
                      {{ getSessionTitle(session) }}
                    </h3>
                    <p class="text-xs text-gray-500 line-clamp-2">
                      {{ getSessionContext(session) }}
                    </p>
                  </div>
                </div>
              </div>

              <!-- Session Info -->
              <div class="space-y-2 text-sm text-gray-600">
                <div class="flex items-center">
                  <ChatBubbleLeftRightIcon class="h-4 w-4 mr-2" />
                  <span>{{ session.messages_count || 0 }} messages</span>
                </div>
                <div v-if="session.course" class="flex items-center">
                  <AcademicCapIcon class="h-4 w-4 mr-2" />
                  <span class="truncate">{{ session.course.title }}</span>
                </div>
              </div>

              <!-- Actions -->
              <div class="mt-6 flex space-x-2">
                <Link
                  :href="route('student.chat.show', session.id)"
                  class="flex-1 text-center px-3 py-2.5 text-sm font-semibold text-white bg-emerald-600 border border-transparent rounded-lg hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-colors"
                >
                  Open Chat
                </Link>
                <button
                  v-if="session.is_active"
                  @click="closeSession(session)"
                  class="px-3 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-emerald-300 rounded-lg hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-colors"
                >
                  <XMarkIcon class="h-4 w-4" />
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-12">
          <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
            <ChatBubbleLeftRightIcon class="mx-auto h-12 w-12 text-gray-400" />
            <h3 class="mt-2 text-sm font-semibold text-gray-900">No chat sessions</h3>
            <p class="mt-1 text-sm text-gray-500">
              {{ filters.course_id || filters.active_only ? 'No sessions match your filters.' : 'Start a new conversation with your AI tutor for any of your courses.' }}
            </p>
            <div class="mt-6">
              <Link
                :href="route('student.chat.create')"
                class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 border border-transparent rounded-lg font-semibold text-white shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200"
              >
                Start New Chat
              </Link>
              <button
                v-if="filters.course_id || filters.active_only"
                @click="clearFilters"
                class="ml-3 inline-flex items-center px-4 py-2.5 border border-emerald-300 rounded-lg font-semibold text-emerald-700 bg-white hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200"
              >
                Clear Filters
              </button>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="chat_sessions.data.length" class="mt-8">
          <Pagination :links="chat_sessions.links" />
        </div>
      </div>
    </div>
  </StudentLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import Pagination from '@/Components/Pagination.vue'
import {
  PlusIcon,
  XMarkIcon,
  ChatBubbleLeftRightIcon,
  AcademicCapIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  chat_sessions: Object,
  courses: Array,
  filters: Object,
})

const filters = ref({
  course_id: props.filters.course_id || '',
  active_only: props.filters.active_only || false,
})

const filter = () => {
  router.get(route('student.chat.index'), filters.value, {
    preserveState: true,
    replace: true,
  })
}

const clearFilters = () => {
  filters.value = { course_id: '', active_only: false }
  filter()
}

const getSessionTitle = (session) => {
  if (session.course_outline) {
    return session.course_outline.title
  } else if (session.course) {
    return session.course.title
  }
  return 'General Chat'
}

const getSessionContext = (session) => {
  if (session.course_outline && session.course_outline.module) {
    return `${session.course.title} • ${session.course_outline.module.title}`
  } else if (session.course) {
    return session.course.title
  }
  return 'General learning assistance'
}

const formatRelativeTime = (dateString) => {
  const date = new Date(dateString)
  const now = new Date()
  const diffInMinutes = Math.floor((now - date) / (1000 * 60))

  if (diffInMinutes < 1) return 'Just now'
  if (diffInMinutes < 60) return `${diffInMinutes}m ago`

  const diffInHours = Math.floor(diffInMinutes / 60)
  if (diffInHours < 24) return `${diffInHours}h ago`

  const diffInDays = Math.floor(diffInHours / 24)
  if (diffInDays < 7) return `${diffInDays}d ago`

  return date.toLocaleDateString()
}

const closeSession = async (session) => {
  if (confirm('Are you sure you want to close this chat session?')) {
    try {
      await router.delete(route('student.chat.close', session.id))
      router.reload()
    } catch (error) {
      console.error('Failed to close session:', error)
    }
  }
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
