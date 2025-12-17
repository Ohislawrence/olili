<!-- resources/js/Pages/frontpages/courses/Show.vue -->
<template>
    <MetaTags
        :title="course.title"
        :description="course.description"
        :image="course.image_url || '/images/olingolearn.png'"
        type="article"
    />
    <AppLayout>
        <Head :title="`${course.title}`" />
        <!-- Course Header -->
        <section class="relative py-20 bg-gradient-to-br from-emerald-600 to-teal-600 text-white">
            <div class="absolute inset-0 bg-black/20"></div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="flex flex-col lg:flex-row items-start gap-8">
                    <!-- Course Info -->
                    <div class="flex-1">
                        <div class="inline-flex items-center px-4 py-2 rounded-full bg-white/20 backdrop-blur-sm border border-white/30 text-white text-sm font-medium mb-6">
                            {{ course.subject }} • {{ course.level }}
                        </div>
                        <h1 class="text-4xl md:text-5xl font-bold mb-6 leading-tight">{{ course.title }}</h1>
                        <p class="text-xl text-emerald-100 mb-8 leading-relaxed">{{ course.description }}</p>

                        <!-- Course Meta -->
                        <div class="flex flex-wrap gap-6 mb-8">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <div class="font-semibold">{{ course.estimated_duration_hours }} Hours</div>
                                    <div class="text-sm text-emerald-200">Estimated Duration</div>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                <div>
                                    <div class="font-semibold">{{ course.modules?.length || 0 }} Modules</div>
                                    <div class="text-sm text-emerald-200">Learning Path</div>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <div class="font-semibold">{{ course.status }}</div>
                                    <div class="text-sm text-emerald-200">Course Status</div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row gap-4">
                            <button
                                v-if="course.progress_percentage > 0"
                                class="bg-white text-emerald-600 px-8 py-4 rounded-xl font-semibold hover:bg-gray-100 transform hover:scale-105 transition-all duration-300 flex items-center justify-center"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Continue Learning
                            </button>
                            <button
                                v-else
                                class="bg-white text-emerald-600 px-8 py-4 rounded-xl font-semibold hover:bg-gray-100 transform hover:scale-105 transition-all duration-300 flex items-center justify-center"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                </svg>
                                Start Course
                            </button>

                            <!-- Share Button with dropdown -->
                            <div class="relative" ref="shareContainer">
                                <button
                                    @click="toggleShareDropdown"
                                    class="bg-transparent border-2 border-white text-white px-8 py-4 rounded-xl font-semibold hover:bg-white/10 transform hover:scale-105 transition-all duration-300 flex items-center justify-center"
                                >
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                                    </svg>
                                    Share Course
                                </button>

                                <!-- Share Dropdown -->
                                <div
                                    v-show="showShareDropdown"
                                    class="absolute z-50 mt-2 w-64 bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden"
                                    style="left: 50%; transform: translateX(-50%);"
                                >
                                    <div class="p-2">
                                        <!-- Copy URL -->
                                        <button
                                            @click.stop="copyUrl"
                                            class="w-full flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors"
                                        >
                                            <svg class="w-5 h-5 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                            </svg>
                                            <span class="font-medium">Copy Link</span>
                                        </button>

                                        <!-- Social Media Sharing -->
                                        <div class="flex items-center justify-around py-3 border-t border-gray-100">
                                            <!-- WhatsApp -->
                                            <button
                                                @click.stop="shareOnWhatsApp"
                                                class="p-2 rounded-full hover:bg-green-50 transition-colors group"
                                                title="Share on WhatsApp"
                                            >
                                                <svg class="w-6 h-6 text-green-600 group-hover:text-green-700" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M20.1 3.9C17.9 1.7 15 .5 12 .5 5.8.5.7 5.6.7 11.9c0 2 .5 3.9 1.5 5.6L.6 23.4l6-1.6c1.6.9 3.5 1.3 5.4 1.3 6.3 0 11.4-5.1 11.4-11.4-.1-2.8-1.2-5.7-3.3-7.8zM12 21.4c-1.7 0-3.3-.5-4.8-1.3l-.4-.2-3.5 1 1-3.4L4 17c-1-1.5-1.4-3.2-1.4-5.1 0-5.2 4.2-9.4 9.4-9.4 2.5 0 4.9 1 6.7 2.8 1.8 1.8 2.8 4.2 2.8 6.7-.1 5.2-4.3 9.4-9.5 9.4zm5.1-7.1c-.3-.1-1.7-.9-1.9-1-.3-.1-.5-.1-.7.1-.2.3-.8 1-.9 1.1-.2.2-.3.2-.6.1s-1.2-.5-2.3-1.4c-.9-.8-1.5-1.7-1.6-2-.2-.3 0-.5.1-.6s.3-.3.4-.5c.2-.1.3-.3.4-.5.1-.2 0-.4 0-.5C10 9 9.3 7.6 9 7c-.1-.4-.4-.3-.5-.3h-.6s-.4.1-.7.3c-.3.3-1 1-1 2.4s1 2.8 1.1 3c.1.2 2 3.1 4.9 4.3.7.3 1.2.5 1.6.6.7.2 1.3.2 1.8.1.6-.1 1.7-.7 1.9-1.3.2-.7.2-1.2.2-1.3-.1-.3-.3-.4-.6-.5z"/>
                                                </svg>
                                            </button>

                                            <!-- Facebook -->
                                            <button
                                                @click.stop="shareOnFacebook"
                                                class="p-2 rounded-full hover:bg-blue-50 transition-colors group"
                                                title="Share on Facebook"
                                            >
                                                <svg class="w-6 h-6 text-blue-600 group-hover:text-blue-700" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                                </svg>
                                            </button>

                                            <!-- Twitter/X -->
                                            <button
                                                @click.stop="shareOnTwitter"
                                                class="p-2 rounded-full hover:bg-gray-50 transition-colors group"
                                                title="Share on Twitter"
                                            >
                                                <svg class="w-6 h-6 text-gray-800 group-hover:text-black" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                                </svg>
                                            </button>

                                            <!-- LinkedIn -->
                                            <button
                                                @click.stop="shareOnLinkedIn"
                                                class="p-2 rounded-full hover:bg-blue-50 transition-colors group"
                                                title="Share on LinkedIn"
                                            >
                                                <svg class="w-6 h-6 text-blue-700 group-hover:text-blue-800" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                                </svg>
                                            </button>

                                            <!-- Telegram -->
                                            <button
                                                @click.stop="shareOnTelegram"
                                                class="p-2 rounded-full hover:bg-blue-50 transition-colors group"
                                                title="Share on Telegram"
                                            >
                                                <svg class="w-6 h-6 text-blue-500 group-hover:text-blue-600" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.697.064-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                                                </svg>
                                            </button>
                                        </div>

                                        <!-- Copy Success Message -->
                                        <div
                                            v-if="copySuccess"
                                            class="mt-2 p-2 bg-green-50 text-green-700 text-sm rounded-lg text-center animate-pulse"
                                        >
                                            ✓ Link copied to clipboard!
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Course Visual -->
                    <div class="lg:w-96 flex-shrink-0">
                        <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl p-6">
                            <div class="text-center mb-6">
                                <div class="w-20 h-20 mx-auto mb-4 bg-white/20 rounded-2xl flex items-center justify-center text-white text-2xl">
                                    📚
                                </div>
                                <h3 class="text-xl font-bold mb-2">Course Overview</h3>
                                <p class="text-emerald-200">AI-Powered Learning Path</p>
                            </div>

                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-emerald-200">Difficulty</span>
                                    <span class="font-semibold">{{ course.level }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-emerald-200">Enrolled</span>
                                    <span class="font-semibold">134 Students</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-emerald-200">Rating</span>
                                    <div class="flex items-center">
                                        <svg v-for="n in 5" :key="n" class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                        <span class="ml-2 font-semibold">4.8</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Course Content -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row gap-12">
                    <!-- Main Content -->
                    <div class="lg:w-2/3">
                        <!-- Learning Objectives -->
                        <div class="mb-12">
                            <h2 class="text-3xl font-bold text-gray-900 mb-6">What You'll Learn</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div
                                    v-for="(objective, index) in course.learning_objectives || []"
                                    :key="index"
                                    class="flex items-start p-4 bg-gray-50 rounded-xl"
                                >
                                    <svg class="w-5 h-5 text-emerald-600 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-gray-700">{{ objective }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Course Curriculum -->
                        <div class="mb-12">
                            <h2 class="text-3xl font-bold text-gray-900 mb-6">Course Curriculum</h2>
                            <div class="space-y-4">
                                <div
                                    v-for="module in course.modules || []"
                                    :key="module.id"
                                    class="bg-white border border-gray-200 rounded-2xl overflow-hidden hover:shadow-lg transition-all duration-300"
                                >
                                    <button
                                        @click="toggleModule(module.id)"
                                        class="w-full flex items-center justify-between p-6 text-left hover:bg-gray-50 transition-colors"
                                    >
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center text-emerald-600 font-semibold mr-4">
                                                {{ module.order }}
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-900">{{ module.title }}</h3>
                                                <p class="text-gray-600 text-sm mt-1">
                                                    {{ module.topics?.length || 0 }} topics • {{ module.estimated_duration_hours || 2 }} hours
                                                </p>
                                            </div>
                                        </div>
                                        <svg
                                            class="w-5 h-5 text-gray-400 transform transition-transform"
                                            :class="{ 'rotate-180': expandedModule === module.id }"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>

                                    <!-- Module Topics -->
                                    <div v-if="expandedModule === module.id" class="border-t border-gray-200">
                                        <div class="p-6 space-y-3">
                                            <div
                                                v-for="topic in module.topics || []"
                                                :key="topic.id"
                                                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
                                            >
                                                <div class="flex items-center">
                                                    <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center text-gray-600 text-sm font-semibold mr-3">
                                                        {{ topic.order }}
                                                    </div>
                                                    <div>
                                                        <h4 class="font-medium text-gray-900">{{ topic.title }}</h4>
                                                        <p class="text-gray-600 text-sm">{{ topic.estimated_duration_minutes }} minutes</p>
                                                    </div>
                                                </div>
                                                <button class="text-emerald-600 hover:text-emerald-700 font-semibold text-sm">
                                                    Start
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Prerequisites -->
                        <div v-if="course.prerequisites && course.prerequisites.length > 0" class="mb-12">
                            <h2 class="text-3xl font-bold text-gray-900 mb-6">Prerequisites</h2>
                            <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6">
                                <ul class="space-y-2">
                                    <li
                                        v-for="(prereq, index) in course.prerequisites"
                                        :key="index"
                                        class="flex items-center text-blue-800"
                                    >
                                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ prereq }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="lg:w-1/3">
                        <!-- Course Actions -->
                        <div class="bg-gray-50 rounded-2xl p-6 mb-8 sticky top-24">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Course Actions</h3>
                            <div class="space-y-3">
                                <!-- Enroll Button -->
                                <button
                                    v-if="$page.props.auth.user"
                                    @click="enrollInCourse"
                                    class="w-full bg-emerald-600 text-white py-3 rounded-xl font-semibold hover:bg-emerald-700 transition-colors flex items-center justify-center"
                                >
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                    </svg>
                                    {{ isEnrolled ? 'Go to Course' : 'Enroll in Course' }}
                                </button>

                                <!-- Sign In Button for guests -->
                                <Link
                                    v-else
                                    :href="route('login')"
                                    class="w-full bg-emerald-600 text-white py-3 rounded-xl font-semibold hover:bg-emerald-700 transition-colors flex items-center justify-center"
                                >
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                    </svg>
                                    Sign In to Enroll
                                </Link>

                                <!-- Share Button in Sidebar -->
                                <button
                                    @click="toggleShareDropdown"
                                    class="w-full border border-gray-300 text-gray-700 py-3 rounded-xl font-semibold hover:bg-gray-100 transition-colors flex items-center justify-center"
                                >
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                                    </svg>
                                    Share Course
                                </button>
                            </div>
                        </div>

                        <!-- Related Courses -->
                        <div v-if="relatedCourses.length > 0" class="bg-white border border-gray-200 rounded-2xl p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Related Courses</h3>
                            <div class="space-y-4">
                                <div
                                    v-for="relatedCourse in relatedCourses"
                                    :key="relatedCourse.id"
                                    class="flex items-start space-x-3 group cursor-pointer"
                                >
                                    <div class="w-16 h-16 flex-shrink-0 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center text-white text-lg font-semibold">
                                        {{ relatedCourse.subject?.charAt(0) || 'C' }}
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-900 group-hover:text-emerald-600 transition-colors line-clamp-2">
                                            {{ relatedCourse.title }}
                                        </h4>
                                        <p class="text-gray-500 text-sm mt-1">{{ relatedCourse.level }}</p>
                                        <div class="flex items-center mt-2 text-sm text-gray-600">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ relatedCourse.estimated_duration_hours }}h
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </AppLayout>
</template>

