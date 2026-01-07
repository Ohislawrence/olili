<template>
  <div>
    <!-- Chat Trigger Button -->
    <button
      @click="openChat"
      class="fixed z-40 flex items-center gap-2 px-4 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 group"
      :class="[
        'right-4 md:right-6',
        isMobile ? 'bottom-20' : 'bottom-6'
      ]"
      title="Ask AI Tutor"
    >
      <ChatBubbleLeftRightIcon class="h-5 w-5 group-hover:scale-110 transition-transform" />
      <span class="text-sm font-semibold whitespace-nowrap">Ask the Tutor</span>
    </button>

    <!-- Chat Popup -->
    <div
        v-if="isOpen"
        class="fixed inset-0 z-50 overflow-hidden"
        aria-labelledby="chat-modal"
        role="dialog"
        aria-modal="true"
        >
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeChat"></div>

        <!-- Chat Container - Updated for right side positioning -->
        <div class="fixed inset-y-0 right-0 max-w-full flex">
            <div class="w-screen max-w-md h-full transform transition-transform duration-300 ease-in-out"
                :class="isOpen ? 'translate-x-0' : 'translate-x-full'">
            <div class="h-full flex flex-col bg-white shadow-xl rounded-l-2xl overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-emerald-600 to-teal-600 px-4 py-6 sm:px-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 bg-white/20 rounded-full flex items-center justify-center">
                        <ChatBubbleLeftRightIcon class="h-6 w-6 text-white" />
                        </div>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-white" id="chat-modal">
                        Oli Ai Tutor
                        </h2>
                        <p class="mt-1 text-sm text-emerald-100">
                        {{ course.title }}
                        </p>
                    </div>
                    </div>
                    <button
                    @click="closeChat"
                    class="rounded-md p-2 text-emerald-200 hover:text-white hover:bg-white/10 transition-colors focus:outline-none focus:ring-2 focus:ring-white"
                    >
                    <span class="sr-only">Close panel</span>
                    <XMarkIcon class="h-5 w-5" />
                    </button>
                </div>

                <!-- Context Selector -->
                <div class="mt-4" v-if="safeAvailableTopics.length > 0">
                    <label class="block text-xs font-medium text-emerald-100 mb-2">
                    Focus on specific topic:
                    </label>
                    <div class="relative">
                    <select
                        v-model="selectedOutlineId"
                        @change="updateContext"
                        class="w-full px-3 py-2 text-sm border border-emerald-300 rounded-lg focus:ring-2 focus:ring-white focus:border-white bg-emerald-500 text-white placeholder-emerald-200 appearance-none pr-8 transition-colors"
                        :disabled="isTyping"
                    >
                        <option value="">General Course Questions</option>
                        <option
                        v-for="topic in safeAvailableTopics"
                        :key="topic.id"
                        :value="topic.id"
                        >
                        {{ topic.full_title || topic.title }}
                        </option>
                    </select>
                    <div class="absolute right-2 top-1/2 transform -translate-y-1/2 pointer-events-none">
                        <ChevronDownIcon class="h-4 w-4 text-emerald-200" />
                    </div>
                    <!-- Loading indicator -->
                    <div v-if="isTyping" class="absolute right-8 top-1/2 transform -translate-y-1/2">
                        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
                    </div>
                    </div>
                    <p v-if="isTyping" class="text-xs text-emerald-200 mt-1 flex items-center">
                    <div class="animate-spin rounded-full h-3 w-3 border-b-2 border-emerald-200 mr-1"></div>
                    Updating context...
                    </p>
                </div>
                </div>

            <!-- Messages -->
            <div
              ref="messagesContainer"
              class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50/50"
              :class="isMobile ? 'pb-20' : ''"
            >
              <!-- Welcome Message -->
              <div v-if="messages.length === 0 && !isLoading" class="text-center py-8">
                <div class="mx-auto h-16 w-16 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full flex items-center justify-center mb-4">
                  <ChatBubbleLeftRightIcon class="h-8 w-8 text-white" />
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                  Welcome to Oli AI Tutor!
                </h3>
                <p class="text-sm text-gray-600 max-w-xs mx-auto">
                  Ask me anything about <strong class="text-emerald-600">{{ course.title }}</strong>. I'm here to help you learn!
                </p>
              </div>

              <!-- Loading State -->
              <div v-if="isLoading && messages.length === 0" class="text-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-emerald-600 mx-auto mb-4"></div>
                <p class="text-sm text-gray-500">Loading chat...</p>
              </div>

              <!-- Chat Messages -->
              <div
                v-for="(message, index) in messages"
                :key="message.id || index"
                class="flex"
                :class="{
                  'justify-end': message.sender_type === 'user',
                  'justify-start': message.sender_type === 'ai',
                }"
              >
                <div
                  class="max-w-[85%] rounded-2xl px-4 py-3 shadow-sm transition-all duration-200"
                  :class="{
                    'bg-gradient-to-r from-emerald-600 to-teal-600 text-white': message.sender_type === 'user',
                    'bg-white border border-gray-200 shadow-sm': message.sender_type === 'ai',
                    'border-l-4 border-l-emerald-500': message.sender_type === 'ai' && message.is_system,
                  }"
                >
                  <div class="text-sm leading-relaxed" v-html="formatMessage(message.message)"></div>
                  <p
                    class="text-xs mt-2 flex items-center"
                    :class="{
                      'text-emerald-200': message.sender_type === 'user',
                      'text-gray-500': message.sender_type === 'ai',
                    }"
                  >
                    {{ formatTime(message.created_at) }}
                    <span
                      v-if="message.sender_type === 'ai' && !message.is_related_to_topic"
                      class="ml-2 px-1.5 py-0.5 bg-amber-100 text-amber-700 rounded text-xs"
                    >
                      May be off-topic
                    </span>
                  </p>
                </div>
              </div>

              <!-- Typing Indicator -->
              <div v-if="isTyping" class="flex justify-start">
                <div class="bg-white border border-gray-200 rounded-2xl px-4 py-3 shadow-sm">
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
                    <span class="text-xs text-gray-500">AI Tutor is typing...</span>
                  </div>
                </div>
              </div>

              <!-- Error Message -->
              <div v-if="chatError" class="flex justify-center">
                <div class="bg-red-50 border border-red-200 rounded-2xl px-4 py-3 max-w-[85%]">
                  <div class="flex items-center">
                    <ExclamationTriangleIcon class="h-4 w-4 text-red-500 mr-2" />
                    <p class="text-sm text-red-700">{{ chatError }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Message Input -->
            <div class="border-t border-gray-200 p-4 bg-white" :class="isMobile ? 'pb-6' : ''">
              <form @submit.prevent="sendMessage" class="flex space-x-3">
                <div class="flex-1 relative">
                  <textarea
                    v-model="newMessage"
                    @keydown.enter.exact.prevent="sendMessage"
                    rows="2"
                    placeholder="Ask a question about this course..."
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 resize-none text-sm shadow-sm transition-colors"
                    :disabled="isSending || isLoading"
                  ></textarea>
                  <div class="absolute bottom-2 right-2">
                    <span class="text-xs text-gray-400" :class="{ 'text-emerald-600': newMessage.length > 0 }">
                      {{ newMessage.length }}/500
                    </span>
                  </div>
                </div>
                <button
                  type="submit"
                  :disabled="!newMessage.trim() || isSending || isLoading || newMessage.length > 500"
                  class="self-end px-4 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-xl hover:from-emerald-700 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 shadow-sm flex items-center justify-center min-w-[44px]"
                  :class="{
                    'hover:scale-105': !isSending && newMessage.trim(),
                    'transform scale-95': isSending
                  }"
                >
                  <PaperAirplaneIcon v-if="!isSending" class="h-4 w-4" />
                  <div v-else class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
                </button>
              </form>
              <p v-if="newMessage.length > 500" class="text-xs text-red-500 mt-2 text-center">
                Message too long. Please keep under 500 characters.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, nextTick, onMounted, onUnmounted } from 'vue'
