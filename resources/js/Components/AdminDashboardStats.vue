<!-- resources/js/Components/AdminDashboardStats.vue -->
<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <h3 class="text-lg font-semibold text-gray-900">Exam Prep Overview</h3>
      <Link
        :href="route('admin.exam-preps.index')"
        class="text-sm font-medium text-emerald-600 hover:text-emerald-500"
      >
        View all →
      </Link>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <!-- Total Exam Preps -->
      <div class="bg-white rounded-lg border border-gray-200 p-4">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
              <AcademicCapIcon class="h-5 w-5 text-blue-600" />
            </div>
          </div>
          <div class="ml-3">
            <p class="text-sm font-medium text-gray-600">Total Exam Preps</p>
            <p class="text-xl font-bold text-gray-900">{{ formatNumber(stats.total_exam_preps) }}</p>
          </div>
        </div>
        <div class="mt-2">
          <div class="flex items-center text-xs">
            <span class="text-gray-500">Active:</span>
            <span class="ml-1 font-semibold text-emerald-600">{{ stats.active_exam_preps }}</span>
          </div>
        </div>
      </div>

      <!-- Total Attempts -->
      <div class="bg-white rounded-lg border border-gray-200 p-4">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center">
              <ClipboardDocumentCheckIcon class="h-5 w-5 text-emerald-600" />
            </div>
          </div>
          <div class="ml-3">
            <p class="text-sm font-medium text-gray-600">Total Attempts</p>
            <p class="text-xl font-bold text-gray-900">{{ formatNumber(stats.total_attempts) }}</p>
          </div>
        </div>
        <div class="mt-2">
          <div class="flex items-center text-xs">
            <span class="text-gray-500">Today:</span>
            <span class="ml-1 font-semibold text-emerald-600">{{ formatNumber(stats.today_attempts) }}</span>
          </div>
        </div>
      </div>

      <!-- Average Score -->
      <div class="bg-white rounded-lg border border-gray-200 p-4">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
              <ChartBarIcon class="h-5 w-5 text-purple-600" />
            </div>
          </div>
          <div class="ml-3">
            <p class="text-sm font-medium text-gray-600">Avg Score</p>
            <p class="text-xl font-bold text-gray-900">{{ stats.average_score.toFixed(1) }}%</p>
          </div>
        </div>
        <div class="mt-2">
          <div class="w-full bg-gray-200 rounded-full h-1.5">
            <div
              class="bg-purple-600 h-1.5 rounded-full"
              :style="{ width: `${stats.average_score}%` }"
            ></div>
          </div>
        </div>
      </div>

      <!-- Pass Rate -->
      <div class="bg-white rounded-lg border border-gray-200 p-4">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
              <CheckCircleIcon class="h-5 w-5 text-green-600" />
            </div>
          </div>
          <div class="ml-3">
            <p class="text-sm font-medium text-gray-600">Pass Rate</p>
            <p class="text-xl font-bold text-gray-900">{{ stats.pass_rate.toFixed(1) }}%</p>
          </div>
        </div>
        <div class="mt-2">
          <div class="flex items-center text-xs">
            <span class="text-gray-500">Passed:</span>
            <span class="ml-1 font-semibold text-emerald-600">{{ formatNumber(stats.passed_attempts) }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Popular Exam Preps -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
      <div class="p-4 border-b border-gray-200">
        <h4 class="text-sm font-semibold text-gray-900">Most Popular Exam Preps</h4>
      </div>
      <div class="divide-y divide-gray-200">
        <div
          v-for="(prep, index) in popularExamPreps"
          :key="prep.id"
          class="p-4 hover:bg-gray-50 transition-colors"
        >
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center">
                  <span class="text-xs font-semibold text-emerald-700">{{ index + 1 }}</span>
                </div>
              </div>
              <div class="ml-3">
                <Link
                  :href="route('admin.exam-preps.show', prep.id)"
                  class="text-sm font-medium text-gray-900 hover:text-emerald-600"
                >
                  {{ prep.name }}
                </Link>
                <div class="flex items-center text-xs text-gray-500 mt-1">
                  <span>{{ prep.exam_board?.name }}</span>
                  <span class="mx-1">•</span>
                  <span>{{ prep.total_questions }} questions</span>
                </div>
              </div>
            </div>
            <div class="text-right">
              <div class="text-sm font-bold text-gray-900">{{ prep.enrolled_count }} enrolled</div>
              <div class="text-xs text-gray-500">{{ prep.average_score.toFixed(1) }}% avg</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import {
  AcademicCapIcon,
  ClipboardDocumentCheckIcon,
  ChartBarIcon,
  CheckCircleIcon,
} from '@heroicons/vue/24/outline'
import { Link } from '@inertiajs/vue3'

const stats = ref({
  total_exam_preps: 0,
  active_exam_preps: 0,
  total_attempts: 0,
  today_attempts: 0,
  average_score: 0,
  pass_rate: 0,
  passed_attempts: 0,
})

const popularExamPreps = ref([])

const formatNumber = (num) => {
  return new Intl.NumberFormat().format(num || 0)
}

const fetchDashboardStats = async () => {
  try {
    const response = await fetch('/admin/dashboard/exam-prep-stats')
    const data = await response.json()

    stats.value = data.stats
    popularExamPreps.value = data.popular_exam_preps || []
  } catch (error) {
    console.error('Failed to fetch dashboard stats:', error)
  }
}

onMounted(() => {
  fetchDashboardStats()
})
</script>
