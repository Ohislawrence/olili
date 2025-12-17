<template>
  <!-- Mobile Bottom Navigation -->
  <nav class="fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-xl border-t border-gray-200/60 md:hidden z-40 shadow-lg safe-area-inset-bottom">
    <div class="flex justify-around items-center h-16 px-2">
      <!-- Dashboard -->
      <Link
        :href="route('student.dashboard')"
        class="flex flex-col items-center justify-center w-16 h-16 transition-all duration-200 group relative"
        :class="route().current('student.dashboard') ? 'text-emerald-600' : 'text-gray-600 hover:text-emerald-500'"
      >
        <div class="relative">
          <HomeIcon class="h-5 w-5 transition-transform duration-200 group-hover:scale-110" />
          <div
            v-if="route().current('student.dashboard')"
            class="absolute -top-1 -right-1 w-2 h-2 bg-emerald-500 rounded-full animate-pulse"
          ></div>
        </div>
        <span class="text-xs mt-1 font-medium transition-colors duration-200"
              :class="route().current('student.dashboard') ? 'text-emerald-600' : 'text-gray-500 group-hover:text-emerald-600'">
          Home
        </span>
        <div
          v-if="route().current('student.dashboard')"
          class="absolute -top-1 left-1/2 transform -translate-x-1/2 w-6 h-1 bg-emerald-500 rounded-full"
        ></div>
      </Link>

      <!-- Courses -->
      <Link
        :href="route('student.courses.index')"
        class="flex flex-col items-center justify-center w-16 h-16 transition-all duration-200 group relative"
        :class="route().current('student.courses.*') ? 'text-emerald-600' : 'text-gray-600 hover:text-emerald-500'"
      >
        <div class="relative">
          <BookOpenIcon class="h-5 w-5 transition-transform duration-200 group-hover:scale-110" />
          <div
            v-if="route().current('student.courses.*')"
            class="absolute -top-1 -right-1 w-2 h-2 bg-emerald-500 rounded-full animate-pulse"
          ></div>
        </div>
        <span class="text-xs mt-1 font-medium transition-colors duration-200"
              :class="route().current('student.courses.*') ? 'text-emerald-600' : 'text-gray-500 group-hover:text-emerald-600'">
          Courses
        </span>
        <div
          v-if="route().current('student.courses.*')"
          class="absolute -top-1 left-1/2 transform -translate-x-1/2 w-6 h-1 bg-emerald-500 rounded-full"
        ></div>
      </Link>



      <!-- catalog -->
      <Link
        :href="route('student.catalog.index')"
        class="flex flex-col items-center justify-center w-16 h-16 transition-all duration-200 group relative"
        :class="route().current('student.catalog.*') ? 'text-emerald-600' : 'text-gray-600 hover:text-emerald-500'"
      >
        <div class="relative">
          <ClipboardDocumentListIcon class="h-5 w-5 transition-transform duration-200 group-hover:scale-110" />
          <div
            v-if="route().current('student.catalog.*')"
            class="absolute -top-1 -right-1 w-2 h-2 bg-emerald-500 rounded-full animate-pulse"
          ></div>
        </div>
        <span class="text-xs mt-1 font-medium transition-colors duration-200"
              :class="route().current('student.catalog.*') ? 'text-emerald-600' : 'text-gray-500 group-hover:text-emerald-600'">
          Catalog
        </span>
        <div
          v-if="route().current('student.catalog.*')"
          class="absolute -top-1 left-1/2 transform -translate-x-1/2 w-6 h-1 bg-emerald-500 rounded-full"
        ></div>
      </Link>

      <!-- Flash Card -->
      <Link
        :href="route('student.flashcards.index')"
        class="flex flex-col items-center justify-center w-16 h-16 transition-all duration-200 group relative"
        :class="route().current('student.flashcards.*') ? 'text-emerald-600' : 'text-gray-600 hover:text-emerald-500'"
      >
        <div class="relative">
          <RectangleStackIcon class="h-5 w-5 transition-transform duration-200 group-hover:scale-110" />
          <div
            v-if="route().current('student.flashcards.*')"
            class="absolute -top-1 -right-1 w-2 h-2 bg-emerald-500 rounded-full animate-pulse"
          ></div>
        </div>
        <span class="text-xs mt-1 font-medium transition-colors duration-200"
              :class="route().current('student.flashcards.*') ? 'text-emerald-600' : 'text-gray-500 group-hover:text-emerald-600'">
          Flash Card
        </span>
        <div
          v-if="route().current('student.flashcards.*')"
          class="absolute -top-1 left-1/2 transform -translate-x-1/2 w-6 h-1 bg-emerald-500 rounded-full"
        ></div>
      </Link>

      <!-- Profile -->
      <Link
        :href="route('student.profile.show')"
        class="flex flex-col items-center justify-center w-16 h-16 transition-all duration-200 group relative"
        :class="route().current('student.profile.*') ? 'text-emerald-600' : 'text-gray-600 hover:text-emerald-500'"
      >
        <div class="relative">
          <UserIcon class="h-5 w-5 transition-transform duration-200 group-hover:scale-110" />
          <div
            v-if="route().current('student.profile.*')"
            class="absolute -top-1 -right-1 w-2 h-2 bg-emerald-500 rounded-full animate-pulse"
          ></div>
        </div>
        <span class="text-xs mt-1 font-medium transition-colors duration-200"
              :class="route().current('student.profile.*') ? 'text-emerald-600' : 'text-gray-500 group-hover:text-emerald-600'">
          Profile
        </span>
        <div
          v-if="route().current('student.profile.*')"
          class="absolute -top-1 left-1/2 transform -translate-x-1/2 w-6 h-1 bg-emerald-500 rounded-full"
        ></div>
      </Link>
    </div>
  </nav>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import {
  HomeIcon,
  BookOpenIcon,
  ChatBubbleLeftRightIcon,
  ClipboardDocumentListIcon,
  RectangleStackIcon,
  UserIcon,
} from '@heroicons/vue/24/outline'
</script>

