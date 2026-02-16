<template>
    <StudentLayout>
        <Head :title="`Study - ${flashcard_set.title}`" />

        <div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 py-4 sm:py-8">
            <div class="max-w-4xl mx-auto px-3 sm:px-4 lg:px-8">
                <!-- Header - Mobile Optimized -->
                <div class="mb-6 sm:mb-8">
                    <!-- Breadcrumb - Hidden on mobile, visible on desktop -->
                    <div class="hidden sm:flex items-center space-x-2 text-sm text-gray-600 mb-2">
                        <Link :href="route('student.flashcards.index')" class="hover:text-emerald-600 transition-colors">
                            Flashcards
                        </Link>
                        <span>/</span>
                        <Link :href="route('student.flashcards.show', flashcard_set.id)" class="hover:text-emerald-600 transition-colors">
                            {{ flashcard_set.title }}
                        </Link>
                        <span>/</span>
                        <span class="text-gray-900 font-medium">Study</span>
                    </div>

                    <!-- Mobile Header -->
                    <div class="flex items-center justify-between sm:hidden mb-3">
                        <Link
                            :href="route('student.flashcards.show', flashcard_set.id)"
                            class="inline-flex items-center text-emerald-600"
                        >
                            <ArrowLeftIcon class="h-5 w-5" />
                        </Link>
                        <span class="text-sm font-medium text-gray-600">Study Session</span>
                        <div class="w-5"></div> <!-- Spacer for alignment -->
                    </div>

                    <!-- Title Section -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Study Session</h1>
                            <p class="text-sm sm:text-base text-gray-600 mt-1 sm:mt-2">{{ flashcard_set.title }}</p>
                        </div>
                        <!-- Desktop Back Button -->
                        <Link
                            :href="route('student.flashcards.show', flashcard_set.id)"
                            class="hidden sm:inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors"
                        >
                            <ArrowLeftIcon class="h-4 w-4 mr-2" />
                            Back to Set
                        </Link>
                    </div>
                </div>

                <!-- Study Session Info - Mobile Optimized -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 mb-6 sm:mb-8">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-2">Study Progress</h3>
                            <div class="flex flex-wrap items-center gap-3 sm:gap-6 text-xs sm:text-sm text-gray-600">
                                <span class="flex items-center">
                                    <DocumentTextIcon class="h-3.5 w-3.5 sm:h-4 sm:w-4 mr-1 sm:mr-2" />
                                    {{ currentCardIndex + 1 }} of {{ studyCards.length }} cards
                                </span>
                                <span class="flex items-center">
                                    <ClockIcon class="h-3.5 w-3.5 sm:h-4 sm:w-4 mr-1 sm:mr-2" />
                                    {{ studyMode === 'due' ? 'Due Cards' : 'All Cards' }}
                                </span>
                                <span v-if="sessionStats.studied > 0" class="flex items-center">
                                    <CheckCircleIcon class="h-3.5 w-3.5 sm:h-4 sm:w-4 mr-1 sm:mr-2 text-emerald-600" />
                                    {{ sessionStats.studied }} studied
                                </span>
                            </div>
                        </div>
                        <div class="mt-3 sm:mt-0 text-right">
                            <div class="text-xl sm:text-2xl font-bold text-emerald-600">
                                {{ Math.round(((currentCardIndex) / studyCards.length) * 100) || 0 }}%
                            </div>
                            <div class="text-xs sm:text-sm text-gray-500">Completion</div>
                        </div>
                    </div>
                    <!-- Progress Bar -->
                    <div class="mt-3 sm:mt-4 w-full bg-gray-200 rounded-full h-2">
                        <div
                            class="bg-gradient-to-r from-emerald-500 to-teal-500 h-2 rounded-full transition-all duration-300"
                            :style="{ width: `${((currentCardIndex) / studyCards.length) * 100 || 0}%` }"
                        ></div>
                    </div>
                </div>

                <!-- Flashcard - Mobile Optimized -->
                <div v-if="studyCards.length > 0 && currentCard" class="mb-6 sm:mb-8">
                    <!-- Card Counter - Mobile Optimized -->
                    <div class="text-center mb-3 sm:mb-6">
                        <span class="inline-flex items-center px-2 py-0.5 sm:px-3 sm:py-1 rounded-full text-xs sm:text-sm font-medium bg-emerald-100 text-emerald-800">
                            Card {{ currentCardIndex + 1 }} of {{ studyCards.length }}
                        </span>
                    </div>

                    <!-- Flashcard Container with Flip Animation - Mobile Height Adjusted -->
                    <div class="flashcard-container perspective-1000" @click="flipCard">
                        <div class="flashcard relative w-full h-[20rem] sm:h-96 cursor-pointer" :class="{ flipped: cardFlipped }">
                            <!-- Front of Card (Question) -->
                            <div class="flashcard-face flashcard-front absolute w-full h-full bg-white rounded-xl shadow-lg border border-gray-200 p-4 sm:p-8 backface-hidden overflow-y-auto">
                                <div class="min-h-full flex flex-col">
                                    <!-- Difficulty Badge -->
                                    <div class="flex justify-center mb-2 sm:mb-4">
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 sm:px-3 sm:py-1 rounded-full text-xs font-medium"
                                            :class="{
                                                'bg-emerald-100 text-emerald-800': currentCard.difficulty_level === 'easy',
                                                'bg-amber-100 text-amber-800': currentCard.difficulty_level === 'medium',
                                                'bg-rose-100 text-rose-800': currentCard.difficulty_level === 'hard',
                                            }"
                                        >
                                            {{ currentCard.difficulty_level }}
                                        </span>
                                    </div>

                                    <!-- Question Content - Scrollable if needed -->
                                    <div class="flex-1 flex items-center justify-center py-2 sm:py-4">
                                        <p class="text-lg sm:text-xl lg:text-2xl font-semibold text-gray-900 leading-relaxed text-center break-words max-w-full">
                                            {{ currentCard.question }}
                                        </p>
                                    </div>

                                    <!-- Hint Text -->
                                    <div class="mt-auto pt-2">
                                        <p class="text-xs sm:text-sm text-gray-500 flex items-center justify-center">
                                            <EyeIcon class="h-3 w-3 sm:h-4 sm:w-4 mr-1" />
                                            <span class="hidden sm:inline">Click to reveal answer</span>
                                            <span class="sm:hidden">Tap to reveal</span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Back of Card (Answer) -->
                            <div class="flashcard-face flashcard-back absolute w-full h-full bg-white rounded-xl shadow-lg border border-gray-200 p-4 sm:p-8 backface-hidden overflow-y-auto">
                                <div class="min-h-full flex flex-col">
                                    <!-- Difficulty Badge -->
                                    <div class="flex justify-center mb-2 sm:mb-4">
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 sm:px-3 sm:py-1 rounded-full text-xs font-medium"
                                            :class="{
                                                'bg-emerald-100 text-emerald-800': currentCard.difficulty_level === 'easy',
                                                'bg-amber-100 text-amber-800': currentCard.difficulty_level === 'medium',
                                                'bg-rose-100 text-rose-800': currentCard.difficulty_level === 'hard',
                                            }"
                                        >
                                            {{ currentCard.difficulty_level }}
                                        </span>
                                    </div>

                                    <!-- Answer Section -->
                                    <div class="flex-1">
                                        <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-2 sm:mb-4 text-center">Answer</h3>
                                        <p class="text-base sm:text-lg lg:text-xl text-gray-700 leading-relaxed text-center mb-3 sm:mb-6 break-words">
                                            {{ currentCard.answer }}
                                        </p>

                                        <!-- Explanation - Collapsible on Mobile -->
                                        <div
                                            v-if="currentCard.explanation"
                                            class="mt-3 sm:mt-6 p-3 sm:p-4 bg-blue-50 rounded-lg border border-blue-200"
                                        >
                                            <h4 class="text-xs sm:text-sm font-medium text-blue-900 mb-1 sm:mb-2">Explanation</h4>
                                            <p class="text-xs sm:text-sm text-blue-800 leading-relaxed break-words">
                                                {{ currentCard.explanation }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Rating Prompt -->
                                    <div class="mt-3 sm:mt-6 text-center">
                                        <p class="text-xs sm:text-sm text-gray-500">
                                            How well did you know this?
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Difficulty Rating - Mobile Grid -->
                    <div v-if="cardFlipped" class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 mt-4 sm:mt-6">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-3 sm:mb-4 text-center">Rate Your Recall</h3>

                        <!-- Mobile: Horizontal scroll for ratings -->
                        <div class="sm:hidden overflow-x-auto pb-2 -mx-2 px-2">
                            <div class="flex space-x-2 min-w-max">
                                <button
                                    v-for="rating in ratings"
                                    :key="rating.value"
                                    @click="rateCard(rating.value)"
                                    class="flex flex-col items-center p-2 rounded-lg border-2 transition-all duration-200 w-20"
                                    :class="rating.classes"
                                >
                                    <div class="text-xl mb-1">{{ rating.emoji }}</div>
                                    <div class="text-xs font-medium">{{ rating.label }}</div>
                                </button>
                            </div>
                        </div>

                        <!-- Desktop: Grid for ratings -->
                        <div class="hidden sm:grid grid-cols-2 md:grid-cols-6 gap-3">
                            <button
                                v-for="rating in ratings"
                                :key="rating.value"
                                @click="rateCard(rating.value)"
                                class="p-3 rounded-lg border-2 transition-all duration-200 hover:scale-105 hover:shadow-md"
                                :class="rating.classes"
                            >
                                <div class="text-2xl font-bold mb-1">{{ rating.emoji }}</div>
                                <div class="text-sm font-medium">{{ rating.label }}</div>
                                <div class="text-xs text-gray-500 mt-1 hidden md:block">{{ rating.description }}</div>
                            </button>
                        </div>
                    </div>

                    <!-- Navigation Controls - Mobile Optimized -->
                    <div v-else class="flex items-center justify-between gap-2 mt-4 sm:mt-6">
                        <button
                            @click="previousCard"
                            :disabled="currentCardIndex === 0"
                            class="flex-1 sm:flex-none inline-flex items-center justify-center px-2 sm:px-4 py-2 border border-gray-300 text-gray-700 text-xs sm:text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <ArrowLeftIcon class="h-3 w-3 sm:h-4 sm:w-4 mr-1 sm:mr-2" />
                            <span class="hidden xs:inline">Previous</span>
                            <span class="xs:hidden">Prev</span>
                        </button>

                        <button
                            @click="flipCard"
                            class="flex-2 sm:flex-none inline-flex items-center justify-center px-4 sm:px-6 py-2 sm:py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-xs sm:text-sm font-semibold rounded-lg hover:from-emerald-700 hover:to-teal-700 transition-all duration-200 shadow-sm hover:shadow-md"
                        >
                            <EyeIcon class="h-3 w-3 sm:h-4 sm:w-4 mr-1 sm:mr-2" />
                            <span class="hidden xs:inline">Reveal Answer</span>
                            <span class="xs:hidden">Reveal</span>
                        </button>

                        <button
                            @click="nextCard"
                            :disabled="currentCardIndex === studyCards.length - 1"
                            class="flex-1 sm:flex-none inline-flex items-center justify-center px-2 sm:px-4 py-2 border border-gray-300 text-gray-700 text-xs sm:text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span class="hidden xs:inline">Next</span>
                            <span class="xs:hidden">Next</span>
                            <ArrowRightIcon class="h-3 w-3 sm:h-4 sm:w-4 ml-1 sm:ml-2" />
                        </button>
                    </div>
                </div>

                <!-- Session Complete / No Cards States - Mobile Optimized -->
                <div v-else-if="studyCards.length === 0" class="text-center py-8 sm:py-12 px-4 bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="mx-auto h-12 w-12 sm:h-16 sm:w-16 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full flex items-center justify-center mb-3 sm:mb-4">
                        <CheckCircleIcon class="h-6 w-6 sm:h-8 sm:w-8 text-white" />
                    </div>
                    <h3 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2">Study Session Complete! 🎉</h3>
                    <p class="text-sm sm:text-base text-gray-600 mb-4 sm:mb-6 max-w-md mx-auto">
                        You've finished studying all {{ studyMode === 'due' ? 'due' : '' }} cards in this set.
                    </p>
                    <div class="flex flex-col sm:flex-row items-center justify-center space-y-2 sm:space-y-0 sm:space-x-4">
                        <Link
                            :href="route('student.flashcards.study', { flashcardSet: flashcard_set.id, mode: 'due' })"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-2 sm:py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-sm font-semibold rounded-lg hover:from-emerald-700 hover:to-teal-700 transition-all duration-200"
                        >
                            <ArrowPathIcon class="h-4 w-4 mr-2" />
                            Study Due Cards
                        </Link>
                        <Link
                            :href="route('student.flashcards.study', { flashcardSet: flashcard_set.id, mode: 'all' })"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-2 sm:py-3 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors"
                        >
                            <AcademicCapIcon class="h-4 w-4 mr-2" />
                            Study All Cards
                        </Link>
                        <Link
                            :href="route('student.flashcards.show', flashcard_set.id)"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-2 sm:py-3 border border-emerald-300 text-emerald-700 text-sm font-medium rounded-lg hover:bg-emerald-50 transition-colors"
                        >
                            <DocumentTextIcon class="h-4 w-4 mr-2" />
                            Back to Set
                        </Link>
                    </div>
                </div>

                <!-- No Cards Available -->
                <div v-else class="text-center py-8 sm:py-12 px-4 bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="mx-auto h-12 w-12 sm:h-16 sm:w-16 bg-gradient-to-r from-amber-500 to-orange-500 rounded-full flex items-center justify-center mb-3 sm:mb-4">
                        <ExclamationTriangleIcon class="h-6 w-6 sm:h-8 sm:w-8 text-white" />
                    </div>
                    <h3 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2">No Cards Available</h3>
                    <p class="text-sm sm:text-base text-gray-600 mb-4 sm:mb-6">
                        {{ studyMode === 'due'
                            ? 'You have no cards due for review right now. Great job!'
                            : 'This flashcard set is empty.'
                        }}
                    </p>
                    <div class="flex flex-col sm:flex-row items-center justify-center space-y-2 sm:space-y-0 sm:space-x-4">
                        <Link
                            v-if="studyMode === 'due'"
                            :href="route('student.flashcards.study', { flashcardSet: flashcard_set.id, mode: 'all' })"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-2 sm:py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-sm font-semibold rounded-lg hover:from-emerald-700 hover:to-teal-700 transition-all duration-200"
                        >
                            <AcademicCapIcon class="h-4 w-4 mr-2" />
                            Study All Cards
                        </Link>
                        <Link
                            :href="route('student.flashcards.show', flashcard_set.id)"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-2 sm:py-3 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors"
                        >
                            <ArrowLeftIcon class="h-4 w-4 mr-2" />
                            Back to Set
                        </Link>
                    </div>
                </div>

                <!-- Session Statistics - Mobile Optimized -->
                <div v-if="sessionStats.studied > 0" class="mt-6 sm:mt-8 bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-3 sm:mb-4">Session Statistics</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-6">
                        <div class="text-center">
                            <div class="text-lg sm:text-2xl font-bold text-emerald-600">{{ sessionStats.studied }}</div>
                            <div class="text-xs sm:text-sm text-gray-600">Cards Studied</div>
                        </div>
                        <div class="text-center">
                            <div class="text-lg sm:text-2xl font-bold text-amber-600">{{ sessionStats.easy }}</div>
                            <div class="text-xs sm:text-sm text-gray-600">Easy</div>
                        </div>
                        <div class="text-center">
                            <div class="text-lg sm:text-2xl font-bold text-blue-600">{{ sessionStats.medium }}</div>
                            <div class="text-xs sm:text-sm text-gray-600">Medium</div>
                        </div>
                        <div class="text-center">
                            <div class="text-lg sm:text-2xl font-bold text-rose-600">{{ sessionStats.hard }}</div>
                            <div class="text-xs sm:text-sm text-gray-600">Hard</div>
                        </div>
                    </div>
                </div>

                <!-- Keyboard Shortcuts Hint - Hidden on Mobile -->
                <div class="hidden sm:block mt-8 text-center">
                    <p class="text-xs text-gray-500">
                        <span class="inline-flex items-center mr-3"><kbd class="px-2 py-1 bg-gray-100 rounded border border-gray-300 text-xs">Space</kbd> Flip card</span>
                        <span class="inline-flex items-center mr-3"><kbd class="px-2 py-1 bg-gray-100 rounded border border-gray-300 text-xs">←</kbd> <kbd class="px-2 py-1 bg-gray-100 rounded border border-gray-300 text-xs">→</kbd> Navigate</span>
                        <span class="inline-flex items-center"><kbd class="px-2 py-1 bg-gray-100 rounded border border-gray-300 text-xs">1-6</kbd> Rate cards</span>
                    </p>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, computed, reactive, onMounted } from 'vue'
