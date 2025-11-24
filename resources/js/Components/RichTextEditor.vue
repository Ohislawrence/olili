<template>
  <div class="rich-text-editor">
    <!-- Toolbar -->
    <div v-if="showToolbar && editor" class="border border-gray-300 rounded-t-lg bg-gray-50 p-2 flex flex-wrap items-center gap-1">
      <!-- Text Formatting -->
      <button
        @click="editor.chain().focus().toggleBold().run()"
        :class="[
          'p-2 rounded hover:bg-gray-200 transition-colors',
          editor.isActive('bold') ? 'bg-gray-300' : ''
        ]"
        :disabled="!editor.can().chain().focus().toggleBold().run()"
        type="button"
        title="Bold"
      >
        <BoldIcon class="h-4 w-4" />
      </button>
      <button
        @click="editor.chain().focus().toggleItalic().run()"
        :class="[
          'p-2 rounded hover:bg-gray-200 transition-colors',
          editor.isActive('italic') ? 'bg-gray-300' : ''
        ]"
        :disabled="!editor.can().chain().focus().toggleItalic().run()"
        type="button"
        title="Italic"
      >
        <ItalicIcon class="h-4 w-4" />
      </button>
      <button
        @click="editor.chain().focus().toggleStrike().run()"
        :class="[
          'p-2 rounded hover:bg-gray-200 transition-colors',
          editor.isActive('strike') ? 'bg-gray-300' : ''
        ]"
        :disabled="!editor.can().chain().focus().toggleStrike().run()"
        type="button"
        title="Strikethrough"
      >
        <StrikeThroughIcon class="h-4 w-4" />
      </button>

      <!-- Text Alignment -->
      <div class="w-px h-6 bg-gray-300 mx-1"></div>
      <button
        @click="editor.chain().focus().setTextAlign('left').run()"
        :class="[
          'p-2 rounded hover:bg-gray-200 transition-colors',
          editor.isActive({ textAlign: 'left' }) ? 'bg-gray-300' : ''
        ]"
        type="button"
        title="Align Left"
      >
        <AlignLeftIcon class="h-4 w-4" />
      </button>
      <button
        @click="editor.chain().focus().setTextAlign('center').run()"
        :class="[
          'p-2 rounded hover:bg-gray-200 transition-colors',
          editor.isActive({ textAlign: 'center' }) ? 'bg-gray-300' : ''
        ]"
        type="button"
        title="Align Center"
      >
        <AlignCenterIcon class="h-4 w-4" />
      </button>
      <button
        @click="editor.chain().focus().setTextAlign('right').run()"
        :class="[
          'p-2 rounded hover:bg-gray-200 transition-colors',
          editor.isActive({ textAlign: 'right' }) ? 'bg-gray-300' : ''
        ]"
        type="button"
        title="Align Right"
      >
        <AlignRightIcon class="h-4 w-4" />
      </button>

      <!-- Lists -->
      <div class="w-px h-6 bg-gray-300 mx-1"></div>
      <button
        @click="editor.chain().focus().toggleBulletList().run()"
        :class="[
          'p-2 rounded hover:bg-gray-200 transition-colors',
          editor.isActive('bulletList') ? 'bg-gray-300' : ''
        ]"
        type="button"
        title="Bullet List"
      >
        <ListBulletIcon class="h-4 w-4" />
      </button>
      <button
        @click="editor.chain().focus().toggleOrderedList().run()"
        :class="[
          'p-2 rounded hover:bg-gray-200 transition-colors',
          editor.isActive('orderedList') ? 'bg-gray-300' : ''
        ]"
        type="button"
        title="Numbered List"
      >
        <NumberedListIcon class="h-4 w-4" />
      </button>

      <!-- Headings -->
      <div class="w-px h-6 bg-gray-300 mx-1"></div>
      <button
        @click="editor.chain().focus().setParagraph().run()"
        :class="[
          'p-2 rounded hover:bg-gray-200 transition-colors text-sm',
          editor.isActive('paragraph') ? 'bg-gray-300' : ''
        ]"
        type="button"
        title="Paragraph"
      >
        Text
      </button>
      <button
        @click="editor.chain().focus().toggleHeading({ level: 2 }).run()"
        :class="[
          'p-2 rounded hover:bg-gray-200 transition-colors text-sm',
          editor.isActive('heading', { level: 2 }) ? 'bg-gray-300' : ''
        ]"
        type="button"
        title="Heading 2"
      >
        H2
      </button>
      <button
        @click="editor.chain().focus().toggleHeading({ level: 3 }).run()"
        :class="[
          'p-2 rounded hover:bg-gray-200 transition-colors text-sm',
          editor.isActive('heading', { level: 3 }) ? 'bg-gray-300' : ''
        ]"
        type="button"
        title="Heading 3"
      >
        H3
      </button>

      <!-- Blockquote -->
      <div class="w-px h-6 bg-gray-300 mx-1"></div>
      <button
        @click="editor.chain().focus().toggleBlockquote().run()"
        :class="[
          'p-2 rounded hover:bg-gray-200 transition-colors',
          editor.isActive('blockquote') ? 'bg-gray-300' : ''
        ]"
        type="button"
        title="Blockquote"
      >
        <QuoteIcon class="h-4 w-4" />
      </button>

      <!-- Code -->
      <button
        @click="editor.chain().focus().toggleCodeBlock().run()"
        :class="[
          'p-2 rounded hover:bg-gray-200 transition-colors',
          editor.isActive('codeBlock') ? 'bg-gray-300' : ''
        ]"
        type="button"
        title="Code Block"
      >
        <CodeBracketIcon class="h-4 w-4" />
      </button>

      <!-- Horizontal Rule -->
      <button
        @click="editor.chain().focus().setHorizontalRule().run()"
        class="p-2 rounded hover:bg-gray-200 transition-colors"
        type="button"
        title="Horizontal Rule"
      >
        <MinusIcon class="h-4 w-4" />
      </button>

      <!-- Clear Formatting -->
      <div class="w-px h-6 bg-gray-300 mx-1"></div>
      <button
        @click="editor.chain().focus().clearNodes().unsetAllMarks().run()"
        class="p-2 rounded hover:bg-gray-200 transition-colors text-sm"
        type="button"
        title="Clear Formatting"
      >
        Clear
      </button>
    </div>

    <!-- Editor Content -->
    <editor-content
      v-if="editor"
      :editor="editor"
      :class="[
        'prose prose-lg max-w-none border border-gray-300 bg-white rounded-b-lg min-h-64 p-4 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500',
        showToolbar ? 'rounded-t-none' : 'rounded-t-lg'
      ]"
    />

    <!-- Loading State -->
    <div v-else class="border border-gray-300 rounded-lg bg-white min-h-64 p-4 flex items-center justify-center">
      <div class="text-gray-500">Loading editor...</div>
    </div>

    <!-- Character Count (Optional) -->
    <div v-if="showCharacterCount && editor" class="mt-2 text-sm text-gray-500 text-right">
      {{ getCharacterCount() }} characters
    </div>
  </div>
