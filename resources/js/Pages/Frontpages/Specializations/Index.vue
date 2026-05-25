<template>
  <MetaTags
    :title="pageTitle"
    :description="pageDescription"
    :image="pageImage"
  />
  <AppLayout>
    <Head :title="pageTitle" />

    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-b from-emerald-900 via-emerald-50 to-white">
      <!-- Decorative Background -->
      <div class="absolute inset-0">
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-emerald-500/20 rounded-full blur-3xl"></div>
        <div class="absolute top-40 -left-24 w-80 h-80 bg-emerald-400/20 rounded-full blur-3xl"></div>
      </div>

      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
          <!-- Left Content -->
          <div>
            <div class="inline-flex items-center gap-2 bg-emerald-100 text-emerald-700 px-5 py-2 rounded-full text-sm font-semibold mb-6">
              🎯 Structured Learning Paths
            </div>

            <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-gray-900 leading-tight mb-5">
              Master Skills That Get You Hired in Nigeria
            </h1>

            <h2 class="text-lg md:text-xl font-semibold text-emerald-700 mb-6">
              Choose a career path, follow structured courses, and build a portfolio that impresses employers.
            </h2>

            <p class="text-gray-700 text-lg leading-relaxed mb-8 max-w-xl">
              OliLearn Specializations are career-focused learning paths that combine multiple courses,
              real-world projects, and AI mentorship to prepare you for Nigerian and global job markets.
            </p>

            <!-- Key Benefits -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-10">
              <div class="flex items-start gap-3">
                <div class="w-7 h-7 rounded-full bg-emerald-600 flex items-center justify-center text-white text-sm font-bold">✓</div>
                <span class="text-gray-700 font-medium">Industry-relevant skills</span>
              </div>
              <div class="flex items-start gap-3">
                <div class="w-7 h-7 rounded-full bg-emerald-600 flex items-center justify-center text-white text-sm font-bold">✓</div>
                <span class="text-gray-700 font-medium">Portfolio projects included</span>
              </div>
              <div class="flex items-start gap-3">
                <div class="w-7 h-7 rounded-full bg-emerald-600 flex items-center justify-center text-white text-sm font-bold">✓</div>
                <span class="text-gray-700 font-medium">AI mentorship & career guidance</span>
              </div>
              <div class="flex items-start gap-3">
                <div class="w-7 h-7 rounded-full bg-emerald-600 flex items-center justify-center text-white text-sm font-bold">✓</div>
                <span class="text-gray-700 font-medium">Nigerian case studies & examples</span>
              </div>
            </div>

            <!-- CTAs -->
            <div class="flex flex-col sm:flex-row gap-4 mb-8">
              <Link
                :href="route('register') + '?campaign=specializations'"
                class="inline-flex items-center justify-center bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-4 rounded-xl font-bold text-lg transition-all duration-300 shadow-lg hover:shadow-xl"
              >
                Start Learning Free
              </Link>
              <Link
                :href="'#categories'"
                class="inline-flex items-center justify-center bg-white border-2 border-emerald-600 text-emerald-700 hover:bg-emerald-50 px-8 py-4 rounded-xl font-semibold text-lg transition-all"
              >
                Browse Specializations
              </Link>
            </div>

            <!-- Trust Signals -->
            <div class="flex flex-wrap items-center gap-6 text-sm text-gray-600">
              <div class="flex items-center gap-2">
                <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
                Project-based learning
              </div>
              <div class="flex items-center gap-2">
                <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
                Certificate of completion
              </div>
              <div class="flex items-center gap-2">
                <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
                Career guidance included
              </div>
            </div>
          </div>

          <!-- Right Stats -->
          <div class="bg-white rounded-3xl shadow-2xl p-8 border border-gray-100">
            <div class="mb-6">
              <h3 class="text-xl font-bold text-gray-900 mb-1">
                Career Outcomes
              </h3>
              <p class="text-gray-600 text-sm">
                Average results after completing specializations
              </p>
            </div>

            <div class="space-y-5">
              <div
                v-for="stat in careerStats"
                :key="stat.label"
              >
                <div class="flex justify-between items-center mb-1">
                  <span class="text-gray-700 font-medium">{{ stat.label }}</span>
                  <span class="font-bold text-emerald-600">{{ stat.value }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                  <div
                    class="h-2.5 rounded-full transition-all"
                    :style="{
                      width: stat.percentage + '%',
                      backgroundColor: stat.color
                    }"
                  ></div>
                </div>
              </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-100">
              <div class="grid grid-cols-3 gap-4 text-center">
                <div>
                  <div class="text-2xl font-extrabold text-emerald-600">
                    {{ totalStudentsFormatted }}
                  </div>
                  <div class="text-xs text-gray-600">Students Enrolled</div>
                </div>
                <div>
                  <div class="text-2xl font-extrabold text-emerald-600">
                    {{ completionRate }}%
                  </div>
                  <div class="text-xs text-gray-600">Completion Rate</div>
                </div>
                <div>
                  <div class="text-2xl font-extrabold text-emerald-600">24/7</div>
                  <div class="text-xs text-gray-600">AI Career Coach</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Categories Filter -->
    <section id="categories" class="py-12 bg-white sticky top-0 z-10 border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
          <div>
            <h2 class="text-2xl font-bold text-gray-900">Browse by Category</h2>
            <p class="text-gray-600 text-sm mt-1">Filter specializations by career field</p>
          </div>
          <div class="text-sm text-gray-500">
            Showing {{ filteredSpecializations.length }} of {{ props.specializations.length }} specializations
          </div>
        </div>

        <!-- Category Filters -->
        <div class="flex flex-wrap gap-2 mb-6">
          <button
            v-for="category in props.categories"
            :key="category.id"
            @click="setActiveCategory(category.id)"
            :class="[
              'px-4 py-2 rounded-full text-sm font-medium transition-all',
              activeCategory === category.id
                ? 'bg-emerald-600 text-white shadow-md'
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
            ]"
          >
            {{ category.name }}
            <span v-if="category.count" class="ml-2 text-xs opacity-80">
              ({{ category.count }})
            </span>
          </button>
        </div>

        <!-- Search and Filter Row -->
        <div class="flex flex-col md:flex-row gap-4">
          <!-- Search Bar -->
          <div class="relative flex-1">
            <input
              type="text"
              v-model="searchQuery"
              placeholder="Search specializations..."
              class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
            >
            <svg class="absolute left-3 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>

          <!-- Level Filter -->
          <div class="flex gap-2">
            <select
              v-model="selectedLevel"
              class="px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
            >
              <option value="">All Levels</option>
              <option value="beginner">Beginner</option>
              <option value="intermediate">Intermediate</option>
              <option value="advanced">Advanced</option>
            </select>

            <!-- Sort Options -->
            <select
              v-model="sortBy"
              class="px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
            >
              <option value="popularity">Most Popular</option>
              <option value="newest">Newest First</option>
              <option value="duration">Shortest First</option>
              <option value="courses">Fewest Courses</option>
            </select>
          </div>
        </div>
      </div>
    </section>

    <!-- Specializations Grid -->
    <section class="py-12 bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Empty State -->
        <div v-if="filteredSpecializations.length === 0" class="col-span-full text-center py-16 bg-white rounded-2xl border border-gray-200">
          <div class="text-gray-400 mb-4">
            <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
          </div>
          <h3 class="text-xl font-semibold text-gray-600 mb-2">No Specializations Found</h3>
          <p class="text-gray-500 mb-6 max-w-md mx-auto">
            Try adjusting your search or filter criteria. All our specializations are designed for Nigerian learners.
          </p>
          <button
            @click="clearFilters"
            class="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-semibold"
          >
            Clear All Filters
            <svg class="w-5 h-5 ml-2" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
          </button>
        </div>

        <!-- Specializations Grid -->
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div
            v-for="specialization in paginatedSpecializations"
            :key="specialization.id"
            class="bg-white border border-gray-200 rounded-2xl overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1"
          >
            <!-- Header with Gradient -->
            <div class="relative h-48 bg-gradient-to-r from-emerald-500 to-emerald-600 overflow-hidden">
              <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>

              <!-- Featured Badge -->
              <div v-if="specialization.is_featured" class="absolute top-4 left-4">
                <span class="inline-block px-3 py-1 rounded-full text-xs bg-yellow-500 text-white font-bold">
                  FEATURED
                </span>
              </div>

              <!-- Discount Badge -->
              <div v-if="specialization.has_discount" class="absolute top-4 right-4">
                <span class="inline-block px-3 py-1 rounded-full text-xs bg-red-500 text-white font-bold">
                  {{ discountPercentage(specialization) }}% OFF
                </span>
              </div>

              <!-- Icon and Title -->
              <div class="absolute bottom-6 left-6 right-6">
                <div class="flex items-start justify-between">
                  <div class="text-4xl mb-3">{{ specialization.icon || '🎓' }}</div>
                  <span class="text-sm bg-white/30 backdrop-blur-sm text-white px-2 py-1 rounded">
                    {{ specialization.level || 'All Levels' }}
                  </span>
                </div>
                <h3 class="text-white font-bold text-xl line-clamp-2">{{ specialization.title }}</h3>
              </div>
            </div>

            <!-- Content -->
            <div class="p-6">
              <p class="text-gray-700 text-sm line-clamp-3 mb-4">
                {{ specialization.description }}
              </p>

              <!-- Skills Covered -->
              <div v-if="specialization.target_skills && specialization.target_skills.length > 0" class="mb-4">
                <div class="text-xs text-gray-500 font-medium mb-2">SKILLS YOU'LL LEARN:</div>
                <div class="flex flex-wrap gap-2 mb-4">
                  <span
                    v-for="skill in specialization.target_skills.slice(0, 4)"
                    :key="skill"
                    class="px-2 py-1 bg-emerald-50 text-emerald-700 text-xs rounded"
                  >
                    {{ skill }}
                  </span>
                  <span v-if="specialization.target_skills.length > 4" class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded">
                    +{{ specialization.target_skills.length - 4 }} more
                  </span>
                </div>
              </div>

              <!-- Details Grid -->
              <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="text-center">
                  <div class="text-2xl font-bold text-emerald-600">{{ specialization.total_courses || 0 }}</div>
                  <div class="text-xs text-gray-600">Courses</div>
                </div>
                <div class="text-center">
                  <div class="text-2xl font-bold text-emerald-600">{{ specialization.estimated_duration_hours || 0 }}</div>
                  <div class="text-xs text-gray-600">Hours</div>
                </div>
              </div>

              <!-- Career Outcomes -->
              <div v-if="specialization.career_opportunities && specialization.career_opportunities.length > 0" class="mb-6">
                <div class="text-xs text-gray-500 font-medium mb-2">CAREER OPPORTUNITIES:</div>
                <div class="flex flex-wrap gap-2">
                  <span
                    v-for="role in specialization.career_opportunities.slice(0, 3)"
                    :key="role"
                    class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded"
                  >
                    {{ role }}
                  </span>
                </div>
              </div>

              <!-- Enrollment Stats -->
              <div class="flex items-center justify-between text-sm text-gray-600 mb-6">
                <div class="flex items-center">
                  <svg class="w-4 h-4 mr-1 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                  </svg>
                  {{ specialization.enrollment_count || 0 }} enrolled
                </div>
                <div v-if="specialization.completion_rate > 0" class="flex items-center">
                  <svg class="w-4 h-4 mr-1 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                  {{ specialization.completion_rate }}% completion
                </div>
              </div>

              <!-- Price and Actions -->
              <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                  <div v-if="specialization.price && specialization.price > 0" class="mb-2">
                    <div v-if="specialization.has_discount && specialization.discount_price" class="flex items-center gap-2">
                      <span class="text-lg font-bold text-emerald-600">₦{{ formatPrice(specialization.discount_price) }}</span>
                      <span class="text-sm text-gray-500 line-through">₦{{ formatPrice(specialization.price) }}</span>
                    </div>
                    <div v-else>
                      <span class="text-lg font-bold text-emerald-600">₦{{ formatPrice(specialization.price) }}</span>
                    </div>
                  </div>
                  <div v-else class="mb-2">
                    <span class="text-lg font-bold text-emerald-600">FREE</span>
                  </div>
                </div>

                <div class="flex gap-2">
                  <Link
                    :href="route('specializations.show', { id: specialization.id, slug: specialization.slug })"
                    class="flex-1 text-center bg-gradient-to-r from-emerald-600 to-emerald-700 text-white py-3 rounded-lg font-medium hover:from-emerald-700 hover:to-emerald-800 transition-all"
                  >
                    View Details
                  </Link>
                  <button
                    @click="toggleFavorite(specialization.id)"
                    class="w-12 flex items-center justify-center border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                    :class="{ 'bg-red-50 border-red-200': isFavorite(specialization.id) }"
                  >
                    <svg
                      class="w-5 h-5"
                      :class="isFavorite(specialization.id) ? 'text-red-500' : 'text-gray-400'"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    >
                      <path
                        v-if="isFavorite(specialization.id)"
                        fill-rule="evenodd"
                        d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                        clip-rule="evenodd"
                      />
                      <path
                        v-else
                        fill-rule="evenodd"
                        d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                        stroke="currentColor"
                        stroke-width="1"
                      />
                    </svg>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Load More / Pagination -->
        <div v-if="filteredSpecializations.length > 0" class="mt-12 text-center">
          <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-sm text-gray-600">
              Showing {{ Math.min(paginatedSpecializations.length, perPage) }} of {{ filteredSpecializations.length }} specializations
            </div>
            <div class="flex gap-2">
              <button
                @click="loadMore"
                v-if="filteredSpecializations.length > visibleCount"
                class="px-6 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium"
              >
                Load More
              </button>
              <Link
                :href="route('register') + '?campaign=specializations'"
                class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors font-medium"
              >
                Start Learning Free
              </Link>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- How Specializations Work -->
    <section class="py-12 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <div class="inline-block bg-emerald-100 text-emerald-700 px-4 py-2 rounded-full text-sm font-medium mb-4">
            🚀 Your Learning Journey
          </div>
          <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">
            How Specializations Work
          </h2>
          <p class="text-gray-600 max-w-2xl mx-auto">
            Follow a structured path from beginner to job-ready professional
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
          <div class="text-center">
            <div class="w-16 h-16 rounded-full bg-emerald-100 flex items-center justify-center mx-auto mb-6">
              <span class="text-2xl">📚</span>
            </div>
            <h3 class="font-bold text-gray-900 mb-3">Choose Path</h3>
            <p class="text-gray-600 text-sm">Select a career-focused specialization</p>
          </div>

          <div class="text-center">
            <div class="w-16 h-16 rounded-full bg-emerald-100 flex items-center justify-center mx-auto mb-6">
              <span class="text-2xl">🎓</span>
            </div>
            <h3 class="font-bold text-gray-900 mb-3">Learn Courses</h3>
            <p class="text-gray-600 text-sm">Complete structured courses in order</p>
          </div>

          <div class="text-center">
            <div class="w-16 h-16 rounded-full bg-emerald-100 flex items-center justify-center mx-auto mb-6">
              <span class="text-2xl">💼</span>
            </div>
            <h3 class="font-bold text-gray-900 mb-3">Build Projects</h3>
            <p class="text-gray-600 text-sm">Create portfolio-ready real projects</p>
          </div>

          <div class="text-center">
            <div class="w-16 h-16 rounded-full bg-emerald-100 flex items-center justify-center mx-auto mb-6">
              <span class="text-2xl">🎯</span>
            </div>
            <h3 class="font-bold text-gray-900 mb-3">Get Certified</h3>
            <p class="text-gray-600 text-sm">Earn certificate and career guidance</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Call to Action -->
    <section class="py-12 bg-gradient-to-r from-emerald-700 to-emerald-800 text-white">
      <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl md:text-3xl font-bold mb-6">
          Ready to Start Your Career Journey?
        </h2>

        <p class="text-lg text-emerald-100 mb-8 max-w-2xl mx-auto">
          Choose from our industry-aligned specializations and build the skills Nigerian employers are looking for.
        </p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <Link
            :href="route('register') + '?campaign=specializations'"
            class="bg-white hover:bg-gray-50 text-emerald-700 px-8 py-3 rounded-lg font-bold text-lg transition-all hover:scale-105"
          >
            Start Free Today
          </Link>
          <button
            @click="scrollToCategories"
            class="bg-transparent border-2 border-white text-white px-8 py-3 rounded-lg font-semibold text-lg hover:bg-white/10 transition-all"
          >
            Browse All Specializations
          </button>
        </div>

        <div class="mt-8 text-emerald-200 text-sm">
          <div class="flex flex-wrap justify-center gap-6">
            <div class="flex items-center">
              <div class="w-2 h-2 bg-emerald-300 rounded-full mr-2"></div>
              No credit card required
            </div>
            <div class="flex items-center">
              <div class="w-2 h-2 bg-emerald-300 rounded-full mr-2"></div>
              Free career guidance
            </div>
            <div class="flex items-center">
              <div class="w-2 h-2 bg-emerald-300 rounded-full mr-2"></div>
              Certificate included
            </div>
          </div>
        </div>
      </div>
    </section>
  </AppLayout>
