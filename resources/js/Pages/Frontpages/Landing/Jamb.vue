<template>
  <MetaTags
    title="JAMB CBT Practice Online in Nigeria | OliLearn"
    description="Prepare for JAMB with CBT practice, AI tutoring, and JAMB-aligned lessons. Built for Nigerian students. Start free on OliLearn."
    image="/images/jamb-landing.png"
  />
  <AppLayout>
    <Head title="JAMB CBT Practice Online in Nigeria | OliLearn" />

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
              🇳🇬 JAMB CBT Preparation for Nigerian Students
            </div>

            <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-gray-900 leading-tight mb-5">
              {{ props.page_data?.hero_title || 'Score Higher in JAMB — With Smart CBT Practice & AI Guidance' }}
            </h1>

            <h2 class="text-lg md:text-xl font-semibold text-emerald-700 mb-6">
              {{ props.page_data?.hero_subtitle || 'Prepare for JAMB the right way: understand concepts, practice real CBT questions, and master time management before exam day.' }}
            </h2>

            <p class="text-gray-700 text-lg leading-relaxed mb-8 max-w-xl">
              OliLearn combines AI tutoring, CBT-style practice, and JAMB-aligned content to help Nigerian students
              prepare smarter — not harder. Every lesson follows the official JAMB syllabus.
            </p>

            <!-- Key Benefits -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-10">
              <div class="flex items-start gap-3">
                <div class="w-7 h-7 rounded-full bg-emerald-600 flex items-center justify-center text-white text-sm font-bold">✓</div>
                <span class="text-gray-700 font-medium">Real JAMB-style CBT questions</span>
              </div>
              <div class="flex items-start gap-3">
                <div class="w-7 h-7 rounded-full bg-emerald-600 flex items-center justify-center text-white text-sm font-bold">✓</div>
                <span class="text-gray-700 font-medium">Timed practice with instant scoring</span>
              </div>
              <div class="flex items-start gap-3">
                <div class="w-7 h-7 rounded-full bg-emerald-600 flex items-center justify-center text-white text-sm font-bold">✓</div>
                <span class="text-gray-700 font-medium">AI tutor explains answers step-by-step</span>
              </div>
              <div class="flex items-start gap-3">
                <div class="w-7 h-7 rounded-full bg-emerald-600 flex items-center justify-center text-white text-sm font-bold">✓</div>
                <span class="text-gray-700 font-medium">Official JAMB syllabus coverage</span>
              </div>
            </div>

            <!-- CTAs -->
            <div class="flex flex-col sm:flex-row gap-4 mb-8">
              <Link
                :href="route('register') + '?campaign=jamb-free'"
                class="inline-flex items-center justify-center bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-4 rounded-xl font-bold text-lg transition-all duration-300 shadow-lg hover:shadow-xl"
              >
                {{ props.page_data?.cta_text || 'Start Free JAMB CBT Practice' }}
              </Link>

              <Link
                :href="route('courses.index') + '?subjects=JAMB'"
                class="inline-flex items-center justify-center bg-white border-2 border-emerald-600 text-emerald-700 hover:bg-emerald-50 px-8 py-4 rounded-xl font-semibold text-lg transition-all"
              >
                View JAMB Courses
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
                Built for Nigerian students
              </div>
              <div class="flex items-center gap-2">
                <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
                Practice anytime, anywhere
              </div>
            </div>
          </div>

          <!-- RIGHT: STATS / PROOF -->
          <div class="bg-white rounded-3xl shadow-2xl p-8 border border-gray-100">
            <div class="mb-6">
              <h3 class="text-xl font-bold text-gray-900 mb-1">
                JAMB Score Improvements
              </h3>
              <p class="text-gray-600 text-sm">
                Average improvement after practicing with OliLearn
              </p>
            </div>

            <div class="space-y-5">
              <div
                v-for="stat in props.jambStats?.subject_improvement"
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
                      width: stat.improvement + 40 + '%',
                      backgroundColor: stat.color || '#10b981'
                    }"
                  ></div>
                </div>
              </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-100">
              <div class="grid grid-cols-3 gap-4 text-center">
                <div>
                  <div class="text-2xl font-extrabold text-emerald-600">
                    +{{ props.jambStats?.average_improvement || 25 }}%
                  </div>
                  <div class="text-xs text-gray-600">Avg. Score Increase</div>
                </div>
                <div>
                  <div class="text-2xl font-extrabold text-emerald-600">
                    {{ props.jambStats?.total_students || '3,000+' }}
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
            Why many students fail JAMB — even after studying hard
          </h2>
          <p class="text-gray-600 max-w-2xl mx-auto">
            JAMB doesn't just test knowledge — it tests speed, accuracy, and confidence.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div class="bg-white p-6 rounded-xl border border-gray-200 hover:border-red-300 transition-all">
            <div class="text-red-500 font-bold text-lg mb-2">1</div>
            <h3 class="font-bold text-gray-900 mb-3">Studying without CBT practice</h3>
            <p class="text-gray-600 text-sm">Traditional study methods don't prepare you for computer-based testing</p>
          </div>

          <div class="bg-white p-6 rounded-xl border border-gray-200 hover:border-red-300 transition-all">
            <div class="text-red-500 font-bold text-lg mb-2">2</div>
            <h3 class="font-bold text-gray-900 mb-3">Poor time management</h3>
            <p class="text-gray-600 text-sm">Running out of time during the actual exam</p>
          </div>

          <div class="bg-white p-6 rounded-xl border border-gray-200 hover:border-red-300 transition-all">
            <div class="text-red-500 font-bold text-lg mb-2">3</div>
            <h3 class="font-bold text-gray-900 mb-3">Memorising without understanding</h3>
            <p class="text-gray-600 text-sm">Memorizing past questions instead of learning concepts</p>
          </div>

          <div class="bg-white p-6 rounded-xl border border-gray-200 hover:border-red-300 transition-all">
            <div class="text-red-500 font-bold text-lg mb-2">4</div>
            <h3 class="font-bold text-gray-900 mb-3">No feedback on weak topics</h3>
            <p class="text-gray-600 text-sm">Not knowing which areas need more attention</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Solution Section -->
    <section class="py-12 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
          <div class="inline-block bg-emerald-100 text-emerald-700 px-4 py-2 rounded-full text-sm font-medium mb-4">
            🎯 JAMB-Specific Solution
          </div>
          <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">
            OliLearn is built for how JAMB is actually written
          </h2>
          <p class="text-gray-600 max-w-2xl mx-auto">
            Our platform simulates the real JAMB CBT experience while providing AI-powered learning support
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div
            v-for="feature in props.jambFeatures"
            :key="feature.title"
            class="text-center"
          >
            <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6 bg-emerald-100 text-emerald-600">
              <div class="font-bold text-xl">{{ feature.icon || '✓' }}</div>
            </div>
            <h3 class="font-bold text-gray-900 mb-3">{{ feature.title }}</h3>
            <p class="text-gray-600">{{ feature.description }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Featured JAMB Courses -->
    <section class="py-12 bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
          <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">
            Popular JAMB Courses
          </h2>
          <p class="text-gray-600 max-w-2xl mx-auto">
            Start preparing with our most popular JAMB CBT practice courses
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
                  {{ course.exam_board || 'JAMB CBT' }}
                </span>
                <h3 class="text-white font-bold text-lg line-clamp-2">{{ course.title }}</h3>
              </div>
            </div>

            <!-- Course Details -->
            <div class="p-5">
              <p class="text-gray-700 text-sm line-clamp-2 mb-4">
                {{ course.description || 'Complete JAMB CBT preparation with AI-powered lessons and practice' }}
              </p>

              <div class="flex items-center justify-between text-sm text-gray-600 mb-4">
                <div class="flex items-center">
                  <div class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></div>
                  <span class="capitalize-first">{{ course.level || 'SS3' }}</span>
                </div>
                <div class="flex items-center">
                  <span>{{ course.duration_display || 'Self-paced' }}</span>
                </div>
              </div>

              <!-- JAMB-specific badges -->
              <div class="flex items-center gap-2 mb-4">
                <span v-if="course.cbt_practice" class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded">
                  CBT Practice
                </span>
                <span v-if="course.mock_exams" class="text-xs bg-amber-100 text-amber-700 px-2 py-1 rounded">
                  Mock Exams
                </span>
                <span v-if="course.is_free" class="text-xs bg-emerald-100 text-emerald-700 px-2 py-1 rounded">
                  FREE
                </span>
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
          <h3 class="text-lg font-semibold text-gray-600 mb-2">JAMB Courses Coming Soon</h3>
          <p class="text-gray-500 mb-6">We're preparing JAMB CBT courses for you. Check back soon!</p>
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
            :href="route('courses.index') + '?category=JAMB'"
            class="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-semibold"
          >
            View All JAMB Courses
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
            How JAMB Preparation Works
          </h2>
          <p class="text-gray-600 max-w-2xl mx-auto">
            Simple 4-step process to JAMB success
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
          <div class="text-center">
            <div class="w-12 h-12 bg-emerald-600 text-white rounded-full flex items-center justify-center text-xl font-bold mx-auto mb-6">
              1
            </div>
            <h3 class="font-bold text-gray-900 mb-3">Choose your JAMB subjects</h3>
            <p class="text-gray-600 text-sm">Select your UTME combination</p>
          </div>

          <div class="text-center">
            <div class="w-12 h-12 bg-emerald-600 text-white rounded-full flex items-center justify-center text-xl font-bold mx-auto mb-6">
              2
            </div>
            <h3 class="font-bold text-gray-900 mb-3">Practice CBT questions</h3>
            <p class="text-gray-600 text-sm">Take timed mock exams with instant scoring</p>
          </div>

          <div class="text-center">
            <div class="w-12 h-12 bg-emerald-600 text-white rounded-full flex items-center justify-center text-xl font-bold mx-auto mb-6">
              3
            </div>
            <h3 class="font-bold text-gray-900 mb-3">Ask the AI tutor</h3>
            <p class="text-gray-600 text-sm">Get explanations when you're stuck</p>
          </div>

          <div class="text-center">
            <div class="w-12 h-12 bg-emerald-600 text-white rounded-full flex items-center justify-center text-xl font-bold mx-auto mb-6">
              4
            </div>
            <h3 class="font-bold text-gray-900 mb-3">Track & improve</h3>
            <p class="text-gray-600 text-sm">Identify weak areas and focus your practice</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Subjects Offered -->
    <section class="py-12 bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
          <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">
            JAMB Subjects Available
          </h2>
          <p class="text-gray-600 max-w-2xl mx-auto">
            Complete coverage of all major JAMB subjects with CBT practice
          </p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
          <div
            v-for="subject in props.jambSubjects"
            :key="subject.name"
            class="bg-white p-5 rounded-xl border border-gray-200 text-center hover:border-emerald-300 transition-all"
          >
            <h3 class="font-bold text-gray-900 mb-2">{{ subject.name }}</h3>
            <p class="text-sm text-gray-600">{{ subject.description }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Social Proof Section -->
    <section class="py-12 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
          <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">
            Students using OliLearn prepare with confidence
          </h2>
          <p class="text-gray-600 max-w-2xl mx-auto">
            Improved performance across all JAMB subjects
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="bg-emerald-50 p-6 rounded-xl border border-emerald-100 text-center">
            <div class="text-4xl font-bold text-emerald-600 mb-3">🚀</div>
            <h3 class="font-bold text-gray-900 mb-3">Improved Speed</h3>
            <p class="text-gray-600">Master time management in CBT exams</p>
          </div>

          <div class="bg-emerald-50 p-6 rounded-xl border border-emerald-100 text-center">
            <div class="text-4xl font-bold text-emerald-600 mb-3">🎯</div>
            <h3 class="font-bold text-gray-900 mb-3">Better Accuracy</h3>
            <p class="text-gray-600">Higher scores under time pressure</p>
          </div>

          <div class="bg-emerald-50 p-6 rounded-xl border border-emerald-100 text-center">
            <div class="text-4xl font-bold text-emerald-600 mb-3">🧠</div>
            <h3 class="font-bold text-gray-900 mb-3">Clear Understanding</h3>
            <p class="text-gray-600">Grasp difficult topics with AI explanations</p>
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
            OliLearn JAMB preparation is designed specifically for:
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div class="bg-emerald-50 p-6 rounded-xl text-center border border-emerald-100">
            <h3 class="font-bold text-gray-900 mb-3">SS3 Students</h3>
            <p class="text-gray-600 text-sm">Preparing for UTME</p>
          </div>

          <div class="bg-emerald-50 p-6 rounded-xl text-center border border-emerald-100">
            <h3 class="font-bold text-gray-900 mb-3">JAMB Re-sit Candidates</h3>
            <p class="text-gray-600 text-sm">Improving previous scores</p>
          </div>

          <div class="bg-emerald-50 p-6 rounded-xl text-center border border-emerald-100">
            <h3 class="font-bold text-gray-900 mb-3">Private Candidates</h3>
            <p class="text-gray-600 text-sm">Flexible learning schedule</p>
          </div>

          <div class="bg-emerald-50 p-6 rounded-xl text-center border border-emerald-100">
            <h3 class="font-bold text-gray-900 mb-3">High Achievers</h3>
            <p class="text-gray-600 text-sm">Aiming for competitive cut-off marks</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Pricing Section -->
    <PricingPlans
      :plans="props.subscriptionPlans"
      :show-role-selector="false"
      :show-enterprise="false"
      section-id="jamb-pricing"
      header-title="Choose Your JAMB Plan"
      header-subtitle="Start Free, Upgrade When Ready"
      header-description="All plans include JAMB CBT practice, AI tutoring, and performance tracking."
    />

    <!-- Final CTA -->
    <section class="py-12 bg-gradient-to-r from-emerald-700 to-emerald-800 text-white">
      <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl md:text-3xl font-bold mb-6">
          JAMB success starts with the right preparation
        </h2>

        <p class="text-lg text-emerald-100 mb-8 max-w-2xl mx-auto">
          Study smart. Practice CBT. Enter the exam confident.
        </p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <Link
            :href="route('register') + '?campaign=jamb-free'"
            class="bg-white hover:bg-gray-50 text-emerald-700 px-8 py-3 rounded-lg font-bold text-lg transition-all hover:scale-105"
          >
            Start Free JAMB CBT Prep Now
          </Link>
          <Link
            :href="route('courses.index') + '?category=JAMB'"
            class="bg-transparent border-2 border-white text-white px-8 py-3 rounded-lg font-semibold text-lg hover:bg-white/10 transition-all"
          >
            Browse JAMB Courses
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
              Free CBT practice included
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

const props = defineProps({
  courses: { type: Array, default: () => [] },
  subscriptionPlans: { type: Array, default: () => [] },
  jambSubjects: { type: Array, default: () => [] },
  jambStats: {
    type: Object,
    default: () => ({
      subject_improvement: [
        { subject: 'Mathematics', improvement: 35, color: '#10b981' },
        { subject: 'English Language', improvement: 28, color: '#059669' },
        { subject: 'Physics', improvement: 42, color: '#047857' },
        { subject: 'Chemistry', improvement: 38, color: '#065f46' }
      ],
      average_improvement: 25,
      total_students: '3,000+'
    })
  },
  jambFeatures: {
    type: Array,
    default: () => [
      { title: 'Real JAMB-style CBT', description: 'Simulated exam environment with timed practice' },
      { title: 'Instant Scoring & Feedback', description: 'Get results immediately with detailed explanations' },
      { title: 'AI-Powered Tutoring', description: 'Step-by-step explanations for every question' },
      { title: 'Performance Analytics', description: 'Track progress and identify weak areas' },
      { title: 'JAMB Syllabus Coverage', description: 'Complete coverage of all subjects' },
      { title: 'Mobile-Friendly Practice', description: 'Study on any device, anytime' }
    ]
  },
  page_data: {
    type: Object,
    default: () => ({
      hero_title: 'Score Higher in JAMB — With Smart CBT Practice & AI Guidance',
      hero_subtitle: 'Prepare for JAMB the right way: understand concepts, practice real CBT questions, and master time management before exam day.',
      cta_text: 'Start Free JAMB CBT Practice'
    })
  }
});
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