</template>

<script setup>
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import TextAlign from '@tiptap/extension-text-align'
import { watch, onUnmounted } from 'vue'
import { 
  BoldIcon, 
  ItalicIcon,
  ListBulletIcon,
  CodeBracketIcon,
  MinusIcon
} from '@heroicons/vue/24/outline'

// Custom SVG icons for missing Heroicons
const StrikeThroughIcon = {
  template: `
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 12.75c1.148 0 2.278.08 3.383.237 1.037.146 1.866.966 1.866 2.013 0 3.728-2.35 6.75-5.25 6.75S6.75 18.728 6.75 15c0-1.046.83-1.867 1.866-2.013A24.204 24.204 0 0112 12.75zm0 0c2.883 0 5.647.508 8.207 1.44a23.91 23.91 0 01-1.152 6.06M12 12.75c-2.883 0-5.647.508-8.208 1.44.125 2.104.52 4.136 1.153 6.06M12 12.75a2.25 2.25 0 002.248-2.354M12 12.75a2.25 2.25 0 01-2.248-2.354M12 8.25c.995 0 1.971-.08 2.922-.236.403-.066.74-.358.795-.762a3.778 3.778 0 00-.399-2.25M12 8.25c-.995 0-1.97-.08-2.922-.236-.402-.066-.74-.358-.795-.762a3.734 3.734 0 01.4-2.253M12 8.25a2.25 2.25 0 00-2.248 2.146M12 8.25a2.25 2.25 0 012.248 2.146M8.683 5a6.032 6.032 0 01-1.155-1.002c.07-.63.27-1.222.574-1.747m.581 2.749A6.04 6.04 0 0112 5.688c1.319 0 2.558.368 3.608 1.007m-7.225 13.25a24.204 24.204 0 003.266-.237" />
    </svg>
  `
}

