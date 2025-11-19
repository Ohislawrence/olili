<template>
  <div
    v-if="canInstall"
    class="fixed bottom-4 left-4 right-4 bg-white shadow-xl rounded-xl p-4 flex items-center justify-between z-50"
  >
    <div>
      <h3 class="font-semibold text-gray-900">Install App</h3>
      <p class="text-sm text-gray-500">Add this app to your device for faster access.</p>
    </div>

    <button
      @click="install"
      class="bg-emerald-600 text-white px-4 py-2 rounded-lg"
    >
      Install
    </button>
  </div>
</template>

<script setup>
import usePWA from "../composables/usePWA.js"
const { canInstall, install } = usePWA()
</script>
