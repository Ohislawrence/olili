[file name]: GuestLayout.vue
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
                v-model="searchQuery"
                placeholder="Search courses, topics, skills..."
                class="w-full pl-10 pr-4 py-2.5 text-sm rounded-xl border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200 hover:bg-white hover:border-gray-300"
                @keyup.enter="performSearch"
                @input="handleSearchInput"
                @focus="showSuggestions = true"
                @blur="onSearchBlur"
              />
              <button
                @click="performSearch"
                class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-emerald-600 transition-colors p-1.5 rounded-lg hover:bg-emerald-50"
                aria-label="Search"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </button>

              <!-- Search Suggestions Dropdown -->
              <div
                v-if="showSuggestions && (filteredCourses.length > 0 || filteredPopularSearches.length > 0)"
                class="absolute top-full mt-1 w-full bg-white rounded-xl border border-gray-200 shadow-lg py-2 z-50 max-h-96 overflow-y-auto"
              >
                <!-- Courses Section -->
                <div v-if="filteredCourses.length > 0">
                  <div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-100">
                    Courses
                  </div>
                  <div
                    v-for="course in filteredCourses"
                    :key="course.id"
                    @click="goToCourse(course)"
                    @mousedown.prevent
                    class="px-3 py-3 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 cursor-pointer flex items-start group"
                  >
                    <div class="flex-shrink-0 mr-3 mt-0.5">
                      <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                      </div>
                    </div>
                    <div class="flex-1 min-w-0">
                      <div class="font-medium text-gray-900 group-hover:text-emerald-700 truncate">{{ course.title }}</div>
                      <div class="flex items-center mt-1 space-x-2">
                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">{{ course.level }}</span>
                        <span class="text-xs text-gray-500">{{ course.subject }}</span>
                        <span v-if="course.is_free" class="text-xs px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700">Free</span>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Popular Searches Section -->
                <div v-if="filteredPopularSearches.length > 0">
                  <div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider border-t border-gray-100">
                    Popular Searches
                  </div>
                  <div
                    v-for="search in filteredPopularSearches"
                    :key="search"
                    @click="selectPopularSearch(search)"
                    @mousedown.prevent
                    class="px-3 py-2.5 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 cursor-pointer flex items-center group"
                  >
                    <svg class="w-4 h-4 mr-3 text-gray-400 group-hover:text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    {{ search }}
                  </div>
                </div>

                <!-- View All Results -->
                <div
                  v-if="searchQuery.trim() && filteredCourses.length > 0"
                  @click="performSearch"
                  @mousedown.prevent
                  class="px-3 py-3 text-sm font-medium text-emerald-600 hover:bg-emerald-50 cursor-pointer border-t border-gray-100 flex items-center justify-center group"
                >
                  View all results for "{{ searchQuery }}"
                  <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                  </svg>
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

            <!-- Desktop Auth Buttons / User Menu -->
            <div class="hidden lg:flex items-center space-x-4">
              <!-- Guest State: Show Sign In/Register buttons -->
              <template v-if="!$page.props.auth.user">
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
              </template>

              <!-- Authenticated State: Show notification and profile dropdown -->
              <template v-else>
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
              </template>
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
              v-model="searchQuery"
              placeholder="Search courses..."
              class="w-full pl-10 pr-12 py-3 text-sm rounded-xl border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"
              @keyup.enter="performSearch"
              @input="handleSearchInput"
              @focus="showSuggestions = true"
              @blur="onSearchBlur"
            />
            <button
              @click="performSearch"
              class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-emerald-600 transition-colors"
              aria-label="Search"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </button>
          </div>

          <!-- Mobile Search Suggestions -->
          <div
            v-if="showSuggestions && (filteredCourses.length > 0 || filteredPopularSearches.length > 0)"
            class="mt-4 bg-white rounded-xl border border-gray-200 shadow-lg py-2 max-h-80 overflow-y-auto"
          >
            <!-- Courses Section -->
            <div v-if="filteredCourses.length > 0">
              <div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-100">
                Courses
              </div>
              <div
                v-for="course in filteredCourses"
                :key="course.id"
                @click="goToCourse(course)"
                @mousedown.prevent
                class="px-3 py-3 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 cursor-pointer flex items-start group"
              >
                <div class="flex-shrink-0 mr-3 mt-0.5">
                  <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                  </div>
                </div>
                <div class="flex-1 min-w-0">
                  <div class="font-medium text-gray-900 group-hover:text-emerald-700 truncate">{{ course.title }}</div>
                  <div class="flex flex-wrap items-center mt-1 space-x-2">
                    <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 mb-1">{{ course.level }}</span>
                    <span class="text-xs text-gray-500 mb-1">{{ course.subject }}</span>
                    <span v-if="course.is_free" class="text-xs px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 mb-1">Free</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Popular Searches Section -->
            <div v-if="filteredPopularSearches.length > 0">
              <div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider border-t border-gray-100">
                Popular Searches
              </div>
              <div
                v-for="search in filteredPopularSearches"
                :key="search"
                @click="selectPopularSearch(search)"
                @mousedown.prevent
                class="px-3 py-2.5 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 cursor-pointer flex items-center group"
              >
                <svg class="w-4 h-4 mr-3 text-gray-400 group-hover:text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                {{ search }}
              </div>
            </div>

            <!-- View All Results -->
            <div
              v-if="searchQuery.trim() && filteredCourses.length > 0"
              @click="performSearch"
              @mousedown.prevent
              class="px-3 py-3 text-sm font-medium text-emerald-600 hover:bg-emerald-50 cursor-pointer border-t border-gray-100 flex items-center justify-center group"
            >
              View all results for "{{ searchQuery }}"
              <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
              </svg>
            </div>
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
            <!-- Mobile Auth Links (only show for guests) -->
            <template v-if="!$page.props.auth.user">
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
            </template>

            <!-- Mobile Authenticated Menu (only show for logged in users) -->
            <template v-else>
              <div class="space-y-1">
                <Link
                  :href="route('student.notifications.index')"
                  @click="showMobileMenu = false"
                  class="block px-3 py-3 rounded-lg text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-colors flex items-center"
                >
                  <BellIcon class="h-5 w-5 mr-3" />
                  Notifications
                  <span
                    v-if="unreadNotificationCount > 0"
                    class="ml-2 w-3 h-3 bg-red-500 rounded-full"
                  ></span>
                </Link>
                <Link
                  :href="route('student.profile.show')"
                  @click="showMobileMenu = false"
                  class="block px-3 py-3 rounded-lg text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-colors flex items-center"
                >
                  <UserCircleIcon class="h-5 w-5 mr-3" />
                  Profile
                </Link>
                <Link
                  :href="route('payment.pricing')"
                  @click="showMobileMenu = false"
                  class="block px-3 py-3 rounded-lg text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-colors flex items-center"
                >
                  <CreditCardIcon class="h-5 w-5 mr-3" />
                  Billing
                </Link>
                <form @submit.prevent="logout" class="w-full">
                  <button
                    type="submit"
                    @click="showMobileMenu = false"
                    class="block w-full text-left px-3 py-3 rounded-lg text-base font-medium text-gray-700 hover:bg-red-50 hover:text-red-700 transition-colors flex items-center"
                  >
                    <ArrowRightOnRectangleIcon class="h-5 w-5 mr-3" />
                    Log Out
                  </button>
                </form>
              </div>
            </template>
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
import { ref, computed, onMounted, onUnmounted, h, watch } from 'vue';
import { debounce } from 'lodash-es';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { BellIcon, UserCircleIcon, CreditCardIcon, ArrowRightOnRectangleIcon } from '@heroicons/vue/24/outline';

