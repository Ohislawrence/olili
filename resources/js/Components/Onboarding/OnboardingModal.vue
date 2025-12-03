<template>
    <TransitionRoot appear :show="isOpen" as="template">
        <Dialog as="div" class="relative z-50" @close="closeModal">
            <TransitionChild
                as="template"
                enter="duration-300 ease-out"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="duration-200 ease-in"
                leave-from="opacity-100"
                leave-to="opacity-0"
            >
                <div class="fixed inset-0 bg-emerald-900/40 backdrop-blur-sm" />
            </TransitionChild>

            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center">
                    <TransitionChild
                        as="template"
                        enter="duration-300 ease-out"
                        enter-from="opacity-0 scale-95"
                        enter-to="opacity-100 scale-100"
                        leave="duration-200 ease-in"
                        leave-from="opacity-100 scale-100"
                        leave-to="opacity-0 scale-95"
                    >
                        <DialogPanel
                            :class="[
                                'w-full max-w-2xl transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all',
                                'relative border border-emerald-100'
                            ]"
                        >
                            <!-- Progress Bar -->
                            <div class="absolute top-0 left-0 right-0 h-1.5 bg-emerald-100">
                                <div 
                                    class="h-full bg-emerald-500 transition-all duration-300"
                                    :style="{ width: progressPercentage + '%' }"
                                ></div>
                            </div>

                            <!-- Close Button -->
                            <button
                                @click="skipAll"
                                class="absolute top-4 right-4 text-emerald-400 hover:text-emerald-600 transition-colors"
                            >
                                <XMarkIcon class="h-6 w-6" />
                            </button>

                            <!-- Content -->
                            <div class="pt-8">
                                <!-- Step Icon -->
                                <div class="flex justify-center mb-6">
                                    <div class="text-5xl">
                                        {{ currentStep.icon }}
                                    </div>
                                </div>

                                <!-- Step Title -->
                                <DialogTitle
                                    as="h3"
                                    class="text-2xl font-bold text-center text-emerald-900 mb-3"
                                >
                                    {{ currentStep.title }}
                                </DialogTitle>

                                <!-- Step Description -->
                                <div class="text-center text-emerald-700 mb-8">
                                    <p class="text-lg font-medium">{{ currentStep.description }}</p>
                                </div>

                                <!-- Step Content -->
                                <div class="mb-8">
                                    <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-6">
                                        <div v-if="currentStep.type === 'text'" class="space-y-4">
                                            <p v-for="(item, index) in currentStep.content" :key="index"
                                               class="text-emerald-800">
                                                {{ item }}
                                            </p>
                                        </div>
                                        
                                        <div v-else-if="currentStep.type === 'list'" class="space-y-3">
                                            <div v-for="(item, index) in currentStep.content" 
                                                 :key="index"
                                                 class="flex items-start">
                                                <CheckCircleIcon class="h-5 w-5 text-emerald-500 mt-0.5 mr-3 flex-shrink-0" />
                                                <span class="text-emerald-800">{{ item }}</span>
                                            </div>
                                        </div>
                                        
                                        <div v-else-if="currentStep.type === 'image'" class="text-center">
                                            <img 
                                                :src="currentStep.content" 
                                                :alt="currentStep.title"
                                                class="rounded-lg shadow-lg mx-auto max-h-64 border border-emerald-200"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <!-- Feature Highlight -->
                                <div v-if="currentStep.feature" class="mb-8">
                                    <div class="bg-emerald-100 border border-emerald-200 rounded-lg p-4">
                                        <div class="flex items-center">
                                            <SparklesIcon class="h-5 w-5 text-emerald-600 mr-2" />
                                            <span class="font-medium text-emerald-800">
                                                {{ currentStep.feature }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Navigation -->
                                <div class="flex justify-between items-center pt-4">
                                    <div class="text-sm text-emerald-600 font-medium">
                                        Step {{ currentStepIndex + 1 }} of {{ steps.length }}
                                    </div>
                                    
                                    <div class="flex space-x-3">
                                        <!-- Back Button -->
                                        <button
                                            v-if="currentStepIndex > 0"
                                            @click="prevStep"
                                            type="button"
                                            class="px-5 py-2.5 text-sm font-medium text-emerald-700 bg-white border border-emerald-300 rounded-lg hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-200"
                                        >
                                            Back
                                        </button>

                                        <!-- Next/Finish Button -->
                                        <button
                                            @click="nextStep"
                                            type="button"
                                            class="px-5 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-lg hover:from-emerald-600 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 shadow-md hover:shadow-lg transition-all duration-200"
                                        >
                                            {{ currentStepIndex === steps.length - 1 ? 'Get Started!' : 'Next' }}
                                            <ChevronRightIcon v-if="currentStepIndex !== steps.length - 1" 
                                                              class="h-4 w-4 inline ml-1" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import {
    Dialog,
    DialogPanel,
    DialogTitle,
    TransitionRoot,
    TransitionChild,
} from '@headlessui/vue';
import {
    XMarkIcon,
    CheckCircleIcon,
    SparklesIcon,
    ChevronRightIcon,
} from '@heroicons/vue/24/outline';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    user: {
        type: Object,
        required: true,
    }
});

const emit = defineEmits(['close']);

const isOpen = ref(props.show);
const currentStepIndex = ref(0);

