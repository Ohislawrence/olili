<template>
    <AppLayout>
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
                        Our Story
                    </div>
                    <h1 class="text-4xl md:text-6xl font-bold mb-6 leading-tight">
                        Revolutionizing
                        <span class="block bg-gradient-to-r from-white to-emerald-200 bg-clip-text text-transparent">
                            Education with AI
                        </span>
                    </h1>
                    <p class="text-xl md:text-2xl mb-12 text-emerald-100 max-w-3xl mx-auto">
                        We believe everyone deserves access to personalized, effective learning.
                        Olilearn combines cutting-edge artificial intelligence with proven educational methodologies
                        to make this vision a reality.
                    </p>
                </div>
            </div>
        </section>

        <!-- Mission & Vision -->
        <section class="py-24 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                    <!-- Mission -->
                    <div>
                        <div class="inline-flex items-center px-4 py-2 rounded-full bg-emerald-100 border border-emerald-200 text-emerald-700 text-sm font-medium mb-6">
                            <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></span>
                            Our Mission
                        </div>
                        <h2 class="text-4xl font-bold text-gray-900 mb-6">
                            Making Quality Education
                            <span class="block text-emerald-700 bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                                Accessible to All
                            </span>
                        </h2>
                        <p class="text-lg text-gray-700 mb-6 leading-relaxed">
                            We're on a mission to democratize education by leveraging artificial intelligence
                            to provide personalized learning experiences that adapt to each individual's needs,
                            pace, and learning style.
                        </p>
                        <p class="text-lg text-gray-700 leading-relaxed">
                            Our platform breaks down traditional barriers to education, making high-quality
                            learning accessible to students worldwide, regardless of their background, location,
                            or economic circumstances.
                        </p>
                    </div>

                    <!-- Vision -->
                    <div class="relative">
                        <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-8 text-white relative overflow-hidden shadow-lg">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-16 translate-x-16"></div>
                            <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full translate-y-12 -translate-x-12"></div>

                            <div class="relative z-10">
                                <div class="inline-flex items-center px-4 py-2 rounded-full bg-white/20 backdrop-blur-sm border border-white/30 text-white text-sm font-medium mb-6">
                                    Our Vision
                                </div>
                                <h3 class="text-2xl font-bold mb-4">A World Where Everyone Can Learn</h3>
                                <p class="text-emerald-100 mb-6">
                                    We envision a future where artificial intelligence empowers every learner
                                    to reach their full potential, transforming education from a one-size-fits-all
                                    model to a personalized journey of discovery and growth.
                                </p>
                                <div class="space-y-3">
                                    <div class="flex items-center text-emerald-100">
                                        <CheckCircleIcon class="h-5 w-5 mr-3" />
                                        Personalized learning paths for every student
                                    </div>
                                    <div class="flex items-center text-emerald-100">
                                        <CheckCircleIcon class="h-5 w-5 mr-3" />
                                        AI-powered tutoring available 24/7
                                    </div>
                                    <div class="flex items-center text-emerald-100">
                                        <CheckCircleIcon class="h-5 w-5 mr-3" />
                                        Global access to quality education
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Our Story -->
        <section class="py-24 bg-gradient-to-br from-slate-50 to-emerald-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">
                        OUR
                        <span class="block text-emerald-700 bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">JOURNEY</span>
                    </h2>
                    <p class="text-xl text-gray-800 max-w-2xl mx-auto">
                        From a simple idea to transforming how people learn around the world.
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div
                        v-for="milestone in milestones"
                        :key="milestone.year"
                        class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-xl transition-all duration-500 group text-center"
                    >
                        <div class="w-16 h-16 mx-auto mb-6 rounded-2xl bg-emerald-100 flex items-center justify-center text-emerald-600 text-2xl font-bold transform transition-transform group-hover:scale-110">
                            {{ milestone.year }}
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">{{ milestone.title }}</h3>
                        <p class="text-gray-700 leading-relaxed">{{ milestone.description }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Team Section -->
        <section class="py-24 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">
                        MEET OUR
                        <span class="block text-emerald-700 bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">TEAM</span>
                    </h2>
                    <p class="text-xl text-gray-800 max-w-2xl mx-auto">
                        Passionate educators, AI experts, and innovators working together to transform learning.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div
                        v-for="member in teamMembers"
                        :key="member.name"
                        class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-xl transition-all duration-500 group text-center"
                    >
                        <div class="w-24 h-24 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white text-2xl font-bold">
                            {{ member.initials }}
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ member.name }}</h3>
                        <p class="text-emerald-600 font-semibold mb-4">{{ member.role }}</p>
                        <p class="text-gray-700 text-sm mb-4">{{ member.bio }}</p>
                        <div class="flex justify-center space-x-3">
                            <a v-if="member.linkedin" :href="member.linkedin" class="text-gray-400 hover:text-emerald-600 transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"></path>
                                </svg>
                            </a>
                            <a v-if="member.twitter" :href="member.twitter" class="text-gray-400 hover:text-emerald-600 transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Values Section -->
        <section class="py-24 bg-gradient-to-br from-emerald-50 to-teal-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">
                        OUR
                        <span class="block text-emerald-700 bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">VALUES</span>
                    </h2>
                    <p class="text-xl text-gray-800 max-w-2xl mx-auto">
                        The principles that guide everything we do at Olilearn.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div
                        v-for="value in values"
                        :key="value.title"
                        class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-lg transition-all duration-500 group text-center"
                    >
                        <div class="w-16 h-16 mx-auto mb-6 rounded-2xl bg-emerald-100 flex items-center justify-center text-emerald-600 text-2xl transform transition-transform group-hover:scale-110">
                            {{ value.icon }}
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">{{ value.title }}</h3>
                        <p class="text-gray-700 leading-relaxed">{{ value.description }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Impact Stats -->
        <section class="py-24 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">
                        OUR
                        <span class="block text-emerald-700 bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">IMPACT</span>
                    </h2>
                    <p class="text-xl text-gray-800 max-w-2xl mx-auto">
                        Making a difference in the lives of learners worldwide.
                    </p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                    <div class="p-6 bg-white rounded-2xl border border-emerald-100 shadow-sm">
                        <div class="text-3xl md:text-4xl font-bold text-emerald-700 mb-2">50K+</div>
                        <div class="text-emerald-600 font-medium">Active Learners</div>
                    </div>
                    <div class="p-6 bg-white rounded-2xl border border-emerald-100 shadow-sm">
                        <div class="text-3xl md:text-4xl font-bold text-emerald-700 mb-2">98%</div>
                        <div class="text-emerald-600 font-medium">Success Rate</div>
                    </div>
                    <div class="p-6 bg-white rounded-2xl border border-emerald-100 shadow-sm">
                        <div class="text-3xl md:text-4xl font-bold text-emerald-700 mb-2">1.5M+</div>
                        <div class="text-emerald-600 font-medium">Learning Hours</div>
                    </div>
                    <div class="p-6 bg-white rounded-2xl border border-emerald-100 shadow-sm">
                        <div class="text-3xl md:text-4xl font-bold text-emerald-700 mb-2">25+</div>
                        <div class="text-emerald-600 font-medium">Countries</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Partners & Recognition -->
        <section class="py-24 bg-gradient-to-br from-slate-50 to-emerald-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">
                        TRUSTED
                        <span class="block text-emerald-700 bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">BY MANY</span>
                    </h2>
                    <p class="text-xl text-gray-800 max-w-2xl mx-auto">
                        We're proud to work with educational institutions and organizations worldwide.
                    </p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-8 items-center">
                    <div
                        v-for="partner in partners"
                        :key="partner.name"
                        class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-lg transition-all duration-300 flex items-center justify-center h-24"
                    >
                        <div class="text-3xl text-gray-400">{{ partner.logo }}</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-24 bg-gradient-to-br from-emerald-600 to-teal-600 text-white">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-4xl font-bold mb-6">Join Our Learning Revolution</h2>
                <p class="text-xl text-emerald-100 mb-8">
                    Be part of the movement that's transforming education through artificial intelligence.
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <Link
                        :href="route('register')"
                        class="inline-flex items-center justify-center bg-white text-emerald-700 px-8 py-4 rounded-xl font-bold hover:bg-emerald-50 transform transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-xl"
                    >
                        <AcademicCapIcon class="h-5 w-5 mr-2" />
                        Start Learning Free
                    </Link>
                    <Link
                        :href="route('contact')"
                        class="inline-flex items-center justify-center bg-transparent border-2 border-white text-white px-8 py-4 rounded-xl font-bold hover:bg-white/10 transform transition-all duration-300 hover:scale-105"
                    >
                        <ChatBubbleLeftRightIcon class="h-5 w-5 mr-2" />
                        Contact Us
                    </Link>
                </div>
            </div>
        </section>
    </AppLayout>
</template>

<script setup>
import { Link, Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/GuestLayout.vue';
import {
    AcademicCapIcon,
    ChatBubbleLeftRightIcon,
    CheckCircleIcon
} from '@heroicons/vue/24/outline';

const milestones = [
    {
        year: '2022',
        title: 'Foundation',
        description: 'Olilearn was founded by a team of educators and AI researchers with a shared vision for personalized learning.'
    },
    {
        year: '2023',
        title: 'Platform Launch',
        description: 'Launched our first AI-powered learning platform with adaptive courses and intelligent tutoring.'
    },
    {
        year: '2024',
        title: 'Global Expansion',
        description: 'Expanded to serve learners in 25+ countries with localized content and multilingual support.'
    }
];

const teamMembers = [
    {
        name: 'Dr. Sarah Chen',
        role: 'CEO & Co-Founder',
        initials: 'SC',
        bio: 'Former Stanford AI researcher with 10+ years in educational technology.',
        linkedin: '#',
        twitter: '#'
    },
    {
        name: 'Michael Rodriguez',
        role: 'CTO',
        initials: 'MR',
        bio: 'AI engineer specializing in machine learning and adaptive learning systems.',
        linkedin: '#',
        twitter: '#'
    },
    {
        name: 'Dr. Emily Watson',
        role: 'Head of Education',
        initials: 'EW',
        bio: 'Curriculum expert with PhD in Educational Psychology from Harvard.',
        linkedin: '#',
        twitter: '#'
    },
    {
        name: 'David Kim',
        role: 'Product Lead',
        initials: 'DK',
        bio: 'Product manager focused on creating intuitive learning experiences.',
        linkedin: '#',
        twitter: '#'
    }
];

const values = [
    {
        icon: '🎯',
        title: 'Personalization',
        description: 'We believe every learner is unique and deserves an educational experience tailored to their needs.'
    },
    {
        icon: '💡',
        title: 'Innovation',
        description: 'We continuously push the boundaries of what\'s possible with AI in education.'
    },
    {
        icon: '🌍',
        title: 'Accessibility',
        description: 'Quality education should be available to everyone, regardless of location or background.'
    },
    {
        icon: '🤝',
        title: 'Collaboration',
        description: 'We work together with educators, students, and institutions to create the best learning tools.'
    }
];

const partners = [
    { name: 'Stanford University', logo: '🎓' },
    { name: 'MIT', logo: '🔬' },
    { name: 'Google for Education', logo: '⚡' },
    { name: 'Microsoft Education', logo: '💻' },
    { name: 'UNESCO', logo: '🌐' },
    { name: 'Khan Academy', logo: '📚' }
];
</script>

<style scoped>
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
</style>
