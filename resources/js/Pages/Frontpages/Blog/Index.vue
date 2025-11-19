<template>
    <AppLayout>
        <!-- Header -->
        <section class="relative py-20 bg-gradient-to-br from-emerald-600 to-teal-600 text-white">
            <div class="absolute inset-0 bg-black/15"></div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="text-center">
                    <h1 class="text-4xl md:text-6xl font-bold mb-6">Our Blog</h1>
                    <p class="text-xl md:text-2xl mb-8 max-w-3xl mx-auto">
                        Insights, tips, and news about AI-powered learning and education technology
                    </p>

                    <!-- Search and Filters -->
                    <div class="max-w-2xl mx-auto">
                        <div class="flex flex-col sm:flex-row gap-4">
                            <div class="flex-1">
                                <input
                                    type="text"
                                    v-model="search"
                                    placeholder="Search articles..."
                                    class="w-full px-6 py-4 rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 text-white placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white/50 shadow-sm"
                                    @keyup.enter="performSearch"
                                >
                            </div>
                            <select
                                v-model="category"
                                class="px-6 py-4 rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-white/50 shadow-sm"
                                @change="filterByCategory"
                            >
                                <option value="">All Categories</option>
                                <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured Posts -->
        <section v-if="featuredPosts.length > 0" class="py-16 bg-slate-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold text-gray-900 mb-8">Featured Articles</h2>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <article
                        v-for="post in featuredPosts"
                        :key="post.id"
                        class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-500 group"
                    >
                        <div class="h-48 relative overflow-hidden">
                            <img
                                v-if="post.image_url"
                                :src="post.image_url"
                                :alt="post.title"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                            />
                            <div v-else class="absolute inset-0 bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white text-4xl">
                                📝
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
                            <h3 class="text-xl font-bold text-gray-900 mb-3 line-clamp-2 group-hover:text-emerald-600 transition-colors">
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
                                    class="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-medium transition-colors group"
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
            </div>
        </section>

        <!-- Main Blog Content -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row gap-12">
                    <!-- Blog Posts -->
                    <div class="lg:w-2/3">
                        <h2 class="text-3xl font-bold text-gray-900 mb-8">Latest Articles</h2>

                        <div v-if="posts.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <article
                                v-for="post in posts.data"
                                :key="post.id"
                                class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-500 group"
                            >
                                <div class="h-48 relative overflow-hidden">
                                    <img
                                        v-if="post.image_url"
                                        :src="post.image_url"
                                        :alt="post.title"
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                    />
                                    <div v-else class="absolute inset-0 bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white text-4xl">
                                        📝
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
                                    <h3 class="text-xl font-bold text-gray-900 mb-3 line-clamp-2 group-hover:text-emerald-600 transition-colors">
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
                                            class="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-medium transition-colors group"
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
                        <div v-else class="text-center py-12">
                            <div class="w-24 h-24 mx-auto mb-4 text-gray-400">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-600 mb-2">No Blog Posts Found</h3>
                            <p class="text-gray-500">Try adjusting your search criteria or check back later.</p>
                        </div>

                        <!-- Pagination -->
                        <div v-if="posts.data.length > 0" class="mt-12 flex justify-center">
                            <div class="flex space-x-2">
                                <button
                                    v-for="(link, index) in posts.links"
                                    :key="index"
                                    @click="loadPage(link.url)"
                                    class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors"
                                    :class="{
                                        'bg-emerald-600 text-white border-emerald-600': link.active,
                                        'opacity-50 cursor-not-allowed': !link.url
                                    }"
                                    :disabled="!link.url"
                                >
                                    <span v-html="link.label"></span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="lg:w-1/3">
                        <!-- Categories -->
                        <div class="bg-emerald-50 rounded-2xl p-6 mb-8 border border-emerald-100">
                            <h3 class="text-xl font-bold text-gray-900 mb-4">Categories</h3>
                            <ul class="space-y-2">
                                <li v-for="cat in categories" :key="cat">
                                    <button
                                        @click="setCategory(cat)"
                                        class="w-full text-left px-4 py-2 rounded-lg hover:bg-white transition-colors text-gray-700"
                                        :class="{ 'bg-white text-emerald-600 font-medium shadow-sm': category === cat }"
                                    >
                                        {{ cat }}
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <!-- Newsletter -->
                        <div class="bg-gradient-to-br from-emerald-600 to-teal-600 rounded-2xl p-6 text-white">
                            <h3 class="text-xl font-bold mb-3">Stay Updated</h3>
                            <p class="text-emerald-100 mb-4">Get the latest articles and learning tips delivered to your inbox.</p>
                            <form @submit.prevent="subscribeNewsletter" class="space-y-3">
                                <input
                                    type="email"
                                    v-model="newsletterEmail"
                                    placeholder="Your email address"
                                    class="w-full px-4 py-3 rounded-lg bg-white/10 backdrop-blur-sm border border-white/20 text-white placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white/50 shadow-sm"
                                    required
                                >
                                <button
                                    type="submit"
                                    class="w-full bg-white text-emerald-600 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors shadow-md hover:shadow-lg"
                                    :disabled="newsletterLoading"
                                >
                                    <span v-if="newsletterLoading">Subscribing...</span>
                                    <span v-else>Subscribe</span>
                                </button>
                            </form>
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
const newsletterEmail = ref('');
const newsletterLoading = ref(false);

const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const performSearch = () => {
    router.get(route('blog.index'), {
        search: search.value,
        category: category.value
    }, {
        preserveState: true,
        replace: true
    });
};

const filterByCategory = () => {
    router.get(route('blog.index'), {
        search: search.value,
        category: category.value
    }, {
        preserveState: true,
        replace: true
    });
};

const setCategory = (cat) => {
    category.value = cat;
    filterByCategory();
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
    // Simulate API call
    setTimeout(() => {
        alert('Thank you for subscribing to our newsletter!');
        newsletterEmail.value = '';
        newsletterLoading.value = false;
    }, 1000);
};

// Watch for changes in search with debounce
let searchTimeout;
watch(search, (newValue) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        performSearch();
    }, 500);
});
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
</style>