import { marked } from "marked"
import DOMPurify from "dompurify"
import {
  ChatBubbleLeftRightIcon,
  XMarkIcon,
  PaperAirplaneIcon,
  ChevronDownIcon,
  ExclamationTriangleIcon,
} from '@heroicons/vue/24/outline'
import hljs from "highlight.js"
import "highlight.js/styles/github-dark.css"

const props = defineProps({
  course: {
    type: Object,
    required: true
  },
  availableTopics: {
    type: Array,
    default: () => []
  }
})

// Reactive state
const isOpen = ref(false)
const newMessage = ref('')
const isSending = ref(false)
const isTyping = ref(false)
const isLoading = ref(false)
const selectedOutlineId = ref('')
const messages = ref([])
const currentSession = ref(null)
const messagesContainer = ref(null)
const chatError = ref(null)
const isMobile = ref(false)

// Safe computed for available topics
const safeAvailableTopics = computed(() => {
  return props.availableTopics || []
})

const previousOutlineId = ref('')

// Check if mobile
const checkMobile = () => {
  isMobile.value = window.innerWidth < 768
}

// Open chat and initialize session
const openChat = async () => {
  isOpen.value = true
  chatError.value = null
  isLoading.value = true

  try {
    await initializeSession()
  } catch (error) {
    console.error('Failed to initialize chat:', error)
    chatError.value = 'Failed to load chat. Please try again.'
  } finally {
    isLoading.value = false
  }

  // Scroll to bottom after opening
  nextTick(() => {
    scrollToBottom()
  })
}

