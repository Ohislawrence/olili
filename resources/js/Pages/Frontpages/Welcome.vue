<template>
  <MetaTags
    title="Your Personal AI Tutor That Grows With You | OliLearn"
    description="Transform your learning journey with OliLearn - the intelligent education platform where AI-powered courses meet expert-led instruction. Get personalized guidance, real-time assistance, and measurable progress tracking."
    image="/images/olingolearn.png"
  />
  <AppLayout>
    <Head title="Learning Made Effortless & Smart" />

    <!-- Hero Section -->
    <section class="py-16 md:py-24 bg-gradient-to-br from-white to-emerald-50 relative overflow-hidden">
      <!-- Animated background elements -->
      <div class="absolute inset-0 z-0 opacity-5">
        <div class="absolute top-20 left-10 w-32 h-32 bg-emerald-500 rounded-full animate-pulse-slow"></div>
        <div class="absolute bottom-20 right-1/4 w-24 h-24 bg-teal-500 rounded-full animate-float-slow"></div>
        <div class="absolute top-1/3 right-10 w-16 h-16 bg-blue-400 rounded-full animate-pulse-slow" style="animation-delay: 1s;"></div>
      </div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-12">
          <div class="inline-flex items-center px-4 py-2 rounded-full bg-emerald-100 border border-emerald-200 text-emerald-700 text-sm font-medium mb-6">
            <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2 animate-pulse"></span>
            🚀 AI-Powered Learning Revolution
          </div>
          <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight">
            Learn Smarter, Not Harder with
            <span class="block text-emerald-700 bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent mt-2">
              Your 24/7 AI Learning Companion
            </span>
          </h1>
          <p class="text-lg md:text-xl text-gray-800 max-w-3xl mx-auto mb-8 leading-relaxed">
            Welcome to OliLearn, where traditional education meets artificial intelligence.
            Our platform combines expertly designed curriculum with intelligent AI tutors
            that adapt to your learning style, pace, and goals. Whether you're mastering
            new skills, preparing for exams, or advancing your career, we're here to guide
            your journey every step of the way.
          </p>
          <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <Link
              :href="route('courses.index')"
              class="bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-3.5 rounded-xl font-semibold text-lg transition-all duration-300 hover:scale-105 hover:shadow-lg shadow-emerald-200"
            >
              Start Learning Free
              <span class="ml-2 text-sm font-normal">→</span>
            </Link>
            <Link
              :href="route('courses.index')"
              class="bg-white text-emerald-700 border-2 border-emerald-200 hover:border-emerald-300 px-8 py-3.5 rounded-xl font-semibold text-lg transition-all duration-300 hover:shadow-lg"
            >
              Explore Courses
            </Link>
          </div>
          <div class="mt-8 flex flex-wrap justify-center gap-6 text-sm text-gray-600">
            <div class="flex items-center">
              <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
              No credit card required
            </div>
            <div class="flex items-center">
              <span class="w-2 h-2 bg-purple-500 rounded-full mr-2"></span>
              100+ AI-enhanced courses
            </div>
          </div>
        </div>

        <!-- Latest Courses Carousel -->
        <div class="mt-16">
          <div class="flex items-center justify-between mb-6">
            <div>
              <h2 class="text-2xl font-bold text-gray-900">Featured AI-Powered Courses</h2>
              <p class="text-gray-600 text-sm mt-1">Handpicked courses designed for maximum learning impact</p>
            </div>
            <Link :href="route('courses.index')" class="text-emerald-600 hover:text-emerald-700 font-medium text-sm">
              Browse All Courses →
            </Link>
          </div>
          <div class="relative">
            <div
              ref="carousel"
              class="flex overflow-x-auto scrollbar-hide space-x-6 pb-4"
              @mouseover="pauseAutoScroll"
              @mouseleave="startAutoScroll"
            >
              <div
                v-for="course in latestCourses"
                :key="course.id"
                class="flex-shrink-0 w-72 bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1"
              >
                <div class="h-36 relative overflow-hidden bg-gradient-to-br from-emerald-500 to-teal-600">
                  <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                  <div class="absolute bottom-4 left-4 right-4">
                    <div class="flex justify-between items-start">
                      <span class="inline-block px-3 py-1 rounded-full text-xs bg-white/30 backdrop-blur-sm text-white font-medium">{{ course.subject }}</span>
                      <span class="text-xs text-white/90 bg-black/20 px-2 py-1 rounded">{{ course.modules_count || 0 }} modules</span>
                    </div>
                    <h3 class="text-white font-bold text-base mt-2 line-clamp-2">{{ course.title }}</h3>
                  </div>
                </div>
                <div class="p-5">
                  <p class="text-gray-700 text-sm line-clamp-2 mb-4">{{ course.description || 'Start your learning journey today' }}</p>
                  <div class="flex items-center justify-between mb-4">
                    <span class="text-xs text-gray-600 flex items-center">
                      <svg class="w-4 h-4 mr-1 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                      </svg>
                      {{ course.estimated_duration_hours || 'Flexible' }} hrs
                    </span>
                    <span class="text-xs text-emerald-700 font-medium bg-emerald-50 px-2 py-1 rounded">AI-Enhanced</span>
                  </div>
                  <Link
                    :href="route('courses.show', { id: course.id, slug: course.slug })"
                    class="w-full text-center block bg-gradient-to-r from-emerald-600 to-teal-600 text-white py-2.5 rounded-lg text-sm font-medium hover:from-emerald-700 hover:to-teal-700 transition-all duration-300"
                  >
                    Start Learning
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- The OliLearn Difference -->
    <section class="py-20 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <div class="inline-flex items-center px-4 py-2 rounded-full bg-emerald-100 border border-emerald-200 text-emerald-700 text-sm font-medium mb-4">
            <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></span>
            Why Choose OliLearn?
          </div>
          <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
            THE FUTURE OF
            <span class="block text-emerald-700 bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">PERSONALIZED LEARNING</span>
          </h2>
          <p class="text-lg text-gray-800 max-w-3xl mx-auto">
            We've reimagined online education by combining human expertise with artificial intelligence
            to create a learning experience that adapts to you—not the other way around.
          </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center mb-16">
          <div>
            <h3 class="text-2xl font-bold text-gray-900 mb-4">Learning That Adapts to You</h3>
            <p class="text-gray-700 mb-6">
              Unlike traditional online courses that follow a one-size-fits-all approach, OliLearn's
              AI analyzes your learning patterns, identifies knowledge gaps, and adjusts the curriculum
              in real-time. Our intelligent system remembers what you've learned, predicts what you
              need to know next, and provides personalized recommendations.
            </p>
            <ul class="space-y-3">
              <li class="flex items-start">
                <span class="text-emerald-500 mr-3">✓</span>
                <span>Adaptive difficulty levels based on your performance</span>
              </li>
              <li class="flex items-start">
                <span class="text-emerald-500 mr-3">✓</span>
                <span>Personalized study recommendations and schedules</span>
              </li>
              <li class="flex items-start">
                <span class="text-emerald-500 mr-3">✓</span>
                <span>Real-time progress tracking with actionable insights</span>
              </li>
            </ul>
          </div>
          <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl p-8 border border-emerald-100">
            <div class="text-center">
              <div class="text-5xl mb-4">📊</div>
              <h4 class="text-xl font-bold text-gray-900 mb-2">Smart Progress Analytics</h4>
              <p class="text-gray-700">
                Visualize your learning journey with detailed analytics that show exactly
                where you excel and where you need more practice.
              </p>
            </div>
          </div>
        </div>

        <!-- AI Features Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
          <div
            v-for="(feature, index) in aiFeatures"
            :key="feature.title"
            class="group text-center p-6 rounded-2xl border border-gray-100 hover:border-emerald-200 hover:shadow-xl transition-all duration-300 hover:-translate-y-2 bg-white"
          >
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-emerald-100 to-teal-100 flex items-center justify-center text-emerald-700 text-2xl group-hover:scale-110 transition-transform">
              {{ feature.icon }}
            </div>
            <h3 class="font-bold text-gray-900 mb-3 text-lg">{{ feature.title }}</h3>
            <p class="text-gray-700 text-sm leading-relaxed">{{ feature.description }}</p>
            <div class="mt-4 text-emerald-600 text-xs font-medium opacity-0 group-hover:opacity-100 transition-opacity">
              Learn more →
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Learning Impact Stats -->
    <section class="py-16 bg-gradient-to-r from-emerald-600 to-teal-600 text-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl md:text-4xl font-bold mb-4">Proven Learning Outcomes</h2>
          <p class="text-emerald-100 text-lg max-w-2xl mx-auto">
            Join thousands of learners who have transformed their skills with OliLearn
          </p>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
          <div class="text-center">
            <div class="text-4xl font-bold mb-2">98%</div>
            <div class="text-emerald-200 text-sm">Completion Satisfaction</div>
            <div class="text-xs text-emerald-300 mt-1">Higher than industry average</div>
          </div>
          <div class="text-center">
            <div class="text-4xl font-bold mb-2">2.5x</div>
            <div class="text-emerald-200 text-sm">Faster Learning</div>
            <div class="text-xs text-emerald-300 mt-1">Compared to traditional methods</div>
          </div>
          <div class="text-center">
            <div class="text-4xl font-bold mb-2">10K+</div>
            <div class="text-emerald-200 text-sm">Active Learners</div>
            <div class="text-xs text-emerald-300 mt-1">Growing community worldwide</div>
          </div>
          <div class="text-center">
            <div class="text-4xl font-bold mb-2">24/7</div>
            <div class="text-emerald-200 text-sm">AI Tutor Availability</div>
            <div class="text-xs text-emerald-300 mt-1">Learn anytime, anywhere</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Featured Courses -->
    <section class="py-20 bg-gradient-to-b from-slate-50 to-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <div class="inline-flex items-center px-4 py-2 rounded-full bg-emerald-100 border border-emerald-200 text-emerald-700 text-sm font-medium mb-4">
            <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></span>
            Curated Learning Paths
          </div>
          <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
            MASTER IN-DEMAND
            <span class="block text-emerald-700 bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">SKILLS TODAY</span>
          </h2>
          <p class="text-lg text-gray-800 max-w-3xl mx-auto">
            Our courses are meticulously designed by industry experts and enhanced with AI to ensure
            you gain practical, market-relevant skills that employers actually want.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div
            v-for="(course, index) in courses"
            :key="course.id"
            class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 group"
          >
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

        <div class="text-center mt-12">
          <Link
            :href="route('courses.index')"
            class="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-medium text-lg group"
          >
            Explore All Courses
            <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
          </Link>
        </div>
      </div>
    </section>

    <!-- How It Works -->
    <section class="py-20 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <div class="inline-flex items-center px-4 py-2 rounded-full bg-emerald-100 border border-emerald-200 text-emerald-700 text-sm font-medium mb-4">
            <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></span>
            Your Learning Journey Simplified
          </div>
          <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
            START LEARNING IN
            <span class="block text-emerald-700 bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">3 SIMPLE STEPS</span>
          </h2>
          <p class="text-lg text-gray-800 max-w-3xl mx-auto">
            From enrollment to mastery, our streamlined process ensures you focus on learning, not navigating complex platforms.
          </p>
        </div>

        <div class="relative">
          <!-- Connecting line for desktop -->
          <div class="hidden lg:block absolute top-1/2 left-0 right-0 h-0.5 bg-gradient-to-r from-emerald-100 via-emerald-300 to-teal-100 transform -translate-y-1/2 z-0"></div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative z-10">
            <div v-for="(step, index) in howItWorks" :key="index" class="text-center p-8 bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-xl transition-shadow duration-300">
              <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-emerald-600 to-teal-600 text-white flex items-center justify-center text-2xl font-bold mx-auto mb-6 relative">
                {{ step.number }}
                <div class="absolute -top-2 -right-2 w-8 h-8 bg-white rounded-full flex items-center justify-center text-emerald-600 text-xs font-bold border-2 border-emerald-100">
                  {{ step.tag }}
                </div>
              </div>
              <h3 class="text-xl font-bold text-gray-900 mb-4">{{ step.title }}</h3>
              <p class="text-gray-700 mb-5">{{ step.description }}</p>
              <div class="text-left bg-gray-50 p-4 rounded-lg">
                <div class="text-xs text-gray-500 mb-2">What you'll get:</div>
                <ul class="text-sm text-gray-700 space-y-1">
                  <li v-for="item in step.features" :key="item" class="flex items-center">
                    <span class="text-emerald-500 mr-2">•</span>
                    {{ item }}
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>

        <div class="text-center mt-12">
          <Link
            :href="route('register')"
            class="inline-flex items-center px-8 py-3.5 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-xl font-semibold text-lg hover:shadow-lg transition-all"
          >
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z" clip-rule="evenodd"/>
            </svg>
            Begin Your Journey Now
          </Link>
        </div>
      </div>
    </section>

    <!-- Testimonials -->
    <section class="py-20 bg-gradient-to-br from-slate-50 to-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <div class="inline-flex items-center px-4 py-2 rounded-full bg-emerald-100 border border-emerald-200 text-emerald-700 text-sm font-medium mb-4">
            <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></span>
            Voices of Success
          </div>
          <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
            TRANSFORMING
            <span class="block text-emerald-700 bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">LEARNING EXPERIENCES</span>
          </h2>
          <p class="text-lg text-gray-800 max-w-3xl mx-auto">
            Hear from learners who have transformed their skills, careers, and confidence with OliLearn
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div
            v-for="(testimonial, index) in testimonials"
            :key="index"
            class="bg-white p-8 rounded-2xl border border-gray-200 hover:shadow-xl transition-shadow duration-300 hover:border-emerald-100"
          >
            <div class="flex items-start mb-6">
              <img :src="testimonial.avatar" :alt="testimonial.name" class="w-12 h-12 rounded-full mr-4 border-2 border-emerald-100">
              <div>
                <div class="font-bold text-gray-900">{{ testimonial.name }}</div>
                <div class="text-sm text-gray-600">{{ testimonial.role }}</div>
                <div class="flex text-emerald-500 mt-2">
                  <svg v-for="n in 5" :key="n" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                  </svg>
                </div>
              </div>
            </div>
            <p class="text-gray-700 italic mb-6 leading-relaxed">"{{ testimonial.quote }}"</p>
            <div class="text-xs text-gray-500 border-t border-gray-100 pt-4">
              Completed: <span class="font-medium text-emerald-600">{{ testimonial.course }}</span>
            </div>
          </div>
        </div>

        <div class="text-center mt-12">
          <div class="inline-flex items-center space-x-6">
            <div class="text-center">
              <div class="text-2xl font-bold text-gray-900">4.9/5</div>
              <div class="text-sm text-gray-600">Platform Rating</div>
            </div>
            <div class="h-8 w-px bg-gray-300"></div>
            <div class="text-center">
              <div class="text-2xl font-bold text-gray-900">94%</div>
              <div class="text-sm text-gray-600">Would Recommend</div>
            </div>
            <div class="h-8 w-px bg-gray-300"></div>
            <div class="text-center">
              <div class="text-2xl font-bold text-gray-900">2,500+</div>
              <div class="text-sm text-gray-600">Certificates Issued</div>
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
      header-badge="Transparent Pricing"
      header-title="INVEST IN YOUR"
      header-subtitle="LEARNING JOURNEY"
      header-description="Choose the plan that fits your goals. All plans include full access to our AI-powered learning platform."
    />

    <!-- FAQ Section -->
    <section class="py-20 bg-white">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <div class="inline-flex items-center px-4 py-2 rounded-full bg-emerald-100 border border-emerald-200 text-emerald-700 text-sm font-medium mb-4">
            <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></span>
            Common Questions
          </div>
          <h2 class="text-3xl font-bold text-gray-900 mb-4">Frequently Asked Questions</h2>
          <p class="text-gray-600 max-w-2xl mx-auto">Get answers to the most common questions about OliLearn</p>
        </div>

        <div class="space-y-6">
          <div class="border border-gray-200 rounded-xl p-6 hover:border-emerald-200 transition-colors">
            <h3 class="font-bold text-gray-900 mb-2 flex items-center">
              <span class="text-emerald-600 mr-3">Q.</span>
              How does the AI tutor actually work?
            </h3>
            <p class="text-gray-700 pl-8">
              Our AI analyzes your learning patterns, quiz results, and engagement to create a personalized learning path. It adapts content difficulty, suggests relevant materials, and provides instant explanations when you're stuck—just like a human tutor but available 24/7.
            </p>
          </div>

          <div class="border border-gray-200 rounded-xl p-6 hover:border-emerald-200 transition-colors">
            <h3 class="font-bold text-gray-900 mb-2 flex items-center">
              <span class="text-emerald-600 mr-3">Q.</span>
              Can I try before I commit?
            </h3>
            <p class="text-gray-700 pl-8">
              Absolutely! We offer a 14-day free trial with full access to all platform features. No credit card required to start. Experience our AI-powered courses and see the difference for yourself.
            </p>
          </div>

          <div class="border border-gray-200 rounded-xl p-6 hover:border-emerald-200 transition-colors">
            <h3 class="font-bold text-gray-900 mb-2 flex items-center">
              <span class="text-emerald-600 mr-3">Q.</span>
              Are the certificates recognized?
            </h3>
            <p class="text-gray-700 pl-8">
              Yes! Our certificates are verified and can be shared on LinkedIn and other professional platforms. They demonstrate completion of rigorous, AI-enhanced courses designed by industry experts.
            </p>
          </div>
        </div>

        <div class="text-center mt-10">
          <Link
            :href="route('faq')"
            class="text-emerald-600 hover:text-emerald-700 font-medium"
          >
            View all FAQs →
          </Link>
        </div>
      </div>
    </section>

    <!-- Final CTA -->
    <section class="py-20 bg-gradient-to-br from-emerald-600 via-emerald-500 to-teal-600 relative overflow-hidden">
      <!-- Background pattern -->
      <div class="absolute inset-0 opacity-10">
        <div
  class="absolute inset-0"
  style="background-image: url('data:image/svg+xml,%3Csvg width=%2260%22 height=%2260%22 viewBox=%220 0 60 60%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cg fill=%22none%22 fill-rule=%22evenodd%22%3E%3Cg fill=%22%23ffffff%22 fill-opacity=%220.4%22%3E%3Cpath d=%22M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z%22/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"
