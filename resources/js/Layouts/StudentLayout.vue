<template>
  <div>
    <!-- Flash Messages -->
    <div class="fixed top-4 right-4 z-50 max-w-sm w-full">
      <!-- Error Message -->
      <div
        v-if="$page.props.flash.error"
        class="bg-red-50 border border-red-200 rounded-xl p-4 shadow-lg mb-3 transition-all duration-300 animate-fade-in"
      >
        <div class="flex items-start">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <div class="ml-3 flex-1">
            <h3 class="text-sm font-medium text-red-800">
              {{ $page.props.flash.error }}
            </h3>
          </div>
          <div class="ml-auto pl-3">
            <button
              @click="clearFlash('error')"
              class="inline-flex text-red-400 hover:text-red-600 transition-colors"
            >
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Success Message -->
      <div
        v-if="$page.props.flash.success || $page.props.flash.status ||  $page.props.flash.message"
        class="bg-green-50 border border-green-200 rounded-xl p-4 shadow-lg transition-all duration-300 animate-fade-in"
      >
        <div class="flex items-start">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <div class="ml-3 flex-1">
            <h3 class="text-sm font-medium text-green-800">
              {{ $page.props.flash.success || $page.props.flash.status ||  $page.props.flash.message}}
            </h3>
          </div>
          <div class="ml-auto pl-3">
            <button
              @click="clearFlash('success')"
              class="inline-flex text-green-400 hover:text-green-600 transition-colors"
            >
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="min-h-screen flex bg-gradient-to-br from-slate-50 via-blue-50/30 to-white">
      <!-- FIXED SIDEBAR -->
      <aside
        class="fixed top-0 left-0 h-screen bg-white/95 backdrop-blur-xl shadow-xl
               border-r border-gray-200/40 z-40
               transition-all duration-300 ease-in-out flex flex-col"
        :class="[
          isSidebarCollapsed ? 'w-20' : 'w-64',
          showingNavigationDropdown ? 'translate-x-0' : '-translate-x-full md:translate-x-0'
        ]"
      >
        <!-- Logo / Collapse Toggle -->
        <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200/30">
          <!-- Logo -->
          <Link
            :href="route('student.dashboard')"
            class="flex items-center"
          >
            <img
              src="/logo-olilearn.PNG"
              alt="OliLearn"
              class="h-10 w-auto object-contain"
            />
          </Link>

          <!-- Collapse / Close Button -->
          <button
            @click="toggleSidebar"
            class="hidden md:flex items-center justify-center p-2 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50/80 rounded-xl transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:bg-emerald-50/50 hover:scale-110"
          >
            <ChevronDoubleLeftIcon v-if="!isSidebarCollapsed" class="h-4 w-4 transform transition-transform duration-300" />
            <ChevronDoubleRightIcon v-else class="h-4 w-4 transform transition-transform duration-300" />
          </button>

          <!-- Mobile Close Button -->
          <button
            @click="showingNavigationDropdown = false"
            class="md:hidden flex items-center justify-center p-2 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50/80 rounded-xl transition-all duration-300 hover:scale-110"
          >
            <XMarkIcon class="h-5 w-5" />
          </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 py-4">
          <div class="space-y-1 px-2">
            <NavLink
              v-for="item in navigation"
              :key="item.name"
              :href="item.href"
              :active="route().current(item.active)"
              class="group flex items-center w-full px-3 py-2 text-sm rounded-lg transition-colors duration-200"
              :class="[
                route().current(item.active)
                  ? 'bg-emerald-500 text-white shadow-sm'
                  : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'
              ]"
            >
              <component
                :is="item.icon"
                class="h-4 w-4 flex-shrink-0 transition-transform duration-200"
                :class="route().current(item.active)
                  ? 'text-white'
                  : 'text-gray-400 group-hover:text-gray-600'"
              />
              <span
                class="ml-3 transition-all duration-200 truncate"
                :class="isSidebarCollapsed ? 'w-0 opacity-0' : 'w-auto opacity-100'"
              >
                {{ item.name }}
              </span>
            </NavLink>
            <div class="mt-8">
                <OnboardingButton />
            </div>
          </div>
        </nav>

        <!-- User Profile Mini -->
        <div class="border-t border-gray-200/30 p-4">
          <div class="flex items-center group cursor-pointer">
            <div class="relative">
              <img
                class="h-9 w-9 rounded-full object-cover border-2 border-emerald-200 ring-2 ring-white shadow-sm group-hover:scale-105 transition-all duration-300"
                :src="$page.props.auth.user.profile_photo_url"
                :alt="$page.props.auth.user.name"
              />
              <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-400 rounded-full border-2 border-white shadow-sm"></div>
            </div>
            <div
              class="ml-3 transition-all duration-300 overflow-hidden"
              :class="isSidebarCollapsed ? 'w-0 opacity-0' : 'w-auto opacity-100'"
            >
              <p class="text-sm font-semibold text-gray-900 truncate">{{ $page.props.auth.user.name }}</p>
              <p class="text-xs text-gray-500 flex items-center">
                <span class="w-2 h-2 bg-emerald-400 rounded-full mr-1.5"></span>
                Student
              </p>
            </div>
          </div>
        </div>
      </aside>

      <!-- Overlay for mobile -->
      <div
        v-show="showingNavigationDropdown"
        @click="showingNavigationDropdown = false"
        class="fixed inset-0 bg-black/30 backdrop-blur-md z-30 md:hidden transition-all duration-300"
      ></div>

      <!-- Main Content -->
      <div class="flex flex-col flex-1 min-w-0 transition-all duration-300"
        :class="isSidebarCollapsed ? 'md:ml-20' : 'md:ml-64'">
        <!-- Top Bar -->
        <header class="bg-white/90 backdrop-blur-xl shadow-sm border-b border-gray-200/30 sticky top-0 z-30">
          <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
            <!-- Mobile menu button -->
            <button
              @click="showingNavigationDropdown = !showingNavigationDropdown"
              class="md:hidden inline-flex items-center justify-center p-2 rounded-xl text-gray-500 hover:text-emerald-600 hover:bg-emerald-50/80 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all duration-300 hover:scale-105"
            >
              <Bars3Icon v-show="!showingNavigationDropdown" class="h-5 w-5 transform transition-transform duration-300" />
              <XMarkIcon v-show="showingNavigationDropdown" class="h-5 w-5 transform transition-transform duration-300" />
            </button>

            <!-- Quick Actions -->
            <div class="hidden sm:flex space-x-3">


            </div>

            <!-- Right-side controls -->
            <div class="flex items-center space-x-2">
              <!-- Notifications -->
              <Link
                :href="route('student.notifications.index')"
                class="relative p-2.5 text-gray-500 hover:text-emerald-600 hover:bg-emerald-50/80 rounded-xl transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 transform hover:scale-105 group"
            >
                <BellIcon class="h-5 w-5 transform group-hover:shake-animation" />
                <span
                    v-if="unreadNotificationCount > 0"
                    class="absolute -top-0.5 -right-0.5 w-3 h-3 bg-red-500 rounded-full border-2 border-white shadow-sm"
                ></span>
            </Link>

              <!-- Profile Dropdown -->
              <Dropdown align="right" width="48">
                <template #trigger>
                  <button class="flex text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all duration-300 transform hover:scale-105 group">
                    <img
                      class="h-9 w-9 rounded-full object-cover border-2 border-emerald-200 ring-2 ring-white shadow-sm group-hover:shadow-md transition-all duration-300"
                      :src="$page.props.auth.user.profile_photo_url"
                      :alt="$page.props.auth.user.name"
                    />
                  </button>
                </template>

                <template #content>
                  <div class="px-4 py-3 border-b border-gray-100/50 bg-gradient-to-r from-gray-50 to-white">
                    <p class="text-sm font-semibold text-gray-900">{{ $page.props.auth.user.name }}</p>
                    <p class="text-sm text-gray-500 mt-0.5">{{ $page.props.auth.user.email }}</p>
                  </div>

                  <div class="py-1 bg-white">
                    <DropdownLink
                      :href="route('student.profile.show')"
                      class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-emerald-50/80 hover:text-emerald-700 transition-all duration-200 group"
                    >
                      <UserCircleIcon class="h-4 w-4 mr-3 text-gray-400 transform group-hover:scale-110 transition-transform duration-300" />
                      Profile
                    </DropdownLink>
                    <DropdownLink
                      :href="route('payment.pricing')"
                      class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-emerald-50/80 hover:text-emerald-700 transition-all duration-200 group"
                    >
                      <CreditCardIcon class="h-4 w-4 mr-3 text-gray-400 transform group-hover:scale-110 transition-transform duration-300" />
                      Billing
                    </DropdownLink>
                  </div>

                  <div class="border-t border-gray-100/50" />

                  <form @submit.prevent="logout">
                    <DropdownLink
                      as="button"
                      class="flex items-center w-full px-4 py-2.5 text-sm text-gray-700 hover:bg-red-50/80 hover:text-red-700 transition-all duration-200 group"
                    >
                      <ArrowRightOnRectangleIcon class="h-4 w-4 mr-3 text-gray-400 transform group-hover:scale-110 transition-transform duration-300" />
                      Log Out
                    </DropdownLink>
                  </form>
                </template>
              </Dropdown>
            </div>
          </div>
        </header>

        <!-- Main Page Content -->
        <div
          v-if="contentLoading"
          class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50"
        >
          <div class="bg-white p-5 rounded-xl shadow-lg text-center max-w-sm">
            <div class="flex items-center justify-center mb-3">
              <svg class="animate-spin h-8 w-8 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            </div>
            <p class="text-lg font-medium text-gray-800">
              Generating your learning content…
            </p>
            <p class="text-sm text-gray-500 mt-1">
              Oli AI is creating personalized materials for you.
            </p>
            <p class="text-xs text-gray-400 mt-2">
              Please stay on this page
            </p>
          </div>
        </div>
        <main class="flex-1 overflow-y-auto bg-transparent pb-20 md:pb-0">
          <div class="p-4 sm:p-6 lg:p-8">
            <div class="max-w-7xl mx-auto" :class="{ 'blur-sm': loading && !props.disableGlobalBlur}">
              <div class="animate-fade-in-up">
                <slot />
              </div>
            </div>
          </div>
        </main>
      </div>
    </div>

    <!-- Mobile Bottom Navigation - Fixed at bottom -->
    <MobileBottomNav class="fixed bottom-0 left-0 right-0 z-40 md:hidden" />
  </div>
  <OnboardingModal
      :show="showOnboardingModal"
      :user="$page.props.auth.user"
      @close="closeOnboarding"
  />
  <!-- PWA Components -->
        <InstallButton />
        <PushPrompt />
