<template>
    <StudentLayout>
        <Head title="My Flashcards" />

        <div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 py-4 sm:py-8">
            <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8">
                <!-- Header - Mobile Optimized -->
                <div class="mb-6 sm:mb-8">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">My Flashcards</h1>
                            <p class="text-sm sm:text-base text-gray-600 mt-1 sm:mt-2">Create and study flashcards from your course topics</p>
                        </div>
                        <Link
                            :href="route('student.flashcards.create')"
                            class="mt-3 sm:mt-0 inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-sm sm:text-base font-semibold rounded-lg hover:from-emerald-700 hover:to-teal-700 transition-all duration-200 shadow-sm hover:shadow-md"
                        >
                            <PlusIcon class="h-4 w-4 sm:h-5 sm:w-5 mr-1 sm:mr-2" />
                            <span class="hidden xs:inline">Create New Set</span>
                            <span class="xs:hidden">Create</span>
                        </Link>
                    </div>
                </div>

                <!-- Stats - Mobile Grid -->
                <div class="grid grid-cols-3 gap-2 sm:gap-6 mb-6 sm:mb-8">
                    <div class="bg-white rounded-lg sm:rounded-xl p-3 sm:p-6 shadow-sm border border-gray-200">
                        <div class="flex flex-col sm:flex-row sm:items-center">
                            <div class="p-1.5 sm:p-3 bg-emerald-100 rounded-lg sm:mr-4 mb-1 sm:mb-0 inline-block">
                                <AcademicCapIcon class="h-4 w-4 sm:h-6 sm:w-6 text-emerald-600" />
                            </div>
                            <div class="text-center sm:text-left">
                                <p class="text-xs sm:text-sm font-medium text-gray-600">Total</p>
                                <p class="text-lg sm:text-2xl font-bold text-gray-900">{{ flashcard_sets.total }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg sm:rounded-xl p-3 sm:p-6 shadow-sm border border-gray-200">
                        <div class="flex flex-col sm:flex-row sm:items-center">
                            <div class="p-1.5 sm:p-3 bg-amber-100 rounded-lg sm:mr-4 mb-1 sm:mb-0 inline-block">
                                <ClockIcon class="h-4 w-4 sm:h-6 sm:w-6 text-amber-600" />
                            </div>
                            <div class="text-center sm:text-left">
                                <p class="text-xs sm:text-sm font-medium text-gray-600">Due</p>
                                <p class="text-lg sm:text-2xl font-bold text-gray-900">{{ dueCount }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg sm:rounded-xl p-3 sm:p-6 shadow-sm border border-gray-200">
                        <div class="flex flex-col sm:flex-row sm:items-center">
                            <div class="p-1.5 sm:p-3 bg-blue-100 rounded-lg sm:mr-4 mb-1 sm:mb-0 inline-block">
                                <BookOpenIcon class="h-4 w-4 sm:h-6 sm:w-6 text-blue-600" />
                            </div>
                            <div class="text-center sm:text-left">
                                <p class="text-xs sm:text-sm font-medium text-gray-600">Courses</p>
                                <p class="text-lg sm:text-2xl font-bold text-gray-900">{{ courses.length }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Flashcard Sets Grid - Mobile Optimized -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-6">
                    <div
                        v-for="set in flashcard_sets.data"
                        :key="set.id"
                        class="bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200 overflow-hidden"
                    >
                        <div class="p-4 sm:p-6">
                            <div class="flex items-start justify-between mb-2 sm:mb-3">
                                <h3 class="text-base sm:text-lg font-semibold text-gray-900 truncate max-w-[70%] sm:max-w-[80%]" :title="set.title">
                                    {{ set.title }}
                                </h3>
                                <div class="flex space-x-1 flex-shrink-0">
                                    <button
                                        @click="deleteSet(set)"
                                        class="p-1.5 sm:p-1 text-gray-400 hover:text-red-500 transition-colors rounded-full hover:bg-red-50"
                                        :title="'Delete ' + set.title"
                                    >
                                        <TrashIcon class="h-3.5 w-3.5 sm:h-4 sm:w-4" />
                                    </button>
                                </div>
                            </div>

                            <p class="text-gray-600 text-xs sm:text-sm mb-3 sm:mb-4 line-clamp-2">{{ set.description || 'No description' }}</p>

                            <div class="flex flex-col xs:flex-row xs:items-center justify-between text-xs sm:text-sm text-gray-500 mb-3 sm:mb-4 gap-1 xs:gap-0">
                                <span class="flex items-center truncate max-w-[60%] xs:max-w-[55%]" :title="set.course.title">
                                    <AcademicCapIcon class="h-3.5 w-3.5 sm:h-4 sm:w-4 mr-1 flex-shrink-0" />
                                    <span class="truncate">{{ set.course.title }}</span>
                                </span>
                                <span class="flex items-center">
                                    <DocumentTextIcon class="h-3.5 w-3.5 sm:h-4 sm:w-4 mr-1 flex-shrink-0" />
                                    {{ set.flashcards_count }} cards
                                </span>
                            </div>

                            <!-- Due indicator for mobile -->
                            <div v-if="set.due_flashcards_count > 0" class="mb-2 sm:mb-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                    <ClockIcon class="h-3 w-3 mr-1" />
                                    {{ set.due_flashcards_count }} due
                                </span>
                            </div>

                            <div class="flex space-x-2">
                                <Link
                                    :href="route('student.flashcards.show', set.id)"
                                    class="flex-1 text-center px-2 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
                                >
                                    View
                                </Link>
                                <Link
                                    :href="route('student.flashcards.study', set.id)"
                                    class="flex-1 text-center px-2 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm font-medium text-white bg-gradient-to-r from-emerald-600 to-teal-600 rounded-lg hover:from-emerald-700 hover:to-teal-700 transition-colors"
                                >
                                    Study
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State - Mobile Optimized -->
                <div
                    v-if="flashcard_sets.data.length === 0"
                    class="text-center py-8 sm:py-12"
                >
                    <div class="mx-auto h-12 w-12 sm:h-16 sm:w-16 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full flex items-center justify-center mb-3 sm:mb-4">
                        <AcademicCapIcon class="h-6 w-6 sm:h-8 sm:w-8 text-white" />
                    </div>
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-1 sm:mb-2">No flashcard sets yet</h3>
                    <p class="text-sm sm:text-base text-gray-600 mb-4 sm:mb-6">Create your first flashcard set to start studying</p>
                    <Link
                        :href="route('student.flashcards.create')"
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-sm sm:text-base font-semibold rounded-lg hover:from-emerald-700 hover:to-teal-700 transition-all duration-200"
                    >
                        <PlusIcon class="h-4 w-4 sm:h-5 sm:w-5 mr-2" />
                        Create Flashcard Set
                    </Link>
                </div>

                <!-- Pagination - Mobile Optimized -->
                <div v-if="flashcard_sets.data.length > 0" class="mt-6 sm:mt-8">
                    <Pagination :links="flashcard_sets.links" />
                </div>

                <!-- Quick Study Reminder - Mobile Only -->
                <div v-if="dueCount > 0" class="mt-4 sm:hidden">
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-3">
                        <div class="flex items-center">
                            <ClockIcon class="h-5 w-5 text-amber-600 mr-2 flex-shrink-0" />
                            <p class="text-sm text-amber-800">
                                You have <span class="font-bold">{{ dueCount }}</span> cards due for review
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>

<script setup>
import { computed } from 'vue'
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
    courses: {
        type: Array,
        default: () => []
    },
    flashcard_sets: {
        type: Object,
        default: () => ({
            data: [],
            total: 0,
            links: []
        })
    },
})

const dueCount = computed(() => {
    if (!props.flashcard_sets?.data) return 0
    return props.flashcard_sets.data.reduce((total, set) => {
        return total + (set.due_flashcards_count || 0)
    }, 0)
})

const deleteSet = (set) => {
    if (confirm(`Are you sure you want to delete "${set.title}"?`)) {
        router.delete(route('student.flashcards.destroy', set.id), {
            preserveScroll: true,
            onSuccess: () => {
                // Optional: Show success notification
            }
        })
    }
}
</script>

<style scoped>
/* Custom line clamp utility */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Truncate text with ellipsis */
.truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Extra small devices breakpoint */
@media (min-width: 480px) {
    .xs\:inline {
        display: inline;
    }
    .xs\:hidden {
        display: none;
    }
    .xs\:flex-row {
        flex-direction: row;
    }
    .xs\:items-center {
        align-items: center;
    }
    .xs\:max-w-\[55\%\] {
        max-width: 55%;
    }
    .xs\:gap-0 {
        gap: 0;
    }
}

/* Touch-friendly tap targets */
button, a {
    min-height: 44px; /* iOS minimum touch target size */
    min-width: 44px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

/* Adjust for very small screens */
@media (max-width: 360px) {
    .text-xs {
        font-size: 0.7rem;
    }
    .p-3 {
        padding: 0.6rem;
    }
}
</style>
