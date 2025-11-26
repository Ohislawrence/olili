<template>
    <StudentLayout>
        <Head title="Create Flashcard Set" />

        <div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 py-8">
            <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Create Flashcard Set</h1>
                            <p class="text-gray-600 mt-2">Generate flashcards from your course topics using AI</p>
                        </div>
                        <Link
                            :href="route('student.flashcards.index')"
                            class="text-gray-600 hover:text-gray-900 transition-colors"
                        >
                            ← Back to Flashcards
                        </Link>
                    </div>
                </div>

                <!-- Form -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <form @submit.prevent="submitForm">
                        <!-- Course Selection -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Select Course *
                            </label>
                            <select
                                v-model="form.course_id"
                                @change="loadOutlines"
                                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
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
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Select Topic (Optional)
                            </label>
                            <select
                                v-model="form.course_outline_id"
                                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
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
                            <div v-if="isLoadingOutlines" class="text-xs text-emerald-600 mt-2 flex items-center">
                                <div class="animate-spin rounded-full h-3 w-3 border-b-2 border-emerald-600 mr-2"></div>
                                Loading topics...
                            </div>
                            <p class="text-xs text-gray-500 mt-2">
                                Selecting a specific topic will generate more focused flashcards
                            </p>
                        </div>

                        <!-- Set Details -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Flashcard Set Title *
                            </label>
                            <input
                                v-model="form.title"
                                type="text"
                                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                :class="{ 'border-red-300': form.errors.title }"
                                placeholder="e.g., Biology Basics Flashcards"
                                required
                            />
                            <p v-if="form.errors.title" class="text-red-500 text-xs mt-1">
                                {{ form.errors.title }}
                            </p>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Description (Optional)
                            </label>
                            <textarea
                                v-model="form.description"
                                rows="3"
                                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                placeholder="Describe what this flashcard set covers..."
                            ></textarea>
                        </div>

                        <!-- Generation Settings -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Number of Cards *
                                </label>
                                <select
                                    v-model="form.number_of_cards"
                                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
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
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Difficulty Level *
                                </label>
                                <select
                                    v-model="form.difficulty_level"
                                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                    required
                                >
                                    <option value="easy">Easy</option>
                                    <option value="medium">Medium</option>
                                    <option value="hard">Hard</option>
                                </select>
                            </div>
                        </div>

                        <!-- Options -->
                        <div class="mb-8">
                            <label class="flex items-center">
                                <input
                                    v-model="form.include_explanations"
                                    type="checkbox"
                                    class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                                />
                                <span class="ml-2 text-sm text-gray-700">
                                    Include detailed explanations
                                </span>
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-between">
                            <Link
                                :href="route('student.flashcards.index')"
                                class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-6 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-semibold rounded-lg hover:from-emerald-700 hover:to-teal-700 disabled:opacity-50 transition-all duration-200 flex items-center"
                            >
                                <div v-if="form.processing" class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                                {{ form.processing ? 'Generating Flashcards...' : 'Create Flashcard Set' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref, reactive, watch } from 'vue'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const props = defineProps({
    courses: Array,
    selected_course: Object,
    selected_outline: Object,
    available_outlines: Array,
})

const isLoadingOutlines = ref(false)
// Create a reactive copy of the available_outlines prop
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
        // Update the reactive variable instead of the prop
        availableOutlines.value = response.data.outlines
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

// Watch for course_id changes and load outlines
watch(() => form.course_id, (newCourseId) => {
    if (newCourseId) {
        loadOutlines()
    } else {
        availableOutlines.value = []
        form.course_outline_id = ''
    }
})
</script>
