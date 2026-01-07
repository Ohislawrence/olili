<template>
  <div v-if="links && links.length > 3" class="flex items-center justify-between">
    <div class="flex-1 flex justify-between sm:hidden">
      <button
        :disabled="!links[0].url"
        @click="$emit('page-change', getPageNumber(links[0].url))"
        class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        Previous
      </button>
      <button
        :disabled="!links[links.length - 1].url"
        @click="$emit('page-change', getPageNumber(links[links.length - 1].url))"
        class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        Next
      </button>
    </div>
    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
      <div>
        <p class="text-sm text-gray-700">
          Showing
          <span class="font-medium">{{ from }}</span>
          to
          <span class="font-medium">{{ to }}</span>
          of
          <span class="font-medium">{{ total }}</span>
          results
        </p>
      </div>
      <div>
        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
          <button
            v-for="(link, index) in links"
            :key="index"
            @click="!link.active && link.url && $emit('page-change', getPageNumber(link.url))"
            :disabled="!link.url || link.active"
            :class="[
              'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
              index === 0 ? 'rounded-l-md' : '',
              index === links.length - 1 ? 'rounded-r-md' : '',
              link.active
                ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
              (!link.url || link.active) ? 'disabled:opacity-50 disabled:cursor-not-allowed' : 'cursor-pointer'
            ]"
            v-html="link.label"
          ></button>
        </nav>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  links: {
    type: Array,
    default: () => []
  },
  from: {
    type: Number,
    default: 0
  },
  to: {
    type: Number,
    default: 0
  },
  total: {
    type: Number,
    default: 0
  }
})

defineEmits(['page-change'])

const getPageNumber = (url) => {
  if (!url) return null
  const match = url.match(/page=(\d+)/)
  return match ? parseInt(match[1]) : null
}
</script>
