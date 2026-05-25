<template>
  <MetaTags
    title="WAEC Online Classes in Nigeria | WAEC Exam Preparation Platform – OliLearn"
    description="Prepare for WAEC in Nigeria with OliLearn. WAEC syllabus-based lessons, AI tutor, quizzes, flashcards, and exam-style practice for SS2 & SS3 students."
    image="/images/waec-landing.png"
  />
  <AppLayout>
    <Head title="WAEC Online Classes in Nigeria | WAEC Exam Preparation Platform – OliLearn" />

    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-b from-emerald-900 via-emerald-50 to-white">
  <!-- Decorative Background -->
  <div class="absolute inset-0">
    <div class="absolute -top-24 -right-24 w-96 h-96 bg-emerald-500/20 rounded-full blur-3xl"></div>
    <div class="absolute top-40 -left-24 w-80 h-80 bg-emerald-400/20 rounded-full blur-3xl"></div>
  </div>

  <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

      <!-- LEFT: CONTENT -->
      <div>
        <div class="inline-flex items-center gap-2 bg-emerald-100 text-emerald-700 px-5 py-2 rounded-full text-sm font-semibold mb-6">
          🇳🇬 WAEC-focused learning for Nigerian students
        </div>

        <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-gray-900 leading-tight mb-5">
          {{ props.page_data?.hero_title || 'WAEC Online Classes That Help You Understand & Pass' }}
        </h1>

        <h2 class="text-lg md:text-xl font-semibold text-emerald-700 mb-6">
          Learn WAEC topics deeply, practice confidently, and walk into the exam hall prepared.
        </h2>

        <p class="text-gray-700 text-lg leading-relaxed mb-8 max-w-xl">
          OliLearn is a WAEC-focused online learning platform built specifically for Nigerian students.
          Every lesson follows the official WAEC syllabus — powered by AI tutors, quizzes, flashcards,
          and exam-style practice.
        </p>

        <!-- Key Benefits -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-10">
          <div class="flex items-start gap-3">
            <div class="w-7 h-7 rounded-full bg-emerald-600 flex items-center justify-center text-white text-sm font-bold">✓</div>
            <span class="text-gray-700 font-medium">100% WAEC syllabus aligned</span>
          </div>
          <div class="flex items-start gap-3">
            <div class="w-7 h-7 rounded-full bg-emerald-600 flex items-center justify-center text-white text-sm font-bold">✓</div>
            <span class="text-gray-700 font-medium">AI tutor for every WAEC course</span>
          </div>
          <div class="flex items-start gap-3">
            <div class="w-7 h-7 rounded-full bg-emerald-600 flex items-center justify-center text-white text-sm font-bold">✓</div>
            <span class="text-gray-700 font-medium">WAEC-style quizzes & revision tools</span>
          </div>
          <div class="flex items-start gap-3">
            <div class="w-7 h-7 rounded-full bg-emerald-600 flex items-center justify-center text-white text-sm font-bold">✓</div>
            <span class="text-gray-700 font-medium">Built for SS2 & SS3 students</span>
          </div>
        </div>

        <!-- CTAs -->
        <div class="flex flex-col sm:flex-row gap-4 mb-8">
          <Link
            :href="route('register') + '?campaign=waec-free'"
            class="inline-flex items-center justify-center bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-4 rounded-xl font-bold text-lg transition-all duration-300 shadow-lg hover:shadow-xl"
          >
            {{ props.page_data?.cta_text || 'Start Free WAEC Prep' }}
          </Link>

          <Link
            :href="route('courses.index') + '?subjects=WAEC'"
            class="inline-flex items-center justify-center bg-white border-2 border-emerald-600 text-emerald-700 hover:bg-emerald-50 px-8 py-4 rounded-xl font-semibold text-lg transition-all"
          >
            View WAEC Courses
          </Link>
        </div>

        <!-- Trust Signals -->
        <div class="flex flex-wrap items-center gap-6 text-sm text-gray-600">
          <div class="flex items-center gap-2">
            <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
            No credit card required
          </div>
          <div class="flex items-center gap-2">
            <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
            Free plan available
          </div>
          <div class="flex items-center gap-2">
            <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
            Learn anytime, anywhere
          </div>
        </div>
      </div>

      <!-- RIGHT: STATS / PROOF -->
      <div class="bg-white rounded-3xl shadow-2xl p-8 border border-gray-100">
        <div class="mb-6">
          <h3 class="text-xl font-bold text-gray-900 mb-1">
            WAEC Score Improvements
          </h3>
          <p class="text-gray-600 text-sm">
            Average improvement after studying with OliLearn
          </p>
        </div>

        <div class="space-y-5">
          <div
            v-for="stat in props.waecStats?.student_improvement"
            :key="stat.subject"
          >
            <div class="flex justify-between items-center mb-1">
              <span class="text-gray-700 font-medium">{{ stat.subject }}</span>
              <span class="font-bold text-emerald-600">+{{ stat.improvement }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2.5">
              <div
                class="h-2.5 rounded-full transition-all"
                :style="{
                  width: stat.improvement + 50 + '%',
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
                {{ props.waecStats?.overall_pass_rate || 98 }}%
              </div>
              <div class="text-xs text-gray-600">Pass Rate</div>
            </div>
            <div>
              <div class="text-2xl font-extrabold text-emerald-600">
                {{ props.waecStats?.total_students || '2,500+' }}
              </div>
              <div class="text-xs text-gray-600">Students</div>
            </div>
            <div>
              <div class="text-2xl font-extrabold text-emerald-600">24/7</div>
              <div class="text-xs text-gray-600">AI Tutor</div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>


    <!-- Problem Section -->
    <section class="py-12 bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
          <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">
            Why Nigerian Students Struggle with WAEC
          </h2>
          <p class="text-gray-600 max-w-2xl mx-auto">
            Most students fail WAEC not because they're not smart, but because they're not studying the right way.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div class="bg-white p-6 rounded-xl border border-gray-200 hover:border-red-300 transition-all">
            <div class="text-red-500 font-bold text-lg mb-2">1</div>
            <h3 class="font-bold text-gray-900 mb-3">Wrong Study Materials</h3>
            <p class="text-gray-600 text-sm">Using outdated notes not aligned with current WAEC syllabus</p>
          </div>

          <div class="bg-white p-6 rounded-xl border border-gray-200 hover:border-red-300 transition-all">
            <div class="text-red-500 font-bold text-lg mb-2">2</div>
            <h3 class="font-bold text-gray-900 mb-3">Limited Teacher Access</h3>
            <p class="text-gray-600 text-sm">No help available after school hours or during holidays</p>
          </div>

          <div class="bg-white p-6 rounded-xl border border-gray-200 hover:border-red-300 transition-all">
            <div class="text-red-500 font-bold text-lg mb-2">3</div>
            <h3 class="font-bold text-gray-900 mb-3">Poor Study Methods</h3>
            <p class="text-gray-600 text-sm">Cramming instead of systematic learning and practice</p>
          </div>

          <div class="bg-white p-6 rounded-xl border border-gray-200 hover:border-red-300 transition-all">
            <div class="text-red-500 font-bold text-lg mb-2">4</div>
            <h3 class="font-bold text-gray-900 mb-3">No Personalized Support</h3>
            <p class="text-gray-600 text-sm">One-size-fits-all teaching doesn't address individual gaps</p>
          </div>
        </div>
      </div>
    </section>

    <!-- AI-Powered Solution -->
    <section class="py-12 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
          <div class="inline-block bg-emerald-100 text-emerald-700 px-4 py-2 rounded-full text-sm font-medium mb-4">
            🤖 AI-Powered Learning
          </div>
          <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">
            How OliLearn's AI Helps You Pass WAEC
          </h2>
          <p class="text-gray-600 max-w-2xl mx-auto">
            Our platform uses AI to create personalized learning experiences for every student
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div
            v-for="feature in props.aiFeatures"
            :key="feature.title"
            class="text-center"
          >
            <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6" :class="colorClasses(feature.color)">
              <div class="font-bold text-xl">{{ feature.icon }}</div>
            </div>
            <h3 class="font-bold text-gray-900 mb-3">{{ feature.title }}</h3>
            <p class="text-gray-600">{{ feature.description }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Featured WAEC Courses -->
    <section class="py-12 bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
          <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">
            Popular WAEC Courses
          </h2>
          <p class="text-gray-600 max-w-2xl mx-auto">
            Start learning with our most popular WAEC preparation courses
          </p>
        </div>

        <!-- Course Grid -->
        <div v-if="props.courses && props.courses.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="course in props.courses.slice(0, 6)"
            :key="course.id"
            class="bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg transition-all duration-300 hover:-translate-y-1"
          >
            <!-- Course Header -->
            <div class="h-40 bg-gradient-to-r from-emerald-500 to-emerald-600 relative overflow-hidden">
              <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
              <div class="absolute bottom-4 left-4 right-4">
                <span class="inline-block px-3 py-1 rounded-full text-xs bg-white/30 backdrop-blur-sm text-white font-medium mb-2">
                  {{ course.exam_board || 'WAEC' }}
                </span>
                <h3 class="text-white font-bold text-lg line-clamp-2">{{ course.title }}</h3>
              </div>
            </div>

            <!-- Course Details -->
            <div class="p-5">
              <p class="text-gray-700 text-sm line-clamp-2 mb-4">
                {{ course.description || 'Complete WAEC preparation with AI-powered lessons and practice' }}
              </p>

              <div class="flex items-center justify-between text-sm text-gray-600 mb-4">
                <div class="flex items-center">
                  <div class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></div>
                  <span class="capitalize-first">{{ course.level || 'All Levels' }}</span>
                </div>
                <div class="flex items-center">
                  <span>{{ course.duration_display || 'Self-paced' }}</span>
                </div>
              </div>

              <div class="flex items-center justify-between mb-4">
                <div>
                  <span v-if="course.is_free" class="text-emerald-600 font-bold">FREE</span>
                  <span v-else class="text-gray-900 font-bold">{{ course.display_price }}</span>
                </div>
                <span class="text-xs bg-emerald-100 text-emerald-700 px-2 py-1 rounded">
                  {{ course.enrollment_status || 'Available' }}
                </span>
              </div>

              <Link
                :href="route('courses.show', { id: course.id, slug: course.slug })"
                class="w-full text-center block bg-gradient-to-r from-emerald-600 to-emerald-700 text-white py-2.5 rounded-lg text-sm font-medium hover:from-emerald-700 hover:to-emerald-800 transition-all"
              >
                View Course
              </Link>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="col-span-full text-center py-12">
          <div class="text-gray-400 mb-4">
            <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l9-5-9-5-9 5 9 5z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l9-5-9-5-9 5 9 5z" opacity="0.5" />
            </svg>
          </div>
          <h3 class="text-lg font-semibold text-gray-600 mb-2">WAEC Courses Coming Soon</h3>
          <p class="text-gray-500 mb-6">We're preparing WAEC courses for you. Check back soon!</p>
          <Link
            :href="route('courses.index')"
            class="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-semibold"
          >
            Browse All Courses
            <svg class="w-5 h-5 ml-2" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
          </Link>
        </div>

        <!-- View All Courses -->
        <div v-if="props.courses && props.courses.length > 0" class="text-center mt-10">
          <Link
            :href="route('courses.index') + '?category=WAEC'"
            class="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-semibold"
          >
            View All WAEC Courses
            <svg class="w-5 h-5 ml-2" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
          </Link>
        </div>
      </div>
    </section>

    <!-- How It Works -->
    <section class="py-12 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
          <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">
            How WAEC Preparation Works
          </h2>
          <p class="text-gray-600 max-w-2xl mx-auto">
            Simple 4-step process to WAEC success
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
          <div class="text-center">
            <div class="w-12 h-12 bg-emerald-600 text-white rounded-full flex items-center justify-center text-xl font-bold mx-auto mb-6">
              1
            </div>
            <h3 class="font-bold text-gray-900 mb-3">Sign Up Free</h3>
            <p class="text-gray-600 text-sm">Create your free account. No credit card required.</p>
          </div>

          <div class="text-center">
            <div class="w-12 h-12 bg-emerald-600 text-white rounded-full flex items-center justify-center text-xl font-bold mx-auto mb-6">
              2
            </div>
            <h3 class="font-bold text-gray-900 mb-3">Choose Subject</h3>
            <p class="text-gray-600 text-sm">Select WAEC subjects you want to prepare for.</p>
          </div>

          <div class="text-center">
            <div class="w-12 h-12 bg-emerald-600 text-white rounded-full flex items-center justify-center text-xl font-bold mx-auto mb-6">
              3
            </div>
            <h3 class="font-bold text-gray-900 mb-3">Learn with AI</h3>
            <p class="text-gray-600 text-sm">Study lessons and get help from your AI tutor.</p>
          </div>

          <div class="text-center">
            <div class="w-12 h-12 bg-emerald-600 text-white rounded-full flex items-center justify-center text-xl font-bold mx-auto mb-6">
              4
            </div>
            <h3 class="font-bold text-gray-900 mb-3">Practice & Excel</h3>
            <p class="text-gray-600 text-sm">Take practice tests and track your improvement.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Subjects Offered -->
    <section class="py-12 bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
          <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">
            WAEC Subjects Available
          </h2>
          <p class="text-gray-600 max-w-2xl mx-auto">
            Complete coverage of all major WAEC subjects with AI-powered lessons
          </p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
          <div
            v-for="subject in props.waecSubjects"
            :key="subject.name"
            class="bg-white p-5 rounded-xl border border-gray-200 text-center hover:border-emerald-300 transition-all"
          >
            <h3 class="font-bold text-gray-900 mb-2">{{ subject.name }}</h3>
            <p class="text-sm text-gray-600">{{ subject.description }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Target Audience -->
    <section class="py-12 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
          <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">
            Who Is This For?
          </h2>
          <p class="text-gray-600 max-w-2xl mx-auto">
            OliLearn WAEC preparation is designed specifically for:
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div class="bg-emerald-50 p-6 rounded-xl text-center border border-emerald-100">
            <h3 class="font-bold text-gray-900 mb-3">SS2 Students</h3>
            <p class="text-gray-600 text-sm">Build strong foundation early</p>
          </div>

          <div class="bg-emerald-50 p-6 rounded-xl text-center border border-emerald-100">
            <h3 class="font-bold text-gray-900 mb-3">SS3 Students</h3>
            <p class="text-gray-600 text-sm">Final preparation & revision</p>
          </div>

          <div class="bg-emerald-50 p-6 rounded-xl text-center border border-emerald-100">
            <h3 class="font-bold text-gray-900 mb-3">Private Candidates</h3>
            <p class="text-gray-600 text-sm">Flexible learning schedule</p>
          </div>

          <div class="bg-emerald-50 p-6 rounded-xl text-center border border-emerald-100">
            <h3 class="font-bold text-gray-900 mb-3">Schools & Parents</h3>
            <p class="text-gray-600 text-sm">Supplement classroom learning</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Pricing Section -->
    <PricingPlans
      :plans="props.subscriptionPlans"
      :show-role-selector="false"
      :show-enterprise="false"
      section-id="waec-pricing"
      header-title="Choose Your WAEC Plan"
      header-subtitle="Start Free, Upgrade When Ready"
      header-description="All plans include AI-powered WAEC courses with dedicated AI tutors. No trials, only real value."
    />

    <!-- Final CTA -->
    <section class="py-12 bg-gradient-to-r from-emerald-700 to-emerald-800 text-white">
      <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl md:text-3xl font-bold mb-6">
          Start Your WAEC Preparation Today
        </h2>

        <p class="text-lg text-emerald-100 mb-8 max-w-2xl mx-auto">
          Don't wait until exams are close. Build understanding early and walk into the WAEC exam hall with confidence.
        </p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <Link
            :href="route('register') + '?plan=waec-free'"
            class="bg-white hover:bg-gray-50 text-emerald-700 px-8 py-3 rounded-lg font-bold text-lg transition-all hover:scale-105"
          >
            Start 100% Free
          </Link>
          <Link
            :href="route('courses.index') + '?category=WAEC'"
            class="bg-transparent border-2 border-white text-white px-8 py-3 rounded-lg font-semibold text-lg hover:bg-white/10 transition-all"
          >
            Browse WAEC Courses
          </Link>
        </div>

        <div class="mt-8 text-emerald-200 text-sm">
          <div class="flex flex-wrap justify-center gap-6">
            <div class="flex items-center">
              <div class="w-2 h-2 bg-emerald-300 rounded-full mr-2"></div>
              No credit card required
            </div>
            <div class="flex items-center">
              <div class="w-2 h-2 bg-emerald-300 rounded-full mr-2"></div>
              Free plan includes AI tutor
            </div>
            <div class="flex items-center">
              <div class="w-2 h-2 bg-emerald-300 rounded-full mr-2"></div>
              Cancel anytime
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
import PricingPlans from '@/Components/PricingPlans.vue';
import MetaTags from '@/Components/MetaTags.vue';
import { computed } from 'vue';

const props = defineProps({
  courses: { type: Array, default: () => [] },
  subscriptionPlans: { type: Array, default: () => [] },
  waecSubjects: { type: Array, default: () => [] },
  waecStats: { type: Object, default: () => ({}) },
  aiFeatures: { type: Array, default: () => [] },
  page_data: { type: Object, default: () => ({}) }
});

// Helper function for dynamic color classes
const colorClasses = (color) => {
  const colorMap = {
    emerald: 'bg-emerald-100 text-emerald-600',
    blue: 'bg-blue-100 text-blue-600',
    purple: 'bg-purple-100 text-purple-600',
    amber: 'bg-amber-100 text-amber-600',
    red: 'bg-red-100 text-red-600',
    green: 'bg-green-100 text-green-600',
    indigo: 'bg-indigo-100 text-indigo-600',
    pink: 'bg-pink-100 text-pink-600',
    yellow: 'bg-yellow-100 text-yellow-600',
    gray: 'bg-gray-100 text-gray-600'
  };

  return colorMap[color] || 'bg-emerald-100 text-emerald-600';
};
</script>

<style scoped>
/* Mobile-first responsive design */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.capitalize-first {
  text-transform: capitalize;
}

/* Optimize for mobile touch */
button, a {
  -webkit-tap-highlight-color: transparent;
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

/* Loading states */
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