// Search state
const searchQuery = ref('');
const showSuggestions = ref(false);
const isSearching = ref(false);

// Course data
const courses = ref([]);
const popularSearches = ref([
  'Python Programming',
  'Data Science',
  'Web Development',
  'Machine Learning',
  'Digital Marketing',
  'Business Analytics',
  'Artificial Intelligence',
  'Graphic Design',
  'JavaScript',
  'React',
  'Vue.js',
  'Laravel',
  'Python Django',
  'Mobile Development',
  'UI/UX Design',
  'Cloud Computing',
  'Cybersecurity',
  'Blockchain',
  'DevOps',
  'SQL Database'
]);

// Filtered results
const filteredCourses = computed(() => {
  if (!searchQuery.value.trim()) {
    // Show top courses when empty
    return courses.value.slice(0, 5);
  }

  const query = searchQuery.value.toLowerCase().trim();
  return courses.value.filter(course => {
    return (
      course.title.toLowerCase().includes(query) ||
      course.description?.toLowerCase().includes(query) ||
      course.subject?.toLowerCase().includes(query) ||
      course.tags?.some(tag => tag.toLowerCase().includes(query))
    );
  }).slice(0, 5);
});

const filteredPopularSearches = computed(() => {
  if (!searchQuery.value.trim()) {
    return popularSearches.value.slice(0, 4);
  }

  const query = searchQuery.value.toLowerCase().trim();
  return popularSearches.value.filter(search =>
    search.toLowerCase().includes(query)
  ).slice(0, 4);
});