<style scoped>
/* Safe area for iOS devices */
.safe-area-inset-bottom {
  padding-bottom: env(safe-area-inset-bottom);
}

/* Enhanced hover effects */
.group:hover .h-5 {
  transform: scale(1.1);
}

/* Smooth transitions for all interactive elements */
* {
  transition-property: color, background-color, border-color, transform, opacity;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 200ms;
}

/* Active state animation */
.router-link-active {
  position: relative;
}

/* Improved active indicator */
.router-link-active::before {
  content: '';
  position: absolute;
  top: -2px;
  left: 50%;
  transform: translateX(-50%);
  width: 24px;
  height: 2px;
  background: linear-gradient(to right, #10b981, #059669);
  border-radius: 1px;
  animation: slideIn 0.2s ease-out;
}

@keyframes slideIn {
  from {
    width: 0;
    opacity: 0;
  }
  to {
    width: 24px;
    opacity: 1;
  }
}

/* Backdrop blur support */
@supports (backdrop-filter: blur(12px)) {
  nav {
    backdrop-filter: blur(12px);
    background-color: rgba(255, 255, 255, 0.85);
  }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
  * {
    transition-duration: 0.01ms !important;
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
  }
}

/* Better touch targets for mobile */
@media (max-width: 768px) {
  .h-16 {
    min-height: 4rem;
  }

  .w-16 {
    min-width: 4rem;
  }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
  nav {
    background-color: rgba(15, 23, 42, 0.95);
    border-top-color: rgba(255, 255, 255, 0.1);
  }

  .text-gray-600 {
    color: rgba(255, 255, 255, 0.7);
  }

  .text-gray-500 {
    color: rgba(255, 255, 255, 0.6);
  }
}
</style>
