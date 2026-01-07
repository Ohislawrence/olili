<template>
  <MetaTags
    title="AI-Powered Courses | Learn Smarter with OliLearn"
    description="Browse our catalog of intelligent, AI-enhanced courses designed by experts. Master any subject with personalized learning paths and real-time guidance."
    image="/images/olingolearn.png"
    type="website"
  />
  <AppLayout>
    <Head title="Discover AI-Powered Courses | OliLearn" />

    <!-- Main Content -->
    <section class="py-8 md:py-12 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header & Search -->
        <div class="mb-8">
          <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
            <div>
              <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 mb-2">
                Discover
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-teal-600">
                  AI-Powered Courses
                </span>
              </h1>
              <p class="text-gray-600 max-w-2xl">
                Expertly designed courses enhanced with artificial intelligence for smarter learning.
              </p>
            </div>


          </div>

          <!-- Quick Search Tags -->
          <div class="flex flex-wrap items-center gap-2 mb-6">
            <span class="text-sm text-gray-600">Popular:</span>
            <div class="flex flex-wrap gap-2">
              <button
                v-for="tag in popularTags"
                :key="tag"
                @click="applyTagSearch(tag)"
                class="px-3 py-1.5 text-sm bg-gray-100 hover:bg-emerald-50 border border-gray-200 hover:border-emerald-300 text-gray-700 hover:text-emerald-700 rounded-lg transition-colors"
              >
                {{ tag }}
              </button>
            </div>
          </div>

          <div class="bg-white rounded-2xl border border-gray-200 shadow-sm mb-10">

  <!-- Header -->
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 px-6 py-5 border-b">
    <div>
      <h2 class="text-lg font-semibold text-gray-900">Filter courses</h2>
      <p class="text-sm text-gray-500">Tap to refine results</p>
    </div>

    <div class="flex items-center gap-3">
      <span v-if="hasActiveFilters" class="text-sm text-gray-500">
        {{ getTotalFilters() }} active
      </span>

      <button
        v-if="hasActiveFilters"
        @click="clearFilters"
        class="text-sm font-medium text-gray-600 hover:text-gray-900"
      >
        Clear all
      </button>
    </div>
  </div>

  <!-- Filters -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6">

    <!-- Subjects -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-3">
        Subjects
      </label>

      <div class="flex flex-wrap gap-2">
        <button
          v-for="subject in subjects"
          :key="subject"
          @click="toggleSubject(subject)"
          class="px-4 py-2 rounded-full text-sm font-medium border transition"
          :class="
            selectedSubjects.includes(subject)
              ? 'bg-emerald-600 text-white border-emerald-600'
              : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50'
          "
        >
          {{ subject }}
        </button>
      </div>
    </div>

    <!-- Skill Level -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-3">
        Skill level
      </label>

      <div class="flex flex-wrap gap-2">
        <button
          v-for="level in levels"
          :key="level"
          @click="toggleLevel(level)"
          class="px-4 py-2 rounded-full text-sm font-medium border transition"
          :class="
            selectedLevels.includes(level)
              ? 'bg-emerald-600 text-white border-emerald-600'
              : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50'
          "
        >
          {{ level }}
        </button>
      </div>
    </div>

    <!-- Features -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-3">
        Features
      </label>

      <div class="flex flex-wrap gap-2">
        <button
          @click="hasCertificate = !hasCertificate; applyFilters()"
          class="px-4 py-2 rounded-full text-sm font-medium border transition"
          :class="
            hasCertificate
              ? 'bg-emerald-600 text-white border-emerald-600'
              : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50'
          "
        >
          Certificate
        </button>

        <button
          @click="hasProjects = !hasProjects; applyFilters()"
          class="px-4 py-2 rounded-full text-sm font-medium border transition"
          :class="
            hasProjects
              ? 'bg-emerald-600 text-white border-emerald-600'
              : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50'
          "
        >
          Projects
        </button>
      </div>
    </div>

  </div>

  <!-- Active Filters -->
  <div v-if="hasActiveFilters" class="px-6 pb-6">
    <div class="flex flex-wrap gap-2">

      <span
        v-for="subject in selectedSubjects"
        :key="subject"
        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm bg-gray-100"
      >
        {{ subject }}
        <button @click="removeFilter('subject', subject)">✕</button>
      </span>

      <span
        v-for="level in selectedLevels"
        :key="level"
        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm bg-emerald-100 text-emerald-700"
      >
        {{ level }}
        <button @click="removeFilter('level', level)">✕</button>
      </span>

    </div>
  </div>
