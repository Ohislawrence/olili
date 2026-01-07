<template>
  <div class="font-sans bg-white min-h-screen flex flex-col">
    <!-- Header -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-sm border-b border-gray-100 shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <!-- Logo -->
          <div class="flex items-center">
            <Link :href="route('welcome')" class="flex items-center space-x-3 group">
              <div class="relative">
                <img
                  src="/logo-olilearn.PNG"
                  alt="OliLearn - AI-Powered Learning Platform"
                  class="h-9 w-auto object-contain transition-transform group-hover:scale-105"
                />
              </div>
            </Link>
          </div>



          <!-- Centered Search Bar (Desktop) -->
          <div class="hidden lg:flex flex-1 max-w-xl mx-8">
            <div class="relative w-full group">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-4 w-4 text-gray-400 group-hover:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </div>
              <input
                type="text"
                v-model="globalSearch"
                placeholder="Search courses, topics, skills..."
                class="w-full pl-10 pr-4 py-2.5 text-sm rounded-xl border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200 hover:bg-white hover:border-gray-300"
                @keyup.enter="handleGlobalSearch"
                @focus="showSearchSuggestions = true"
                @blur="setTimeout(() => showSearchSuggestions = false, 200)"
              />
              <button
                @click="handleGlobalSearch"
                class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-emerald-600 transition-colors p-1.5 rounded-lg hover:bg-emerald-50"
                aria-label="Search"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </button>

              <!-- Search Suggestions -->
              <div
                v-if="showSearchSuggestions && globalSearch.trim() && popularSearches.length > 0"
                class="absolute top-full mt-1 w-full bg-white rounded-xl border border-gray-200 shadow-lg py-2 z-50"
              >
                <div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                  Popular Searches
                </div>
                <div
                  v-for="search in popularSearches"
                  :key="search"
                  @click="selectSearchSuggestion(search)"
                  @mousedown.prevent
                  class="px-3 py-2.5 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 cursor-pointer flex items-center group"
                >
                  <svg class="w-4 h-4 mr-3 text-gray-400 group-hover:text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                  </svg>
                  {{ search }}
                </div>
              </div>
            </div>
          </div>

          <!-- Auth Buttons + Mobile Menu -->
          <div class="flex items-center space-x-4">
            <!-- Mobile Search Button -->
            <button
              @click="toggleMobileSearch"
              class="lg:hidden text-gray-600 hover:text-emerald-600 transition-colors p-2 rounded-lg hover:bg-gray-100"
              aria-label="Search"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </button>

            <!-- Desktop Auth Buttons -->
            <div class="hidden lg:flex items-center space-x-4">
              <Link
                :href="route('login')"
                class="text-gray-700 hover:text-emerald-600 text-sm font-medium transition-colors px-4 py-2 rounded-lg hover:bg-gray-50"
              >
                Sign In
              </Link>
              <Link
                :href="route('register')"
                class="bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white px-5 py-2.5 rounded-xl text-sm font-medium shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-0.5"
              >
                Start Learning Free
              </Link>
            </div>

            <!-- Mobile Menu Button -->
            <button
              @click="toggleMobileMenu"
              class="lg:hidden text-gray-600 hover:text-emerald-600 transition-colors p-2 rounded-lg hover:bg-gray-100"
              aria-label="Menu"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path v-if="showMobileMenu" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Mobile Search Bar -->
        <div
          v-if="showMobileSearch"
          class="lg:hidden py-4 border-t border-gray-100 animate-slide-down"
        >
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
            <input
              type="text"
              v-model="globalSearch"
              placeholder="Search courses..."
              class="w-full pl-10 pr-12 py-3 text-sm rounded-xl border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"
              @keyup.enter="handleGlobalSearch"
            />
            <button
              @click="handleGlobalSearch"
              class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-emerald-600 transition-colors"
              aria-label="Search"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Mobile Menu -->
        <div
          v-if="showMobileMenu"
          class="lg:hidden py-4 border-t border-gray-100 animate-slide-down bg-white/95 backdrop-blur-sm"
        >
          <div class="space-y-1">
            <Link
              v-for="item in navItems"
              :key="item.name"
              :href="item.route"
              @click="showMobileMenu = false"
              :class="[
                'block px-3 py-3 rounded-lg text-base font-medium transition-colors',
                isActive(item.component)
                  ? 'bg-emerald-50 text-emerald-700'
                  : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'
              ]"
            >
              {{ item.name }}
            </Link>
          </div>
          <div class="mt-6 pt-6 border-t border-gray-100 space-y-4">
            <Link
              :href="route('login')"
              @click="showMobileMenu = false"
              class="block w-full text-center text-gray-700 hover:text-emerald-600 text-base font-medium py-2.5 rounded-lg border border-gray-200 hover:border-emerald-200 transition-colors"
            >
              Sign In
            </Link>
            <Link
              :href="route('register')"
              @click="showMobileMenu = false"
              class="block w-full text-center bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-base font-medium py-3 rounded-xl shadow-sm hover:shadow-md transition-all"
            >
              Start Learning Free
            </Link>
          </div>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow">
      <slot />
    </main>

    <!-- Footer -->
    <footer class="bg-gradient-to-b from-white to-gray-50 border-t border-gray-100">
      <!-- Newsletter Section -->
      <div class="bg-gradient-to-r from-emerald-50 to-teal-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto text-center">
          <div class="inline-flex items-center px-4 py-2 rounded-full bg-emerald-100 border border-emerald-200 text-emerald-700 text-sm font-medium mb-6">
            <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2 animate-pulse"></span>
            Stay Updated
          </div>
          <h3 class="text-2xl font-bold text-gray-900 mb-3">Join Our Learning Community</h3>
          <p class="text-gray-600 mb-8 max-w-2xl mx-auto">
            Get weekly updates on new courses, learning tips, and exclusive offers. No spam, just valuable insights.
          </p>
          <form @submit.prevent="subscribeNewsletter" class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto">
            <input
              type="email"
              placeholder="Your email address"
              v-model="newsletterEmail"
              class="flex-grow px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm bg-white"
              required
            />
            <button
              type="submit"
              class="bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white px-6 py-3 rounded-xl font-medium text-sm transition-all duration-300 hover:shadow-lg hover:-translate-y-0.5"
              :disabled="newsletterLoading"
            >
              <span v-if="newsletterLoading">
                <svg class="animate-spin h-5 w-5 mx-auto text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                </svg>
              </span>
              <span v-else>Subscribe Now</span>
            </button>
          </form>
          <p class="text-xs text-gray-500 mt-4">
            By subscribing, you agree to our Privacy Policy and consent to receive updates.
          </p>
        </div>
      </div>

      <!-- Main Footer Content -->
      <div class="py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10">
            <!-- Brand -->
            <div class="lg:col-span-2">
              <Link :href="route('welcome')" class="flex items-center space-x-3 mb-6 group">
                <div class="relative">
                  <img
                    src="/logo-olilearn.PNG"
                    alt="OliLearn"
                    class="h-10 w-auto object-contain transition-transform group-hover:scale-105"
                  />
                </div>
                <div>
                  <span class="text-xl font-bold text-gray-900">OliLearn</span>
                  <div class="text-sm text-emerald-600 font-medium mt-1">Intelligent Learning Platform</div>
                </div>
              </Link>
              <p class="text-gray-600 text-sm leading-relaxed max-w-md mb-6">
                Transforming education through AI-powered personalized learning. We help learners worldwide achieve their goals with smart, adaptive courses and expert guidance.
              </p>
              <div class="flex space-x-4">
                <a
                  v-for="(social, index) in socialLinks"
                  :key="index"
                  :href="social.url"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="w-10 h-10 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-gray-600 hover:text-emerald-600 hover:border-emerald-200 hover:shadow-sm transition-all duration-300 hover:-translate-y-1"
                  :aria-label="social.name"
                >
                  <component :is="social.icon" class="w-5 h-5" />
                </a>
              </div>
            </div>

            <!-- Quick Links -->
            <div>
              <h3 class="text-gray-900 font-bold text-sm uppercase tracking-wider mb-6">Platform</h3>
              <ul class="space-y-3">
                <li v-for="link in quickLinks" :key="link.name">
                  <Link
                    :href="link.route"
                    class="text-gray-600 hover:text-emerald-600 text-sm transition-colors duration-200 flex items-center group"
                  >
                    <span class="w-1.5 h-1.5 rounded-full bg-gray-300 group-hover:bg-emerald-500 mr-3 transition-colors"></span>
                    {{ link.name }}
                  </Link>
                </li>
              </ul>
            </div>

            <!-- Support -->
            <div>
              <h3 class="text-gray-900 font-bold text-sm uppercase tracking-wider mb-6">Support</h3>
              <ul class="space-y-3">
                <li v-for="link in supportLinks" :key="link.name">
                  <Link
                    :href="link.route"
                    class="text-gray-600 hover:text-emerald-600 text-sm transition-colors duration-200 flex items-center group"
                  >
                    <span class="w-1.5 h-1.5 rounded-full bg-gray-300 group-hover:bg-emerald-500 mr-3 transition-colors"></span>
                    {{ link.name }}
                  </Link>
                </li>
              </ul>
            </div>

            <!-- Legal -->
            <div>
              <h3 class="text-gray-900 font-bold text-sm uppercase tracking-wider mb-6">Legal</h3>
              <ul class="space-y-3">
                <li v-for="link in legalLinks" :key="link.name">
                  <Link
                    :href="link.route"
                    class="text-gray-600 hover:text-emerald-600 text-sm transition-colors duration-200 flex items-center group"
                  >
                    <span class="w-1.5 h-1.5 rounded-full bg-gray-300 group-hover:bg-emerald-500 mr-3 transition-colors"></span>
                    {{ link.name }}
                  </Link>
                </li>
              </ul>
            </div>
          </div>

          <!-- Bottom Bar -->
          <div class="border-t border-gray-200 mt-10 pt-8 flex flex-col md:flex-row justify-between items-center">
            <div class="text-gray-500 text-sm mb-4 md:mb-0">
              <p>© {{ currentYear }} OliLearn. All rights reserved.</p>
            </div>
            <div class="flex items-center space-x-6">
              <div class="flex items-center space-x-2 text-sm text-gray-500">
                <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>Trusted by 10,000+ learners in Nigeria</span>
              </div>
              <div class="flex items-center space-x-2">
                <img src="/images/secure-payment.svg" alt="Secure Payment" class="h-6" />
                <span class="text-xs text-gray-500">Secure Payment</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </footer>

    <!-- Back to Top Button -->
    <button
      v-if="showBackToTop"
      @click="scrollToTop"
      class="fixed bottom-6 right-6 w-12 h-12 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 z-40 flex items-center justify-center group"
      aria-label="Back to top"
    >
      <svg class="w-5 h-5 transform group-hover:-translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
      </svg>
    </button>
  </div>
