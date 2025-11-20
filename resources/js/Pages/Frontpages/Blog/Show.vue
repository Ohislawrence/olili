<template>
    <MetaTags
        :title="post.title"
        :description="post.excerpt"
        :image="post.image_url"
        type="article"
    />
    <AppLayout>
        <Head :title="`${post.title}`" />

        <!-- Article Header -->
        <section class="relative py-20 bg-gradient-to-br from-emerald-600 to-teal-600 text-white">
            <div class="absolute inset-0 bg-black/20"></div>
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="text-center">
                    <div class="inline-flex items-center px-4 py-2 rounded-full bg-white/20 backdrop-blur-sm border border-white/30 text-white text-sm font-medium mb-6">
                        {{ post.category }}
                    </div>
                    <h1 class="text-4xl md:text-5xl font-bold mb-6 leading-tight">{{ post.title }}</h1>
                    <p class="text-xl text-emerald-100 mb-8 max-w-2xl mx-auto">{{ post.excerpt }}</p>

                    <div class="flex flex-col sm:flex-row items-center justify-center gap-6 text-emerald-100">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            By {{ post.author?.name || 'Admin' }}
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            {{ formatDate(post.published_at) }}
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ readingTime }} min read
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Article Content -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row gap-12">
                    <!-- Main Content -->
                    <article class="lg:w-2/3">
                        <!-- Featured Image -->
                        <div class="mb-8 rounded-2xl overflow-hidden shadow-sm">
                            <img
                                v-if="post.image_url"
                                :src="post.image_url"
                                :alt="post.title"
                                class="w-full h-64 md:h-96 object-cover"
                            />
                            <div v-else class="w-full h-64 md:h-96 bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white text-6xl">
                                📝
                            </div>
                        </div>

                        <!-- Article Content -->
                        <div class="prose prose-lg max-w-none text-gray-800">
                            <div v-html="post.content"></div>
                        </div>

                        <!-- Tags -->
                        <div v-if="post.tags && post.tags.length > 0" class="mt-8 pt-8 border-t border-gray-200">
                            <div class="flex flex-wrap gap-2">
                                <span
                                    v-for="tag in post.tags"
                                    :key="tag"
                                    class="px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full text-sm font-medium"
                                >
                                    #{{ tag }}
                                </span>
                            </div>
                        </div>

                        <!-- Share Buttons -->
                        <div class="mt-8 pt-8 border-t border-gray-200">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Share this article</h4>
                            <div class="flex space-x-4">
                                <button class="p-3 bg-blue-500 text-white rounded-xl hover:bg-blue-600 transition-colors shadow-sm hover:shadow-md">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"></path>
                                    </svg>
                                </button>
                                <button class="p-3 bg-blue-800 text-white rounded-xl hover:bg-blue-900 transition-colors shadow-sm hover:shadow-md">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"></path>
                                    </svg>
                                </button>
                                <button class="p-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors shadow-sm hover:shadow-md">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M18.77 7.46H14.5v-1.9c0-.9.6-1.1 1-1.1h3V.5h-4.33C10.24.5 9.5 3.44 9.5 5.32v2.15h-3v4h3v12h5v-12h3.85l.42-4z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </article>

                    <!-- Sidebar -->
                    <aside class="lg:w-1/3">
                        <!-- Author Bio -->
                        <div class="bg-emerald-50 rounded-2xl p-6 mb-8 border border-emerald-100">
                            <div class="flex items-center mb-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                    {{ post.author?.name?.charAt(0) || 'A' }}
                                </div>
                                <div class="ml-4">
                                    <h4 class="font-semibold text-gray-900">{{ post.author?.name || 'Admin' }}</h4>
                                    <p class="text-gray-600 text-sm">Author</p>
                                </div>
                            </div>
                            <p class="text-gray-700 text-sm">
                                Expert in AI-powered education and learning technologies.
                            </p>
                        </div>

                        <!-- Related Posts -->
                        <div v-if="relatedPosts.length > 0" class="bg-white rounded-2xl border border-gray-200 p-6 mb-8 shadow-sm">
                            <h4 class="text-lg font-bold text-gray-900 mb-4">Related Articles</h4>
                            <div class="space-y-4">
                                <article
                                    v-for="relatedPost in relatedPosts"
                                    :key="relatedPost.id"
                                    class="flex items-start space-x-3 group cursor-pointer"
                                    @click="goToPost(relatedPost)"
                                >
                                    <div class="w-16 h-16 flex-shrink-0 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white text-lg">
                                        📝
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h5 class="font-medium text-gray-900 group-hover:text-emerald-600 transition-colors line-clamp-2 text-sm">
                                            {{ relatedPost.title }}
                                        </h5>
                                        <p class="text-gray-500 text-xs mt-1">{{ formatDate(relatedPost.published_at) }}</p>
                                    </div>
                                </article>
                            </div>
                        </div>

                        <!-- Popular Posts -->
                        <div v-if="popularPosts.length > 0" class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                            <h4 class="text-lg font-bold text-gray-900 mb-4">Popular Articles</h4>
                            <div class="space-y-4">
                                <article
                                    v-for="popularPost in popularPosts"
                                    :key="popularPost.id"
                                    class="flex items-start space-x-3 group cursor-pointer"
                                    @click="goToPost(popularPost)"
                                >
                                    <div class="w-16 h-16 flex-shrink-0 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white text-lg">
                                        📈
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h5 class="font-medium text-gray-900 group-hover:text-emerald-600 transition-colors line-clamp-2 text-sm">
                                            {{ popularPost.title }}
                                        </h5>
                                        <p class="text-gray-500 text-xs mt-1">{{ formatDate(popularPost.published_at) }}</p>
                                    </div>
                                </article>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-16 bg-gradient-to-br from-emerald-600 to-teal-600 text-white">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-3xl font-bold mb-6">Ready to Transform Your Learning?</h2>
                <p class="text-xl text-emerald-100 mb-8">
                    Join thousands of learners who are already using Olilearn to achieve their goals.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <Link
                        :href="route('register')"
                        class="bg-white text-emerald-700 px-8 py-4 rounded-xl font-bold hover:bg-emerald-50 transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl"
                    >
                        Start Learning Free
                    </Link>
                    <Link
                        :href="route('courses.index')"
                        class="bg-transparent border-2 border-white text-white px-8 py-4 rounded-xl font-bold hover:bg-white/10 transform hover:scale-105 transition-all duration-300"
                    >
                        Browse Courses
                    </Link>
                </div>
            </div>
        </section>
    </AppLayout>
