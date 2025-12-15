<template>
    <StudentLayout>
        <Head title="Notifications" />

        <div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 py-8">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Notifications</h1>
                            <p class="text-gray-600 mt-2">Your recent alerts and messages</p>
                        </div>
                        <div class="flex space-x-3">
                            <button
                                @click="markAllAsRead"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors"
                                :disabled="unreadCount === 0"
                            >
                                <CheckCircleIcon class="h-4 w-4 mr-2" />
                                Mark All Read
                            </button>
                            <button
                                @click="clearAll"
                                class="inline-flex items-center px-4 py-2 border border-red-300 text-red-700 font-medium rounded-lg hover:bg-red-50 transition-colors"
                            >
                                <TrashIcon class="h-4 w-4 mr-2" />
                                Clear All
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                        <div class="flex items-center">
                            <div class="p-3 bg-emerald-100 rounded-lg mr-4">
                                <BellIcon class="h-6 w-6 text-emerald-600" />
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Notifications</p>
                                <p class="text-2xl font-bold text-gray-900">{{ notifications.length }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                        <div class="flex items-center">
                            <div class="p-3 bg-blue-100 rounded-lg mr-4">
                                <EnvelopeIcon class="h-6 w-6 text-blue-600" />
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-600">Unread</p>
                                <p class="text-2xl font-bold text-gray-900">{{ unreadCount }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                        <div class="flex items-center">
                            <div class="p-3 bg-amber-100 rounded-lg mr-4">
                                <ClockIcon class="h-6 w-6 text-amber-600" />
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-600">Recent</p>
                                <p class="text-2xl font-bold text-gray-900">{{ recentCount }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter Tabs -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
                    <div class="flex space-x-1">
                        <button
                            v-for="tab in tabs"
                            :key="tab.key"
                            @click="activeTab = tab.key"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                            :class="activeTab === tab.key
                                ? 'bg-emerald-100 text-emerald-800'
                                : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'"
                        >
                            {{ tab.name }}
                            <span
                                v-if="tab.count !== null"
                                class="ml-2 px-2 py-0.5 text-xs rounded-full"
                                :class="activeTab === tab.key
                                    ? 'bg-emerald-200 text-emerald-900'
                                    : 'bg-gray-200 text-gray-700'"
                            >
                                {{ tab.count }}
                            </span>
                        </button>
                    </div>
                </div>

                <!-- Notifications List -->
                <div class="space-y-4">
                    <!-- Empty State -->
                    <div
                        v-if="filteredNotifications.length === 0"
                        class="text-center py-12 bg-white rounded-xl shadow-sm border border-gray-200"
                    >
                        <div class="mx-auto h-16 w-16 bg-gradient-to-r from-gray-400 to-gray-600 rounded-full flex items-center justify-center mb-4">
                            <BellIcon class="h-8 w-8 text-white" />
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No notifications</h3>
                        <p class="text-gray-600 mb-6">You're all caught up! No notifications to display.</p>
                    </div>

                    <!-- Notifications -->
                    <div
                        v-for="notification in filteredNotifications"
                        :key="notification.id"
                        class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200"
                        :class="{
                            'border-l-4 border-l-emerald-500': notification.type === 'success',
                            'border-l-4 border-l-blue-500': notification.type === 'info',
                            'border-l-4 border-l-amber-500': notification.type === 'warning',
                            'border-l-4 border-l-red-500': notification.type === 'error',
                            'bg-blue-50 border-blue-200': !notification.read_at,
                        }"
                    >
                        <div class="p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start space-x-4 flex-1">
                                    <!-- Notification Icon -->
                                    <div
                                        class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center"
                                        :class="{
                                            'bg-emerald-100': notification.type === 'success',
                                            'bg-blue-100': notification.type === 'info',
                                            'bg-amber-100': notification.type === 'warning',
                                            'bg-red-100': notification.type === 'error',
                                            'bg-gray-100': !notification.type,
                                        }"
                                    >
                                        <component
                                            :is="getNotificationIcon(notification.type)"
                                            class="h-5 w-5"
                                            :class="{
                                                'text-emerald-600': notification.type === 'success',
                                                'text-blue-600': notification.type === 'info',
                                                'text-amber-600': notification.type === 'warning',
                                                'text-red-600': notification.type === 'error',
                                                'text-gray-600': !notification.type,
                                            }"
                                        />
                                    </div>

                                    <!-- Notification Content -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center space-x-2 mb-1">
                                            <h3 class="text-lg font-semibold text-gray-900 truncate">
                                                {{ notification.data.title || getDefaultTitle(notification.type) }}
                                            </h3>
                                            <span
                                                v-if="!notification.read_at"
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                                            >
                                                New
                                            </span>
                                        </div>

                                        <p class="text-gray-600 mb-2">
                                            {{ notification.data.message || notification.data.content || 'No message content' }}
                                        </p>

                                        <!-- Notification Metadata -->
                                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                                            <span class="flex items-center">
                                                <CalendarIcon class="h-4 w-4 mr-1" />
                                                {{ formatDate(notification.created_at) }}
                                            </span>
                                            <span class="flex items-center">
                                                <ClockIcon class="h-4 w-4 mr-1" />
                                                {{ formatTime(notification.created_at) }}
                                            </span>

                                        </div>

                                        <!-- Action Buttons -->
                                        <div v-if="notification.data.action_url" class="mt-3 flex space-x-2">
                                            <Link
                                                :href="notification.data.action_url"
                                                class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-emerald-700 bg-emerald-50 rounded-lg hover:bg-emerald-100 transition-colors"
                                            >
                                                <ArrowTopRightOnSquareIcon class="h-4 w-4 mr-1" />
                                                View
                                            </Link>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Menu -->
                                <div class="flex items-center space-x-2">
                                    <button
                                        v-if="!notification.read_at"
                                        @click="markAsRead(notification.id)"
                                        class="p-2 text-gray-400 hover:text-emerald-600 transition-colors"
                                        title="Mark as read"
                                    >
                                        <CheckCircleIcon class="h-5 w-5" />
                                    </button>
                                    <button
                                        @click="deleteNotification(notification.id)"
                                        class="p-2 text-gray-400 hover:text-red-600 transition-colors"
                                        title="Delete notification"
                                    >
                                        <TrashIcon class="h-5 w-5" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Load More Button -->
                <div v-if="hasMorePages" class="mt-8 text-center">
                    <button
                        @click="loadMore"
                        class="inline-flex items-center px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors"
                    >
                        <ArrowPathIcon class="h-4 w-4 mr-2" />
                        Load More Notifications
                    </button>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, computed, reactive } from 'vue'
