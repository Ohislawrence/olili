<template>
  <div class="min-h-screen bg-gray-50 flex">
    <!-- Collapsible Sidebar -->
    <aside
      class="bg-white shadow-sm border-r border-gray-200 flex flex-col transition-all duration-300 ease-in-out fixed md:relative z-40"
      :class="isSidebarCollapsed ? 'w-16' : 'w-64'"
      :style="{ height: '100dvh' }"
    >
      <!-- Logo / Collapse Toggle -->
      <div class="flex items-center justify-between h-16 px-4 border-b border-gray-100">
        <Link
          :href="route('admin.dashboard')"
          class="flex items-center overflow-hidden"
        >
          <img
            src="/logo-olilearn.PNG"
            alt="OliLearn"
            class="h-10 w-auto object-contain flex-shrink-0"
          />
          <span
            class="ml-3 text-sm font-semibold text-gray-700 transition-opacity duration-200"
            :class="{ 'opacity-0 w-0 overflow-hidden': isSidebarCollapsed }"
          >
            Admin
          </span>
        </Link>

        <!-- Collapse Button (hidden on mobile) -->
        <button
          @click="toggleSidebar"
          class="hidden md:block text-gray-500 hover:text-gray-700 focus:outline-none"
        >
          <ChevronDoubleLeftIcon v-if="!isSidebarCollapsed" class="h-5 w-5" />
          <ChevronDoubleRightIcon v-else class="h-5 w-5" />
        </button>
      </div>

      <!-- Navigation -->
      <nav class="flex-1 overflow-y-auto py-4">
        <div class="space-y-1 px-2">
          <NavLink
            v-for="item in navigation"
            :key="item.name"
            :href="item.href"
            :active="route().current(item.active)"
            class="group flex items-center w-full px-3 py-2.5 text-sm font-medium rounded-lg transition-colors duration-150 whitespace-nowrap"
            active-class="bg-emerald-50 text-emerald-700 border-r-2 border-emerald-500"
            inactive-class="text-gray-700 hover:bg-gray-100 hover:text-gray-900"
          >
            <component
              :is="item.icon"
              class="h-5 w-5 flex-shrink-0 transition-colors"
              :class="route().current(item.active) ? 'text-emerald-600' : 'text-gray-500'"
            />
            <span
              class="ml-3 transition-all duration-200 overflow-hidden"
              :class="isSidebarCollapsed ? 'w-0 opacity-0' : 'w-auto opacity-100'"
            >
              {{ item.name }}
            </span>
          </NavLink>
        </div>
      </nav>

      <!-- Footer (optional) -->
      <div
        v-if="!isSidebarCollapsed"
        class="px-4 py-3 border-t border-gray-100 text-xs text-gray-500"
      >
        Admin Panel v1.0
      </div>
    </aside>

    <!-- Mobile Sidebar Overlay -->
    <div
      v-if="showingNavigationDropdown"
      class="fixed inset-0 bg-black bg-opacity-50 z-30 md:hidden"
      @click="showingNavigationDropdown = false"
    ></div>

    <!-- Main Content Area -->
    <div class="flex flex-col flex-1 min-w-0 md:ml-0 transition-all duration-300"
         :class="isSidebarCollapsed ? 'md:ml-16' : 'md:ml-64'">
      <!-- Top Bar -->
      <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-20">
        <div class="flex items-center justify-between h-16 px-4 sm:px-6">
          <!-- Mobile menu button and logo -->
          <div class="flex items-center space-x-3">
            <button
              @click="showingNavigationDropdown = !showingNavigationDropdown"
              class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none"
            >
              <Bars3Icon v-show="!showingNavigationDropdown" class="h-6 w-6" />
              <XMarkIcon v-show="showingNavigationDropdown" class="h-6 w-6" />
            </button>

            <!-- Mobile Logo -->
            <Link
              :href="route('admin.dashboard')"
              class="md:hidden flex items-center"
            >
              <img
                src="/logo-olilearn.PNG"
                alt="OliLearn"
                class="h-8 w-auto object-contain"
              />
            </Link>
          </div>

          <!-- Quick Stats (only visible on larger screens) -->
          <div class="hidden lg:flex space-x-6 text-sm text-gray-600">
            <div class="flex items-center">
              <UsersIcon class="h-4 w-4 mr-1 text-emerald-600" />
              {{ $page.props.stats?.total_students || 0 }} Students
            </div>
            <div class="flex items-center">
              <CurrencyDollarIcon class="h-4 w-4 mr-1 text-emerald-600" />
              ${{ $page.props.stats?.ai_cost_today || '0.00' }} Today
            </div>
          </div>

          <!-- Profile Dropdown -->
          <div class="flex items-center">
            <Dropdown align="right" width="48">
              <template #trigger>
                <button class="flex text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all">
                  <img
                    class="h-8 w-8 rounded-full object-cover border-2 border-transparent hover:border-emerald-300"
                    :src="$page.props.auth.user.profile_photo_url"
                    :alt="$page.props.auth.user.name"
                  />
                </button>
              </template>

              <template #content>
                <div class="block px-4 py-2 text-xs text-gray-400 border-b border-gray-100">
                  {{ $page.props.auth.user.name }}
                </div>
                <DropdownLink :href="route('profile.show')" class="flex items-center">
                  <UserIcon class="h-4 w-4 mr-2" />
                  Profile
                </DropdownLink>
                <DropdownLink :href="route('admin.settings.index')" class="flex items-center">
                  <Cog6ToothIcon class="h-4 w-4 mr-2" />
                  Settings
                </DropdownLink>
                <div class="border-t border-gray-100" />
                <DropdownLink :href="route('student.dashboard')" class="flex items-center">
                  <EyeIcon class="h-4 w-4 mr-2" />
                  Student View
                </DropdownLink>
                <div class="border-t border-gray-100" />
                <form @submit.prevent="logout">
                  <DropdownLink as="button" class="flex items-center w-full text-red-600 hover:text-red-700">
                    <ArrowRightOnRectangleIcon class="h-4 w-4 mr-2" />
                    Log Out
                  </DropdownLink>
                </form>
              </template>
            </Dropdown>
          </div>
        </div>
      </header>

      <!-- Mobile Sidebar -->
      <div
        v-show="showingNavigationDropdown"
        class="md:hidden bg-white shadow-lg border-b border-gray-200 fixed top-16 left-0 right-0 z-30 max-h-[calc(100dvh-4rem)] overflow-y-auto"
        :class="{ 'animate-slide-in': showingNavigationDropdown }"
      >
        <div class="px-2 pt-2 pb-3 space-y-1">
          <ResponsiveNavLink
            v-for="item in navigation"
            :key="item.name"
            :href="item.href"
            :active="route().current(item.active)"
            class="flex items-center px-3 py-3 text-base font-medium rounded-lg mx-2 transition-colors"
            active-class="bg-emerald-50 text-emerald-700 border-r-2 border-emerald-500"
            inactive-class="text-gray-700 hover:bg-gray-100 hover:text-gray-900"
          >
            <component
              :is="item.icon"
              class="h-5 w-5 mr-3 flex-shrink-0"
              :class="route().current(item.active) ? 'text-emerald-600' : 'text-gray-500'"
            />
            {{ item.name }}
          </ResponsiveNavLink>
        </div>
      </div>

      <!-- Page Content -->
      <main class="flex-1 overflow-y-auto p-4 sm:p-6 bg-gray-50 min-h-[calc(100dvh-4rem)]">
        <div class="max-w-full">
          <slot />
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import NavLink from '@/Components/NavLink.vue'
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue'
import {
  HomeIcon,
  UsersIcon,
  BookOpenIcon,
  CpuChipIcon,
  ChartBarIcon,
  ClipboardDocumentListIcon,
  Cog6ToothIcon,
  ArrowRightOnRectangleIcon,
  Bars3Icon,
  XMarkIcon,
  ChevronDoubleLeftIcon,
  ChevronDoubleRightIcon,
  CurrencyDollarIcon,
  UserIcon,
  EyeIcon,
} from '@heroicons/vue/24/outline'