<script setup>
import { Link, Head, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/GuestLayout.vue';
import MetaTags from '@/Components/MetaTags.vue';

const props = defineProps({
    course: Object,
    relatedCourses: Array
});

const page = usePage();
const expandedModule = ref(null);
const showShareDropdown = ref(false);
const copySuccess = ref(false);
const shareContainer = ref(null);

const isEnrolled = computed(() => props.course.user_enrollment_count > 0);

// Share functionality
const courseUrl = typeof window !== 'undefined' ? window.location.href : '';
const shareText = `Check out this course: ${props.course.title}`;

const toggleShareDropdown = () => {
    showShareDropdown.value = !showShareDropdown.value;
};

const closeShareDropdown = (event) => {
    if (shareContainer.value && !shareContainer.value.contains(event.target)) {
        showShareDropdown.value = false;
    }
};

const copyUrl = async () => {
    try {
        await navigator.clipboard.writeText(courseUrl);
        copySuccess.value = true;
        setTimeout(() => {
            copySuccess.value = false;
        }, 2000);
    } catch (err) {
        console.error('Failed to copy: ', err);
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = courseUrl;
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
            copySuccess.value = true;
            setTimeout(() => {
                copySuccess.value = false;
            }, 2000);
        } catch (err) {
            console.error('Fallback copy failed: ', err);
        }
        document.body.removeChild(textArea);
    }
};

