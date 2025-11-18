<!-- resources/js/Pages/Admin/AiAnalytics/Show.vue -->
<template>
    <AdminLayout>
        <Head :title="`AI Usage Log #${usageLog.id}`" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex justify-between items-start">
                <div>
                    <div class="flex items-center space-x-2 mb-2">
                        <Link
                            :href="route('admin.ai-analytics.index')"
                            class="text-indigo-600 hover:text-indigo-900 text-sm font-medium"
                        >
                            ← Back to Analytics
                        </Link>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        Usage Log #{{ usageLog.id }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">
                        Detailed view of AI usage request
                    </p>
                </div>
                <div class="flex space-x-3">
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                        :class="usageLog.success ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                    >
                        <CheckCircleIcon v-if="usageLog.success" class="h-4 w-4 mr-1" />
                        <XCircleIcon v-else class="h-4 w-4 mr-1" />
                        {{ usageLog.success ? 'Success' : 'Failed' }}
                    </span>
                </div>
            </div>

            <!-- Main Content -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column - Basic Info -->
                <div class="space-y-6">
                    <!-- Request Info -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Request Information</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">User</dt>
                                <dd class="text-sm text-gray-900 mt-1">
                                    {{ usageLog.user?.name || 'System' }}
                                    <span v-if="usageLog.user?.email" class="text-gray-500 block">
                                        {{ usageLog.user.email }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">AI Provider</dt>
                                <dd class="text-sm text-gray-900 mt-1">{{ usageLog.ai_provider?.name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Model Used</dt>
                                <dd class="text-sm text-gray-900 mt-1">{{ usageLog.model_used }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Purpose</dt>
                                <dd class="text-sm text-gray-900 mt-1 capitalize">
                                    {{ usageLog.purpose.replace(/_/g, ' ') }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Endpoint</dt>
                                <dd class="text-sm text-gray-900 mt-1 font-mono">{{ usageLog.endpoint }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Token Usage -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Token Usage</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Prompt Tokens</dt>
                                <dd class="text-sm text-gray-900 mt-1">{{ usageLog.prompt_tokens?.toLocaleString() }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Completion Tokens</dt>
                                <dd class="text-sm text-gray-900 mt-1">{{ usageLog.completion_tokens?.toLocaleString() }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Total Tokens</dt>
                                <dd class="text-sm text-gray-900 mt-1 font-semibold">
                                    {{ usageLog.total_tokens?.toLocaleString() }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Cost</dt>
                                <dd class="text-sm text-gray-900 mt-1 font-semibold">
                                    ${{ usageLog.cost?.toFixed(6) }}
                                </dd>
                            </div>
                            <div v-if="usageLog.cost > 0">
                                <dt class="text-sm font-medium text-gray-500">Tokens per Dollar</dt>
                                <dd class="text-sm text-gray-900 mt-1">
                                    {{ Math.round(usageLog.total_tokens / usageLog.cost)?.toLocaleString() }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Right Column - Metadata -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Error Message -->
                    <div v-if="!usageLog.success" class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-red-900 mb-4">Error Details</h3>
                        <div class="bg-red-50 border border-red-200 rounded-md p-4">
                            <p class="text-sm text-red-800 font-mono">{{ usageLog.error_message }}</p>
                        </div>
                    </div>

                    <!-- Request Metadata -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Request Metadata</h3>
                        <div v-if="usageLog.request_metadata" class="bg-gray-50 rounded-md p-4">
                            <pre class="text-sm text-gray-800 whitespace-pre-wrap">{{ JSON.stringify(usageLog.request_metadata, null, 2) }}</pre>
                        </div>
                        <div v-else class="text-sm text-gray-500 italic">
                            No request metadata available
                        </div>
                    </div>

                    <!-- Response Metadata -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Response Metadata</h3>
                        <div v-if="usageLog.response_metadata" class="bg-gray-50 rounded-md p-4">
                            <pre class="text-sm text-gray-800 whitespace-pre-wrap">{{ JSON.stringify(usageLog.response_metadata, null, 2) }}</pre>
                        </div>
                        <div v-else class="text-sm text-gray-500 italic">
                            No response metadata available
                        </div>
                    </div>

                    <!-- Timestamps -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Timestamps</h3>
                        <dl class="space-y-2">
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Created At</dt>
                                <dd class="text-sm text-gray-900">{{ formatDateTime(usageLog.created_at) }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Updated At</dt>
                                <dd class="text-sm text-gray-900">{{ formatDateTime(usageLog.updated_at) }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { CheckCircleIcon, XCircleIcon } from '@heroicons/vue/24/outline'

defineProps({
    usageLog: Object,
})

const formatDateTime = (dateString) => {
    return new Date(dateString).toLocaleString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
    })
}
</script>
