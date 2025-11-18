<template>
    <span
        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
        :class="statusClasses"
    >
        <span
            class="w-2 h-2 rounded-full mr-1.5"
            :class="dotClasses"
        ></span>
        {{ status }}
    </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    status: {
        type: String,
        required: true
    }
})

const statusClasses = computed(() => {
    const classes = {
        success: 'bg-green-100 text-green-800',
        failed: 'bg-red-100 text-red-800',
        pending: 'bg-yellow-100 text-yellow-800',
        refunded: 'bg-purple-100 text-purple-800',
    }
    return classes[props.status] || 'bg-gray-100 text-gray-800'
})

const dotClasses = computed(() => {
    const classes = {
        success: 'bg-green-400',
        failed: 'bg-red-400',
        pending: 'bg-yellow-400',
        refunded: 'bg-purple-400',
    }
    return classes[props.status] || 'bg-gray-400'
})
</script>
