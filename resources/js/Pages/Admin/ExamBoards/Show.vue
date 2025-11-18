<!-- resources/js/Pages/Admin/ExamBoards/Show.vue -->
<template>
    <AdminLayout>
        <Head :title="`${exam_board.name} - Exam Board`" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex justify-between items-start">
                <div>
                    <div class="flex items-center space-x-2 mb-2">
                        <Link
                            :href="route('admin.exam-boards.index')"
                            class="text-indigo-600 hover:text-indigo-900 text-sm font-medium"
                        >
                            ← Back to Exam Boards
                        </Link>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="h-12 w-12 bg-indigo-100 rounded-full flex items-center justify-center">
                            <AcademicCapIcon class="h-6 w-6 text-indigo-600" />
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ exam_board.name }}</h1>
                            <div class="flex items-center space-x-4 mt-1">
                                <span class="text-sm text-gray-600">Code: {{ exam_board.code }}</span>
                                <span class="text-sm text-gray-600">Country: {{ exam_board.country }}</span>
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                    :class="exam_board.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                >
                                    {{ exam_board.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <Link
                        :href="route('admin.exam-boards.edit', exam_board.id)"
                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        Edit
                    </Link>
                    <button
                        @click="toggleActive"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        {{ exam_board.is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                </div>
            </div>

            <!-- Description -->
            <div v-if="exam_board.description" class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-sm text-blue-800">{{ exam_board.description }}</p>
            </div>

            <!-- Main Content -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Subjects -->
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Available Subjects</h3>
                        </div>
                        <div class="p-6">
                            <div class="flex flex-wrap gap-2">
                                <span
                                    v-for="(subject, index) in safeSubjects"
                                    :key="index"
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800"
                                >
                                    {{ subject }}
                                </span>
                            </div>
                            <p v-if="safeSubjects.length === 0" class="text-sm text-gray-500 italic">
                                No subjects configured for this exam board.
                            </p>
                        </div>
                    </div>

                    <!-- Recent Courses -->
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Recent Courses</h3>
                        </div>
                        <div class="divide-y divide-gray-200">
                            <div
                                v-for="course in safeRecentCourses"
                                :key="course.id"
                                class="px-6 py-4"
                            >
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-sm font-semibold text-gray-900">
                                            {{ course.subject || 'Unknown Subject' }}
                                        </h4>
                                        <p class="text-sm text-gray-500">
                                            Student: {{ course.student_profile?.user?.name || 'Unknown' }}
                                        </p>
                                    </div>
                                    <div class="text-right text-sm text-gray-500">
                                        <div>{{ formatDate(course.created_at) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-if="safeRecentCourses.length === 0" class="px-6 py-8 text-center">
                            <BookOpenIcon class="mx-auto h-8 w-8 text-gray-400" />
                            <p class="mt-2 text-sm text-gray-500">No courses found for this exam board.</p>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Statistics -->
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Statistics</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Total Courses</span>
                                <span class="text-sm text-gray-900">{{ exam_board.courses_count || 0 }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Total Students</span>
                                <span class="text-sm text-gray-900">{{ exam_board.student_profiles_count || 0 }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Available Subjects</span>
                                <span class="text-sm text-gray-900">{{ safeSubjects.length }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Country</span>
                                <span class="text-sm text-gray-900">{{ exam_board.country || 'Not specified' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Popular Subjects -->
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Popular Subjects</h3>
                        </div>
                        <div class="p-6 space-y-3">
                            <div
                                v-for="subject in safePopularSubjects"
                                :key="subject.subject"
                                class="flex justify-between items-center"
                            >
                                <span class="text-sm text-gray-900">{{ subject.subject || 'Unknown' }}</span>
                                <span class="text-sm text-gray-500">{{ subject.course_count || 0 }} courses</span>
                            </div>
                            <p v-if="safePopularSubjects.length === 0" class="text-sm text-gray-500 italic">
                                No course data available.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { AcademicCapIcon, BookOpenIcon } from '@heroicons/vue/24/outline'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { computed } from 'vue'

const props = defineProps({
    exam_board: {
        type: Object,
        default: () => ({})
    },
    recent_courses: {
        type: Array,
        default: () => []
    },
    popular_subjects: {
        type: Array,
        default: () => []
    },
})

// Safe computed properties with fallbacks
const safeSubjects = computed(() => {
    return props.exam_board.subjects || []
})

const safeRecentCourses = computed(() => {
    return props.recent_courses || []
})

const safePopularSubjects = computed(() => {
    return props.popular_subjects || []
})

const formatDate = (dateString) => {
    if (!dateString) return 'Unknown date'
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    })
}

const toggleActive = () => {
    if (confirm(`Are you sure you want to ${props.exam_board.is_active ? 'deactivate' : 'activate'} this exam board?`)) {
        router.post(route('admin.exam-boards.toggle-active', props.exam_board.id))
    }
}
</script>