</div>

          </div>

        <!-- Results Header -->
        <div class="flex items-center justify-between mb-6">
          <div>
            <h2 class="text-lg font-semibold text-gray-900">
              <span class="text-emerald-600">{{ courses.data.length }}</span>
              {{ courses.data.length === 1 ? 'Course' : 'Courses' }} Available
            </h2>
            <p v-if="hasActiveFilters" class="text-sm text-gray-600 mt-1">
              Filtered by: <span class="font-medium">{{ activeFiltersText }}</span>
            </p>
          </div>
          <div class="text-sm text-gray-600">
            Total: {{ totalCourses }} courses • {{ totalLearningHours }}+ learning hours
          </div>
        </div>

        <!-- Courses Grid -->
        <div v-if="courses.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div
            v-for="course in courses.data"
            :key="course.id"
            class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 group"
          >
            <!-- Course Image/Header -->
            <div class="h-48 relative overflow-hidden bg-gradient-to-br from-emerald-500 to-teal-600">
              <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
              <div class="absolute inset-0 flex items-end p-6">
                <div>
                  <span class="inline-block px-3 py-1 rounded-full text-xs bg-white/30 backdrop-blur-sm text-white mb-3 font-medium">{{ course.subject }}</span>
                  <h3 class="text-white font-bold text-xl line-clamp-2">{{ course.title }}</h3>
                </div>
              </div>
              <div class="absolute top-4 right-4">
                <span class="text-xs text-white bg-black/30 px-2 py-1 rounded-full">AI-Enhanced</span>
              </div>
            </div>
            <div class="p-6">
              <p class="text-gray-700 text-sm mb-5 line-clamp-3">{{ course.description || 'Transform your career with this comprehensive course designed for modern learners.' }}</p>

              <div class="flex items-center justify-between text-xs text-gray-600 mb-5">
                <div class="flex items-center space-x-4">
                  <span class="flex items-center">
                    <svg class="w-4 h-4 mr-1 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    {{ course.estimated_duration_hours || 'Self-paced' }} hrs
                  </span>
                  <span class="flex items-center">
                    <svg class="w-4 h-4 mr-1 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                    </svg>
                    {{ course.modules_count || 0 }} modules
                  </span>
                </div>
                <span class="text-emerald-700 font-medium">{{ course.level}}</span>
              </div>



              <Link
                :href="route('courses.show', { id: course.id, slug: course.slug })"
                class="w-full block text-center bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white py-3 rounded-xl font-medium transition-all duration-300 group-hover:shadow-lg"
              >
                Enroll Now • Start Today
              </Link>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-12 bg-gray-50 rounded-xl border border-gray-200">
          <div class="w-16 h-16 mx-auto mb-4 text-gray-400">
            <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
            </svg>
          </div>
          <h3 class="text-xl font-bold text-gray-900 mb-2">No courses match your search</h3>
          <p class="text-gray-600 max-w-md mx-auto mb-6">
            {{ hasActiveFilters
              ? 'Try adjusting your filters or search terms to find what you\'re looking for.'
              : 'We\'re constantly adding new courses. Check back soon!'
            }}
          </p>
          <button
            v-if="hasActiveFilters"
            @click="clearFilters"
            class="px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white rounded-lg font-medium transition-all hover:shadow-md"
          >
            Clear All Filters
          </button>
        </div>

        <!-- Pagination -->
        <div v-if="courses.data.length > 0 && courses.links.length > 3" class="mt-8">
          <nav class="flex items-center justify-center space-x-2">
            <button
              v-for="(link, index) in courses.links"
              :key="index"
              @click="loadPage(link.url)"
              v-html="link.label"
              :disabled="!link.url || link.active"
              :class="[
                'px-3 py-1.5 rounded-lg text-sm font-medium transition-all',
                link.active
                  ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow'
                  : link.url
                  ? 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 hover:border-gray-400'
                  : 'bg-gray-100 border border-gray-200 text-gray-400 cursor-not-allowed'
              ]"
            ></button>
          </nav>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="py-12 bg-gradient-to-br from-emerald-900 via-emerald-800 to-teal-900 text-white">
      <div class="max-w-4xl mx-auto px-4 text-center">
        <h2 class="text-2xl md:text-3xl font-bold mb-4">Ready to Start Learning?</h2>
        <p class="text-emerald-100 mb-6 max-w-2xl mx-auto">
          Join thousands of learners mastering new skills with our AI-powered platform.
        </p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
          <Link
            :href="route('register')"
            class="bg-white text-emerald-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors"
          >
            Start Free Trial
          </Link>
          <Link
            :href="route('pricing')"
            class="bg-transparent border border-white text-white px-6 py-3 rounded-lg font-semibold hover:bg-white/10 transition-colors"
          >
            View Pricing Plans
          </Link>
        </div>
      </div>
    </section>
  </AppLayout>