// Search functions
const handleSearchInput = debounce(async () => {
  if (searchQuery.value.trim().length >= 2) {
    isSearching.value = true;
    try {
      // Fetch courses from API
      await fetchCourses(searchQuery.value);
    } catch (error) {
      console.error('Search error:', error);
    } finally {
      isSearching.value = false;
    }
  }
}, 300);

const performSearch = () => {
  if (searchQuery.value.trim()) {
    router.visit(route('courses.index', {
      search: searchQuery.value.trim(),
      q: searchQuery.value.trim()
    }), {
      preserveScroll: false,
      preserveState: false
    });
    showSuggestions.value = false;
    showMobileSearch.value = false;
  } else {
    router.visit(route('courses.index'));
  }
};

const selectPopularSearch = (searchTerm) => {
  searchQuery.value = searchTerm;
  performSearch();
};

const goToCourse = (course) => {
  router.visit(
  route('courses.show', {
    id: course.id,
    slug: course.slug
  }),
  {
    preserveScroll: false,
    preserveState: false
  }
);
  showSuggestions.value = false;
  showMobileSearch.value = false;
};

const onSearchBlur = () => {
  setTimeout(() => {
    showSuggestions.value = false;
  }, 200);
};

// Fetch courses from API
const fetchCourses = async (query = '') => {
  try {
    const response = await fetch(route('api.courses.search', {
      search: query,
      limit: 10,
      public_only: true
    }));

    if (response.ok) {
      const data = await response.json();
      courses.value = data.courses || data.data || [];
    }
  } catch (error) {
    console.error('Failed to fetch courses:', error);
    // Fallback to sample data if API fails
    courses.value = getSampleCourses();
  }
};