</template>

<script setup>
import { Link, Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '@/Layouts/GuestLayout.vue';
import MetaTags from '@/Components/MetaTags.vue';

const props = defineProps({
    post: Object,
    relatedPosts: Array,
    popularPosts: Array
});

const readingTime = computed(() => {
    const words = props.post.content ? props.post.content.split(/\s+/).length : 0;
    return Math.ceil(words / 200); // Average reading speed: 200 words per minute
});

const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const goToPost = (post) => {
    // Navigate to the clicked post
    window.location.href = route('blog.show', post.slug);
};
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.prose {
    max-width: none;
}

.prose :where(p):not(:where([class~="not-prose"] *)) {
    @apply text-gray-700 leading-relaxed;
}

.prose :where(h1):not(:where([class~="not-prose"] *)) {
    @apply text-3xl font-bold text-gray-900 mb-6;
}

.prose :where(h2):not(:where([class~="not-prose"] *)) {
    @apply text-2xl font-semibold text-gray-900 mb-4 mt-8;
}

.prose :where(h3):not(:where([class~="not-prose"] *)) {
    @apply text-xl font-medium text-gray-900 mb-3 mt-6;
}

.prose :where(a):not(:where([class~="not-prose"] *)) {
    @apply text-emerald-600 hover:text-emerald-700 underline;
}

.prose :where(blockquote):not(:where([class~="not-prose"] *)) {
    @apply border-l-4 border-emerald-500 pl-4 italic text-gray-600 my-6;
}

.prose :where(code):not(:where([class~="not-prose"] *)) {
    @apply bg-gray-100 text-gray-800 px-2 py-1 rounded text-sm;
}

.prose :where(pre):not(:where([class~="not-prose"] *)) {
    @apply bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto mb-6;
}
</style>