import StudentLayout from '@/Layouts/StudentLayout.vue'
import {
    AcademicCapIcon,
    DocumentTextIcon,
    CalendarIcon,
    ClockIcon,
    EyeIcon,
    ArrowLeftIcon,
    ArrowRightIcon,
    ArrowPathIcon,
    CheckCircleIcon,
    ExclamationTriangleIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
    flashcard_set: Object,
    due_flashcards: Array,
    all_flashcards: Array,
    study_mode: String,
})

// Reactive state
const currentCardIndex = ref(0)
const cardFlipped = ref(false)
const studyMode = ref(props.study_mode || 'due')

// Session statistics
const sessionStats = reactive({
    studied: 0,
    easy: 0,
    medium: 0,
    hard: 0,
})

// Difficulty ratings
const ratings = [
    {
        value: 0,
        emoji: '😵',
        label: 'Forgot',
        description: 'Complete blackout',
        classes: 'border-red-200 bg-red-50 hover:bg-red-100 text-red-700'
    },
    {
        value: 1,
        emoji: '😟',
        label: 'Hard',
        description: 'Incorrect recall',
        classes: 'border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700'
    },
    {
        value: 2,
        emoji: '😐',
        label: 'Medium',
        description: 'Correct with effort',
        classes: 'border-amber-200 bg-amber-50 hover:bg-amber-100 text-amber-700'
    },
    {
        value: 3,
        emoji: '😊',
        label: 'Easy',
        description: 'Correct after hesitation',
        classes: 'border-blue-200 bg-blue-50 hover:bg-blue-100 text-blue-700'
    },
    {
        value: 4,
        emoji: '🎯',
        label: 'Perfect',
        description: 'Instant recall',
        classes: 'border-emerald-200 bg-emerald-50 hover:bg-emerald-100 text-emerald-700'
    },
    {
        value: 5,
        emoji: '🤩',
        label: 'Too Easy',
        description: 'Trivial, review later',
        classes: 'border-purple-200 bg-purple-50 hover:bg-purple-100 text-purple-700'
    }
]