import StudentLayout from '@/Layouts/StudentLayout.vue'
import {
    BellIcon,
    EnvelopeIcon,
    ClockIcon,
    CheckCircleIcon,
    TrashIcon,
    ArrowPathIcon,
    CalendarIcon,
    ExclamationTriangleIcon,
    ExclamationCircleIcon,
    InformationCircleIcon,
    CheckCircleIcon as SuccessIcon,
    ArrowTopRightOnSquareIcon,
    ShieldExclamationIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
    notifications: {
        type: Object,
        default: () => ({ data: [] })
    },
    filters: {
        type: Object,
        default: () => ({})
    }
})

// Reactive state
const activeTab = ref('all')
const localNotifications = ref([...props.notifications.data])

// Tabs configuration
const tabs = computed(() => [
    { key: 'all', name: 'All Notifications', count: props.notifications.total },
    { key: 'unread', name: 'Unread', count: unreadCount.value },
    { key: 'success', name: 'Success', count: typeCount.value.success },
    { key: 'info', name: 'Info', count: typeCount.value.info },
    { key: 'warning', name: 'Warning', count: typeCount.value.warning },
    { key: 'error', name: 'Error', count: typeCount.value.error },
])

// Computed properties
const unreadCount = computed(() => {
    return localNotifications.value.filter(n => !n.read_at).length
})

const recentCount = computed(() => {
    const oneWeekAgo = new Date()
    oneWeekAgo.setDate(oneWeekAgo.getDate() - 7)
    return localNotifications.value.filter(n => new Date(n.created_at) > oneWeekAgo).length
})

