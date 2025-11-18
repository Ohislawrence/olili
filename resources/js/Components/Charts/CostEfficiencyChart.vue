<!-- resources/js/Components/Charts/CostEfficiencyChart.vue -->
<template>
    <div class="h-64">
        <div v-if="data.length === 0" class="flex items-center justify-center h-full text-gray-500">
            No cost efficiency data available
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

    chartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: props.data.map(item => {
                const date = new Date(item.date)
                return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
            }),
            datasets: [{
                label: 'Cost per 1K Tokens ($)',
                data: props.data.map(item => item.cost_per_thousand_tokens),
                borderColor: 'rgb(139, 92, 246)',
                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Cost per 1K Tokens ($)'
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