></div>

      </div>

      <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8 relative z-10">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
          Your Learning Transformation Starts Here
        </h2>
        <p class="text-lg text-emerald-100 mb-8 max-w-2xl mx-auto leading-relaxed">
          Join thousands of learners who have discovered the power of AI-enhanced education.
          Whether you're advancing your career, learning a new skill, or preparing for exams,
          OliLearn provides the tools, guidance, and community to help you succeed.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <Link
            :href="route('register')"
            class="bg-white text-emerald-700 px-8 py-3.5 rounded-xl font-semibold text-lg hover:bg-gray-50 transition-all duration-300 hover:scale-105 hover:shadow-xl"
          >
            Start Your Free Trial
          </Link>
          <Link
            :href="route('courses.index')"
            class="bg-transparent border-2 border-white text-white px-8 py-3.5 rounded-xl font-semibold text-lg hover:bg-white/10 transition-all"
          >
            Browse Courses First
          </Link>
        </div>
        <div class="mt-8 text-emerald-200 text-sm">
          <div class="flex flex-wrap justify-center gap-6">
            <div class="flex items-center">
              <span class="w-2 h-2 bg-white rounded-full mr-2"></span>
              No credit card needed
            </div>
            <div class="flex items-center">
              <span class="w-2 h-2 bg-white rounded-full mr-2"></span>
              Full platform access for 14 days
            </div>
            <div class="flex items-center">
              <span class="w-2 h-2 bg-white rounded-full mr-2"></span>
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
import { onMounted, ref } from 'vue';