// Computed properties
const studyCards = computed(() => {
    return studyMode.value === 'due' ? props.due_flashcards : props.all_flashcards
})

const currentCard = computed(() => {
    return studyCards.value[currentCardIndex.value]
})

// Methods
const flipCard = () => {
    cardFlipped.value = !cardFlipped.value
}

const nextCard = () => {
    if (currentCardIndex.value < studyCards.value.length - 1) {
        currentCardIndex.value++
        cardFlipped.value = false
    }
}

const previousCard = () => {
    if (currentCardIndex.value > 0) {
        currentCardIndex.value--
        cardFlipped.value = false
    }
}

const rateCard = async (quality) => {
    if (!currentCard.value) return

    try {
        // Update session statistics
        sessionStats.studied++
        if (quality >= 4) sessionStats.easy++
        else if (quality >= 2) sessionStats.medium++
        else sessionStats.hard++

        // Send rating to server
        const response = await axios.post(
            route('student.flashcards.update-progress', currentCard.value.id),
            { quality }
        )

        if (response.data.success) {
            // Move to next card or end session
            if (currentCardIndex.value < studyCards.value.length - 1) {
                currentCardIndex.value++
                cardFlipped.value = false
            } else {
                // Session complete
                currentCardIndex.value = studyCards.value.length
            }
        } else {
            console.error('Failed to update progress:', response.data.error)
        }
    } catch (error) {
        console.error('Error rating card:', error)
        // Still move to next card even if API fails
        if (currentCardIndex.value < studyCards.value.length - 1) {
            currentCardIndex.value++
            cardFlipped.value = false
        }
    }
}

