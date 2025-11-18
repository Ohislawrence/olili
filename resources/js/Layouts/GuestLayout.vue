<template>
    <div class="font-poppins bg-gray-50 min-h-screen">
        <!-- Header -->
        <header class="sticky top-0 z-50 bg-white/80 backdrop-blur-sm border-b border-gray-200/50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center space-x-2">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-gray-900">AITutor</span>
                    </div>

                    <nav class="hidden md:flex space-x-8">
                        <a href="#" class="text-gray-700 hover:text-emerald-600 font-medium transition-colors">Home</a>
                        <a href="#" class="text-gray-700 hover:text-emerald-600 font-medium transition-colors">Features</a>
                        <a href="#" class="text-gray-700 hover:text-emerald-600 font-medium transition-colors">Courses</a>
                        <a href="#" class="text-gray-700 hover:text-emerald-600 font-medium transition-colors">Community</a>
                        <a href="#" class="text-gray-700 hover:text-emerald-600 font-medium transition-colors">About</a>
                    </nav>

                    <div class="flex items-center space-x-4">
                        <Link :href="route('login')" class="px-4 py-2 rounded-lg font-medium text-gray-700 hover:bg-gray-100 transition">Sign In</Link>
                        <Link :href="route('register')" class="bg-gradient-to-r from-emerald-600 to-teal-600 text-white px-4 py-2 rounded-lg font-medium hover:from-emerald-500 hover:to-teal-500 transition">Get Started</Link>
                    </div>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="min-h-screen flex items-center relative overflow-hidden">
            <!-- Floating background elements -->
            <div class="absolute inset-0 z-0">
                <div class="floating-circle bg-emerald-500/10"></div>
                <div class="floating-circle bg-teal-500/10" style="animation-delay: 2s;"></div>
                <div class="floating-circle bg-cyan-500/10" style="animation-delay: 4s;"></div>
                <div class="floating-triangle bg-emerald-600/5"></div>
                <div class="floating-diamond bg-teal-600/5" style="animation-delay: 3s;"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 relative z-10 w-full">
                <div class="text-center">
                    <h1 class="text-5xl md:text-7xl font-bold mb-6 leading-tight animate-fade-in-up">
                        Learning Made
                        <span class="block bg-gradient-to-r from-emerald-600 via-teal-500 to-cyan-400 bg-clip-text text-transparent animate-slide-in-right">
                            Effortless & Smart
                        </span>
                    </h1>
                    <p class="text-xl md:text-2xl mb-12 text-gray-600 max-w-3xl mx-auto animate-fade-in-up" style="animation-delay: 0.3s;">
                        AITutor does more than help you find the right tutor.
                        We accompany you on your learning journey with artificial intelligence,
                        digital tools, and personalized follow-up.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-6 justify-center animate-fade-in-up" style="animation-delay: 0.6s;">
                        <a
                            href="#"
                            class="group bg-white/80 backdrop-blur-sm border border-emerald-200 text-emerald-700 px-8 py-4 rounded-2xl font-semibold text-lg hover:bg-emerald-50 transform transition-all duration-500 hover:scale-105 hover:shadow-xl hover:border-emerald-300"
                        >
                            <span class="relative">
                                I'm a Student
                                <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-emerald-500 transition-all duration-500 group-hover:w-full"></span>
                            </span>
                        </a>
                        <a
                            href="#"
                            class="group bg-gradient-to-r from-emerald-600/90 to-teal-600/90 backdrop-blur-sm text-white px-8 py-4 rounded-2xl font-semibold text-lg transform transition-all duration-500 hover:scale-105 hover:shadow-2xl hover:from-emerald-500 hover:to-teal-500"
                        >
                            <span class="relative">
                                I'm a Tutor
                                <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-white transition-all duration-500 group-hover:w-full"></span>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Solution Section -->
        <section class="py-24 relative">
            <div class="absolute top-0 left-1/2 transform -translate-x-1/2 w-24 h-24 rounded-full bg-gradient-radial-emerald opacity-20 animate-pulse-slow"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">
                        YOUR LEARNING JOURNEY
                        <span class="block bg-gradient-to-r from-emerald-600 to-teal-500 bg-clip-text text-transparent">PEACE OF MIND</span>
                    </h2>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                    <div class="animate-float-in">
                        <h3 class="text-3xl font-bold text-gray-900 mb-8">
                            A LEARNER'S<br>
                            <span class="bg-gradient-to-r from-emerald-600 to-teal-500 bg-clip-text text-transparent">JOURNEY</span><br>
                            NOT A BATTLE
                        </h3>
                        <p class="text-lg text-gray-600 mb-8 leading-relaxed">
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
                                <div class="flex-shrink-0 w-6 h-6 rounded-full bg-gradient-to-r from-emerald-500 to-teal-500 flex items-center justify-center text-white text-sm font-bold transform transition-transform group-hover:scale-110 animate-bounce-slow">
                                    {{ index + 1 }}
                                </div>
                                <div class="pt-0.5">
                                    <h4 class="font-semibold text-gray-900 group-hover:text-emerald-600 transition-colors">{{ benefit }}</h4>
                                </div>
                            </div>
                        </div>
                        <a
                            href="#"
                            class="inline-flex items-center mt-8 text-emerald-600 font-semibold hover:text-emerald-700 transition-colors group"
                        >
                            Discover the Concept
                            <svg class="ml-2 w-5 h-5 transform transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>
                    </div>

                    <div class="space-y-6">
                        <div
                            v-for="(feature, index) in features"
                            :key="feature.title"
                            class="backdrop-blur-sm bg-white/60 border border-gray-200/50 p-6 rounded-2xl shadow-sm hover:shadow-md transition-all duration-500 card-glow"
                            :style="{ 'animation-delay': index * 0.15 + 's' }"
                        >
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500/20 to-teal-500/20 flex items-center justify-center text-emerald-600">
                                    <span class="text-2xl transform transition-transform hover:scale-110">{{ feature.icon }}</span>
                                </div>
                                <div>
                                    <h4 class="text-xl font-semibold text-gray-900 mb-2 group-hover:text-emerald-600 transition-colors">{{ feature.title }}</h4>
                                    <p class="text-gray-600 text-sm leading-relaxed">{{ feature.description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works Section -->
        <section class="py-24 relative">
            <div class="absolute top-0 right-0 w-32 h-32 rounded-full bg-gradient-radial-teal opacity-10 animate-pulse-slow" style="animation-delay: 1s;"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">
                        HOW IT
                        <span class="block bg-gradient-to-r from-emerald-600 to-teal-500 bg-clip-text text-transparent">WORKS</span>
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
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
                            <div class="w-16 h-16 rounded-full bg-gradient-to-r from-emerald-500 to-teal-500 flex items-center justify-center text-white text-xl font-bold mx-auto mb-4 transform transition-transform group-hover:scale-110 animate-step-pop">
                                {{ step.number }}
                            </div>
                            <div v-if="index < 2" class="hidden md:block absolute top-8 left-40 w-32 h-0.5 bg-gradient-to-r from-emerald-300 to-teal-300 transform -translate-y-1/2"></div>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">{{ step.title }}</h3>
                        <p class="text-gray-600 leading-relaxed">{{ step.description }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured Courses -->
        <section class="py-24 relative">
            <div class="absolute bottom-0 left-0 w-40 h-40 rounded-full bg-gradient-radial-cyan opacity-10 animate-pulse-slow" style="animation-delay: 2s;"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">
                        FEATURED
                        <span class="block bg-gradient-to-r from-emerald-600 to-teal-500 bg-clip-text text-transparent">COURSES</span>
                    </h2>
                    <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                        Explore our most popular courses designed with AI personalization at their core.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div
                        v-for="(course, index) in featuredCourses"
                        :key="course.id"
                        class="backdrop-blur-sm bg-white/60 border border-gray-200/50 rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-500 course-card"
                        :style="{ 'animation-delay': index * 0.15 + 's' }"
                    >
                        <div class="h-48 relative overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-r from-emerald-500/20 to-teal-500/20"></div>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="text-center p-4 transform transition-transform hover:scale-105">
                                    <span class="inline-block px-3 py-1 rounded-full text-sm bg-white/20 backdrop-blur-sm border border-white/30">
                                        {{ course.category }}
                                    </span>
                                    <h3 class="text-2xl font-bold mt-4 text-white">{{ course.title }}</h3>
                                </div>
                            </div>
                            <div class="course-glow"></div>
                        </div>
                        <div class="p-6">
                            <p class="text-gray-600 mb-6 leading-relaxed">{{ course.description }}</p>
                            <div class="flex justify-between items-center">
                                <div class="flex items-center text-emerald-600 font-semibold">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ course.duration }}
                                </div>
                                <a
                                    href="#"
                                    class="text-emerald-600 hover:text-emerald-700 font-medium transition-colors group"
                                >
                                    View Course
                                    <svg class="inline-block ml-1 w-4 h-4 transform transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-12">
                    <a
                        href="#"
                        class="inline-flex items-center text-gray-700 hover:text-emerald-600 font-semibold transition-colors group"
                    >
                        Discover All Our Courses
                        <svg class="ml-2 w-5 h-5 transform transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </section>

        <!-- Community Section -->
        <section class="py-24 relative">
            <div class="absolute top-1/2 left-0 w-32 h-32 rounded-full bg-gradient-radial-emerald opacity-10 animate-pulse-slow" style="animation-delay: 3s;"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                    <div class="animate-float-in">
                        <h2 class="text-4xl font-bold text-gray-900 mb-8">
                            A Community of<br>
                            <span class="bg-gradient-to-r from-emerald-600 to-teal-500 bg-clip-text text-transparent">Passionate Learners</span>
                        </h2>
                        <p class="text-lg text-gray-600 mb-8 leading-relaxed">
                            AITutor is about sharing best practices and creating connections because you should know much earlier to make the right learning choices.
                        </p>

                        <div class="space-y-6">
                            <div
                                v-for="topic in communityTopics"
                                :key="topic.title"
                                class="flex items-start space-x-4 group cursor-pointer"
                            >
                                <div class="flex-shrink-0 w-3 h-3 rounded-full bg-emerald-400 mt-2 group-hover:scale-150 transition-transform"></div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 group-hover:text-emerald-600 transition-colors">
                                        {{ topic.title }}
                                    </h4>
                                    <p class="text-gray-500 text-sm">{{ topic.date }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex items-center text-sm text-gray-500">
                            <span class="mr-2">Active members:</span>
                            <div class="flex -space-x-2">
                                <img v-for="n in 5" :key="n" src="https://placehold.co/32x32/6ee7b7/ffffff?text=U" alt="User" class="w-8 h-8 rounded-full border-2 border-white">
                            </div>
                            <span class="ml-2 text-emerald-600 font-semibold">+12.5K</span>
                        </div>
                    </div>

                    <div class="relative">
                        <div class="backdrop-blur-sm bg-gradient-to-br from-emerald-600/20 to-teal-600/20 border border-emerald-200/50 rounded-2xl p-8 relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-32 h-32 rounded-full bg-white/10 -translate-y-16 translate-x-16"></div>
                            <div class="absolute bottom-0 left-0 w-24 h-24 rounded-full bg-white/10 translate-y-12 -translate-x-12"></div>

                            <div class="relative z-10">
                                <h3 class="text-2xl font-bold mb-6 text-gray-900">Join the Community</h3>
                                <p class="mb-8 text-gray-700 leading-relaxed">
                                    Exchange with other learners, share your discoveries and progress together in a supportive environment where everyone grows.
                                </p>
                                <a
                                    href="#"
                                    class="inline-flex items-center bg-white/80 backdrop-blur-sm text-emerald-700 px-6 py-3 rounded-xl font-semibold hover:bg-emerald-50 transform transition-all duration-500 hover:scale-105 group"
                                >
                                    <span class="relative">
                                        Join Us
                                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-emerald-500 transition-all duration-500 group-hover:w-full"></span>
                                    </span>
                                    <svg class="ml-2 w-5 h-5 transform transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>

                        <!-- Floating interactive elements -->
                        <div class="absolute -top-6 -right-6 w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-500/20 to-teal-500/20 border border-emerald-300/50 flex items-center justify-center animate-float-slow hover:scale-110 transition-transform cursor-pointer">
                            <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>
                            </svg>
                        </div>
                        <div class="absolute -bottom-4 -left-4 w-12 h-12 rounded-full bg-gradient-to-br from-cyan-400/30 to-teal-400/30 border border-cyan-300/50 flex items-center justify-center animate-float-slow" style="animation-delay: 1s;">
                            <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section class="py-24 relative">
            <div class="absolute top-0 right-1/4 w-20 h-20 rounded-full bg-gradient-radial-teal opacity-10 animate-pulse-slow" style="animation-delay: 4s;"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">
                        WHAT OUR
                        <span class="block bg-gradient-to-r from-emerald-600 to-teal-500 bg-clip-text text-transparent">LEARNERS SAY</span>
                    </h2>
                    <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                        Real stories from students who transformed their learning experience.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div
                        v-for="(testimonial, index) in testimonials"
                        :key="index"
                        class="backdrop-blur-sm bg-white/60 border border-gray-200/50 p-8 rounded-2xl shadow-sm hover:shadow-lg transition-all duration-500 testimonial-card"
                        :style="{ 'animation-delay': index * 0.2 + 's' }"
                    >
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 rounded-full overflow-hidden mr-4">
                                <img :src="testimonial.avatar" alt="Avatar" class="w-full h-full object-cover">
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900">{{ testimonial.name }}</h4>
                                <p class="text-sm text-gray-500">{{ testimonial.role }}</p>
                            </div>
                        </div>
                        <p class="text-gray-700 italic leading-relaxed">"{{ testimonial.quote }}"</p>
                        <div class="flex mt-4 text-emerald-500">
                            <svg v-for="n in 5" :key="n" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-24 relative">
            <div class="absolute inset-0 z-0">
                <div class="w-64 h-64 rounded-full bg-gradient-radial-emerald opacity-10 absolute top-1/4 left-1/4 animate-pulse-slow"></div>
                <div class="w-48 h-48 rounded-full bg-gradient-radial-teal opacity-10 absolute bottom-1/4 right-1/4 animate-pulse-slow" style="animation-delay: 2s;"></div>
            </div>

            <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8 relative z-10">
                <h2 class="text-4xl font-bold text-gray-900 mb-6">
                    Ready to Transform<br>
                    <span class="bg-gradient-to-r from-emerald-600 to-teal-500 bg-clip-text text-transparent">Your Learning Experience?</span>
                </h2>
                <p class="text-xl mb-12 text-gray-600">
                    We're available to discuss your learning goals and ambitions
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <a
                        href="#"
                        class="group bg-white/80 backdrop-blur-sm border border-emerald-200 text-emerald-700 px-8 py-4 rounded-2xl font-semibold text-lg transform transition-all duration-500 hover:scale-105 hover:shadow-xl hover:border-emerald-300"
                    >
                        <span class="relative">
                            Contact Us
                            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-emerald-500 transition-all duration-500 group-hover:w-full"></span>
                        </span>
                    </a>
                    <a
                        href="#"
                        class="group bg-gradient-to-r from-emerald-600 to-teal-600 text-white px-8 py-4 rounded-2xl font-semibold text-lg transform transition-all duration-500 hover:scale-105 hover:shadow-2xl"
                    >
                        <span class="relative">
                            Request a Demo
                            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-white/50 transition-all duration-500 group-hover:w-full"></span>
                        </span>
                    </a>
                </div>

                <div class="mt-12 flex items-center justify-center text-sm text-gray-500">
                    <svg class="w-5 h-5 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Free 14-day trial • No credit card required • Cancel anytime</span>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="py-16 border-t border-gray-200/50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                    <div v-for="(stat, index) in stats" :key="index" class="stat-item">
                        <div class="text-3xl md:text-4xl font-bold text-gray-900 mb-2 animate-count-up" :data-target="stat.value">{{ stat.value }}</div>
                        <div class="text-gray-600 font-medium">{{ stat.label }}</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white py-16 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div>
                        <div class="flex items-center space-x-2 mb-4">
                            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                </svg>
                            </div>
                            <span class="text-xl font-bold">AITutor</span>
                        </div>
                        <p class="text-gray-400 mb-4">
                            Your intelligent learning companion. Learn smarter, not harder.
                        </p>
                        <div class="flex space-x-4">
                            <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-emerald-600 transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"></path>
                                </svg>
                            </a>
                            <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-emerald-600 transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"></path>
                                </svg>
                            </a>
                            <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-emerald-600 transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                        <ul class="space-y-2">
                            <li><a href="#" class="text-gray-400 hover:text-emerald-400 transition">Home</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-emerald-400 transition">Courses</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-emerald-400 transition">Features</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-emerald-400 transition">Pricing</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-emerald-400 transition">Blog</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Support</h3>
                        <ul class="space-y-2">
                            <li><a href="#" class="text-gray-400 hover:text-emerald-400 transition">Help Center</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-emerald-400 transition">Community</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-emerald-400 transition">Contact Us</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-emerald-400 transition">FAQ</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Subscribe</h3>
                        <p class="text-gray-400 mb-4">Get the latest news and updates</p>
                        <div class="flex">
                            <input type="email" placeholder="Your email" class="px-4 py-2 rounded-l-lg w-full text-gray-800 focus:outline-none">
                            <button class="bg-emerald-600 hover:bg-emerald-500 px-4 py-2 rounded-r-lg transition">→</button>
                        </div>
                    </div>
                </div>
                <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-500">
                    <p>© 2025 AITutor. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
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
        avatar: 'https://placehold.co/100x100/6ee7b7/ffffff?text=SJ'
    },
    {
        name: 'Michael Chen',
        role: 'Working Professional',
        quote: 'As someone balancing work and learning, AITutor\'s flexibility and smart scheduling have been game-changing for my career growth.',
        avatar: 'https://placehold.co/100x100/2dd4bf/ffffff?text=MC'
    },
    {
        name: 'Emma Rodriguez',
        role: 'High School Teacher',
        quote: 'I use AITutor to supplement my classroom teaching. My students love the interactive approach and their test scores have improved significantly.',
        avatar: 'https://placehold.co/100x100/14b8a6/ffffff?text=ER'
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
/* Gradient Definitions */
.bg-gradient-radial-emerald {
    background: radial-gradient(circle, rgba(16, 185, 129, 0.3) 0%, transparent 70%);
}

.bg-gradient-radial-teal {
    background: radial-gradient(circle, rgba(20, 183, 167, 0.3) 0%, transparent 70%);
}

.bg-gradient-radial-cyan {
    background: radial-gradient(circle, rgba(14, 165, 233, 0.3) 0%, transparent 70%);
}

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

.floating-triangle {
    position: absolute;
    width: 100px;
    height: 100px;
    clip-path: polygon(50% 0%, 0% 100%, 100% 100%);
    top: 20%;
    right: 10%;
    animation: float 12s ease-in-out infinite;
}

.floating-diamond {
    position: absolute;
    width: 70px;
    height: 70px;
    clip-path: polygon(50% 0%, 100% 50%, 50% 100%, 0% 50%);
    bottom: 15%;
    left: 15%;
    animation: float 10s ease-in-out infinite;
}

/* Animation Keyframes */
@keyframes float {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    50% { transform: translateY(-30px) rotate(5deg); }
}

@keyframes pulse-slow {
    0%, 100% { opacity: 0.3; transform: scale(1); }
    50% { opacity: 0.6; transform: scale(1.05); }
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

.animate-pulse-slow {
    animation: pulse-slow 4s ease-in-out infinite;
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
.card-glow {
    position: relative;
    overflow: hidden;
}

.card-glow::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(16, 185, 129, 0.1) 0, transparent 70%);
    transform: scale(0);
    opacity: 0;
    transition: transform 0.5s, opacity 0.5s;
    pointer-events: none;
}

.card-glow:hover::before {
    transform: scale(1);
    opacity: 1;
}

.course-card .course-glow {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(16, 185, 129, 0.2), rgba(20, 183, 167, 0.2));
    opacity: 0;
    transition: opacity 0.3s;
}

.course-card:hover .course-glow {
    opacity: 1;
}

.testimonial-card {
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.testimonial-card:hover {
    transform: translateY(-8px) scale(1.02);
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