</template>

<script setup>
import { ref, onMounted, watch, nextTick, onUnmounted } from 'vue'
import { router, usePage, Head } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import NavLink from '@/Components/NavLink.vue'
import OnboardingModal from '@/Components/Onboarding/OnboardingModal.vue';
import OnboardingButton from '@/Components/Onboarding/OnboardingButton.vue';
import PushPrompt from '@/Components/PWA/PushPrompt.vue';
import InstallButton from '@/Components/PWA/InstallButton.vue';
import {
  HomeIcon,
  BookOpenIcon,
  ClipboardDocumentListIcon,
  ChatBubbleLeftRightIcon,
  CreditCardIcon,
  UserCircleIcon,
  ArrowRightOnRectangleIcon,
  PlusIcon,
  BellIcon,
  Bars3Icon,
  XMarkIcon,
  ChevronDoubleLeftIcon,
  ChevronDoubleRightIcon,
  RectangleStackIcon,
} from '@heroicons/vue/24/outline'
import MobileBottomNav from '@/Components/Student/MobileBottomNav.vue'


const props = defineProps({
  disableGlobalBlur: {
    type: Boolean,
    default: false
  },
  contentLoading: {
    type: Boolean,
    default: false
  },
  showOnboarding: {
        type: Boolean,
        default: false,
    },
})

