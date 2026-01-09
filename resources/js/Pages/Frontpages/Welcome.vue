<template>
  <MetaTags
    title="Master In-Demand Skills with AI-Powered Courses | OliLearn"
    description="Nigeria's premier AI-powered learning platform. Access expert-led courses, prepare for local/international exams, and gain practical skills with personalized AI tutoring."
    image="/images/olingolearn.png"
  />
  <AppLayout>
    <Head title="Learn Skills That Matter in Today's World" />

    <!-- Hero Section with Carousel -->
<section class="relative overflow-hidden bg-white">
  <!-- Carousel Container -->
  <div class="relative h-[600px] md:h-[700px]">
    <!-- Slide 1 - Skills Focus -->
    <div
      v-if="heroCarousel.currentIndex === 0"
      class="absolute inset-0 transition-opacity duration-1000 ease-in-out"
    >
      <!-- Background Image with Gradient Overlay -->
      <div
        class="absolute inset-0 bg-cover bg-center"
        :style="{ backgroundImage: 'url(/images/hero/skill-bg.png)' }"
      >
        <div class="absolute inset-0 bg-gradient-to-r from-emerald-900/80 via-emerald-800/70 to-emerald-700/60"></div>
        <div class="absolute inset-0 bg-black/30"></div>
      </div>

      <!-- Content -->
      <div class="absolute inset-0 flex items-center">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
          <div class="max-w-2xl">
            <div class="inline-flex items-center px-4 py-2 rounded-full bg-emerald-500/20 backdrop-blur-sm text-white text-sm font-medium mb-6 border border-emerald-400/30">
              <span class="w-2 h-2 bg-emerald-400 rounded-full mr-2 animate-pulse"></span>
              🚀 AI-Powered Learning
            </div>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6 leading-tight">
              Learn Skills That
              <span class="block text-emerald-300 mt-2">Employers Actually Want</span>
            </h1>
            <p class="text-lg text-emerald-100 mb-8 leading-relaxed">
              Nigeria's #1 AI-powered learning platform. Get job-ready skills with courses designed for the African market and global recognition.
            </p>
            <div class="flex flex-col sm:flex-row gap-4">
              <Link
                :href="route('courses.index')"
                class="bg-emerald-500 hover:bg-emerald-600 text-white px-8 py-3.5 rounded-lg font-bold text-lg transition-all duration-300 hover:shadow-xl shadow-emerald-500/30"
              >
                Start Learning Free
              </Link>
              <Link
                :href="route('courses.index')"
                class="bg-transparent border-2 border-white text-white hover:bg-white/10 px-8 py-3.5 rounded-lg font-semibold text-lg transition-all backdrop-blur-sm"
              >
                Explore 100+ Courses
              </Link>
            </div>
            <div class="mt-8 flex flex-wrap gap-6 text-sm text-emerald-200">
              <div class="flex items-center">
                <span class="w-2 h-2 bg-emerald-400 rounded-full mr-2"></span>
                No credit card required
              </div>
              <div class="flex items-center">
                <span class="w-2 h-2 bg-emerald-400 rounded-full mr-2"></span>
                Lifetime free account
              </div>
              <div class="flex items-center">
                <span class="w-2 h-2 bg-emerald-400 rounded-full mr-2"></span>
                Certificate included
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Slide 2 - Exam Preparation -->
    <div
      v-if="heroCarousel.currentIndex === 1"
      class="absolute inset-0 transition-opacity duration-1000 ease-in-out"
    >
      <!-- Background Image with Gradient Overlay -->
      <div
        class="absolute inset-0 bg-cover bg-center"
        :style="{ backgroundImage: 'url(/images/hero/exam-bg2.png)' }"
      >
        <div class="absolute inset-0 bg-gradient-to-r from-emerald-800/80 via-emerald-700/70 to-emerald-600/60"></div>
        <div class="absolute inset-0 bg-black/30"></div>
      </div>

      <!-- Content -->
      <div class="absolute inset-0 flex items-center">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
          <div class="max-w-2xl ml-auto text-right">
            <div class="inline-flex items-center px-4 py-2 rounded-full bg-emerald-500/20 backdrop-blur-sm text-white text-sm font-medium mb-6 border border-emerald-400/30 ml-auto">
              <span class="w-2 h-2 bg-emerald-400 rounded-full mr-2"></span>
              📚 Exam Excellence
            </div>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6 leading-tight">
              Ace WAEC, JAMB, &
              <span class="block text-emerald-300 mt-2">International Exams</span>
            </h1>
            <p class="text-lg text-emerald-100 mb-8 leading-relaxed">
              Comprehensive preparation for Nigerian and international examinations with AI-powered practice tests and personalized study plans.
            </p>
            <Link
              :href="route('courses.index') + '?subjects=Exam+Preparation'"
              class="inline-flex bg-emerald-500 hover:bg-emerald-600 text-white px-8 py-3.5 rounded-lg font-bold text-lg transition-all duration-300 hover:shadow-xl shadow-emerald-500/30"
            >
              Explore Exam Courses
            </Link>
          </div>
        </div>
      </div>
    </div>

    <!-- Slide 3 - AI Tutors -->
    <div
      v-if="heroCarousel.currentIndex === 2"
      class="absolute inset-0 transition-opacity duration-1000 ease-in-out"
    >
      <!-- Background Image with Gradient Overlay -->
      <div
        class="absolute inset-0 bg-cover bg-center"
        :style="{ backgroundImage: 'url(/images/hero/exam-bg.png)' }"
      >
        <div class="absolute inset-0 bg-gradient-to-r from-emerald-900/80 via-emerald-800/70 to-emerald-700/60"></div>
        <div class="absolute inset-0 bg-black/30"></div>
      </div>

      <!-- Content -->
      <div class="absolute inset-0 flex items-center">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
          <div class="max-w-2xl">
            <div class="inline-flex items-center px-4 py-2 rounded-full bg-emerald-500/20 backdrop-blur-sm text-white text-sm font-medium mb-6 border border-emerald-400/30">
              <span class="w-2 h-2 bg-emerald-400 rounded-full mr-2 animate-pulse"></span>
              🤖 AI-Powered Tutoring
            </div>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6 leading-tight">
              Learn from Industry
              <span class="block text-emerald-300 mt-2">Experts & AI Tutors</span>
            </h1>
            <p class="text-lg text-emerald-100 mb-8 leading-relaxed">
              Combine expert Nigerian instructors with 24/7 AI tutoring for a learning experience tailored to African learners.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center">
              <Link
                :href="route('register')"
                class="bg-emerald-500 hover:bg-emerald-600 text-white px-8 py-3.5 rounded-lg font-bold text-lg transition-all duration-300 hover:shadow-xl shadow-emerald-500/30"
              >
                Join Free Today
              </Link>
              <div class="flex flex-col sm:flex-row gap-4 text-sm text-white">
                <div class="flex items-center">
                  <span class="w-2 h-2 bg-emerald-400 rounded-full mr-2 animate-pulse"></span>
                  24/7 AI Support
                </div>
                <div class="flex items-center">
                  <span class="w-2 h-2 bg-emerald-400 rounded-full mr-2 animate-pulse"></span>
                  Personalized Learning
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Navigation Arrows -->
    <button
      @click="prevSlide"
      class="absolute left-4 top-1/2 transform -translate-y-1/2 p-3 rounded-full bg-white/10 backdrop-blur-sm text-white hover:bg-white/20 transition-all z-10"
      aria-label="Previous slide"
    >
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
      </svg>
    </button>

    <button
      @click="nextSlide"
      class="absolute right-4 top-1/2 transform -translate-y-1/2 p-3 rounded-full bg-white/10 backdrop-blur-sm text-white hover:bg-white/20 transition-all z-10"
      aria-label="Next slide"
    >
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
      </svg>
    </button>

    <!-- Carousel Controls -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 flex space-x-3 z-10">
      <button
        v-for="(_, index) in 3"
        :key="index"
        @click="heroCarousel.currentIndex = index"
        class="w-3 h-3 rounded-full transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:ring-offset-2 focus:ring-offset-transparent"
        :class="heroCarousel.currentIndex === index
          ? 'bg-emerald-400 w-8 shadow-lg shadow-emerald-400/50'
          : 'bg-white/50 hover:bg-white hover:scale-110'"
        :aria-label="`Go to slide ${index + 1}`"
      />
    </div>
  </div>
