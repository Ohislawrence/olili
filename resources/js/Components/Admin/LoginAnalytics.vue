<!-- resources/js/Components/Admin/LoginAnalytics.vue -->
<template>
  <div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200">
      <h2 class="text-lg font-semibold text-gray-900">Login Analytics</h2>
      <p class="text-sm text-gray-600 mt-1">User login patterns and device statistics</p>
    </div>

    <div class="p-6">
      <!-- Empty State -->
      <div
        v-if="!hasData"
        class="text-center py-8 text-gray-500"
      >
        <ComputerDesktopIcon class="mx-auto h-12 w-12 text-gray-400" />
        <p class="mt-2">No login data available</p>
        <p class="text-sm">Login analytics will appear here as users access the system.</p>
      </div>

      <!-- Data Content -->
      <div v-else>
        <!-- Platform Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <!-- Platforms -->
          <div class="bg-gray-50 rounded-lg p-4">
            <h3 class="text-sm font-medium text-gray-900 mb-3 flex items-center">
              <ComputerDesktopIcon class="h-4 w-4 mr-2" />
              Platforms
            </h3>
            <div class="space-y-2">
              <div
                v-for="(count, platform) in safeStats.platforms"
                :key="platform"
                class="flex justify-between items-center text-sm"
              >
                <span class="text-gray-600 truncate">{{ formatPlatformName(platform) }}</span>
                <span class="font-medium text-gray-900 bg-white px-2 py-1 rounded text-xs">
                  {{ count }}
                </span>
              </div>
            </div>
          </div>

          <!-- Browsers -->
          <div class="bg-gray-50 rounded-lg p-4">
            <h3 class="text-sm font-medium text-gray-900 mb-3 flex items-center">
              <GlobeAltIcon class="h-4 w-4 mr-2" />
              Browsers
            </h3>
            <div class="space-y-2">
              <div
                v-for="(count, browser) in safeStats.browsers"
                :key="browser"
                class="flex justify-between items-center text-sm"
              >
                <span class="text-gray-600 truncate">{{ formatBrowserName(browser) }}</span>
                <span class="font-medium text-gray-900 bg-white px-2 py-1 rounded text-xs">
                  {{ count }}
                </span>
              </div>
            </div>
          </div>

          <!-- Devices -->
          <div class="bg-gray-50 rounded-lg p-4">
            <h3 class="text-sm font-medium text-gray-900 mb-3 flex items-center">
              <DevicePhoneMobileIcon class="h-4 w-4 mr-2" />
              Devices
            </h3>
            <div class="space-y-2">
              <div
                v-for="(count, device) in safeStats.devices"
                :key="device"
                class="flex justify-between items-center text-sm"
              >
                <span class="text-gray-600 capitalize">{{ device }}</span>
                <span class="font-medium text-gray-900 bg-white px-2 py-1 rounded text-xs">
                  {{ count }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Login Times Chart -->
        <div class="border-t pt-6">
          <h3 class="text-sm font-medium text-gray-900 mb-4 flex items-center">
            <ClockIcon class="h-4 w-4 mr-2" />
            Login Times (24 Hours)
          </h3>

          <div
            class="flex items-end space-x-1 h-32"
            role="img"
            :aria-label="`Bar chart showing login distribution throughout the day. Peak hour: ${peakHour}`"
          >
            <div
              v-for="(count, hour) in safeStats.login_times"
              :key="hour"
              class="flex-1 flex flex-col items-center group relative"
            >
              <!-- Tooltip -->
              <div class="absolute -top-10 opacity-0 group-hover:opacity-100 transition-opacity duration-200 bg-gray-900 text-white text-xs rounded py-1 px-2 whitespace-nowrap z-10">
                {{ hour }}:00 - {{ count }} logins
              </div>

              <!-- Bar -->
              <div
                class="w-full bg-gradient-to-t from-blue-500 to-blue-400 rounded-t transition-all duration-500 hover:from-blue-600 hover:to-blue-500 cursor-help"
                :style="{ height: `${getBarHeight(count)}%` }"
                :class="{
                  'from-green-500 to-green-400 hover:from-green-600 hover:to-green-500': count === maxLoginCount
                }"
                :aria-label="`${count} logins at ${hour}:00`"
              ></div>

              <!-- Hour Label -->
              <span
                class="text-xs text-gray-500 mt-1"
                :class="{
                  'font-medium text-blue-600': count === maxLoginCount
                }"
              >
                {{ formatHour(hour) }}
              </span>
            </div>
          </div>

          <!-- Chart Legend -->
          <div class="flex justify-center items-center space-x-4 mt-4 text-xs text-gray-600">
            <div class="flex items-center">
              <div class="w-3 h-3 bg-blue-400 rounded mr-1"></div>
              <span>Normal</span>
            </div>
            <div class="flex items-center">
              <div class="w-3 h-3 bg-green-400 rounded mr-1"></div>
              <span>Peak ({{ peakHour }})</span>
            </div>
          </div>
        </div>

        <!-- Summary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6 pt-6 border-t">
          <div class="text-center">
            <div class="text-2xl font-bold text-gray-900">{{ totalLogins }}</div>
            <div class="text-sm text-gray-600">Total Logins</div>
          </div>
          <div class="text-center">
            <div class="text-2xl font-bold text-gray-900">{{ peakHour }}</div>
            <div class="text-sm text-gray-600">Peak Hour</div>
          </div>
          <div class="text-center">
            <div class="text-2xl font-bold text-gray-900">{{ mostPopularPlatform }}</div>
            <div class="text-sm text-gray-600">Top Platform</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import {
  ComputerDesktopIcon,
  GlobeAltIcon,
  DevicePhoneMobileIcon,
  ClockIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  stats: {
    type: Object,
    default: () => ({})
  }
})

