<template>
  <AdminLayout>
    <Head :title="`Flashcards - ${course.title}`" />
    
    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header with back button -->
        <div class="mb-6">
          <div class="flex items-center justify-between">
            <div>
              <Link
                :href="route('admin.courses.show', course.id)"
                class="text-sm text-gray-500 hover:text-gray-700 flex items-center"
              >
                <ArrowLeftIcon class="h-4 w-4 mr-1" />
                Back to Course
              </Link>
              <h1 class="mt-2 text-2xl font-bold text-gray-900">
                Flashcards - {{ course.title }}
              </h1>
            </div>
          </div>
        </div>
        
        <!-- Flashcard content here -->
        <!-- ... -->
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { ArrowLeftIcon } from '@heroicons/vue/24/outline'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  course: Object,
  flashcards: Object,
  flashcardSets: Object,
})
</script>