</section>

    <!-- Subjects Grid -->
    <section class="py-16 bg-beige-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl font-bold text-gray-900 mb-4">Browse by Subject</h2>
          <p class="text-gray-600 max-w-2xl mx-auto">
            Explore courses in your field of interest. All subjects include AI-enhanced learning.
          </p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
          <Link
            v-for="subject in subjects"
            :key="subject.name"
            :href="route('courses.index') + `?subjects=${encodeURIComponent(subject.name)}`"
            class="bg-white p-6 rounded-xl border border-gray-200 hover:border-emerald-300 hover:shadow-lg transition-all duration-300 text-center group hover:-translate-y-1"
          >
            <div class="text-3xl mb-3 text-emerald-600">{{ subject.icon }}</div>
            <h3 class="font-semibold text-gray-900 mb-1">{{ subject.name }}</h3>
            <p class="text-xs text-gray-500">{{ subject.count }}+ courses</p>
          </Link>
        </div>
      </div>
    </section>

    <!-- Why OliLearn Section -->
    <section class="py-16 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gradient-to-r from-emerald-50 to-emerald-100 rounded-2xl p-8 md:p-12 border border-emerald-200">
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
            <div>
              <div class="inline-flex items-center px-4 py-2 rounded-full bg-emerald-100 text-emerald-700 text-sm font-medium mb-4">
                <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></span>
                The OliLearn Advantage
              </div>
              <h2 class="text-3xl font-bold text-gray-900 mb-4">
                Why Thousands of Nigerian Students Choose OliLearn
              </h2>
              <p class="text-gray-700 mb-6">
                We combine world-class education with African context. Our platform is built specifically for Nigerian learners, addressing local educational needs while preparing you for global opportunities.
              </p>
              <ul class="space-y-3 mb-8">
                <li class="flex items-start">
                  <span class="text-emerald-500 mr-3 text-xl">✓</span>
                  <span><strong>Contextual Learning:</strong> Courses designed with Nigerian and African examples</span>
                </li>
                <li class="flex items-start">
                  <span class="text-emerald-500 mr-3 text-xl">✓</span>
                  <span><strong>Affordable Excellence:</strong> World-class education at African-friendly prices</span>
                </li>
                <li class="flex items-start">
                  <span class="text-emerald-500 mr-3 text-xl">✓</span>
                  <span><strong>24/7 AI Support:</strong> Get help anytime, even with slow internet connections</span>
                </li>
              </ul>
              <Link
                :href="route('about')"
                class="inline-flex items-center bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors"
              >
                Learn More About Us
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
              </Link>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div class="bg-white p-6 rounded-xl text-center border border-emerald-100 shadow-sm">
                <div class="text-3xl mb-3 text-emerald-600">🎓</div>
                <div class="text-2xl font-bold text-emerald-600 mb-1">98%</div>
                <div class="text-sm text-gray-600">Exam Pass Rate</div>
              </div>
              <div class="bg-white p-6 rounded-xl text-center border border-emerald-100 shadow-sm">
                <div class="text-3xl mb-3 text-emerald-600">👨‍🏫</div>
                <div class="text-2xl font-bold text-emerald-600 mb-1">50+</div>
                <div class="text-sm text-gray-600">Expert Instructors</div>
              </div>
              <div class="bg-white p-6 rounded-xl text-center border border-emerald-100 shadow-sm">
                <div class="text-3xl mb-3 text-emerald-600">🤖</div>
                <div class="text-2xl font-bold text-emerald-600 mb-1">24/7</div>
                <div class="text-sm text-gray-600">AI Tutor Support</div>
              </div>
              <div class="bg-white p-6 rounded-xl text-center border border-emerald-100 shadow-sm">
                <div class="text-3xl mb-3 text-emerald-600">💼</div>
                <div class="text-2xl font-bold text-emerald-600 mb-1">2,500+</div>
                <div class="text-sm text-gray-600">Career Success Stories</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Featured Courses by Skill -->
    <section class="py-16 bg-beige-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl font-bold text-gray-900 mb-4">Popular Skill Categories</h2>
          <p class="text-gray-600 max-w-2xl mx-auto">
            Master in-demand skills with our AI-enhanced courses. Each category features practical, project-based learning.
          </p>
        </div>

        <!-- Tabs -->
        <div class="mb-8">
          <div class="border-b border-gray-200">
            <nav class="flex flex-wrap -mb-px">
              <button
                v-for="tab in skillTabs"
                :key="tab.id"
                @click="activeSkillTab = tab.id"
                :class="[
                  'px-6 py-3 text-sm font-medium border-b-2 transition-colors',
                  activeSkillTab === tab.id
                    ? 'border-emerald-500 text-emerald-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                ]"
              >
                {{ tab.name }}
              </button>
            </nav>
          </div>
        </div>

        <!-- Tab Content -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
          <div
            v-for="course in getCoursesBySkill(activeSkillTab)"
            :key="course.id"
            class="bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1"
          >
            <div class="h-40 relative overflow-hidden bg-gradient-to-r from-emerald-500 to-emerald-600">
              <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
              <div class="absolute bottom-4 left-4 right-4">
                <span class="inline-block px-3 py-1 rounded-full text-xs bg-white/30 backdrop-blur-sm text-white font-medium mb-2">
                  {{ course.subject }}
                </span>
                <h3 class="text-white font-bold text-lg line-clamp-2">{{ course.title }}</h3>
              </div>
            </div>
            <div class="p-5">
              <p class="text-gray-700 text-sm line-clamp-2 mb-4">
                {{ course.description || 'Start your learning journey today' }}
              </p>
              <div class="flex items-center justify-between text-sm text-gray-600 mb-4">
                <div class="flex items-center">
                  <svg class="w-4 h-4 mr-1 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                  </svg>
                  {{ course.estimated_duration_hours || 'Self-paced' }} hrs
                </div>
                <span class="text-emerald-600 font-medium">{{ course.level }}</span>
              </div>
              <Link
                :href="route('courses.show', { id: course.id, slug: course.slug })"
                class="w-full text-center block bg-gradient-to-r from-emerald-600 to-emerald-700 text-white py-2.5 rounded-lg text-sm font-medium hover:from-emerald-700 hover:to-emerald-800 transition-all duration-300"
              >
                Enroll Now
              </Link>
            </div>
          </div>
        </div>

        <div class="text-center">
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
      </div>
    </section>

    <!-- Testimonials -->
    <section class="py-16 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl font-bold text-gray-900 mb-4">Success Stories from Our Learners</h2>
          <p class="text-gray-600 max-w-2xl mx-auto">
            Hear from Nigerian students and professionals who transformed their careers with OliLearn
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div
            v-for="testimonial in testimonials"
            :key="testimonial.id"
            class="bg-gray-50 p-6 rounded-xl border border-gray-200 hover:border-emerald-200 transition-colors hover:shadow-lg"
          >
            <div class="flex items-start mb-4">
              <img :src="testimonial.avatar" :alt="testimonial.name" class="w-12 h-12 rounded-full mr-4 border-2 border-emerald-100">
              <div>
                <div class="font-bold text-gray-900">{{ testimonial.name }}</div>
                <div class="text-sm text-gray-600">{{ testimonial.location }} • {{ testimonial.role }}</div>
              </div>
            </div>
            <p class="text-gray-700 mb-4 italic">"{{ testimonial.quote }}"</p>
            <div class="text-sm text-gray-500">
              Completed: <span class="font-medium text-emerald-600">{{ testimonial.course }}</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Exam Preparation -->
    <section class="py-16 bg-emerald-900 text-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
          <div>
            <div class="inline-flex items-center px-4 py-2 rounded-full bg-emerald-800 text-emerald-100 text-sm font-medium mb-4">
              <span class="w-2 h-2 bg-emerald-300 rounded-full mr-2"></span>
              Exam Excellence
            </div>
            <h2 class="text-3xl font-bold mb-6">
              Ace Nigerian & International Exams with Confidence
            </h2>
            <p class="text-emerald-100 mb-8 leading-relaxed">
              OliLearn provides comprehensive preparation for all major examinations. Our AI-powered platform adapts to your learning style and focuses on your weak areas.
            </p>

            <div class="grid grid-cols-2 gap-6 mb-8">
              <div class="bg-emerald-800/50 p-4 rounded-lg border border-emerald-700">
                <div class="text-2xl font-bold mb-2 text-emerald-300">WAEC</div>
                <div class="text-sm text-emerald-200">Comprehensive syllabus coverage</div>
              </div>
              <div class="bg-emerald-800/50 p-4 rounded-lg border border-emerald-700">
                <div class="text-2xl font-bold mb-2 text-emerald-300">JAMB</div>
                <div class="text-sm text-emerald-200">Past questions & CBT practice</div>
              </div>
              <div class="bg-emerald-800/50 p-4 rounded-lg border border-emerald-700">
                <div class="text-2xl font-bold mb-2 text-emerald-300">SAT/IELTS</div>
                <div class="text-sm text-emerald-200">International exam prep</div>
              </div>
              <div class="bg-emerald-800/50 p-4 rounded-lg border border-emerald-700">
                <div class="text-2xl font-bold mb-2 text-emerald-300">Professional</div>
                <div class="text-sm text-emerald-200">Certifications & licenses</div>
              </div>
            </div>

            <Link
              :href="route('courses.index') + '?subjects=Exam+Preparation'"
              class="inline-flex items-center bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-3 rounded-lg font-bold transition-colors"
            >
              Explore Exam Courses
              <svg class="w-5 h-5 ml-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
              </svg>
            </Link>
          </div>

          <div class="bg-white p-8 rounded-2xl text-gray-900">
            <h3 class="text-xl font-bold mb-6 text-emerald-800">Why Choose OliLearn for Exams?</h3>
            <ul class="space-y-4">
              <li class="flex items-start">
                <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mr-3 flex-shrink-0">
                  ✓
                </div>
                <div>
                  <div class="font-semibold mb-1">Adaptive Practice Tests</div>
                  <p class="text-sm text-gray-600">AI-generated tests that focus on your weak areas</p>
                </div>
              </li>
              <li class="flex items-start">
                <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mr-3 flex-shrink-0">
                  ✓
                </div>
                <div>
                  <div class="font-semibold mb-1">Past Questions Database</div>
                  <p class="text-sm text-gray-600">Access to 10+ years of past questions with solutions</p>
                </div>
              </li>
              <li class="flex items-start">
                <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mr-3 flex-shrink-0">
                  ✓
                </div>
                <div>
                  <div class="font-semibold mb-1">Performance Analytics</div>
                  <p class="text-sm text-gray-600">Track progress and predict exam scores</p>
                </div>
              </li>
              <li class="flex items-start">
                <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mr-3 flex-shrink-0">
                  ✓
                </div>
                <div>
                  <div class="font-semibold mb-1">Expert Video Explanations</div>
                  <p class="text-sm text-gray-600">Nigerian instructors explain difficult concepts</p>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </section>

    <!-- Trending Courses Carousel -->
    <section class="py-16 bg-beige-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
          <div>
            <h2 class="text-3xl font-bold text-gray-900">Trending Courses This Week</h2>
            <p class="text-gray-600 mt-2">Most enrolled courses by Nigerian learners</p>
          </div>
          <Link
            :href="route('courses.index') + '?sort=popular'"
            class="text-emerald-600 hover:text-emerald-700 font-medium"
          >
            View All Trending →
          </Link>
        </div>

        <div class="relative">
          <div class="overflow-hidden">
            <div
              ref="trendingCarousel"
              class="flex transition-transform duration-500 ease-out"
              :style="{ transform: `translateX(-${trendingCarousel.currentIndex * 100}%)` }"
            >
              <div
                v-for="(group, index) in trendingCourseGroups"
                :key="index"
                class="w-full flex-shrink-0"
              >
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                  <div
                    v-for="course in group"
                    :key="course.id"
                    class="bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1"
                  >
                    <div class="h-48 relative overflow-hidden bg-gradient-to-r from-emerald-500 to-emerald-600">
                      <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                      <div class="absolute top-4 left-4">
                        <span class="inline-block px-3 py-1 rounded-full text-xs bg-white/30 backdrop-blur-sm text-white font-medium">
                          TRENDING
                        </span>
                      </div>
                      <div class="absolute bottom-4 left-4 right-4">
                        <h3 class="text-white font-bold text-lg line-clamp-2">{{ course.title }}</h3>
                      </div>
                    </div>
                    <div class="p-5">
                      <div class="flex items-center justify-between mb-4">
                        <span class="text-sm text-gray-600">{{ course.subject }}</span>
                        <span class="text-xs bg-emerald-100 text-emerald-800 px-2 py-1 rounded-full">
                          {{ course.enrollment_count || 0 }}+ enrolled
                        </span>
                      </div>
                      <Link
                        :href="route('courses.show', { id: course.id, slug: course.slug })"
                        class="w-full text-center block bg-gradient-to-r from-emerald-600 to-emerald-700 text-white py-2.5 rounded-lg text-sm font-medium hover:from-emerald-700 hover:to-emerald-800 transition-all"
                      >
                        Join Now
                      </Link>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Navigation -->
          <button
            @click="scrollTrending('left')"
            :disabled="trendingCarousel.currentIndex === 0"
            class="absolute left-0 top-1/2 transform -translate-y-1/2 -translate-x-4 p-3 rounded-full bg-white shadow-lg hover:shadow-xl text-gray-600 hover:text-gray-900 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
          </button>
          <button
            @click="scrollTrending('right')"
            :disabled="trendingCarousel.currentIndex >= trendingCourseGroups.length - 1"
            class="absolute right-0 top-1/2 transform -translate-y-1/2 translate-x-4 p-3 rounded-full bg-white shadow-lg hover:shadow-xl text-gray-600 hover:text-gray-900 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </button>

          <!-- Dots -->
          <div class="flex justify-center mt-8 space-x-2">
            <button
              v-for="(_, index) in trendingCourseGroups"
              :key="index"
              @click="trendingCarousel.currentIndex = index"
              class="w-2 h-2 rounded-full transition-all"
              :class="trendingCarousel.currentIndex === index ? 'bg-emerald-600 w-6' : 'bg-gray-300'"
            />
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-gradient-to-r from-emerald-700 to-emerald-800">
      <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
          Ready to Transform Your Learning Journey?
        </h2>
        <p class="text-lg text-emerald-100 mb-8 max-w-2xl mx-auto">
          Join 10,000+ Nigerian learners who are mastering new skills, acing exams, and advancing their careers with OliLearn.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <Link
            :href="route('register')"
            class="bg-white hover:bg-gray-50 text-emerald-700 px-8 py-3.5 rounded-lg font-bold text-lg transition-all hover:scale-105 hover:shadow-xl"
          >
            Start Free Trial
          </Link>
          <Link
            :href="route('courses.index')"
            class="bg-transparent border-2 border-white text-white px-8 py-3.5 rounded-lg font-semibold text-lg hover:bg-white/10 transition-all"
          >
            Browse All Courses
          </Link>
        </div>
        <div class="mt-8 text-emerald-200 text-sm">
          <div class="flex flex-wrap justify-center gap-6">
            <div class="flex items-center">
              <span class="w-2 h-2 bg-emerald-300 rounded-full mr-2"></span>
              Lifetime free account
            </div>
            <div class="flex items-center">
              <span class="w-2 h-2 bg-emerald-300 rounded-full mr-2"></span>
              No credit card required
            </div>
            <div class="flex items-center">
              <span class="w-2 h-2 bg-emerald-300 rounded-full mr-2"></span>
              Cancel anytime
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Pricing -->
    <PricingPlans
      :plans="subscriptionPlans"
      :show-role-selector="true"
      :show-enterprise="true"
      section-id="pricing"
      header-badge="Simple Pricing"
      header-title="INVEST IN YOUR FUTURE"
      header-subtitle="AFRICAN-FRIENDLY PLANS"
      header-description="Choose a plan that works for you. All plans include access to our AI-powered platform and Nigerian instructor support."
    />
  </AppLayout>
