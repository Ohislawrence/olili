<!-- resources/js/Pages/Admin/ExamBoards/Index.vue -->
<template>
    <AdminLayout>
        <Head title="Exam Boards Management" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Exam Boards</h1>
                    <p class="mt-1 text-sm text-gray-600">
                        Manage examination boards and their available subjects
                    </p>
                </div>
                <Link
                    :href="route('admin.exam-boards.create')"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    <PlusIcon class="h-4 w-4 mr-2" />
                    Add Exam Board
                </Link>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <AcademicCapIcon class="h-6 w-6 text-gray-400" />
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">
                                        Total Exam Boards
                                    </dt>
                                    <dd class="text-lg font-semibold text-gray-900">
                                        {{ exam_boards.length }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <BookOpenIcon class="h-6 w-6 text-gray-400" />
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">
                                        Active Boards
                                    </dt>
                                    <dd class="text-lg font-semibold text-gray-900">
                                        {{ activeBoardsCount }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <GlobeAltIcon class="h-6 w-6 text-gray-400" />
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">
                                        Countries
                                    </dt>
                                    <dd class="text-lg font-semibold text-gray-900">
                                        {{ countriesCount }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Exam Boards Table -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        All Exam Boards
                    </h3>
                </div>
                <ul class="divide-y divide-gray-200">
                    <li v-for="board in exam_boards" :key="board.id">
                        <div class="px-4 py-4 sm:px-6 hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                            <AcademicCapIcon class="h-6 w-6 text-indigo-600" />
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="flex items-center">
                                            <h4 class="text-sm font-semibold text-gray-900">
                                                {{ board.name }}
                                            </h4>
                                            <span
                                                class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                                :class="board.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                            >
                                                {{ board.is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </div>
                                        <div class="mt-1 text-sm text-gray-500">
                                            <span class="font-medium">Code:</span> {{ board.code }} •
                                            <span class="font-medium">Country:</span> {{ board.country }} •
                                            <span class="font-medium">Subjects:</span> {{ board.subjects?.length || 0 }}
                                        </div>
                                        <div class="mt-1 text-sm text-gray-500">
                                            <span class="font-medium">Courses:</span> {{ board.courses_count }} •
                                            <span class="font-medium">Students:</span> {{ board.student_profiles_count }}
                                        </div>
                                        <p v-if="board.description" class="mt-1 text-sm text-gray-600">
                                            {{ board.description }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <Link
                                        :href="route('admin.exam-boards.show', board.id)"
                                        class="text-indigo-600 hover:text-indigo-900 text-sm font-medium"
                                    >
                                        View
                                    </Link>
                                    <Link
                                        :href="route('admin.exam-boards.edit', board.id)"
                                        class="text-blue-600 hover:text-blue-900 text-sm font-medium"
                                    >
                                        Edit
                                    </Link>
                                    <button
                                        @click="toggleActive(board)"
                                        class="text-gray-600 hover:text-gray-900 text-sm font-medium"
                                    >
                                        {{ board.is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                    <button
                                        v-if="board.courses_count === 0 && board.student_profiles_count === 0"
                                        @click="confirmDelete(board)"
                                        class="text-red-600 hover:text-red-900 text-sm font-medium"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>

                <!-- Empty State -->
                <div v-if="exam_boards.length === 0" class="text-center py-12">
                    <AcademicCapIcon class="mx-auto h-12 w-12 text-gray-400" />
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No exam boards</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating a new exam board.</p>
                    <div class="mt-6">
                        <Link
                            :href="route('admin.exam-boards.create')"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                        >
                            <PlusIcon class="h-4 w-4 mr-2" />
                            Add Exam Board
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { computed } from 'vue'
import {
    PlusIcon,
    AcademicCapIcon,
    BookOpenIcon,
    GlobeAltIcon
} from '@heroicons/vue/24/outline'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    exam_boards: {
        type: Array,
        default: () => []
    }
})

const activeBoardsCount = computed(() => {
    return props.exam_boards.filter(board => board.is_active).length
})

const countriesCount = computed(() => {
    const countries = new Set(props.exam_boards.map(board => board.country))
    return countries.size
})

const toggleActive = (board) => {
    if (confirm(`Are you sure you want to ${board.is_active ? 'deactivate' : 'activate'} this exam board?`)) {
        router.post(route('admin.exam-boards.toggle-active', board.id))
    }
}

const confirmDelete = (board) => {
    if (confirm(`Are you sure you want to delete "${board.name}"? This action cannot be undone.`)) {
        router.delete(route('admin.exam-boards.destroy', board.id))
    }
}
</script>
