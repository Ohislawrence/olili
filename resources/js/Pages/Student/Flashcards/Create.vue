<template>
    <StudentLayout>
        <Head title="Create Flashcard Set" />

        <div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 py-4 sm:py-8">
            <div class="max-w-2xl mx-auto px-3 sm:px-4 lg:px-8">
                <!-- Header - Mobile Optimized -->
                <div class="mb-6 sm:mb-8">
                    <!-- Mobile Header with Back Button -->
                    <div class="flex items-center justify-between sm:hidden mb-3">
                        <Link
                            :href="route('student.flashcards.index')"
                            class="inline-flex items-center text-emerald-600"
                        >
                            <ArrowLeftIcon class="h-5 w-5 mr-1" />
                            <span class="text-sm">Back</span>
                        </Link>
                        <span class="text-sm font-medium text-gray-600">Create New Set</span>
                        <div class="w-12"></div> <!-- Spacer for alignment -->
                    </div>

                    <!-- Desktop Header -->
                    <div class="hidden sm:flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Create Flashcard Set</h1>
                            <p class="text-sm sm:text-base text-gray-600 mt-1 sm:mt-2">Generate flashcards from your course topics using AI</p>
                        </div>
                        <Link
                            :href="route('student.flashcards.index')"
                            class="text-sm sm:text-base text-gray-600 hover:text-gray-900 transition-colors"
                        >
                            ← Back to Flashcards
                        </Link>
                    </div>
                </div>

                <!-- Form - Mobile Optimized -->
                <div class="bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
                    <form @submit.prevent="submitForm">
                        <!-- Course Selection -->
                        <div class="mb-4 sm:mb-6">
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                                Select Course <span class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="form.course_id"
                                @change="loadOutlines"
                                class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white"
                                :class="{ 'border-red-300': form.errors.course_id }"
                                required
                            >
                                <option value="">Choose a course</option>
                                <option
                                    v-for="course in courses"
                                    :key="course.id"
                                    :value="course.id"
                                >
                                    {{ course.title }}
                                </option>
                            </select>
                            <p v-if="form.errors.course_id" class="text-red-500 text-xs mt-1">
                                {{ form.errors.course_id }}
                            </p>
                        </div>

                        <!-- Topic Selection -->
                        <div class="mb-4 sm:mb-6">
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                                Select Topic
                                <span class="text-gray-400 text-xs ml-1">(Optional)</span>
                            </label>
                            <select
                                v-model="form.course_outline_id"
                                class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white disabled:bg-gray-100"
                                :disabled="!form.course_id || isLoadingOutlines"
                            >
                                <option value="">General Course Flashcards</option>
                                <option
                                    v-for="outline in availableOutlines"
                                    :key="outline.id"
                                    :value="outline.id"
                                >
                                    {{ outline.full_title }}
                                </option>
                            </select>

                            <!-- Loading State -->
                            <div v-if="isLoadingOutlines" class="flex items-center mt-2">
                                <div class="animate-spin rounded-full h-3 w-3 sm:h-4 sm:w-4 border-b-2 border-emerald-600 mr-2"></div>
                                <span class="text-xs sm:text-sm text-emerald-600">Loading topics...</span>
                            </div>

                            <!-- Help Text -->
                            <p class="text-xs text-gray-500 mt-1 sm:mt-2">
                                <LightBulbIcon class="h-3 w-3 inline-block mr-1 text-emerald-500" />
                                Selecting a specific topic generates more focused flashcards
                            </p>
                        </div>

                        <!-- Set Details -->
                        <div class="mb-4 sm:mb-6">
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                                Flashcard Set Title <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.title"
                                type="text"
                                class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                :class="{ 'border-red-300': form.errors.title }"
                                placeholder="e.g., Biology Basics Flashcards"
                                required
                            />
                            <p v-if="form.errors.title" class="text-red-500 text-xs mt-1">
                                {{ form.errors.title }}
                            </p>
                        </div>

                        <div class="mb-4 sm:mb-6">
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                                Description
                                <span class="text-gray-400 text-xs ml-1">(Optional)</span>
                            </label>
                            <textarea
                                v-model="form.description"
                                rows="3"
                                class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                placeholder="Describe what this flashcard set covers..."
                            ></textarea>
                        </div>

                        <!-- Generation Settings - Mobile Stacked -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mb-4 sm:mb-6">
                            <div>
                                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                                    Number of Cards <span class="text-red-500">*</span>
                                </label>
                                <select
                                    v-model="form.number_of_cards"
                                    class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white"
                                    required
                                >
                                    <option value="5">5 Cards</option>
                                    <option value="10">10 Cards</option>
                                    <option value="15">15 Cards</option>
                                    <option value="20">20 Cards</option>
                                    <option value="25">25 Cards</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                                    Difficulty Level <span class="text-red-500">*</span>
                                </label>
                                <div class="flex space-x-2 sm:block">
                                    <select
                                        v-model="form.difficulty_level"
                                        class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white"
                                        required
                                    >
                                        <option value="easy">Easy</option>
                                        <option value="medium">Medium</option>
                                        <option value="hard">Hard</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Options - Mobile Friendly -->
                        <div class="mb-6 sm:mb-8">
                            <label class="flex items-center p-3 sm:p-0 bg-gray-50 sm:bg-transparent rounded-lg border border-gray-200 sm:border-0">
                                <input
                                    v-model="form.include_explanations"
                                    type="checkbox"
                                    class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 h-4 w-4 sm:h-5 sm:w-5"
                                />
                                <span class="ml-3 text-sm text-gray-700">
                                    Include detailed explanations
                                </span>
                            </label>
                            <p class="text-xs text-gray-500 mt-1 ml-7 sm:ml-0">
                                Adds explanations to help understand the answers
                            </p>
                        </div>

                        <!-- AI Generation Info - Mobile Friendly -->
                        <div class="mb-6 sm:mb-8 p-3 sm:p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <div class="flex items-start">
                                <SparklesIcon class="h-4 w-4 sm:h-5 sm:w-5 text-blue-600 mr-2 sm:mr-3 flex-shrink-0 mt-0.5" />
                                <div>
                                    <h4 class="text-xs sm:text-sm font-semibold text-blue-900 mb-1">AI-Powered Generation</h4>
                                    <p class="text-xs text-blue-700 leading-relaxed">
                                        Our AI will analyze your course content and create high-quality flashcards tailored to your selected topic and difficulty level.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button - Mobile Optimized -->
                        <div class="flex flex-col-reverse sm:flex-row items-center justify-between gap-3 sm:gap-4">
                            <Link
                                :href="route('student.flashcards.index')"
                                class="w-full sm:w-auto text-center px-4 sm:px-5 py-2.5 sm:py-2 text-sm text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full sm:w-auto px-5 sm:px-6 py-3 sm:py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-sm font-semibold rounded-lg hover:from-emerald-700 hover:to-teal-700 disabled:opacity-50 transition-all duration-200 flex items-center justify-center"
                            >
                                <div v-if="form.processing" class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                                <span class="flex items-center">
                                    <SparklesIcon v-if="!form.processing" class="h-4 w-4 mr-2" />
                                    {{ form.processing ? 'Generating...' : 'Create Flashcard Set' }}
                                </span>
                            </button>
                        </div>

                        <!-- Processing Time Note -->
                        <p v-if="form.processing" class="text-xs text-center text-emerald-600 mt-4">
                            This may take a few moments. Please don't close the window.
                        </p>
                    </form>
                </div>

                <!-- Quick Tips - Mobile Friendly -->
                <div class="mt-4 sm:mt-6 p-3 sm:p-4 bg-emerald-50 rounded-lg border border-emerald-200">
                    <h4 class="text-xs sm:text-sm font-semibold text-emerald-900 mb-2 flex items-center">
                        <LightBulbIcon class="h-4 w-4 mr-1.5" />
                        Tips for better flashcards
                    </h4>
                    <ul class="space-y-1.5 text-xs text-emerald-700">
                        <li class="flex items-start">
                            <CheckIcon class="h-3.5 w-3.5 mr-1.5 mt-0.5 flex-shrink-0" />
                            <span>Select a specific topic for more focused content</span>
                        </li>
                        <li class="flex items-start">
                            <CheckIcon class="h-3.5 w-3.5 mr-1.5 mt-0.5 flex-shrink-0" />
                            <span>Choose "Hard" difficulty for more challenging questions</span>
                        </li>
                        <li class="flex items-start">
                            <CheckIcon class="h-3.5 w-3.5 mr-1.5 mt-0.5 flex-shrink-0" />
                            <span>Enable explanations to better understand complex topics</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import StudentLayout from '@/Layouts/StudentLayout.vue'
