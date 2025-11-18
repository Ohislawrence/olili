<template>
    <StudentLayout>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 to-emerald-50">
    <Head title="AI Tutor Chat" />

    <div class="flex h-screen">
      <!-- Chat Sidebar -->
      <div class="w-80 bg-white border-r border-gray-200 flex flex-col">
        <div class="p-4 border-b border-gray-200">
          <h2 class="text-lg font-bold text-gray-900">AI Tutor</h2>
          <p class="text-sm text-gray-600 mt-1">
            Ask questions about your course
          </p>
        </div>

        <!-- Context Selector -->
        <div class="p-4 border-b border-gray-200">
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Course Context
          </label>
          <select
            v-model="selectedOutlineId"
            @change="updateContext"
            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 shadow-sm"
          >
            <option value="">General Questions</option>
            <option
              v-for="outline in related_outlines"
              :key="outline.id"
              :value="outline.id"
            >
              {{ outline.title }}
            </option>
          </select>
          <p class="text-xs text-gray-500 mt-2">
            Setting context helps the AI provide more relevant answers
          </p>
        </div>

        <!-- Chat Sessions List -->
        <div class="flex-1 overflow-auto p-4">
          <h3 class="text-sm font-medium text-gray-900 mb-3">
            Recent Chats
          </h3>
          <div class="space-y-2">
            <Link
              v-for="session in recentSessions"
              :key="session.id"
              :href="route('student.chat.show', session.id)"
              class="block p-3 rounded-lg border border-gray-200 hover:border-emerald-300 hover:bg-emerald-50 transition-colors"
              :class="{
                'border-emerald-300 bg-emerald-50': session.id === chat_session.id,
              }"
            >
              <p class="text-sm font-medium text-gray-900 truncate">
                {{ session.topic_context || 'General Chat' }}
              </p>
              <p class="text-xs text-gray-500 mt-1">
                {{ formatDate(session.last_activity_at) }}
              </p>
            </Link>
          </div>
        </div>

        <!-- Actions -->
        <div class="p-4 border-t border-gray-200">
          <Link
            :href="route('student.chat.create')"
            class="w-full flex items-center justify-center px-4 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-emerald-600 to-teal-600 rounded-lg hover:from-emerald-700 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 shadow-sm hover:shadow-md transition-all duration-200"
          >
            <PlusIcon class="h-4 w-4 mr-2" />
            New Chat
          </Link>
        </div>
      </div>

      <!-- Main Chat Area -->
      <div class="flex-1 flex flex-col">
        <!-- Chat Header -->
        <div class="bg-white border-b border-gray-200 px-6 py-4">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-xl font-bold text-gray-900">
                {{ chat_session.topic_context || 'AI Tutor' }}
              </h1>
              <p
                v-if="chat_session.course"
                class="text-sm text-gray-600 mt-1"
              >
                {{ chat_session.course.title }}
              </p>
            </div>
            <button
              @click="closeSession"
              class="inline-flex items-center px-3 py-2 border border-emerald-300 rounded-lg text-sm font-medium text-emerald-700 bg-white hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 shadow-sm hover:shadow-md transition-colors"
            >
              <XMarkIcon class="h-4 w-4 mr-1" />
              End Chat
            </button>
          </div>
        </div>

        <!-- Messages -->
        <div
          ref="messagesContainer"
          class="flex-1 overflow-auto p-6 space-y-4"
        >
          <div
            v-for="message in chat_session.messages"
            :key="message.id"
            class="flex"
            :class="{
              'justify-end': message.sender_type === 'user',
              'justify-start': message.sender_type !== 'user',
            }"
          >
            <div
              class="max-w-3/4 rounded-xl px-4 py-3 shadow-sm"
              :class="{
                'bg-gradient-to-r from-emerald-600 to-teal-600 text-white': message.sender_type === 'user',
                'bg-white border border-gray-200': message.sender_type !== 'user',
              }"
            >
              <div v-html="formatMessage(message.message)"></div>
              <p
                class="text-xs mt-1"
                :class="{
                  'text-emerald-200': message.sender_type === 'user',
                  'text-gray-500': message.sender_type !== 'user',
                }"
              >
                {{ formatTime(message.created_at) }}
                <span
                  v-if="message.sender_type === 'ai' && !message.is_related_to_topic"
                  class="ml-2 text-amber-500"
                >
                  (May be off-topic)
                </span>
              </p>
            </div>
          </div>

          <!-- Typing Indicator -->
          <div v-if="isTyping" class="flex justify-start">
            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3 shadow-sm">
              <div class="flex space-x-1">
                <div
                  class="w-2 h-2 bg-emerald-400 rounded-full animate-bounce"
                  style="animation-delay: 0.1s"
                ></div>
                <div
                  class="w-2 h-2 bg-emerald-400 rounded-full animate-bounce"
                  style="animation-delay: 0.2s"
                ></div>
                <div
                  class="w-2 h-2 bg-emerald-400 rounded-full animate-bounce"
                  style="animation-delay: 0.3s"
                ></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Message Input -->
        <div class="border-t border-gray-200 p-4 bg-white">
          <form @submit.prevent="sendMessage" class="flex space-x-4">
            <div class="flex-1">
              <textarea
                v-model="newMessage"
                @keydown.enter.exact.prevent="sendMessage"
                rows="2"
                placeholder="Ask a question about your course..."
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 resize-none shadow-sm"
                :disabled="isSending"
              ></textarea>
            </div>
            <button
              type="submit"
              :disabled="!newMessage.trim() || isSending"
              class="self-end px-6 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-lg hover:from-emerald-700 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 shadow-sm hover:shadow-md"
            >
              <PaperAirplaneIcon class="h-4 w-4" />
            </button>
          </form>
          <p class="text-xs text-gray-500 mt-2">
            Press Enter to send. The AI will focus on your current course context.
          </p>
        </div>
      </div>
    </div>
  </div>
 </StudentLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, nextTick, onMounted, onUnmounted } from 'vue'
