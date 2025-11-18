<template>
    <AppLayout>
        <!-- Hero Section -->
        <section class="min-h-screen flex items-center relative overflow-hidden bg-white">
            <!-- Background pattern -->
            <div class="absolute inset-0 z-0 opacity-5">
                <div class="absolute top-20 left-10 w-40 h-40 bg-emerald-500 rounded-full"></div>
                <div class="absolute top-40 right-20 w-60 h-60 bg-teal-500 rounded-full"></div>
                <div class="absolute bottom-20 left-1/4 w-32 h-32 bg-cyan-500 rounded-full"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 relative z-10 w-full">
                <div class="text-center">
                    <h1 class="text-5xl md:text-7xl font-bold mb-6 leading-tight animate-fade-in-up">
                        Learning Made
                        <span class="block text-emerald-700 animate-slide-in-right">
                            Effortless & Smart
                        </span>
                    </h1>
                    <p class="text-xl md:text-2xl mb-12 text-gray-800 max-w-3xl mx-auto animate-fade-in-up" style="animation-delay: 0.3s;">
                        AITutor does more than help you find the right tutor.
                        We accompany you on your learning journey with artificial intelligence,
                        digital tools, and personalized follow-up.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-6 justify-center animate-fade-in-up" style="animation-delay: 0.6s;">
                        <Link
                            :href="route('register')"
                            class="group bg-white border-2 border-emerald-600 text-emerald-700 px-8 py-4 rounded-xl font-semibold text-lg hover:bg-emerald-50 transform transition-all duration-300 hover:scale-105 hover:shadow-lg"
                        >
                            <span class="relative">
                                I'm a Student
                                <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-emerald-600 transition-all duration-300 group-hover:w-full"></span>
                            </span>
                        </Link>
                        <Link
                            :href="route('dashboard')"
                            class="group bg-emerald-700 text-white px-8 py-4 rounded-xl font-semibold text-lg transform transition-all duration-300 hover:scale-105 hover:shadow-lg hover:bg-emerald-800"
                        >
                            <span class="relative">
                                I'm a Tutor
                                <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-white transition-all duration-300 group-hover:w-full"></span>
                            </span>
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- Solution Section -->
        <section class="py-24 relative bg-slate-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">
                        YOUR LEARNING JOURNEY
                        <span class="block text-emerald-700">PEACE OF MIND</span>
                    </h2>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                    <div class="animate-float-in">
                        <h3 class="text-3xl font-bold text-gray-900 mb-8">
                            A LEARNER'S<br>
                            <span class="text-emerald-700">JOURNEY</span><br>
                            NOT A BATTLE
                        </h3>
                        <p class="text-lg text-gray-800 mb-8 leading-relaxed">
                            Learning should be an exciting journey, not a struggle.
                            With AITutor, discover the joy of learning through
                            intelligent tools that adapt to your pace, preferences, and goals.
                        </p>
                        <div class="space-y-6">
                            <div
                                v-for="(benefit, index) in benefits"
                                :key="index"
                                class="flex items-start space-x-4 group"
                                :style="{ 'animation-delay': index * 0.2 + 's' }"
                            >
                                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-emerald-700 flex items-center justify-center text-white text-sm font-bold transform transition-transform group-hover:scale-110 animate-bounce-slow">
                                    {{ index + 1 }}
                                </div>
                                <div class="pt-0.5">
                                    <h4 class="font-semibold text-gray-900 group-hover:text-emerald-700 transition-colors">{{ benefit }}</h4>
                                </div>
                            </div>
                        </div>
                        <Link
                            :href="route('dashboard')"
                            class="inline-flex items-center mt-8 text-emerald-700 font-semibold hover:text-emerald-800 transition-colors group"
                        >
                            Discover the Concept
                            <svg class="ml-2 w-5 h-5 transform transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </Link>
                    </div>

                    <div class="space-y-6">
                        <div
                            v-for="(feature, index) in features"
                            :key="feature.title"
                            class="bg-white border border-gray-200 p-6 rounded-xl shadow-sm hover:shadow-md transition-all duration-300 card-hover"
                            :style="{ 'animation-delay': index * 0.15 + 's' }"
                        >
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-700">
                                    <span class="text-2xl transform transition-transform hover:scale-110">{{ feature.icon }}</span>
                                </div>
                                <div>
                                    <h4 class="text-xl font-semibold text-gray-900 mb-2 group-hover:text-emerald-700 transition-colors">{{ feature.title }}</h4>
                                    <p class="text-gray-700 text-sm leading-relaxed">{{ feature.description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works Section -->
        <section class="py-24 relative bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">
                        HOW IT
                        <span class="block text-emerald-700">WORKS</span>
                    </h2>
                    <p class="text-xl text-gray-800 max-w-3xl mx-auto">
                        Our AI-powered platform adapts to your unique learning style through a seamless process.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div
                        v-for="(step, index) in howItWorks"
                        :key="index"
                        class="text-center group"
                        :style="{ 'animation-delay': index * 0.2 + 's' }"
                    >
                        <div class="relative inline-block mb-6">
                            <div class="w-20 h-20 rounded-full bg-emerald-700 flex items-center justify-center text-white text-xl font-bold mx-auto mb-4 transform transition-transform group-hover:scale-110 animate-step-pop">
                                {{ step.number }}
                            </div>
                            <div v-if="index < 2" class="hidden md:block absolute top-10 left-40 w-32 h-0.5 bg-emerald-300 transform -translate-y-1/2"></div>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">{{ step.title }}</h3>
                        <p class="text-gray-700 leading-relaxed">{{ step.description }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured Courses -->
        <section class="py-24 relative bg-slate-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">
                        FEATURED
                        <span class="block text-emerald-700">COURSES</span>
                    </h2>
                    <p class="text-xl text-gray-800 max-w-2xl mx-auto">
                        Explore our most popular courses designed with AI personalization at their core.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div
                        v-for="(course, index) in featuredCourses"
                        :key="course.id"
                        class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 course-card"
                        :style="{ 'animation-delay': index * 0.15 + 's' }"
                    >
                        <div class="h-48 relative overflow-hidden bg-emerald-700">
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="text-center p-4 transform transition-transform hover:scale-105">
                                    <span class="inline-block px-3 py-1 rounded-full text-sm bg-white/20 backdrop-blur-sm border border-white/30 text-white">
                                        {{ course.category }}
                                    </span>
                                    <h3 class="text-2xl font-bold mt-4 text-white">{{ course.title }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <p class="text-gray-700 mb-6 leading-relaxed">{{ course.description }}</p>
                            <div class="flex justify-between items-center">
                                <div class="flex items-center text-emerald-700 font-semibold">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ course.duration }}
                                </div>
                                <Link
                                    :href="route('dashboard')"
                                    class="text-emerald-700 hover:text-emerald-800 font-medium transition-colors group"
                                >
                                    View Course
                                    <svg class="inline-block ml-1 w-4 h-4 transform transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                    </svg>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-12">
                    <Link
                        :href="route('dashboard')"
                        class="inline-flex items-center text-gray-800 hover:text-emerald-700 font-semibold transition-colors group"
                    >
                        Discover All Our Courses
                        <svg class="ml-2 w-5 h-5 transform transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </Link>
                </div>
            </div>
        </section>

        <!-- Community Section -->
        <section class="py-24 relative bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                    <div class="animate-float-in">
                        <h2 class="text-4xl font-bold text-gray-900 mb-8">
                            A Community of<br>
                            <span class="text-emerald-700">Passionate Learners</span>
                        </h2>
                        <p class="text-lg text-gray-800 mb-8 leading-relaxed">
                            AITutor is about sharing best practices and creating connections because you should know much earlier to make the right learning choices.
                        </p>

                        <div class="space-y-6">
                            <div
                                v-for="topic in communityTopics"
                                :key="topic.title"
                                class="flex items-start space-x-4 group cursor-pointer"
                            >
                                <div class="flex-shrink-0 w-4 h-4 rounded-full bg-emerald-500 mt-2 group-hover:scale-150 transition-transform"></div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 group-hover:text-emerald-700 transition-colors">
                                        {{ topic.title }}
                                    </h4>
                                    <p class="text-gray-600 text-sm">{{ topic.date }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex items-center text-sm text-gray-600">
                            <span class="mr-2">Active members:</span>
                            <div class="flex -space-x-2">
                                <img v-for="n in 5" :key="n" src="https://placehold.co/32x32/059669/ffffff?text=U" alt="User" class="w-8 h-8 rounded-full border-2 border-white">
                            </div>
                            <span class="ml-2 text-emerald-700 font-semibold">+12.5K</span>
                        </div>
                    </div>

                    <div class="relative">
                        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-8 relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-32 h-32 rounded-full bg-emerald-100 -translate-y-16 translate-x-16"></div>
                            <div class="absolute bottom-0 left-0 w-24 h-24 rounded-full bg-emerald-100 translate-y-12 -translate-x-12"></div>

                            <div class="relative z-10">
                                <h3 class="text-2xl font-bold mb-6 text-gray-900">Join the Community</h3>
                                <p class="mb-8 text-gray-800 leading-relaxed">
                                    Exchange with other learners, share your discoveries and progress together in a supportive environment where everyone grows.
                                </p>
                                <Link
                                    :href="route('dashboard')"
                                    class="inline-flex items-center bg-white border border-emerald-600 text-emerald-700 px-6 py-3 rounded-lg font-semibold hover:bg-emerald-50 transform transition-all duration-300 hover:scale-105 group"
                                >
                                    <span class="relative">
                                        Join Us
                                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-emerald-700 transition-all duration-300 group-hover:w-full"></span>
                                    </span>
                                    <svg class="ml-2 w-5 h-5 transform transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                    </svg>
                                </Link>
                            </div>
                        </div>

                        <!-- Floating interactive elements -->
                        <div class="absolute -top-6 -right-6 w-16 h-16 rounded-xl bg-emerald-100 border border-emerald-300 flex items-center justify-center animate-float-slow hover:scale-110 transition-transform cursor-pointer">
                            <svg class="w-8 h-8 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>
                            </svg>
                        </div>
                        <div class="absolute -bottom-4 -left-4 w-12 h-12 rounded-full bg-cyan-100 border border-cyan-300 flex items-center justify-center animate-float-slow" style="animation-delay: 1s;">
                            <svg class="w-6 h-6 text-cyan-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section class="py-24 relative bg-slate-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">
                        WHAT OUR
                        <span class="block text-emerald-700">LEARNERS SAY</span>
                    </h2>
                    <p class="text-xl text-gray-800 max-w-2xl mx-auto">
                        Real stories from students who transformed their learning experience.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div
                        v-for="(testimonial, index) in testimonials"
                        :key="index"
                        class="bg-white border border-gray-200 p-8 rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 testimonial-card"
                        :style="{ 'animation-delay': index * 0.2 + 's' }"
                    >
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 rounded-full overflow-hidden mr-4 border-2 border-emerald-200">
                                <img :src="testimonial.avatar" alt="Avatar" class="w-full h-full object-cover">
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900">{{ testimonial.name }}</h4>
                                <p class="text-sm text-gray-600">{{ testimonial.role }}</p>
                            </div>
                        </div>
                        <p class="text-gray-800 italic leading-relaxed">"{{ testimonial.quote }}"</p>
                        <div class="flex mt-4 text-amber-500">
                            <svg v-for="n in 5" :key="n" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-24 relative bg-white">
            <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8 relative z-10">
                <h2 class="text-4xl font-bold text-gray-900 mb-6">
                    Ready to Transform<br>
                    <span class="text-emerald-700">Your Learning Experience?</span>
                </h2>
                <p class="text-xl mb-12 text-gray-800">
                    We're available to discuss your learning goals and ambitions
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <Link
                        :href="route('dashboard')"
                        class="group bg-white border-2 border-emerald-600 text-emerald-700 px-8 py-4 rounded-xl font-semibold text-lg transform transition-all duration-300 hover:scale-105 hover:shadow-lg hover:bg-emerald-50"
                    >
                        <span class="relative">
                            Contact Us
                            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-emerald-600 transition-all duration-300 group-hover:w-full"></span>
                        </span>
                    </Link>
                    <Link
                        :href="route('dashboard')"
                        class="group bg-emerald-700 text-white px-8 py-4 rounded-xl font-semibold text-lg transform transition-all duration-300 hover:scale-105 hover:shadow-lg hover:bg-emerald-800"
                    >
                        <span class="relative">
                            Request a Demo
                            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-white/50 transition-all duration-300 group-hover:w-full"></span>
                        </span>
                    </Link>
                </div>

                <div class="mt-12 flex items-center justify-center text-sm text-gray-600">
                    <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Free 14-day trial • No credit card required • Cancel anytime</span>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="py-16 border-t border-gray-200 bg-slate-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                    <div v-for="(stat, index) in stats" :key="index" class="stat-item">
                        <div class="text-3xl md:text-4xl font-bold text-gray-900 mb-2 animate-count-up" :data-target="stat.value">{{ stat.value }}</div>
                        <div class="text-gray-700 font-medium">{{ stat.label }}</div>
                    </div>
                </div>
            </div>
        </section>
    </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/GuestLayout.vue';

const benefits = [
    'Personalized learning paths based on your goals',
    'Real-time feedback and adaptive challenges',
    'Progress tracking with actionable insights'
];

const features = [
    {
        icon: '🎯',
        title: 'AI-Powered Personalization',
        description: 'Our algorithms analyze your learning patterns to deliver content that matches your style and pace.'
    },
    {
        icon: '💬',
        title: 'Intelligent Feedback',
        description: 'Get instant, constructive feedback on your work with suggestions for improvement and next steps.'
    },
    {
        icon: '📊',
        title: 'Progress Analytics',
        description: 'Visual dashboards show your growth over time, highlighting strengths and areas for development.'
    }
];

const howItWorks = [
    {
        number: '1',
        title: 'Assessment',
        description: 'We begin with a comprehensive assessment to understand your current knowledge and learning preferences.'
    },
    {
        number: '2',
        title: 'Personalization',
        description: 'Our AI creates a customized learning path tailored to your goals, pace, and preferred methods.'
    },
    {
        number: '3',
        title: 'Growth',
        description: 'Engage with adaptive content and receive real-time feedback as you progress toward mastery.'
    }
];

const featuredCourses = [
    {
        id: 1,
        title: 'AI Fundamentals',
        category: 'Artificial Intelligence',
        description: 'Master the foundations of AI through hands-on projects and real-world applications.',
        duration: '6 Weeks'
    },
    {
        id: 2,
        title: 'Web Development Pro',
        category: 'Full-Stack Development',
        description: 'Build modern web applications using the latest frameworks and best practices.',
        duration: '10 Weeks'
    },
    {
        id: 3,
        title: 'Data Science Mastery',
        category: 'Data Analysis',
        description: 'Transform raw data into insights using Python, statistics, and machine learning techniques.',
        duration: '12 Weeks'
    }
];

const communityTopics = [
    {
        title: 'Learning: Traditional Methods vs AI?',
        date: 'February 10, 2024'
    },
    {
        title: 'Tutoring and Digital Platforms',
        date: 'January 15, 2024'
    },
    {
        title: 'The Autonomous Learner',
        date: 'December 3, 2024'
    }
];

const testimonials = [
    {
        name: 'Sarah Johnson',
        role: 'Computer Science Student',
        quote: 'AITutor helped me go from barely passing to top of my class in just one semester. The personalized feedback made all the difference.',
        avatar: 'https://placehold.co/100x100/059669/ffffff?text=SJ'
    },
    {
        name: 'Michael Chen',
        role: 'Working Professional',
        quote: 'As someone balancing work and learning, AITutor\'s flexibility and smart scheduling have been game-changing for my career growth.',
        avatar: 'https://placehold.co/100x100/0d9488/ffffff?text=MC'
    },
    {
        name: 'Emma Rodriguez',
        role: 'High School Teacher',
        quote: 'I use AITutor to supplement my classroom teaching. My students love the interactive approach and their test scores have improved significantly.',
        avatar: 'https://placehold.co/100x100/0f766e/ffffff?text=ER'
    }
];

const stats = [
    { value: '50K+', label: 'Active Learners' },
    { value: '98%', label: 'Satisfaction Rate' },
    { value: '1.5M', label: 'Hours Learned' },
    { value: '4.8★', label: 'Average Rating' }
];
</script>

<style scoped>
/* Floating Elements */
.floating-circle {
    position: absolute;
    width: 80px;
    height: 80px;
    border-radius: 50%;
    filter: blur(20px);
    opacity: 0.6;
    animation: float 8s ease-in-out infinite;
}

/* Animation Keyframes */
@keyframes float {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    50% { transform: translateY(-30px) rotate(5deg); }
}

@keyframes fade-in-up {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes slide-in-right {
    from { opacity: 0; transform: translateX(50px); }
    to { opacity: 1; transform: translateX(0); }
}

@keyframes float-in {
    from { opacity: 0; transform: translateY(40px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes bounce-slow {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-8px); }
}

@keyframes step-pop {
    0% { transform: scale(0.8); opacity: 0; }
    70% { transform: scale(1.1); }
    100% { transform: scale(1); opacity: 1; }
}

@keyframes float-slow {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    50% { transform: translateY(-15px) rotate(3deg); }
}

@keyframes count-up {
    from { transform: scale(0.5); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}

/* Apply Animations */
.animate-fade-in-up {
    animation: fade-in-up 0.8s ease-out forwards;
}

.animate-slide-in-right {
    animation: slide-in-right 0.8s ease-out forwards;
}

.animate-float-in {
    animation: float-in 0.8s ease-out forwards;
}

.animate-bounce-slow {
    animation: bounce-slow 2.5s infinite;
}

.animate-step-pop {
    animation: step-pop 0.6s ease-out forwards;
}

.animate-float-slow {
    animation: float-slow 5s ease-in-out infinite;
}

.animate-count-up {
    animation: count-up 1s ease-out forwards;
}

/* Card Effects */
.card-hover {
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.card-hover:hover {
    transform: translateY(-5px);
}

.course-card {
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.course-card:hover {
    transform: translateY(-8px);
}

.testimonial-card {
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.testimonial-card:hover {
    transform: translateY(-8px);
}

/* Stat Items */
.stat-item {
    transition: transform 0.3s ease;
}

.stat-item:hover {
    transform: translateY(-5px);
}

/* Ensure proper stacking context */
.relative {
    position: relative;
    z-index: 1;
}
</style>
