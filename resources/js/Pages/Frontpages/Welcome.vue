<template>
    <AppLayout>
        <Head title="Learning Made Effortless & Smart" />
        <!-- Hero Section -->
        <section class="min-h-screen flex items-center relative overflow-hidden bg-gradient-to-br from-white to-emerald-50">
            <!-- Enhanced Background pattern -->
            <div class="absolute inset-0 z-0 opacity-10">
                <div class="absolute top-20 left-10 w-40 h-40 bg-emerald-500 rounded-full animate-pulse-slow"></div>
                <div class="absolute top-40 right-20 w-60 h-60 bg-teal-500 rounded-full animate-float"></div>
                <div class="absolute bottom-20 left-1/4 w-32 h-32 bg-cyan-500 rounded-full animate-bounce-slow"></div>
                <div class="absolute top-1/2 right-1/3 w-24 h-24 bg-violet-400 rounded-full animate-ping-slow"></div>
            </div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 relative z-10 w-full">
                <div class="text-center">
                    <div class="inline-flex items-center px-4 py-2 rounded-full bg-emerald-100 border border-emerald-200 text-emerald-700 text-sm font-medium mb-8 animate-fade-in">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2 animate-pulse"></span>
                        AI-Powered Learning Platform
                    </div>
                    <h1 class="text-5xl md:text-7xl font-bold mb-6 leading-tight animate-fade-in-up">
                        Learning Made
                        <span class="block text-emerald-700 animate-slide-in-right bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                            Effortless & Smart
                        </span>
                    </h1>
                    <p class="text-xl md:text-2xl mb-12 text-gray-800 max-w-3xl mx-auto animate-fade-in-up" style="animation-delay: 0.3s;">
                        Olilearn does more than help you find the right tutor.
                        We accompany you on your learning journey with artificial intelligence,
                        digital tools, and personalized follow-up.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-6 justify-center animate-fade-in-up" style="animation-delay: 0.6s;">
                        <Link
                            :href="route('register')"
                            class="group bg-white border-2 border-emerald-600 text-emerald-700 px-8 py-4 rounded-xl font-semibold text-lg hover:bg-emerald-50 transform transition-all duration-300 hover:scale-105 hover:shadow-xl shadow-emerald-200/50"
                        >
                            <span class="relative flex items-center">
                                <svg class="w-5 h-5 mr-3 group-hover:animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v6l9-5-9-5-9 5 9 5z"></path>
                                </svg>
                                I'm a Student
                                <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-emerald-600 transition-all duration-300 group-hover:w-full"></span>
                            </span>
                        </Link>
                        <Link
                            :href="route('register')"
                            class="group bg-gradient-to-r from-emerald-600 to-teal-600 text-white px-8 py-4 rounded-xl font-semibold text-lg transform transition-all duration-300 hover:scale-105 hover:shadow-xl shadow-emerald-500/25"
                        >
                            <span class="relative flex items-center">
                                <svg class="w-5 h-5 mr-3 group-hover:animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                I'm a Tutor
                                <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-white transition-all duration-300 group-hover:w-full"></span>
                            </span>
                        </Link>
                    </div>
                    <!-- Trust indicators -->
                    <div class="mt-16 animate-fade-in-up" style="animation-delay: 0.9s;">
                        <p class="text-gray-600 mb-6">Trusted by 50,000+ learners worldwide</p>
                        <div class="flex flex-wrap justify-center items-center gap-8 opacity-60">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-emerald-500 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <span class="text-sm font-medium text-gray-700">98% Success Rate</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                </div>
                                <span class="text-sm font-medium text-gray-700">2x Faster Learning</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                </div>
                                <span class="text-sm font-medium text-gray-700">4.8/5 Rating</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- AI Features Section -->
        <section class="py-24 relative bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <div class="inline-flex items-center px-4 py-2 rounded-full bg-emerald-100 border border-emerald-200 text-emerald-700 text-sm font-medium mb-4">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></span>
                        AI-Powered Features
                    </div>
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">
                        SMART LEARNING
                        <span class="block text-emerald-700 bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">EXPERIENCE</span>
                    </h2>
                    <p class="text-xl text-gray-800 max-w-2xl mx-auto">
                        Harness the power of artificial intelligence to transform your learning journey.
                    </p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div
                        v-for="(feature, index) in aiFeatures"
                        :key="feature.title"
                        class="text-center group p-6 rounded-2xl hover:bg-gradient-to-br hover:from-emerald-50 hover:to-teal-50 transition-all duration-500"
                        :style="{ 'animation-delay': index * 0.1 + 's' }"
                    >
                        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white text-2xl transform transition-transform group-hover:scale-110 group-hover:rotate-3">
                            {{ feature.icon }}
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">{{ feature.title }}</h3>
                        <p class="text-gray-700 leading-relaxed">{{ feature.description }}</p>
                    </div>
                </div>
            </div>
        </section>
        <!-- Enhanced Featured Courses Section -->
        <section class="py-24 relative bg-gradient-to-b from-slate-50 to-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <div class="inline-flex items-center px-4 py-2 rounded-full bg-emerald-100 border border-emerald-200 text-emerald-700 text-sm font-medium mb-4">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></span>
                        Featured Learning Paths
                    </div>
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">
                        FEATURED
                        <span class="block text-emerald-700 bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">COURSES</span>
                    </h2>
                    <p class="text-xl text-gray-800 max-w-2xl mx-auto">
                        Explore our most popular courses designed with AI personalization at their core.
                    </p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div
                        v-for="(course, index) in courses"
                        :key="course.id"
                        class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-500 course-card group"
                        :style="{ 'animation-delay': index * 0.15 + 's' }"
                    >
                        <div class="h-48 relative overflow-hidden bg-gradient-to-br from-emerald-500 to-teal-600">
                            <div class="absolute inset-0 bg-black/20 group-hover:bg-black/10 transition-all duration-300"></div>
                            <div class="absolute inset-0 flex items-center justify-center p-6">
                                <div class="text-center transform transition-transform group-hover:scale-105 duration-300">
                                    <span class="inline-block px-4 py-2 rounded-full text-sm bg-white/20 backdrop-blur-sm border border-white/30 text-white font-medium mb-3">
                                        {{ course.subject }}
                                    </span>
                                    <h3 class="text-2xl font-bold text-white leading-tight">{{ course.title }}</h3>
                                    <p class="text-white/90 text-sm mt-2">{{ course.level }}</p>
                                </div>
                            </div>
                            <div class="absolute top-4 right-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs bg-white/20 backdrop-blur-sm text-white border border-white/30">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ course.progress_percentage || 0 }}%
                                </span>
                            </div>
                        </div>
                        <div class="p-6">
                            <p class="text-gray-700 mb-6 leading-relaxed line-clamp-3">{{ course.description }}</p>
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center text-emerald-700 font-semibold text-sm">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ course.estimated_duration_hours || 'N/A' }} Hours
                                </div>
                                <div class="flex items-center text-gray-600 text-sm">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    {{ course.modules_count || 0 }} Modules
                                </div>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 mb-4">
                                <div
                                    class="bg-gradient-to-r from-emerald-500 to-teal-600 h-2 rounded-full transition-all duration-500 ease-out"
                                    :style="{ width: (course.progress_percentage || 0) + '%' }"
                                ></div>
                            </div>
                            <Link
                                :href="route('courses.show', course.id)"
                                class="w-full inline-flex items-center justify-center bg-emerald-50 text-emerald-700 px-6 py-3 rounded-xl font-semibold hover:bg-emerald-100 transform transition-all duration-300 group-hover:scale-105 border border-emerald-200"
                            >
                                <span class="flex items-center">
                                    View Course
                                    <svg class="ml-2 w-4 h-4 transform transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                    </svg>
                                </span>
                            </Link>
                        </div>
                    </div>
                </div>
                <div v-if="courses.length === 0" class="text-center py-12">
                    <div class="w-24 h-24 mx-auto mb-4 text-gray-400">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 14v6l9-5-9-5-9 5 9 5z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">No Courses Available</h3>
                    <p class="text-gray-500">Check back later for new learning opportunities.</p>
                </div>
                <div class="text-center mt-12">
                    <Link
                        :href="route('courses.index')"
                        class="inline-flex items-center bg-white border-2 border-emerald-600 text-emerald-700 px-8 py-4 rounded-xl font-semibold text-lg hover:bg-emerald-50 transform transition-all duration-300 hover:scale-105 hover:shadow-lg group"
                    >
                        <span class="relative">
                            Discover All Our Courses
                            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-emerald-600 transition-all duration-300 group-hover:w-full"></span>
                        </span>
                        <svg class="ml-2 w-5 h-5 transform transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </Link>
                </div>
            </div>
        </section>
        <!-- Pricing Section -->
        <PricingPlans
            :plans="subscriptionPlans"
            :show-role-selector="true"
            :show-enterprise="true"
            section-id="pricing"
            header-badge="Simple Pricing"
            header-title="AFFORDABLE"
            header-subtitle="PLANS FOR EVERYONE"
            header-description="Choose the perfect plan for your learning needs. All plans include our core AI features."
        />
        <!-- How It Works Section -->
        <section class="py-24 relative bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <div class="inline-flex items-center px-4 py-2 rounded-full bg-emerald-100 border border-emerald-200 text-emerald-700 text-sm font-medium mb-4">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></span>
                        Simple Process
                    </div>
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">
                        HOW IT
                        <span class="block text-emerald-700 bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">WORKS</span>
                    </h2>
                    <p class="text-xl text-gray-800 max-w-2xl mx-auto">
                        Get started with Olilearn in just three simple steps.
                    </p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div
                        v-for="(step, index) in howItWorks"
                        :key="index"
                        class="text-center group p-8 rounded-2xl hover:bg-gradient-to-br hover:from-emerald-50 hover:to-teal-50 transition-all duration-500"
                    >
                        <div class="relative inline-block mb-6">
                            <div class="w-20 h-20 rounded-full bg-gradient-to-r from-emerald-500 to-teal-600 flex items-center justify-center text-white text-2xl font-bold mx-auto transform transition-transform group-hover:scale-110 group-hover:rotate-12">
                                {{ step.number }}
                            </div>
                            <div v-if="index < 2" class="hidden md:block absolute top-10 left-40 w-32 h-1 bg-gradient-to-r from-emerald-300 to-teal-300 transform -translate-y-1/2"></div>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">{{ step.title }}</h3>
                        <p class="text-gray-700 leading-relaxed">{{ step.description }}</p>
                    </div>
                </div>
            </div>
        </section>
        <!-- Blog Section -->
        <section class="py-24 relative bg-slate-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <div class="inline-flex items-center px-4 py-2 rounded-full bg-emerald-100 border border-emerald-200 text-emerald-700 text-sm font-medium mb-4">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></span>
                        Latest Updates
                    </div>
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">
                        FROM OUR
                        <span class="block text-emerald-700 bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">BLOG</span>
                    </h2>
                    <p class="text-xl text-gray-800 max-w-2xl mx-auto">
                        Stay updated with the latest insights, tips, and news about AI-powered learning.
                    </p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div
                        v-for="(post, index) in blogPosts"
                        :key="post.id"
                        class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-500 blog-card group"
                        :style="{ 'animation-delay': index * 0.15 + 's' }"
                    >
                        <div class="h-48 relative overflow-hidden bg-gradient-to-br from-emerald-500 to-teal-600">
                            <img
                                v-if="post.image_url"
                                :src="post.image_url"
                                :alt="post.title"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                            />
                            <div v-else class="absolute inset-0 flex items-center justify-center text-white text-4xl">
                                📁
                            </div>
                            <div class="absolute top-4 left-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs bg-white/20 backdrop-blur-sm text-white border border-white/30">
                                    {{ post.category }}
                                </span>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center text-gray-500 text-sm mb-3">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                {{ formatDate(post.published_at) }}
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3 line-clamp-2 group-hover:text-emerald-700 transition-colors">
                                {{ post.title }}
                            </h3>
                            <p class="text-gray-700 mb-4 leading-relaxed line-clamp-3">{{ post.excerpt }}</p>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center text-gray-600 text-sm">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    By {{ post.author?.name || 'Admin' }}
                                </div>
                                <Link
                                    :href="route('blog.show', post.slug)"
                                    class="inline-flex items-center text-emerald-700 hover:text-emerald-800 font-medium transition-colors group"
                                >
                                    Read More
                                    <svg class="ml-1 w-4 h-4 transform transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                    </svg>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="blogPosts.length === 0" class="text-center py-12">
                    <div class="w-24 h-24 mx-auto mb-4 text-gray-400">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">No Blog Posts Yet</h3>
                    <p class="text-gray-500">Stay tuned for upcoming articles and insights.</p>
                </div>
                <div class="text-center mt-12">
                    <Link
                        :href="route('blog.index')"
                        class="inline-flex items-center bg-white border-2 border-emerald-600 text-emerald-700 px-8 py-4 rounded-xl font-semibold text-lg hover:bg-emerald-50 transform transition-all duration-300 hover:scale-105 hover:shadow-lg group"
                    >
                        <span class="relative">
                            View All Blog Posts
                            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-emerald-600 transition-all duration-300 group-hover:w-full"></span>
                        </span>
                        <svg class="ml-2 w-5 h-5 transform transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </Link>
                </div>
            </div>
        </section>
        <!-- Testimonials Section -->
        <section class="py-24 relative bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <div class="inline-flex items-center px-4 py-2 rounded-full bg-emerald-100 border border-emerald-200 text-emerald-700 text-sm font-medium mb-4">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></span>
                        Success Stories
                    </div>
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">
                        WHAT OUR
                        <span class="block text-emerald-700 bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">LEARNERS SAY</span>
                    </h2>
                    <p class="text-xl text-gray-800 max-w-2xl mx-auto">
                        Real stories from students who transformed their learning experience.
                    </p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div
                        v-for="(testimonial, index) in testimonials"
                        :key="index"
                        class="bg-white border border-gray-200 rounded-2xl p-8 shadow-sm hover:shadow-xl transition-all duration-500 testimonial-card group"
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
        <!-- Final CTA Section -->
        <section class="py-24 relative bg-gradient-to-br from-emerald-600 to-teal-600">
            <div class="absolute inset-0 bg-black/10"></div>
            <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8 relative z-10">
                <h2 class="text-4xl font-bold text-white mb-6">
                    Ready to Start Your
                    <span class="block bg-gradient-to-r from-white to-emerald-200 bg-clip-text text-transparent">Learning Journey?</span>
                </h2>
                <p class="text-xl mb-12 text-emerald-100">
                    Join thousands of learners who are already transforming their education with AI.
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
                        :href="route('pricing')"
                        class="group bg-transparent border-2 border-white text-white px-8 py-4 rounded-xl font-semibold text-lg transform transition-all duration-300 hover:scale-105 hover:bg-white/10"
                    >
                        <span class="relative flex items-center">
                            View Plans
                            <svg class="ml-2 w-5 h-5 transform transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </span>
                    </Link>
                </div>
                <div class="mt-12 flex items-center justify-center text-sm text-emerald-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    <div v-for="(stat, index) in stats" :key="index" class="stat-item p-6 rounded-2xl hover:bg-white hover:shadow-lg transition-all duration-300">
                        <div class="text-3xl md:text-4xl font-bold text-gray-900 mb-2 animate-count-up">{{ stat.value }}</div>
                        <div class="text-gray-700 font-medium">{{ stat.label }}</div>
                    </div>
                </div>
            </div>
        </section>
    </AppLayout>