const props = defineProps({
  courses: { type: Array, default: () => [] },
  blogPosts: { type: Array, default: () => [] },
  subscriptionPlans: { type: Array, default: () => [] }
});

// Latest courses = first 6 from props
const latestCourses = ref(props.courses.slice(0, 6));

// Auto-scroll carousel logic
const carousel = ref(null);
let scrollInterval = null;

const startAutoScroll = () => {
  if (latestCourses.value.length <= 3) return;
  scrollInterval = setInterval(() => {
    if (carousel.value) {
      carousel.value.scrollBy({ left: 1, behavior: 'smooth' });
      if (carousel.value.scrollLeft + carousel.value.clientWidth >= carousel.value.scrollWidth - 1) {
        carousel.value.scrollLeft = 0;
      }
    }
  }, 30);
};

const pauseAutoScroll = () => {
  if (scrollInterval) clearInterval(scrollInterval);
};

onMounted(() => {
  startAutoScroll();
});

// Enhanced AI Features with more descriptive text
const aiFeatures = [
  {
    icon: "🤖",
    title: "24/7 AI Tutor",
    description: "Get instant answers and explanations from your personal AI tutor, available anytime you need guidance on course material."
  },
  {
    icon: "📊",
    title: "Adaptive Learning",
    description: "Our AI analyzes your performance and adjusts course difficulty and content to match your unique learning pace."
  },
  {
    icon: "🎯",
    title: "Personalized Paths",
    description: "Receive customized learning recommendations based on your goals, progress, and knowledge gaps."
  },
  {
    icon: "⚡",
    title: "Real-time Feedback",
    description: "Immediate assessment and constructive feedback on quizzes, exercises, and assignments to accelerate learning."
  }
];