const shareOnWhatsApp = () => {
    const url = `https://wa.me/?text=${encodeURIComponent(`${shareText}\n\n${courseUrl}`)}`;
    window.open(url, '_blank');
    showShareDropdown.value = false;
};

const shareOnFacebook = () => {
    const url = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(courseUrl)}&quote=${encodeURIComponent(shareText)}`;
    window.open(url, '_blank', 'width=600,height=400');
    showShareDropdown.value = false;
};

const shareOnTwitter = () => {
    const url = `https://twitter.com/intent/tweet?text=${encodeURIComponent(shareText)}&url=${encodeURIComponent(courseUrl)}`;
    window.open(url, '_blank', 'width=600,height=400');
    showShareDropdown.value = false;
};

const shareOnLinkedIn = () => {
    const url = `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(courseUrl)}`;
    window.open(url, '_blank', 'width=600,height=400');
    showShareDropdown.value = false;
};

const shareOnTelegram = () => {
    const url = `https://t.me/share/url?url=${encodeURIComponent(courseUrl)}&text=${encodeURIComponent(shareText)}`;
    window.open(url, '_blank', 'width=600,height=400');
    showShareDropdown.value = false;
};

// Enroll functionality
const enrollInCourse = () => {
    if (isEnrolled.value) {
        // Redirect to course dashboard if already enrolled
        router.visit(route('student.courses.show', props.course.id));
    } else {
        // Enroll in course
        router.post(route('student.catalog.enroll', props.course.id), {}, {
            onSuccess: () => {
                // Course enrolled successfully
                router.visit(route('student.courses.show', props.course.id));
            },
            onError: (errors) => {
                if (errors.message) {
                    alert(errors.message);
                }
            }
        });
    }
};

const toggleModule = (moduleId) => {
    expandedModule.value = expandedModule.value === moduleId ? null : moduleId;
};

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

// Close dropdown when clicking outside
onMounted(() => {
    document.addEventListener('click', closeShareDropdown);
});

onUnmounted(() => {
    document.removeEventListener('click', closeShareDropdown);
});
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Smooth transitions for module expansion */
.module-content {
    transition: all 0.3s ease-in-out;
}

/* Animation for copy success */
@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.7;
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
