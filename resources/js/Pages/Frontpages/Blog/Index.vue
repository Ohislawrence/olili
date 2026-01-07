<template>
    <AppLayout>
        <Head title="Blogpost Updates" />
        <!-- Header - Lighter Green -->
        <section class="relative py-16 bg-gradient-to-br from-emerald-50 via-teal-50 to-white">
            <!-- Decorative elements -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute -top-20 -right-20 w-64 h-64 bg-emerald-100 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob"></div>
                <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-teal-100 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-2000"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <!-- Breadcrumb -->
                <nav class="mb-8">
                    <ol class="flex items-center space-x-2 text-sm">
                        <li>
                            <Link :href="route('welcome')" class="text-emerald-600 hover:text-emerald-700 transition-colors">
                                Home
                            </Link>
                        </li>
                        <li class="text-gray-400">/</li>
                        <li class="font-medium text-gray-700">Blog</li>
                    </ol>
                </nav>

                <div class="text-center max-w-4xl mx-auto">
                    <!-- Badge -->
                    <div class="inline-flex items-center px-4 py-2 rounded-full bg-emerald-100 border border-emerald-200 text-emerald-700 text-sm font-medium mb-6">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                        </svg>
                        Insights & Updates
                    </div>

                    <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">Learning Insights</h1>
                    <p class="text-xl text-gray-600 mb-10 max-w-3xl mx-auto leading-relaxed">
                        Discover articles on AI-powered learning, education technology, and practical tips for learners and educators.
                    </p>

                    <!-- Search and Filters - Cleaner design -->
                    <div class="max-w-2xl mx-auto bg-white rounded-2xl p-2 border border-gray-200 shadow-sm">
                        <div class="flex flex-col sm:flex-row gap-2">
                            <div class="flex-1 relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input
                                    type="text"
                                    v-model="search"
                                    placeholder="Search articles by keyword..."
                                    class="w-full pl-12 pr-4 py-3.5 rounded-xl border-0 focus:ring-2 focus:ring-emerald-500/30 focus:ring-offset-2 text-gray-700 placeholder-gray-500 bg-transparent"
                                    @keyup.enter="performSearch"
                                >
                            </div>
                            <div class="relative">
                                <select
                                    v-model="category"
                                    class="appearance-none w-full sm:w-auto px-4 py-3.5 pr-10 rounded-xl border-0 bg-gray-50 text-gray-700 focus:ring-2 focus:ring-emerald-500/30 focus:ring-offset-2 focus:outline-none"
                                    @change="filterByCategory"
                                >
                                    <option value="">All Categories</option>
                                    <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick filters -->
                    <div class="mt-6 flex flex-wrap justify-center gap-2">
                        <button
                            v-for="cat in categories.slice(0, 5)"
                            :key="cat"
                            @click="setCategory(cat)"
                            :class="[
                                'px-4 py-2 rounded-full text-sm font-medium transition-all duration-200',
                                category === cat
                                    ? 'bg-emerald-600 text-white shadow-sm'
                                    : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200 hover:border-gray-300'
                            ]"
                        >
                            {{ cat }}
                        </button>
                        <button
                            v-if="category"
                            @click="clearFilters"
                            class="px-4 py-2 rounded-full text-sm font-medium bg-gray-100 text-gray-600 hover:bg-gray-200 border border-gray-200 transition-colors"
                        >
                            Clear filters
                        </button>
                    </div>
                </div>
            </div>
        </section>



        <!-- Main Blog Content -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row gap-12">
                    <!-- Blog Posts -->
                    <div class="lg:w-2/3">
                        <!-- Header with stats -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8">
                            <div>
                                <h2 class="text-3xl font-bold text-gray-900">Latest Articles</h2>
                                <p class="text-gray-600 mt-2">
                                    {{ posts.total }} articles published
                                    <span v-if="category"> in "{{ category }}"</span>
                                </p>
                            </div>
                            <div class="mt-4 sm:mt-0 flex items-center space-x-2">
                                <span class="text-sm text-gray-500">Sort by:</span>
                                <select
                                    v-model="sortBy"
                                    class="px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/30"
                                    @change="performSearch"
                                >
                                    <option value="latest">Latest</option>
                                    <option value="popular">Most Popular</option>
                                    <option value="trending">Trending</option>
                                </select>
                            </div>
                        </div>

                        <!-- Articles Grid -->
                        <div v-if="posts.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <article
                                v-for="post in posts.data"
                                :key="post.id"
                                class="group bg-white border border-gray-100 rounded-2xl overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1"
                            >
                                <!-- Image -->
                                <div class="h-48 relative overflow-hidden">
                                    <img
                                        v-if="post.image_url"
                                        :src="post.image_url.startsWith('http') ? post.image_url : '/storage/' + post.image_url"
                                        :alt="post.title"
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                                    />
                                    <div v-else class="absolute inset-0 bg-gradient-to-br from-emerald-100 to-teal-100 flex items-center justify-center">
                                        <div class="text-5xl text-emerald-600">📝</div>
                                    </div>
                                    <!-- Category badge -->
                                    <div class="absolute top-4 left-4">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white/90 backdrop-blur-sm text-emerald-700 border border-emerald-200 shadow-sm">
                                            {{ post.category }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="p-6">
                                    <!-- Meta info -->
                                    <div class="flex items-center text-sm text-gray-500 mb-4">
                                        <span class="flex items-center mr-4">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            {{ formatDate(post.published_at) }}
                                        </span>
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            {{ post.views || '0' }} views
                                        </span>
                                    </div>

                                    <!-- Title -->
                                    <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-emerald-700 transition-colors line-clamp-2">
                                        {{ post.title }}
                                    </h3>

                                    <!-- Excerpt -->
                                    <p class="text-gray-600 mb-4 leading-relaxed line-clamp-3">
                                        {{ post.excerpt }}
                                    </p>

                                    <!-- Footer -->
                                    <div class="flex items-center justify-between mt-6 pt-6 border-t border-gray-100">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-100 to-teal-100 flex items-center justify-center text-emerald-700 font-medium text-xs mr-2">
                                                {{ post.author?.name?.charAt(0) || 'A' }}
                                            </div>
                                            <span class="text-sm text-gray-700">{{ post.author?.name || 'Admin' }}</span>
                                        </div>
                                        <Link
                                            :href="route('blog.show', post.slug)"
                                            class="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-medium text-sm transition-colors group"
                                        >
                                            Read More
                                            <svg class="ml-1 w-4 h-4 transform transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                            </svg>
                                        </Link>
                                    </div>
                                </div>
                            </article>
                        </div>

                        <!-- Empty State -->
                        <div v-else class="text-center py-16">
                            <div class="w-24 h-24 mx-auto mb-6 text-gray-300">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-semibold text-gray-600 mb-2">No Articles Found</h3>
                            <p class="text-gray-500 max-w-md mx-auto mb-6">
                                We couldn't find any articles matching your criteria. Try different keywords or categories.
                            </p>
                            <button
                                @click="clearFilters"
                                class="inline-flex items-center px-6 py-3 bg-emerald-600 text-white rounded-xl font-medium hover:bg-emerald-700 transition-colors"
                            >
                                Clear All Filters
                                <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Pagination - Improved -->
                        <div v-if="posts.data.length > 0 && posts.links.length > 3" class="mt-12">
                            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                                <div class="text-sm text-gray-600">
                                    Showing {{ posts.from || 0 }} to {{ posts.to || 0 }} of {{ posts.total }} articles
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button
                                        @click="loadPage(posts.prev_page_url)"
                                        :disabled="!posts.prev_page_url"
                                        class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center"
                                    >
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                        </svg>
                                        Previous
                                    </button>

                                    <div class="flex space-x-1">
                                        <button
                                            v-for="(link, index) in posts.links.slice(1, -1)"
                                            :key="index"
                                            @click="loadPage(link.url)"
                                            class="w-10 h-10 flex items-center justify-center rounded-lg border transition-colors"
                                            :class="link.active
                                                ? 'bg-emerald-600 text-white border-emerald-600'
                                                : 'border-gray-300 text-gray-700 hover:bg-gray-50'"
                                        >
                                            {{ link.label }}
                                        </button>
                                    </div>

                                    <button
                                        @click="loadPage(posts.next_page_url)"
                                        :disabled="!posts.next_page_url"
                                        class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center"
                                    >
                                        Next
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="lg:w-1/3">
                        <!-- Categories Widget -->
                        <div class="bg-white rounded-2xl p-6 mb-8 border border-gray-200 shadow-sm">
                            <div class="flex items-center mb-6">
                                <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600 mr-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900">Categories</h3>
                            </div>
                            <ul class="space-y-2">
                                <li v-for="cat in categories" :key="cat">
                                    <button
                                        @click="setCategory(cat)"
                                        :class="[
                                            'w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-200',
                                            category === cat
                                                ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
                                                : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'
                                        ]"
                                    >
                                        <div class="flex items-center">
                                            <div :class="[
                                                'w-2 h-2 rounded-full mr-3',
                                                category === cat ? 'bg-emerald-500' : 'bg-gray-300'
                                            ]"></div>
                                            <span class="font-medium">{{ cat }}</span>
                                        </div>
                                        <span class="text-sm text-gray-500 bg-gray-100 px-2 py-1 rounded-lg">
                                            {{ getCategoryCount(cat) }}
                                        </span>
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <!-- Newsletter Widget - Lighter green -->
                        <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl p-6 border border-emerald-100 shadow-sm mb-8">
                            <div class="text-center mb-6">
                                <div class="w-16 h-16 mx-auto mb-4 bg-white rounded-2xl flex items-center justify-center text-emerald-600 text-2xl shadow-sm">
                                    ✉️
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 mb-2">Stay Updated</h3>
                                <p class="text-gray-600 text-sm">
                                    Get weekly insights, tips, and updates delivered to your inbox.
                                </p>
                            </div>
                            <form @submit.prevent="subscribeNewsletter" class="space-y-4">
                                <input
                                    type="email"
                                    v-model="newsletterEmail"
                                    placeholder="Your email address"
                                    class="w-full px-4 py-3.5 rounded-xl border border-gray-300 bg-white text-gray-700 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-transparent shadow-sm"
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

                        <!-- Popular Articles -->
                        <div v-if="featuredPosts.length > 0" class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm">
                            <h3 class="text-xl font-bold text-gray-900 mb-6">Popular Reads</h3>
                            <div class="space-y-4">
                                <article
                                    v-for="post in featuredPosts.slice(0, 3)"
                                    :key="post.id"
                                    class="flex items-start space-x-3 group cursor-pointer"
                                >
                                    <div class="w-16 h-16 flex-shrink-0 rounded-xl bg-gradient-to-br from-emerald-100 to-teal-100 flex items-center justify-center text-emerald-600 text-lg">
                                        {{ post.title.charAt(0) }}
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900 group-hover:text-emerald-600 transition-colors line-clamp-2 mb-1">
                                            {{ post.title }}
                                        </h4>
                                        <div class="flex items-center text-xs text-gray-500">
                                            <span>{{ formatDate(post.published_at, true) }}</span>
                                            <span class="mx-2">•</span>
                                            <span>{{ post.read_time || '5 min' }} read</span>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </AppLayout>
