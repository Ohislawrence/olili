<template>
    <AppLayout>
        <Head title="Features - AI-Powered Learning Platform" />

        <!-- Hero Section -->
        <section class="relative py-20 bg-gradient-to-br from-emerald-600 to-teal-600 text-white overflow-hidden">
            <!-- Background Elements -->
            <div class="absolute inset-0">
                <div class="absolute top-10 left-10 w-20 h-20 bg-white/10 rounded-full animate-pulse-slow"></div>
                <div class="absolute top-1/4 right-20 w-32 h-32 bg-white/5 rounded-full animate-float"></div>
                <div class="absolute bottom-20 left-1/3 w-24 h-24 bg-white/10 rounded-full animate-bounce-slow"></div>
                <div class="absolute top-1/2 right-1/4 w-16 h-16 bg-white/5 rounded-full animate-ping-slow"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="text-center">
                    <div class="inline-flex items-center px-4 py-2 rounded-full bg-white/20 backdrop-blur-sm border border-white/30 text-white text-sm font-medium mb-8">
                        <span class="w-2 h-2 bg-white rounded-full mr-2 animate-pulse"></span>
                        AI-Powered Learning Platform
                    </div>
                    <h1 class="text-4xl md:text-6xl font-bold mb-6 leading-tight">
                        Powerful Features for
                        <span class="block bg-gradient-to-r from-white to-emerald-200 bg-clip-text text-transparent">
                            Smart Learning
                        </span>
                    </h1>
                    <p class="text-xl md:text-2xl mb-12 text-emerald-100 max-w-3xl mx-auto">
                        Discover how Olilearn combines artificial intelligence with proven learning methodologies
                        to create the most effective and engaging educational experience.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <Link
                            :href="route('register')"
                            class="inline-flex items-center px-8 py-4 bg-white text-emerald-700 font-bold rounded-xl hover:bg-emerald-50 transform transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-xl group"
                        >
                            <AcademicCapIcon class="h-5 w-5 mr-2" />
                            Start Learning Free
                        </Link>
                        <a
                            href="#features"
                            class="inline-flex items-center px-8 py-4 border-2 border-white text-white font-bold rounded-xl hover:bg-white/10 transform transition-all duration-300 hover:scale-105 group"
                        >
                            <LightBulbIcon class="h-5 w-5 mr-2" />
                            Explore Features
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="py-16 bg-gradient-to-br from-slate-50 to-emerald-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                    <div class="p-6 bg-white rounded-2xl shadow-sm border border-emerald-100">
                        <div class="text-3xl md:text-4xl font-bold text-emerald-700 mb-2">98%</div>
                        <div class="text-emerald-600 font-medium">Success Rate</div>
                    </div>
                    <div class="p-6 bg-white rounded-2xl shadow-sm border border-emerald-100">
                        <div class="text-3xl md:text-4xl font-bold text-emerald-700 mb-2">2.5x</div>
                        <div class="text-emerald-600 font-medium">Faster Learning</div>
                    </div>
                    <div class="p-6 bg-white rounded-2xl shadow-sm border border-emerald-100">
                        <div class="text-3xl md:text-4xl font-bold text-emerald-700 mb-2">50K+</div>
                        <div class="text-emerald-600 font-medium">Active Learners</div>
                    </div>
                    <div class="p-6 bg-white rounded-2xl shadow-sm border border-emerald-100">
                        <div class="text-3xl md:text-4xl font-bold text-emerald-700 mb-2">4.8★</div>
                        <div class="text-emerald-600 font-medium">Average Rating</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Core Features Grid -->
        <section id="features" class="py-24 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <div class="inline-flex items-center px-4 py-2 rounded-full bg-emerald-100 border border-emerald-200 text-emerald-700 text-sm font-medium mb-4">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></span>
                        Core Features
                    </div>
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">
                        CORE
                        <span class="block text-emerald-700 bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">FEATURES</span>
                    </h2>
                    <p class="text-xl text-gray-800 max-w-2xl mx-auto">
                        Everything you need to learn effectively, powered by advanced artificial intelligence.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div
                        v-for="(feature, index) in mainFeatures"
                        :key="feature.title"
                        class="bg-white border border-gray-200 rounded-2xl p-8 shadow-sm hover:shadow-xl transition-all duration-500 feature-card group"
                        :style="{ 'animation-delay': index * 0.1 + 's' }"
                    >
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white text-2xl mb-6 transform transition-transform group-hover:scale-110 group-hover:rotate-3">
                            {{ feature.icon }}
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">{{ feature.title }}</h3>
                        <p class="text-gray-700 leading-relaxed mb-6">{{ feature.description }}</p>
                        <ul class="space-y-3">
                            <li
                                v-for="(point, pointIndex) in feature.points"
                                :key="pointIndex"
                                class="flex items-start text-gray-700"
                            >
                                <CheckCircleIcon class="h-5 w-5 text-emerald-500 mr-3 mt-0.5 flex-shrink-0" />
                                <span>{{ point }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- AI-Powered Learning Section -->
        <section class="py-24 bg-gradient-to-br from-slate-50 to-emerald-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Centered Heading -->
                <div class="text-center mb-16">
                    <div class="inline-flex items-center px-4 py-2 rounded-full bg-emerald-100 border border-emerald-200 text-emerald-700 text-sm font-medium mb-6">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></span>
                        AI Intelligence
                    </div>
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">
                        Adaptive Learning
                        <span class="block text-emerald-700 bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                            That Knows You
                        </span>
                    </h2>
                    <p class="text-xl text-gray-700 max-w-3xl mx-auto leading-relaxed">
                        Our AI continuously analyzes your learning patterns, strengths, and areas for improvement
                        to deliver a truly personalized educational experience.
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                    <!-- Visual Representation -->
                    <div class="relative">
                        <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-8 text-white relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full -translate-y-10 translate-x-10"></div>
                            <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/10 rounded-full translate-y-10 -translate-x-10"></div>

                            <div class="relative z-10">
                                <div class="text-6xl mb-6">🤖</div>
                                <h3 class="text-2xl font-bold mb-4">Smart Learning Assistant</h3>
                                <p class="text-emerald-100 mb-6">
                                    Your personal AI tutor available 24/7 to answer questions, provide explanations,
                                    and guide your learning journey.
                                </p>
                                <div class="space-y-3">
                                    <div class="flex items-center text-emerald-100">
                                        <CheckCircleIcon class="h-5 w-5 mr-3" />
                                        Instant answers to your questions
                                    </div>
                                    <div class="flex items-center text-emerald-100">
                                        <CheckCircleIcon class="h-5 w-5 mr-3" />
                                        Step-by-step explanations
                                    </div>
                                    <div class="flex items-center text-emerald-100">
                                        <CheckCircleIcon class="h-5 w-5 mr-3" />
                                        Personalized learning recommendations
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Floating elements -->
                        <div class="absolute -top-6 -right-6 w-16 h-16 rounded-2xl bg-white shadow-lg border border-gray-200 flex items-center justify-center animate-float-slow">
                            <span class="text-2xl">🎯</span>
                        </div>
                        <div class="absolute -bottom-4 -left-4 w-12 h-12 rounded-full bg-white shadow-lg border border-gray-200 flex items-center justify-center animate-float-slow" style="animation-delay: 2s;">
                            <span class="text-xl">⚡</span>
                        </div>
                    </div>

                    <!-- Content -->
                    <div>
                        <div class="space-y-6">
                            <div
                                v-for="(aiFeature, index) in aiFeatures"
                                :key="aiFeature.title"
                                class="flex items-start space-x-4 group"
                            >
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center text-white transform transition-transform group-hover:scale-110">
                                    <span class="text-xl">{{ aiFeature.icon }}</span>
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900 mb-2 group-hover:text-emerald-600 transition-colors">
                                        {{ aiFeature.title }}
                                    </h4>
                                    <p class="text-gray-700">{{ aiFeature.description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- For Students Section -->
        <section class="py-24 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">
                        FOR
                        <span class="block text-emerald-700 bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">STUDENTS</span>
                    </h2>
                    <p class="text-xl text-gray-800 max-w-2xl mx-auto">
                        Everything you need to excel in your studies and achieve your learning goals.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div
                        v-for="(feature, index) in studentFeatures"
                        :key="feature.title"
                        class="bg-gradient-to-b from-white to-emerald-50 border border-emerald-100 rounded-2xl p-6 shadow-sm hover:shadow-xl transition-all duration-500 group text-center"
                        :style="{ 'animation-delay': index * 0.1 + 's' }"
                    >
                        <div class="w-12 h-12 mx-auto mb-4 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white text-xl transform transition-transform group-hover:scale-110">
                            {{ feature.icon }}
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-3">{{ feature.title }}</h3>
                        <p class="text-gray-700">{{ feature.description }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- For Tutors Section -->
        <section class="py-24 bg-gradient-to-br from-slate-50 to-emerald-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Centered Heading -->
                <div class="text-center mb-16">
                    <div class="inline-flex items-center px-4 py-2 rounded-full bg-emerald-100 border border-emerald-200 text-emerald-700 text-sm font-medium mb-6">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></span>
                        For Educators
                    </div>
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">
                        Teaching
                        <span class="block text-emerald-700 bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                            Made Powerful
                        </span>
                    </h2>
                    <p class="text-xl text-gray-700 max-w-3xl mx-auto leading-relaxed">
                        Empower your teaching with AI tools that handle administrative tasks,
                        provide insights, and help you create exceptional learning experiences.
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                    <!-- Content -->
                    <div>
                        <div class="space-y-6">
                            <div
                                v-for="(tutorFeature, index) in tutorFeatures"
                                :key="tutorFeature.title"
                                class="flex items-start space-x-4 group"
                            >
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center text-white transform transition-transform group-hover:scale-110">
                                    <span class="text-xl">{{ tutorFeature.icon }}</span>
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900 mb-2 group-hover:text-emerald-600 transition-colors">
                                        {{ tutorFeature.title }}
                                    </h4>
                                    <p class="text-gray-700">{{ tutorFeature.description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Visual -->
                    <div class="order-1 lg:order-2">
                        <div class="bg-gradient-to-br from-emerald-500 to-teal-500 rounded-2xl p-8 text-white relative overflow-hidden">
                            <div class="absolute top-0 left-0 w-32 h-32 bg-white/10 rounded-full -translate-y-16 -translate-x-16"></div>
                            <div class="absolute bottom-0 right-0 w-24 h-24 bg-white/10 rounded-full translate-y-12 translate-x-12"></div>

                            <div class="relative z-10">
                                <div class="text-6xl mb-6">👨‍🏫</div>
                                <h3 class="text-2xl font-bold mb-4">Teaching Power Tools</h3>
                                <p class="text-emerald-100 mb-6">
                                    Advanced tools to create engaging content, track student progress, and focus on what you do best - teaching.
                                </p>
                                <div class="space-y-3">
                                    <div class="flex items-center text-emerald-100">
                                        <CheckCircleIcon class="h-5 w-5 mr-3" />
                                        AI-assisted course creation
                                    </div>
                                    <div class="flex items-center text-emerald-100">
                                        <CheckCircleIcon class="h-5 w-5 mr-3" />
                                        Automated grading & feedback
                                    </div>
                                    <div class="flex items-center text-emerald-100">
                                        <CheckCircleIcon class="h-5 w-5 mr-3" />
                                        Detailed student analytics
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Platform Technology -->
        <section class="py-24 bg-gradient-to-br from-gray-900 to-emerald-900 text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold mb-4">
                        PLATFORM
                        <span class="block bg-gradient-to-r from-emerald-400 to-teal-400 bg-clip-text text-transparent">TECHNOLOGY</span>
                    </h2>
                    <p class="text-xl text-gray-300 max-w-2xl mx-auto">
                        Built with cutting-edge technology to deliver the best learning experience.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div
                        v-for="(tech, index) in platformTech"
                        :key="tech.title"
                        class="bg-gray-800/50 backdrop-blur-sm rounded-2xl p-8 hover:bg-gray-800/70 transition-all duration-500 group"
                        :style="{ 'animation-delay': index * 0.1 + 's' }"
                    >
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center text-white text-2xl mb-6 transform transition-transform group-hover:scale-110">
                            {{ tech.icon }}
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-4">{{ tech.title }}</h3>
                        <p class="text-gray-300 leading-relaxed">{{ tech.description }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Final CTA -->
        <section class="py-24 bg-gradient-to-br from-emerald-600 to-teal-600 text-white">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-4xl font-bold mb-6">Ready to Transform Your Learning?</h2>
                <p class="text-xl text-emerald-100 mb-8 max-w-2xl mx-auto">
                    Join thousands of learners and educators who are already transforming education with AI.
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <Link
                        :href="route('register')"
                        class="inline-flex items-center px-8 py-4 bg-white text-emerald-700 font-bold rounded-xl hover:bg-emerald-50 transform transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-xl"
                    >
                        <AcademicCapIcon class="h-5 w-5 mr-2" />
                        Start Free Trial
                    </Link>
                    <Link
                        :href="route('pricing')"
                        class="inline-flex items-center px-8 py-4 border-2 border-white text-white font-bold rounded-xl hover:bg-white/10 transform transition-all duration-300 hover:scale-105"
                    >
                        <CurrencyDollarIcon class="h-5 w-5 mr-2" />
                        View Pricing
                    </Link>
                </div>
                <div class="mt-8 text-emerald-200 text-sm">
                    <LockClosedIcon class="h-4 w-4 inline mr-2" />
                    No credit card required • 14-day free trial • Cancel anytime
                </div>
            </div>
        </section>
    </AppLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/GuestLayout.vue'
import MetaTags from '@/Components/MetaTags.vue';
import {
    AcademicCapIcon,
    LightBulbIcon,
    CheckCircleIcon,
    LockClosedIcon,
    CurrencyDollarIcon,
} from '@heroicons/vue/24/outline'

// Feature data remains the same
const mainFeatures = [
    {
        icon: '🧠',
        title: 'Adaptive Learning Paths',
        description: 'Personalized learning journeys that adapt to your pace, style, and goals.',
        points: [
            'AI-powered course recommendations',
            'Dynamic difficulty adjustment',
            'Personalized study schedules',
            'Progress-based content delivery'
        ]
    },
    {
        icon: '💬',
        title: 'AI Tutoring Assistant',
        description: '24/7 intelligent tutoring with instant answers and detailed explanations.',
        points: [
            'Natural language conversations',
            'Step-by-step problem solving',
            'Concept clarification',
            'Real-time feedback'
        ]
    },
    {
        icon: '📊',
        title: 'Advanced Analytics',
        description: 'Deep insights into your learning patterns and performance metrics.',
        points: [
            'Progress tracking & visualization',
            'Strength & weakness analysis',
            'Learning habit insights',
            'Predictive performance analytics'
        ]
    },
    {
        icon: '🎯',
        title: 'Smart Assessments',
        description: 'Intelligent testing that adapts to your knowledge level and learning needs.',
        points: [
            'Adaptive quiz generation',
            'Automated grading & feedback',
            'Knowledge gap identification',
            'Personalized practice questions'
        ]
    },
    {
        icon: '🤝',
        title: 'Collaborative Learning',
        description: 'Connect with peers and mentors in an engaging learning community.',
        points: [
            'Group study sessions',
            'Peer-to-peer learning',
            'Expert mentorship',
            'Discussion forums'
        ]
    },
    {
        icon: '📱',
        title: 'Multi-Platform Access',
        description: 'Seamless learning experience across all your devices.',
        points: [
            'Progressive web app',
            'Mobile-first design',
            'Offline learning capability',
            'Sync across devices'
        ]
    }
]

const aiFeatures = [
    {
        icon: "🧠",
        title: "Personalized Learning Paths",
        description: "AI creates custom courses that adapt to your learning style, pace, and knowledge gaps for optimal progress."
    },
    {
        icon: "💬",
        title: "24/7 AI Tutor Support",
        description: "Get instant answers and explanations anytime you're stuck. Your AI tutor understands your course context."
    },
    {
        icon: "📈",
        title: "Progress Intelligence",
        description: "Smart analytics track your learning patterns and automatically adjust content difficulty and focus areas."
    },
    {
        icon: "🔄",
        title: "Adaptive Assessments",
        description: "Quizzes and projects that evolve based on your performance, ensuring you master each concept thoroughly."
    }
]

const studentFeatures = [
    {
        icon: '⏰',
        title: 'Smart Scheduling',
        description: 'AI-optimized study plans that fit your calendar and learning pace'
    },
    {
        icon: '🏆',
        title: 'Gamified Learning',
        description: 'Earn badges, points, and rewards as you progress through courses'
    },
    {
        icon: '📚',
        title: 'Personal Library',
        description: 'Save and organize your learning materials for easy access'
    },
    {
        icon: '👥',
        title: 'Study Groups',
        description: 'Connect with peers studying similar topics for collaborative learning'
    },
    {
        icon: '🎓',
        title: 'Certification',
        description: 'Earn verifiable certificates upon course completion'
    },
    {
        icon: '🔔',
        title: 'Smart Reminders',
        description: 'Personalized notifications to keep you on track with your goals'
    },
    {
        icon: '📖',
        title: 'Note Taking',
        description: 'AI-enhanced note taking with automatic organization and summarization'
    },
    {
        icon: '🌐',
        title: 'Global Community',
        description: 'Learn with students from around the world in our vibrant community'
    }
]

const tutorFeatures = [
    {
        icon: "📚",
        title: "AI Course Creation",
        description: "Create personalized learning paths and course materials in minutes with AI assistance tailored to each student's needs."
    },
    {
        icon: "🤖",
        title: "Smart Tutoring Assistant",
        description: "Let our AI handle routine questions while you focus on complex concepts and personalized guidance."
    },
    {
        icon: "📊",
        title: "Progress Analytics",
        description: "Get detailed insights into student performance, learning patterns, and areas needing improvement."
    },
    {
        icon: "⚡",
        title: "Automated Assessment",
        description: "Generate quizzes and projects automatically, with AI-powered grading and personalized feedback."
    }
]

const platformTech = [
    {
        icon: '🤖',
        title: 'Machine Learning',
        description: 'Advanced ML algorithms that continuously improve based on user interactions and learning patterns.'
    },
    {
        icon: '☁️',
        title: 'Cloud Infrastructure',
        description: 'Scalable cloud architecture ensuring fast, reliable access from anywhere in the world.'
    },
    {
        icon: '🔒',
        title: 'Enterprise Security',
        description: 'Bank-level security with encryption, secure data storage, and privacy protection.'
    },
    {
        icon: '📊',
        title: 'Big Data Analytics',
        description: 'Processing millions of data points to derive meaningful learning insights and recommendations.'
    },
    {
        icon: '🌐',
        title: 'Global CDN',
        description: 'Content delivery network ensuring fast loading times and smooth experience worldwide.'
    },
    {
        icon: '🔄',
        title: 'Real-time Sync',
        description: 'Instant synchronization across all devices so you never lose your progress.'
    }
]
</script>

<style scoped>
.feature-card {
    transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.feature-card:hover {
    transform: translateY(-8px);
}

@keyframes pulse-slow {
    0%, 100% { opacity: 0.5; transform: scale(1); }
    50% { opacity: 0.8; transform: scale(1.05); }
}

@keyframes float {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(5deg); }
}

@keyframes bounce-slow {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

@keyframes ping-slow {
    0% { transform: scale(1); opacity: 1; }
    75%, 100% { transform: scale(2); opacity: 0; }
}

@keyframes float-slow {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    50% { transform: translateY(-15px) rotate(3deg); }
}

.animate-pulse-slow {
    animation: pulse-slow 4s ease-in-out infinite;
}

.animate-float {
    animation: float 6s ease-in-out infinite;
}

.animate-bounce-slow {
    animation: bounce-slow 3s infinite;
}

.animate-ping-slow {
    animation: ping-slow 3s cubic-bezier(0, 0, 0.2, 1) infinite;
}

.animate-float-slow {
    animation: float-slow 4s ease-in-out infinite;
}
</style>
