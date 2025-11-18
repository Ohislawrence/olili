<!-- resources/js/Components/Charts/PurposeUsageChart.vue -->
<template>
    <div class="h-64">
        <div v-if="data.length === 0" class="flex items-center justify-center h-full text-gray-500">
            No purpose data available
        </div>
        <canvas v-else ref="chartCanvas"></canvas>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import Chart from 'chart.js/auto'

const props = defineProps({
    data: Array
})

const chartCanvas = ref(null)
let chartInstance = null

const createChart = () => {
    if (!chartCanvas.value || props.data.length === 0) return

    if (chartInstance) {
        chartInstance.destroy()
    }

    const ctx = chartCanvas.value.getContext('2d')

    // Sort by total cost descending
    const sortedData = [...props.data].sort((a, b) => b.total_cost - a.total_cost)

    chartInstance = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: sortedData.map(item => {
                const purpose = item.purpose.replace(/_/g, ' ')
                return purpose.charAt(0).toUpperCase() + purpose.slice(1)
            }),
            datasets: [{
                data: sortedData.map(item => item.total_cost),
                backgroundColor: [
                    'rgba(79, 70, 229, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(239, 68, 68, 0.8)',
                    'rgba(139, 92, 246, 0.8)',
                    'rgba(14, 165, 233, 0.8)',
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const value = context.parsed
                            const total = context.dataset.data.reduce((a, b) => a + b, 0)
                            const percentage = Math.round((value / total) * 100)
                            return `$${value.toFixed(4)} (${percentage}%)`
                        }
                    }
                }
            }
        }
    })
}

onMounted(() => {
    createChart()
})

watch(() => props.data, () => {
    createChart()
})
</script>