import {
    ArrowLeftIcon,
    LightBulbIcon,
    SparklesIcon,
    CheckIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
    courses: {
        type: Array,
        default: () => []
    },
    selected_course: {
        type: Object,
        default: null
    },
    selected_outline: {
        type: Object,
        default: null
    },
    available_outlines: {
        type: Array,
        default: () => []
    },
})

const isLoadingOutlines = ref(false)
const availableOutlines = ref([...props.available_outlines])

const form = useForm({
    course_id: props.selected_course?.id || '',
    course_outline_id: props.selected_outline?.id || '',
    title: '',
    description: '',
    number_of_cards: 10,
    difficulty_level: 'medium',
    include_explanations: false,
})

const loadOutlines = async () => {
    if (!form.course_id) {
        availableOutlines.value = []
        return
    }

    isLoadingOutlines.value = true
    try {
        const response = await axios.get(route('student.courses.outlines', form.course_id))
        availableOutlines.value = response.data.outlines || []
    } catch (error) {
        console.error('Failed to load outlines:', error)
        availableOutlines.value = []
    } finally {
        isLoadingOutlines.value = false
    }
}

const submitForm = () => {
    form.post(route('student.flashcards.store'))
}

// Load outlines if course is pre-selected
if (form.course_id) {
    loadOutlines()
}

// Watch for course_id changes
watch(() => form.course_id, (newCourseId) => {
    if (newCourseId) {
        loadOutlines()
    } else {
        availableOutlines.value = []
        form.course_outline_id = ''
    }
})
</script>

<style scoped>
/* Touch-friendly tap targets */
@media (max-width: 640px) {
    button,
    a,
    select,
    input,
    textarea,
    [role="button"] {
        min-height: 44px;
    }

    /* Better touch targets for checkboxes */
    label.flex.items-center {
        min-height: 44px;
        cursor: pointer;
    }
}

/* Extra small devices */
@media (min-width: 480px) {
    .xs\:inline {
        display: inline;
    }
}

/* Improve form field focus states for mobile */
@media (max-width: 640px) {
    input:focus,
    select:focus,
    textarea:focus {
        font-size: 16px; /* Prevents zoom on iOS */
    }
}

/* Smooth transitions */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
}

/* Better checkbox touch area */
input[type="checkbox"] {
    cursor: pointer;
}
</style>