</template>

<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted, h } from 'vue';

// Current year for copyright
const currentYear = new Date().getFullYear();

// Navigation items
const navItems = [
    { name: 'Home', route: route('welcome'), component: 'frontpages/Welcome' },
  { name: 'Features', route: route('features'), component: 'frontpages/Features' },
  { name: 'Pricing', route: route('pricing'), component: 'frontpages/Pricing' },


];

// Footer links
const quickLinks = [
  { name: 'Browse Courses', route: route('courses.index') },
  { name: 'Learning Paths', route: route('learning-paths') },
  { name: 'AI Tutor', route: route('ai-tutor') },
  { name: 'For Teams', route: route('teams') },
  { name: 'Home', route: route('welcome'), component: 'frontpages/Welcome' },
  { name: 'About', route: route('about'), component: 'frontpages/About' },
];

const supportLinks = [
  { name: 'Help Center', route: route('help') },
  { name: 'Community', route: route('community.index') },
  { name: 'Contact Support', route: route('contact') },
  { name: 'FAQ', route: route('faq') },
  { name: 'Blog', route: route('blog.index'), component: 'Blog' },
  { name: 'Pricing', route: route('pricing'), component: 'frontpages/Pricing' },
];

const legalLinks = [
  { name: 'Privacy Policy', route: route('privacy') },
  { name: 'Terms of Service', route: route('terms') },
  { name: 'Cookie Policy', route: route('cookies') },
  { name: 'Accessibility', route: route('accessibility') },
  { name: 'GDPR Compliance', route: route('gdpr') },
];