const showOnboardingModal = ref(props.showOnboarding);

const page = usePage()
const loading = ref(false)
const showingNavigationDropdown = ref(false)
const isSidebarCollapsed = ref(false)
const unreadNotificationCount = ref(0)

// Auto-hide flash messages after 5 seconds
const autoHideFlash = () => {
  // Use only page.props - no need for props.props
  if (page.props.flash.success || page.props.flash.error || page.props.flash.message || page.props.flash.status) {
    setTimeout(() => {
      clearFlash()
    }, 5000)
  }
}

// Clear flash messages properly
const clearFlash = (type = null) => {
  if (type) {
    // Clear specific flash type
    router.reload({ only: ['flash'] })
  } else {
    // Clear all flash messages
    router.reload({ only: ['flash'] })
  }
}

onMounted(() => {
  const saved = localStorage.getItem('student_sidebar_collapsed')
  if (saved !== null) isSidebarCollapsed.value = JSON.parse(saved)

  // Auto-hide any existing flash messages on mount
  autoHideFlash()
})

const toggleSidebar = () => {
  isSidebarCollapsed.value = !isSidebarCollapsed.value
  localStorage.setItem('student_sidebar_collapsed', isSidebarCollapsed.value)
}

const navigation = [
  { name: 'Dashboard', href: route('student.dashboard'), active: 'student.dashboard', icon: HomeIcon },
  { name: 'My Courses', href: route('student.courses.index'), active: 'student.courses.*', icon: BookOpenIcon },
  { name: 'Catalogs', href: route('student.catalog.browse'), active: 'student.catalog.*', icon: ClipboardDocumentListIcon },
  { name: 'Flash Card', href: route('student.flashcards.index'), active: 'student.flashcards.*', icon: RectangleStackIcon },
  { name: 'Profile', href: route('student.profile.show'), active: 'student.profile.*', icon: UserCircleIcon },
]

