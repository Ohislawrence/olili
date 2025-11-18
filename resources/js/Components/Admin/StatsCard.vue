<!-- resources/js/Components/Admin/StatsCard.vue -->
<template>
  <div class="bg-white rounded-lg shadow p-6">
    <div class="flex items-center">
      <div class="p-2 rounded-lg" :class="iconBgColor">
        <component
          :is="iconComponent"
          class="h-6 w-6"
          :class="iconColor"
        />
      </div>
      <div class="ml-4 flex-1">
        <p class="text-sm font-medium text-gray-600">
          {{ title }}
        </p>
        <div class="flex items-baseline">
          <p class="text-2xl font-semibold text-gray-900">
            {{ value }}
          </p>
          <div
            v-if="change !== undefined && change !== null"
            class="ml-2 flex items-baseline text-sm font-semibold"
            :class="changeColor"
          >
            <span v-if="changeType === 'percentage'">{{ Math.abs(change) }}%</span>
            <span v-else>{{ change >= 0 ? '+' : '' }}{{ change }}</span>
            <span class="ml-1 text-xs text-gray-500">{{ changeLabel }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import {
  UsersIcon,
  AcademicCapIcon,
  CurrencyDollarIcon,
  UserGroupIcon,
  ChartBarIcon,
  ExclamationTriangleIcon,
  CreditCardIcon,
  SignalIcon,
  SparklesIcon,
  TrophyIcon,
  CheckCircleIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  title: String,
  value: [String, Number],
  icon: {
    type: String,
    default: 'chart-bar',
    validator: (value) => [
      'users',
      'academic-cap',
      'currency-dollar',
      'user-group',
      'chart-bar',
      'exclamation',
      'credit-card',
      'signal',
      'sparkles',
      'trophy',
      'check-circle'
    ].includes(value)
  },
  color: {
    type: String,
    default: 'blue',
    validator: (value) => [
      'blue',
      'green',
      'purple',
      'orange',
      'red',
      'yellow',
      'indigo',
      'pink',
      'emerald',
      'teal'
    ].includes(value)
  },
  change: [Number, String],
  changeLabel: String,
  changeType: {
    type: String,
    default: 'number',
    validator: (value) => ['number', 'percentage'].includes(value)
  }
})

const iconComponent = computed(() => {
  const icons = {
    'users': UsersIcon,
    'academic-cap': AcademicCapIcon,
    'currency-dollar': CurrencyDollarIcon, // Use this for cash/money
    'user-group': UserGroupIcon,
    'chart-bar': ChartBarIcon,
    'exclamation': ExclamationTriangleIcon,
    'credit-card': CreditCardIcon,
    'signal': SignalIcon, // Use this for status
    'sparkles': SparklesIcon,
    'trophy': TrophyIcon,
    'check-circle': CheckCircleIcon,
  }
  return icons[props.icon] || ChartBarIcon
})

const colorMap = {
  blue: {
    bg: 'bg-blue-100',
    icon: 'text-blue-600',
    change: 'text-green-600'
  },
  green: {
    bg: 'bg-green-100',
    icon: 'text-green-600',
    change: 'text-green-600'
  },
  purple: {
    bg: 'bg-purple-100',
    icon: 'text-purple-600',
    change: 'text-green-600'
  },
  orange: {
    bg: 'bg-orange-100',
    icon: 'text-orange-600',
    change: 'text-green-600'
  },
  red: {
    bg: 'bg-red-100',
    icon: 'text-red-600',
    change: 'text-red-600'
  },
  yellow: {
    bg: 'bg-yellow-100',
    icon: 'text-yellow-600',
    change: 'text-yellow-600'
  },
  indigo: {
    bg: 'bg-indigo-100',
    icon: 'text-indigo-600',
    change: 'text-green-600'
  },
  pink: {
    bg: 'bg-pink-100',
    icon: 'text-pink-600',
    change: 'text-green-600'
  },
  emerald: {
    bg: 'bg-emerald-100',
    icon: 'text-emerald-600',
    change: 'text-green-600'
  },
  teal: {
    bg: 'bg-teal-100',
    icon: 'text-teal-600',
    change: 'text-green-600'
  }
}

const iconBgColor = computed(() => colorMap[props.color].bg)
const iconColor = computed(() => colorMap[props.color].icon)
const changeColor = computed(() => {
  if (props.changeType === 'percentage') {
    return props.change >= 0 ? 'text-green-600' : 'text-red-600'
  }
  // Handle negative numbers for non-percentage changes
  if (typeof props.change === 'number' && props.change < 0) {
    return 'text-red-600'
  }
  return colorMap[props.color].change
})
</script>
