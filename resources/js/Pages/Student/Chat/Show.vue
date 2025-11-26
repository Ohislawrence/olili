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
                            :disabled="isUpdatingContext"
                        >
                            <option value="">General Questions</option>
                            <option
                                v-for="outline in related_topics"
                                :key="outline.id"
                                :value="outline.id"
                            >
                                {{ outline.title }}
                            </option>
                        </select>
                        <div v-if="isUpdatingContext" class="text-xs text-emerald-600 mt-2 flex items-center">
                            <div class="animate-spin rounded-full h-3 w-3 border-b-2 border-emerald-600 mr-2"></div>
                            Updating context...
                        </div>
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
                            <div class="flex space-x-2">
                                <button
                                    @click="loadMessages"
                                    class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 shadow-sm hover:shadow-md transition-colors"
                                    :disabled="isLoadingMessages"
                                >
                                    <ArrowPathIcon class="h-4 w-4 mr-1" />
                                    {{ isLoadingMessages ? 'Refreshing...' : 'Refresh' }}
                                </button>
                                <button
                                    @click="closeSession"
                                    class="inline-flex items-center px-3 py-2 border border-emerald-300 rounded-lg text-sm font-medium text-emerald-700 bg-white hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 shadow-sm hover:shadow-md transition-colors"
                                >
                                    <XMarkIcon class="h-4 w-4 mr-1" />
                                    End Chat
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Messages -->
                    <div
                        ref="messagesContainer"
                        class="flex-1 overflow-auto p-6 space-y-4"
                    >
                        <!-- Welcome Message -->
                        <div v-if="messages.length === 0 && !isLoadingMessages" class="text-center py-12">
                            <div class="mx-auto h-16 w-16 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full flex items-center justify-center mb-4">
                                <ChatBubbleLeftRightIcon class="h-8 w-8 text-white" />
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                Welcome to AI Tutor!
                            </h3>
                            <p class="text-sm text-gray-600 max-w-md mx-auto">
                                Ask me anything about your course. I'm here to help you learn and understand the material better.
                            </p>
                        </div>

                        <!-- Loading Messages -->
                        <div v-if="isLoadingMessages" class="text-center py-8">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-emerald-600 mx-auto mb-4"></div>
                            <p class="text-sm text-gray-500">Loading messages...</p>
                        </div>

                        <!-- Chat Messages -->
                        <div
                            v-for="message in messages"
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
                                <div class="flex items-center space-x-2">
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
                                    <span class="text-xs text-gray-500">Oli Tutor is typing...</span>
                                </div>
                            </div>
                        </div>

                        <!-- Error Message -->
                        <div v-if="chatError" class="flex justify-center">
                            <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 max-w-md">
                                <div class="flex items-center">
                                    <ExclamationTriangleIcon class="h-4 w-4 text-red-500 mr-2" />
                                    <p class="text-sm text-red-700">{{ chatError }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Message Input -->
                    <div class="border-t border-gray-200 p-4 bg-white">
                        <form @submit.prevent="sendMessage" class="flex space-x-4">
                            <div class="flex-1 relative">
                                <textarea
                                    v-model="newMessage"
                                    @keydown.enter.exact.prevent="sendMessage"
                                    rows="2"
                                    placeholder="Ask a question about your course..."
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 resize-none shadow-sm transition-colors"
                                    :disabled="isSending || isLoadingMessages"
                                    :class="{
                                        'border-red-300': newMessage.length > 1000,
                                        'hover:border-gray-400': !isSending && !isLoadingMessages
                                    }"
                                ></textarea>
                                <div class="absolute bottom-2 right-2">
                                    <span class="text-xs" :class="newMessage.length > 1000 ? 'text-red-500' : 'text-gray-400'">
                                        {{ newMessage.length }}/1000
                                    </span>
                                </div>
                            </div>
                            <button
                                type="submit"
                                :disabled="!newMessage.trim() || isSending || isLoadingMessages || newMessage.length > 1000"
                                class="self-end px-6 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-lg hover:from-emerald-700 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 shadow-sm hover:shadow-md flex items-center justify-center min-w-[100px]"
                                :class="{
                                    'hover:scale-105': !isSending && newMessage.trim() && newMessage.length <= 1000,
                                    'transform scale-95': isSending
                                }"
                            >
                                <PaperAirplaneIcon v-if="!isSending" class="h-4 w-4" />
                                <div v-else class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
                                <span class="ml-2">{{ isSending ? 'Sending...' : 'Send' }}</span>
                            </button>
                        </form>
                        <p v-if="newMessage.length > 1000" class="text-xs text-red-500 mt-2">
                            Message too long. Please keep under 1000 characters.
                        </p>
                        <p v-else class="text-xs text-gray-500 mt-2">
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
import { ref, nextTick, onMounted, onUnmounted, reactive, watch } from 'vue'
import StudentLayout from '@/Layouts/StudentLayout.vue'
import {
  PlusIcon,
  XMarkIcon,
  PaperAirplaneIcon,
  ArrowPathIcon,
  ChatBubbleLeftRightIcon,
  ExclamationTriangleIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  chat_session: Object,
  related_topics: Array,
})

