<template>
    <AppLayout>
        <Head title="Create New Post" />
        
        <div class="min-h-screen bg-gradient-to-br from-slate-50 to-emerald-50 py-12">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
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

                <!-- Header -->
                <div class="mb-8">
                    <div class="inline-flex items-center px-4 py-2 rounded-full bg-emerald-100 border border-emerald-200 text-emerald-700 text-sm font-medium mb-4">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></span>
                        Share with Community
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900">
                        Create New Post
                    </h1>
                    <p class="text-gray-600 mt-2">
                        Share your learning experience, ask questions, or celebrate achievements with fellow learners.
                    </p>
                </div>

                <!-- Form -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
                    <form @submit.prevent="submitForm">
                        <!-- Post Type Selection -->
                        <div class="mb-8">
                            <label class="block text-sm font-medium text-gray-900 mb-4">
                                What type of post is this?
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <button
                                    type="button"
                                    v-for="type in postTypes"
                                    :key="type.value"
                                    @click="form.type = type.value"
                                    :class="[
                                        'p-4 rounded-xl border-2 text-center transition-all duration-300',
                                        form.type === type.value
                                            ? 'border-emerald-500 bg-emerald-50 scale-105'
                                            : 'border-gray-200 hover:border-emerald-300 hover:bg-gray-50'
                                    ]"
                                >
                                    <div class="text-2xl mb-2">{{ type.icon }}</div>
                                    <div class="font-medium text-gray-900">{{ type.label }}</div>
                                    <p class="text-xs text-gray-500 mt-1">{{ type.description }}</p>
                                </button>
                            </div>
                        </div>

                        <!-- Course Selection -->
                        <div class="mb-8" v-if="courses && courses.length > 0">
                            <label class="block text-sm font-medium text-gray-900 mb-2">
                                Link to Course (Optional)
                            </label>
                            <select
                                v-model="form.course_id"
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white"
                            >
                                <option :value="null">No specific course</option>
                                <option
                                    v-for="course in courses"
                                    :key="course.id"
                                    :value="course.id"
                                >
                                    {{ course.title }} ({{ course.subject }})
                                </option>
                            </select>
                        </div>

                        <!-- Title -->
                        <div class="mb-8">
                            <label class="block text-sm font-medium text-gray-900 mb-2">
                                Title
                            </label>
                            <input
                                v-model="form.title"
                                type="text"
                                required
                                placeholder="Give your post a descriptive title..."
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 placeholder-gray-400"
                            />
                            <p class="text-xs text-gray-500 mt-2">
                                Be specific and concise. Good titles attract more engagement.
                            </p>
                        </div>

                        <!-- Content Editor -->
                        <div class="mb-8">
                            <label class="block text-sm font-medium text-gray-900 mb-2">
                                Content
                            </label>
                            <div class="border border-gray-300 rounded-xl overflow-hidden">
                                <div class="bg-gray-50 border-b border-gray-200 px-4 py-3">
                                    <div class="flex space-x-4">
                                        <button
                                            type="button"
                                            @click="insertFormat('bold')"
                                            class="px-3 py-1 rounded hover:bg-gray-200"
                                            title="Bold"
                                        >
                                            <BoldIcon class="h-4 w-4" />
                                        </button>
                                        <button
                                            type="button"
                                            @click="insertFormat('italic')"
                                            class="px-3 py-1 rounded hover:bg-gray-200"
                                            title="Italic"
                                        >
                                            <ItalicIcon class="h-4 w-4" />
                                        </button>
                                        <button
                                            type="button"
                                            @click="insertFormat('code')"
                                            class="px-3 py-1 rounded hover:bg-gray-200"
                                            title="Code"
                                        >
                                            <CodeBracketIcon class="h-4 w-4" />
                                        </button>
                                        <button
                                            type="button"
                                            @click="insertFormat('link')"
                                            class="px-3 py-1 rounded hover:bg-gray-200"
                                            title="Link"
                                        >
                                            <LinkIcon class="h-4 w-4" />
                                        </button>
                                    </div>
                                </div>
                                <textarea
                                    v-model="form.content"
                                    required
                                    rows="10"
                                    placeholder="Share your thoughts, questions, or achievements..."
                                    class="w-full px-4 py-4 focus:outline-none resize-none"
                                ></textarea>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 mt-2">
                                <div>Markdown is supported</div>
                                <div>{{ form.content.length }} characters</div>
                            </div>
                        </div>

                        <!-- Tags -->
                        <div class="mb-8">
                            <label class="block text-sm font-medium text-gray-900 mb-2">
                                Tags
                            </label>
                            <div class="flex flex-wrap gap-2 mb-3">
                                <span
                                    v-for="tag in form.tags"
                                    :key="tag"
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-emerald-100 text-emerald-800"
                                >
                                    {{ tag }}
                                    <button
                                        type="button"
                                        @click="removeTag(tag)"
                                        class="ml-2 text-emerald-600 hover:text-emerald-800"
                                    >
                                        ×
                                    </button>
                                </span>
                            </div>
                            <div class="flex gap-2">
                                <input
                                    v-model="newTag"
                                    @keyup.enter="addTag"
                                    type="text"
                                    placeholder="Add tags (press Enter)"
                                    class="flex-1 px-4 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                />
                                <button
                                    type="button"
                                    @click="addTag"
                                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 font-medium"
                                >
                                    Add
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">
                                Add relevant tags to help others find your post (e.g., ai, python, math)
                            </p>
                        </div>

                        <!-- Guidelines -->
                        <div class="mb-8 p-4 bg-amber-50 border border-amber-200 rounded-xl">
                            <h3 class="font-medium text-amber-900 mb-2 flex items-center">
                                <ExclamationTriangleIcon class="h-5 w-5 mr-2" />
                                Community Guidelines
                            </h3>
                            <ul class="text-sm text-amber-800 space-y-1">
                                <li>• Be respectful and kind to other learners</li>
                                <li>• Share only educational content</li>
                                <li>• No spam or self-promotion</li>
                                <li>• Keep discussions on-topic</li>
                                <li>• Cite sources when sharing resources</li>
                            </ul>
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-end space-x-4">
                            <Link
                                :href="route('community.index')"
                                class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                :disabled="processing"
                                :class="[
                                    'px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-xl font-medium',
                                    'hover:from-emerald-700 hover:to-teal-700 transform transition-all duration-300 hover:scale-105',
                                    'disabled:opacity-50 disabled:cursor-not-allowed'
                                ]"
                            >
                                <span v-if="processing">
                                    <ArrowPathIcon class="h-5 w-5 animate-spin inline mr-2" />
                                    Publishing...
                                </span>
                                <span v-else>
                                    <PaperAirplaneIcon class="h-5 w-5 inline mr-2" />
                                    Publish Post
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue'; // Changed from GuestLayout
import {
    ArrowLeftIcon,
    ArrowPathIcon,
    PaperAirplaneIcon,
    ExclamationTriangleIcon,
    BoldIcon,
    ItalicIcon,
    CodeBracketIcon,
    LinkIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    courses: Array,
});

