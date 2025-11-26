<template>
    <StudentLayout>
        <Head :title="`Study - ${flashcard_set.title}`" />

        <div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 py-8">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="flex items-center space-x-2 text-sm text-gray-600 mb-2">
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
                            <h1 class="text-3xl font-bold text-gray-900">Study Session</h1>
                            <p class="text-gray-600 mt-2">{{ flashcard_set.title }}</p>
                        </div>
                        <div class="flex space-x-3">
                            <Link
                                :href="route('student.flashcards.show', flashcard_set.id)"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors"
                            >
                                <ArrowLeftIcon class="h-4 w-4 mr-2" />
                                Back to Set
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Study Session Info -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Study Progress</h3>
                            <div class="flex items-center space-x-6 text-sm text-gray-600">
                                <span class="flex items-center">
                                    <DocumentTextIcon class="h-4 w-4 mr-2" />
                                    {{ currentCardIndex + 1 }} of {{ studyCards.length }} cards
                                </span>
                                <span class="flex items-center">
                                    <ClockIcon class="h-4 w-4 mr-2" />
                                    {{ studyMode === 'due' ? 'Due Cards' : 'All Cards' }}
                                </span>
                                <span v-if="sessionStats.studied > 0" class="flex items-center">
                                    <CheckCircleIcon class="h-4 w-4 mr-2 text-emerald-600" />
                                    {{ sessionStats.studied }} studied
                                </span>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-emerald-600">
                                {{ Math.round(((currentCardIndex) / studyCards.length) * 100) }}%
                            </div>
                            <div class="text-sm text-gray-500">Completion</div>
                        </div>
                    </div>
                    <!-- Progress Bar -->
                    <div class="mt-4 w-full bg-gray-200 rounded-full h-2">
                        <div
                            class="bg-gradient-to-r from-emerald-500 to-teal-500 h-2 rounded-full transition-all duration-300"
                            :style="{ width: `${((currentCardIndex) / studyCards.length) * 100}%` }"
                        ></div>
                    </div>
                </div>

                <!-- Flashcard -->
                <div v-if="studyCards.length > 0 && currentCard" class="mb-8">
                    <!-- Card Counter -->
                    <div class="text-center mb-6">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-emerald-100 text-emerald-800">
                            Card {{ currentCardIndex + 1 }} of {{ studyCards.length }}
                        </span>
                    </div>

                    <!-- Flashcard Container with Flip Animation -->
                    <div class="flashcard-container perspective-1000" @click="flipCard">
                        <div class="flashcard relative w-full h-96 cursor-pointer" :class="{ flipped: cardFlipped }">
                            <!-- Front of Card (Question) -->
                            <div class="flashcard-face flashcard-front absolute w-full h-full bg-white rounded-xl shadow-lg border border-gray-200 p-8 backface-hidden">
                                <div class="h-full flex flex-col justify-center items-center text-center">
                                    <div class="mb-4">
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium mb-4"
                                            :class="{
                                                'bg-emerald-100 text-emerald-800': currentCard.difficulty_level === 'easy',
                                                'bg-amber-100 text-amber-800': currentCard.difficulty_level === 'medium',
                                                'bg-rose-100 text-rose-800': currentCard.difficulty_level === 'hard',
                                            }"
                                        >
                                            {{ currentCard.difficulty_level }}
                                        </span>
                                    </div>
                                    <div class="flex-1 flex items-center justify-center">
                                        <p class="text-2xl font-semibold text-gray-900 leading-relaxed">
                                            {{ currentCard.question }}
                                        </p>
                                    </div>
                                    <div class="mt-6">
                                        <p class="text-sm text-gray-500 flex items-center justify-center">
                                            <EyeIcon class="h-4 w-4 mr-1" />
                                            Click to reveal answer
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Back of Card (Answer) -->
                            <div class="flashcard-face flashcard-back absolute w-full h-full bg-white rounded-xl shadow-lg border border-gray-200 p-8 backface-hidden">
                                <div class="h-full flex flex-col justify-center">
                                    <div class="text-center mb-6">
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                                            :class="{
                                                'bg-emerald-100 text-emerald-800': currentCard.difficulty_level === 'easy',
                                                'bg-amber-100 text-amber-800': currentCard.difficulty_level === 'medium',
                                                'bg-rose-100 text-rose-800': currentCard.difficulty_level === 'hard',
                                            }"
                                        >
                                            {{ currentCard.difficulty_level }}
                                        </span>
                                    </div>

                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-4 text-center">Answer</h3>
                                        <p class="text-xl text-gray-700 leading-relaxed text-center mb-6">
                                            {{ currentCard.answer }}
                                        </p>

                                        <div
                                            v-if="currentCard.explanation"
                                            class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200"
                                        >
                                            <h4 class="text-sm font-medium text-blue-900 mb-2">Explanation</h4>
                                            <p class="text-sm text-blue-800 leading-relaxed">
                                                {{ currentCard.explanation }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="mt-6 text-center">
                                        <p class="text-sm text-gray-500">
                                            How well did you know this?
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Difficulty Rating (Only show when answer is revealed) -->
                    <div v-if="cardFlipped" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 text-center">Rate Your Recall</h3>
                        <div class="grid grid-cols-1 md:grid-cols-6 gap-3">
                            <button
                                v-for="rating in ratings"
                                :key="rating.value"
                                @click="rateCard(rating.value)"
                                class="p-4 rounded-lg border-2 transition-all duration-200 hover:scale-105 hover:shadow-md"
                                :class="rating.classes"
                            >
                                <div class="text-2xl font-bold mb-1">{{ rating.emoji }}</div>
                                <div class="text-sm font-medium">{{ rating.label }}</div>
                                <div class="text-xs text-gray-500 mt-1">{{ rating.description }}</div>
                            </button>
                        </div>
                    </div>

                    <!-- Navigation Controls -->
                    <div v-else class="flex items-center justify-between mt-6">
                        <button
                            @click="previousCard"
                            :disabled="currentCardIndex === 0"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <ArrowLeftIcon class="h-4 w-4 mr-2" />
                            Previous
                        </button>

                        <button
                            @click="flipCard"
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-semibold rounded-lg hover:from-emerald-700 hover:to-teal-700 transition-all duration-200 shadow-sm hover:shadow-md"
                        >
                            <EyeIcon class="h-4 w-4 mr-2" />
                            Reveal Answer
                        </button>

                        <button
                            @click="nextCard"
                            :disabled="currentCardIndex === studyCards.length - 1"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Next
                            <ArrowRightIcon class="h-4 w-4 ml-2" />
                        </button>
                    </div>
                </div>

                <!-- Session Complete -->
                <div v-else-if="studyCards.length === 0" class="text-center py-12 bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="mx-auto h-16 w-16 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full flex items-center justify-center mb-4">
                        <CheckCircleIcon class="h-8 w-8 text-white" />
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Study Session Complete! 🎉</h3>
                    <p class="text-gray-600 mb-6 max-w-md mx-auto">
                        You've finished studying all {{ studyMode === 'due' ? 'due' : '' }} cards in this set.
                    </p>
                    <div class="flex items-center justify-center space-x-4">
                        <Link
                            :href="route('student.flashcards.study', { flashcardSet: flashcard_set.id, mode: 'due' })"
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-semibold rounded-lg hover:from-emerald-700 hover:to-teal-700 transition-all duration-200"
                        >
                            <ArrowPathIcon class="h-4 w-4 mr-2" />
                            Study Due Cards
                        </Link>
                        <Link
                            :href="route('student.flashcards.study', { flashcardSet: flashcard_set.id, mode: 'all' })"
                            class="inline-flex items-center px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors"
                        >
                            <AcademicCapIcon class="h-4 w-4 mr-2" />
                            Study All Cards
                        </Link>
                        <Link
                            :href="route('student.flashcards.show', flashcard_set.id)"
                            class="inline-flex items-center px-6 py-3 border border-emerald-300 text-emerald-700 font-medium rounded-lg hover:bg-emerald-50 transition-colors"
                        >
                            <DocumentTextIcon class="h-4 w-4 mr-2" />
                            Back to Set
                        </Link>
                    </div>
                </div>

                <!-- No Cards Available -->
                <div v-else class="text-center py-12 bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="mx-auto h-16 w-16 bg-gradient-to-r from-amber-500 to-orange-500 rounded-full flex items-center justify-center mb-4">
                        <ExclamationTriangleIcon class="h-8 w-8 text-white" />
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">No Cards Available</h3>
                    <p class="text-gray-600 mb-6">
                        {{ studyMode === 'due'
                            ? 'You have no cards due for review right now. Great job!'
                            : 'This flashcard set is empty.'
                        }}
                    </p>
                    <div class="flex items-center justify-center space-x-4">
                        <Link
                            v-if="studyMode === 'due'"
                            :href="route('student.flashcards.study', { flashcardSet: flashcard_set.id, mode: 'all' })"
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-semibold rounded-lg hover:from-emerald-700 hover:to-teal-700 transition-all duration-200"
                        >
                            <AcademicCapIcon class="h-4 w-4 mr-2" />
                            Study All Cards
                        </Link>
                        <Link
                            :href="route('student.flashcards.show', flashcard_set.id)"
                            class="inline-flex items-center px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors"
                        >
                            <ArrowLeftIcon class="h-4 w-4 mr-2" />
                            Back to Set
                        </Link>
                    </div>
                </div>

                <!-- Session Statistics -->
                <div v-if="sessionStats.studied > 0" class="mt-8 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Session Statistics</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-emerald-600">{{ sessionStats.studied }}</div>
                            <div class="text-sm text-gray-600">Cards Studied</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-amber-600">{{ sessionStats.easy }}</div>
                            <div class="text-sm text-gray-600">Easy</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ sessionStats.medium }}</div>
                            <div class="text-sm text-gray-600">Medium</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-rose-600">{{ sessionStats.hard }}</div>
                            <div class="text-sm text-gray-600">Hard</div>
                        </div>
                    </div>
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

const isLastCard = computed(() => {
    return currentCardIndex.value === studyCards.value.length - 1
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
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.flashcard-front {
    z-index: 2;
    transform: rotateY(0deg);
}

.flashcard-back {
    transform: rotateY(180deg);
}

/* Ensure proper height */
.h-96 {
    height: 24rem;
}
</style>
