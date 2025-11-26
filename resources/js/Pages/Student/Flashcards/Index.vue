<template>
    <StudentLayout>
        <Head title="My Flashcards" />

        <div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">My Flashcards</h1>
                            <p class="text-gray-600 mt-2">Create and study flashcards from your course topics</p>
                        </div>
                        <Link
                            :href="route('student.flashcards.create')"
                            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-semibold rounded-lg hover:from-emerald-700 hover:to-teal-700 transition-all duration-200 shadow-sm hover:shadow-md"
                        >
                            <PlusIcon class="h-5 w-5 mr-2" />
                            Create New Set
                        </Link>
                    </div>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                        <div class="flex items-center">
                            <div class="p-3 bg-emerald-100 rounded-lg mr-4">
                                <AcademicCapIcon class="h-6 w-6 text-emerald-600" />
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Sets</p>
                                <p class="text-2xl font-bold text-gray-900">{{ flashcard_sets.total }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                        <div class="flex items-center">
                            <div class="p-3 bg-amber-100 rounded-lg mr-4">
                                <ClockIcon class="h-6 w-6 text-amber-600" />
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-600">Due for Review</p>
                                <p class="text-2xl font-bold text-gray-900">{{ dueCount }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                        <div class="flex items-center">
                            <div class="p-3 bg-blue-100 rounded-lg mr-4">
                                <BookOpenIcon class="h-6 w-6 text-blue-600" />
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-600">Active Courses</p>
                                <p class="text-2xl font-bold text-gray-900">{{ courses.length }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Flashcard Sets Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <div
                        v-for="set in flashcard_sets.data"
                        :key="set.id"
                        class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200 overflow-hidden"
                    >
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-3">
                                <h3 class="text-lg font-semibold text-gray-900 truncate">{{ set.title }}</h3>
                                <div class="flex space-x-1">
                                    <button
                                        @click="deleteSet(set)"
                                        class="p-1 text-gray-400 hover:text-red-500 transition-colors"
                                    >
                                        <TrashIcon class="h-4 w-4" />
                                    </button>
                                </div>
                            </div>

                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ set.description || 'No description' }}</p>

                            <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                                <span class="flex items-center">
                                    <AcademicCapIcon class="h-4 w-4 mr-1" />
                                    {{ set.course.title }}
                                </span>
                                <span class="flex items-center">
                                    <DocumentTextIcon class="h-4 w-4 mr-1" />
                                    {{ set.flashcards_count }} cards
                                </span>
                            </div>

                            <div class="flex space-x-2">
                                <Link
                                    :href="route('student.flashcards.show', set.id)"
                                    class="flex-1 text-center px-3 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
                                >
                                    View
                                </Link>
                                <Link
                                    :href="route('student.flashcards.study', set.id)"
                                    class="flex-1 text-center px-3 py-2 text-sm font-medium text-white bg-gradient-to-r from-emerald-600 to-teal-600 rounded-lg hover:from-emerald-700 hover:to-teal-700 transition-colors"
                                >
                                    Study
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div
                    v-if="flashcard_sets.data.length === 0"
                    class="text-center py-12"
                >
                    <div class="mx-auto h-16 w-16 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full flex items-center justify-center mb-4">
                        <AcademicCapIcon class="h-8 w-8 text-white" />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No flashcard sets yet</h3>
                    <p class="text-gray-600 mb-6">Create your first flashcard set to start studying</p>
                    <Link
                        :href="route('student.flashcards.create')"
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-semibold rounded-lg hover:from-emerald-700 hover:to-teal-700 transition-all duration-200"
                    >
                        <PlusIcon class="h-5 w-5 mr-2" />
                        Create Flashcard Set
                    </Link>
                </div>

                <!-- Pagination -->
                <div v-if="flashcard_sets.data.length > 0" class="mt-8">
                    <Pagination :links="flashcard_sets.links" />
                </div>
            </div>
        </div>
    </StudentLayout>
</template>

<script setup>
import { reactive, ref, computed, onMounted, watch } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import {
    PlusIcon,
    AcademicCapIcon,
    ClockIcon,
    BookOpenIcon,
    DocumentTextIcon,
    TrashIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
    courses: Array,
    flashcard_sets: Object,
})

const dueCount = computed(() => {
    return props.flashcard_sets.data.reduce((total, set) => {
        return total + (set.due_flashcards_count || 0)
    }, 0)
})

const deleteSet = (set) => {
    if (confirm(`Are you sure you want to delete "${set.title}"?`)) {
        router.delete(route('student.flashcards.destroy', set.id))
    }
}
</script>
