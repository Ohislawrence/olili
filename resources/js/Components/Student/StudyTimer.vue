<!-- resources/js/Components/Student/StudyTimer.vue -->
<template>
  <div class="flex items-center space-x-2 bg-gray-100 rounded-lg px-3 py-2">
    <ClockIcon class="h-4 w-4 text-gray-600" />
    <span class="text-sm font-medium text-gray-900">
      {{ formattedTime }}
    </span>
    <button
      v-if="!isRunning"
      @click="startTimer"
      class="p-1 text-green-600 hover:text-green-700"
    >
      <PlayIcon class="h-3 w-3" />
    </button>
    <button
      v-else
      @click="pauseTimer"
      class="p-1 text-orange-600 hover:text-orange-700"
    >
      <PauseIcon class="h-3 w-3" />
    </button>
  </div>
</template>

<script setup>
import { ref, onUnmounted, computed } from 'vue'
import { ClockIcon, PlayIcon, PauseIcon } from '@heroicons/vue/24/outline'

const emit = defineEmits(['time-update'])

const isRunning = ref(false)
const startTime = ref(null)
const elapsedTime = ref(0)
const timerInterval = ref(null)

const formattedTime = computed(() => {
  const totalSeconds = Math.floor(elapsedTime.value / 1000)
  const hours = Math.floor(totalSeconds / 3600)
  const minutes = Math.floor((totalSeconds % 3600) / 60)
  const seconds = totalSeconds % 60

  if (hours > 0) {
    return `${hours}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`
  }
  return `${minutes}:${seconds.toString().padStart(2, '0')}`
})

const startTimer = () => {
  isRunning.value = true
  startTime.value = Date.now() - elapsedTime.value

  timerInterval.value = setInterval(() => {
    elapsedTime.value = Date.now() - startTime.value
    emit('time-update', elapsedTime.value)
  }, 1000)
}

const pauseTimer = () => {
  isRunning.value = false
  if (timerInterval.value) {
    clearInterval(timerInterval.value)
    timerInterval.value = null
  }
}

const resetTimer = () => {
  pauseTimer()
  elapsedTime.value = 0
  emit('time-update', 0)
}

onUnmounted(() => {
  if (timerInterval.value) {
    clearInterval(timerInterval.value)
  }
})

defineExpose({
  resetTimer,
})
</script>
