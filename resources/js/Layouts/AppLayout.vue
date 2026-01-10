<template>
  <div class="min-h-screen bg-gray-50 flex flex-col">

    <!-- HEADER -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-50">
      <div class="max-w-[1200px] mx-auto px-4">
        <div class="h-16 flex items-center justify-between">

          <!-- Left -->
          <div class="flex items-center gap-8">
            <Link :href="route('welcome')" class="flex items-center gap-2">
              <div class="w-9 h-9 bg-emerald-600 rounded flex items-center justify-center">
                <span class="text-white text-sm font-semibold">OL</span>
              </div>
              <span class="text-lg font-semibold tracking-tight text-gray-900">
                OliLearn
              </span>
            </Link>

            <!-- Desktop Nav -->
            <nav class="hidden md:flex items-center gap-6">
              <NavLink
                v-for="item in navigation"
                :key="item.name"
                :href="item.href"
                :active="route().current(item.active)"
                class="relative text-sm font-medium text-gray-600 hover:text-gray-900"
              >
                {{ item.name }}
                <span
                  v-if="route().current(item.active)"
                  class="absolute -bottom-5 left-0 w-full h-0.5 bg-emerald-600"
                />
              </NavLink>
            </nav>
          </div>

          <!-- Right -->
          <div class="flex items-center gap-4">

            <!-- Search -->
            <div class="hidden md:block relative">
              <input
                type="text"
                placeholder="Search"
                class="w-64 rounded-full border border-gray-300 pl-10 pr-4 py-1.5 text-sm focus:border-emerald-500 focus:ring-emerald-500"
              />
              <svg class="absolute left-3 top-2.5 h-4 w-4 text-gray-400"
                   viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                      d="M21 21l-4.35-4.35m1.85-5.65a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
            </div>

            <!-- Auth -->
            <template v-if="$page.props.auth.user">
              <div class="relative">
                <button
                  @click="showProfileDropdown = !showProfileDropdown"
                  class="flex items-center gap-2"
                >
                  <img
                    class="h-8 w-8 rounded-full border border-gray-200"
                    :src="$page.props.auth.user.profile_photo_url"
                    :alt="$page.props.auth.user.name"
                  />
                  <span class="hidden md:block text-sm font-medium text-gray-700">
                    {{ $page.props.auth.user.name }}
                  </span>
                </button>

                <!-- Dropdown -->
                <div
                  v-if="showProfileDropdown"
                  class="absolute right-0 mt-2 w-52 rounded-md bg-white border border-gray-200 shadow-lg py-1"
                >
                  <Link :href="route('dashboard')" class="dropdown-item">
                    Dashboard
                  </Link>
                  <Link :href="route('my-courses')" class="dropdown-item">
                    My Courses
                  </Link>
                  <Link :href="route('profile.show')" class="dropdown-item">
                    Profile
                  </Link>
                  <div class="border-t my-1"></div>
                  <form @submit.prevent="logout">
                    <button class="dropdown-item w-full text-left">
                      Log out
                    </button>
                  </form>
                </div>
              </div>
            </template>

            <template v-else>
              <Link
                :href="route('login')"
                class="text-sm font-medium text-gray-700 hover:text-gray-900"
              >
                Log in
              </Link>
              <Link
                :href="route('register')"
                class="rounded-full bg-emerald-600 px-4 py-1.5 text-sm font-medium text-white hover:bg-emerald-700"
              >
                Join for Free
              </Link>
            </template>

            <!-- Mobile -->
            <button
              @click="showMobileMenu = !showMobileMenu"
              class="md:hidden p-2"
            >
              <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path v-if="!showMobileMenu" stroke-width="2"
                      d="M4 6h16M4 12h16M4 18h16"/>
                <path v-else stroke-width="2"
                      d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>


      </div>
    </header>

    <!-- MAIN -->
    <main class="flex-1">
      <div class="max-w-[1200px] mx-auto px-4 py-8">
        <slot />
      </div>
    </main>

    <!-- FOOTER -->
    <footer class="bg-gray-100 border-t">
      <div class="max-w-[1200px] mx-auto px-4 py-10">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 text-sm text-gray-600">

          <div>
            <div class="flex items-center gap-2 mb-3">
              <div class="w-8 h-8 bg-emerald-600 rounded flex items-center justify-center">
                <span class="text-white text-xs font-semibold">OL</span>
              </div>
              <span class="font-semibold text-gray-800">OliLearn</span>
            </div>
            <p class="text-gray-500">
              AI-powered learning for modern education.
            </p>
          </div>

          <div>
            <h4 class="font-medium text-gray-800 mb-3">Explore</h4>
            <ul class="space-y-2">
              <li><Link class="footer-link" :href="route('courses.index')">Courses</Link></li>
              <li><Link class="footer-link" :href="route('pricing')">Pricing</Link></li>
              <li><Link class="footer-link" :href="route('business')">For Business</Link></li>
            </ul>
          </div>

          <div>
            <h4 class="font-medium text-gray-800 mb-3">Company</h4>
            <ul class="space-y-2">
              <li><Link class="footer-link" :href="route('about')">About</Link></li>
              <li><Link class="footer-link" :href="route('contact')">Contact</Link></li>
            </ul>
          </div>

          <div>
            <h4 class="font-medium text-gray-800 mb-3">Legal</h4>
            <ul class="space-y-2">
              <li><a href="#" class="footer-link">Terms</a></li>
              <li><a href="#" class="footer-link">Privacy</a></li>
            </ul>
          </div>
        </div>

        <div class="mt-8 text-center text-xs text-gray-500">
          © {{ new Date().getFullYear() }} OliLearn. All rights reserved.
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import NavLink from '@/Components/NavLink.vue'

const showProfileDropdown = ref(false)
const showMobileMenu = ref(false)

const navigation = [
  { name: 'Courses', href: '/courses', active: 'courses.*' },
  { name: 'AI Tutor', href: '/chat', active: 'chat.*' },
  { name: 'Pricing', href: '/pricing', active: 'pricing.*' },
  { name: 'For Business', href: '/business', active: 'business.*' },
]

const logout = () => {
  router.post(route('logout'))
}
</script>

<style scoped>
.dropdown-item {
  @apply block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100;
}

.footer-link {
  @apply text-gray-500 hover:text-gray-800;
}
</style>