// Close chat
const closeChat = () => {
  isOpen.value = false
  newMessage.value = ''
  chatError.value = null
}

// Initialize chat session for the course
const initializeSession = async () => {
  try {
    const response = await axios.get(route('student.courses.tutor.session', { course: props.course.id }))

    if (response.data.success) {
      currentSession.value = response.data.session
      messages.value = response.data.messages || []
    } else {
      throw new Error(response.data.error || 'Failed to load session')
    }
  } catch (error) {
    console.error('Failed to initialize session:', error)
    throw error
  }
}

// Send message
const sendMessage = async () => {
  if (!newMessage.value.trim() || isSending.value || newMessage.value.length > 500) return

  const messageText = newMessage.value.trim()
  newMessage.value = ''
  isSending.value = true
  isTyping.value = true
  chatError.value = null

  // Add user message immediately
  const userMessage = {
    id: Date.now(),
    sender_type: 'user',
    message: messageText,
    created_at: new Date().toISOString(),
    is_related_to_topic: true
  }
  messages.value.push(userMessage)

  scrollToBottom()

  try {
    const response = await axios.post(
      route('student.courses.tutor.send-message', { course: props.course.id }),
      {
        message: messageText,
        outline_id: selectedOutlineId.value || null
      }
    )

    if (response.data.success) {
      // Add AI response
      const aiMessage = {
        id: response.data.message.id || Date.now() + 1,
        sender_type: 'ai',
        message: response.data.message.message,
        created_at: response.data.message.created_at,
        is_related_to_topic: response.data.message.is_related_to_topic,
        metadata: response.data.message.metadata
      }
      messages.value.push(aiMessage)

      // Update session if needed
      if (response.data.session_id && !currentSession.value) {
        currentSession.value = { id: response.data.session_id }
      }
    } else {
      throw new Error(response.data.error || 'Failed to send message')
    }
  } catch (error) {
    console.error('Failed to send message:', error)
    // Add error message
    const errorMessage = {
      id: Date.now() + 1,
      sender_type: 'ai',
      message: "I'm sorry, I'm having trouble responding right now. Please try again.",
      created_at: new Date().toISOString(),
      is_related_to_topic: true
    }
    messages.value.push(errorMessage)
    chatError.value = 'Failed to send message. Please try again.'
  } finally {
    isSending.value = false
    isTyping.value = false
    scrollToBottom()
  }
}

// Update chat context
const updateContext = async () => {
  if (!currentSession.value) {
    // If no session exists, create one with the selected context
    await createSessionWithContext()
    return
  }

  chatError.value = null
  isTyping.value = true

  // Store previous value for potential revert
  previousOutlineId.value = currentSession.value.course_outline_id

  try {
    const response = await axios.post(
      route('student.courses.tutor.update-context', {
        course: props.course.id
      }),
      {
        outline_id: selectedOutlineId.value || null,
        session_id: currentSession.value.id
      }
    )

    if (response.data.success) {
      // Update session context locally
      if (currentSession.value) {
        currentSession.value.course_outline_id = selectedOutlineId.value
        currentSession.value.topic_context = response.data.new_context
      }

      // Add system message about context change
      const topicName = selectedOutlineId.value
        ? safeAvailableTopics.value.find(t => t.id === selectedOutlineId.value)?.title
        : 'General Course Questions'

      const systemMessage = {
        id: Date.now(),
        sender_type: 'ai',
        message: `📚 <strong>Context Updated:</strong> Now focusing on <em>"${topicName}"</em>. Ask me anything specific about this topic!`,
        created_at: new Date().toISOString(),
        is_related_to_topic: true,
        is_system: true
      }
      messages.value.push(systemMessage)
      scrollToBottom()

    } else {
      throw new Error(response.data.error || 'Failed to update context')
    }

  } catch (error) {
    console.error('Context update failed:', error)

    // Revert the select value
    selectedOutlineId.value = previousOutlineId.value

    // Show user-friendly error message
    if (error.response?.data?.error) {
      chatError.value = error.response.data.error
    } else if (error.response?.status === 404) {
      chatError.value = 'The selected topic was not found in this course.'
    } else if (error.response?.status === 403) {
      chatError.value = 'You do not have permission to modify this chat session.'
    } else if (error.code === 'NETWORK_ERROR') {
      chatError.value = 'Network error. Please check your connection and try again.'
    } else {
      chatError.value = 'Failed to update chat context. Please try again.'
    }

    // Show error for 5 seconds then clear
    setTimeout(() => {
      chatError.value = null
    }, 5000)

  } finally {
    isTyping.value = false
  }
}

