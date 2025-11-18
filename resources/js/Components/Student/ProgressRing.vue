<!-- resources/js/Components/Student/ProgressRing.vue -->
<template>
  <div class="relative inline-flex items-center justify-center">
    <svg
      :width="sizePx"
      :height="sizePx"
      class="transform -rotate-90"
    >
      <!-- Background Circle -->
      <circle
        :cx="center"
        :cy="center"
        :r="radius"
        stroke="currentColor"
        :stroke-width="strokeWidth"
        fill="transparent"
        class="text-gray-200"
      />

      <!-- Progress Circle -->
      <circle
        :cx="center"
        :cy="center"
        :r="radius"
        stroke="currentColor"
        :stroke-width="strokeWidth"
        fill="transparent"
        :stroke-dasharray="circumference"
        :stroke-dashoffset="strokeDashoffset"
        class="text-blue-600 transition-all duration-500 ease-in-out"
        :class="progressColor"
      />
    </svg>
    <span
      class="absolute text-sm font-medium"
      :class="textColor"
    >
      {{ progress }}%
    </span>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  progress: {
    type: Number,
    default: 0,
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg'].includes(value),
  },
})

const sizeMap = {
  sm: 40,
  md: 60,
  lg: 80,
}

const strokeWidthMap = {
  sm: 4,
  md: 6,
  lg: 8,
}

const sizePx = computed(() => sizeMap[props.size])
const strokeWidth = computed(() => strokeWidthMap[props.size])
const center = computed(() => sizePx.value / 2)
const radius = computed(() => center.value - strokeWidth.value / 2)
const circumference = computed(() => 2 * Math.PI * radius.value)
const strokeDashoffset = computed(
  () => circumference.value - (props.progress / 100) * circumference.value
)

const progressColor = computed(() => {
  if (props.progress < 30) return 'text-red-500'
  if (props.progress < 70) return 'text-yellow-500'
  return 'text-green-500'
})

const textColor = computed(() => {
  if (props.progress < 30) return 'text-red-600'
  if (props.progress < 70) return 'text-yellow-600'
  return 'text-green-600'
})
</script>
