<template>
  <div
    v-if="visible"
    class="fixed inset-0 flex flex-col items-center justify-center bg-white/40 backdrop-blur-md z-[9999]"
  >
    <div class="animate-spin h-12 w-12 rounded-full border-4 border-green-500 border-t-transparent"></div>

    <div class="mt-4 text-green-700 text-sm font-medium">
      {{ message }}
    </div>
  </div>
</template>

<script setup>
defineProps({
  visible: Boolean,
  message: {
    type: String,
    default: ''
  }
})
</script>