</template>

<script setup>
import { Link, Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/GuestLayout.vue';
import MetaTags from '@/Components/MetaTags.vue';
import { ref, computed, onMounted } from 'vue';

const props = defineProps({
  specializations: {
    type: Array,
    default: () => []
  },
  categories: {
    type: Array,
    default: () => []
  },
  meta: {
    type: Object,
    default: () => ({
      title: 'Career-Focused Learning Paths & Specializations | OliLearn Nigeria',
      description: 'Master in-demand skills with structured learning paths. Choose from career-focused specializations in tech, business, creative arts, and more.',
      image: '/images/specializations-landing.png'
    })
  }
});

// Page meta data
const pageTitle = computed(() => props.meta?.title || 'Career-Focused Learning Paths & Specializations | OliLearn Nigeria');
const pageDescription = computed(() => props.meta?.description || 'Master in-demand skills with structured learning paths. Choose from career-focused specializations in tech, business, creative arts, and more.');
const pageImage = computed(() => props.meta?.image || '/images/specializations-landing.png');

// Reactive state
const activeCategory = ref('all');
const searchQuery = ref('');
const selectedLevel = ref('');
const sortBy = ref('popularity');
const visibleCount = ref(9);
const perPage = 9;
const favoriteIds = ref([]);

// Career statistics
const careerStats = computed(() => {
  const totalEnrollments = props.specializations.reduce((sum, spec) => sum + (spec.enrollment_count || 0), 0);
  const totalCompletions = props.specializations.reduce((sum, spec) => sum + (spec.completion_count || 0), 0);
  const completionRate = totalEnrollments > 0 ? Math.round((totalCompletions / totalEnrollments) * 100) : 0;

  return [
    { label: 'Salary Increase', value: '+45%', percentage: 90, color: '#10b981' },
    { label: 'Job Placement', value: '85%', percentage: 85, color: '#059669' },
    { label: 'Skill Improvement', value: '+92%', percentage: 92, color: '#047857' },
    { label: 'Completion Rate', value: `${completionRate}%`, percentage: completionRate, color: '#065f46' }
  ];
});

// Total students formatted
const totalStudentsFormatted = computed(() => {
  const total = props.specializations.reduce((sum, spec) => sum + (spec.enrollment_count || 0), 0);
  return total > 1000 ? `${(total / 1000).toFixed(1)}k+` : total;
});

// Average completion rate
const completionRate = computed(() => {
  const totalEnrollments = props.specializations.reduce((sum, spec) => sum + (spec.enrollment_count || 0), 0);
  const totalCompletions = props.specializations.reduce((sum, spec) => sum + (spec.completion_count || 0), 0);
  return totalEnrollments > 0 ? Math.round((totalCompletions / totalEnrollments) * 100) : 0;
});

// Filter specializations
const filteredSpecializations = computed(() => {
  let filtered = [...props.specializations];

  // Filter by category
  if (activeCategory.value !== 'all') {
    filtered = filtered.filter(s => s.category === activeCategory.value);
  }

  // Filter by search query
  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(s =>
      s.title?.toLowerCase().includes(query) ||
      s.description?.toLowerCase().includes(query) ||
      s.target_skills?.some(skill => skill.toLowerCase().includes(query)) ||
      s.target_career?.toLowerCase().includes(query)
    );
  }

  // Filter by level
  if (selectedLevel.value) {
    filtered = filtered.filter(s => s.level?.toLowerCase() === selectedLevel.value);
  }

  // Sort results
  return sortSpecializations(filtered);
});

