<!-- resources/js/Components/FlashMessages.vue -->
<template>
    <div v-if="$page.props.flash && hasMessages" class="fixed top-4 right-4 z-50 space-y-2 max-w-sm">
        <!-- Success Message -->
        <div
            v-if="$page.props.flash.success"
            class="bg-green-50 border border-green-200 rounded-lg p-4 shadow-lg flex items-start space-x-3"
        >
            <CheckCircleIcon class="h-5 w-5 text-green-600 mt-0.5 flex-shrink-0" />
            <div class="flex-1">
                <p class="text-sm font-medium text-green-800">
                    {{ $page.props.flash.success }}
                </p>
            </div>
            <button @click="clearFlash('success')" class="text-green-600 hover:text-green-800">
                <XMarkIcon class="h-4 w-4" />
            </button>
        </div>

        <!-- Error Message -->
        <div
            v-if="$page.props.flash.error"
            class="bg-red-50 border border-red-200 rounded-lg p-4 shadow-lg flex items-start space-x-3"
        >
            <ExclamationTriangleIcon class="h-5 w-5 text-red-600 mt-0.5 flex-shrink-0" />
            <div class="flex-1">
                <p class="text-sm font-medium text-red-800">
                    {{ $page.props.flash.error }}
                </p>
            </div>
            <button @click="clearFlash('error')" class="text-red-600 hover:text-red-800">
                <XMarkIcon class="h-4 w-4" />
            </button>
        </div>

        <!-- Warning Message -->
        <div
            v-if="$page.props.flash.warning"
            class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 shadow-lg flex items-start space-x-3"
        >
            <ExclamationTriangleIcon class="h-5 w-5 text-yellow-600 mt-0.5 flex-shrink-0" />
            <div class="flex-1">
                <p class="text-sm font-medium text-yellow-800">
                    {{ $page.props.flash.warning }}
                </p>
            </div>
            <button @click="clearFlash('warning')" class="text-yellow-600 hover:text-yellow-800">
                <XMarkIcon class="h-4 w-4" />
            </button>
        </div>

        <!-- Info Message -->
        <div
            v-if="$page.props.flash.info"
            class="bg-blue-50 border border-blue-200 rounded-lg p-4 shadow-lg flex items-start space-x-3"
        >
            <InformationCircleIcon class="h-5 w-5 text-blue-600 mt-0.5 flex-shrink-0" />
            <div class="flex-1">
                <p class="text-sm font-medium text-blue-800">
                    {{ $page.props.flash.info }}
                </p>
            </div>
            <button @click="clearFlash('info')" class="text-blue-600 hover:text-blue-800">
                <XMarkIcon class="h-4 w-4" />
            </button>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import {
    CheckCircleIcon,
    ExclamationTriangleIcon,
    InformationCircleIcon,
    XMarkIcon
} from '@heroicons/vue/24/outline'

const page = usePage()

const hasMessages = computed(() => {
    return page.props.flash && (
        page.props.flash.success ||
        page.props.flash.error ||
        page.props.flash.warning ||
        page.props.flash.info
    )
})

const clearFlash = (type) => {
    router.reload({ only: ['flash'] })
}
</script>
