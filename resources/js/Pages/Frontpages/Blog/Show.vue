<template>
    <MetaTags
        :title="post.title"
        :description="post.excerpt"
        :image="post.image_url"
        type="article"
    />
    <AppLayout>
        <Head :title="`${post.title}`" />

        <!-- Article Header - Lighter Green -->
        <section class="relative py-16 bg-gradient-to-br from-emerald-50 via-teal-50 to-white">
            <!-- Decorative elements -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute -top-40 -right-40 w-80 h-80 bg-emerald-100 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob"></div>
                <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-teal-100 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-2000"></div>
            </div>

            <!-- Wider container -->
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <!-- Breadcrumb -->
                <nav class="mb-8">
                    <ol class="flex items-center space-x-2 text-sm">
                        <li>
                            <Link :href="route('welcome')" class="text-emerald-600 hover:text-emerald-700 transition-colors">
                                Home
                            </Link>
                        </li>
                        <li class="text-gray-400">/</li>
                        <li>
                            <Link :href="route('blog.index')" class="text-emerald-600 hover:text-emerald-700 transition-colors">
                                Blog
                            </Link>
                        </li>
                        <li class="text-gray-400">/</li>
                        <li class="text-gray-700 truncate max-w-xs">{{ post.title }}</li>
                    </ol>
                </nav>

                <div class="text-center">
                    <!-- Badge -->
                    <div class="inline-flex items-center px-4 py-2 rounded-full bg-emerald-100 border border-emerald-200 text-emerald-700 text-sm font-medium mb-6">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                        </svg>
                        {{ post.category }}
                    </div>

                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 mb-6 leading-tight max-w-4xl mx-auto">
                        {{ post.title }}
                    </h1>

                    <p class="text-lg sm:text-xl text-gray-600 mb-8 max-w-3xl mx-auto leading-relaxed">
                        {{ post.excerpt }}
                    </p>

                    <!-- Article Meta -->
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4 text-gray-600">
                        <!-- Author -->
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-100 to-teal-100 flex items-center justify-center text-emerald-700 font-semibold text-sm mr-3">
                                {{ post.author?.name?.charAt(0) || 'A' }}
                            </div>
                            <div class="text-left">
                                <div class="font-medium text-gray-900">{{ post.author?.name || 'Admin' }}</div>
                                <div class="text-sm text-gray-500">Author</div>
                            </div>
                        </div>

                        <div class="hidden sm:block text-gray-300">•</div>

                        <!-- Date -->
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span>{{ formatDate(post.published_at) }}</span>
                        </div>

                        <div class="hidden sm:block text-gray-300">•</div>

                        <!-- Read Time -->
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>{{ readingTime }} min read</span>
                        </div>
                    </div>

                    <!-- Share buttons - desktop -->
                    <div class="hidden sm:flex items-center justify-center gap-3 mt-8">
                        <span class="text-sm text-gray-500">Share:</span>
                        <button
                            @click="shareOnTwitter"
                            class="p-2.5 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition-colors hover:scale-105"
                            title="Share on Twitter"
                        >
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.213c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                            </svg>
                        </button>
                        <button
                            @click="shareOnLinkedIn"
                            class="p-2.5 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition-colors hover:scale-105"
                            title="Share on LinkedIn"
                        >
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                            </svg>
                        </button>
                        <button
                            @click="shareOnFacebook"
                            class="p-2.5 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition-colors hover:scale-105"
                            title="Share on Facebook"
                        >
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </button>
                        <button
                            @click="copyToClipboard"
                            class="p-2.5 bg-emerald-100 text-emerald-700 rounded-xl hover:bg-emerald-200 transition-colors hover:scale-105 flex items-center gap-2"
                            :title="copySuccess ? 'Link copied!' : 'Copy link to clipboard'"
                        >
                            <svg v-if="!copySuccess" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-sm font-medium hidden lg:inline">Copy Link</span>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Article Content -->
        <section class="py-12 bg-white">
            <!-- Widest container for content area -->
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row gap-8 lg:gap-12">
                    <!-- Main Content - Wider (2/3 of container) -->
                    <article class="lg:w-2/3 lg:max-w-3xl">
                        <!-- Featured Image -->
                        <div class="mb-8 rounded-2xl overflow-hidden shadow-lg border border-gray-200">
                            <img
                                v-if="post.image_url"
                                :src="post.image_url.startsWith('http') ? post.image_url : '/storage/' + post.image_url"
                                :alt="post.title"
                                class="w-full h-64 md:h-96 object-cover hover:scale-105 transition-transform duration-700"
                            />
                            <div v-else class="w-full h-64 md:h-96 bg-gradient-to-br from-emerald-100 to-teal-100 flex items-center justify-center">
                                <div class="text-7xl text-emerald-600">📝</div>
                            </div>
                        </div>

                        <!-- Content Stats -->
                        <div class="flex items-center justify-between mb-8 p-4 bg-gray-50 rounded-xl">
                            <div class="flex items-center space-x-4 text-sm text-gray-600">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Published
                                </span>
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    {{ post.views || 0 }} views
                                </span>
                            </div>
                            <!-- Mobile share -->
                            <button @click="shareNative" class="sm:hidden p-2 bg-emerald-100 text-emerald-700 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Article Content - Wider prose container -->
                        <div class="prose prose-lg max-w-none text-gray-800 lg:max-w-3xl">
                            <div v-html="post.content"></div>
                        </div>

                        <!-- Tags -->
                        <div v-if="post.tags && post.tags.length > 0" class="mt-12 pt-8 border-t border-gray-200">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Article Topics</h4>
                            <div class="flex flex-wrap gap-2">
                                <Link
                                    v-for="tag in post.tags"
                                    :key="tag"
                                    :href="route('blog.index', { search: tag })"
                                    class="inline-flex items-center px-4 py-2 bg-emerald-50 text-emerald-700 rounded-full text-sm font-medium hover:bg-emerald-100 hover:text-emerald-800 transition-colors border border-emerald-200"
                                >
                                    <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    {{ tag }}
                                </Link>
                            </div>
                        </div>

                        <!-- Author Bio - Detailed -->
                        <div class="mt-12 bg-gray-50 rounded-2xl p-6 border border-gray-200">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">
                                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-emerald-100 to-teal-100 flex items-center justify-center text-emerald-700 text-2xl font-bold flex-shrink-0">
                                    {{ post.author?.name?.charAt(0) || 'A' }}
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="text-xl font-bold text-gray-900">{{ post.author?.name || 'Admin' }}</h4>
                                        <div class="flex space-x-2">
                                            <button class="p-2 text-gray-500 hover:text-emerald-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                </svg>
                                            </button>
                                            <button class="p-2 text-gray-500 hover:text-emerald-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <p class="text-gray-600 mb-4">
                                        Expert in AI-powered education and learning technologies with over 5 years of experience in developing innovative learning solutions.
                                    </p>
                                    <div class="flex items-center text-sm text-gray-500">
                                        <span class="flex items-center mr-4">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                                            </svg>
                                            {{ getAuthorArticleCount() }} articles
                                        </span>
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            San Francisco, CA
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Share Section -->
                        <div class="mt-12 pt-8 border-t border-gray-200">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900 mb-2">Share this article</h4>
                                    <p class="text-gray-600 text-sm">Help others discover this valuable content</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <button
                                        @click="shareOnTwitter"
                                        class="flex items-center gap-2 px-4 py-2.5 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-100 transition-colors font-medium"
                                    >
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.213c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                        </svg>
                                        <span>Twitter</span>
                                    </button>
                                    <button
                                        @click="shareOnLinkedIn"
                                        class="flex items-center gap-2 px-4 py-2.5 bg-blue-50 text-blue-700 rounded-xl hover:bg-blue-100 transition-colors font-medium"
                                    >
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                        </svg>
                                        <span>LinkedIn</span>
                                    </button>
                                    <button
                                        @click="copyToClipboard"
                                        class="flex items-center gap-2 px-4 py-2.5 bg-emerald-100 text-emerald-700 rounded-xl hover:bg-emerald-200 transition-colors font-medium"
                                        :class="copySuccess ? 'bg-emerald-200' : ''"
                                    >
                                        <svg v-if="!copySuccess" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                        <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span>{{ copySuccess ? 'Copied!' : 'Copy Link' }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Copy Success Message -->
                        <div v-if="copySuccess" class="mt-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm animate-fade-in">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Article link copied to clipboard!
                            </div>
                        </div>
                    </article>

                    <!-- Sidebar - Adjusted width for wider main content -->
                    <aside class="lg:w-1/3 lg:max-w-xs space-y-8">
                        <!-- Table of Contents -->
                        <div v-if="hasHeadings" class="bg-gray-50 rounded-2xl p-6 border border-gray-200 sticky top-24">
                            <div class="flex items-center mb-4">
                                <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center text-emerald-600 mr-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                                    </svg>
                                </div>
                                <h4 class="text-lg font-bold text-gray-900">Table of Contents</h4>
                            </div>
                            <nav class="space-y-2">
                                <a
                                    v-for="(heading, index) in headings"
                                    :key="index"
                                    :href="`#${heading.id}`"
                                    class="block px-3 py-2.5 rounded-lg text-gray-600 hover:text-emerald-700 hover:bg-white transition-colors text-sm font-medium border-l-2 border-transparent hover:border-emerald-500"
                                >
                                    {{ heading.text }}
                                </a>
                            </nav>
                        </div>

                        <!-- Related Posts -->
                        <div v-if="relatedPosts.length > 0" class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                            <div class="flex items-center mb-6">
                                <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center text-emerald-600 mr-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                                    </svg>
                                </div>
                                <h4 class="text-lg font-bold text-gray-900">Related Articles</h4>
                            </div>
                            <div class="space-y-4">
                                <article
                                    v-for="relatedPost in relatedPosts.slice(0, 3)"
                                    :key="relatedPost.id"
                                    class="group"
                                    @click="goToPost(relatedPost)"
                                >
                                    <div class="flex items-start space-x-3 cursor-pointer">
                                        <div class="w-12 h-12 flex-shrink-0 rounded-lg bg-gradient-to-br from-emerald-50 to-teal-50 flex items-center justify-center text-emerald-600 text-lg border border-emerald-100">
                                            {{ relatedPost.title.charAt(0) }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h5 class="font-medium text-gray-900 group-hover:text-emerald-700 transition-colors line-clamp-2 text-sm">
                                                {{ relatedPost.title }}
                                            </h5>
                                            <div class="flex items-center text-xs text-gray-500 mt-1">
                                                <span>{{ formatDate(relatedPost.published_at, true) }}</span>
                                                <span class="mx-2">•</span>
                                                <span>{{ getReadTime(relatedPost.content) }} min</span>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        </div>

                        <!-- Subscribe Card -->
                        <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl p-6 border border-emerald-100 shadow-sm">
                            <div class="text-center mb-6">
                                <div class="w-16 h-16 mx-auto mb-4 bg-white rounded-2xl flex items-center justify-center text-emerald-600 text-2xl border border-emerald-200">
                                    ✉️
                                </div>
                                <h4 class="text-xl font-bold text-gray-900 mb-2">Never Miss an Update</h4>
                                <p class="text-gray-600 text-sm">
                                    Get the latest articles and learning tips delivered to your inbox.
                                </p>
                            </div>
                            <form @submit.prevent="subscribeNewsletter" class="space-y-4">
                                <input
                                    type="email"
                                    v-model="newsletterEmail"
                                    placeholder="Your email address"
                                    class="w-full px-4 py-3.5 rounded-xl border border-gray-300 bg-white text-gray-700 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-transparent"
                                    required
                                >
                                <button
                                    type="submit"
                                    :disabled="newsletterLoading"
                                    class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 text-white py-3.5 rounded-xl font-semibold hover:from-emerald-700 hover:to-teal-700 transition-all duration-300 shadow-md hover:shadow-lg disabled:opacity-70"
                                >
                                    <span v-if="newsletterLoading">
                                        <svg class="animate-spin h-5 w-5 mx-auto text-white" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                                        </svg>
                                    </span>
                                    <span v-else>Subscribe Now</span>
                                </button>
                            </form>
                            <p class="text-xs text-gray-500 text-center mt-4">
                                No spam. Unsubscribe anytime.
                            </p>
                        </div>
                    </aside>
                </div>
            </div>
        </section>

        <!-- CTA Section - Lighter Green -->
        <section class="py-16 bg-gradient-to-br from-emerald-50 via-teal-50 to-white border-t border-gray-200">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div class="inline-flex items-center px-4 py-2 rounded-full bg-emerald-100 border border-emerald-200 text-emerald-700 text-sm font-medium mb-6">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Ready to Level Up?
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-6">Continue Your Learning Journey</h2>
                <p class="text-lg text-gray-600 mb-8 max-w-3xl mx-auto">
                    Explore our AI-powered courses designed to help you master new skills and achieve your goals.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <Link
                        :href="route('courses.index')"
                        class="inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-xl font-bold hover:from-emerald-700 hover:to-teal-700 transition-all duration-300 shadow-lg hover:shadow-xl hover:-translate-y-0.5"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        Explore Courses
                    </Link>
                    <Link
                        :href="route('blog.index')"
                        class="inline-flex items-center justify-center px-8 py-4 bg-white text-gray-700 rounded-xl font-bold hover:bg-gray-50 transition-all duration-300 border-2 border-gray-200 hover:border-gray-300"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                        </svg>
                        More Articles
                    </Link>
                </div>
            </div>
        </section>
    </AppLayout>
</template>

<script setup>
import { Link, Head } from '@inertiajs/vue3';
import { computed, ref, onMounted } from 'vue';
import AppLayout from '@/Layouts/GuestLayout.vue';
import MetaTags from '@/Components/MetaTags.vue';

const props = defineProps({
    post: Object,
    relatedPosts: Array,
    popularPosts: Array
});

const copySuccess = ref(false);
const newsletterEmail = ref('');
const newsletterLoading = ref(false);
const headings = ref([]);

const currentUrl = typeof window !== 'undefined' ? window.location.href : '';
const currentTitle = props.post.title;
const currentDescription = props.post.excerpt || '';

const readingTime = computed(() => {
    const words = props.post.content ? props.post.content.split(/\s+/).length : 0;
    return Math.ceil(words / 200);
});

const hasHeadings = computed(() => headings.value.length > 0);

const formatDate = (dateString, short = false) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    if (short) {
        return date.toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric'
        });
    }
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const getReadTime = (content) => {
    if (!content) return '5';
    const words = content.split(/\s+/).length;
    return Math.ceil(words / 200);
};

