<template>
    <MetaTags
        :title="post.title + ' - Olilearn Community'"
        :description="post.excerpt"
        image="/images/community.png"
    />
    <AppLayout>
        <Head :title="post.title" />
        
        <div class="min-h-screen bg-gradient-to-br from-slate-50 to-emerald-50 py-12">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Back Navigation -->
                <div class="mb-8">
                    <Link
                        :href="route('community.index')"
                        class="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-medium"
                    >
                        <ArrowLeftIcon class="h-4 w-4 mr-2" />
                        Back to Community
                    </Link>
                </div>

                <!-- Post Content -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-8">
                        <!-- Post Card -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                            <!-- Post Header -->
                            <div class="p-8 border-b border-gray-100">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex items-center space-x-3">
                                        <Link :href="route('community.profile.show', post.user.id)">
                                            <img
                                                :src="post.user.profile_photo_url"
                                                :alt="post.user.name"
                                                class="w-12 h-12 rounded-full"
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
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                                                    :class="postTypeClasses[post.type]">
                                                    {{ post.type }}
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-500">{{ post.time_ago }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span v-if="post.is_pinned" class="text-emerald-600 text-sm">
                                            📌 Pinned
                                        </span>
                                        <button
                                            v-if="canEdit"
                                            @click="editing = true"
                                            class="text-gray-400 hover:text-emerald-600 p-2"
                                        >
                                            <PencilSquareIcon class="h-5 w-5" />
                                        </button>
                                        <button
                                            v-if="canDelete"
                                            @click="deletePost"
                                            class="text-gray-400 hover:text-red-600 p-2"
                                        >
                                            <TrashIcon class="h-5 w-5" />
                                        </button>
                                    </div>
                                </div>

                                <!-- Post Title & Content -->
                                <div v-if="!editing">
                                    <h1 class="text-3xl font-bold text-gray-900 mb-6">
                                        {{ post.title }}
                                    </h1>
                                    <div class="prose prose-lg max-w-none mb-6" v-html="formattedContent"></div>
                                </div>

                                <!-- Edit Form -->
                                <form v-else @submit.prevent="updatePost" class="space-y-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-900 mb-2">
                                            Title
                                        </label>
                                        <input
                                            v-model="editForm.title"
                                            type="text"
                                            required
                                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-900 mb-2">
                                            Content
                                        </label>
                                        <textarea
                                            v-model="editForm.content"
                                            rows="8"
                                            required
                                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                        ></textarea>
                                    </div>
                                    <div class="flex justify-end space-x-4">
                                        <button
                                            type="button"
                                            @click="editing = false"
                                            class="px-4 py-2 text-gray-700 hover:text-gray-900"
                                        >
                                            Cancel
                                        </button>
                                        <button
                                            type="submit"
                                            :disabled="updating"
                                            class="px-4 py-2 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 disabled:opacity-50"
                                        >
                                            {{ updating ? 'Saving...' : 'Save Changes' }}
                                        </button>
                                    </div>
                                </form>

                                <!-- Tags -->
                                <div v-if="post.tags && post.tags.length > 0" class="mb-6">
                                    <div class="flex flex-wrap gap-2">
                                        <span
                                            v-for="tag in post.tags"
                                            :key="tag"
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800 hover:bg-gray-200 cursor-pointer"
                                            @click="searchByTag(tag)"
                                        >
                                            {{ tag }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Course Link -->
                                <div v-if="post.course" class="mb-6">
                                    <div class="inline-flex items-center px-4 py-2 bg-emerald-50 border border-emerald-100 rounded-xl">
                                        <AcademicCapIcon class="h-5 w-5 text-emerald-600 mr-2" />
                                        <span class="text-sm text-gray-700">Related to course:</span>
                                        <Link
                                            :href="route('courses.show', post.course.id)"
                                            class="ml-2 text-sm font-medium text-emerald-700 hover:text-emerald-800"
                                        >
                                            {{ post.course.title }}
                                        </Link>
                                    </div>
                                </div>

                                <!-- Stats & Actions -->
                                <div class="flex items-center justify-between pt-6 border-t border-gray-100">
                                    <div class="flex items-center space-x-6">
                                        <!-- Like Button -->
                                        <button
                                            @click="toggleLike"
                                            :class="[
                                                'flex items-center space-x-2 px-4 py-2 rounded-xl transition-all duration-300',
                                                isLiked
                                                    ? 'bg-red-50 text-red-600 border border-red-100'
                                                    : 'text-gray-600 hover:text-emerald-600 hover:bg-emerald-50'
                                            ]"
                                        >
                                            <HeartIcon
                                                :class="[
                                                    'h-5 w-5',
                                                    isLiked ? 'fill-current' : ''
                                                ]"
                                            />
                                            <span class="font-medium">{{ post.like_count }}</span>
                                            <span>{{ isLiked ? 'Liked' : 'Like' }}</span>
                                        </button>

                                        <!-- Comment Button -->
                                        <button
                                            @click="scrollToComments"
                                            class="flex items-center space-x-2 px-4 py-2 rounded-xl text-gray-600 hover:text-emerald-600 hover:bg-emerald-50 transition-all duration-300"
                                        >
                                            <ChatBubbleLeftRightIcon class="h-5 w-5" />
                                            <span class="font-medium">{{ post.comment_count }}</span>
                                            <span>Comments</span>
                                        </button>

                                        <!-- Share Button -->
                                        <button
                                            @click="sharePost"
                                            class="flex items-center space-x-2 px-4 py-2 rounded-xl text-gray-600 hover:text-emerald-600 hover:bg-emerald-50 transition-all duration-300"
                                        >
                                            <ShareIcon class="h-5 w-5" />
                                            <span>Share</span>
                                        </button>
                                    </div>

                                    <div class="flex items-center space-x-2 text-sm text-gray-500">
                                        <EyeIcon class="h-4 w-4" />
                                        <span>{{ post.views }} views</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Comments Section -->
                            <div id="comments" class="p-8">
                                <h2 class="text-xl font-bold text-gray-900 mb-6">Comments ({{ post.comment_count }})</h2>
                                
                                <!-- Add Comment -->
                                <div class="mb-8">
                                    <form @submit.prevent="addComment">
                                        <div class="flex space-x-4">
                                            <div class="flex-shrink-0">
                                                <img
                                                    :src="$page.props.auth.user.profile_photo_url"
                                                    :alt="$page.props.auth.user.name"
                                                    class="w-10 h-10 rounded-full"
                                                />
                                            </div>
                                            <div class="flex-1">
                                                <textarea
                                                    v-model="newComment"
                                                    rows="3"
                                                    placeholder="Share your thoughts..."
                                                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 resize-none"
                                                ></textarea>
                                                <div class="flex justify-end mt-2">
                                                    <button
                                                        type="submit"
                                                        :disabled="!newComment.trim() || commenting"
                                                        class="px-4 py-2 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 disabled:opacity-50"
                                                    >
                                                        {{ commenting ? 'Posting...' : 'Post Comment' }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <!-- Comments List -->
                                <div v-if="post.comments.length === 0" class="text-center py-8 text-gray-500">
                                    <ChatBubbleLeftRightIcon class="h-12 w-12 mx-auto mb-4 text-gray-300" />
                                    <p>No comments yet. Be the first to share your thoughts!</p>
                                </div>

                                <div v-else class="space-y-6">
                                    <div
                                        v-for="comment in post.comments"
                                        :key="comment.id"
                                        class="space-y-4"
                                    >
                                        <!-- Parent Comment -->
                                        <div class="flex space-x-4">
                                            <div class="flex-shrink-0">
                                                <Link :href="route('community.profile.show', comment.user.id)">
                                                    <img
                                                        :src="comment.user.profile_photo_url"
                                                        :alt="comment.user.name"
                                                        class="w-10 h-10 rounded-full"
                                                    />
                                                </Link>
                                            </div>
                                            <div class="flex-1">
                                                <div class="bg-gray-50 rounded-2xl p-4">
                                                    <div class="flex justify-between items-start mb-2">
                                                        <div>
                                                            <Link
                                                                :href="route('community.profile.show', comment.user.id)"
                                                                class="font-medium text-gray-900 hover:text-emerald-700"
                                                            >
                                                                {{ comment.user.name }}
                                                            </Link>
                                                            <span class="text-sm text-gray-500 ml-2">{{ comment.time_ago }}</span>
                                                        </div>
                                                        <button
                                                            v-if="canDeleteComment(comment)"
                                                            @click="deleteComment(comment)"
                                                            class="text-gray-400 hover:text-red-600"
                                                        >
                                                            <TrashIcon class="h-4 w-4" />
                                                        </button>
                                                    </div>
                                                    <p class="text-gray-700 whitespace-pre-wrap">{{ comment.content }}</p>
                                                    <div class="flex items-center space-x-4 mt-3">
                                                        <button
                                                            @click="replyTo(comment)"
                                                            class="text-sm text-gray-500 hover:text-emerald-600"
                                                        >
                                                            Reply
                                                        </button>
                                                        <button
                                                            @click="toggleCommentLike(comment)"
                                                            class="text-sm text-gray-500 hover:text-emerald-600"
                                                        >
                                                            {{ comment.is_liked ? 'Unlike' : 'Like' }} ({{ comment.like_count }})
                                                        </button>
                                                    </div>
                                                </div>

                                                <!-- Reply Form -->
                                                <div v-if="replyingTo === comment.id" class="mt-4 ml-8">
                                                    <form @submit.prevent="submitReply">
                                                        <div class="flex space-x-4">
                                                            <div class="flex-shrink-0">
                                                                <img
                                                                    :src="$page.props.auth.user.profile_photo_url"
                                                                    :alt="$page.props.auth.user.name"
                                                                    class="w-8 h-8 rounded-full"
                                                                />
                                                            </div>
                                                            <div class="flex-1">
                                                                <textarea
                                                                    v-model="replyContent"
                                                                    rows="2"
                                                                    placeholder="Write a reply..."
                                                                    class="w-full px-3 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 resize-none"
                                                                ></textarea>
                                                                <div class="flex justify-end space-x-2 mt-2">
                                                                    <button
                                                                        type="button"
                                                                        @click="replyingTo = null"
                                                                        class="px-3 py-1 text-sm text-gray-600 hover:text-gray-800"
                                                                    >
                                                                        Cancel
                                                                    </button>
                                                                    <button
                                                                        type="submit"
                                                                        :disabled="!replyContent.trim() || replying"
                                                                        class="px-3 py-1 text-sm bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 disabled:opacity-50"
                                                                    >
                                                                        {{ replying ? 'Posting...' : 'Reply' }}
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>

                                                <!-- Replies -->
                                                <div v-if="comment.replies && comment.replies.length > 0" class="mt-4 ml-8 space-y-3">
                                                    <div
                                                        v-for="reply in comment.replies"
                                                        :key="reply.id"
                                                        class="flex space-x-3"
                                                    >
                                                        <div class="flex-shrink-0">
                                                            <Link :href="route('community.profile.show', reply.user.id)">
                                                                <img
                                                                    :src="reply.user.profile_photo_url"
                                                                    :alt="reply.user.name"
                                                                    class="w-8 h-8 rounded-full"
                                                                />
                                                            </Link>
                                                        </div>
                                                        <div class="flex-1">
                                                            <div class="bg-gray-50 rounded-2xl p-3">
                                                                <div class="flex justify-between items-start mb-1">
                                                                    <div>
                                                                        <Link
                                                                            :href="route('community.profile.show', reply.user.id)"
                                                                            class="text-sm font-medium text-gray-900 hover:text-emerald-700"
                                                                        >
                                                                            {{ reply.user.name }}
                                                                        </Link>
                                                                        <span class="text-xs text-gray-500 ml-2">{{ reply.time_ago }}</span>
                                                                    </div>
                                                                    <button
                                                                        v-if="canDeleteComment(reply)"
                                                                        @click="deleteComment(reply)"
                                                                        class="text-gray-400 hover:text-red-600"
                                                                    >
                                                                        <TrashIcon class="h-3 w-3" />
                                                                    </button>
                                                                </div>
                                                                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ reply.content }}</p>
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

                    <!-- Sidebar -->
                    <div class="lg:col-span-1 space-y-6">
                        <!-- Author Card -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">About Author</h3>
                            <div class="flex items-center space-x-3 mb-4">
                                <img
                                    :src="post.user.profile_photo_url"
                                    :alt="post.user.name"
                                    class="w-16 h-16 rounded-full"
                                />
                                <div>
                                    <Link
                                        :href="route('community.profile.show', post.user.id)"
                                        class="font-bold text-gray-900 hover:text-emerald-700"
                                    >
                                        {{ post.user.name }}
                                    </Link>
                                    <p class="text-sm text-gray-500">{{ post.user.roles?.[0]?.name || 'Learner' }}</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-4 text-center mb-4">
                                <div>
                                    <div class="text-xl font-bold text-emerald-700">{{ post.user.post_count || 0 }}</div>
                                    <div class="text-xs text-gray-500">Posts</div>
                                </div>
                                <div>
                                    <div class="text-xl font-bold text-emerald-700">{{ post.user.follower_count || 0 }}</div>
                                    <div class="text-xs text-gray-500">Followers</div>
                                </div>
                                <div>
                                    <div class="text-xl font-bold text-emerald-700">{{ post.user.following_count || 0 }}</div>
                                    <div class="text-xs text-gray-500">Following</div>
                                </div>
                            </div>
                            <button
                                v-if="$page.props.auth.user.id !== post.user.id"
                                @click="toggleFollow"
                                :class="[
                                    'w-full px-4 py-2 rounded-xl font-medium transition-all duration-300',
                                    isFollowing
                                        ? 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                        : 'bg-emerald-600 text-white hover:bg-emerald-700'
                                ]"
                            >
                                {{ isFollowing ? 'Following' : 'Follow' }}
                            </button>
                        </div>

                        <!-- Related Posts -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Related Posts</h3>
                            <div class="space-y-4">
                                <div
                                    v-for="related in relatedPosts"
                                    :key="related.id"
                                    class="group cursor-pointer"
                                >
                                    <Link :href="route('community.posts.show', related.id)" class="block">
                                        <h4 class="font-medium text-gray-900 group-hover:text-emerald-700 mb-1 line-clamp-2">
                                            {{ related.title }}
                                        </h4>
                                        <div class="flex items-center text-sm text-gray-500">
                                            <span>{{ related.user.name }}</span>
                                            <span class="mx-2">•</span>
                                            <span>{{ related.time_ago }}</span>
                                        </div>
                                    </Link>
                                </div>
                            </div>
                            <Link
                                :href="route('community.index')"
                                class="block text-center mt-4 text-emerald-600 hover:text-emerald-700 font-medium text-sm"
                            >
                                View all posts →
                            </Link>
                        </div>

                        <!-- Course Info -->
                        <div v-if="post.course" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Course Info</h3>
                            <div class="space-y-3">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ post.course.title }}</div>
                                    <div class="text-xs text-gray-500">{{ post.course.subject }} • {{ post.course.level }}</div>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">Progress</span>
                                    <span class="font-medium text-emerald-700">{{ post.course.progress_percentage }}%</span>
                                </div>
                                <Link
                                    :href="route('courses.show', post.course.id)"
                                    class="block w-full text-center px-4 py-2 bg-emerald-50 text-emerald-700 rounded-xl font-medium hover:bg-emerald-100"
                                >
                                    View Course
                                </Link>
                            </div>
                        </div>

                        <!-- Share Card -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Share this post</h3>
                            <div class="flex space-x-3">
                                <button
                                    @click="shareOnPlatform('facebook')"
                                    class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 font-medium"
                                >
                                    Facebook
                                </button>
                                <button
                                    @click="shareOnPlatform('twitter')"
                                    class="flex-1 px-4 py-2 bg-blue-400 text-white rounded-xl hover:bg-blue-500 font-medium"
                                >
                                    Twitter
                                </button>
                                <button
                                    @click="copyLink"
                                    class="flex-1 px-4 py-2 bg-gray-600 text-white rounded-xl hover:bg-gray-700 font-medium"
                                >
                                    Copy Link
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/GuestLayout.vue';
import MetaTags from '@/Components/MetaTags.vue';
import { marked } from 'marked';
import {
    ArrowLeftIcon,
    HeartIcon,
    ChatBubbleLeftRightIcon,
    ShareIcon,
    EyeIcon,
    PencilSquareIcon,
    TrashIcon,
    AcademicCapIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    post: Object,
    relatedPosts: Array,
    isLiked: Boolean,
    isFollowing: Boolean,
});