</template>
<script setup>
import { Link, Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/GuestLayout.vue';
import PricingPlans from '@/Components/PricingPlans.vue';
import { ref } from 'vue';

// Define props
const props = defineProps({
    courses: {
        type: Array,
        default: () => []
    },
    blogPosts: {
        type: Array,
        default: () => []
    },
    subscriptionPlans: {
        type: Array,
        default: () => []
    }
});

// Billing cycle state
const billingCycle = ref('monthly');

// Format date function
const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

// AI Features Data
const aiFeatures = [
    {
        icon: '🧠',
        title: 'Adaptive Learning',
        description: 'AI that adjusts to your learning pace and style for optimal progress'
    },
    {
        icon: '💬',
        title: 'Smart Tutoring',
        description: '24/7 AI tutor that provides instant explanations and guidance'
    },
    {
        icon: '📊',
        title: 'Progress Analytics',
        description: 'Detailed insights into your learning patterns and improvement areas'
    },
    {
        icon: '🎯',
        title: 'Personalized Paths',
        description: 'Custom learning journeys tailored to your goals and preferences'
    }
];

// Rest of your existing data...
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
        title: 'Sign Up & Assessment',
        description: 'Create your account and take our AI-powered assessment to understand your current level and learning preferences.'
    },
    {
        number: '2',
        title: 'Get Personalized Plan',
        description: 'Receive a customized learning path with recommended courses and activities tailored to your goals.'
    },
    {
        number: '3',
        title: 'Learn & Grow',
        description: 'Start learning with AI guidance, track your progress, and achieve your learning objectives faster.'
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
        quote: 'Olilrean helped me go from barely passing to top of my class in just one semester. The personalized feedback made all the difference.',
        avatar: 'https://placehold.co/100x100/059669/ffffff?text=SJ'
    },
    {
        name: 'Michael Chen',
        role: 'Working Professional',
        quote: 'As someone balancing work and learning, Oilearn\'s flexibility and smart scheduling have been game-changing for my career growth.',
        avatar: 'https://placehold.co/100x100/0d9488/ffffff?text=MC'
    },
    {
        name: 'Emma Rodriguez',
        role: 'High School Teacher',
        quote: 'I use Olilearn to supplement my classroom teaching. My students love the interactive approach and their test scores have improved significantly.',
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
/* Enhanced Animations */
@keyframes pulse-slow {
    0%, 100% { opacity: 0.5; transform: scale(1); }
    50% { opacity: 0.8; transform: scale(1.05); }
}

@keyframes ping-slow {
    0% { transform: scale(1); opacity: 1; }
    75%, 100% { transform: scale(2); opacity: 0; }
}

.animate-pulse-slow {
    animation: pulse-slow 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.animate-ping-slow {
    animation: ping-slow 2s cubic-bezier(0, 0, 0.2, 1) infinite;
}

.animate-fade-in {
    animation: fade-in-up 0.6s ease-out forwards;
}

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

/* Enhanced card effects */
.course-card {
    transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.course-card:hover {
    transform: translateY(-12px) scale(1.02);
}

.blog-card {
    transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.blog-card:hover {
    transform: translateY(-8px);
}

.pricing-card {
    transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.pricing-card:hover {
    transform: translateY(-8px);
}

.testimonial-card {
    transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.testimonial-card:hover {
    transform: translateY(-8px);
}

/* Rest of your existing styles... */
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

.animate-float {
    animation: float 8s ease-in-out infinite;
}

/* Card Effects */
.card-hover {
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.card-hover:hover {
    transform: translateY(-5px);
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