// Onboarding steps configuration
const steps = ref([
    {
        id: 'welcome',
        title: 'Welcome to OliLearn! 🎉',
        description: 'Your AI-driven learning platform that adapts to you',
        icon: '👋',
        type: 'text',
        content: [
            'Get ready to transform your learning experience with AI assistance, interactive courses, quizzes, and more.',
            'This quick tour will show you how to make the most of OliLearn.',
        ],
        feature: null,
    },
    {
        id: 'dashboard',
        title: 'Your Learning Dashboard',
        description: 'Central hub for all your activities',
        icon: '📊',
        type: 'list',
        content: [
            'Track your progress across all courses',
            'Quick access to recent activities',
            'View upcoming deadlines and recommendations',
            'Access AI Tutor anytime from here',
        ],
        feature: 'Tip: Visit your dashboard daily to stay on track!',
    },
    {
        id: 'courses',
        title: 'Create & Manage Courses',
        description: 'Organize your learning journey',
        icon: '📚',
        type: 'text',
        content: [
            'Create personalized courses for any subject - WAEC, JAMB, professional certifications, or personal interests.',
            'Each course includes lessons, quizzes, You can create flashcards, and AI-powered explanations and chats.',
        ],
        feature: 'Try creating your first course now!',
    },
    {
        id: 'ai-tutor',
        title: 'Oli Tutor - Your AI Assistant',
        description: '24/7 learning companion',
        icon: '🤖',
        type: 'list',
        content: [
            'Ask questions about any topic in your courses',
            'Get instant explanations and examples',
            'Request quiz questions on specific topics',
            'Receive personalized study recommendations',
        ],
        feature: 'Look for the Oli AI Tutor button onn the bottom right while taking your course',
    },
    {
        id: 'quizzes',
        title: 'Interactive Quizzes',
        description: 'Test your knowledge',
        icon: '🧠',
        type: 'text',
        content: [
            'Take quizzes to reinforce what you\'ve learned',
            'Track your scores and identify weak areas',
            'Get detailed explanations for each answer',
            'Challenge yourself with timed quizzes',
        ],
        feature: 'Regular quizzes improve retention by 60%!',
    },
    {
        id: 'flashcards',
        title: 'Smart Flashcards',
        description: 'Master key concepts',
        icon: '🎴',
        type: 'list',
        content: [
            'Review important terms and definitions',
            'Mark cards as "mastered" or "need review"',
            'Spaced repetition for better memory',
            'Create custom flashcards for any topic',
        ],
        feature: 'Flashcards can be created from any course.',
    },
    {
        id: 'progress',
        title: 'Track Your Progress',
        description: 'See how far you\'ve come',
        icon: '📈',
        type: 'text',
        content: [
            'Visual progress charts for each course',
            'Detailed analytics on study time and performance',
            'Set and track learning goals',
            'Receive achievement badges for milestones',
        ],
        feature: 'Consistency is key - aim for daily progress!',
    },
    {
        id: 'mobile',
        title: 'Learn Anywhere',
        description: 'Mobile-friendly platform',
        icon: '📱',
        type: 'list',
        content: [
            'Access all features on mobile devices',
            'Study on the go with our responsive design',
            'Download lessons for offline study',
            'Push notifications for reminders',
        ],
        feature: 'Bookmark OliLearn on your phone for quick access',
    },
    {
        id: 'community',
        title: 'Join Our Community',
        description: 'Learn with others',
        icon: '👥',
        type: 'text',
        content: [
            'Connect with other learners on similar journeys',
            'Share insights and study tips',
            'Participate in group discussions',
            'Find study partners for difficult topics',
        ],
        feature: 'Learning together makes the journey more enjoyable!',
    },
    {
        id: 'get-started',
        title: 'Ready to Begin? 🚀',
        description: 'Your learning adventure starts now',
        icon: '🎯',
        type: 'list',
        content: [
            'Complete your profile for personalized recommendations',
            'Create your first course',
            'Try asking Oli Tutor a question',
            'Set a weekly study goal',
        ],
        feature: 'Need help? Click the support button anytime!',
    },
]);

const currentStep = computed(() => steps.value[currentStepIndex.value]);
const progressPercentage = computed(() => 
    ((currentStepIndex.value + 1) / steps.value.length) * 100
);

// Check if user has completed onboarding
const hasCompletedOnboarding = computed(() => 
    props.user.onboarding_completed_at !== null
);

onMounted(() => {
    // Show onboarding on first login or if user wants to restart
    if (!hasCompletedOnboarding.value || localStorage.getItem('showOnboarding')) {
        isOpen.value = true;
    }
});

const nextStep = () => {
    if (currentStepIndex.value < steps.value.length - 1) {
        currentStepIndex.value++;
    } else {
        finishOnboarding();
    }
};

const prevStep = () => {
    if (currentStepIndex.value > 0) {
        currentStepIndex.value--;
    }
};

const finishOnboarding = () => {
    // Mark onboarding as complete
    router.post(route('onboarding.complete'), {}, {
        onSuccess: () => {
            isOpen.value = false;
            emit('close');
            localStorage.removeItem('showOnboarding');
        }
    });
};

const skipAll = () => {
    if (confirm('Skip onboarding? You can restart it anytime from your profile.')) {
        router.post(route('onboarding.skip'), {}, {
            onSuccess: () => {
                isOpen.value = false;
                emit('close');
            }
        });
    }
};

const closeModal = () => {
    // Don't close on overlay click - require explicit action
    return false;
};
</script>

<style scoped>
/* Custom emerald color enhancements */
:deep(.bg-emerald-50) {
    background-color: rgba(209, 250, 229, 0.3);
}

:deep(.border-emerald-100) {
    border-color: rgba(167, 243, 208, 0.5);
}

:deep(.text-emerald-900) {
    color: #064e3b;
}

:deep(.text-emerald-800) {
    color: #065f46;
}

:deep(.text-emerald-700) {
    color: #047857;
}

:deep(.text-emerald-600) {
    color: #059669;
}

/* Gradient animation for the next button */
button.bg-gradient-to-r:hover {
    background-size: 200% auto;
    background-position: right center;
}
</style>