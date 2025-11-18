<!-- resources/js/Components/SummaryCard.vue -->
<template>
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <component
                        :is="iconComponent"
                        class="h-6 w-6 text-gray-400"
                        :class="iconColor"
                    />
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            {{ title }}
                        </dt>
                        <dd class="text-lg font-semibold text-gray-900">
                            {{ value }}
                        </dd>
                        <dd class="text-sm text-gray-500 mt-1">
                            {{ subtitle }}
                            <span
                                v-if="trend"
                                class="ml-2"
                                :class="trendPositive ? 'text-green-600' : 'text-red-600'"
                            >
                                {{ trend }}
                            </span>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import {
    CurrencyDollarIcon,
    ChatBubbleLeftRightIcon,
    CpuChipIcon,
    CheckBadgeIcon,
    UserGroupIcon,
    ClockIcon
} from '@heroicons/vue/24/outline'
import { computed } from 'vue'

const props = defineProps({
    title: String,
    value: String,
    subtitle: String,
    trend: String,
    trendPositive: {
        type: Boolean,
        default: true
    },
    icon: String
})

const iconComponent = computed(() => {
    const icons = {
        CurrencyDollarIcon,
        ChatBubbleLeftRightIcon,
        CpuChipIcon,
        CheckBadgeIcon,
        UserGroupIcon,
        ClockIcon
    }
    return icons[props.icon] || CurrencyDollarIcon
})

const iconColor = computed(() => {
    return props.trendPositive ? 'text-green-500' : 'text-red-500'
})
</script>