// Safe computed properties with fallbacks
const safeStats = computed(() => ({
  platforms: props.stats.platforms || {},
  browsers: props.stats.browsers || {},
  devices: props.stats.devices || {},
  login_times: props.stats.login_times || generateEmptyHours()
}))

const hasData = computed(() => {
  const stats = safeStats.value
  return Object.keys(stats.platforms).length > 0 ||
         Object.keys(stats.browsers).length > 0 ||
         Object.keys(stats.devices).length > 0
})

const maxLoginCount = computed(() => {
  const times = Object.values(safeStats.value.login_times)
  return times.length > 0 ? Math.max(...times) : 1
})

const peakHour = computed(() => {
  const times = safeStats.value.login_times
  const peak = Object.entries(times).reduce((max, [hour, count]) =>
    count > max.count ? { hour, count } : max,
    { hour: '00', count: 0 }
  )
  return `${peak.hour}:00`
})

const totalLogins = computed(() => {
  const times = Object.values(safeStats.value.login_times)
  return times.reduce((sum, count) => sum + count, 0)
})

const mostPopularPlatform = computed(() => {
  const platforms = safeStats.value.platforms
  if (Object.keys(platforms).length === 0) return 'N/A'

  return Object.entries(platforms).reduce((max, [platform, count]) =>
    count > max.count ? { platform, count } : max,
    { platform: 'Unknown', count: 0 }
  ).platform
})

// Methods
const getBarHeight = (count) => {
  if (maxLoginCount.value === 0) return 0
  return Math.max((count / maxLoginCount.value) * 90, 5) // Minimum 5% height for visibility
}

const formatHour = (hour) => {
  const hourNum = parseInt(hour)
  return hourNum === 0 ? '12a' :
         hourNum < 12 ? `${hourNum}a` :
         hourNum === 12 ? '12p' :
         `${hourNum - 12}p`
}

const formatPlatformName = (platform) => {
  const names = {
    'Windows': 'Windows',
    'macOS': 'macOS',
    'Linux': 'Linux',
    'iOS': 'iOS',
    'Android': 'Android',
    'unknown': 'Unknown'
  }
  return names[platform] || platform
}

const formatBrowserName = (browser) => {
  const names = {
    'Chrome': 'Chrome',
    'Firefox': 'Firefox',
    'Safari': 'Safari',
    'Edge': 'Edge',
    'Opera': 'Opera',
    'unknown': 'Unknown'
  }
  return names[browser] || browser
}

// Generate empty hours for the chart
const generateEmptyHours = () => {
  const hours = {}
  for (let i = 0; i < 24; i++) {
    hours[i.toString().padStart(2, '0')] = 0
  }
  return hours
}
</script>