// Create new session with specific context
const createSessionWithContext = async () => {
  chatError.value = null
  isTyping.value = true

  try {
    // Create session by sending a message with the context
    const welcomeMessage = selectedOutlineId.value
      ? `I'd like to focus on this specific topic.`
      : `I'd like to discuss general course questions.`

    const response = await axios.post(
      route('student.courses.tutor.send-message', {
        course: props.course.id
      }),
      {
        message: welcomeMessage,
        outline_id: selectedOutlineId.value || null
      }
    )

    if (response.data.success) {
      currentSession.value = { id: response.data.session_id }

      // Add the AI response
      const aiMessage = {
        id: response.data.message.id || Date.now() + 1,
        sender_type: 'ai',
        message: response.data.message.message,
        created_at: response.data.message.created_at,
        is_related_to_topic: response.data.message.is_related_to_topic,
        metadata: response.data.message.metadata
      }
      messages.value.push(aiMessage)

      // Add context confirmation message
      const topicName = selectedOutlineId.value
        ? safeAvailableTopics.value.find(t => t.id === selectedOutlineId.value)?.title
        : 'General Course Questions'

      const contextMessage = {
        id: Date.now() + 2,
        sender_type: 'ai',
        message: `🎯 <strong>Chat Context Set:</strong> I'll focus on <em>"${topicName}"</em>. What would you like to know about this topic?`,
        created_at: new Date().toISOString(),
        is_related_to_topic: true,
        is_system: true
      }
      messages.value.push(contextMessage)

      scrollToBottom()

    } else {
      throw new Error(response.data.error || 'Failed to create session')
    }

  } catch (error) {
    console.error('Failed to create session with context:', error)
    chatError.value = 'Failed to start chat with selected context. Please try again.'

    // Revert the select value
    selectedOutlineId.value = ''

  } finally {
    isTyping.value = false
  }
}

// Helper functions

function formatMessage(message) {
  if (!message) return ""

  // Remove model artifacts
  const cleaned = message.replace(/<\｜begin▁of▁sentence\｜>/g, "")

  // Configure marked ONCE per call (safe here)
  marked.setOptions({
    gfm: true,
    breaks: true,
    headerIds: false,
    langPrefix: "language-",

    highlight(code, lang) {
      // If language is known, use it
      if (lang && hljs.getLanguage(lang)) {
        return hljs.highlight(code, { language: lang }).value
      }

      // Fallback: auto-detect
      return hljs.highlightAuto(code).value
    }
  })

  const html = marked.parse(cleaned)

  return DOMPurify.sanitize(html, {
    USE_PROFILES: { html: true }
  })
}

const formatTime = (dateString) => {
  if (!dateString) return ''
  return new Date(dateString).toLocaleTimeString('en-US', {
    hour: '2-digit',
    minute: '2-digit'
  })
}

const scrollToBottom = () => {
  nextTick(() => {
    if (messagesContainer.value) {
      messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
    }
  })
}

// Close on escape key
const handleEscape = (event) => {
  if (event.key === 'Escape' && isOpen.value) {
    closeChat()
  }
}

// Handle resize
const handleResize = () => {
  checkMobile()
}

onMounted(() => {
  document.addEventListener('keydown', handleEscape)
  window.addEventListener('resize', handleResize)
  checkMobile()
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleEscape)
  window.removeEventListener('resize', handleResize)
})
</script>

<style>
pre {
  background: #0f172a; /* dark slate */
  color: #e5e7eb;
  padding: 1rem;
  border-radius: 12px;
  overflow-x: auto;
  margin: 1rem 0;
}

pre code {
  font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
  font-size: 0.9rem;
  line-height: 1.6;
}
</style>

