<template>
    <MetaTags
        title="Community - Olilearn"
        description="Join the Olilearn community to connect with learners, share achievements, and discuss AI-powered courses."
        image="/images/community.png"
    />
    <AppLayout>
        <Head title="Community - Connect, Share, Learn" />
        
        <!-- Hero Section -->
        <section class="relative py-16 bg-gradient-to-br from-emerald-600 to-teal-600 text-white overflow-hidden">
            <div class="absolute inset-0">
                <div class="absolute top-10 left-10 w-20 h-20 bg-white/10 rounded-full animate-pulse-slow"></div>
                <div class="absolute top-1/4 right-20 w-32 h-32 bg-white/5 rounded-full animate-float"></div>
                <div class="absolute bottom-20 left-1/3 w-24 h-24 bg-white/10 rounded-full animate-bounce-slow"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="text-center">
                    <div class="inline-flex items-center px-4 py-2 rounded-full bg-white/20 backdrop-blur-sm border border-white/30 text-white text-sm font-medium mb-6">
                        <span class="w-2 h-2 bg-white rounded-full mr-2 animate-pulse"></span>
                        Connect & Learn Together
                    </div>
                    <h1 class="text-4xl md:text-6xl font-bold mb-6 leading-tight">
                        <span class="block bg-gradient-to-r from-white to-emerald-200 bg-clip-text text-transparent">
                            Community
                        </span>
                    </h1>
                    <p class="text-xl md:text-2xl mb-8 text-emerald-100 max-w-3xl mx-auto">
                        Share your journey, ask questions, and connect with other learners.
                    </p>
                    
                    <!-- Search Bar -->
                    <div class="max-w-2xl mx-auto">
                        <div class="relative">
                            <input
                                v-model="search"
                                @keyup.enter="applyFilters"
                                type="text"
                                placeholder="Search discussions, questions, or resources..."
                                class="w-full px-6 py-4 rounded-2xl bg-white/10 backdrop-blur-sm border border-white/30 text-white placeholder-emerald-200 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent"
                            />
                            <button
                                @click="applyFilters"
                                class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-white text-emerald-700 px-6 py-2 rounded-xl font-medium hover:bg-emerald-50 transition-colors"
                            >
                                Search
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Content -->
        <section class="py-12 bg-gradient-to-br from-slate-50 to-emerald-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                    <!-- Sidebar -->
                    <div class="lg:col-span-1 space-y-6">
                        <!-- Create Post Button -->
                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('community.profile.followers', 2)"
                            class="block w-full bg-gradient-to-r from-emerald-600 to-teal-600 text-white px-6 py-4 rounded-2xl font-bold text-center hover:from-emerald-700 hover:to-teal-700 transform transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-xl"
                        >
                            <PencilSquareIcon class="h-5 w-5 inline mr-2" />
                            Create Post
                        </Link>
                        <div
                            v-else
                            class="block w-full bg-gradient-to-r from-emerald-600 to-teal-600 text-white px-6 py-4 rounded-2xl font-bold text-center relative group cursor-pointer"
                            @click="showLoginPrompt = true"
                        >
                            <PencilSquareIcon class="h-5 w-5 inline mr-2" />
                            Create Post
                            <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 rounded-2xl transition-opacity"></div>
                        </div>

                        <!-- Filters -->
                        <div class="bg-white rounded-2xl p-6 shadow-sm">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Filters</h3>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Post Type</label>
                                    <select
                                        v-model="filters.type"
                                        @change="applyFilters"
                                        class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                    >
                                        <option value="all">All Posts</option>
                                        <option value="discussion">Discussions</option>
                                        <option value="question">Questions</option>
                                        <option value="achievement">Achievements</option>
                                        <option value="resource">Resources</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                                    <select
                                        v-model="filters.sort"
                                        @change="applyFilters"
                                        class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                    >
                                        <option value="recent">Most Recent</option>
                                        <option value="popular">Most Popular</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Top Users -->
                        <div class="bg-white rounded-2xl p-6 shadow-sm">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Top Contributors</h3>
                            <div class="space-y-4">
                                <div
                                    v-for="user in topUsers"
                                    :key="user.id"
                                    class="flex items-center space-x-3 p-3 hover:bg-emerald-50 rounded-xl transition-colors"
                                >
                                    <img
                                        :src="user.profile_photo_url"
                                        :alt="user.name"
                                        class="w-10 h-10 rounded-full"
                                    />
                                    <div class="flex-1 min-w-0">
                                        <Link
                                            :href="route('community.profile.show', user.id)"
                                            class="text-sm font-medium text-gray-900 hover:text-emerald-700 truncate"
                                        >
                                            {{ user.name }}
                                        </Link>
                                        <p class="text-xs text-gray-500">{{ user.community_posts_count }} posts</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Popular Tags -->
                        <div class="bg-white rounded-2xl p-6 shadow-sm">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Popular Tags</h3>
                            <div class="flex flex-wrap gap-2">
                                <span
                                    v-for="tag in popularTags"
                                    :key="tag"
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 hover:bg-emerald-200 cursor-pointer"
                                    @click="searchByTag(tag)"
                                >
                                    {{ tag }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Posts List -->
                    <div class="lg:col-span-3">
                        <!-- Pinned Posts -->
                        <div v-if="pinnedPosts.length > 0" class="mb-8">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4">📌 Pinned Posts</h2>
                            <div class="space-y-4">
                                <CommunityPostCard
                                    v-for="post in pinnedPosts"
                                    :key="post.id"
                                    :post="post"
                                />
                            </div>
                        </div>

                        <!-- All Posts -->
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 mb-4">
                                {{ filters.type === 'all' ? 'All Posts' : filters.type.charAt(0).toUpperCase() + filters.type.slice(1) }}
                            </h2>
                            
                            <div v-if="posts.data.length === 0" class="text-center py-12">
                                <div class="text-gray-400 mb-4">
                                    <ChatBubbleLeftRightIcon class="h-16 w-16 mx-auto" />
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No posts found</h3>
                                <p class="text-gray-600">Be the first to start a discussion!</p>
                            </div>

                            <div v-else class="space-y-4">
                                <CommunityPostCard
                                    v-for="post in posts.data"
                                    :key="post.id"
                                    :post="post"
                                />
                            </div>

                            <!-- Pagination -->
                            <div v-if="posts.links" class="mt-8">
                                <Pagination :links="posts.links" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Login Modal -->
    <div v-if="showLoginPrompt" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full">
            <div class="text-center mb-6">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-emerald-100 flex items-center justify-center">
                    <LockClosedIcon class="h-8 w-8 text-emerald-600" />
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Join the Conversation</h3>
                <p class="text-gray-600">
                    Sign in to create posts, like content, and connect with other learners.
                </p>
            </div>
            
            <div class="space-y-4">
                <Link
                    :href="route('login')"
                    class="block w-full bg-gradient-to-r from-emerald-600 to-teal-600 text-white px-6 py-3 rounded-xl font-bold text-center hover:from-emerald-700 hover:to-teal-700"
                >
                    Sign In
                </Link>
                <Link
                    :href="route('register')"
                    class="block w-full border-2 border-emerald-600 text-emerald-600 px-6 py-3 rounded-xl font-bold text-center hover:bg-emerald-50"
                >
                    Create Account
                </Link>
                <button
                    @click="showLoginPrompt = false"
                    class="block w-full text-gray-600 hover:text-gray-800 px-6 py-3"
                >
                    Continue Browsing
                </button>
            </div>
        </div>
    </div>
    </AppLayout>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/GuestLayout.vue';
import MetaTags from '@/Components/MetaTags.vue';
import CommunityPostCard from '@/Components/Community/PostCard.vue';
import Pagination from '@/Components/Pagination.vue';
import { LockClosedIcon } from '@heroicons/vue/24/outline';
import {
    PencilSquareIcon,
    ChatBubbleLeftRightIcon
} from '@heroicons/vue/24/outline';


const showLoginPrompt = ref(false);

const props = defineProps({
    posts: Object,
    topUsers: Array,
    popularTags: Array,
    filters: Object,
});

const search = ref(props.filters.search || '');
const filters = ref({
    type: props.filters.type || 'all',
    sort: props.filters.sort || 'recent',
});

const pinnedPosts = computed(() => {
    return props.posts.data.filter(post => post.is_pinned);
});

function applyFilters() {
    router.get(route('community.index'), {
        ...filters.value,
        search: search.value,
    });
}

function searchByTag(tag) {
    search.value = tag;
    applyFilters();
}

// Debounce search
let searchTimeout;
watch(search, (newValue) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 500);
});
</script>