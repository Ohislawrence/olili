<!-- resources/js/Pages/Admin/Settings/Index.vue -->
<template>
    <AdminLayout>
        <Head title="System Settings" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">System Settings</h1>
                    <p class="mt-1 text-sm text-gray-600">
                        Manage system configuration and application settings
                    </p>
                </div>
                <div class="flex space-x-3">
                    <button
                        @click="clearCache"
                        :disabled="cacheClearing"
                        class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-800 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                    >
                        <ArrowPathIcon class="h-4 w-4 mr-2" />
                        {{ cacheClearing ? 'Clearing...' : 'Clear Cache' }}
                    </button>
                    <button
                        @click="runMaintenance"
                        :disabled="maintenanceRunning"
                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                    >
                        <Cog6ToothIcon class="h-4 w-4 mr-2" />
                        {{ maintenanceRunning ? 'Running...' : 'Run Maintenance' }}
                    </button>
                </div>
            </div>

            <!-- System Information -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">System Information</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Laravel Version</label>
                            <p class="mt-1 text-sm text-gray-900">{{ system_info.laravel_version }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">PHP Version</label>
                            <p class="mt-1 text-sm text-gray-900">{{ system_info.php_version }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Environment</label>
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1"
                                :class="environmentClass"
                            >
                                {{ system_info.environment }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Debug Mode</label>
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1"
                                :class="system_info.debug_mode ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'"
                            >
                                {{ system_info.debug_mode ? 'Enabled' : 'Disabled' }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Timezone</label>
                            <p class="mt-1 text-sm text-gray-900">{{ system_info.timezone }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Maintenance Mode</label>
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1"
                                :class="system_info.maintenance_mode ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'"
                            >
                                {{ system_info.maintenance_mode ? 'Enabled' : 'Disabled' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- AI Settings -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">AI Settings</h3>
                </div>
                <form @submit.prevent="updateAiSettings">
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Default Provider -->
                            <div>
                                <label for="default_provider" class="block text-sm font-medium text-gray-700">
                                    Default AI Provider *
                                </label>
                                <select
                                    id="default_provider"
                                    v-model="aiForm.default_provider"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :class="{ 'border-red-300': aiForm.errors.default_provider }"
                                >
                                    <option value="openai">OpenAI</option>
                                    <option value="anthropic">Anthropic</option>
                                    <option value="deepseek">DeepSeek</option>
                                    <option value="azure">Azure OpenAI</option>
                                </select>
                                <p v-if="aiForm.errors.default_provider" class="mt-1 text-sm text-red-600">
                                    {{ aiForm.errors.default_provider }}
                                </p>
                            </div>

                            <!-- Max Tokens -->
                            <div>
                                <label for="max_tokens_per_request" class="block text-sm font-medium text-gray-700">
                                    Max Tokens per Request *
                                </label>
                                <input
                                    type="number"
                                    id="max_tokens_per_request"
                                    v-model="aiForm.max_tokens_per_request"
                                    min="100"
                                    max="32000"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :class="{ 'border-red-300': aiForm.errors.max_tokens_per_request }"
                                />
                                <p v-if="aiForm.errors.max_tokens_per_request" class="mt-1 text-sm text-red-600">
                                    {{ aiForm.errors.max_tokens_per_request }}
                                </p>
                            </div>

                            <!-- Default Temperature -->
                            <div>
                                <label for="default_temperature" class="block text-sm font-medium text-gray-700">
                                    Default Temperature *
                                </label>
                                <input
                                    type="number"
                                    id="default_temperature"
                                    v-model="aiForm.default_temperature"
                                    min="0"
                                    max="2"
                                    step="0.1"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :class="{ 'border-red-300': aiForm.errors.default_temperature }"
                                />
                                <p v-if="aiForm.errors.default_temperature" class="mt-1 text-sm text-red-600">
                                    {{ aiForm.errors.default_temperature }}
                                </p>
                                <p class="mt-1 text-sm text-gray-500">
                                    Lower values = more focused, Higher values = more creative
                                </p>
                            </div>

                            <!-- Cost Alert Threshold -->
                            <div>
                                <label for="cost_alert_threshold" class="block text-sm font-medium text-gray-700">
                                    Cost Alert Threshold ($) *
                                </label>
                                <input
                                    type="number"
                                    id="cost_alert_threshold"
                                    v-model="aiForm.cost_alert_threshold"
                                    min="0"
                                    step="0.01"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :class="{ 'border-red-300': aiForm.errors.cost_alert_threshold }"
                                />
                                <p v-if="aiForm.errors.cost_alert_threshold" class="mt-1 text-sm text-red-600">
                                    {{ aiForm.errors.cost_alert_threshold }}
                                </p>
                            </div>
                        </div>

                        <!-- Checkbox Options -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="flex items-center">
                                <input
                                    type="checkbox"
                                    id="enable_cost_tracking"
                                    v-model="aiForm.enable_cost_tracking"
                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                />
                                <label for="enable_cost_tracking" class="ml-2 block text-sm text-gray-900">
                                    Enable Cost Tracking
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input
                                    type="checkbox"
                                    id="auto_switch_provider"
                                    v-model="aiForm.auto_switch_provider"
                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                />
                                <label for="auto_switch_provider" class="ml-2 block text-sm text-gray-900">
                                    Auto-switch Provider on Failure
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                        <button
                            type="submit"
                            :disabled="aiForm.processing"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                        >
                            <span v-if="aiForm.processing">Saving...</span>
                            <span v-else>Save AI Settings</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Course Settings -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Course Settings</h3>
                </div>
                <form @submit.prevent="updateCourseSettings">
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Max Weekly Study Hours -->
                            <div>
                                <label for="max_weekly_study_hours" class="block text-sm font-medium text-gray-700">
                                    Max Weekly Study Hours *
                                </label>
                                <input
                                    type="number"
                                    id="max_weekly_study_hours"
                                    v-model="courseForm.max_weekly_study_hours"
                                    min="1"
                                    max="40"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :class="{ 'border-red-300': courseForm.errors.max_weekly_study_hours }"
                                />
                                <p v-if="courseForm.errors.max_weekly_study_hours" class="mt-1 text-sm text-red-600">
                                    {{ courseForm.errors.max_weekly_study_hours }}
                                </p>
                            </div>

                            <!-- Default Course Duration -->
                            <div>
                                <label for="default_course_duration_weeks" class="block text-sm font-medium text-gray-700">
                                    Default Course Duration (Weeks) *
                                </label>
                                <input
                                    type="number"
                                    id="default_course_duration_weeks"
                                    v-model="courseForm.default_course_duration_weeks"
                                    min="1"
                                    max="52"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :class="{ 'border-red-300': courseForm.errors.default_course_duration_weeks }"
                                />
                                <p v-if="courseForm.errors.default_course_duration_weeks" class="mt-1 text-sm text-red-600">
                                    {{ courseForm.errors.default_course_duration_weeks }}
                                </p>
                            </div>
                        </div>

                        <!-- Checkbox Options -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="flex items-center">
                                <input
                                    type="checkbox"
                                    id="auto_generate_content"
                                    v-model="courseForm.auto_generate_content"
                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                />
                                <label for="auto_generate_content" class="ml-2 block text-sm text-gray-900">
                                    Auto-generate Course Content
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input
                                    type="checkbox"
                                    id="enable_ai_grading"
                                    v-model="courseForm.enable_ai_grading"
                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                />
                                <label for="enable_ai_grading" class="ml-2 block text-sm text-gray-900">
                                    Enable AI Grading
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input
                                    type="checkbox"
                                    id="enable_progress_tracking"
                                    v-model="courseForm.enable_progress_tracking"
                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                />
                                <label for="enable_progress_tracking" class="ml-2 block text-sm text-gray-900">
                                    Enable Progress Tracking
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input
                                    type="checkbox"
                                    id="enable_chat_restrictions"
                                    v-model="courseForm.enable_chat_restrictions"
                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                />
                                <label for="enable_chat_restrictions" class="ml-2 block text-sm text-gray-900">
                                    Enable Chat Restrictions
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                        <button
                            type="submit"
                            :disabled="courseForm.processing"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                        >
                            <span v-if="courseForm.processing">Saving...</span>
                            <span v-else>Save Course Settings</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- System Logs -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">System Logs</h3>
                </div>
                <div class="p-6">
                    <div class="flex space-x-4 mb-4">
                        <select
                            v-model="logType"
                            @change="loadLogs"
                            class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="laravel">Laravel Log</option>
                            <option value="ai">AI Log</option>
                            <option value="jobs">Queue Log</option>
                        </select>
                        <select
                            v-model="logLines"
                            @change="loadLogs"
                            class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="50">Last 50 lines</option>
                            <option value="100">Last 100 lines</option>
                            <option value="200">Last 200 lines</option>
                            <option value="500">Last 500 lines</option>
                        </select>
                        <button
                            @click="loadLogs"
                            :disabled="loadingLogs"
                            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            <ArrowPathIcon class="h-4 w-4 mr-2" :class="{ 'animate-spin': loadingLogs }" />
                            Refresh
                        </button>
                    </div>
                    <div class="bg-gray-900 rounded-lg p-4 font-mono text-sm text-green-400 max-h-96 overflow-y-auto">
                        <div v-if="loadingLogs" class="text-center py-4">
                            <ArrowPathIcon class="h-6 w-6 animate-spin mx-auto" />
                            <p class="mt-2">Loading logs...</p>
                        </div>
                        <div v-else-if="systemLogs.length === 0" class="text-center py-4 text-gray-500">
                            No log data available
                        </div>
                        <div v-else>
                            <div
                                v-for="(log, index) in systemLogs"
                                :key="index"
                                class="whitespace-pre-wrap break-words border-b border-gray-700 py-1"
                            >
                                {{ log }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head, router } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'
import {
    ArrowPathIcon,
    Cog6ToothIcon,
    ExclamationTriangleIcon
} from '@heroicons/vue/24/outline'
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    system_info: Object,
    ai_settings: Object,
    course_settings: Object,
})

// Reactive data
const cacheClearing = ref(false)
const maintenanceRunning = ref(false)
const loadingLogs = ref(false)
const systemLogs = ref([])
const logType = ref('laravel')
const logLines = ref(100)

// Forms
const aiForm = useForm({
    default_provider: props.ai_settings.default_provider,
    max_tokens_per_request: props.ai_settings.max_tokens_per_request,
    default_temperature: props.ai_settings.default_temperature,
    cost_alert_threshold: props.ai_settings.cost_alert_threshold,
    enable_cost_tracking: props.ai_settings.enable_cost_tracking,
    auto_switch_provider: props.ai_settings.auto_switch_provider,
})

const courseForm = useForm({
    auto_generate_content: props.course_settings.auto_generate_content,
    enable_ai_grading: props.course_settings.enable_ai_grading,
    max_weekly_study_hours: props.course_settings.max_weekly_study_hours,
    default_course_duration_weeks: props.course_settings.default_course_duration_weeks,
    enable_progress_tracking: props.course_settings.enable_progress_tracking,
    enable_chat_restrictions: props.course_settings.enable_chat_restrictions,
})

// Computed properties
const environmentClass = computed(() => {
    const env = props.system_info.environment
    switch (env) {
        case 'production': return 'bg-green-100 text-green-800'
        case 'staging': return 'bg-yellow-100 text-yellow-800'
        case 'local': return 'bg-blue-100 text-blue-800'
        default: return 'bg-gray-100 text-gray-800'
    }
})

// Methods
const clearCache = async () => {
    if (!confirm('Are you sure you want to clear all system cache? This may temporarily slow down the application.')) {
        return
    }

    cacheClearing.value = true
    try {
        await router.post(route('admin.settings.clear-cache'), {}, {
            preserveScroll: true,
            onFinish: () => {
                cacheClearing.value = false
            }
        })
    } catch (error) {
        cacheClearing.value = false
        console.error('Failed to clear cache:', error)
    }
}

const runMaintenance = async () => {
    if (!confirm('Run system maintenance tasks? This will restart queues and run scheduled tasks.')) {
        return
    }

    maintenanceRunning.value = true
    try {
        await router.post(route('admin.settings.run-maintenance'), {}, {
            preserveScroll: true,
            onFinish: () => {
                maintenanceRunning.value = false
            }
        })
    } catch (error) {
        maintenanceRunning.value = false
        console.error('Failed to run maintenance:', error)
    }
}

const updateAiSettings = () => {
    aiForm.post(route('admin.settings.update-ai'), {
        preserveScroll: true,
    })
}

const updateCourseSettings = () => {
    courseForm.post(route('admin.settings.update-course'), {
        preserveScroll: true,
    })
}

const loadLogs = async () => {
    loadingLogs.value = true
    try {
        const response = await axios.get(route('admin.settings.get-logs'), {
            params: {
                type: logType.value,
                lines: logLines.value
            }
        })
        systemLogs.value = response.data.logs
    } catch (error) {
        console.error('Failed to load logs:', error)
        systemLogs.value = ['Error loading log file.']
    } finally {
        loadingLogs.value = false
    }
}

// Lifecycle
onMounted(() => {
    loadLogs()
})
</script>
