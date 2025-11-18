<template>
    <div class="h-64">
        <div v-if="data.length === 0" class="flex items-center justify-center h-full text-gray-500">
            No data available for the selected period
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
            datasets: [
                {
                    label: 'Total Tokens',
                    data: props.data.map(item => item.total_tokens),
                    borderColor: 'rgb(79, 70, 229)',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    yAxisID: 'y',
                    tension: 0.4
                },
                {
                    label: 'Total Cost',
                    data: props.data.map(item => item.total_cost),
                    borderColor: 'rgb(16, 185, 129)',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    yAxisID: 'y1',
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Tokens'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Cost ($)'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
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