// Sort specializations
const sortSpecializations = (specializations) => {
  const sorted = [...specializations];

  switch (sortBy.value) {
    case 'popularity':
      return sorted.sort((a, b) => (b.enrollment_count || 0) - (a.enrollment_count || 0));
    case 'newest':
      return sorted.sort((a, b) => new Date(b.published_at || b.created_at) - new Date(a.published_at || a.created_at));
    case 'duration':
      return sorted.sort((a, b) => (a.estimated_duration_hours || 0) - (b.estimated_duration_hours || 0));
    case 'courses':
      return sorted.sort((a, b) => (a.total_courses || 0) - (b.total_courses || 0));
    default:
      return sorted;
  }
};

// Paginated specializations
const paginatedSpecializations = computed(() => {
  return filteredSpecializations.value.slice(0, visibleCount.value);
});

// Format price
const formatPrice = (price) => {
  if (!price) return '0.00';
  return parseFloat(price).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
};

// Calculate discount percentage
const discountPercentage = (specialization) => {
  if (!specialization.has_discount || !specialization.price || !specialization.discount_price) return 0;
  const original = parseFloat(specialization.price);
  const discount = parseFloat(specialization.discount_price);
  return Math.round(((original - discount) / original) * 100);
};

// Toggle favorite
const toggleFavorite = (id) => {
  const index = favoriteIds.value.indexOf(id);
  if (index > -1) {
    favoriteIds.value.splice(index, 1);
  } else {
    favoriteIds.value.push(id);
  }
  // Save to localStorage
  localStorage.setItem('favoriteSpecializations', JSON.stringify(favoriteIds.value));
};

