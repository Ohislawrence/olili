<template>
    <MetaTags
        title="Frequently Asked Questions"
        description="Find answers to common questions about Olilearn's AI-powered learning platform for Nigerian students, parents, and tutors."
        image="/images/olingolearn.png"
    />
    <AppLayout>
        <Head title="Frequently Asked Questions - Olilearn" />

        <!-- Hero Section -->
        <section class="relative py-20 bg-gradient-to-br from-emerald-600 to-teal-600 text-white overflow-hidden">
            <!-- Background Elements -->
            <div class="absolute inset-0">
                <div class="absolute top-10 left-10 w-20 h-20 bg-white/10 rounded-full animate-pulse-slow"></div>
                <div class="absolute top-1/4 right-20 w-32 h-32 bg-white/5 rounded-full animate-float"></div>
                <div class="absolute bottom-20 left-1/3 w-24 h-24 bg-white/10 rounded-full animate-bounce-slow"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="text-center">
                    <div class="inline-flex items-center px-4 py-2 rounded-full bg-white/20 backdrop-blur-sm border border-white/30 text-white text-sm font-medium mb-8">
                        <span class="w-2 h-2 bg-white rounded-full mr-2 animate-pulse"></span>
                        Help Center
                    </div>
                    <h1 class="text-4xl md:text-6xl font-bold mb-6 leading-tight">
                        Frequently Asked
                        <span class="block bg-gradient-to-r from-white to-emerald-200 bg-clip-text text-transparent">
                            Questions
                        </span>
                    </h1>
                    <p class="text-xl md:text-2xl mb-12 text-emerald-100 max-w-3xl mx-auto">
                        Find quick answers to common questions about Olilearn's AI-powered learning platform
                        for Nigerian students, parents, and tutors.
                    </p>

                    <!-- Search Bar -->
                    <div class="max-w-2xl mx-auto mt-8">
                        <div class="relative">
                            <input
                                type="text"
                                v-model="searchQuery"
                                placeholder="Search for answers..."
                                class="w-full px-6 py-4 pr-12 rounded-2xl bg-white/10 backdrop-blur-sm border border-white/20 text-white placeholder-emerald-200 focus:outline-none focus:ring-2 focus:ring-white/30 focus:border-white/30 transition-all duration-300"
                            />
                            <div class="absolute right-4 top-1/2 transform -translate-y-1/2">
                                <svg class="w-5 h-5 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-emerald-200 text-sm mt-3">
                            {{ filteredFaqs.length }} answers found
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ Categories -->
        <section class="py-16 bg-white border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-wrap gap-4 justify-center">
                    <button
                        v-for="category in categories"
                        :key="category.id"
                        @click="activeCategory = category.id"
                        :class="[
                            'px-6 py-3 rounded-xl font-medium transition-all duration-300 transform hover:scale-105',
                            activeCategory === category.id
                                ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-200'
                                : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100'
                        ]"
                    >
                        <span class="flex items-center">
                            <span class="mr-2">{{ category.icon }}</span>
                            {{ category.name }}
                            <span v-if="category.count" class="ml-2 text-xs opacity-75">
                                ({{ category.count }})
                            </span>
                        </span>
                    </button>
                </div>
            </div>
        </section>

        <!-- FAQ Content -->
        <section class="py-24 bg-white">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Empty State -->
                <div v-if="filteredFaqs.length === 0" class="text-center py-16">
                    <div class="w-24 h-24 mx-auto mb-6 text-gray-400">
                        <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">No results found</h3>
                    <p class="text-gray-600 mb-8 max-w-md mx-auto">
                        We couldn't find any answers matching "{{ searchQuery }}". Try different keywords or browse by category.
                    </p>
                    <button
                        @click="clearSearch"
                        class="inline-flex items-center px-6 py-3 bg-emerald-600 text-white font-medium rounded-xl hover:bg-emerald-700 transition-all duration-300 transform hover:scale-105"
                    >
                        Clear Search
                    </button>
                </div>

                <!-- FAQ Sections -->
                <div v-else class="space-y-8">
                    <div v-for="section in faqSections" :key="section.id" class="border-b border-gray-100 pb-12 last:border-b-0">
                        <div class="flex items-center mb-8">
                            <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 text-xl mr-4">
                                {{ section.icon }}
                            </div>
                            <div>
                                <h2 class="text-3xl font-bold text-gray-900">{{ section.title }}</h2>
                                <p class="text-gray-600 mt-2">{{ section.description }}</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div
                                v-for="faq in section.faqs"
                                :key="faq.id"
                                class="bg-white border border-gray-200 rounded-2xl overflow-hidden hover:shadow-lg transition-all duration-300"
                            >
                                <button
                                    @click="toggleFaq(faq)"
                                    class="w-full flex items-center justify-between text-left p-6 hover:bg-emerald-50 transition-colors duration-300"
                                >
                                    <span class="text-lg font-semibold text-gray-900 pr-8">{{ faq.question }}</span>
                                    <svg
                                        class="w-6 h-6 text-emerald-600 flex-shrink-0 transition-transform duration-300"
                                        :class="{ 'rotate-180': faq.open }"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <transition
                                    enter-active-class="transition-all duration-300 ease-out"
                                    leave-active-class="transition-all duration-300 ease-in"
                                    enter-from-class="opacity-0 max-h-0"
                                    enter-to-class="opacity-100 max-h-96"
                                    leave-from-class="opacity-100 max-h-96"
                                    leave-to-class="opacity-0 max-h-0"
                                >
                                    <div v-show="faq.open" class="px-6 pb-6">
                                        <div class="text-gray-700 leading-relaxed">
                                            {{ faq.answer }}
                                        </div>
                                        <div v-if="faq.additionalInfo" class="mt-4 p-4 bg-emerald-50 rounded-xl border border-emerald-100">
                                            <div class="flex items-start">
                                                <svg class="w-5 h-5 text-emerald-600 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <div class="text-sm text-gray-700">{{ faq.additionalInfo }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </transition>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Still Need Help Section -->
        <section class="py-24 bg-gradient-to-br from-slate-50 to-emerald-50">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-4xl font-bold text-gray-900 mb-6">
                    Still Need
                    <span class="block text-emerald-700 bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                        Help?
                    </span>
                </h2>
                <p class="text-xl text-gray-800 mb-12 max-w-2xl mx-auto">
                    Can't find what you're looking for? Our Nigerian support team is here to help you.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Contact Support -->
                    <div class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-xl transition-all duration-500 text-center group">
                        <div class="w-16 h-16 mx-auto mb-6 rounded-2xl bg-emerald-100 flex items-center justify-center text-emerald-600 text-2xl transform transition-transform group-hover:scale-110">
                            ✉️
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Email Support</h3>
                        <p class="text-gray-700 mb-6 leading-relaxed">
                            Get personalized assistance from our Nigerian support team
                        </p>
                        <a
                            :href="`mailto:hello@olilearn.com`"
                            class="inline-flex items-center text-emerald-700 hover:text-emerald-800 font-semibold transition-colors group"
                        >
                            hello@olilearn.com
                            <svg class="ml-2 w-4 h-4 transform transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </a>
                    </div>

                    <!-- Live Chat -->
                    <div class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-xl transition-all duration-500 text-center group">
                        <div class="w-16 h-16 mx-auto mb-6 rounded-2xl bg-emerald-100 flex items-center justify-center text-emerald-600 text-2xl transform transition-transform group-hover:scale-110">
                            💬
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Live Chat</h3>
                        <p class="text-gray-700 mb-6 leading-relaxed">
                            Chat with our support team during business hours (9AM-6PM WAT)
                        </p>
                        <button
                            @click="startLiveChat"
                            class="inline-flex items-center bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-semibold px-6 py-3 rounded-xl transition-all duration-300 transform hover:scale-105"
                        >
                            Start Chat
                            <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Community -->
                    <div class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-xl transition-all duration-500 text-center group">
                        <div class="w-16 h-16 mx-auto mb-6 rounded-2xl bg-emerald-100 flex items-center justify-center text-emerald-600 text-2xl transform transition-transform group-hover:scale-110">
                            👥
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Community Forum</h3>
                        <p class="text-gray-700 mb-6 leading-relaxed">
                            Connect with other learners and get peer support
                        </p>
                        <Link
                            :href="route('community.index')"
                            class="inline-flex items-center text-emerald-700 hover:text-emerald-800 font-semibold transition-colors group"
                        >
                            Join Community
                            <svg class="ml-2 w-4 h-4 transform transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- Quick Tips -->
        <section class="py-24 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <div class="inline-flex items-center px-4 py-2 rounded-full bg-emerald-100 border border-emerald-200 text-emerald-700 text-sm font-medium mb-4">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></span>
                        Quick Tips
                    </div>
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">
                        GETTING THE MOST FROM
                        <span class="block text-emerald-700 bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                            OLLILEARN
                        </span>
                    </h2>
                    <p class="text-xl text-gray-800 max-w-2xl mx-auto">
                        Useful tips to enhance your AI-powered learning experience
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div
                        v-for="tip in tips"
                        :key="tip.id"
                        class="bg-gradient-to-br from-emerald-50 to-white rounded-2xl p-8 border border-emerald-100 hover:border-emerald-200 transition-all duration-300 hover:shadow-xl group"
                    >
                        <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 text-xl mb-6 transform transition-transform group-hover:scale-110">
                            {{ tip.icon }}
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">{{ tip.title }}</h3>
                        <p class="text-gray-700 leading-relaxed">{{ tip.description }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-24 bg-gradient-to-br from-emerald-600 to-teal-600 text-white">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-4xl font-bold mb-6">
                    Ready to Start Your
                    <span class="block bg-gradient-to-r from-white to-emerald-200 bg-clip-text text-transparent">
                        Learning Journey?
                    </span>
                </h2>
                <p class="text-xl mb-12 text-emerald-100">
                    Join thousands of Nigerian students already mastering subjects with AI
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <Link
                        :href="route('register')"
                        class="group bg-white text-emerald-700 px-8 py-4 rounded-xl font-semibold text-lg transform transition-all duration-300 hover:scale-105 hover:shadow-2xl"
                    >
                        <span class="relative flex items-center">
                            Start Learning Free
                            <svg class="ml-2 w-5 h-5 transform transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </span>
                    </Link>
                    <Link
                        :href="route('contact')"
                        class="group bg-transparent border-2 border-white text-white px-8 py-4 rounded-xl font-semibold text-lg transform transition-all duration-300 hover:scale-105 hover:bg-white/10"
                    >
                        <span class="relative flex items-center">
                            Contact Support
                            <svg class="ml-2 w-5 h-5 transform transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </span>
                    </Link>
                </div>
                <p class="text-sm text-emerald-200 mt-8">
                    Free 14-day trial • No credit card required • Cancel anytime
                </p>
            </div>
        </section>
    </AppLayout>
</template>

<script setup>
import { Link, Head } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import AppLayout from '@/Layouts/GuestLayout.vue';
import MetaTags from '@/Components/MetaTags.vue';

const searchQuery = ref('');
const activeCategory = ref('all');

// FAQ Data
const allFaqs = ref([
    // Getting Started
    {
        id: 1,
        question: 'How do I sign up for Olilearn in Nigeria?',
        answer: 'Simply visit our website and click "Start Learning Free". You can sign up using your email address or phone number. We support Nigerian numbers starting with +234. The signup process takes less than 2 minutes.',
        additionalInfo: 'Make sure to use a valid email address as we\'ll send you a verification link to activate your account.',
        category: 'getting-started',
        icon: '🚀',
        section: 'getting-started'
    },
    {
        id: 2,
        question: 'Is Olilearn free to use?',
        answer: 'Yes! We offer a free plan that includes basic AI tutoring features. You can create courses, ask questions, and track your progress at no cost. For advanced features like personalized learning paths and detailed analytics, we offer affordable premium plans.',
        additionalInfo: 'Our free plan includes 3 AI-generated courses per month and basic progress tracking.',
        category: 'getting-started',
        icon: '💰',
        section: 'getting-started'
    },
    {
        id: 3,
        question: 'What devices can I use Olilearn on?',
        answer: 'Olilearn works on any device with internet access. We have a mobile-responsive website that works perfectly on smartphones, tablets, laptops, and desktop computers. We optimize for Nigerian internet speeds, so you can learn even with limited bandwidth.',
        additionalInfo: 'For best experience, we recommend using Chrome, Firefox, or Safari browsers.',
        category: 'getting-started',
        icon: '📱',
        section: 'getting-started'
    },

    // For Students
    {
        id: 4,
        question: 'How does the AI tutor help me learn?',
        answer: 'Our AI tutor adapts to your learning style and pace. It provides instant explanations, generates practice questions, identifies knowledge gaps, and creates personalized learning paths. Think of it as having a personal tutor available 24/7 who understands Nigerian curriculum requirements.',
        additionalInfo: 'The more you interact with the AI tutor, the better it understands your learning needs.',
        category: 'students',
        icon: '🧠',
        section: 'students'
    },
    {
        id: 5,
        question: 'Can I use Olilearn for WAEC/JAMB preparation?',
        answer: 'Absolutely! We have specialized courses and practice materials for WAEC, JAMB, and other Nigerian exams. Our AI tutor can help you with past questions, exam strategies, and subject-specific preparation. Many Nigerian students have improved their scores using our platform.',
        additionalInfo: 'We update our exam materials regularly to match current syllabus requirements.',
        category: 'students',
        icon: '📚',
        section: 'students'
    },
    {
        id: 6,
        question: 'How do I track my learning progress?',
        answer: 'Olilearn provides detailed progress dashboards showing your completion rates, time spent learning, quiz scores, and skill improvements. You can view daily, weekly, and monthly progress reports. Our AI also identifies areas where you need more practice.',
        additionalInfo: 'Set daily learning goals and track your streaks to stay motivated!',
        category: 'students',
        icon: '📊',
        section: 'students'
    },

    // For Parents
    {
        id: 7,
        question: 'How can parents monitor their children\'s progress?',
        answer: 'Parents get access to a dedicated Parent Dashboard where you can view your child\'s learning activities, completed lessons, quiz scores, and time spent learning. You\'ll also receive weekly progress reports via email or WhatsApp.',
        additionalInfo: 'Parent accounts are free and can be linked to multiple student accounts.',
        category: 'parents',
        icon: '👨‍👩‍👧‍👦',
        section: 'parents'
    },
    {
        id: 8,
        question: 'Is Olilearn safe for children?',
        answer: 'Yes, safety is our priority. We have content filters, privacy protections, and parental controls. All AI interactions are monitored to ensure age-appropriate content. We comply with Nigerian data protection regulations.',
        additionalInfo: 'You can set screen time limits and learning schedules for your children.',
        category: 'parents',
        icon: '🛡️',
        section: 'parents'
    },
    {
        id: 9,
        question: 'Can I pay for my child\'s premium subscription?',
        answer: 'Yes! Parents can purchase premium subscriptions for their children. We accept Nigerian payment methods including bank transfers, debit cards, and mobile money. Educational discounts are available for multiple children.',
        additionalInfo: 'Contact our support team for family package information.',
        category: 'parents',
        icon: '💳',
        section: 'parents'
    },

    // For Tutors
    {
        id: 10,
        question: 'How can tutors use Olilearn?',
        answer: 'Tutors can create and manage courses for their students, track student progress, assign homework, and use AI tools to enhance their teaching. Our platform helps tutors save time on lesson planning and focus more on personalized instruction.',
        additionalInfo: 'We offer special tutor accounts with additional management features.',
        category: 'tutors',
        icon: '👨‍🏫',
        section: 'tutors'
    },
    {
        id: 11,
        question: 'Can I earn money as an Olilearn tutor?',
        answer: 'Yes! Qualified tutors can create and sell courses on our platform. We handle payments, marketing, and student management while you focus on teaching. You earn commission on course enrollments and can set your own pricing.',
        additionalInfo: 'Our tutor approval process ensures quality education standards.',
        category: 'tutors',
        icon: '💼',
        section: 'tutors'
    },
    {
        id: 12,
        question: 'How does Olilearn help tutors with Nigerian curriculum?',
        answer: 'We provide curriculum-aligned templates, WAEC/JAMB question banks, and Nigerian educational resources. Our AI can help you create lesson plans that match Nigerian educational standards and examination requirements.',
        additionalInfo: 'Join our tutor community to share resources and best practices.',
        category: 'tutors',
        icon: '🇳🇬',
        section: 'tutors'
    },

    // Payment & Pricing
    {
        id: 13,
        question: 'What payment methods do you accept in Nigeria?',
        answer: 'We accept all major Nigerian payment methods including: Bank transfers, Debit/Credit cards (Visa, Mastercard, Verve), Mobile money, and USSD payments. All transactions are secure and processed in Nigerian Naira.',
        additionalInfo: 'We offer educational discounts for schools and institutions.',
        category: 'payment',
        icon: '💵',
        section: 'payment'
    },
    {
        id: 14,
        question: 'Do you offer discounts for students?',
        answer: 'Yes! We offer student discounts for verified Nigerian students. You can get up to 50% off on premium plans. Just provide your student ID or school verification during signup. We also have special rates for university students.',
        additionalInfo: 'Contact support@olilearn.com with your student details for discount codes.',
        category: 'payment',
        icon: '🎓',
        section: 'payment'
    },
    {
        id: 15,
        question: 'Can I cancel my subscription anytime?',
        answer: 'Yes, you can cancel your premium subscription at any time. After cancellation, you\'ll continue to have access to premium features until the end of your billing period. No hidden fees or penalties.',
        additionalInfo: 'We offer a 30-day money-back guarantee if you\'re not satisfied.',
        category: 'payment',
        icon: '🔄',
        section: 'payment'
    },

    // Technical Support
    {
        id: 16,
        question: 'What if I have poor internet connection?',
        answer: 'Olilearn is optimized for Nigerian internet conditions. We use data compression and caching to work with limited bandwidth. You can also download course materials for offline learning and sync your progress when you\'re back online.',
        additionalInfo: 'Try using our "Lite Mode" for slower connections.',
        category: 'technical',
        icon: '🌐',
        section: 'technical'
    },
    {
        id: 17,
        question: 'How do I reset my password?',
        answer: 'Click "Forgot Password" on the login page. Enter your email address, and we\'ll send you a password reset link. If you used a phone number, you can request an SMS reset code. Reset links expire after 1 hour for security.',
        additionalInfo: 'Check your spam folder if you don\'t see our email.',
        category: 'technical',
        icon: '🔐',
        section: 'technical'
    },
    {
        id: 18,
        question: 'Is there an Olilearn mobile app?',
        answer: 'Yes! You can download the Olilearn app from Google Play Store (Android) and Apple App Store (iOS). The app offers all features of our website with additional offline capabilities and push notifications for reminders.',
        additionalInfo: 'Search "Olilearn" in your app store to download.',
        category: 'technical',
        icon: '📲',
        section: 'technical'
    },
]);

// Categories
const categories = ref([
    { id: 'all', name: 'All Questions', icon: '📋', count: 18 },
    { id: 'getting-started', name: 'Getting Started', icon: '🚀', count: 3 },
    { id: 'students', name: 'For Students', icon: '🎓', count: 3 },
    { id: 'parents', name: 'For Parents', icon: '👨‍👩‍👧‍👦', count: 3 },
    { id: 'tutors', name: 'For Tutors', icon: '👨‍🏫', count: 3 },
    { id: 'payment', name: 'Payment & Pricing', icon: '💰', count: 3 },
    { id: 'technical', name: 'Technical Support', icon: '🔧', count: 3 },
]);

// FAQ Sections
const faqSections = computed(() => {
    const sections = [
        {
            id: 'getting-started',
            title: 'Getting Started',
            description: 'Learn how to begin your AI-powered learning journey with Olilearn',
            icon: '🚀',
            faqs: filteredFaqs.value.filter(faq => faq.section === 'getting-started')
        },
        {
            id: 'students',
            title: 'For Students',
            description: 'Answers for Nigerian students using Olilearn for their studies',
            icon: '🎓',
            faqs: filteredFaqs.value.filter(faq => faq.section === 'students')
        },
        {
            id: 'parents',
            title: 'For Parents',
            description: 'Information for parents supporting their children\'s learning',
            icon: '👨‍👩‍👧‍👦',
            faqs: filteredFaqs.value.filter(faq => faq.section === 'parents')
        },
        {
            id: 'tutors',
            title: 'For Tutors',
            description: 'Resources for Nigerian educators using our platform',
            icon: '👨‍🏫',
            faqs: filteredFaqs.value.filter(faq => faq.section === 'tutors')
        },
        {
            id: 'payment',
            title: 'Payment & Pricing',
            description: 'Information about our plans and Nigerian payment options',
            icon: '💰',
            faqs: filteredFaqs.value.filter(faq => faq.section === 'payment')
        },
        {
            id: 'technical',
            title: 'Technical Support',
            description: 'Technical questions and troubleshooting for Nigerian users',
            icon: '🔧',
            faqs: filteredFaqs.value.filter(faq => faq.section === 'technical')
        }
    ];

    // Remove empty sections
    return sections.filter(section => section.faqs.length > 0);
});

// Quick Tips
const tips = ref([
    {
        id: 1,
        icon: '🎯',
        title: 'Set Daily Goals',
        description: 'Set achievable daily learning goals to build consistency. Even 30 minutes daily can lead to significant progress over time.'
    },
    {
        id: 2,
        icon: '💬',
        title: 'Ask Questions',
        description: 'Don\'t hesitate to ask your AI tutor questions. The more specific your questions, the better the AI can help you understand difficult concepts.'
    },
    {
        id: 3,
        icon: '📅',
        title: 'Use Reminders',
        description: 'Set up learning reminders to stay on track. Consistent practice is key to mastering any subject with our AI platform.'
    },
    {
        id: 4,
        icon: '📊',
        title: 'Track Progress',
        description: 'Regularly check your progress dashboard to identify strengths and areas that need more focus. Celebrate your learning milestones!'
    }
]);

// Filtered FAQs
const filteredFaqs = computed(() => {
    let filtered = allFaqs.value;

    // Apply search filter
    if (searchQuery.value.trim()) {
        const query = searchQuery.value.toLowerCase().trim();
        filtered = filtered.filter(faq =>
            faq.question.toLowerCase().includes(query) ||
            faq.answer.toLowerCase().includes(query)
        );
    }

    // Apply category filter
    if (activeCategory.value !== 'all') {
        filtered = filtered.filter(faq => faq.category === activeCategory.value);
    }

    return filtered;
});

// Methods
const toggleFaq = (faq) => {
    faq.open = !faq.open;
};

const clearSearch = () => {
    searchQuery.value = '';
    activeCategory.value = 'all';
};

const startLiveChat = () => {
    // In a real app, this would trigger your live chat widget
    alert('Live chat is available Monday-Friday, 9AM-6PM WAT. For now, please email us at hello@olilearn.com');
};

// Initialize all FAQs as closed
onMounted(() => {
    allFaqs.value.forEach(faq => {
        faq.open = false;
    });
});
</script>

<style scoped>
/* Reuse animations from other pages */
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

.animate-pulse-slow {
    animation: pulse-slow 4s ease-in-out infinite;
}

.animate-float {
    animation: float 6s ease-in-out infinite;
}

.animate-bounce-slow {
    animation: bounce-slow 3s infinite;
}

/* Smooth transitions */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}

/* FAQ card hover effects */
.group:hover .group-hover\:scale-110 {
    transform: scale(1.1);
}

/* Custom scrollbar for better UX */
@media (min-width: 768px) {
    ::-webkit-scrollbar {
        width: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb {
        background: #10b981;
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #059669;
    }
}

/* Accessibility focus styles */
button:focus, input:focus {
    outline: 2px solid #10b981;
    outline-offset: 2px;
}

/* Loading skeleton animation */
@keyframes shimmer {
    0% { background-position: -200px 0; }
    100% { background-position: calc(200px + 100%) 0; }
}

.animate-shimmer {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200px 100%;
    animation: shimmer 2s infinite;
}
</style>
