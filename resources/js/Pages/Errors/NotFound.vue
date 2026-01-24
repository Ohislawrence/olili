<template>
  <GuestLayout>
    <Head title="404 - Page Not Found" />

    <div class="min-h-screen flex flex-col items-center justify-center p-6">
      <div class="w-full max-w-md text-center">
        <!-- Logo -->
        <div class="flex justify-center mb-8">
          <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center">
              <AcademicCapIcon class="w-8 h-8 text-emerald-600" />
            </div>
            <span class="text-2xl font-bold text-gray-900">Olilearn</span>
          </div>
        </div>

        <!-- Error Content -->
        <div class="bg-white rounded-2xl shadow-2xl p-8">
          <div class="w-20 h-20 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <AcademicCapIcon class="w-12 h-12 text-emerald-600" />
          </div>

          <h1 class="text-6xl font-bold text-gray-800 mb-2">404</h1>
          <h2 class="text-2xl font-semibold text-gray-700 mb-4">Page Not Found</h2>

          <p class="text-gray-600 mb-8">
            Oops! The page you're looking for doesn't exist.
          </p>

          <div class="space-y-4">
            <Link
              :href="route('dashboard')"
              class="block w-full py-3 px-6 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition duration-200"
            >
              Go to Dashboard
            </Link>
            <Link
              :href="route('home')"
              class="block w-full py-3 px-6 border-2 border-emerald-500 text-emerald-600 font-semibold rounded-lg hover:bg-emerald-50 transition duration-200"
            >
              Back to Home
            </Link>
          </div>
        </div>
      </div>
    </div>
  </GuestLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { AcademicCapIcon } from '@heroicons/vue/24/outline'
</script>