// Check if specialization is favorite
const isFavorite = (id) => {
  return favoriteIds.value.includes(id);
};

// Set active category
const setActiveCategory = (categoryId) => {
  activeCategory.value = categoryId;
  visibleCount.value = perPage; // Reset pagination when category changes
  scrollToCategories();
};

// Load more specializations
const loadMore = () => {
  visibleCount.value += perPage;
};

// Clear filters
const clearFilters = () => {
  activeCategory.value = 'all';
  searchQuery.value = '';
  selectedLevel.value = '';
  sortBy.value = 'popularity';
  visibleCount.value = perPage;
};

// Scroll to categories section
const scrollToCategories = () => {
  const element = document.getElementById('categories');
  if (element) {
    element.scrollIntoView({ behavior: 'smooth' });
  }
};

// Initialize favorites from localStorage
onMounted(() => {
  const savedFavorites = localStorage.getItem('favoriteSpecializations');
  if (savedFavorites) {
    favoriteIds.value = JSON.parse(savedFavorites);
  }
});
</script>

<style scoped>
/* Custom styles */
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

/* Sticky header animation */
.sticky {
  animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
  from {
    transform: translateY(-100%);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

/* Smooth transitions */
.transition-all {
  transition: all 0.3s ease;
}

/* Responsive typography */
@media (max-width: 640px) {
  h1 {
    font-size: 1.875rem;
    line-height: 2.25rem;
  }

  h2 {
    font-size: 1.5rem;
    line-height: 2rem;
  }
}

/* Loading skeleton animation */
.skeleton {
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: loading 1.5s infinite;
}

@keyframes loading {
  0% { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}
</style>
