<!-- resources/js/Components/Admin/UsageChart.vue -->
<template>
  <div class="w-full">
    <!-- Chart Tabs -->
    <div class="border-b border-gray-200 mb-6">
      <nav class="-mb-px flex space-x-8">
        <button
          v-for="tab in tabs"
          :key="tab.name"
          @click="activeTab = tab.name"
          class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm"
          :class="tabClasses(tab)"
        >
          {{ tab.label }}
        </button>
      </nav>
    </div>

    <!-- Chart Container -->
    <div class="h-80">
      <canvas ref="chartCanvas"></canvas>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, nextTick } from 'vue'
import Chart from 'chart.js/auto'

const props = defineProps({
  userRegistrations: Array,
  courseCreations: Array,
  aiCosts: Array,
})

const chartCanvas = ref(null)
const activeTab = ref('users')
let chartInstance = null

const tabs = [
  { name: 'users', label: 'User Registrations' },
  { name: 'courses', label: 'Course Creation' },
  { name: 'costs', label: 'AI Costs' },
]

const tabClasses = (tab) => {
  return activeTab.value === tab.name
    ? 'border-blue-500 text-blue-600'
    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
}

const prepareChartData = () => {
  let labels = []
  let data = []
  let label = ''
  let backgroundColor = ''

  switch (activeTab.value) {
    case 'users':
      labels = props.userRegistrations.map(item => item.date)
      data = props.userRegistrations.map(item => item.count)
      label = 'User Registrations'
      backgroundColor = 'rgba(59, 130, 246, 0.5)'
      break
    case 'courses':
      labels = props.courseCreations.map(item => item.date)
      data = props.courseCreations.map(item => item.count)
      label = 'Courses Created'
      backgroundColor = 'rgba(16, 185, 129, 0.5)'
      break
    case 'costs':
      labels = props.aiCosts.map(item => item.date)
      data = props.aiCosts.map(item => parseFloat(item.total_cost || 0))
      label = 'AI Costs ($)'
      backgroundColor = 'rgba(139, 92, 246, 0.5)'
      break
  }

  return {
    labels,
    datasets: [
      {
        label,
        data,
        backgroundColor,
        borderColor: backgroundColor.replace('0.5', '1'),
        borderWidth: 2,
        fill: true,
        tension: 0.4,
      },
    ],
  }
}

const createChart = () => {
  if (chartInstance) {
    chartInstance.destroy()
  }

  if (!chartCanvas.value) return

  const ctx = chartCanvas.value.getContext('2d')
  const chartData = prepareChartData()

  chartInstance = new Chart(ctx, {
    type: 'line',
    data: chartData,
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false,
        },
        tooltip: {
          mode: 'index',
          intersect: false,
        },
      },
      scales: {
        x: {
          grid: {
            display: false,
          },
        },
        y: {
          beginAtZero: true,
          grid: {
            color: 'rgba(0, 0, 0, 0.1)',
          },
        },
      },
      interaction: {
        mode: 'nearest',
        axis: 'x',
        intersect: false,
      },
    },
  })
}

onMounted(() => {
  nextTick(() => {
    createChart()
  })
})

watch([() => activeTab.value, () => props.userRegistrations], () => {
  nextTick(() => {
    createChart()
  })
})
</script>
