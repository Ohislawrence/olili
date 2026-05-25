<!-- resources/js/Components/ExamPrep/GenerationStatusBadge.vue -->
<template>
  <span
    v-if="status"
    :class="[
      'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
      badgeClasses
    ]"
  >
    <span :class="dotClasses" class="mr-1.5 h-2 w-2 rounded-full"></span>
    {{ label }}
  </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  status: {
    type: String,
    default: null,
    validator: (value) => ['pending', 'processing', 'completed', 'failed', null].includes(value)
  }
})

const statusConfig = {
  pending: {
    label: 'Pending',
    bg: 'bg-gray-100',
    text: 'text-gray-800',
    dot: 'bg-gray-400'
  },
  processing: {
    label: 'Generating...',
    bg: 'bg-blue-100',
    text: 'text-blue-800',
    dot: 'bg-blue-500 animate-pulse'
  },
  completed: {
    label: 'Generated',
    bg: 'bg-emerald-100',
    text: 'text-emerald-800',
    dot: 'bg-emerald-500'
  },
  failed: {
    label: 'Failed',
    bg: 'bg-red-100',
    text: 'text-red-800',
    dot: 'bg-red-500'
  }
}

const config = computed(() => statusConfig[props.status] || null)

const badgeClasses = computed(() => {
  if (!config.value) return ''
  return `${config.value.bg} ${config.value.text}`
})

const dotClasses = computed(() => {
  if (!config.value) return ''
  return config.value.dot
})

const label = computed(() => {
  if (!config.value) return ''
  return config.value.label
})
</script>
