<!-- resources/js/Components/Admin/RichTextEditorEmail.vue -->
<template>
  <div class="rich-text-editor-email">
    <!-- Toolbar -->
    <div class="toolbar border border-gray-300 border-b-0 rounded-t-lg bg-gray-50 p-2 flex flex-wrap gap-1">
      <button type="button" @click="formatText('bold')" class="p-2 hover:bg-gray-200 rounded" title="Bold">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10.5a3.5 3.5 0 11-7 0 3.5 3.5 0 017 0z" />
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
        </svg>
      </button>
      <button type="button" @click="formatText('italic')" class="p-2 hover:bg-gray-200 rounded" title="Italic">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
      </button>
      <button type="button" @click="formatText('underline')" class="p-2 hover:bg-gray-200 rounded" title="Underline">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
        </svg>
      </button>

      <div class="w-px h-6 bg-gray-300 mx-1"></div>

      <button type="button" @click="formatText('h1')" class="p-2 hover:bg-gray-200 rounded text-sm font-bold" title="Heading 1">
        H1
      </button>
      <button type="button" @click="formatText('h2')" class="p-2 hover:bg-gray-200 rounded text-sm font-bold" title="Heading 2">
        H2
      </button>
      <button type="button" @click="formatText('h3')" class="p-2 hover:bg-gray-200 rounded text-sm font-bold" title="Heading 3">
        H3
      </button>

      <div class="w-px h-6 bg-gray-300 mx-1"></div>

      <button type="button" @click="formatText('ul')" class="p-2 hover:bg-gray-200 rounded" title="Bullet List">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
        </svg>
      </button>
      <button type="button" @click="formatText('ol')" class="p-2 hover:bg-gray-200 rounded" title="Numbered List">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
        </svg>
      </button>

      <div class="w-px h-6 bg-gray-300 mx-1"></div>

      <button type="button" @click="formatText('link')" class="p-2 hover:bg-gray-200 rounded" title="Insert Link">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
        </svg>
      </button>

      <div class="w-px h-6 bg-gray-300 mx-1"></div>

      <div class="flex items-center ml-auto">
        <button
          type="button"
          @click="toggleHtml"
          class="p-2 hover:bg-gray-200 rounded text-sm"
          :class="showHtml ? 'bg-gray-200' : ''"
        >
          {{ showHtml ? 'Visual' : 'HTML' }}
        </button>
      </div>
    </div>

    <!-- HTML Editor -->
    <textarea
      v-if="showHtml"
      v-model="htmlContent"
      :style="{ height }"
      class="w-full border border-gray-300 rounded-b-lg p-4 font-mono text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
      :class="{ 'border-red-300': error }"
      @input="handleHtmlInput"
      placeholder="Enter your email message here... (HTML supported)"
    ></textarea>

    <!-- Visual Editor (Simple textarea for now - more stable) -->
    <textarea
      v-else
      v-model="textContent"
      :style="{ height }"
      class="w-full border border-gray-300 rounded-b-lg p-4 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
      :class="{ 'border-red-300': error }"
      @input="handleTextInput"
      placeholder="Enter your email message here..."
    ></textarea>

    <!-- Error Message -->
    <div v-if="error" class="mt-1 text-sm text-red-600">
      {{ error }}
    </div>
  </div>
</template>

<script setup>
import { ref, watch, nextTick, computed } from 'vue'

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  error: {
    type: String,
    default: ''
  },
  height: {
    type: String,
    default: '200px'
  }
})

const emit = defineEmits(['update:modelValue'])

const showHtml = ref(false)
const htmlContent = ref(props.modelValue)
const textContent = ref(props.modelValue)

// Convert HTML to plain text for visual editor
const htmlToText = (html) => {
  const temp = document.createElement('div')
  temp.innerHTML = html
  return temp.textContent || temp.innerText || ''
}

// Convert text to basic HTML
const textToHtml = (text) => {
  return text
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;')
    .replace(/\n/g, '<br>')
    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
    .replace(/\*(.*?)\*/g, '<em>$1</em>')
    .replace(/__(.*?)__/g, '<u>$1</u>')
}

// Watch for external changes to modelValue
watch(() => props.modelValue, (newValue) => {
  htmlContent.value = newValue
  textContent.value = htmlToText(newValue)
}, { immediate: true })

// Handle HTML editor input
const handleHtmlInput = () => {
  emit('update:modelValue', htmlContent.value)
  textContent.value = htmlToText(htmlContent.value)
}

// Handle text editor input
const handleTextInput = () => {
  htmlContent.value = textToHtml(textContent.value)
  emit('update:modelValue', htmlContent.value)
}

const formatText = (command) => {
  if (showHtml.value) {
    // Format HTML content
    let selectionStart = htmlContent.value.length
    let selectionEnd = htmlContent.value.length

    switch (command) {
      case 'bold':
        htmlContent.value += '<strong>bold text</strong>'
        break
      case 'italic':
        htmlContent.value += '<em>italic text</em>'
        break
      case 'underline':
        htmlContent.value += '<u>underlined text</u>'
        break
      case 'h1':
        htmlContent.value += '<h1>Heading 1</h1>'
        break
      case 'h2':
        htmlContent.value += '<h2>Heading 2</h2>'
        break
      case 'h3':
        htmlContent.value += '<h3>Heading 3</h3>'
        break
      case 'ul':
        htmlContent.value += '<ul><li>List item</li></ul>'
        break
      case 'ol':
        htmlContent.value += '<ol><li>List item</li></ol>'
        break
      case 'link':
        const url = prompt('Enter URL:', 'https://')
        if (url) {
          htmlContent.value += `<a href="${url}" target="_blank">Link text</a>`
        }
        break
    }

    handleHtmlInput()
  } else {
    // Format text content
    let textToAdd = ''
    let cursorPosition = textContent.value.length

    switch (command) {
      case 'bold':
        textToAdd = '**bold text**'
        break
      case 'italic':
        textToAdd = '*italic text*'
        break
      case 'underline':
        textToAdd = '__underlined text__'
        break
      case 'h1':
        textToAdd = '\n\n# Heading 1\n\n'
        break
      case 'h2':
        textToAdd = '\n\n## Heading 2\n\n'
        break
      case 'h3':
        textToAdd = '\n\n### Heading 3\n\n'
        break
      case 'ul':
        textToAdd = '\n\n• List item\n• Another item\n\n'
        break
      case 'ol':
        textToAdd = '\n\n1. First item\n2. Second item\n\n'
        break
      case 'link':
        const url = prompt('Enter URL:', 'https://')
        if (url) {
          textToAdd = `[Link text](${url})`
        }
        break
    }

    if (textToAdd) {
      textContent.value += textToAdd
      handleTextInput()

      // Focus back on the textarea
      nextTick(() => {
        const textarea = document.querySelector('.rich-text-editor-email textarea')
        if (textarea) {
          textarea.focus()
          textarea.setSelectionRange(cursorPosition, cursorPosition + textToAdd.length)
        }
      })
    }
  }
}

const toggleHtml = () => {
  showHtml.value = !showHtml.value
}
</script>

<style scoped>
.rich-text-editor-email textarea {
  resize: vertical;
  min-height: 100px;
  line-height: 1.5;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}

.rich-text-editor-email textarea:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}
</style>