// Sample courses for demonstration
const getSampleCourses = () => [
  {
    id: 1,
    title: 'Complete Python Bootcamp: From Zero to Hero',
    slug: 'complete-python-bootcamp',
    description: 'Learn Python like a Professional! Start from the basics and go all the way to creating your own applications.',
    subject: 'Programming',
    level: 'Beginner',
    is_free: true,
    tags: ['python', 'programming', 'beginner']
  },
  {
    id: 2,
    title: 'Data Science and Machine Learning Bootcamp',
    slug: 'data-science-ml-bootcamp',
    description: 'Complete Data Science Training: Mathematics, Statistics, Python, Advanced Statistics in Python, Machine & Deep Learning',
    subject: 'Data Science',
    level: 'Intermediate',
    is_free: false,
    tags: ['data-science', 'machine-learning', 'python']
  },
  {
    id: 3,
    title: 'The Complete Web Development Bootcamp',
    slug: 'complete-web-development',
    description: 'Become a full-stack web developer with just one course. HTML, CSS, Javascript, Node, React, MongoDB and more!',
    subject: 'Web Development',
    level: 'Beginner',
    is_free: true,
    tags: ['web-development', 'javascript', 'react', 'node']
  },
  {
    id: 4,
    title: 'Machine Learning A-Z: Hands-On Python',
    slug: 'machine-learning-hands-on',
    description: 'Learn to create Machine Learning Algorithms in Python from two Data Science experts.',
    subject: 'Machine Learning',
    level: 'Advanced',
    is_free: false,
    tags: ['machine-learning', 'python', 'ai']
  },
  {
    id: 5,
    title: 'Digital Marketing Masterclass',
    slug: 'digital-marketing-masterclass',
    description: 'Get a job in digital marketing, become an influencer, or grow your business. Ultimate digital marketing course.',
    subject: 'Marketing',
    level: 'Beginner',
    is_free: true,
    tags: ['marketing', 'digital-marketing', 'business']
  }
];

// Mobile menu state
const showMobileSearch = ref(false);
const showMobileMenu = ref(false);

const toggleMobileSearch = () => {
  showMobileSearch.value = !showMobileSearch.value;
  showMobileMenu.value = false;
  if (showMobileSearch.value) {
    // Focus the search input when opened on mobile
    setTimeout(() => {
      const input = document.querySelector('.lg:hidden input[type="text"]');
      if (input) input.focus();
    }, 100);
  }
};

const toggleMobileMenu = () => {
  showMobileMenu.value = !showMobileMenu.value;
  showMobileSearch.value = false;
};

// Current year for copyright
const currentYear = new Date().getFullYear();

// Navigation items (unchanged)
const navItems = [
  { name: 'Home', route: route('welcome'), component: 'frontpages/Welcome' },
  { name: 'Features', route: route('features'), component: 'frontpages/Features' },
  { name: 'Pricing', route: route('pricing'), component: 'frontpages/Pricing' },
];

// Footer links (unchanged)
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

// Social media links (unchanged)
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

// Newsletter (unchanged)
const newsletterEmail = ref('');
const newsletterLoading = ref(false);

const subscribeNewsletter = async () => {
  if (!newsletterEmail.value.trim()) return;

  newsletterLoading.value = true;
  try {
    await new Promise(resolve => setTimeout(resolve, 1500));
    console.log('Subscribed:', newsletterEmail.value);
    alert('Thank you for subscribing to our newsletter!');
    newsletterEmail.value = '';
  } catch (error) {
    alert('Something went wrong. Please try again.');
  } finally {
    newsletterLoading.value = false;
  }
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

// Unread notification count
const unreadNotificationCount = ref(0);

// Logout function
const logout = () => {
  router.post(route('logout'));
};

// Lifecycle hooks
onMounted(() => {
  window.addEventListener('scroll', checkScrollPosition);
  checkScrollPosition();

  // Load initial courses
  fetchCourses();

  // Fetch unread notification count if user is authenticated
  if ($page.props.auth.user) {
    // Implement API call for notifications
  }
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

@keyframes shake {
  0%, 100% { transform: rotate(0deg); }
  25% { transform: rotate(-5deg); }
  75% { transform: rotate(5deg); }
}

.animate-slide-down {
  animation: slide-down 0.3s ease-out;
}

.shake-animation {
  animation: shake 0.5s ease-in-out infinite;
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

/* Search suggestions custom scrollbar */
.search-suggestions::-webkit-scrollbar {
  width: 6px;
}

.search-suggestions::-webkit-scrollbar-track {
  background: #f8fafc;
  border-radius: 3px;
}

.search-suggestions::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
}

.search-suggestions::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
</style>
