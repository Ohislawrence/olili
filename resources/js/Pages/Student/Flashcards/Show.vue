<template>
    <StudentLayout>
        <Head :title="`Flashcards - ${flashcard_set.title}`" />

        <div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 py-4 sm:py-8">
            <div class="max-w-6xl mx-auto px-3 sm:px-4 lg:px-8">
                <!-- Header - Mobile Optimized -->
                <div class="mb-6 sm:mb-8">
                    <!-- Mobile Breadcrumb & Back -->
                    <div class="flex items-center justify-between sm:hidden mb-3">
                        <Link
                            :href="route('student.flashcards.index')"
                            class="inline-flex items-center text-emerald-600"
                        >
                            <ArrowLeftIcon class="h-5 w-5 mr-1" />
                            <span class="text-sm">Back</span>
                        </Link>
                        <span class="text-sm font-medium text-gray-600">Flashcard Set</span>
                        <div class="w-16"></div> <!-- Spacer for alignment -->
                    </div>

                    <!-- Desktop Breadcrumb -->
                    <div class="hidden sm:flex items-center space-x-2 text-sm text-gray-600 mb-2">
                        <Link :href="route('student.flashcards.index')" class="hover:text-emerald-600 transition-colors">
                            Flashcards
                        </Link>
                        <span>/</span>
                        <span class="text-gray-900 font-medium">{{ flashcard_set.title }}</span>
                    </div>

                    <!-- Title and Actions -->
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ flashcard_set.title }}</h1>
                            <p class="text-sm sm:text-base text-gray-600 mt-1 sm:mt-2">{{ flashcard_set.description || 'No description' }}</p>

                            <!-- Mobile Meta Info -->
                            <div class="flex flex-wrap items-center gap-3 mt-2 text-xs sm:hidden">
                                <span class="flex items-center bg-gray-100 px-2 py-1 rounded-full">
                                    <AcademicCapIcon class="h-3 w-3 mr-1" />
                                    {{ flashcard_set.course.title }}
                                </span>
                                <span class="flex items-center bg-gray-100 px-2 py-1 rounded-full">
                                    <DocumentTextIcon class="h-3 w-3 mr-1" />
                                    {{ flashcard_set.flashcards.length }} cards
                                </span>
                                <span class="flex items-center bg-gray-100 px-2 py-1 rounded-full">
                                    <CalendarIcon class="h-3 w-3 mr-1" />
                                    {{ formatShortDate(flashcard_set.created_at) }}
                                </span>
                            </div>

                            <!-- Desktop Meta Info -->
                            <div class="hidden sm:flex items-center space-x-4 mt-3 text-sm text-gray-500">
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

                        <!-- Mobile Action Buttons -->
                        <div class="flex items-center space-x-2 mt-3 sm:hidden">
                            <Link
                                :href="route('student.flashcards.study', flashcard_set.id)"
                                class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-sm font-semibold rounded-lg"
                            >
                                <PlayIcon class="h-4 w-4 mr-1" />
                                Study
                            </Link>
                            <button
                                @click="confirmDelete"
                                class="flex-1 inline-flex items-center justify-center px-3 py-2 border border-red-300 text-red-700 text-sm font-medium rounded-lg"
                            >
                                <TrashIcon class="h-4 w-4 mr-1" />
                                Delete
                            </button>
                        </div>

                        <!-- Desktop Action Buttons -->
                        <div class="hidden sm:flex space-x-3">
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

                <!-- Stats Cards - Mobile Optimized -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-6 mb-6 sm:mb-8">
                    <div class="bg-white rounded-lg sm:rounded-xl p-3 sm:p-6 shadow-sm border border-gray-200">
                        <div class="flex flex-col sm:flex-row sm:items-center">
                            <div class="p-1.5 sm:p-3 bg-emerald-100 rounded-lg sm:mr-4 mb-1 sm:mb-0 inline-block">
                                <DocumentTextIcon class="h-4 w-4 sm:h-6 sm:w-6 text-emerald-600" />
                            </div>
                            <div class="text-center sm:text-left">
                                <p class="text-xs sm:text-sm font-medium text-gray-600">Total</p>
                                <p class="text-lg sm:text-2xl font-bold text-gray-900">{{ flashcard_set.flashcards.length }}</p>
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
                                <StarIcon class="h-4 w-4 sm:h-6 sm:w-6 text-blue-600" />
                            </div>
                            <div class="text-center sm:text-left">
                                <p class="text-xs sm:text-sm font-medium text-gray-600">Mastered</p>
                                <p class="text-lg sm:text-2xl font-bold text-gray-900">{{ masteredCount }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg sm:rounded-xl p-3 sm:p-6 shadow-sm border border-gray-200">
                        <div class="flex flex-col sm:flex-row sm:items-center">
                            <div class="p-1.5 sm:p-3 bg-purple-100 rounded-lg sm:mr-4 mb-1 sm:mb-0 inline-block">
                                <ChartBarIcon class="h-4 w-4 sm:h-6 sm:w-6 text-purple-600" />
                            </div>
                            <div class="text-center sm:text-left">
                                <p class="text-xs sm:text-sm font-medium text-gray-600">Mastery</p>
                                <p class="text-lg sm:text-2xl font-bold text-gray-900">{{ masteryRate }}%</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Difficulty Distribution - Mobile Optimized -->
                <div class="bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 mb-6 sm:mb-8">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-3 sm:mb-4">Difficulty Distribution</h3>
                    <div class="grid grid-cols-3 gap-2 sm:gap-4">
                        <div class="flex flex-col items-center p-2 sm:p-4 bg-emerald-50 rounded-lg border border-emerald-200">
                            <div class="flex items-center mb-1">
                                <div class="w-2 h-2 sm:w-3 sm:h-3 bg-emerald-500 rounded-full mr-1 sm:mr-2"></div>
                                <span class="text-xs sm:text-sm font-medium text-gray-700">Easy</span>
                            </div>
                            <span class="text-base sm:text-lg font-bold text-emerald-700">{{ easyCount }}</span>
                        </div>
                        <div class="flex flex-col items-center p-2 sm:p-4 bg-amber-50 rounded-lg border border-amber-200">
                            <div class="flex items-center mb-1">
                                <div class="w-2 h-2 sm:w-3 sm:h-3 bg-amber-500 rounded-full mr-1 sm:mr-2"></div>
                                <span class="text-xs sm:text-sm font-medium text-gray-700">Medium</span>
                            </div>
                            <span class="text-base sm:text-lg font-bold text-amber-700">{{ mediumCount }}</span>
                        </div>
                        <div class="flex flex-col items-center p-2 sm:p-4 bg-rose-50 rounded-lg border border-rose-200">
                            <div class="flex items-center mb-1">
                                <div class="w-2 h-2 sm:w-3 sm:h-3 bg-rose-500 rounded-full mr-1 sm:mr-2"></div>
                                <span class="text-xs sm:text-sm font-medium text-gray-700">Hard</span>
                            </div>
                            <span class="text-base sm:text-lg font-bold text-rose-700">{{ hardCount }}</span>
                        </div>
                    </div>
                </div>

                <!-- Actions Bar - Mobile Optimized -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
                    <div class="flex space-x-2">
                        <button
                            @click="showAllCards = !showAllCards"
                            class="flex-1 sm:flex-none inline-flex items-center justify-center px-3 sm:px-4 py-2 border border-gray-300 rounded-lg text-xs sm:text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors"
                        >
                            <EyeIcon class="h-3.5 w-3.5 sm:h-4 sm:w-4 mr-1 sm:mr-2" />
                            <span class="hidden xs:inline">{{ showAllCards ? 'Show Due Only' : 'Show All' }}</span>
                            <span class="xs:hidden">{{ showAllCards ? 'Due Only' : 'All' }}</span>
                        </button>
                        <button
                            @click="resetProgress"
                            class="flex-1 sm:flex-none inline-flex items-center justify-center px-3 sm:px-4 py-2 border border-amber-300 rounded-lg text-xs sm:text-sm font-medium text-amber-700 bg-white hover:bg-amber-50 transition-colors"
                        >
                            <ArrowPathIcon class="h-3.5 w-3.5 sm:h-4 sm:w-4 mr-1 sm:mr-2" />
                            <span class="hidden xs:inline">Reset</span>
                            <span class="xs:hidden">Reset</span>
                        </button>
                    </div>

                    <div class="flex flex-col xs:flex-row gap-2">
                        <div class="relative flex-1">
                            <input
                                v-model="searchTerm"
                                type="text"
                                placeholder="Search cards..."
                                class="w-full pl-8 sm:pl-10 pr-3 sm:pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                            />
                            <MagnifyingGlassIcon class="h-3.5 w-3.5 sm:h-4 sm:w-4 text-gray-400 absolute left-2.5 sm:left-3 top-1/2 transform -translate-y-1/2" />
                        </div>
                        <select
                            v-model="difficultyFilter"
                            class="px-2 sm:px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white"
                        >
                            <option value="all">All</option>
                            <option value="easy">Easy</option>
                            <option value="medium">Medium</option>
                            <option value="hard">Hard</option>
                        </select>
                    </div>
                </div>

                <!-- Due Count Badge - Mobile -->
                <div v-if="!showAllCards && dueCount > 0" class="mb-3 sm:hidden">
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-2 text-center">
                        <p class="text-xs text-amber-800">
                            Showing <span class="font-bold">{{ filteredFlashcards.length }}</span> due cards
                        </p>
                    </div>
                </div>

                <!-- Flashcards Grid - Mobile Optimized -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-6">
                    <div
                        v-for="flashcard in filteredFlashcards"
                        :key="flashcard.id"
                        class="bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200 overflow-hidden"
                        :class="{
                            'border-l-4 sm:border-l-4 border-emerald-500': flashcard.difficulty_level === 'easy',
                            'border-l-4 sm:border-l-4 border-amber-500': flashcard.difficulty_level === 'medium',
                            'border-l-4 sm:border-l-4 border-rose-500': flashcard.difficulty_level === 'hard',
                        }"
                    >
                        <div class="p-4 sm:p-6">
                            <!-- Card Header -->
                            <div class="flex items-start justify-between mb-3">
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                    :class="{
                                        'bg-emerald-100 text-emerald-800': flashcard.difficulty_level === 'easy',
                                        'bg-amber-100 text-amber-800': flashcard.difficulty_level === 'medium',
                                        'bg-rose-100 text-rose-800': flashcard.difficulty_level === 'hard',
                                    }"
                                >
                                    {{ flashcard.difficulty_level }}
                                </span>
                                <div class="flex items-center space-x-2 text-xs text-gray-500">
                                    <span v-if="flashcard.repetitions > 0" class="flex items-center bg-gray-100 px-1.5 py-0.5 rounded-full">
                                        <ArrowPathIcon class="h-3 w-3 mr-1" />
                                        {{ flashcard.repetitions }}
                                    </span>
                                    <span v-if="flashcard.next_review_date" class="flex items-center bg-gray-100 px-1.5 py-0.5 rounded-full whitespace-nowrap">
                                        <CalendarIcon class="h-3 w-3 mr-1" />
                                        {{ formatShortReviewDate(flashcard.next_review_date) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Question -->
                            <div class="mb-3">
                                <h3 class="text-sm font-semibold text-gray-900 mb-1">Question</h3>
                                <p class="text-sm text-gray-700 line-clamp-3">{{ flashcard.question }}</p>
                            </div>

                            <!-- Answer (Collapsible) -->
                            <div class="mb-3">
                                <button
                                    @click="toggleAnswer(flashcard.id)"
                                    class="flex items-center justify-between w-full text-left"
                                >
                                    <h3 class="text-sm font-semibold text-gray-900">Answer</h3>
                                    <ChevronDownIcon
                                        class="h-4 w-4 text-gray-500 transition-transform duration-200"
                                        :class="{ 'transform rotate-180': showAnswers[flashcard.id] }"
                                    />
                                </button>
                                <div
                                    v-if="showAnswers[flashcard.id]"
                                    class="mt-2 p-3 bg-gray-50 rounded-lg border border-gray-200"
                                >
                                    <p class="text-sm text-gray-700">{{ flashcard.answer }}</p>
                                    <div
                                        v-if="flashcard.explanation"
                                        class="mt-2 pt-2 border-t border-gray-200"
                                    >
                                        <h4 class="text-xs font-medium text-gray-900 mb-1">Explanation</h4>
                                        <p class="text-xs text-gray-600">{{ flashcard.explanation }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Progress Info -->
                            <div class="flex items-center justify-between text-xs text-gray-500 pt-2 border-t border-gray-200">
                                <span>Interval: {{ flashcard.interval || 0 }}d</span>
                                <span>Ease: {{ flashcard.ease_factor || 2.5 }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State - Mobile Optimized -->
                <div
                    v-if="filteredFlashcards.length === 0"
                    class="text-center py-8 sm:py-12 bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-200"
                >
                    <div class="mx-auto h-12 w-12 sm:h-16 sm:w-16 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full flex items-center justify-center mb-3 sm:mb-4">
                        <DocumentTextIcon class="h-6 w-6 sm:h-8 sm:w-8 text-white" />
                    </div>
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-1 sm:mb-2">No flashcards found</h3>
                    <p class="text-sm sm:text-base text-gray-600 mb-4 sm:mb-6 px-4">Try adjusting your search or filter criteria</p>

                    <!-- Quick action buttons on mobile -->
                    <div class="flex flex-col xs:flex-row items-center justify-center gap-2 px-4">
                        <button
                            @click="resetFilters"
                            class="w-full xs:w-auto inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg"
                        >
                            Clear Filters
                        </button>
                        <Link
                            :href="route('student.flashcards.study', flashcard_set.id)"
                            class="w-full xs:w-auto inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-sm font-semibold rounded-lg"
                        >
                            <PlayIcon class="h-4 w-4 mr-2" />
                            Study All Cards
                        </Link>
                    </div>
                </div>

                <!-- Quick Study Actions - Mobile Optimized -->
                <div class="mt-6 sm:mt-8 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-lg sm:rounded-xl p-4 sm:p-6 border border-emerald-200">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="mb-3 sm:mb-0">
                            <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-1">Ready to study?</h3>
                            <p class="text-xs sm:text-sm text-gray-600">Start a study session to review your flashcards</p>
                        </div>
                        <div class="flex flex-col xs:flex-row gap-2">
                            <Link
                                :href="route('student.flashcards.study', { flashcardSet: flashcard_set.id, mode: 'due' })"
                                class="inline-flex items-center justify-center px-4 sm:px-6 py-2.5 sm:py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-sm font-semibold rounded-lg hover:from-emerald-700 hover:to-teal-700 transition-all duration-200 shadow-sm"
                            >
                                <PlayIcon class="h-4 w-4 mr-2" />
                                <span class="hidden xs:inline">Study Due Cards</span>
                                <span class="xs:hidden">Due ({{ dueCount }})</span>
                            </Link>
                            <Link
                                :href="route('student.flashcards.study', { flashcardSet: flashcard_set.id, mode: 'all' })"
                                class="inline-flex items-center justify-center px-4 sm:px-6 py-2.5 sm:py-3 border border-emerald-300 text-emerald-700 text-sm font-semibold rounded-lg hover:bg-emerald-50 transition-colors bg-white"
                            >
                                <AcademicCapIcon class="h-4 w-4 mr-2" />
                                <span class="hidden xs:inline">Study All Cards</span>
                                <span class="xs:hidden">All</span>
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
    ArrowLeftIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
    flashcard_set: {
        type: Object,
        required: true
    },
})

// Reactive state
const showAllCards = ref(true)
const searchTerm = ref('')
const difficultyFilter = ref('all')
const showAnswers = reactive({})

// Computed properties
const dueCount = computed(() => {
    if (!props.flashcard_set?.flashcards) return 0
    return props.flashcard_set.flashcards.filter(card =>
        !card.next_review_date || new Date(card.next_review_date) <= new Date()
    ).length
})

const masteredCount = computed(() => {
    if (!props.flashcard_set?.flashcards) return 0
    return props.flashcard_set.flashcards.filter(card =>
        card.repetitions >= 5 && card.ease_factor >= 2.0
    ).length
})

const masteryRate = computed(() => {
    if (!props.flashcard_set?.flashcards?.length) return 0
    return Math.round((masteredCount.value / props.flashcard_set.flashcards.length) * 100)
})

const easyCount = computed(() => {
    if (!props.flashcard_set?.flashcards) return 0
    return props.flashcard_set.flashcards.filter(card => card.difficulty_level === 'easy').length
})

const mediumCount = computed(() => {
    if (!props.flashcard_set?.flashcards) return 0
    return props.flashcard_set.flashcards.filter(card => card.difficulty_level === 'medium').length
})

const hardCount = computed(() => {
    if (!props.flashcard_set?.flashcards) return 0
    return props.flashcard_set.flashcards.filter(card => card.difficulty_level === 'hard').length
})

const filteredFlashcards = computed(() => {
    if (!props.flashcard_set?.flashcards) return []

    let flashcards = [...props.flashcard_set.flashcards]

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
            card.question?.toLowerCase().includes(term) ||
            card.answer?.toLowerCase().includes(term) ||
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
    if (!dateString) return 'N/A'
    const date = new Date(dateString)
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const formatShortDate = (dateString) => {
    if (!dateString) return 'N/A'
    const date = new Date(dateString)
    return date.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric'
    })
}

const formatReviewDate = (dateString) => {
    if (!dateString) return 'N/A'
    const date = new Date(dateString)
    const now = new Date()
    const diffTime = date - now
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))

    if (diffDays <= 0) return 'Today'
    if (diffDays === 1) return 'Tomorrow'
    if (diffDays <= 7) return `In ${diffDays} days`

    return date.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric'
    })
}

const formatShortReviewDate = (dateString) => {
    if (!dateString) return 'N/A'
    const date = new Date(dateString)
    const now = new Date()
    const diffTime = date - now
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))

    if (diffDays <= 0) return 'Today'
    if (diffDays === 1) return 'Tmrw'
    if (diffDays <= 7) return `${diffDays}d`
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
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

const resetFilters = () => {
    searchTerm.value = ''
    difficultyFilter.value = 'all'
    showAllCards.value = true
}
</script>

<style scoped>
/* Line clamp utilities */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Touch-friendly tap targets */
@media (max-width: 640px) {
    button,
    a,
    [role="button"] {
        min-height: 44px;
    }

    input,
    select {
        min-height: 44px;
    }
}

/* Extra small devices */
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
}
</style>
