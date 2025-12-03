<template>
    <MetaTags
        :title="profileUser.name + ' - Following - Olilearn'"
        :description="profileUser.name + '\'s following on Olilearn'"
        :image="profileUser.profile_photo_url"
    />
    <AppLayout>
        <Head :title="profileUser.name + ' - Following'" />
        
        <div class="min-h-screen bg-gradient-to-br from-slate-50 to-emerald-50 py-12">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Back Navigation -->
                <div class="mb-8">
                    <Link
                        :href="route('community.profile.show', profileUser.id)"
                        class="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-medium"
                    >
                        <ArrowLeftIcon class="h-4 w-4 mr-2" />
                        Back to Profile
                    </Link>
                </div>

                <!-- Header -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 mb-8">
                    <div class="flex items-center space-x-4 mb-6">
                        <img
                            :src="profileUser.profile_photo_url"
                            :alt="profileUser.name"
                            class="w-16 h-16 rounded-full"
                        />
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ profileUser.name }}</h1>
                            <p class="text-gray-600">{{ followers.total }} Followers</p>
                        </div>
                    </div>
                </div>

                <!-- Followers List -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <!-- Tabs -->
                    <div class="border-b border-gray-200">
                        <nav class="flex">
                            <Link
                                :href="route('community.profile.followers', profileUser.id)"
                                :class="[
                                    'flex-1 text-center py-4 font-medium text-sm border-b-2',
                                    route().current('community.profile.followers')
                                        ? 'border-emerald-500 text-emerald-600'
                                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                ]"
                            >
                                Followers
                            </Link>
                            <Link
                                :href="route('community.profile.following', profileUser.id)"
                                :class="[
                                    'flex-1 text-center py-4 font-medium text-sm border-b-2',
                                    route().current('community.profile.following')
                                        ? 'border-emerald-500 text-emerald-600'
                                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                ]"
                            >
                                Following
                            </Link>
                        </nav>
                    </div>

                    <!-- Followers Content -->
                    <div v-if="followers.data.length === 0" class="text-center py-12">
                        <UsersIcon class="h-16 w-16 text-gray-300 mx-auto mb-4" />
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No followers yet</h3>
                        <p class="text-gray-600">
                            {{ isOwnProfile ? 'Share posts and engage with others to get followers!' : 'This user doesn\'t have any followers yet.' }}
                        </p>
                    </div>

                    <div v-else class="divide-y divide-gray-200">
                        <div
                            v-for="follower in followers.data"
                            :key="follower.id"
                            class="p-6 hover:bg-gray-50 transition-colors"
                        >
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <Link :href="route('community.profile.show', follower.follower.id)">
                                        <img
                                            :src="follower.follower.profile_photo_url"
                                            :alt="follower.follower.name"
                                            class="w-12 h-12 rounded-full"
                                        />
                                    </Link>
                                    <div>
                                        <Link
                                            :href="route('community.profile.show', follower.follower.id)"
                                            class="font-bold text-gray-900 hover:text-emerald-700 block"
                                        >
                                            {{ follower.follower.name }}
                                        </Link>
                                        <p v-if="follower.follower.bio" class="text-sm text-gray-600 line-clamp-1">
                                            {{ follower.follower.bio }}
                                        </p>
                                        <div class="flex items-center space-x-4 mt-1">
                                            <span class="text-xs text-gray-500">
                                                {{ follower.follower.post_count || 0 }} posts
                                            </span>
                                            <span class="text-xs text-gray-500">
                                                {{ follower.follower.follower_count || 0 }} followers
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <button
                                    v-if="!isOwnProfile && follower.follower.id !== $page.props.auth.user.id"
                                    @click="toggleFollowUser(follower.follower)"
                                    :class="[
                                        'px-4 py-2 rounded-xl font-medium text-sm transition-all duration-300',
                                        isFollowingUser(follower.follower)
                                            ? 'bg-gray-100 text-gray-700 hover:bg-gray-200 border border-gray-300'
                                            : 'bg-emerald-600 text-white hover:bg-emerald-700'
                                    ]"
                                >
                                    {{ isFollowingUser(follower.follower) ? 'Following' : 'Follow' }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div v-if="followers.links && followers.data.length > 0" class="p-6 border-t border-gray-200">
                        <Pagination :links="followers.links" />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import MetaTags from '@/Components/MetaTags.vue';
import Pagination from '@/Components/Pagination.vue';
import {
    ArrowLeftIcon,
    UsersIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    profileUser: Object,
    followers: Object,
    followingUsers: Array, // Array of user IDs that current user is following
});

const isOwnProfile = computed(() => {
    return props.profileUser.id === $page.props.auth.user.id;
});

function isFollowingUser(user) {
    return props.followingUsers?.includes(user.id) || false;
}

function toggleFollowUser(user) {
    if (isFollowingUser(user)) {
        router.delete(route('community.profile.unfollow', user.id));
    } else {
        router.post(route('community.profile.follow', user.id));
    }
}
</script>

<style scoped>
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>