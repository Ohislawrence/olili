<!-- resources/js/Components/ExamPrep/StatusBadge.vue -->
<template>
  <span
    :class="[
      'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
      badgeClasses
    ]"
  >
    {{ label }}
  </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  status: {
    type: String,
    required: true
  },
  statuses: {
    type: Object,
    default: () => ({
      draft: 'Draft',
      active: 'Active',
      archived: 'Archived'
    })
  }
})

const statusConfig = {
  draft: {
    bg: 'bg-gray-100',
    text: 'text-gray-800'
  },
  active: {
    bg: 'bg-emerald-100',
    text: 'text-emerald-800'
  },
  archived: {
    bg: 'bg-amber-100',
    text: 'text-amber-800'
  }
}

const badgeClasses = computed(() => {
  const config = statusConfig[props.status] || statusConfig.draft
  return `${config.bg} ${config.text}`
})

const label = computed(() => {
  return props.statuses[props.status] || props.status
})
</script>