</template>

<script setup>
import { Link, Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/GuestLayout.vue';
import PricingPlans from '@/Components/PricingPlans.vue';
import MetaTags from '@/Components/MetaTags.vue';
import { ref, computed, onMounted, onUnmounted } from 'vue';

const props = defineProps({
  courses: { type: Array, default: () => [] },
  subscriptionPlans: { type: Array, default: () => [] }
});

// Hero Carousel
const heroCarousel = ref({
  currentIndex: 0,
  autoPlayInterval: null,
  isPaused: false,
  totalSlides: 3
});

// Navigation functions
const nextSlide = () => {
  heroCarousel.value.currentIndex = (heroCarousel.value.currentIndex + 1) % heroCarousel.value.totalSlides;
};

const prevSlide = () => {
  heroCarousel.value.currentIndex = heroCarousel.value.currentIndex === 0
    ? heroCarousel.value.totalSlides - 1
    : heroCarousel.value.currentIndex - 1;
};

// Start auto-play
const startAutoPlay = () => {
  heroCarousel.value.autoPlayInterval = setInterval(() => {
    if (!heroCarousel.value.isPaused) {
      nextSlide();
    }
  }, 5000); // Change slide every 5 seconds
};

// Pause auto-play on hover
const pauseAutoPlay = () => {
  heroCarousel.value.isPaused = true;
};

const resumeAutoPlay = () => {
  heroCarousel.value.isPaused = false;
};

// Touch support for mobile
const touchStartX = ref(0);
const touchEndX = ref(0);

const handleTouchStart = (e) => {
  touchStartX.value = e.touches[0].clientX;
  pauseAutoPlay();
};

const handleTouchEnd = (e) => {
  touchEndX.value = e.changedTouches[0].clientX;
  handleSwipe();
  setTimeout(() => resumeAutoPlay(), 3000); // Resume after 3 seconds
};

const handleSwipe = () => {
  const threshold = 50;
  const diff = touchStartX.value - touchEndX.value;

  if (Math.abs(diff) > threshold) {
    if (diff > 0) {
      nextSlide(); // Swipe left
    } else {
      prevSlide(); // Swipe right
    }
  }
};

onMounted(() => {
  startAutoPlay();

  // Add touch event listeners to carousel
  const carousel = document.querySelector('.relative.h-[600px]');
  if (carousel) {
    carousel.addEventListener('touchstart', handleTouchStart);
    carousel.addEventListener('touchend', handleTouchEnd);
  }

  // Pause on hover
  carousel?.addEventListener('mouseenter', pauseAutoPlay);
  carousel?.addEventListener('mouseleave', resumeAutoPlay);
});

onUnmounted(() => {
  if (heroCarousel.value.autoPlayInterval) {
    clearInterval(heroCarousel.value.autoPlayInterval);
  }

  // Remove event listeners
  const carousel = document.querySelector('.relative.h-[600px]');
  if (carousel) {
    carousel.removeEventListener('touchstart', handleTouchStart);
    carousel.removeEventListener('touchend', handleTouchEnd);
    carousel.removeEventListener('mouseenter', pauseAutoPlay);
    carousel.removeEventListener('mouseleave', resumeAutoPlay);
  }
});

// Subjects
const subjects = ref([
  { name: 'Computer Science', icon: '💻', count: 45 },
  { name: 'Mathematics', icon: '📐', count: 32 },
  { name: 'Science', icon: '🔬', count: 28 },
  { name: 'Business', icon: '💼', count: 36 },
  { name: 'Languages', icon: '🗣️', count: 24 },
  { name: 'Exam Prep', icon: '📚', count: 41 }
]);

// Skill Tabs
const activeSkillTab = ref('tech');
const skillTabs = ref([
  { id: 'tech', name: 'Technology & Coding' },
  { id: 'business', name: 'Business & Finance' },
  { id: 'creative', name: 'Creative Arts' },
  { id: 'academic', name: 'Academic Subjects' }
]);

// Filter courses by skill
const getCoursesBySkill = (skill) => {
  const skillMap = {
    tech: ['Computer Science', 'Programming', 'Web Development'],
    business: ['Business', 'Finance', 'Marketing'],
    creative: ['Design', 'Music', 'Writing'],
    academic: ['Mathematics', 'Science', 'Languages']
  };

  return props.courses
    .filter(course => skillMap[skill]?.includes(course.subject))
    .slice(0, 3);
};

// Testimonials
const testimonials = ref([
  {
    id: 1,
    name: 'Chinedu Okoro',
    role: 'JAMB Candidate',
    location: 'Lagos',
    avatar: 'https://api.dicebear.com/7.x/avataaars/svg?seed=Chinedu',
    quote: 'OliLearn helped me score 305 in JAMB. The AI practice tests were exactly like the real exam!',
    course: 'JAMB Preparation Course'
  },
  {
    id: 2,
    name: 'Aisha Bello',
    role: 'Web Developer',
    location: 'Abuja',
    avatar: 'https://api.dicebear.com/7.x/avataaars/svg?seed=Aisha',
    quote: 'Landed my first developer job after completing the Full Stack course. The projects were portfolio-ready!',
    course: 'Full Stack Web Development'
  },
  {
    id: 3,
    name: 'Emeka Nwosu',
    role: 'WAEC Student',
    location: 'Enugu',
    avatar: 'https://api.dicebear.com/7.x/avataaars/svg?seed=Emeka',
    quote: 'Went from failing Maths to scoring A1. The AI tutor explained concepts in ways I could understand.',
    course: 'WAEC Mathematics'
  }
]);

// Trending Courses Carousel
const trendingCarousel = ref({
  currentIndex: 0,
  autoScrollInterval: null
});

const trendingCarouselRef = ref(null);

const trendingCourseGroups = computed(() => {
  const groups = [];
  const trendingCourses = props.courses.slice(0, 9); // Get top 9 trending

  for (let i = 0; i < trendingCourses.length; i += 3) {
    groups.push(trendingCourses.slice(i, i + 3));
  }

  return groups;
});

const scrollTrending = (direction) => {
  if (direction === 'left' && trendingCarousel.value.currentIndex > 0) {
    trendingCarousel.value.currentIndex--;
  } else if (direction === 'right' && trendingCarousel.value.currentIndex < trendingCourseGroups.value.length - 1) {
    trendingCarousel.value.currentIndex++;
  }
};

const startAutoScroll = () => {
  if (trendingCourseGroups.value.length <= 1) return;

  trendingCarousel.value.autoScrollInterval = setInterval(() => {
    if (trendingCarousel.value.currentIndex >= trendingCourseGroups.value.length - 1) {
      trendingCarousel.value.currentIndex = 0;
    } else {
      trendingCarousel.value.currentIndex++;
    }
  }, 5000);
};

onMounted(() => {
  // Auto-scroll hero carousel
  setInterval(() => {
    heroCarousel.value.currentIndex = (heroCarousel.value.currentIndex + 1) % 3;
  }, 5000);

  // Start trending carousel auto-scroll
  startAutoScroll();
});

onUnmounted(() => {
  if (trendingCarousel.value.autoScrollInterval) {
    clearInterval(trendingCarousel.value.autoScrollInterval);
  }
});
</script>

<style scoped>
/* Add beige color to Tailwind */
.bg-beige-50 {
  background-color: #fafaf8;
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

/* Smooth transitions */
.slide-enter-active,
.slide-leave-active {
  transition: opacity 0.5s ease-in-out;
}

.slide-enter-from,
.slide-leave-to {
  opacity: 0;
}
</style>
