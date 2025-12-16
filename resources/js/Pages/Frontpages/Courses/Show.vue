<!-- resources/js/Pages/frontpages/courses/Show.vue -->
<template>
    <MetaTags
        :title="course.title"
        :description="course.description"
        :image="course.image_url || '/images/olingolearn.png'"
        type="article"
    />
    <AppLayout>
        <Head :title="`${course.title}`" />
        <!-- Course Header -->
        <section class="relative py-20 bg-gradient-to-br from-emerald-600 to-teal-600 text-white">
            <div class="absolute inset-0 bg-black/20"></div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="flex flex-col lg:flex-row items-start gap-8">
                    <!-- Course Info -->
                    <div class="flex-1">
                        <div class="inline-flex items-center px-4 py-2 rounded-full bg-white/20 backdrop-blur-sm border border-white/30 text-white text-sm font-medium mb-6">
                            {{ course.subject }} • {{ course.level }}
                        </div>
                        <h1 class="text-4xl md:text-5xl font-bold mb-6 leading-tight">{{ course.title }}</h1>
                        <p class="text-xl text-emerald-100 mb-8 leading-relaxed">{{ course.description }}</p>

                        <!-- Course Meta -->
                        <div class="flex flex-wrap gap-6 mb-8">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <div class="font-semibold">{{ course.estimated_duration_hours }} Hours</div>
                                    <div class="text-sm text-emerald-200">Estimated Duration</div>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                <div>
                                    <div class="font-semibold">{{ course.modules?.length || 0 }} Modules</div>
                                    <div class="text-sm text-emerald-200">Learning Path</div>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <div class="font-semibold">{{ course.status }}</div>
                                    <div class="text-sm text-emerald-200">Course Status</div>
                                </div>
                            </div>
                        </div>



                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row gap-4">
                            <button
                                v-if="course.progress_percentage > 0"
                                class="bg-white text-emerald-600 px-8 py-4 rounded-xl font-semibold hover:bg-gray-100 transform hover:scale-105 transition-all duration-300 flex items-center justify-center"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Continue Learning
                            </button>
                            <button
                                v-else
                                class="bg-white text-emerald-600 px-8 py-4 rounded-xl font-semibold hover:bg-gray-100 transform hover:scale-105 transition-all duration-300 flex items-center justify-center"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                </svg>
                                Start Course
                            </button>
                            <button class="bg-transparent border-2 border-white text-white px-8 py-4 rounded-xl font-semibold hover:bg-white/10 transform hover:scale-105 transition-all duration-300">
                                <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                                Save for Later
                            </button>
                        </div>
                    </div>

                    <!-- Course Visual -->
                    <div class="lg:w-96 flex-shrink-0">
                        <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl p-6">
                            <div class="text-center mb-6">
                                <div class="w-20 h-20 mx-auto mb-4 bg-white/20 rounded-2xl flex items-center justify-center text-white text-2xl">
                                    📚
                                </div>
                                <h3 class="text-xl font-bold mb-2">Course Overview</h3>
                                <p class="text-emerald-200">AI-Powered Learning Path</p>
                            </div>

                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-emerald-200">Difficulty</span>
                                    <span class="font-semibold">{{ course.level }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-emerald-200">Last Updated</span>
                                    <span class="font-semibold">{{ formatDate(course.updated_at) }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-emerald-200">Enrolled</span>
                                    <span class="font-semibold">1,234 Students</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-emerald-200">Rating</span>
                                    <div class="flex items-center">
                                        <svg v-for="n in 5" :key="n" class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                        <span class="ml-2 font-semibold">4.8</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Course Content -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row gap-12">
                    <!-- Main Content -->
                    <div class="lg:w-2/3">
                        <!-- Learning Objectives -->
                        <div class="mb-12">
                            <h2 class="text-3xl font-bold text-gray-900 mb-6">What You'll Learn</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div
                                    v-for="(objective, index) in course.learning_objectives || []"
                                    :key="index"
                                    class="flex items-start p-4 bg-gray-50 rounded-xl"
                                >
                                    <svg class="w-5 h-5 text-emerald-600 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-gray-700">{{ objective }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Course Curriculum -->
                        <div class="mb-12">
                            <h2 class="text-3xl font-bold text-gray-900 mb-6">Course Curriculum</h2>
                            <div class="space-y-4">
                                <div
                                    v-for="module in course.modules || []"
                                    :key="module.id"
                                    class="bg-white border border-gray-200 rounded-2xl overflow-hidden hover:shadow-lg transition-all duration-300"
                                >
                                    <button
                                        @click="toggleModule(module.id)"
                                        class="w-full flex items-center justify-between p-6 text-left hover:bg-gray-50 transition-colors"
                                    >
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center text-emerald-600 font-semibold mr-4">
                                                {{ module.order }}
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-900">{{ module.title }}</h3>
                                                <p class="text-gray-600 text-sm mt-1">
                                                    {{ module.topics?.length || 0 }} topics • {{ module.estimated_duration_hours || 2 }} hours
                                                </p>
                                            </div>
                                        </div>
                                        <svg
                                            class="w-5 h-5 text-gray-400 transform transition-transform"
                                            :class="{ 'rotate-180': expandedModule === module.id }"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>

                                    <!-- Module Topics -->
                                    <div v-if="expandedModule === module.id" class="border-t border-gray-200">
                                        <div class="p-6 space-y-3">
                                            <div
                                                v-for="topic in module.topics || []"
                                                :key="topic.id"
                                                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
                                            >
                                                <div class="flex items-center">
                                                    <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center text-gray-600 text-sm font-semibold mr-3">
                                                        {{ topic.order }}
                                                    </div>
                                                    <div>
                                                        <h4 class="font-medium text-gray-900">{{ topic.title }}</h4>
                                                        <p class="text-gray-600 text-sm">{{ topic.estimated_duration_minutes }} minutes</p>
                                                    </div>
                                                </div>
                                                <button class="text-emerald-600 hover:text-emerald-700 font-semibold text-sm">
                                                    Start
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Prerequisites -->
                        <div v-if="course.prerequisites && course.prerequisites.length > 0" class="mb-12">
                            <h2 class="text-3xl font-bold text-gray-900 mb-6">Prerequisites</h2>
                            <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6">
                                <ul class="space-y-2">
                                    <li
                                        v-for="(prereq, index) in course.prerequisites"
                                        :key="index"
                                        class="flex items-center text-blue-800"
                                    >
                                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ prereq }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="lg:w-1/3">
                        <!-- Course Actions -->
                        <div class="bg-gray-50 rounded-2xl p-6 mb-8 sticky top-24">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Course Actions</h3>
                            <div class="space-y-3">
                                <button class="w-full bg-emerald-600 text-white py-3 rounded-xl font-semibold hover:bg-emerald-700 transition-colors flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                    </svg>
                                    Enroll in Course
                                </button>
                                <button class="w-full border border-gray-300 text-gray-700 py-3 rounded-xl font-semibold hover:bg-gray-100 transition-colors flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                    </svg>
                                    Save for Later
                                </button>
                                <button class="w-full border border-gray-300 text-gray-700 py-3 rounded-xl font-semibold hover:bg-gray-100 transition-colors flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                                    </svg>
                                    Share Course
                                </button>
                            </div>
                        </div>

                        <!-- Related Courses -->
                        <div v-if="relatedCourses.length > 0" class="bg-white border border-gray-200 rounded-2xl p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Related Courses</h3>
                            <div class="space-y-4">
                                <div
                                    v-for="relatedCourse in relatedCourses"
                                    :key="relatedCourse.id"
                                    class="flex items-start space-x-3 group cursor-pointer"
                                >
                                    <div class="w-16 h-16 flex-shrink-0 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center text-white text-lg font-semibold">
                                        {{ relatedCourse.subject?.charAt(0) || 'C' }}
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-900 group-hover:text-emerald-600 transition-colors line-clamp-2">
                                            {{ relatedCourse.title }}
                                        </h4>
                                        <p class="text-gray-500 text-sm mt-1">{{ relatedCourse.level }}</p>
                                        <div class="flex items-center mt-2 text-sm text-gray-600">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ relatedCourse.estimated_duration_hours }}h
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </AppLayout>
</template>

<script setup>
import { Link,Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/Layouts/GuestLayout.vue';
import MetaTags from '@/Components/MetaTags.vue';

const props = defineProps({
    course: Object,
    relatedCourses: Array
});

const expandedModule = ref(null);

const toggleModule = (moduleId) => {
    expandedModule.value = expandedModule.value === moduleId ? null : moduleId;
};

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Smooth transitions for module expansion */
.module-content {
    transition: all 0.3s ease-in-out;
}
</style>
