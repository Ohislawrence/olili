<template>
  <div v-if="links.length > 3" class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
    <div class="flex flex-1 justify-between sm:hidden">
      <inertia-link
        :href="prevPageUrl"
        :class="{
          'pointer-events-none opacity-50': !prevPageUrl
        }"
        class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
      >
        Previous
      </inertia-link>
      <inertia-link
        :href="nextPageUrl"
        :class="{
          'pointer-events-none opacity-50': !nextPageUrl
        }"
        class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
      >
        Next
      </inertia-link>
    </div>
    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
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
        <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
          <inertia-link
            v-for="(link, index) in links"
            :key="index"
            :href="link.url"
            v-html="link.label"
            :class="{
              'z-10 bg-indigo-600 text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600': link.active,
              'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:outline-offset-0': !link.active && link.url,
              'text-gray-400 pointer-events-none': !link.url
            }"
            class="relative inline-flex items-center px-4 py-2 text-sm font-semibold ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0"
          />
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
    required: true
  },
  meta: {
    type: Object,
    default: () => ({})
  }
})

const prevPageUrl = computed(() => {
  return props.links[0]?.url || null
})

const nextPageUrl = computed(() => {
  return props.links[props.links.length - 1]?.url || null
})

const from = computed(() => {
  return props.meta.from || 0
})

const to = computed(() => {
  return props.meta.to || 0
})

const total = computed(() => {
  return props.meta.total || 0
})
</script>
