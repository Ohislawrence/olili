<template>
    <StudentLayout>
        <Head :title="`Flashcards - ${flashcard_set.title}`" />

        <div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 py-8">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="flex items-center space-x-2 text-sm text-gray-600 mb-2">
                                <Link :href="route('student.flashcards.index')" class="hover:text-emerald-600 transition-colors">
                                    Flashcards
                                </Link>
                                <span>/</span>
                                <span class="text-gray-900 font-medium">{{ flashcard_set.title }}</span>
                            </div>
                            <h1 class="text-3xl font-bold text-gray-900">{{ flashcard_set.title }}</h1>
                            <p class="text-gray-600 mt-2">{{ flashcard_set.description || 'No description' }}</p>
                            <div class="flex items-center space-x-4 mt-3 text-sm text-gray-500">
                                <span class="flex items-center">
                                    <AcademicCapIcon class="h-4 w-4 mr-1" />
                                    {{ flashcard_set.course.title }}
                                </span>
                                <span class="flex items-center">
                                    <DocumentTextIcon class="h-4 w-4 mr-1" />
                                    {{ flashcard_set.flashcards.length }} cards
                                </span>
                                <span class="flex items-center">
                                    <CalendarIcon class="h-4 w-4 mr-1" />
                                    Created {{ formatDate(flashcard_set.created_at) }}
                                </span>
                            </div>
                        </div>
                        <div class="flex space-x-3">
                            <Link
                                :href="route('student.flashcards.study', flashcard_set.id)"
                                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-semibold rounded-lg hover:from-emerald-700 hover:to-teal-700 transition-all duration-200 shadow-sm hover:shadow-md"
                            >
                                <PlayIcon class="h-4 w-4 mr-2" />
                                Study Now
                            </Link>
                            <button
                                @click="confirmDelete"
                                class="inline-flex items-center px-4 py-2 border border-red-300 text-red-700 font-medium rounded-lg hover:bg-red-50 transition-colors"
                            >
                                <TrashIcon class="h-4 w-4 mr-2" />
                                Delete
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                        <div class="flex items-center">
                            <div class="p-3 bg-emerald-100 rounded-lg mr-4">
                                <DocumentTextIcon class="h-6 w-6 text-emerald-600" />
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Cards</p>
                                <p class="text-2xl font-bold text-gray-900">{{ flashcard_set.flashcards.length }}</p>
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
                                <StarIcon class="h-6 w-6 text-blue-600" />
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-600">Mastered</p>
                                <p class="text-2xl font-bold text-gray-900">{{ masteredCount }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                        <div class="flex items-center">
                            <div class="p-3 bg-purple-100 rounded-lg mr-4">
                                <ChartBarIcon class="h-6 w-6 text-purple-600" />
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-600">Mastery Rate</p>
                                <p class="text-2xl font-bold text-gray-900">{{ masteryRate }}%</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Difficulty Distribution -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Difficulty Distribution</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="flex items-center justify-between p-4 bg-emerald-50 rounded-lg border border-emerald-200">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-emerald-500 rounded-full mr-3"></div>
                                <span class="text-sm font-medium text-gray-700">Easy</span>
                            </div>
                            <span class="text-lg font-bold text-emerald-700">{{ easyCount }}</span>
                        </div>
                        <div class="flex items-center justify-between p-4 bg-amber-50 rounded-lg border border-amber-200">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-amber-500 rounded-full mr-3"></div>
                                <span class="text-sm font-medium text-gray-700">Medium</span>
                            </div>
                            <span class="text-lg font-bold text-amber-700">{{ mediumCount }}</span>
                        </div>
                        <div class="flex items-center justify-between p-4 bg-rose-50 rounded-lg border border-rose-200">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-rose-500 rounded-full mr-3"></div>
                                <span class="text-sm font-medium text-gray-700">Hard</span>
                            </div>
                            <span class="text-lg font-bold text-rose-700">{{ hardCount }}</span>
                        </div>
                    </div>
                </div>

                <!-- Actions Bar -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex space-x-3">
                        <button
                            @click="showAllCards = !showAllCards"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors"
                        >
                            <EyeIcon class="h-4 w-4 mr-2" />
                            {{ showAllCards ? 'Show Due Cards Only' : 'Show All Cards' }}
                        </button>
                        <button
                            @click="resetProgress"
                            class="inline-flex items-center px-4 py-2 border border-amber-300 rounded-lg text-sm font-medium text-amber-700 bg-white hover:bg-amber-50 transition-colors"
                        >
                            <ArrowPathIcon class="h-4 w-4 mr-2" />
                            Reset Progress
                        </button>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="relative">
                            <input
                                v-model="searchTerm"
                                type="text"
                                placeholder="Search cards..."
                                class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 w-64"
                            />
                            <MagnifyingGlassIcon class="h-4 w-4 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" />
                        </div>
                        <select
                            v-model="difficultyFilter"
                            class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        >
                            <option value="all">All Difficulties</option>
                            <option value="easy">Easy</option>
                            <option value="medium">Medium</option>
                            <option value="hard">Hard</option>
                        </select>
                    </div>
                </div>

                <!-- Flashcards Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="flashcard in filteredFlashcards"
                        :key="flashcard.id"
                        class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200 overflow-hidden"
                        :class="{
                            'border-emerald-200': flashcard.difficulty_level === 'easy',
                            'border-amber-200': flashcard.difficulty_level === 'medium',
                            'border-rose-200': flashcard.difficulty_level === 'hard',
                        }"
                    >
                        <div class="p-6">
                            <!-- Card Header -->
                            <div class="flex items-start justify-between mb-4">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                    :class="{
                                        'bg-emerald-100 text-emerald-800': flashcard.difficulty_level === 'easy',
                                        'bg-amber-100 text-amber-800': flashcard.difficulty_level === 'medium',
                                        'bg-rose-100 text-rose-800': flashcard.difficulty_level === 'hard',
                                    }"
                                >
                                    {{ flashcard.difficulty_level }}
                                </span>
                                <div class="flex items-center space-x-1 text-sm text-gray-500">
                                    <span v-if="flashcard.repetitions > 0" class="flex items-center">
                                        <ArrowPathIcon class="h-3 w-3 mr-1" />
                                        {{ flashcard.repetitions }}
                                    </span>
                                    <span v-if="flashcard.next_review_date" class="flex items-center">
                                        <CalendarIcon class="h-3 w-3 mr-1" />
                                        {{ formatReviewDate(flashcard.next_review_date) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Question -->
                            <div class="mb-4">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Question</h3>
                                <p class="text-gray-700">{{ flashcard.question }}</p>
                            </div>

                            <!-- Answer (Collapsible) -->
                            <div class="mb-4">
                                <button
                                    @click="toggleAnswer(flashcard.id)"
                                    class="flex items-center justify-between w-full text-left"
                                >
                                    <h3 class="text-lg font-semibold text-gray-900">Answer</h3>
                                    <ChevronDownIcon
                                        class="h-4 w-4 text-gray-500 transition-transform duration-200"
                                        :class="{ 'transform rotate-180': showAnswers[flashcard.id] }"
                                    />
                                </button>
                                <div
                                    v-if="showAnswers[flashcard.id]"
                                    class="mt-2 p-3 bg-gray-50 rounded-lg border border-gray-200"
                                >
                                    <p class="text-gray-700">{{ flashcard.answer }}</p>
                                    <div
                                        v-if="flashcard.explanation"
                                        class="mt-3 pt-3 border-t border-gray-200"
                                    >
                                        <h4 class="text-sm font-medium text-gray-900 mb-1">Explanation</h4>
                                        <p class="text-sm text-gray-600">{{ flashcard.explanation }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Progress Info -->
                            <div class="flex items-center justify-between text-xs text-gray-500 pt-3 border-t border-gray-200">
                                <span>Interval: {{ flashcard.interval }} days</span>
                                <span>Ease: {{ flashcard.ease_factor }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div
                    v-if="filteredFlashcards.length === 0"
                    class="text-center py-12 bg-white rounded-xl shadow-sm border border-gray-200"
                >
                    <div class="mx-auto h-16 w-16 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full flex items-center justify-center mb-4">
                        <DocumentTextIcon class="h-8 w-8 text-white" />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No flashcards found</h3>
                    <p class="text-gray-600 mb-6">Try adjusting your search or filter criteria</p>
                </div>

                <!-- Quick Study Actions -->
                <div class="mt-8 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-xl p-6 border border-emerald-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Ready to study?</h3>
                            <p class="text-gray-600">Start a study session to review your flashcards</p>
                        </div>
                        <div class="flex space-x-3">
                            <Link
    :href="route('student.flashcards.study', { flashcardSet: flashcard_set.id, mode: 'due' })"
    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-semibold rounded-lg hover:from-emerald-700 hover:to-teal-700 transition-all duration-200 shadow-sm hover:shadow-md"
>
    <PlayIcon class="h-5 w-5 mr-2" />
    Study Due Cards ({{ dueCount }})
</Link>
<Link
    :href="route('student.flashcards.study', { flashcardSet: flashcard_set.id, mode: 'all' })"
    class="inline-flex items-center px-6 py-3 border border-emerald-300 text-emerald-700 font-semibold rounded-lg hover:bg-emerald-50 transition-colors"
>
    <AcademicCapIcon class="h-5 w-5 mr-2" />
    Study All Cards
</Link>
                        </div>
                    </div>
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
    AcademicCapIcon,
    DocumentTextIcon,
    CalendarIcon,
    PlayIcon,
    TrashIcon,
    ClockIcon,
    StarIcon,
    ChartBarIcon,
    EyeIcon,
    ArrowPathIcon,
    MagnifyingGlassIcon,
    ChevronDownIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
    flashcard_set: Object,
})

// Reactive state
const showAllCards = ref(true)
const searchTerm = ref('')
const difficultyFilter = ref('all')
const showAnswers = reactive({})

// Computed properties
const dueCount = computed(() => {
    return props.flashcard_set.flashcards.filter(card =>
        !card.next_review_date || new Date(card.next_review_date) <= new Date()
    ).length
})

const masteredCount = computed(() => {
    return props.flashcard_set.flashcards.filter(card =>
        card.repetitions >= 5 && card.ease_factor >= 2.0
    ).length
})

const masteryRate = computed(() => {
    return props.flashcard_set.flashcards.length > 0
        ? Math.round((masteredCount.value / props.flashcard_set.flashcards.length) * 100)
        : 0
})

const easyCount = computed(() => {
    return props.flashcard_set.flashcards.filter(card => card.difficulty_level === 'easy').length
})

const mediumCount = computed(() => {
    return props.flashcard_set.flashcards.filter(card => card.difficulty_level === 'medium').length
})

const hardCount = computed(() => {
    return props.flashcard_set.flashcards.filter(card => card.difficulty_level === 'hard').length
})

const filteredFlashcards = computed(() => {
    let flashcards = props.flashcard_set.flashcards

    // Filter by due status
    if (!showAllCards.value) {
        flashcards = flashcards.filter(card =>
            !card.next_review_date || new Date(card.next_review_date) <= new Date()
        )
    }

    // Filter by search term
    if (searchTerm.value) {
        const term = searchTerm.value.toLowerCase()
        flashcards = flashcards.filter(card =>
            card.question.toLowerCase().includes(term) ||
            card.answer.toLowerCase().includes(term) ||
            (card.explanation && card.explanation.toLowerCase().includes(term))
        )
    }

    // Filter by difficulty
    if (difficultyFilter.value !== 'all') {
        flashcards = flashcards.filter(card => card.difficulty_level === difficultyFilter.value)
    }

    return flashcards
})

// Methods
const formatDate = (dateString) => {
    const date = new Date(dateString)
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const formatReviewDate = (dateString) => {
    const date = new Date(dateString)
    const now = new Date()
    const diffTime = Math.abs(date - now)
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))

    if (diffDays === 0) return 'Today'
    if (diffDays === 1) return 'Tomorrow'
    if (diffDays <= 7) return `In ${diffDays} days`

    return date.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric'
    })
}

const toggleAnswer = (flashcardId) => {
    showAnswers[flashcardId] = !showAnswers[flashcardId]
}

const confirmDelete = () => {
    if (confirm(`Are you sure you want to delete "${props.flashcard_set.title}"? This action cannot be undone.`)) {
        router.delete(route('student.flashcards.destroy', props.flashcard_set.id))
    }
}

const resetProgress = () => {
    if (confirm('Are you sure you want to reset all progress for this flashcard set? This will reset all learning intervals and ease factors.')) {
        router.post(route('student.flashcards.reset-progress', props.flashcard_set.id))
    }
}
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