const QuoteIcon = {
  template: `
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
      <path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 01-1.44-4.282m3.102.069a18.03 18.03 0 01-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 018.835 2.535M10.34 6.66a23.847 23.847 0 008.835-2.535m0 0A23.74 23.74 0 0018.795 3m.38 1.125a23.91 23.91 0 011.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 001.014-5.395m0-3.46c.495.413.811 1.035.811 1.73 0 .695-.316 1.317-.811 1.73m0-3.46a24.347 24.347 0 010 3.46" />
    </svg>
  `
}

const NumberedListIcon = {
  template: `
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
      <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
    </svg>
  `
}

const AlignLeftIcon = {
  template: `
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
      <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12" />
    </svg>
  `
}

const AlignCenterIcon = {
  template: `
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
      <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
    </svg>
  `
}

const AlignRightIcon = {
  template: `
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
      <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12" />
    </svg>
  `
}

const props = defineProps({
  modelValue: {
    type: String,
    default: '',
  },
  showToolbar: {
    type: Boolean,
    default: true,
  },
  showCharacterCount: {
    type: Boolean,
    default: false,
  },
  placeholder: {
    type: String,
    default: 'Start writing your content...',
  },
})

const emit = defineEmits(['update:modelValue'])

const editor = useEditor({
  content: props.modelValue,
  extensions: [
    StarterKit,
    TextAlign.configure({
      types: ['heading', 'paragraph'],
    }),
  ],
  editorProps: {
    attributes: {
      class: 'prose prose-lg max-w-none focus:outline-none min-h-64',
      placeholder: props.placeholder,
    },
  },
  onUpdate: () => {
    // Only emit if editor is ready
    if (editor.value) {
      emit('update:modelValue', editor.value.getHTML())
    }
  },
  onCreate: () => {
    console.log('Editor created and ready')
  },
})

// Helper function to get character count
const getCharacterCount = () => {
  if (!editor.value) return 0
  
  // Simple character count implementation
  const text = editor.value.getText()
  return text.length
}

// Watch for external modelValue changes
watch(() => props.modelValue, (value) => {
  if (editor.value && value !== editor.value.getHTML()) {
    editor.value.commands.setContent(value, false)
  }
})

// Clean up editor
onUnmounted(() => {
  if (editor.value) {
    editor.value.destroy()
  }
})
</script>

<style scoped>
.rich-text-editor :deep(.ProseMirror) {
  outline: none;
  min-height: 16rem;
}

.rich-text-editor :deep(.ProseMirror p.is-editor-empty:first-child::before) {
  color: #9ca3af;
  content: attr(data-placeholder);
  float: left;
  height: 0;
  pointer-events: none;
}

.rich-text-editor :deep(.ProseMirror h1) {
  @apply text-3xl font-bold text-gray-900 mb-4;
}

.rich-text-editor :deep(.ProseMirror h2) {
  @apply text-2xl font-bold text-gray-900 mb-3 mt-6;
}

.rich-text-editor :deep(.ProseMirror h3) {
  @apply text-xl font-semibold text-gray-900 mb-2 mt-4;
}

.rich-text-editor :deep(.ProseMirror ul) {
  @apply list-disc list-inside mb-4;
}

.rich-text-editor :deep(.ProseMirror ol) {
  @apply list-decimal list-inside mb-4;
}

.rich-text-editor :deep(.ProseMirror blockquote) {
  @apply border-l-4 border-emerald-500 pl-4 italic text-gray-600 my-4;
}

.rich-text-editor :deep(.ProseMirror code) {
  @apply bg-gray-100 text-gray-800 px-1 py-0.5 rounded text-sm;
}

.rich-text-editor :deep(.ProseMirror pre) {
  @apply bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto mb-4;
}
</style>