// Enhanced How It Works with more details
const howItWorks = [
  {
    number: "1",
    tag: "Quick",
    title: "Choose Your Course",
    description: "Browse our catalog of AI-enhanced courses and select the one that matches your learning goals.",
    features: [
      "100+ expert-designed courses",
      "AI-powered difficulty assessment",
      "Free trial access included",
      "Flexible learning schedules"
    ]
  },
  {
    number: "2",
    tag: "Smart",
    title: "Learn with AI Guidance",
    description: "Dive into interactive lessons while your AI tutor provides real-time support and personalized insights.",
    features: [
      "24/7 AI tutor assistance",
      "Interactive quizzes & exercises",
      "Progress tracking dashboard",
      "Knowledge gap analysis"
    ]
  },
  {
    number: "3",
    tag: "Achieve",
    title: "Master & Get Certified",
    description: "Complete courses, demonstrate mastery, and earn verifiable certificates for your achievements.",
    features: [
      "Verifiable certificates",
      "LinkedIn integration",
      "Skill assessment reports",
      "Portfolio-ready projects"
    ]
  }
];

// Enhanced Testimonials with more realistic scenarios
const testimonials = [
  {
    name: "Sarah Johnson",
    role: "Marketing Professional",
    avatar: "https://api.dicebear.com/7.x/avataaars/svg?seed=Sarah",
    quote: "OliLearn's AI tutor helped me transition from traditional marketing to digital in just 3 months. The personalized learning path was exactly what I needed!",
    course: "Digital Marketing Mastery"
  },
  {
    name: "Michael Chen",
    role: "Software Developer",
    avatar: "https://api.dicebear.com/7.x/avataaars/svg?seed=Michael",
    quote: "As a developer, I appreciated how the AI adapted to my existing knowledge. I skipped basics and dove straight into advanced concepts I needed for work.",
    course: "Advanced Python & AI"
  },
  {
    name: "Jessica Williams",
    role: "Career Changer",
    avatar: "https://api.dicebear.com/7.x/avataaars/svg?seed=Jessica",
    quote: "After 10 years in finance, I wanted a career change. OliLearn's data science course with AI guidance made the transition possible while working full-time.",
    course: "Data Science Fundamentals"
  }
];

// Keep existing formatDate function
const formatDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
};
</script>

<style scoped>
/* Hide scrollbar but allow scrolling */
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}

/* Line clamp utilities */
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

/* Custom animations */
@keyframes pulse-slow {
  0%, 100% { opacity: 0.3; transform: scale(1); }
  50% { opacity: 0.5; transform: scale(1.05); }
}

@keyframes float-slow {
  0%, 100% { transform: translateY(0) rotate(0deg); }
  50% { transform: translateY(-20px) rotate(5deg); }
}

@keyframes fade-in-up {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-pulse-slow {
  animation: pulse-slow 4s ease-in-out infinite;
}

.animate-float-slow {
  animation: float-slow 6s ease-in-out infinite;
}

.animate-fade-in-up {
  animation: fade-in-up 0.6s ease-out forwards;
}

/* Enhanced hover effects */
.hover-lift {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.hover-lift:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Gradient text effect */
.gradient-text {
  background-clip: text;
  -webkit-background-clip: text;
  color: transparent;
  background-image: linear-gradient(to right, #059669, #0d9488);
}
</style>
