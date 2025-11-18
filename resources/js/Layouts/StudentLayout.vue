<template>
    <div>
    <!-- Flash Messages -->
    <div v-if="$page.props.flash.error || $page.props.flash.success" class="fixed top-4 right-4 z-50 max-w-sm w-full">
      <!-- Error Message -->
      <div
        v-if="$page.props.flash.error"
        class="bg-red-50 border border-red-200 rounded-xl p-4 shadow-lg mb-3 transition-all duration-300"
      >
        <div class="flex items-start">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <div class="ml-3">
            <h3 class="text-sm font-medium text-red-800">
              {{ $page.props.flash.error }}
            </h3>
          </div>
          <div class="ml-auto pl-3">
            <button
              @click="$page.props.flash.error = null"
              class="inline-flex text-red-400 hover:text-red-600"
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
        v-if="$page.props.flash.success"
        class="bg-green-50 border border-green-200 rounded-xl p-4 shadow-lg transition-all duration-300"
      >
        <div class="flex items-start">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <div class="ml-3">
            <h3 class="text-sm font-medium text-green-800">
              {{ $page.props.flash.success }}
            </h3>
          </div>
          <div class="ml-auto pl-3">
            <button
              @click="$page.props.flash.success = null"
              class="inline-flex text-green-400 hover:text-green-600"
            >
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
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
            <Link
              :href="route('student.courses.create')"
              class="inline-flex items-center px-4 py-2.5 text-sm font-semibold rounded-xl text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.03] active:scale-[0.98] group"
            >
              <PlusIcon class="h-4 w-4 mr-2 transform group-hover:scale-110 transition-transform duration-300" />
              New Course
            </Link>
            <Link
              :href="route('student.chat.create')"
              class="inline-flex items-center px-4 py-2.5 text-sm font-semibold rounded-xl text-emerald-700 bg-white border border-emerald-200/60 hover:border-emerald-300 hover:bg-emerald-50/80 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.03] active:scale-[0.98] group"
            >
              <ChatBubbleLeftRightIcon class="h-4 w-4 mr-2 transform group-hover:scale-110 transition-transform duration-300" />
              Ask AI Tutor
            </Link>
          </div>

          <!-- Right-side controls -->
          <div class="flex items-center space-x-2">
            <!-- Notifications -->
            <button class="relative p-2.5 text-gray-500 hover:text-emerald-600 hover:bg-emerald-50/80 rounded-xl transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 transform hover:scale-105 group">
              <BellIcon class="h-5 w-5 transform group-hover:shake-animation" />
              <span class="absolute -top-0.5 -right-0.5 w-3 h-3 bg-red-500 rounded-full border-2 border-white shadow-sm"></span>
            </button>

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
      <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 bg-transparent">
        <div class="max-w-7xl mx-auto" :class="{ 'blur-sm pointer-events-none select-none': loading }">
          <div class="animate-fade-in-up">
            <slot />
          </div>
        </div>
      </main>
    </div>
  </div>
  <PageLoader :visible="loading" :message="loaderMessage" />
</template>

<script setup>
import { ref, onMounted,watch } from 'vue'
import { router,usePage } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import NavLink from '@/Components/NavLink.vue'
import PageLoader from "@/Components/PageLoader.vue"
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
} from '@heroicons/vue/24/outline'

const loading = ref(false)
const loaderMessage = ref('')

const showingNavigationDropdown = ref(false)
const isSidebarCollapsed = ref(false)

onMounted(() => {
  const saved = localStorage.getItem('student_sidebar_collapsed')
  if (saved !== null) isSidebarCollapsed.value = JSON.parse(saved)
})

const toggleSidebar = () => {
  isSidebarCollapsed.value = !isSidebarCollapsed.value
  localStorage.setItem('student_sidebar_collapsed', isSidebarCollapsed.value)
}

const navigation = [
  { name: 'Dashboard', href: route('student.dashboard'), active: 'student.dashboard', icon: HomeIcon },
  { name: 'My Courses', href: route('student.courses.index'), active: 'student.courses.*', icon: BookOpenIcon },
  { name: 'Quizzes', href: route('student.quizzes.index'), active: 'student.quizzes.*', icon: ClipboardDocumentListIcon },
  { name: 'AI Tutor', href: route('student.chat.index'), active: 'student.chat.*', icon: ChatBubbleLeftRightIcon },
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

watch(
  () => usePage().props.flash,
  (flash) => {
    if (flash.error || flash.success) {
      setTimeout(() => {
        usePage().props.flash.error = null
        usePage().props.flash.success = null
      }, 5000)
    }
  },
  { deep: true, immediate: true }
)
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
</style>
