<template>
    <AppLayout title="Login History">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Login History
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <!-- Stats -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <h3 class="text-lg font-semibold text-blue-800">Total Logins (30d)</h3>
                                <p class="text-2xl font-bold text-blue-600">{{ stats.total_logins_30d }}</p>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg">
                                <h3 class="text-lg font-semibold text-green-800">Success Rate</h3>
                                <p class="text-2xl font-bold text-green-600">{{ stats.success_rate_30d }}%</p>
                            </div>
                            <div class="bg-purple-50 p-4 rounded-lg">
                                <h3 class="text-lg font-semibold text-purple-800">Unique Devices</h3>
                                <p class="text-2xl font-bold text-purple-600">{{ stats.unique_devices }}</p>
                            </div>
                        </div>

                        <!-- Suspicious Activity Alert -->
                        <div v-if="suspiciousActivities.length" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <h3 class="text-lg font-semibold text-red-800 mb-2">Suspicious Activity Detected</h3>
                            <ul class="list-disc list-inside text-red-700">
                                <li v-for="activity in suspiciousActivities" :key="activity">
                                    {{ activity }}
                                </li>
                            </ul>
                        </div>

                        <!-- Login History Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Device</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Session Duration</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="login in loginHistory" :key="login.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ formatDate(login.login_at) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <div>{{ login.device_type }}</div>
                                            <div class="text-gray-500 text-xs">{{ login.browser }} on {{ login.platform }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <div>{{ login.get_location }}</div>
                                            <div class="text-gray-500 text-xs">{{ login.ip_address }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="login.was_successful ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                                {{ login.was_successful ? 'Success' : 'Failed' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ login.get_session_duration_formatted }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4" v-if="loginHistory.meta">
                            <Pagination :links="loginHistory.meta.links" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { ref, onMounted } from 'vue';

const props = defineProps({
    loginHistory: Object,
    stats: Object,
    suspiciousActivities: Array,
});

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleString();
};
</script>