const getAuthorArticleCount = () => {
    // This would come from backend
    // For now, return a dummy count
    return Math.floor(Math.random() * 50) + 10;
};

const goToPost = (post) => {
    window.location.href = route('blog.show', post.slug);
};

// Social sharing functions
const shareOnTwitter = () => {
    const text = encodeURIComponent(currentTitle);
    const url = encodeURIComponent(currentUrl);
    const shareUrl = `https://twitter.com/intent/tweet?text=${text}&url=${url}`;
    window.open(shareUrl, '_blank', 'width=600,height=400');
};

const shareOnLinkedIn = () => {
    const url = encodeURIComponent(currentUrl);
    const shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${url}`;
    window.open(shareUrl, '_blank', 'width=600,height=400');
};

const shareOnFacebook = () => {
    const url = encodeURIComponent(currentUrl);
    const shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
    window.open(shareUrl, '_blank', 'width=600,height=400');
};

const copyToClipboard = async () => {
    try {
        await navigator.clipboard.writeText(currentUrl);
        copySuccess.value = true;
        setTimeout(() => {
            copySuccess.value = false;
        }, 3000);
    } catch (err) {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = currentUrl;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        copySuccess.value = true;
        setTimeout(() => {
            copySuccess.value = false;
        }, 3000);
    }
};

const shareNative = async () => {
    if (navigator.share) {
        try {
            await navigator.share({
                title: currentTitle,
                text: currentDescription,
                url: currentUrl,
            });
        } catch (err) {
            console.log('Error sharing:', err);
        }
    } else {
        copyToClipboard();
    }
};

const subscribeNewsletter = async () => {
    newsletterLoading.value = true;
    try {
        // Simulate API call
        await new Promise(resolve => setTimeout(resolve, 1500));
        alert('Thank you for subscribing to our newsletter!');
        newsletterEmail.value = '';
    } catch (error) {
        alert('Something went wrong. Please try again.');
    } finally {
        newsletterLoading.value = false;
    }
};

// Extract headings from content
const extractHeadings = () => {
    if (typeof document === 'undefined') return;

    setTimeout(() => {
        const articleContent = document.querySelector('.prose');
        if (articleContent) {
            const h2Elements = articleContent.querySelectorAll('h2, h3');
            headings.value = Array.from(h2Elements).map((heading, index) => {
                const id = `heading-${index}`;
                heading.id = id;
                return {
                    id,
                    text: heading.textContent,
                    level: heading.tagName.toLowerCase()
                };
            });
        }
    }, 100);
};

onMounted(() => {
    extractHeadings();
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

@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}

/* Prose content styling - Wider */
.prose {
    max-width: none;
}

.prose :where(p):not(:where([class~="not-prose"] *)) {
    @apply text-gray-700 leading-relaxed mb-6 text-base;
}

.prose :where(h1):not(:where([class~="not-prose"] *)) {
    @apply text-3xl font-bold text-gray-900 mb-6 mt-8;
}

.prose :where(h2):not(:where([class~="not-prose"] *)) {
    @apply text-2xl font-semibold text-gray-900 mb-4 mt-8 pt-2;
}

.prose :where(h3):not(:where([class~="not-prose"] *)) {
    @apply text-xl font-medium text-gray-900 mb-3 mt-6;
}

.prose :where(a):not(:where([class~="not-prose"] *)) {
    @apply text-emerald-600 hover:text-emerald-700 underline font-medium;
}

.prose :where(blockquote):not(:where([class~="not-prose"] *)) {
    @apply border-l-4 border-emerald-500 pl-6 italic text-gray-600 my-6 py-2 bg-emerald-50/50 rounded-r-lg text-lg;
}

.prose :where(code):not(:where([class~="not-prose"] *)) {
    @apply bg-gray-100 text-gray-800 px-2 py-1 rounded text-sm font-mono;
}

.prose :where(pre):not(:where([class~="not-prose"] *)) {
    @apply bg-gray-900 text-gray-100 p-6 rounded-lg overflow-x-auto mb-6 border border-gray-700 text-sm;
}

.prose :where(img):not(:where([class~="not-prose"] *)) {
    @apply rounded-xl shadow-md my-8;
}

.prose :where(ul):not(:where([class~="not-prose"] *)) {
    @apply list-disc pl-6 space-y-3 mb-6;
}

.prose :where(ol):not(:where([class~="not-prose"] *)) {
    @apply list-decimal pl-6 space-y-3 mb-6;
}

.prose :where(li):not(:where([class~="not-prose"] *)) {
    @apply text-gray-700;
}

.prose :where(strong):not(:where([class~="not-prose"] *)) {
    @apply font-semibold text-gray-900;
}

.prose :where(table):not(:where([class~="not-prose"] *)) {
    @apply w-full border-collapse my-6;
}

.prose :where(th):not(:where([class~="not-prose"] *)) {
    @apply border border-gray-300 px-4 py-2 bg-gray-50 text-left font-semibold;
}

.prose :where(td):not(:where([class~="not-prose"] *)) {
    @apply border border-gray-300 px-4 py-2;
}
</style>
