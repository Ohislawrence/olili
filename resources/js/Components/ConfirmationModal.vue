<template>
  <Modal :show="show" @close="close">
    <div class="p-6">
      <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 rounded-full bg-red-100">
        <ExclamationTriangleIcon class="w-6 h-6 text-red-600" />
      </div>

      <h2 class="text-lg font-semibold text-gray-900 text-center mb-2">
        <slot name="title"></slot>
      </h2>

      <p class="text-sm text-gray-600 text-center mb-6">
        <slot name="content"></slot>
      </p>

      <div class="flex justify-center space-x-3">
        <button
          @click="close"
          class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
        >
          Cancel
        </button>
        <button
          @click="confirm"
          class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
        >
          Confirm
        </button>
      </div>
    </div>
  </Modal>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue'
import Modal from '@/Components/Modal.vue'
import { ExclamationTriangleIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  show: Boolean,
})

const emit = defineEmits(['close', 'confirm'])

const close = () => emit('close')
const confirm = () => emit('confirm')
</script>
