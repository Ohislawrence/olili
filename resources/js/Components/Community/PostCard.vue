<template>
    <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100">
        <!-- Post Header -->
        <div class="flex items-start justify-between mb-4">
            <div class="flex items-center space-x-3">
                <Link :href="route('community.profile.show', post.user.id)">
                    <img
                        :src="post.user.profile_photo_url"
                        :alt="post.user.name"
                        class="w-10 h-10 rounded-full"
                    />
                </Link>
                <div>
                    <div class="flex items-center space-x-2">
                        <Link
                            :href="route('community.profile.show', post.user.id)"
                            class="font-bold text-gray-900 hover:text-emerald-700"
                        >
                            {{ post.user.name }}
                        </Link>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                            :class="postTypeClasses[post.type]">
                            {{ post.type }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-500">{{ post.time_ago }}</p>
                </div>
            </div>
            <div v-if="post.is_pinned" class="text-emerald-600">
                <span class="text-xs font-medium px-2 py-1 bg-emerald-100 rounded-full">📌 Pinned</span>
            </div>
        </div>

        <!-- Post Content -->
        <Link :href="route('community.posts.show', post.id)">
            <h3 class="text-xl font-bold text-gray-900 mb-3 hover:text-emerald-700 transition-colors">
                {{ post.title }}
            </h3>
            <p class="text-gray-700 mb-4 line-clamp-3">{{ post.excerpt }}</p>
        </Link>

        <!-- Tags -->
        <div v-if="post.tags && post.tags.length > 0" class="mb-4">
            <div class="flex flex-wrap gap-2">
                <span
                    v-for="tag in post.tags"
                    :key="tag"
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                >
                    {{ tag }}
                </span>
            </div>
        </div>

        <!-- Post Footer -->
        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
            <div class="flex items-center space-x-6">
                <!-- Likes -->
                <button
        v-if="$page.props.auth.user"
        @click="toggleLike"
        class="flex items-center space-x-2 text-gray-500 hover:text-emerald-600 transition-colors"
    >
        <HeartIcon
            :class="[
                'h-5 w-5',
                isLiked ? 'text-red-500 fill-current' : ''
            ]"
        />
        <span>{{ post.like_count }} likes</span>
    </button>
    <div
        v-else
        @click="requireLogin"
        class="flex items-center space-x-2 text-gray-500 cursor-pointer hover:text-emerald-600 transition-colors"
    >
        <HeartIcon class="h-5 w-5" />
        <span>{{ post.like_count }} likes</span>
    </div>

                <!-- Comments -->
                <Link
                    :href="route('community.posts.show', post.id)"
                    class="flex items-center space-x-2 text-gray-500 hover:text-emerald-600 transition-colors"
                >
                    <ChatBubbleLeftRightIcon class="h-5 w-5" />
                    <span>{{ post.comment_count }} comments</span>
                </Link>

                <!-- Views -->
                <div class="flex items-center space-x-2 text-gray-500">
                    <EyeIcon class="h-5 w-5" />
                    <span>{{ post.views }} views</span>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <Link
                    v-if="post.course"
                    :href="route('courses.show', post.course.id)"
                    class="text-sm text-emerald-600 hover:text-emerald-700 font-medium"
                >
                    {{ post.course.title }}
                </Link>
                <Link
                    :href="route('community.posts.show', post.id)"
                    class="text-sm text-gray-600 hover:text-emerald-700 font-medium"
                >
                    Read more →
                </Link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import {
    HeartIcon,
    ChatBubbleLeftRightIcon,
    EyeIcon
} from '@heroicons/vue/24/outline';


const page = usePage();

const props = defineProps({
    post: Object,
});

function requireLogin() {
    if (!page.props.auth.user) {
        // Show login modal or redirect
        if (typeof window.showLoginModal === 'function') {
            window.showLoginModal();
        } else {
            window.location.href = '/login?redirect=' + encodeURIComponent(window.location.pathname);
        }
    }
}

const postTypeClasses = {
    discussion: 'bg-blue-100 text-blue-800',
    question: 'bg-purple-100 text-purple-800',
    achievement: 'bg-emerald-100 text-emerald-800',
    resource: 'bg-amber-100 text-amber-800',
};

const isLiked = ref(false);

function toggleLike() {
    // Implement like functionality
    if (isLiked.value) {
        router.delete(route('community.posts.unlike', props.post.id));
    } else {
        router.post(route('community.posts.like', props.post.id));
    }
    isLiked.value = !isLiked.value;
}
</script>

<style scoped>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>