// Social media links with icons
const socialLinks = [
  {
    name: 'Twitter',
    url: 'https://twitter.com/olilearn',
    icon: {
      render: () => h('svg', { class: 'w-5 h-5', fill: 'currentColor', viewBox: '0 0 24 24' }, [
        h('path', { d: 'M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84' })
      ])
    }
  },
  {
    name: 'YouTube',
    url: 'https://youtube.com/olilearn',
    icon: {
      render: () => h('svg', { class: 'w-5 h-5', fill: 'currentColor', viewBox: '0 0 24 24' }, [
        h('path', { d: 'M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z' })
      ])
    }
  },
  {
    name: 'LinkedIn',
    url: 'https://linkedin.com/company/olilearn',
    icon: {
      render: () => h('svg', { class: 'w-5 h-5', fill: 'currentColor', viewBox: '0 0 24 24' }, [
        h('path', { d: 'M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z' })
      ])
    }
  },
];

// Newsletter
const newsletterEmail = ref('');
const newsletterLoading = ref(false);

const subscribeNewsletter = async () => {
  if (!newsletterEmail.value.trim()) return;

  newsletterLoading.value = true;
  try {
    await new Promise(resolve => setTimeout(resolve, 1500));
    // In a real app, you would make an API call here
    console.log('Subscribed:', newsletterEmail.value);
    alert('Thank you for subscribing to our newsletter!');
    newsletterEmail.value = '';
  } catch (error) {
    alert('Something went wrong. Please try again.');
  } finally {
    newsletterLoading.value = false;
  }
};