// Reactive state
const messagesContainer = ref(null)
const newMessage = ref('')
const isSending = ref(false)
const isTyping = ref(false)
const isLoadingMessages = ref(false)
const isUpdatingContext = ref(false)
const selectedOutlineId = ref(props.chat_session.course_outline_id || '')
const chatError = ref(null)
const messages = reactive([])
const recentSessions = ref([]) // Added missing reactive property
let pollingInterval = null

// Initialize messages from props
messages.push(...props.chat_session.messages)

// Watch for selectedOutlineId changes and update context
watch(selectedOutlineId, (newValue, oldValue) => {
  if (newValue !== oldValue) {
    updateContext()
  }
})

const formatMessage = (message) => {
  if (!message) return ''
  return message
    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
    .replace(/\*(.*?)\*/g, '<em>$1</em>')
    .replace(/`(.*?)`/g, '<code class="bg-gray-100 px-1 rounded text-xs">$1</code>')
    .replace(/\n/g, '<br>')
}

const formatTime = (dateString) => {
  return new Date(dateString).toLocaleTimeString('en-US', {
    hour: '2-digit',
    minute: '2-digit',
  })
}

// Added missing formatDate method
const formatDate = (dateString) => {
  const date = new Date(dateString)
  const now = new Date()
  const diffTime = Math.abs(now - date)
  const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24))

  if (diffDays === 0) {
    return 'Today'
  } else if (diffDays === 1) {
    return 'Yesterday'
  } else if (diffDays < 7) {
    return `${diffDays} days ago`
  } else {
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
  }
}

const scrollToBottom = () => {
  nextTick(() => {
    if (messagesContainer.value) {
      messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
    }
  })
}

const loadMessages = async () => {
  if (isLoadingMessages.value) return

  isLoadingMessages.value = true
  chatError.value = null

  try {
    // FIXED: Use correct route name and parameters
    const response = await axios.get(
      route('chat.messages', {
        chatSession: props.chat_session.id
      })
    )

    if (response.data.success) {
      // Clear current messages and add new ones
      messages.splice(0, messages.length, ...response.data.messages)
      scrollToBottom()

      // Update session data if provided
      if (response.data.session) {
        props.chat_session.course_outline_id = response.data.session.course_outline_id
        props.chat_session.topic_context = response.data.session.topic_context
        // Keep select in sync
        selectedOutlineId.value = response.data.session.course_outline_id || ''
      }
    } else {
      throw new Error(response.data.error || 'Failed to load messages')
    }
  } catch (error) {
    console.error('Failed to load messages:', error)
    chatError.value = error.response?.data?.error || 'Failed to load messages. Please try again.'
  } finally {
    isLoadingMessages.value = false
  }
}

