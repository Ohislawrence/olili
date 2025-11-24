<template>
  <AdminLayout>
    <Head :title="blog_post.title" />
    
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">{{ blog_post.title }}</h1>
          <div class="flex items-center space-x-4 mt-2">
            <span
              :class="[
                'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                blog_post.is_published ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'
              ]"
            >
              {{ blog_post.is_published ? 'Published' : 'Draft' }}
            </span>
            <span class="text-sm text-gray-500">by {{ blog_post.author?.name }}</span>
            <span class="text-sm text-gray-500">{{ formatDate(blog_post.published_at) }}</span>
          </div>
        </div>
        <div class="flex space-x-3">
          <Link
            :href="route('admin.blog-posts.edit', blog_post.id)"
            class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150"
          >
            Edit Post
          </Link>
          <Link
            :href="route('admin.blog-posts.index')"
            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150"
          >
            Back to Posts
          </Link>
        </div>
      </div>

      <!-- Blog Post Content -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <!-- Featured Image -->
        <div v-if="blog_post.image_url" class="w-full h-64 bg-gray-200">
          <img
            :src="'/storage/' + blog_post.image_url"
            :alt="blog_post.title"
            class="w-full h-full object-cover"
          >
        </div>

        <!-- Content -->
        <div class="p-8">
          <!-- Meta Information -->
          <div class="flex flex-wrap items-center gap-4 mb-6 text-sm text-gray-500">
            <div class="flex items-center">
              <CalendarIcon class="h-4 w-4 mr-1" />
              {{ formatDate(blog_post.published_at) }}
            </div>
            <div class="flex items-center">
              <ClockIcon class="h-4 w-4 mr-1" />
              {{ blog_post.reading_time }} min read
            </div>
            <div class="flex items-center">
              <TagIcon class="h-4 w-4 mr-1" />
              {{ blog_post.category }}
            </div>
          </div>

          <!-- Excerpt -->
          <div v-if="blog_post.excerpt" class="prose prose-lg mb-8">
            <p class="text-xl text-gray-700 leading-relaxed italic">{{ blog_post.excerpt }}</p>
          </div>

          <!-- Content -->
          <div class="prose prose-lg max-w-none">
            <div v-html="formatContent(blog_post.content)"></div>
          </div>

          <!-- Tags -->
          <div v-if="blog_post.tags && blog_post.tags.length" class="mt-8 pt-6 border-t border-gray-200">
            <h3 class="text-sm font-medium text-gray-900 mb-3">Tags</h3>
            <div class="flex flex-wrap gap-2">
              <span
                v-for="(tag, index) in blog_post.tags"
                :key="index"
                class="inline-flex px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-800"
              >
                {{ tag }}
              </span>
            </div>
          </div>

          <!-- SEO Information -->
          <div class="mt-8 pt-6 border-t border-gray-200">
            <h3 class="text-sm font-medium text-gray-900 mb-3">SEO Information</h3>
            <div class="space-y-2 text-sm text-gray-600">
              <div>
                <strong>Meta Title:</strong> 
                <span class="ml-2">{{ blog_post.meta_title || 'Not set' }}</span>
              </div>
              <div>
                <strong>Meta Description:</strong> 
                <span class="ml-2">{{ blog_post.meta_description || 'Not set' }}</span>
              </div>
              <div>
                <strong>Slug:</strong> 
                <span class="ml-2">{{ blog_post.slug }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { marked } from 'marked'
import DOMPurify from 'dompurify'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { CalendarIcon, ClockIcon, TagIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  blog_post: Object,
})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

const formatContent = (content) => {
  if (!content) return ""
  marked.setOptions({
    breaks: true,
    gfm: true,
  })
  const html = marked.parse(content)
  return DOMPurify.sanitize(html)
}
</script>