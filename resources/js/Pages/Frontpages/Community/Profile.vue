<template>
    <MetaTags
        :title="profileUser.name + ' - Olilearn Community'"
        :description="profileUser.bio || 'Olilearn community member'"
        :image="profileUser.profile_photo_url"
    />
    <AppLayout>
        <Head :title="profileUser.name + ' - Profile'" />
        
        <div class="min-h-screen bg-gradient-to-br from-slate-50 to-emerald-50 py-12">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Profile Header -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 mb-8">
                    <div class="flex flex-col md:flex-row items-start md:items-center space-y-6 md:space-y-0 md:space-x-8">
                        <!-- Avatar -->
                        <div class="relative">
                            <img
                                :src="profileUser.profile_photo_url"
                                :alt="profileUser.name"
                                class="w-32 h-32 rounded-2xl border-4 border-white shadow-lg"
                            />
                            <div v-if="profileUser.is_active" class="absolute bottom-2 right-2 w-4 h-4 bg-emerald-500 rounded-full border-2 border-white"></div>
                        </div>

                        <!-- User Info -->
                        <div class="flex-1">
                            <div class="flex flex-col md:flex-row md:items-center justify-between mb-4">
                                <div>
                                    <h1 class="text-3xl font-bold text-gray-900">{{ profileUser.name }}</h1>
                                    <div class="flex items-center space-x-2 mt-2">
                                        <span v-if="profileUser.roles" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-emerald-100 text-emerald-800">
                                            {{ profileUser.roles[0]?.name || 'Learner' }}
                                        </span>
                                        <span v-if="profileUser.is_online" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                            Online
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="flex space-x-3 mt-4 md:mt-0">
                                    <button
                                        v-if="isOwnProfile"
                                        :href="route('profile.show')"
                                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-medium"
                                    >
                                        Edit Profile
                                    </button>
                                    <button
                                        v-else
                                        @click="toggleFollow"
                                        :class="[
                                            'px-4 py-2 rounded-xl font-medium transition-all duration-300',
                                            isFollowing
                                                ? 'bg-gray-100 text-gray-700 hover:bg-gray-200 border border-gray-300'
                                                : 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white hover:from-emerald-700 hover:to-teal-700'
                                        ]"
                                    >
                                        {{ isFollowing ? 'Following' : 'Follow' }}
                                    </button>
                                    <button
                                        v-if="!isOwnProfile"
                                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-medium"
                                    >
                                        Message
                                    </button>
                                </div>
                            </div>

                            <!-- Bio -->
                            <p v-if="profileUser.bio" class="text-gray-700 mb-6">{{ profileUser.bio }}</p>

                            <!-- Stats -->
                            <div class="grid grid-cols-4 gap-4">
                                <Link
                                    :href="route('community.profile.followers', profileUser.id)"
                                    class="text-center p-4 rounded-xl hover:bg-gray-50 transition-colors"
                                >
                                    <div class="text-2xl font-bold text-emerald-700">{{ stats.followers || 0 }}</div>
                                    <div class="text-sm text-gray-600">Followers</div>
                                </Link>
                                <Link
                                    :href="route('community.profile.following', profileUser.id)"
                                    class="text-center p-4 rounded-xl hover:bg-gray-50 transition-colors"
                                >
                                    <div class="text-2xl font-bold text-emerald-700">{{ stats.following || 0 }}</div>
                                    <div class="text-sm text-gray-600">Following</div>
                                </Link>
                                <div class="text-center p-4 rounded-xl">
                                    <div class="text-2xl font-bold text-emerald-700">{{ stats.posts || 0 }}</div>
                                    <div class="text-sm text-gray-600">Posts</div>
                                </div>
                                <div class="text-center p-4 rounded-xl">
                                    <div class="text-2xl font-bold text-emerald-700">{{ stats.likes_received || 0 }}</div>
                                    <div class="text-sm text-gray-600">Likes</div>
                                </div>
                            </div>

                            <!-- Additional Info -->
                            <div class="flex flex-wrap gap-4 mt-6 text-sm text-gray-600">
                                <div v-if="profileUser.location" class="flex items-center">
                                    <MapPinIcon class="h-4 w-4 mr-2" />
                                    {{ profileUser.location }}
                                </div>
                                <div v-if="profileUser.website" class="flex items-center">
                                    <LinkIcon class="h-4 w-4 mr-2" />
                                    <a :href="profileUser.website" target="_blank" class="text-emerald-600 hover:text-emerald-700">
                                        Website
                                    </a>
                                </div>
                                <div class="flex items-center">
                                    <CalendarIcon class="h-4 w-4 mr-2" />
                                    Joined {{ joinDate }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="mb-8">
                    <div class="border-b border-gray-200">
                        <nav class="flex space-x-8">
                            <button
                                @click="activeTab = 'posts'"
                                :class="[
                                    'py-4 px-1 border-b-2 font-medium text-sm',
                                    activeTab === 'posts'
                                        ? 'border-emerald-500 text-emerald-600'
                                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                ]"
                            >
                                <NewspaperIcon class="h-5 w-5 inline mr-2" />
                                Posts
                            </button>
                            <button
                                v-if="isOwnProfile"
                                @click="activeTab = 'bookmarks'"
                                :class="[
                                    'py-4 px-1 border-b-2 font-medium text-sm',
                                    activeTab === 'bookmarks'
                                        ? 'border-emerald-500 text-emerald-600'
                                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                ]"
                            >
                                <BookmarkIcon class="h-5 w-5 inline mr-2" />
                                Bookmarks
                            </button>
                            <button
                                @click="activeTab = 'courses'"
                                :class="[
                                    'py-4 px-1 border-b-2 font-medium text-sm',
                                    activeTab === 'courses'
                                        ? 'border-emerald-500 text-emerald-600'
                                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                ]"
                            >
                                <AcademicCapIcon class="h-5 w-5 inline mr-2" />
                                Courses
                            </button>
                            <button
                                @click="activeTab = 'about'"
                                :class="[
                                    'py-4 px-1 border-b-2 font-medium text-sm',
                                    activeTab === 'about'
                                        ? 'border-emerald-500 text-emerald-600'
                                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                ]"
                            >
                                <InformationCircleIcon class="h-5 w-5 inline mr-2" />
                                About
                            </button>
                        </nav>
                    </div>
                </div>

                <!-- Tab Content -->
                <div>
                    <!-- Posts Tab -->
                    <div v-if="activeTab === 'posts'" class="space-y-6">
                        <div v-if="posts.data.length === 0" class="text-center py-12">
                            <NewspaperIcon class="h-16 w-16 text-gray-300 mx-auto mb-4" />
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No posts yet</h3>
                            <p class="text-gray-600 mb-6">
                                {{ isOwnProfile ? 'Share your first post with the community!' : 'This user hasn\'t posted anything yet.' }}
                            </p>
                            <Link
                                v-if="isOwnProfile"
                                :href="route('posts.make')"
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-xl font-bold hover:from-emerald-700 hover:to-teal-700"
                            >
                                <PencilSquareIcon class="h-5 w-5 mr-2" />
                                Create First Post
                            </Link>
                        </div>

                        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div
                                v-for="post in posts.data"
                                :key="post.id"
                                class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200 hover:shadow-xl transition-all duration-300"
                            >
                                <div class="flex items-center justify-between mb-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                                        :class="postTypeClasses[post.type]">
                                        {{ post.type }}
                                    </span>
                                    <span class="text-sm text-gray-500">{{ post.time_ago }}</span>
                                </div>
                                <Link :href="route('community.posts.show', post.id)">
                                    <h3 class="text-lg font-bold text-gray-900 mb-3 hover:text-emerald-700 line-clamp-2">
                                        {{ post.title }}
                                    </h3>
                                    <p class="text-gray-700 mb-4 line-clamp-3">{{ post.excerpt }}</p>
                                </Link>
                                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                                        <div class="flex items-center space-x-1">
                                            <HeartIcon class="h-4 w-4" />
                                            <span>{{ post.like_count }}</span>
                                        </div>
                                        <div class="flex items-center space-x-1">
                                            <ChatBubbleLeftRightIcon class="h-4 w-4" />
                                            <span>{{ post.comment_count }}</span>
                                        </div>
                                    </div>
                                    <Link
                                        :href="route('community.posts.show', post.id)"
                                        class="text-sm text-emerald-600 hover:text-emerald-700 font-medium"
                                    >
                                        Read more →
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div v-if="posts.links && posts.data.length > 0" class="mt-8">
                            <Pagination :links="posts.links" />
                        </div>
                    </div>

                    <!-- Courses Tab -->
                    <div v-if="activeTab === 'courses'">
                        <div v-if="profileUser.courses && profileUser.courses.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div
                                v-for="course in profileUser.courses"
                                :key="course.id"
                                class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200"
                            >
                                <div class="flex items-center justify-between mb-4">
                                    <span class="text-sm font-medium text-emerald-700">{{ course.subject }}</span>
                                    <span class="text-xs px-2 py-1 bg-gray-100 rounded-full">{{ course.level }}</span>
                                </div>
                                <h3 class="font-bold text-gray-900 mb-3">{{ course.title }}</h3>
                                <div class="mb-4">
                                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                                        <span>Progress</span>
                                        <span>{{ course.progress_percentage }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div
                                            class="bg-gradient-to-r from-emerald-500 to-teal-600 h-2 rounded-full"
                                            :style="{ width: course.progress_percentage + '%' }"
                                        ></div>
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500">
                                    Started {{ course.start_date }}
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-12">
                            <AcademicCapIcon class="h-16 w-16 text-gray-300 mx-auto mb-4" />
                            <p class="text-gray-600">
                                {{ isOwnProfile ? 'You haven\'t enrolled in any courses yet.' : 'This user hasn\'t enrolled in any courses yet.' }}
                            </p>
                        </div>
                    </div>

                    <!-- About Tab -->
                    <div v-if="activeTab === 'about'" class="bg-white rounded-2xl p-8 shadow-sm border border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 mb-4">Learning Stats</h3>
                                <div class="space-y-4">
                                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-xl">
                                        <span class="text-gray-700">Total Learning Hours</span>
                                        <span class="font-bold text-emerald-700">120h</span>
                                    </div>
                                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-xl">
                                        <span class="text-gray-700">Courses Completed</span>
                                        <span class="font-bold text-emerald-700">3</span>
                                    </div>
                                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-xl">
                                        <span class="text-gray-700">Quizzes Passed</span>
                                        <span class="font-bold text-emerald-700">15</span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 mb-4">Achievements</h3>
                                <div v-if="achievements.length > 0" class="grid grid-cols-3 gap-3">
                                    <div
                                        v-for="achievement in achievements"
                                        :key="achievement.id"
                                        class="text-center p-3 bg-emerald-50 rounded-xl"
                                    >
                                        <div class="text-2xl mb-2">{{ achievement.icon }}</div>
                                        <div class="text-xs font-medium text-emerald-800">{{ achievement.title }}</div>
                                    </div>
                                </div>
                                <div v-else class="text-center py-6 text-gray-500">
                                    <TrophyIcon class="h-12 w-12 mx-auto mb-3 text-gray-300" />
                                    <p>No achievements yet</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router,usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import MetaTags from '@/Components/MetaTags.vue';
import Pagination from '@/Components/Pagination.vue';
import {
    MapPinIcon,
    LinkIcon,
    CalendarIcon,
    NewspaperIcon,
    BookmarkIcon,
    AcademicCapIcon,
    InformationCircleIcon,
    PencilSquareIcon,
    HeartIcon,
    ChatBubbleLeftRightIcon,
    TrophyIcon
} from '@heroicons/vue/24/outline';

const page = usePage();

const props = defineProps({
    profileUser: Object,
    posts: Object,
    isFollowing: Boolean,
    stats: Object,
});

const activeTab = ref('posts');
const achievements = ref([
    { id: 1, title: 'First Post', icon: '📝' },
    { id: 2, title: 'Active Learner', icon: '🎯' },
    { id: 3, title: 'Helpful', icon: '🤝' },
]);

const isOwnProfile = computed(() => {
    return props.profileUser.id === page.props.auth.user.id;
});

const joinDate = computed(() => {
    return new Date(props.profileUser.created_at).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
    });
});

const postTypeClasses = {
    discussion: 'bg-blue-100 text-blue-800',
    question: 'bg-purple-100 text-purple-800',
    achievement: 'bg-emerald-100 text-emerald-800',
    resource: 'bg-amber-100 text-amber-800',
};

function toggleFollow() {
    if (props.isFollowing) {
        router.delete(route('community.profile.unfollow', props.profileUser.id));
    } else {
        router.post(route('community.profile.follow', props.profileUser.id));
    }
}
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