const sendMessage = async () => {
  if (!newMessage.value.trim() || isSending.value || newMessage.value.length > 1000) return

  const messageText = newMessage.value.trim()
  newMessage.value = ''
  isSending.value = true
  isTyping.value = true
  chatError.value = null

  // Add user message immediately for better UX
  const userMessage = {
    id: Date.now(), // Temporary ID
    sender_type: 'user',
    message: messageText,
    created_at: new Date().toISOString(),
    is_related_to_topic: true
  }
  messages.push(userMessage)
  scrollToBottom()

  try {
    // FIXED: Use correct route name and parameters
    const response = await axios.post(
      route('courses.chat.send-message', {
        course: props.chat_session.course_id,
        chatSession: props.chat_session.id
      }),
      {
        message: messageText,
      }
    )

    if (response.data.success) {
      // Remove temporary message and add the real one from server
      const tempMessageIndex = messages.findIndex(msg => msg.id === userMessage.id)
      if (tempMessageIndex > -1) {
        messages.splice(tempMessageIndex, 1)
      }

      messages.push(response.data.message)

      // Update session data if provided
      if (response.data.session) {
        Object.assign(props.chat_session, response.data.session)
        // Update selected outline to match the session
        selectedOutlineId.value = props.chat_session.course_outline_id || ''
      }
    } else {
      throw new Error(response.data.message || 'Failed to send message')
    }
  } catch (error) {
    console.error('Failed to send message:', error)

    // Remove the temporary user message on error
    const tempMessageIndex = messages.findIndex(msg => msg.id === userMessage.id)
    if (tempMessageIndex > -1) {
      messages.splice(tempMessageIndex, 1)
    }

    // Add error message
    chatError.value = error.response?.data?.message || 'Failed to send message. Please try again.'

    // Add error message from AI
    const errorMessage = {
      id: Date.now() + 1,
      sender_type: 'ai',
      message: "I'm sorry, I'm having trouble responding right now. Please try again in a moment.",
      created_at: new Date().toISOString(),
      is_related_to_topic: true
    }
    messages.push(errorMessage)
  } finally {
    isSending.value = false
    isTyping.value = false
    scrollToBottom()
  }
}

const updateContext = async () => {
  if (isUpdatingContext.value) return

  isUpdatingContext.value = true
  chatError.value = null

  try {
    console.log('Updating context to:', selectedOutlineId.value)

    // FIXED: Use correct route name
    const response = await axios.post(
      route('chat.update-context', props.chat_session.id),
      {
        outline_id: selectedOutlineId.value || null,
      }
    )

    if (response.data.success) {
      // Update session context locally
      props.chat_session.course_outline_id = selectedOutlineId.value
      props.chat_session.topic_context = response.data.new_context?.topic_title || 'General Questions'

      // Add system message about context change
      const topicName = selectedOutlineId.value
        ? props.related_topics.find(t => t.id == selectedOutlineId.value)?.title
        : 'General Questions'

      const systemMessage = {
        id: Date.now(),
        sender_type: 'ai',
        message: `🎯 <strong>Context Updated:</strong> I'm now focusing on <em>"${topicName}"</em>. Ask me anything specific about this topic!`,
        created_at: new Date().toISOString(),
        is_related_to_topic: true,
        is_system: true
      }
      messages.push(systemMessage)
      scrollToBottom()

      console.log('Context updated successfully')
    } else {
      throw new Error(response.data.message || 'Failed to update context')
    }
  } catch (error) {
    console.error('Context update failed:', error)
    chatError.value = error.response?.data?.message || 'Failed to update context. Please try again.'

    // Revert the select value on error - but only if it's different from current session
    if (selectedOutlineId.value !== (props.chat_session.course_outline_id || '')) {
      selectedOutlineId.value = props.chat_session.course_outline_id || ''
    }
  } finally {
    isUpdatingContext.value = false
  }
}

const closeSession = async () => {
  if (confirm('Are you sure you want to close this chat session?')) {
    try {
      // FIXED: Use correct route name
      await axios.delete(route('chat.close', props.chat_session.id))
      router.visit(route('chat.index'))
    } catch (error) {
      console.error('Failed to close session:', error)
      chatError.value = 'Failed to close session. Please try again.'
    }
  }
}

// Poll for new messages
const startPolling = () => {
  pollingInterval = setInterval(async () => {
    if (isSending.value || isTyping.value || isUpdatingContext.value) return

    try {
      // FIXED: Use correct route name
      const response = await axios.get(
        route('chat.messages', {
          chatSession: props.chat_session.id
        })
      )

      if (response.data.success) {
        // Only update if messages changed
        const currentMessageIds = messages.map(m => m.id).sort()
        const newMessageIds = response.data.messages.map(m => m.id).sort()
        const messagesChanged = JSON.stringify(currentMessageIds) !== JSON.stringify(newMessageIds)

        if (messagesChanged) {
          messages.splice(0, messages.length, ...response.data.messages)
          scrollToBottom()
        }

        // Update session context
        if (response.data.session) {
          const currentOutlineId = props.chat_session.course_outline_id || ''
          const newOutlineId = response.data.session.course_outline_id || ''
          if (currentOutlineId !== newOutlineId) {
            props.chat_session.course_outline_id = newOutlineId
            selectedOutlineId.value = newOutlineId
          }
        }
      }
    } catch (error) {
      console.error('Failed to poll messages:', error)
      // Don't show error for polling failures
    }
  }, 3000)
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