const processing = ref(false);
const newTag = ref('');

const postTypes = [
    { value: 'discussion', icon: '💬', label: 'Discussion', description: 'Start a conversation' },
    { value: 'question', icon: '❓', label: 'Question', description: 'Ask for help' },
    { value: 'achievement', icon: '🏆', label: 'Achievement', description: 'Share success' },
    { value: 'resource', icon: '📚', label: 'Resource', description: 'Share learning materials' },
];

const form = reactive({
    title: '',
    content: '',
    type: 'discussion',
    course_id: null,
    tags: [],
});

function insertFormat(type) {
    const textarea = document.querySelector('textarea');
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const selectedText = form.content.substring(start, end);
    
    let formattedText = '';
    switch(type) {
        case 'bold':
            formattedText = `**${selectedText}**`;
            break;
        case 'italic':
            formattedText = `*${selectedText}*`;
            break;
        case 'code':
            formattedText = `\`${selectedText}\``;
            break;
        case 'link':
            formattedText = `[${selectedText || 'link text'}](url)`;
            break;
    }
    
    form.content = form.content.substring(0, start) + formattedText + form.content.substring(end);
}

function addTag() {
    if (newTag.value.trim() && !form.tags.includes(newTag.value.trim())) {
        form.tags.push(newTag.value.trim());
        newTag.value = '';
    }
}

function removeTag(tag) {
    form.tags = form.tags.filter(t => t !== tag);
}

function submitForm() {
    processing.value = true;
    router.post(route('community.posts.store'), form, {
        onFinish: () => processing.value = false,
    });
}

// Optional: Redirect if not authenticated
onMounted(() => {
    // Add auth check if needed
    // if (!props.auth.user) {
    //     router.visit(route('login'));
    // }
});
</script>