const logout = () => {
  router.post(route('logout'))
}

router.on('start', () => {
  loading.value = true
})

router.on('finish', () => {
  loading.value = false
})

// Watch for flash messages and auto-hide them


// Fetch unread notification count
const fetchUnreadCount = async () => {
    try {
        const response = await axios.get(route('student.notifications.unread-count'))
        unreadNotificationCount.value = response.data.count
    } catch (error) {
        console.error('Failed to fetch unread notification count:', error)
    }
}

// Fetch count on component mount
onMounted(() => {
    fetchUnreadCount()

    // Optional: Refresh count every 30 seconds
    setInterval(fetchUnreadCount, 30000)
})

// Watch for changes from parent
watch(() => props.showOnboarding, (newVal) => {
    showOnboardingModal.value = newVal;
});

const closeOnboarding = () => {
    showOnboardingModal.value = false;
};

// Check localStorage for restart flag
const checkLocalStorage = () => {
    if (localStorage.getItem('showOnboarding')) {
        showOnboardingModal.value = true;
        localStorage.removeItem('showOnboarding');
    }
};

// Check on component mount
onMounted(() => {
    checkLocalStorage();
});
</script>

<style scoped>
/* Custom scrollbar */
nav::-webkit-scrollbar {
  width: 4px;
}
nav::-webkit-scrollbar-track {
  background: transparent;
}
nav::-webkit-scrollbar-thumb {
  background: linear-gradient(to bottom, #a3d9a5, #6bbf6d);
  border-radius: 2px;
}
nav::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(to bottom, #6bbf6d, #4a9c4d);
}

/* Shake animation for bell icon */
@keyframes shake {
  0%, 100% { transform: rotate(0deg); }
  25% { transform: rotate(-5deg); }
  75% { transform: rotate(5deg); }
}

.shake-animation {
  animation: shake 0.5s ease-in-out;
}

/* Fade in up animation for main content */
@keyframes fade-in-up {
  0% {
    opacity: 0;
    transform: translateY(10px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in-up {
  animation: fade-in-up 0.6s ease-out;
}

/* Fade in animation for flash messages */
@keyframes fade-in {
  0% {
    opacity: 0;
    transform: translateY(-10px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fade-in 0.3s ease-out;
}
</style>
