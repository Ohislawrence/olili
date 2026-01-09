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

        <!-- Course Header - Lighter Green -->
        <section class="relative py-16 bg-gradient-to-br from-emerald-50 via-teal-50 to-white">
            <!-- Decorative elements -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute -top-40 -right-40 w-80 h-80 bg-emerald-100 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob"></div>
                <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-teal-100 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-2000"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <!-- Breadcrumb -->
                <nav class="mb-6">
                    <ol class="flex items-center space-x-2 text-sm">
                        <li>
                            <Link :href="route('courses.index')" class="text-emerald-600 hover:text-emerald-700 transition-colors">
                                Courses
                            </Link>
                        </li>
                        <li class="text-gray-400">/</li>
                        <li class="text-gray-400">{{ course.subject }}</li>
                        <li class="text-gray-400">/</li>
                        <li class="font-medium text-gray-700 truncate">{{ course.title }}</li>
                    </ol>
                </nav>

                <div class="flex flex-col lg:flex-row items-start gap-8">
                    <!-- Course Info -->
                    <div class="flex-1">
                        <!-- Badges -->
                        <div class="flex flex-wrap gap-2 mb-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-emerald-100 text-emerald-800 border border-emerald-200">
                                {{ course.subject }}
                            </span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                {{ course.level }}
                            </span>
                            <span v-if="course.status === 'published'" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 border border-green-200">
                                Published
                            </span>
                            <span v-if="course.is_featured" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-amber-100 text-amber-800 border border-amber-200">
                                Featured
                            </span>
                        </div>

                        <h1 class="text-4xl md:text-5xl font-bold mb-6 text-gray-900 leading-tight">{{ course.title }}</h1>
                        <p class="text-xl text-gray-600 mb-8 leading-relaxed max-w-3xl">{{ course.description }}</p>

                        <!-- Course Meta - Compact -->
                        <div class="flex flex-wrap items-center gap-6 mb-8 p-4 bg-white/60 backdrop-blur-sm rounded-2xl border border-gray-100 shadow-sm">
                            <div class="flex items-center">
                                <div class="p-2 bg-emerald-100 rounded-lg mr-3">
                                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900">{{ course.estimated_duration_hours }} Hours</div>
                                    <div class="text-sm text-gray-500">Duration</div>
                                </div>
                            </div>

                            <div class="flex items-center">
                                <div class="p-2 bg-blue-100 rounded-lg mr-3">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900">{{ course.modules?.length || 0 }} Modules</div>
                                    <div class="text-sm text-gray-500">Content</div>
                                </div>
                            </div>

                            <div class="flex items-center">
                                <div class="p-2 bg-purple-100 rounded-lg mr-3">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900">{{ course.status }}</div>
                                    <div class="text-sm text-gray-500">Status</div>
                                </div>
                            </div>

                            <div class="flex items-center ml-auto">
                                <div class="flex items-center">
                                    <div class="flex mr-1">
                                        <svg v-for="n in 5" :key="n" class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    </div>
                                    <span class="text-gray-900 font-semibold ml-1">4.8</span>
                                    <span class="text-gray-500 text-sm ml-1">(24 reviews)</span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons - More Visible -->
                        <div class="flex flex-col sm:flex-row gap-4 mb-8">
                            <!-- Main Action Button -->
                            <button
                                v-if="course.progress_percentage > 0"
                                @click="enrollInCourse"
                                class="group relative bg-gradient-to-r from-emerald-600 to-teal-600 text-white px-8 py-4 rounded-xl font-semibold hover:from-emerald-700 hover:to-teal-700 transform hover:scale-[1.02] transition-all duration-300 flex items-center justify-center shadow-lg hover:shadow-xl"
                            >
                                <div class="absolute inset-0 bg-gradient-to-r from-emerald-700 to-teal-700 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                <svg class="w-5 h-5 mr-2 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="relative z-10">Continue Learning ({{ course.progress_percentage }}%)</span>
                            </button>
                            <button
                                v-else-if="$page.props.auth.user && isEnrolled"
                                @click="enrollInCourse"
                                class="group relative bg-gradient-to-r from-emerald-600 to-teal-600 text-white px-8 py-4 rounded-xl font-semibold hover:from-emerald-700 hover:to-teal-700 transform hover:scale-[1.02] transition-all duration-300 flex items-center justify-center shadow-lg hover:shadow-xl"
                            >
                                <div class="absolute inset-0 bg-gradient-to-r from-emerald-700 to-teal-700 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                <svg class="w-5 h-5 mr-2 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                </svg>
                                <span class="relative z-10">Go to Course Dashboard</span>
                            </button>
                            <button
                                v-else-if="$page.props.auth.user"
                                @click="enrollInCourse"
                                class="group relative bg-gradient-to-r from-emerald-600 to-teal-600 text-white px-8 py-4 rounded-xl font-semibold hover:from-emerald-700 hover:to-teal-700 transform hover:scale-[1.02] transition-all duration-300 flex items-center justify-center shadow-lg hover:shadow-xl"
                            >
                                <div class="absolute inset-0 bg-gradient-to-r from-emerald-700 to-teal-700 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                <svg class="w-5 h-5 mr-2 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                </svg>
                                <span class="relative z-10">Enroll Now - Start Learning</span>
                            </button>
                            <Link
                                v-else
                                :href="route('login')"
                                class="group relative bg-gradient-to-r from-emerald-600 to-teal-600 text-white px-8 py-4 rounded-xl font-semibold hover:from-emerald-700 hover:to-teal-700 transform hover:scale-[1.02] transition-all duration-300 flex items-center justify-center shadow-lg hover:shadow-xl"
                            >
                                <div class="absolute inset-0 bg-gradient-to-r from-emerald-700 to-teal-700 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                <svg class="w-5 h-5 mr-2 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                </svg>
                                <span class="relative z-10">Sign In to Enroll</span>
                            </Link>

                            <!-- Secondary Actions -->
                            <div class="flex gap-3">
                                <button
                                    @click="toggleWishlist"
                                    class="group p-4 bg-white border border-gray-200 rounded-xl hover:border-emerald-300 hover:bg-emerald-50 transition-all duration-300 shadow-sm hover:shadow-md"
                                    :title="isWishlisted ? 'Remove from wishlist' : 'Add to wishlist'"
                                >
                                    <svg
                                        class="w-5 h-5 transition-colors"
                                        :class="isWishlisted ? 'text-red-500 fill-red-500' : 'text-gray-400 group-hover:text-red-500'"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                    </svg>
                                </button>

                                <!-- Share Button -->
                                <div class="relative" ref="shareContainer">
                                    <button
                                        @click.stop="toggleShareDropdown"
                                        class="group p-4 bg-white border border-gray-200 rounded-xl hover:border-emerald-300 hover:bg-emerald-50 transition-all duration-300 shadow-sm hover:shadow-md flex items-center"
                                    >
                                        <svg class="w-5 h-5 text-gray-500 group-hover:text-emerald-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Course Visual Card -->
                    <div class="lg:w-96 flex-shrink-0">
                        <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-lg">
                            <!-- Progress Indicator -->
                            <div v-if="course.progress_percentage > 0" class="mb-6">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-gray-700">Your Progress</span>
                                    <span class="text-sm font-semibold text-emerald-600">{{ course.progress_percentage }}%</span>
                                </div>
                                <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div
                                        class="h-full bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full transition-all duration-500"
                                        :style="{ width: `${course.progress_percentage}%` }"
                                    ></div>
                                </div>
                            </div>

                            <div class="text-center mb-6">
                                <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-2xl flex items-center justify-center text-emerald-600 text-2xl">
                                    📚
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 mb-1">Course Overview</h3>
                                <p class="text-gray-500 text-sm">AI-Powered Learning Journey</p>
                            </div>

                            <div class="space-y-3">
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <span class="text-gray-600 text-sm">Difficulty Level</span>
                                    <span class="font-semibold text-gray-900">{{ course.level }}</span>
                                </div>
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <span class="text-gray-600 text-sm">Total Enrolled</span>
                                    <span class="font-semibold text-gray-900">{{ course.enrollment_count || 134 }} Students</span>
                                </div>
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <span class="text-gray-600 text-sm">Last Updated</span>
                                    <span class="font-semibold text-gray-900">{{ formatDate(course.updated_at) }}</span>
                                </div>
                            </div>

                            <!-- Quick Stats -->
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="text-center p-3 bg-emerald-50 rounded-lg">
                                        <div class="text-lg font-bold text-emerald-700">{{ course.modules?.length || 0 }}</div>
                                        <div class="text-xs text-emerald-600">Modules</div>
                                    </div>
                                    <div class="text-center p-3 bg-blue-50 rounded-lg">
                                        <div class="text-lg font-bold text-blue-700">{{ getTotalTopics() }}</div>
                                        <div class="text-xs text-blue-600">Topics</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Course Content -->
        <section class="py-16 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Navigation Tabs -->
                <div class="mb-8">
                    <div class="border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8">
                            <button
                                @click="activeTab = 'curriculum'"
                                :class="[
                                    'py-4 px-1 border-b-2 font-medium text-sm transition-colors',
                                    activeTab === 'curriculum'
                                        ? 'border-emerald-500 text-emerald-600'
                                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                ]"
                            >
                                Curriculum
                            </button>
                            <button
                                @click="activeTab = 'overview'"
                                :class="[
                                    'py-4 px-1 border-b-2 font-medium text-sm transition-colors',
                                    activeTab === 'overview'
                                        ? 'border-emerald-500 text-emerald-600'
                                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                ]"
                            >
                                Overview
                            </button>
                            <button
                                @click="activeTab = 'reviews'"
                                :class="[
                                    'py-4 px-1 border-b-2 font-medium text-sm transition-colors',
                                    activeTab === 'reviews'
                                        ? 'border-emerald-500 text-emerald-600'
                                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                ]"
                            >
                                Reviews
                            </button>
                            <button
                                @click="activeTab = 'faq'"
                                :class="[
                                    'py-4 px-1 border-b-2 font-medium text-sm transition-colors',
                                    activeTab === 'faq'
                                        ? 'border-emerald-500 text-emerald-600'
                                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                ]"
                            >
                                FAQ
                            </button>
                        </nav>
                    </div>
                </div>

                <div class="flex flex-col lg:flex-row gap-8">
                    <!-- Main Content -->
                    <div class="lg:w-2/3">
                        <!-- Curriculum Tab -->
                        <div v-if="activeTab === 'curriculum'" class="space-y-6">
                            <!-- Course Progress Summary -->
                            <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-xl font-bold text-gray-900">Course Content</h3>
                                    <span class="text-sm text-gray-500">{{ getTotalTopics() }} topics • {{ course.estimated_duration_hours }} total hours</span>
                                </div>

                                <!-- Module Accordions -->
                                <div class="space-y-3">
                                    <div
                                        v-for="module in course.modules || []"
                                        :key="module.id"
                                        class="border border-gray-200 rounded-xl overflow-hidden hover:border-emerald-300 transition-colors"
                                    >
                                        <button
                                            @click="toggleModule(module.id)"
                                            class="w-full flex items-center justify-between p-5 text-left bg-white hover:bg-gray-50 transition-colors"
                                        >
                                            <div class="flex items-center flex-1">
                                                <div class="w-10 h-10 bg-emerald-50 rounded-lg flex items-center justify-center text-emerald-600 font-semibold mr-4 flex-shrink-0">
                                                    {{ module.order }}
                                                </div>
                                                <div class="flex-1">
                                                    <h4 class="font-semibold text-gray-900">{{ module.title }}</h4>
                                                    <div class="flex items-center mt-1 text-sm text-gray-500">
                                                        <span class="flex items-center mr-4">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                                            </svg>
                                                            {{ module.topics?.length || 0 }} topics
                                                        </span>
                                                        <span class="flex items-center">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                            {{ Math.ceil((module.estimated_duration_minutes || 120) / 60) }}h
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex items-center">
                                                <span v-if="getModuleProgress(module.id)" class="text-sm text-emerald-600 font-medium mr-3">
                                                    {{ getModuleProgress(module.id) }}% complete
                                                </span>
                                                <svg
                                                    class="w-5 h-5 text-gray-400 transition-transform duration-300"
                                                    :class="{ 'rotate-180': expandedModule === module.id }"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </div>
                                        </button>

                                        <!-- Module Topics -->
                                        <div
                                            v-if="expandedModule === module.id"
                                            class="border-t border-gray-200 bg-gray-50/50"
                                            :class="isModuleExpanded(module.id) ? 'animate-slideDown' : 'animate-slideUp'"
                                        >
                                            <div class="p-4">
                                                <div v-if="module.description" class="mb-4 p-4 bg-white rounded-lg border border-gray-200">
                                                    <p class="text-gray-600">{{ module.description }}</p>
                                                </div>

                                                <div class="space-y-2">
                                                    <div
                                                        v-for="topic in module.topics || []"
                                                        :key="topic.id"
                                                        class="flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200 hover:border-emerald-300 hover:shadow-sm transition-all"
                                                    >
                                                        <div class="flex items-center">
                                                            <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 text-sm font-medium mr-3">
                                                                {{ topic.order }}
                                                            </div>
                                                            <div>
                                                                <h5 class="font-medium text-gray-900">{{ topic.title }}</h5>
                                                                <div class="flex items-center text-sm text-gray-500 mt-1">
                                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                    </svg>
                                                                    {{ topic.estimated_duration_minutes }} min
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Overview Tab -->
                        <div v-if="activeTab === 'overview'" class="space-y-8">
                            <!-- Learning Objectives -->
                            <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm">
                                <h3 class="text-xl font-bold text-gray-900 mb-6">What You'll Learn</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div
                                        v-for="(objective, index) in course.learning_objectives || []"
                                        :key="index"
                                        class="flex items-start p-4 bg-emerald-50/50 rounded-xl border border-emerald-100"
                                    >
                                        <svg class="w-5 h-5 text-emerald-600 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-gray-700">{{ objective }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Prerequisites -->
                            <div v-if="course.prerequisites && course.prerequisites.length > 0" class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm">
                                <h3 class="text-xl font-bold text-gray-900 mb-6">Prerequisites</h3>
                                <div class="bg-blue-50/50 border border-blue-100 rounded-xl p-5">
                                    <div class="flex items-start mb-4">
                                        <svg class="w-6 h-6 text-blue-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <p class="text-blue-800">Before starting this course, we recommend having basic knowledge in these areas:</p>
                                    </div>
                                    <ul class="space-y-2">
                                        <li
                                            v-for="(prereq, index) in course.prerequisites"
                                            :key="index"
                                            class="flex items-center text-blue-700 ml-9"
                                        >
                                            <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            {{ prereq }}
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Target Audience -->
                            <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm">
                                <h3 class="text-xl font-bold text-gray-900 mb-6">Who is this course for?</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <div class="p-4 bg-gradient-to-br from-emerald-50 to-white rounded-xl border border-emerald-100">
                                        <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center text-emerald-600 mb-3">
                                            👩‍💻
                                        </div>
                                        <h4 class="font-semibold text-gray-900 mb-2">Beginners</h4>
                                        <p class="text-gray-600 text-sm">Starting their journey in this subject</p>
                                    </div>
                                    <div class="p-4 bg-gradient-to-br from-blue-50 to-white rounded-xl border border-blue-100">
                                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 mb-3">
                                            🎓
                                        </div>
                                        <h4 class="font-semibold text-gray-900 mb-2">Students</h4>
                                        <p class="text-gray-600 text-sm">Looking to supplement their academic studies</p>
                                    </div>
                                    <div class="p-4 bg-gradient-to-br from-purple-50 to-white rounded-xl border border-purple-100">
                                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center text-purple-600 mb-3">
                                            💼
                                        </div>
                                        <h4 class="font-semibold text-gray-900 mb-2">Professionals</h4>
                                        <p class="text-gray-600 text-sm">Seeking to upgrade their skills for career growth</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reviews Tab -->
                        <div v-if="activeTab === 'reviews'" class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm">
                            <h3 class="text-xl font-bold text-gray-900 mb-6">Student Reviews</h3>
                            <!-- Add reviews content here -->
                            <div class="text-center py-12 text-gray-500">
                                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                </svg>
                                <p>No reviews yet. Be the first to review this course!</p>
                            </div>
                        </div>

                        <!-- FAQ Tab -->
                        <div v-if="activeTab === 'faq'" class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm">
                            <h3 class="text-xl font-bold text-gray-900 mb-6">Frequently Asked Questions</h3>
                            <!-- Add FAQ content here -->
                            <div class="text-center py-12 text-gray-500">
                                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p>Common questions will appear here</p>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="lg:w-1/3">
                        <!-- Instructor Card -->
                        <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Course Instructor</h3>
                            <div class="flex items-start space-x-4">
                                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-emerald-100 to-teal-100 flex items-center justify-center text-emerald-600 text-xl font-bold">
                                    AI
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900">OliLearn AI</h4>
                                    <p class="text-sm text-gray-600 mt-1">AI-Powered Learning Assistant</p>
                                    <div class="flex items-center mt-3 text-sm text-gray-500">
                                        <svg class="w-4 h-4 mr-1 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span>Expert in {{ course.subject }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Course Features -->
                        <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Course Features</h3>
                            <div class="space-y-3">
                                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                    <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center text-emerald-600 mr-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-900">Certificate</h4>
                                        <p class="text-sm text-gray-500">Upon completion</p>
                                    </div>
                                </div>
                                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 mr-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-900">Hands-on Projects</h4>
                                        <p class="text-sm text-gray-500">Practical experience</p>
                                    </div>
                                </div>
                                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center text-purple-600 mr-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-900">Lifetime Access</h4>
                                        <p class="text-sm text-gray-500">Learn at your pace</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Related Courses -->
                        <div v-if="relatedCourses.length > 0" class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Related Courses</h3>
                                <Link :href="route('courses.index')" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">
                                    View all
                                </Link>
                            </div>
                            <div class="space-y-4">
                                <Link
                                    v-for="relatedCourse in relatedCourses.slice(0, 3)"
                                    :key="relatedCourse.id"
                                    :href="route('courses.show', relatedCourse.id)"
                                    class="group flex items-start space-x-3 p-3 rounded-xl hover:bg-gray-50 transition-colors border border-transparent hover:border-gray-200"
                                >
                                    <div class="w-12 h-12 flex-shrink-0 rounded-lg bg-gradient-to-br from-emerald-100 to-teal-100 flex items-center justify-center text-emerald-600 font-semibold">
                                        {{ relatedCourse.subject?.charAt(0) || 'C' }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-medium text-gray-900 group-hover:text-emerald-600 transition-colors truncate">
                                            {{ relatedCourse.title }}
                                        </h4>
                                        <div class="flex items-center text-sm text-gray-500 mt-1">
                                            <span class="truncate">{{ relatedCourse.level }}</span>
                                            <span class="mx-2">•</span>
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                {{ relatedCourse.estimated_duration_hours }}h
                                            </span>
                                        </div>
                                    </div>
                                </Link>
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
const activeTab = ref('curriculum');
const isWishlisted = ref(false);

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

// Course functionality
const enrollInCourse = () => {
    if (isEnrolled.value) {
        router.visit(route('student.courses.show', { id: props.course.id, slug: props.course.slug}));
    } else {
        router.post(route('student.catalog.enroll', props.course.id), {}, {
            onSuccess: () => {
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

const toggleWishlist = () => {
    isWishlisted.value = !isWishlisted.value;
    // Add API call here to update wishlist
};

const toggleModule = (moduleId) => {
    expandedModule.value = expandedModule.value === moduleId ? null : moduleId;
};

const isModuleExpanded = (moduleId) => {
    return expandedModule.value === moduleId;
};

const getTotalTopics = () => {
    return props.course.modules?.reduce((total, module) => {
        return total + (module.topics?.length || 0);
    }, 0) || 0;
};

const getModuleProgress = (moduleId) => {
    // This would come from your backend
    // For now, return a dummy value or 0
    return 0;
};

const startTopic = (topic) => {
    // Navigate to topic content
    console.log('Starting topic:', topic.id);
    // router.visit(route('student.topics.show', topic.id));
};

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

// Close dropdown when clicking outside
onMounted(() => {
    document.addEventListener('click', closeShareDropdown);
    // Check wishlist status from local storage or API
    const wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
    isWishlisted.value = wishlist.includes(props.course.id);
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

/* Animations */
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

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
        max-height: 0;
    }
    to {
        opacity: 1;
        transform: translateY(0);
        max-height: 500px;
    }
}

@keyframes slideUp {
    from {
        opacity: 1;
        transform: translateY(0);
        max-height: 500px;
    }
    to {
        opacity: 0;
        transform: translateY(-10px);
        max-height: 0;
    }
}

.animate-slideDown {
    animation: slideDown 0.3s ease-out forwards;
}

.animate-slideUp {
    animation: slideUp 0.3s ease-in forwards;
}
</style>
