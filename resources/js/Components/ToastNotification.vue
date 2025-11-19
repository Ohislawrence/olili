<!-- resources/js/Components/ToastNotification.vue -->
<template>
    <TransitionGroup
        name="toast"
        tag="div"
        class="fixed top-4 right-4 z-50 space-y-2 max-w-sm"
    >
        <div
            v-for="toast in toasts"
            :key="toast.id"
            :class="[
                'rounded-lg p-4 shadow-lg flex items-start space-x-3 transition-all duration-300',
                toastClasses[toast.type]
            ]"
        >
            <component :is="toastIcons[toast.type]" class="h-5 w-5 mt-0.5 flex-shrink-0" />
            <div class="flex-1">
                <p class="text-sm font-medium">
                    {{ toast.message }}
                </p>
            </div>
            <button @click="removeToast(toast.id)" class="hover:opacity-70">
                <XMarkIcon class="h-4 w-4" />
            </button>
        </div>
    </TransitionGroup>
</template>

<script setup>
import { ref } from 'vue'
import {
    CheckCircleIcon,
    ExclamationTriangleIcon,
    InformationCircleIcon,
    XMarkIcon
} from '@heroicons/vue/24/outline'

const toasts = ref([])
let toastId = 0

const toastClasses = {
    success: 'bg-green-50 border border-green-200 text-green-800',
    error: 'bg-red-50 border border-red-200 text-red-800',
    warning: 'bg-yellow-50 border border-yellow-200 text-yellow-800',
    info: 'bg-blue-50 border border-blue-200 text-blue-800'
}

const toastIcons = {
    success: CheckCircleIcon,
    error: ExclamationTriangleIcon,
    warning: ExclamationTriangleIcon,
    info: InformationCircleIcon
}

const showToast = (message, type = 'info', duration = 5000) => {
    const id = toastId++
    toasts.value.push({ id, message, type })

    if (duration > 0) {
        setTimeout(() => {
            removeToast(id)
        }, duration)
    }
}

const removeToast = (id) => {
    toasts.value = toasts.value.filter(toast => toast.id !== id)
}

// Make it available globally
window.showToast = showToast

defineExpose({ showToast })
</script>

<style scoped>
.toast-enter-active,
.toast-leave-active {
    transition: all 0.3s ease;
}

.toast-enter-from {
    opacity: 0;
    transform: translateX(100%);
}

.toast-leave-to {
    opacity: 0;
    transform: translateX(100%);
}
</style>
