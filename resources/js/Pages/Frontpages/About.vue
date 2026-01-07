<template>
    <MetaTags
        title="About Olilearn"
        description="Discover how Olilearn combines expert-created courses with AI-powered tutoring to deliver exceptional learning experiences for students worldwide."
        image="/images/olingolearn.png"
    />
    <AppLayout>
        <Head title="Expert Courses, AI Tutor - Your Complete Learning Solution" />

        <!-- Hero Section -->
        <section class="relative py-20 bg-gradient-to-br from-emerald-50 via-teal-50 to-white overflow-hidden">
            <!-- Background Elements -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute -top-40 -right-40 w-80 h-80 bg-emerald-100 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob"></div>
                <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-teal-100 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-2000"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="text-center">
                    <!-- Badge -->
                    <div class="inline-flex items-center px-4 py-2 rounded-full bg-emerald-100 border border-emerald-200 text-emerald-700 text-sm font-medium mb-8">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2 animate-pulse"></span>
                        Redefining Modern Education
                    </div>

                    <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6 leading-tight">
                        Expert Courses
                        <span class="block text-emerald-700 bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                            Meets AI-Powered Tutoring
                        </span>
                    </h1>

                    <p class="text-xl text-gray-600 mb-10 max-w-3xl mx-auto leading-relaxed">
                        At Olilearn, we combine professionally created courses with intelligent AI tutors
                        to provide students with a complete, effective learning experience that adapts to
                        their individual needs and pace.
                    </p>

                    <!-- Stats -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-2xl mx-auto">
                        <div class="text-center p-4 bg-white rounded-xl border border-gray-200 shadow-sm">
                            <div class="text-2xl font-bold text-emerald-700">100+</div>
                            <div class="text-sm text-gray-600">Expert Courses</div>
                        </div>
                        <div class="text-center p-4 bg-white rounded-xl border border-gray-200 shadow-sm">
                            <div class="text-2xl font-bold text-emerald-700">24/7</div>
                            <div class="text-sm text-gray-600">AI Tutor Support</div>
                        </div>
                        <div class="text-center p-4 bg-white rounded-xl border border-gray-200 shadow-sm">
                            <div class="text-2xl font-bold text-emerald-700">10K+</div>
                            <div class="text-sm text-gray-600">Active Students</div>
                        </div>
                        <div class="text-center p-4 bg-white rounded-xl border border-gray-200 shadow-sm">
                            <div class="text-2xl font-bold text-emerald-700">98%</div>
                            <div class="text-sm text-gray-600">Satisfaction Rate</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Our Model Section -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">
                        How Olilearn Works
                    </h2>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        A powerful combination of expert-curated content and intelligent AI assistance
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Expert Courses -->
                    <div class="bg-white rounded-2xl p-8 border border-gray-200 shadow-sm hover:shadow-lg transition-all duration-300">
                        <div class="w-16 h-16 bg-emerald-100 rounded-2xl flex items-center justify-center text-emerald-600 text-2xl mb-6">
                            📚
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Expert-Created Courses</h3>
                        <p class="text-gray-600 mb-4">
                            Our team of professional educators and subject matter experts create comprehensive,
                            structured courses designed to deliver maximum learning impact.
                        </p>
                        <ul class="space-y-2 text-gray-600">
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-emerald-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Professionally structured curriculum
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-emerald-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Quality-controlled content
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-emerald-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Regular updates and improvements
                            </li>
                        </ul>
                    </div>

                    <!-- AI Tutor -->
                    <div class="bg-white rounded-2xl p-8 border border-gray-200 shadow-sm hover:shadow-lg transition-all duration-300">
                        <div class="w-16 h-16 bg-emerald-100 rounded-2xl flex items-center justify-center text-emerald-600 text-2xl mb-6">
                            🤖
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Personal AI Tutor</h3>
                        <p class="text-gray-600 mb-4">
                            Every student gets their own AI tutor that provides personalized guidance,
                            answers questions, and helps reinforce learning 24/7.
                        </p>
                        <ul class="space-y-2 text-gray-600">
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-emerald-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Available anytime, anywhere
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-emerald-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Personalized learning support
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-emerald-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Instant answers to questions
                            </li>
                        </ul>
                    </div>

                    <!-- Student Experience -->
                    <div class="bg-white rounded-2xl p-8 border border-gray-200 shadow-sm hover:shadow-lg transition-all duration-300">
                        <div class="w-16 h-16 bg-emerald-100 rounded-2xl flex items-center justify-center text-emerald-600 text-2xl mb-6">
                            🎓
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Seamless Student Experience</h3>
                        <p class="text-gray-600 mb-4">
                            Students enroll in expertly crafted courses and receive personalized
                            AI tutoring support throughout their learning journey.
                        </p>
                        <ul class="space-y-2 text-gray-600">
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-emerald-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Enroll in curated courses
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-emerald-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Progress tracking and analytics
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-emerald-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Certificates of completion
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Mission & Vision -->
        <section class="py-16 bg-gradient-to-br from-emerald-50 to-teal-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <!-- Mission -->
                    <div>
                        <div class="inline-flex items-center px-4 py-2 rounded-full bg-emerald-100 border border-emerald-200 text-emerald-700 text-sm font-medium mb-6">
                            <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></span>
                            Our Mission
                        </div>
                        <h2 class="text-3xl font-bold text-gray-900 mb-6">
                            Combining Expertise with AI for
                            <span class="block text-emerald-700">
                                Optimal Learning Outcomes
                            </span>
                        </h2>
                        <p class="text-lg text-gray-700 mb-6 leading-relaxed">
                            We believe the best learning happens when expert knowledge meets personalized
                            support. That's why we've created a platform where professional educators
                            design comprehensive courses, and AI tutors provide individualized guidance
                            to each student.
                        </p>
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center text-emerald-600 mr-3 flex-shrink-0">
                                    ✓
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 mb-1">Expert-Curated Content</h4>
                                    <p class="text-gray-600 text-sm">Courses created by professional educators and subject matter experts</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center text-emerald-600 mr-3 flex-shrink-0">
                                    ✓
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 mb-1">Personalized AI Support</h4>
                                    <p class="text-gray-600 text-sm">24/7 AI tutoring that adapts to each student's learning style and pace</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Vision -->
                    <div class="relative">
                        <div class="bg-white rounded-2xl p-8 border border-gray-200 shadow-lg relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-50 rounded-full -translate-y-16 translate-x-16"></div>
                            <div class="absolute bottom-0 left-0 w-24 h-24 bg-teal-50 rounded-full translate-y-12 -translate-x-12"></div>

                            <div class="relative z-10">
                                <div class="inline-flex items-center px-4 py-2 rounded-full bg-emerald-100 border border-emerald-200 text-emerald-700 text-sm font-medium mb-6">
                                    Our Vision
                                </div>
                                <h3 class="text-2xl font-bold text-gray-900 mb-4">A World Where Every Student Has Access to Expert Education and Personal Guidance</h3>
                                <p class="text-gray-600 mb-6">
                                    We envision a future where quality education is enhanced by intelligent technology,
                                    creating a learning experience that's both structured and personalized,
                                    accessible to students everywhere.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Benefits Section -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">
                        Why Choose Olilearn?
                    </h2>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        The perfect balance of structured learning and personalized support
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Benefit 1 -->
                    <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm hover:shadow-lg transition-all duration-300">
                        <div class="w-12 h-12 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-xl flex items-center justify-center text-emerald-600 text-xl mb-4">
                            🎯
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-3">Quality-Checked Courses</h3>
                        <p class="text-gray-600 text-sm">
                            Every course is created and reviewed by experts to ensure accuracy,
                            relevance, and educational effectiveness.
                        </p>
                    </div>

                    <!-- Benefit 2 -->
                    <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm hover:shadow-lg transition-all duration-300">
                        <div class="w-12 h-12 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-xl flex items-center justify-center text-emerald-600 text-xl mb-4">
                            🤖
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-3">24/7 AI Tutor</h3>
                        <p class="text-gray-600 text-sm">
                            Get instant answers and personalized guidance whenever you need it,
                            making learning continuous and flexible.
                        </p>
                    </div>

                    <!-- Benefit 3 -->
                    <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm hover:shadow-lg transition-all duration-300">
                        <div class="w-12 h-12 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-xl flex items-center justify-center text-emerald-600 text-xl mb-4">
                            📈
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-3">Progress Tracking</h3>
                        <p class="text-gray-600 text-sm">
                            Monitor your learning journey with detailed analytics and insights
                            into your strengths and areas for improvement.
                        </p>
                    </div>

                    <!-- Benefit 4 -->
                    <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm hover:shadow-lg transition-all duration-300">
                        <div class="w-12 h-12 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-xl flex items-center justify-center text-emerald-600 text-xl mb-4">
                            🏆
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-3">Certification</h3>
                        <p class="text-gray-600 text-sm">
                            Earn recognized certificates upon course completion to showcase
                            your skills and knowledge.
                        </p>
                    </div>

                    <!-- Benefit 5 -->
                    <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm hover:shadow-lg transition-all duration-300">
                        <div class="w-12 h-12 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-xl flex items-center justify-center text-emerald-600 text-xl mb-4">
                            🔄
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-3">Regular Updates</h3>
                        <p class="text-gray-600 text-sm">
                            Courses are continuously updated with the latest information and
                            teaching methodologies.
                        </p>
                    </div>

                    <!-- Benefit 6 -->
                    <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm hover:shadow-lg transition-all duration-300">
                        <div class="w-12 h-12 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-xl flex items-center justify-center text-emerald-600 text-xl mb-4">
                            🌍
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-3">Global Community</h3>
                        <p class="text-gray-600 text-sm">
                            Join thousands of learners from around the world in a supportive
                            learning community.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Student Journey -->
        <section class="py-16 bg-gradient-to-br from-emerald-50 to-teal-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">
                        The Student Journey
                    </h2>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        From enrollment to mastery - your path to success
                    </p>
                </div>

                <div class="relative">
                    <!-- Timeline line -->
                    <div class="absolute left-4 md:left-1/2 transform md:-translate-x-1/2 h-full w-1 bg-emerald-200"></div>

                    <!-- Steps -->
                    <div class="space-y-8 relative">
                        <!-- Step 1 -->
                        <div class="flex flex-col md:flex-row items-center gap-6">
                            <div class="w-24 h-24 bg-white rounded-2xl border-4 border-emerald-100 flex items-center justify-center text-emerald-600 text-2xl shadow-lg z-10">
                                1️⃣
                            </div>
                            <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm md:w-1/2">
                                <h3 class="text-xl font-bold text-gray-900 mb-3">Browse & Enroll</h3>
                                <p class="text-gray-600">
                                    Explore our catalog of expert-created courses and enroll in the ones
                                    that match your learning goals and interests.
                                </p>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="flex flex-col md:flex-row-reverse items-center gap-6">
                            <div class="w-24 h-24 bg-white rounded-2xl border-4 border-emerald-100 flex items-center justify-center text-emerald-600 text-2xl shadow-lg z-10">
                                2️⃣
                            </div>
                            <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm md:w-1/2">
                                <h3 class="text-xl font-bold text-gray-900 mb-3">Learn with Structure</h3>
                                <p class="text-gray-600">
                                    Follow the professionally designed curriculum, complete modules,
                                    and master concepts through structured learning paths.
                                </p>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="flex flex-col md:flex-row items-center gap-6">
                            <div class="w-24 h-24 bg-white rounded-2xl border-4 border-emerald-100 flex items-center justify-center text-emerald-600 text-2xl shadow-lg z-10">
                                3️⃣
                            </div>
                            <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm md:w-1/2">
                                <h3 class="text-xl font-bold text-gray-900 mb-3">Get AI Tutor Support</h3>
                                <p class="text-gray-600">
                                    Use your personal AI tutor for instant help with difficult concepts,
                                    practice questions, and personalized guidance.
                                </p>
                            </div>
                        </div>

                        <!-- Step 4 -->
                        <div class="flex flex-col md:flex-row-reverse items-center gap-6">
                            <div class="w-24 h-24 bg-white rounded-2xl border-4 border-emerald-100 flex items-center justify-center text-emerald-600 text-2xl shadow-lg z-10">
                                4️⃣
                            </div>
                            <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm md:w-1/2">
                                <h3 class="text-xl font-bold text-gray-900 mb-3">Achieve & Earn</h3>
                                <p class="text-gray-600">
                                    Complete courses, earn certificates, and track your progress as you
                                    build new skills and knowledge.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-16 bg-white">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-3xl p-8 md:p-12 border border-emerald-100">
                    <div class="inline-flex items-center px-4 py-2 rounded-full bg-white border border-emerald-200 text-emerald-700 text-sm font-medium mb-6">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></span>
                        Start Your Learning Journey
                    </div>

                    <h2 class="text-3xl font-bold text-gray-900 mb-6">
                        Ready to Experience Expert Education with AI Support?
                    </h2>

                    <p class="text-lg text-gray-600 mb-8 max-w-2xl mx-auto">
                        Join thousands of students who are already benefiting from our unique
                        combination of professionally created courses and personalized AI tutoring.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <Link
                            :href="route('courses.index')"
                            class="inline-flex items-center justify-center bg-gradient-to-r from-emerald-600 to-teal-600 text-white px-8 py-4 rounded-xl font-bold hover:from-emerald-700 hover:to-teal-700 transition-all duration-300 shadow-lg hover:shadow-xl hover:-translate-y-0.5"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            Explore Courses
                        </Link>
                        <Link
                            :href="route('register')"
                            class="inline-flex items-center justify-center bg-white text-gray-700 px-8 py-4 rounded-xl font-bold hover:bg-gray-50 transition-all duration-300 border-2 border-gray-200 hover:border-gray-300"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                            Create Account
                        </Link>
                    </div>

                    <p class="text-sm text-gray-500 mt-6">
                        No credit card required • Start with free courses
                    </p>
                </div>
            </div>
        </section>
    </AppLayout>
</template>

<script setup>
import { Link, Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/GuestLayout.vue';
import MetaTags from '@/Components/MetaTags.vue';
</script>

<style scoped>
@keyframes blob {
    0% {
        transform: translate(0px, 0px) scale(1);
    }
    33% {
        transform: translate(30px, -50px) scale(1.1);
    }
    66% {
        transform: translate(-20px, 20px) scale(0.9);
    }
    100% {
        transform: translate(0px, 0px) scale(1);
    }
}

.animate-blob {
    animation: blob 7s infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

/* Timeline for mobile */
@media (max-width: 768px) {
    .relative > div:first-child {
        left: 2rem !important;
    }
}
</style>