import StudentLayout from '@/Layouts/StudentLayout.vue'
import {
  PlusIcon,
  XMarkIcon,
  PaperAirplaneIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  chat_session: Object,
  related_outlines: Array,
  recent_sessions: Array,
})

const messagesContainer = ref(null)
const newMessage = ref('')
const isSending = ref(false)
const isTyping = ref(false)
const selectedOutlineId = ref(props.chat_session.course_outline_id)
let pollingInterval = null

const formatMessage = (message) => {
  // Simple markdown formatting
  return message
    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
    .replace(/\*(.*?)\*/g, '<em>$1</em>')
    .replace(/`(.*?)`/g, '<code class="bg-gray-100 px-1 rounded">$1</code>')
    .replace(/\n/g, '<br>')
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
  })
}

const formatTime = (dateString) => {
  return new Date(dateString).toLocaleTimeString('en-US', {
    hour: '2-digit',
    minute: '2-digit',
  })
}

const scrollToBottom = () => {
  nextTick(() => {
    if (messagesContainer.value) {
      messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
    }
  })
}

const sendMessage = async () => {
  if (!newMessage.value.trim() || isSending.value) return

  const message = newMessage.value.trim()
  newMessage.value = ''
  isSending.value = true
  isTyping.value = true

  try {
    const response = await axios.post(
      route('student.chat.send-message', props.chat_session.id),
      {
        message: message,
      }
    )

    if (response.data.success) {
      // Reload the page to get updated messages
      router.reload()
    }
  } catch (error) {
    console.error('Failed to send message:', error)
    isTyping.value = false
  } finally {
    isSending.value = false
  }
}

const updateContext = async () => {
  try {
    await axios.post(
      route('student.chat.update-context', props.chat_session.id),
      {
        outline_id: selectedOutlineId.value,
      }
    )
  } catch (error) {
    console.error('Failed to update context:', error)
  }
}

const closeSession = () => {
  router.post(route('student.chat.close', props.chat_session.id))
}

// Poll for new messages
const startPolling = () => {
  pollingInterval = setInterval(async () => {
    try {
      const response = await axios.get(
        route('student.chat.messages', props.chat_session.id)
      )
      // Update messages if there are new ones
      if (response.data.messages.length !== props.chat_session.messages.length) {
        router.reload()
      }
    } catch (error) {
      console.error('Failed to poll messages:', error)
    }
  }, 5000) // Poll every 5 seconds
}

onMounted(() => {
  scrollToBottom()
  startPolling()
})

onUnmounted(() => {
  if (pollingInterval) {
    clearInterval(pollingInterval)
  }
})
</script>
