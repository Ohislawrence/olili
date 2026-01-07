<!-- resources/js/Components/Charts/LineChart.vue -->
<template>
  <div class="chart-container" :style="{ height: height }">
    <canvas ref="chartCanvas"></canvas>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, onUnmounted } from 'vue'
import Chart from 'chart.js/auto'

const props = defineProps({
  data: {
    type: Object,
    default: () => ({})
  },
  options: {
    type: Object,
    default: () => ({})
  },
  height: {
    type: String,
    default: '300px'
  },
  gradient: {
    type: Boolean,
    default: true
  },
  fill: {
    type: Boolean,
    default: true
  },
  tension: {
    type: Number,
    default: 0.4
  }
})

const chartCanvas = ref(null)
let chartInstance = null

// Default options for line chart
const defaultOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'top',
      labels: {
        usePointStyle: true,
        padding: 20,
        font: {
          size: 12
        }
      }
    },
    tooltip: {
      mode: 'index',
      intersect: false,
      backgroundColor: 'rgba(0, 0, 0, 0.7)',
      titleFont: {
        size: 14
      },
      bodyFont: {
        size: 13
      },
      padding: 12,
      cornerRadius: 6,
      callbacks: {
        label: function(context) {
          let label = context.dataset.label || ''
          if (label) {
            label += ': '
          }
          if (context.parsed.y !== null) {
            // Format numbers with commas for thousands
            const value = context.parsed.y
            label += value.toLocaleString()
          }
          return label
        }
      }
    }
  },
  interaction: {
    mode: 'nearest',
    axis: 'x',
    intersect: false
  },
  scales: {
    x: {
      grid: {
        display: true,
        color: 'rgba(0, 0, 0, 0.05)'
      },
      ticks: {
        font: {
          size: 11
        },
        maxRotation: 45
      }
    },
    y: {
      beginAtZero: true,
      grid: {
        display: true,
        color: 'rgba(0, 0, 0, 0.05)'
      },
      ticks: {
        font: {
          size: 11
        },
        callback: function(value) {
          // Format numbers with K for thousands
          if (value >= 1000) {
            return (value / 1000).toFixed(1) + 'K'
          }
          return value
        }
      }
    }
  },
  elements: {
    line: {
      tension: props.tension
    },
    point: {
      radius: 3,
      hoverRadius: 6,
      backgroundColor: 'white',
      borderWidth: 2
    }
  }
}

// Create gradient fill for datasets
const createGradient = (ctx, chartArea, color) => {
  if (!ctx || !chartArea) return color

  const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top)

  // Parse the color
  let rgbaColor
  if (color.startsWith('rgba')) {
    rgbaColor = color
  } else if (color.startsWith('#')) {
    // Convert hex to rgba
    const hex = color.replace('#', '')
    const r = parseInt(hex.substring(0, 2), 16)
    const g = parseInt(hex.substring(2, 4), 16)
    const b = parseInt(hex.substring(4, 6), 16)
    rgbaColor = `rgba(${r}, ${g}, ${b}, 0.1)`
  } else {
    // Default fallback
    rgbaColor = 'rgba(59, 130, 246, 0.1)'
  }

  gradient.addColorStop(0, 'rgba(255, 255, 255, 0)')
  gradient.addColorStop(1, rgbaColor)

  return gradient
}

// Prepare chart data with enhancements
const prepareChartData = () => {
  if (!props.data || !props.data.datasets) return props.data

  const data = { ...props.data }

  // Enhance each dataset
  data.datasets = data.datasets.map((dataset, index) => {
    const enhancedDataset = { ...dataset }

    // Set default line styles
    enhancedDataset.borderWidth = enhancedDataset.borderWidth || 2
    enhancedDataset.pointBorderWidth = enhancedDataset.pointBorderWidth || 2
    enhancedDataset.pointRadius = enhancedDataset.pointRadius || 3
    enhancedDataset.pointHoverRadius = enhancedDataset.pointHoverRadius || 6
    enhancedDataset.pointBackgroundColor = enhancedDataset.pointBackgroundColor || 'white'

    // Generate colors if not provided
    if (!enhancedDataset.borderColor) {
      const colors = [
        '#3b82f6', // blue
        '#10b981', // emerald
        '#8b5cf6', // violet
        '#f59e0b', // amber
        '#ef4444', // red
        '#06b6d4', // cyan
        '#84cc16', // lime
        '#f97316', // orange
        '#ec4899', // pink
        '#6366f1', // indigo
      ]
      enhancedDataset.borderColor = colors[index % colors.length]
    }

    // Set point border color to match line color
    enhancedDataset.pointBorderColor = enhancedDataset.borderColor

    // Set fill if enabled
    if (props.fill && !enhancedDataset.backgroundColor) {
      enhancedDataset.backgroundColor = enhancedDataset.borderColor
    }

    return enhancedDataset
  })

  return data
}

// Initialize or update the chart
const initChart = () => {
  if (!chartCanvas.value || !props.data) return

  // Destroy existing chart instance
  if (chartInstance) {
    chartInstance.destroy()
  }

  const ctx = chartCanvas.value.getContext('2d')
  const preparedData = prepareChartData()

  // Create gradients for fill if enabled
  if (props.gradient && props.fill) {
    const chartArea = {
      top: 0,
      right: chartCanvas.value.width,
      bottom: chartCanvas.value.height,
      left: 0
    }

    preparedData.datasets = preparedData.datasets.map(dataset => {
      if (dataset.backgroundColor && dataset.backgroundColor.startsWith('rgba') || dataset.backgroundColor.startsWith('#')) {
        return {
          ...dataset,
          backgroundColor: createGradient(ctx, chartArea, dataset.backgroundColor)
        }
      }
      return dataset
    })
  }

  // Merge default options with props
  const mergedOptions = { ...defaultOptions, ...props.options }

  // Create new chart instance
  chartInstance = new Chart(ctx, {
    type: 'line',
    data: preparedData,
    options: mergedOptions
  })
}

// Lifecycle hooks
onMounted(() => {
  // Initialize chart after component is mounted
  setTimeout(() => {
    initChart()
  }, 100)
})

// Watch for data changes
watch(() => props.data, () => {
  if (chartInstance) {
    // Update data and re-render
    const preparedData = prepareChartData()

    if (props.gradient && props.fill && chartCanvas.value) {
      const ctx = chartCanvas.value.getContext('2d')
      const chartArea = {
        top: 0,
        right: chartCanvas.value.width,
        bottom: chartCanvas.value.height,
        left: 0
      }

      preparedData.datasets = preparedData.datasets.map(dataset => {
        if (dataset.backgroundColor && (dataset.backgroundColor.startsWith('rgba') || dataset.backgroundColor.startsWith('#'))) {
          return {
            ...dataset,
            backgroundColor: createGradient(ctx, chartArea, dataset.backgroundColor)
          }
        }
        return dataset
      })
    }

    chartInstance.data = preparedData
    chartInstance.update()
  } else {
    initChart()
  }
}, { deep: true })

// Watch for options changes
watch(() => props.options, () => {
  if (chartInstance) {
    chartInstance.options = { ...defaultOptions, ...props.options }
    chartInstance.update()
  }
}, { deep: true })

// Cleanup on unmount
onUnmounted(() => {
  if (chartInstance) {
    chartInstance.destroy()
    chartInstance = null
  }
})
</script>

<style scoped>
.chart-container {
  position: relative;
  width: 100%;
}
</style>