</template>

<script setup>
import { Link, router, Head } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AppLayout from '@/Layouts/GuestLayout.vue';

const props = defineProps({
    posts: Object,
    categories: Array,
    featuredPosts: Array,
    filters: Object
});

const search = ref(props.filters.search || '');
const category = ref(props.filters.category || '');
const sortBy = ref(props.filters.sort || 'latest');
const newsletterEmail = ref('');
const newsletterLoading = ref(false);

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

const getCategoryCount = (cat) => {
    // This would ideally come from backend
    // For now, return a dummy count
    return Math.floor(Math.random() * 20) + 5;
};

const performSearch = () => {
    router.get(route('blog.index'), {
        search: search.value,
        category: category.value,
        sort: sortBy.value
    }, {
        preserveState: true,
        replace: true
    });
};

const filterByCategory = () => {
    performSearch();
};

const setCategory = (cat) => {
    category.value = cat;
    performSearch();
};

const clearFilters = () => {
    search.value = '';
    category.value = '';
    sortBy.value = 'latest';
    performSearch();
};

const loadPage = (url) => {
    if (url) {
        router.visit(url, {
            preserveState: true,
            preserveScroll: true
        });
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

// Watch for changes with debounce
let searchTimeout;
watch([search, sortBy], () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        performSearch();
    }, 500);
}, { deep: true });
</script>

<style scoped>
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
</style>
