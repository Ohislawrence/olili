<!-- resources/js/Pages/frontpages/courses/Index.vue -->
<template>
    <AppLayout>
        <!-- Header -->
        <section class="relative py-20 bg-gradient-to-br from-emerald-600 to-teal-600 text-white">
            <div class="absolute inset-0 bg-black/20"></div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="text-center">
                    <h1 class="text-4xl md:text-6xl font-bold mb-6">Explore Courses</h1>
                    <p class="text-xl md:text-2xl mb-8 max-w-3xl mx-auto">
                        Discover our AI-powered courses designed to help you learn smarter and faster
                    </p>

                    <!-- Search Bar -->
                    <div class="max-w-2xl mx-auto">
                        <div class="relative">
                            <input
                                type="text"
                                v-model="search"
                                placeholder="Search courses by title, subject, or description..."
                                class="w-full px-6 py-4 rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 text-white placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white/50"
                                @keyup.enter="performSearch"
                            >
                            <button
                                @click="performSearch"
                                class="absolute right-2 top-2 p-2 text-white hover:bg-white/10 rounded-lg transition-colors"
                            >
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Filters & Content -->
        <section class="py-12 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row gap-8">
                    <!-- Sidebar Filters -->
                    <div class="lg:w-1/4">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 sticky top-24">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Filters</h3>

                            <!-- Subject Filter -->
                            <div class="mb-6">
                                <h4 class="font-medium text-gray-900 mb-3">Subject</h4>
                                <div class="space-y-2">
                                    <label
                                        v-for="subject in subjects"
                                        :key="subject"
                                        class="flex items-center cursor-pointer group"
                                    >
                                        <input
                                            type="checkbox"
                                            :value="subject"
                                            v-model="selectedSubjects"
                                            @change="applyFilters"
                                            class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                                        >
                                        <span class="ml-3 text-gray-700 group-hover:text-emerald-600 transition-colors">
                                            {{ subject }}
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <!-- Level Filter -->
                            <div class="mb-6">
                                <h4 class="font-medium text-gray-900 mb-3">Level</h4>
                                <div class="space-y-2">
                                    <label
                                        v-for="level in levels"
                                        :key="level"
                                        class="flex items-center cursor-pointer group"
                                    >
                                        <input
                                            type="checkbox"
                                            :value="level"
                                            v-model="selectedLevels"
                                            @change="applyFilters"
                                            class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                                        >
                                        <span class="ml-3 text-gray-700 group-hover:text-emerald-600 transition-colors">
                                            {{ level }}
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <!-- Status Filter -->
                            <div class="mb-6">
                                <h4 class="font-medium text-gray-900 mb-3">Status</h4>
                                <div class="space-y-2">
                                    <label class="flex items-center cursor-pointer group">
                                        <input
                                            type="checkbox"
                                            value="active"
                                            v-model="selectedStatus"
                                            @change="applyFilters"
                                            class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                                        >
                                        <span class="ml-3 text-gray-700 group-hover:text-emerald-600 transition-colors">
                                            Active Courses
                                        </span>
                                    </label>
                                    <label class="flex items-center cursor-pointer group">
                                        <input
                                            type="checkbox"
                                            value="completed"
                                            v-model="selectedStatus"
                                            @change="applyFilters"
                                            class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                                        >
                                        <span class="ml-3 text-gray-700 group-hover:text-emerald-600 transition-colors">
                                            Completed Courses
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <!-- Clear Filters -->
                            <button
                                @click="clearFilters"
                                class="w-full py-2 px-4 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
                            >
                                Clear All Filters
                            </button>
                        </div>

                        <!-- Quick Stats -->
                        <div class="mt-6 bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Learning Stats</h3>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Total Courses</span>
                                    <span class="font-semibold text-gray-900">{{ totalCourses }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Available Subjects</span>
                                    <span class="font-semibold text-gray-900">{{ subjects.length }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Learning Hours</span>
                                    <span class="font-semibold text-gray-900">{{ totalLearningHours }}+</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Main Content -->
                    <div class="lg:w-3/4">
                        <!-- Results Header -->
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900">
                                    {{ courses.data.length }} Course{{ courses.data.length !== 1 ? 's' : '' }} Found
                                </h2>
                                <p class="text-gray-600 mt-1" v-if="hasActiveFilters">
                                    Filtered by: {{ activeFiltersText }}
                                </p>
                            </div>

                            <!-- Sort Options -->
                            <div class="flex items-center space-x-4">
                                <select
                                    v-model="sortBy"
                                    @change="applySorting"
                                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                >
                                    <option value="latest">Latest</option>
                                    <option value="popular">Most Popular</option>
                                    <option value="duration">Duration</option>
                                    <option value="progress">Progress</option>
                                </select>
                            </div>
                        </div>

                        <!-- Courses Grid -->
                        <div v-if="courses.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                            <div
                                v-for="course in courses.data"
                                :key="course.id"
                                class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-500 course-card group"
                            >
                                <!-- Course Image/Header -->
                                <div class="h-48 relative overflow-hidden bg-gradient-to-br from-emerald-500 to-teal-600">
                                    <div class="absolute inset-0 bg-black/20 group-hover:bg-black/10 transition-all duration-300"></div>
                                    <div class="absolute inset-0 flex items-center justify-center p-6">
                                        <div class="text-center transform transition-transform group-hover:scale-105 duration-300">
                                            <span class="inline-block px-3 py-1 rounded-full text-sm bg-white/20 backdrop-blur-sm border border-white/30 text-white font-medium mb-3">
                                                {{ course.subject }}
                                            </span>
                                            <h3 class="text-xl font-bold text-white leading-tight line-clamp-2">{{ course.title }}</h3>
                                            <p class="text-white/90 text-sm mt-2">{{ course.level }}</p>
                                        </div>
                                    </div>

                                    <!-- Progress Badge -->
                                    <div class="absolute top-4 right-4">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs bg-white/20 backdrop-blur-sm text-white border border-white/30">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ course.progress_percentage || 0 }}%
                                        </span>
                                    </div>

                                    <!-- Status Badge -->
                                    <div class="absolute top-4 left-4">
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs backdrop-blur-sm border font-medium"
                                            :class="getStatusClasses(course.status)"
                                        >
                                            {{ course.status }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Course Content -->
                                <div class="p-6">
                                    <p class="text-gray-700 mb-4 leading-relaxed line-clamp-3">{{ course.description }}</p>

                                    <!-- Course Meta -->
                                    <div class="space-y-3 mb-4">
                                        <div class="flex items-center justify-between text-sm text-gray-600">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                {{ course.estimated_duration_hours || 'N/A' }} Hours
                                            </div>
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                                </svg>
                                                {{ course.modules_count || 0 }} Modules
                                            </div>
                                        </div>

                                        <!-- Dates -->
                                        <div class="flex items-center justify-between text-xs text-gray-500">
                                            <span>Start: {{ course.start_date || 'Flexible' }}</span>
                                            <span v-if="course.target_completion_date">Due: {{ course.target_completion_date }}</span>
                                        </div>
                                    </div>

                                    <!-- Progress Bar -->
                                    <div class="w-full bg-gray-200 rounded-full h-2 mb-4">
                                        <div
                                            class="bg-gradient-to-r from-emerald-500 to-teal-600 h-2 rounded-full transition-all duration-500 ease-out"
                                            :style="{ width: (course.progress_percentage || 0) + '%' }"
                                        ></div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="flex space-x-3">
                                        <Link
                                            :href="route('courses.show', course.id)"
                                            class="flex-1 bg-emerald-600 text-white text-center py-3 px-4 rounded-xl font-semibold hover:bg-emerald-700 transform transition-all duration-300 group-hover:scale-105"
                                        >
                                            View Course
                                        </Link>
                                        <button
                                            v-if="course.progress_percentage > 0"
                                            class="px-4 py-3 border border-emerald-600 text-emerald-600 rounded-xl font-semibold hover:bg-emerald-50 transition-colors"
                                        >
                                            Continue
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div v-else class="text-center py-16 bg-white rounded-2xl border border-gray-200">
                            <div class="w-24 h-24 mx-auto mb-6 text-gray-400">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-semibold text-gray-900 mb-3">No Courses Found</h3>
                            <p class="text-gray-600 mb-6 max-w-md mx-auto">
                                {{ hasActiveFilters
                                    ? 'Try adjusting your filters to see more courses.'
                                    : 'Check back soon for new course offerings.'
                                }}
                            </p>
                            <button
                                v-if="hasActiveFilters"
                                @click="clearFilters"
                                class="bg-emerald-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-emerald-700 transition-colors"
                            >
                                Clear Filters
                            </button>
                            <Link
                                v-else
                                :href="route('welcome')"
                                class="bg-emerald-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-emerald-700 transition-colors"
                            >
                                Back to Home
                            </Link>
                        </div>

                        <!-- Pagination -->
                        <div v-if="courses.data.length > 0" class="mt-12 flex justify-center">
                            <div class="flex space-x-2">
                                <button
                                    v-for="(link, index) in courses.links"
                                    :key="index"
                                    @click="loadPage(link.url)"
                                    class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors flex items-center"
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
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-16 bg-gradient-to-br from-emerald-600 to-teal-600 text-white">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-3xl font-bold mb-6">Ready to Start Learning?</h2>
                <p class="text-xl text-emerald-100 mb-8">
                    Join thousands of learners who are already transforming their education with AI-powered courses.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <Link
                        :href="route('register')"
                        class="bg-white text-emerald-600 px-8 py-4 rounded-xl font-semibold hover:bg-gray-100 transform hover:scale-105 transition-all duration-300"
                    >
                        Start Learning Free
                    </Link>
                    <Link
                        :href="route('pricing')"
                        class="bg-transparent border-2 border-white text-white px-8 py-4 rounded-xl font-semibold hover:bg-white/10 transform hover:scale-105 transition-all duration-300"
                    >
                        View Plans
                    </Link>
                </div>
            </div>
        </section>
    </AppLayout>
</template>

<script setup>
import { Link, router,Head } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import AppLayout from '@/Layouts/GuestLayout.vue';

const props = defineProps({
    courses: Object,
    subjects: Array,
    levels: Array,
    filters: Object
});

// Reactive state
const search = ref(props.filters.search || '');
const selectedSubjects = ref(props.filters.subjects ? props.filters.subjects.split(',') : []);
const selectedLevels = ref(props.filters.levels ? props.filters.levels.split(',') : []);
const selectedStatus = ref(props.filters.status ? props.filters.status.split(',') : []);
const sortBy = ref(props.filters.sort || 'latest');

// Computed properties
const hasActiveFilters = computed(() => {
    return search.value || selectedSubjects.value.length > 0 || selectedLevels.value.length > 0 || selectedStatus.value.length > 0;
});

const activeFiltersText = computed(() => {
    const filters = [];
    if (search.value) filters.push(`"${search.value}"`);
    if (selectedSubjects.value.length > 0) filters.push(`${selectedSubjects.value.length} subject${selectedSubjects.value.length > 1 ? 's' : ''}`);
    if (selectedLevels.value.length > 0) filters.push(`${selectedLevels.value.length} level${selectedLevels.value.length > 1 ? 's' : ''}`);
    if (selectedStatus.value.length > 0) filters.push(`${selectedStatus.value.length} status${selectedStatus.value.length > 1 ? 'es' : ''}`);
    return filters.join(', ');
});

const totalCourses = computed(() => {
    return props.courses.total || props.courses.data.length;
});

const totalLearningHours = computed(() => {
    return props.courses.data.reduce((total, course) => {
        return total + (course.estimated_duration_hours || 0);
    }, 0);
});

// Methods
const performSearch = () => {
    applyFilters();
};

const applyFilters = () => {
    const params = {
        search: search.value || null,
        subjects: selectedSubjects.value.length > 0 ? selectedSubjects.value.join(',') : null,
        levels: selectedLevels.value.length > 0 ? selectedLevels.value.join(',') : null,
        status: selectedStatus.value.length > 0 ? selectedStatus.value.join(',') : null,
        sort: sortBy.value
    };

    router.get(route('courses.index'), params, {
        preserveState: true,
        replace: true
    });
};

const applySorting = () => {
    applyFilters();
};

const clearFilters = () => {
    search.value = '';
    selectedSubjects.value = [];
    selectedLevels.value = [];
    selectedStatus.value = [];
    sortBy.value = 'latest';
    applyFilters();
};

const loadPage = (url) => {
    if (url) {
        router.visit(url, {
            preserveState: true,
            preserveScroll: true
        });
    }
};

const getStatusClasses = (status) => {
    const baseClasses = 'inline-flex items-center px-3 py-1 rounded-full text-xs backdrop-blur-sm border font-medium';

    switch (status?.toLowerCase()) {
        case 'active':
            return `${baseClasses} bg-green-100 text-green-800 border-green-200`;
        case 'completed':
            return `${baseClasses} bg-blue-100 text-blue-800 border-blue-200`;
        case 'draft':
            return `${baseClasses} bg-gray-100 text-gray-800 border-gray-200`;
        default:
            return `${baseClasses} bg-gray-100 text-gray-800 border-gray-200`;
    }
};

// Watch for search changes with debounce
let searchTimeout;
watch(search, (newValue) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        performSearch();
    }, 500);
});
</script>

<style scoped>
.course-card {
    transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.course-card:hover {
    transform: translateY(-8px);
}

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

/* Sticky sidebar on larger screens */
@media (min-width: 1024px) {
    .sticky {
        position: sticky;
    }
}
</style>