const editing = ref(false);
const updating = ref(false);
const commenting = ref(false);
const replying = ref(false);
const replyingTo = ref(null);
const replyContent = ref('');
const newComment = ref('');

const postTypeClasses = {
    discussion: 'bg-blue-100 text-blue-800',
    question: 'bg-purple-100 text-purple-800',
    achievement: 'bg-emerald-100 text-emerald-800',
    resource: 'bg-amber-100 text-amber-800',
};

const editForm = reactive({
    title: props.post.title,
    content: props.post.content,
});

const formattedContent = computed(() => {
    return marked(props.post.content);
});

const canEdit = computed(() => {
    return props.post.user.id === $page.props.auth.user.id || $page.props.auth.user.is_admin;
});

const canDelete = computed(() => {
    return props.post.user.id === $page.props.auth.user.id || $page.props.auth.user.is_admin;
});

function canDeleteComment(comment) {
    return comment.user.id === $page.props.auth.user.id || 
           props.post.user.id === $page.props.auth.user.id ||
           $page.props.auth.user.is_admin;
}

function toggleLike() {
    if (props.isLiked) {
        router.delete(route('community.posts.unlike', props.post.id));
    } else {
        router.post(route('community.posts.like', props.post.id));
    }
}

function toggleFollow() {
    if (props.isFollowing) {
        router.delete(route('community.profile.unfollow', props.post.user.id));
    } else {
        router.post(route('community.profile.follow', props.post.user.id));
    }
}

