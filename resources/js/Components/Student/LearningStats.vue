<!-- resources/js/Components/Student/LearningStats.vue -->
<template>
  <div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200">
      <h2 class="text-lg font-semibold text-gray-900">Learning Analytics</h2>
    </div>
    <div class="p-6">
      <!-- Streak -->
      <div class="flex items-center justify-between mb-6">
        <div>
          <p class="text-sm font-medium text-gray-600">Current Streak</p>
          <p class="text-2xl font-bold text-gray-900">
            {{ analytics.streak_days }} day(s)
          </p>
        </div>
        <FireIcon class="h-8 w-8 text-orange-500" />
      </div>

      <!-- Weekly Progress -->
      <div class="mb-6">
        <h3 class="text-sm font-medium text-gray-900 mb-3">
          Weekly Study Time (hours)
        </h3>
        <div class="flex items-end space-x-1 h-20">
          <div
            v-for="(day, index) in weeklyProgress"
            :key="index"
            class="flex-1 flex flex-col items-center"
          >
            <div
              class="w-full bg-blue-200 rounded-t transition-all duration-500"
              :style="{ height: `${day.height}%` }"
            ></div>
            <span class="text-xs text-gray-500 mt-1">{{ day.label }}</span>
          </div>
        </div>
      </div>

      <!-- Stats -->
      <div class="grid grid-cols-2 gap-4">
        <div>
          <p class="text-sm font-medium text-gray-600">Total Study Time</p>
          <p class="text-lg font-semibold text-gray-900">
            {{ analytics.total_study_time_hours }}h
          </p>
        </div>
        <div>
          <p class="text-sm font-medium text-gray-600">Completed Items</p>
          <p class="text-lg font-semibold text-gray-900">
            {{ analytics.completed_items }}
          </p>
        </div>
        <div>
          <p class="text-sm font-medium text-gray-600">Average Score</p>
          <p class="text-lg font-semibold text-gray-900">
            {{ analytics.average_score }}%
          </p>
        </div>
        <div>
          <p class="text-sm font-medium text-gray-600">Active Courses</p>
          <p class="text-lg font-semibold text-gray-900">
            {{ analytics.active_courses }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { FireIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  analytics: Object,
})

const weeklyProgress = computed(() => {
  const days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
  const weeklyData = props.analytics.weekly_progress || {}

  return days.map((day) => {
    const studyTime = weeklyData[day]?.study_time || 0
    const hours = studyTime / 60 // Convert minutes to hours
    const height = Math.min((hours / 8) * 100, 100) // Cap at 8 hours for 100%

    return {
      label: day,
      height: height,
      hours: hours.toFixed(1),
    }
  })
})
</script>
