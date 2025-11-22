<template>
  <!-- Mobile Bottom Navigation -->
  <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 md:hidden z-50 shadow-lg">
    <div class="flex justify-around items-center h-18">
      <!-- Dashboard -->
      <Link
        :href="route('student.dashboard')"
        class="flex flex-col items-center justify-center w-16 h-16 transition-all duration-200 group"
        :class="route().current('student.dashboard') ? 'text-emerald-600 scale-105' : 'text-gray-600 hover:text-emerald-500'"
      >
        <div class="relative">
          <HomeIcon class="h-6 w-6 transition-transform duration-200 group-hover:scale-110" />
          <div
            v-if="route().current('student.dashboard')"
            class="absolute -top-1 left-1/2 transform -translate-x-1/2 w-2 h-2 bg-emerald-500 rounded-full animate-pulse"
          ></div>
        </div>
        <span class="text-xs mt-1 font-medium" :class="route().current('student.dashboard') ? 'text-emerald-600' : 'text-gray-600'">Home</span>
      </Link>

      <!-- Courses -->
      <Link
        :href="route('student.courses.index')"
        class="flex flex-col items-center justify-center w-16 h-16 transition-all duration-200 group"
        :class="route().current('student.courses.*') ? 'text-emerald-600 scale-105' : 'text-gray-600 hover:text-emerald-500'"
      >
        <div class="relative">
          <BookOpenIcon class="h-6 w-6 transition-transform duration-200 group-hover:scale-110" />
          <div
            v-if="route().current('student.courses.*')"
            class="absolute -top-1 left-1/2 transform -translate-x-1/2 w-2 h-2 bg-emerald-500 rounded-full animate-pulse"
          ></div>
        </div>
        <span class="text-xs mt-1 font-medium" :class="route().current('student.courses.*') ? 'text-emerald-600' : 'text-gray-600'">Courses</span>
      </Link>

      <!-- AI Tutor -->
      <Link
        :href="route('student.chat.index')"
        class="flex flex-col items-center justify-center w-16 h-16 transition-all duration-200 group"
        :class="route().current('student.chat.index') ? 'text-emerald-600 scale-105' : 'text-gray-600 hover:text-emerald-500'"
      >
        <div class="relative">
          <ChatBubbleLeftRightIcon class="h-6 w-6 transition-transform duration-200 group-hover:scale-110" />
          <div
            v-if="route().current('student.chat.index')"
            class="absolute -top-1 left-1/2 transform -translate-x-1/2 w-2 h-2 bg-emerald-500 rounded-full animate-pulse"
          ></div>
        </div>
        <span class="text-xs mt-1 font-medium" :class="route().current('student.chat.index') ? 'text-emerald-600' : 'text-gray-600'">AI Tutor</span>
      </Link>

      <!-- Progress -->
      <Link
        :href="route('student.quizzes.index')"
        class="flex flex-col items-center justify-center w-16 h-16 transition-all duration-200 group"
        :class="route().current('student.quizzes.index') ? 'text-emerald-600 scale-105' : 'text-gray-600 hover:text-emerald-500'"
      >
        <div class="relative">
          <ChartBarIcon class="h-6 w-6 transition-transform duration-200 group-hover:scale-110" />
          <div
            v-if="route().current('student.quizzes.index')"
            class="absolute -top-1 left-1/2 transform -translate-x-1/2 w-2 h-2 bg-emerald-500 rounded-full animate-pulse"
          ></div>
        </div>
        <span class="text-xs mt-1 font-medium" :class="route().current('student.quizzes.index') ? 'text-emerald-600' : 'text-gray-600'">Quizzes</span>
      </Link>

      <!-- Profile -->
      <Link
        :href="route('student.profile.show')"
        class="flex flex-col items-center justify-center w-16 h-16 transition-all duration-200 group"
        :class="route().current('student.profile.show') ? 'text-emerald-600 scale-105' : 'text-gray-600 hover:text-emerald-500'"
      >
        <div class="relative">
          <UserIcon class="h-6 w-6 transition-transform duration-200 group-hover:scale-110" />
          <div
            v-if="route().current('student.profile.show')"
            class="absolute -top-1 left-1/2 transform -translate-x-1/2 w-2 h-2 bg-emerald-500 rounded-full animate-pulse"
          ></div>
        </div>
        <span class="text-xs mt-1 font-medium" :class="route().current('student.profile.show') ? 'text-emerald-600' : 'text-gray-600'">Profile</span>
      </Link>
    </div>

    <!-- Safe area spacer for iOS -->
    <div class="h-safe-bottom bg-white"></div>
  </nav>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import {
  HomeIcon,
  BookOpenIcon,
  ChatBubbleLeftRightIcon,
  ChartBarIcon,
  UserIcon,
} from '@heroicons/vue/24/outline'
</script>

<style scoped>
/* Safe area for iOS devices */
.h-safe-bottom {
  height: env(safe-area-inset-bottom);
}

/* Enhanced active state */
.router-link-active {
  position: relative;
}

/* Custom active indicator with animation */
.router-link-active::after {
  content: '';
  position: absolute;
  bottom: -1px;
  left: 50%;
  transform: translateX(-50%);
  width: 24px;
  height: 3px;
  background: linear-gradient(to right, #059669, #0d9488);
  border-radius: 2px;
  animation: slideIn 0.3s ease-out;
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

/* Hover effects */
.group:hover .h-6 {
  transform: scale(1.15);
}

/* Ensure proper stacking */
.fixed {
  z-index: 50;
}
</style>