const showingNavigationDropdown = ref(false)
const isSidebarCollapsed = ref(false)

// Close mobile sidebar when clicking on a link
const closeMobileSidebar = () => {
  showingNavigationDropdown.value = false
}

// Persist sidebar state in localStorage
onMounted(() => {
  const saved = localStorage.getItem('admin_sidebar_collapsed')
  if (saved !== null) {
    isSidebarCollapsed.value = JSON.parse(saved)
  }

  // Close mobile sidebar on resize
  const handleResize = () => {
    if (window.innerWidth >= 768) {
      showingNavigationDropdown.value = false
    }
  }

  window.addEventListener('resize', handleResize)
})

const toggleSidebar = () => {
  isSidebarCollapsed.value = !isSidebarCollapsed.value
  localStorage.setItem('admin_sidebar_collapsed', isSidebarCollapsed.value)
}

const navigation = [
  { name: 'Dashboard', href: route('admin.dashboard'), active: 'admin.dashboard', icon: HomeIcon },
  { name: 'Users', href: route('admin.users.index'), active: 'admin.users.*', icon: UsersIcon },
  { name: 'Courses', href: route('admin.courses.index'), active: 'admin.courses.*', icon: BookOpenIcon },
  { name: 'AI Providers', href: route('admin.ai-providers.index'), active: 'admin.ai-providers.*', icon: CpuChipIcon },
  { name: 'Analytics', href: route('admin.ai-analytics.index'), active: 'admin.ai-analytics.*', icon: ChartBarIcon },
  { name: 'Exam Boards', href: route('admin.exam-boards.index'), active: 'admin.exam-boards.*', icon: ClipboardDocumentListIcon },
  { name: 'Settings', href: route('admin.settings.index'), active: 'admin.settings.*', icon: Cog6ToothIcon },
  { name: 'Subscriptions', href: route('admin.subscriptions.index'), active: 'admin.subscriptions.*', icon: Cog6ToothIcon },
  { name: 'Plans', href: route('admin.subscription-plans.index'), active: 'admin.subscription-plans.*', icon: Cog6ToothIcon },
]

const logout = () => {
  router.post(route('logout'))
}
</script>

<style scoped>
/* Smooth animations */
nav svg {
  transition: margin 0.2s ease;
}

/* Mobile sidebar animation */
@keyframes slide-in {
  from {
    transform: translateY(-10px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

.animate-slide-in {
  animation: slide-in 0.2s ease-out;
}

/* Improve scrollbar for mobile */
@media (max-width: 768px) {
  .overflow-y-auto {
    -webkit-overflow-scrolling: touch;
  }
}

/* Ensure proper spacing on mobile */
@media (max-width: 767px) {
  .min-h-\[calc\(100dvh-4rem\)\] {
    min-height: calc(100dvh - 4rem);
  }
}
</style>