</template>

<script setup>
import { Link, router, Head } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import AppLayout from '@/Layouts/GuestLayout.vue';
import MetaTags from '@/Components/MetaTags.vue';

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
const sortBy = ref(props.filters.sort || 'latest');
const hasCertificate = ref(props.filters.certificate === 'true');
const hasProjects = ref(props.filters.projects === 'true');

// Popular search tags
const popularTags = [
  'Python',
  'Data Science',
  'Web Development',
  'AI',
  'Marketing',
  'Design',
  'Business',
  'Programming'
];

// Computed properties
const hasActiveFilters = computed(() => {
  return search.value ||
         selectedSubjects.value.length > 0 ||
         selectedLevels.value.length > 0 ||
         hasCertificate.value ||
         hasProjects.value;
});

const activeFiltersText = computed(() => {
  const parts = [];
  if (search.value) parts.push(`"${search.value}"`);
  if (selectedSubjects.value.length) parts.push(`${selectedSubjects.value.length} subject${selectedSubjects.value.length > 1 ? 's' : ''}`);
  if (selectedLevels.value.length) parts.push(`${selectedLevels.value.length} level${selectedLevels.value.length > 1 ? 's' : ''}`);
  if (hasCertificate.value) parts.push('Certificate');
  if (hasProjects.value) parts.push('Projects');
  return parts.join(' • ');
});

const totalCourses = computed(() => props.courses.total || props.courses.data.length);
const totalLearningHours = computed(() => {
  return props.courses.data.reduce((sum, course) => sum + (course.estimated_duration_hours || 0), 0);
});

// Methods
const performSearch = () => applyFilters();
const applyTagSearch = (tag) => {
  search.value = tag;
  applyFilters();
};

const toggleLevel = (level) => {
  const index = selectedLevels.value.indexOf(level);
  if (index === -1) {
    selectedLevels.value.push(level);
  } else {
    selectedLevels.value.splice(index, 1);
  }
  applyFilters();
};

const removeFilter = (type, value) => {
  if (type === 'subject') {
    const index = selectedSubjects.value.indexOf(value);
    if (index !== -1) selectedSubjects.value.splice(index, 1);
  } else if (type === 'level') {
    const index = selectedLevels.value.indexOf(value);
    if (index !== -1) selectedLevels.value.splice(index, 1);
  } else if (type === 'search') {
    search.value = '';
  }
  applyFilters();
};

const applyFilters = () => {
  router.get(route('courses.index'), {
    search: search.value || null,
    subjects: selectedSubjects.value.length ? selectedSubjects.value.join(',') : null,
    levels: selectedLevels.value.length ? selectedLevels.value.join(',') : null,
    certificate: hasCertificate.value ? 'true' : null,
    projects: hasProjects.value ? 'true' : null,
    sort: sortBy.value
  }, {
    preserveState: true,
    preserveScroll: true,
    replace: true
  });
};

const applySorting = () => applyFilters();

const clearFilters = () => {
  search.value = '';
  selectedSubjects.value = [];
  selectedLevels.value = [];
  hasCertificate.value = false;
  hasProjects.value = false;
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

// Debounced search
let searchTimeout;
watch(search, () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(performSearch, 500);
});

const sortOptions = computed(() => [
  { value: 'latest', label: 'Latest First' },
  { value: 'popular', label: 'Most Popular' },
  { value: 'duration', label: 'Shortest First' },
  { value: 'rating', label: 'Highest Rated' }
]);

const getSortLabel = () => {
  const option = sortOptions.value.find(opt => opt.value === sortBy.value);
  return option ? option.label : 'Sort By';
};
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