// Global search
const globalSearch = ref('');
const showMobileSearch = ref(false);
const showSearchSuggestions = ref(false);

// Popular search suggestions
const popularSearches = computed(() => {
  const searches = [
    'Python Programming',
    'Data Science',
    'Web Development',
    'Machine Learning',
    'Digital Marketing',
    'Business Analytics',
    'Artificial Intelligence',
    'Graphic Design'
  ];

  if (!globalSearch.value.trim()) return searches.slice(0, 4);

  return searches.filter(search =>
    search.toLowerCase().includes(globalSearch.value.toLowerCase())
  ).slice(0, 4);
});

const handleGlobalSearch = () => {
  if (globalSearch.value.trim()) {
    router.visit(route('courses.index', { search: globalSearch.value.trim() }), {
      preserveScroll: false,
      preserveState: false
    });
  } else {
    router.visit(route('courses.index'));
  }
  showMobileSearch.value = false;
  showSearchSuggestions.value = false;
};

const selectSearchSuggestion = (suggestion) => {
  globalSearch.value = suggestion;
  handleGlobalSearch();
};

// Mobile menu
const showMobileMenu = ref(false);

const toggleMobileSearch = () => {
  showMobileSearch.value = !showMobileSearch.value;
  showMobileMenu.value = false;
};

const toggleMobileMenu = () => {
  showMobileMenu.value = !showMobileMenu.value;
  showMobileSearch.value = false;
};

// Back to top button
const showBackToTop = ref(false);

const checkScrollPosition = () => {
  showBackToTop.value = window.scrollY > 300;
};

const scrollToTop = () => {
  window.scrollTo({ top: 0, behavior: 'smooth' });
};

// Active route check
const $page = usePage();

const isActive = (componentPrefix) => {
  if (!componentPrefix) return false;
  if (componentPrefix === $page.component) return true;
  if (typeof componentPrefix === 'string' && $page.component?.startsWith(componentPrefix)) return true;
  return false;
};

// Lifecycle hooks
onMounted(() => {
  window.addEventListener('scroll', checkScrollPosition);
  checkScrollPosition();
});

onUnmounted(() => {
  window.removeEventListener('scroll', checkScrollPosition);
});
</script>

<style scoped>
/* Custom animations */
@keyframes slide-down {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-slide-down {
  animation: slide-down 0.3s ease-out;
}

/* Custom scrollbar */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: #f1f5f9;
}

::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* Smooth transitions */
* {
  scroll-behavior: smooth;
}

/* Focus styles */
:focus-visible {
  outline: 2px solid #10b981;
  outline-offset: 2px;
}
</style>
