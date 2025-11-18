<template>
    <div class="h-64">
        <div v-if="data.length === 0" class="flex items-center justify-center h-full text-gray-500">
            No provider data available
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
        type: 'bar',
        data: {
            labels: props.data.map(item => item.ai_provider?.name || 'Unknown'),
            datasets: [
                {
                    label: 'Total Cost ($)',
                    data: props.data.map(item => item.total_cost),
                    backgroundColor: 'rgba(79, 70, 229, 0.8)',
                    borderColor: 'rgb(79, 70, 229)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Cost ($)'
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