const typeCount = computed(() => {
    return {
        success: localNotifications.value.filter(n => n.type === 'success').length,
        info: localNotifications.value.filter(n => n.type === 'info').length,
        warning: localNotifications.value.filter(n => n.type === 'warning').length,
        error: localNotifications.value.filter(n => n.type === 'error').length,
    }
})

const filteredNotifications = computed(() => {
    let notifications = localNotifications.value

    switch (activeTab.value) {
        case 'unread':
            notifications = notifications.filter(n => !n.read_at)
            break
        case 'success':
            notifications = notifications.filter(n => n.type === 'success')
            break
        case 'info':
            notifications = notifications.filter(n => n.type === 'info')
            break
        case 'warning':
            notifications = notifications.filter(n => n.type === 'warning')
            break
        case 'error':
            notifications = notifications.filter(n => n.type === 'error')
            break
    }

    return notifications
})

const hasMorePages = computed(() => {
    return props.notifications.next_page_url !== null
})

// Methods
const getNotificationIcon = (type) => {
    const icons = {
        success: SuccessIcon,
        info: InformationCircleIcon,
        warning: ExclamationTriangleIcon,
        error: ExclamationCircleIcon,
        security: ShieldExclamationIcon, // Add this icon
    }
    return icons[type] || BellIcon
}

const getDefaultTitle = (type) => {
    const titles = {
        success: 'Success',
        info: 'Information',
        warning: 'Warning',
        error: 'Error',
        security: 'Security Alert', // Add security title
    }
    return titles[type] || 'Notification'
}

const getTypeBadgeClass = (type) => {
    const classes = {
        success: 'bg-emerald-100 text-emerald-800',
        info: 'bg-blue-100 text-blue-800',
        warning: 'bg-amber-100 text-amber-800',
        error: 'bg-red-100 text-red-800',
        security: 'bg-purple-100 text-purple-800', // Add security class
    }
    return classes[type] || 'bg-gray-100 text-gray-800'
}

const formatDate = (dateString) => {
    const date = new Date(dateString)
    const now = new Date()
    const diffTime = Math.abs(now - date)
    const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24))

    if (diffDays === 0) return 'Today'
    if (diffDays === 1) return 'Yesterday'
    if (diffDays < 7) return `${diffDays} days ago`

    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const formatTime = (dateString) => {
    return new Date(dateString).toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit',
    })
}

const markAsRead = async (notificationId) => {
    try {
        await axios.post(route('student.notifications.mark-as-read', notificationId))

        // Update local state
        const notification = localNotifications.value.find(n => n.id === notificationId)
        if (notification) {
            notification.read_at = new Date().toISOString()
        }
    } catch (error) {
        console.error('Failed to mark notification as read:', error)
    }
}

const markAllAsRead = async () => {
    try {
        await axios.post(route('student.notifications.mark-all-read'))

        // Update local state
        localNotifications.value.forEach(notification => {
            notification.read_at = new Date().toISOString()
        })
    } catch (error) {
        console.error('Failed to mark all notifications as read:', error)
    }
}

const deleteNotification = async (notificationId) => {
    if (!confirm('Are you sure you want to delete this notification?')) {
        return
    }

    try {
        await axios.delete(route('student.notifications.destroy', notificationId))

        // Remove from local state
        localNotifications.value = localNotifications.value.filter(n => n.id !== notificationId)
    } catch (error) {
        console.error('Failed to delete notification:', error)
    }
}

const clearAll = async () => {
    if (!confirm('Are you sure you want to clear all notifications? This action cannot be undone.')) {
        return
    }

    try {
        await axios.delete(route('student.notifications.clear-all'))

        // Clear local state
        localNotifications.value = []
    } catch (error) {
        console.error('Failed to clear all notifications:', error)
    }
}

const loadMore = async () => {
    if (!props.notifications.next_page_url) return

    try {
        const response = await axios.get(props.notifications.next_page_url)
        localNotifications.value.push(...response.data.notifications.data)
        props.notifications.next_page_url = response.data.notifications.next_page_url
    } catch (error) {
        console.error('Failed to load more notifications:', error)
    }
}


</script>
