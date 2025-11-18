<!-- resources/js/Layouts/AppLayout.vue -->
<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <!-- Logo and main navigation -->
          <div class="flex">
            <div class="flex-shrink-0 flex items-center">
              <Link :href="route('dashboard')">
                <ApplicationLogo class="block h-8 w-auto text-gray-800" />
              </Link>
            </div>

            <!-- Navigation Links -->
            <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
              <NavLink
                v-for="item in navigation"
                :key="item.name"
                :href="item.href"
                :active="route().current(item.active)"
                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
              >
                {{ item.name }}
              </NavLink>
            </div>
          </div>

          <!-- Right navigation -->
          <div class="hidden sm:ml-6 sm:flex sm:items-center">
            <!-- Notifications -->
            <button class="bg-white p-1 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
              <BellIcon class="h-6 w-6" />
            </button>

            <!-- Profile dropdown -->
            <div class="ml-3 relative">
              <Dropdown align="right" width="48">
                <template #trigger>
                  <button class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <img class="h-8 w-8 rounded-full" :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name" />
                  </button>
                </template>

                <template #content>
                  <!-- Account Management -->
                  <div class="block px-4 py-2 text-xs text-gray-400">
                    Manage Account
                  </div>

                  <DropdownLink :href="route('profile.show')">
                    Profile
                  </DropdownLink>

                  <DropdownLink v-if="$page.props.jetstream.hasApiFeatures" :href="route('api-tokens.index')">
                    API Tokens
                  </DropdownLink>

                  <div class="border-t border-gray-100" />

                  <!-- Authentication -->
                  <form @submit.prevent="logout">
                    <DropdownLink as="button">
                      Log Out
                    </DropdownLink>
                  </form>
                </template>
              </Dropdown>
            </div>
          </div>

          <!-- Mobile menu button -->
          <div class="sm:hidden flex items-center">
            <button @click="showingNavigationDropdown = !showingNavigationDropdown" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
              <Bars3Icon v-show="!showingNavigationDropdown" class="h-6 w-6" />
              <XMarkIcon v-show="showingNavigationDropdown" class="h-6 w-6" />
            </button>
          </div>
        </div>
      </div>

      <!-- Mobile menu -->
      <div v-show="showingNavigationDropdown" class="sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
          <ResponsiveNavLink
            v-for="item in navigation"
            :key="item.name"
            :href="item.href"
            :active="route().current(item.active)"
          >
            {{ item.name }}
          </ResponsiveNavLink>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
          <div class="flex items-center px-4">
            <div class="flex-shrink-0">
              <img class="h-10 w-10 rounded-full" :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name" />
            </div>
            <div class="ml-3">
              <div class="text-base font-medium text-gray-800">{{ $page.props.auth.user.name }}</div>
              <div class="text-sm font-medium text-gray-500">{{ $page.props.auth.user.email }}</div>
            </div>
          </div>

          <div class="mt-3 space-y-1">
            <ResponsiveNavLink :href="route('profile.show')" :active="route().current('profile.show')">
              Profile
            </ResponsiveNavLink>

            <ResponsiveNavLink v-if="$page.props.jetstream.hasApiFeatures" :href="route('api-tokens.index')" :active="route().current('api-tokens.index')">
              API Tokens
            </ResponsiveNavLink>

            <!-- Authentication -->
            <form method="POST" @submit.prevent="logout">
              <ResponsiveNavLink as="button">
                Log Out
              </ResponsiveNavLink>
            </form>
          </div>
        </div>
      </div>
    </nav>

    <!-- Page Heading -->
    <header v-if="$slots.header" class="bg-white shadow">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <slot name="header" />
      </div>
    </header>

    <!-- Page Content -->
    <main>
      <slot />
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="md:flex md:items-center md:justify-between">
          <div class="flex justify-center space-x-6 md:order-2">
            <a href="#" class="text-gray-400 hover:text-gray-500">
              <span class="sr-only">Facebook</span>
              <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
              </svg>
            </a>
            <a href="#" class="text-gray-400 hover:text-gray-500">
              <span class="sr-only">Twitter</span>
              <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
              </svg>
            </a>
          </div>
          <div class="mt-8 md:mt-0 md:order-1">
            <p class="text-center text-sm text-gray-500">
              &copy; 2024 AI Tutor. All rights reserved.
            </p>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import NavLink from '@/Components/NavLink.vue'
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue'
import { BellIcon, Bars3Icon, XMarkIcon } from '@heroicons/vue/24/outline'

const showingNavigationDropdown = ref(false)

const navigation = [
  { name: 'Courses', href: '/courses', active: 'courses.*' },
  { name: 'Quizzes', href: '/quizzes', active: 'quizzes.*' },
  { name: 'AI Tutor', href: '/chat', active: 'chat.*' },
  { name: 'Pricing', href: '/pricing', active: 'pricing.*' },
]

const logout = () => {
  Inertia.post(route('logout'))
}
</script>