function updatePost() {
    updating.value = true;
    router.put(route('community.posts.update', props.post.id), editForm, {
        onFinish: () => {
            updating.value = false;
            editing.value = false;
        },
    });
}

function deletePost() {
    if (confirm('Are you sure you want to delete this post?')) {
        router.delete(route('community.posts.destroy', props.post.id));
    }
}

function addComment() {
    commenting.value = true;
    router.post(route('community.posts.comments.store', props.post.id), {
        content: newComment.value,
    }, {
        onFinish: () => {
            commenting.value = false;
            newComment.value = '';
        },
    });
}

function replyTo(comment) {
    replyingTo.value = comment.id;
    replyContent.value = '';
}

function submitReply() {
    replying.value = true;
    router.post(route('community.posts.comments.store', props.post.id), {
        content: replyContent.value,
        parent_id: replyingTo.value,
    }, {
        onFinish: () => {
            replying.value = false;
            replyingTo.value = null;
            replyContent.value = '';
        },
    });
}

function deleteComment(comment) {
    if (confirm('Are you sure you want to delete this comment?')) {
        router.delete(route('community.comments.destroy', comment.id));
    }
}

function toggleCommentLike(comment) {
    // Implement comment like functionality
}

function scrollToComments() {
    document.getElementById('comments').scrollIntoView({ behavior: 'smooth' });
}

function sharePost() {
    if (navigator.share) {
        navigator.share({
            title: props.post.title,
            text: props.post.excerpt,
            url: window.location.href,
        });
    }
}

function shareOnPlatform(platform) {
    const url = window.location.href;
    const text = encodeURIComponent(props.post.title);
    
    let shareUrl;
    switch(platform) {
        case 'facebook':
            shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
            break;
        case 'twitter':
            shareUrl = `https://twitter.com/intent/tweet?text=${text}&url=${url}`;
            break;
    }
    
    window.open(shareUrl, '_blank', 'width=600,height=400');
}

function copyLink() {
    navigator.clipboard.writeText(window.location.href);
    alert('Link copied to clipboard!');
}

function searchByTag(tag) {
    router.get(route('community.index'), { search: tag });
}

onMounted(() => {
    // Update post view count
    router.reload({ only: ['post'] });
});
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>