// Keyboard shortcuts
onMounted(() => {
    const handleKeyPress = (event) => {
        if (studyCards.value.length === 0) return

        switch (event.key) {
            case ' ':
            case 'Enter':
                event.preventDefault()
                flipCard()
                break
            case 'ArrowLeft':
                event.preventDefault()
                previousCard()
                break
            case 'ArrowRight':
                event.preventDefault()
                if (cardFlipped.value) {
                    nextCard()
                }
                break
            case '1':
            case '2':
            case '3':
            case '4':
            case '5':
            case '6':
                if (cardFlipped.value) {
                    event.preventDefault()
                    const rating = parseInt(event.key) - 1
                    if (rating >= 0 && rating <= 5) {
                        rateCard(rating)
                    }
                }
                break
        }
    }

    window.addEventListener('keydown', handleKeyPress)

    // Cleanup
    return () => {
        window.removeEventListener('keydown', handleKeyPress)
    }
})
</script>

<style scoped>
/* Flashcard flip animation styles */
.perspective-1000 {
    perspective: 1000px;
}

.flashcard {
    transition: transform 0.6s;
    transform-style: preserve-3d;
    position: relative;
}

.flashcard.flipped {
    transform: rotateY(180deg);
}

.flashcard-face {
    backface-visibility: hidden;
    -webkit-backface-visibility: hidden; /* Safari support */
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch; /* Smooth scrolling on iOS */
}

.flashcard-front {
    z-index: 2;
    transform: rotateY(0deg);
}

.flashcard-back {
    transform: rotateY(180deg);
}

/* Custom scrollbar for flashcard content */
.flashcard-face::-webkit-scrollbar {
    width: 4px;
}

.flashcard-face::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.flashcard-face::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 4px;
}

.flashcard-face::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Mobile height */
@media (max-width: 640px) {
    .h-96 {
        height: 20rem;
    }
}

/* Extra small devices */
@media (max-width: 380px) {
    .h-96 {
        height: 18rem;
    }
}

/* Break words for long content */
.break-words {
    word-break: break-word;
    overflow-wrap: break-word;
    hyphens: auto;
}

/* Custom class for extra small screens */
@media (min-width: 380px) {
    .xs\:inline {
        display: inline;
    }
    .xs\:hidden {
        display: none;
    }
}
</style>
