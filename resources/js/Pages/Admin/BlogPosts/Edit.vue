<template>
  <AdminLayout>
    <Head :title="`Edit: ${blog_post.title}`" />
    
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Edit Blog Post</h1>
          <p class="text-gray-600 mt-2">Update your blog post content and settings</p>
        </div>
        <Link
          :href="route('admin.blog-posts.index')"
          class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150"
        >
          Back to Posts
        </Link>
      </div>

      <!-- Form -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <form @submit.prevent="submit">
          <div class="p-6 space-y-6">
            <!-- Basic Information -->
            <div>
              <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
              
              <div class="grid grid-cols-1 gap-6">
                <!-- Title -->
                <div>
                  <label for="title" class="block text-sm font-medium text-gray-700">Title *</label>
                  <input
                    id="title"
                    v-model="form.title"
                    type="text"
                    required
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-emerald-500 focus:border-emerald-500"
                  >
                  <p v-if="form.errors.title" class="mt-1 text-sm text-red-600">{{ form.errors.title }}</p>
                </div>

                <!-- Slug -->
                <div>
                  <label for="slug" class="block text-sm font-medium text-gray-700">Slug</label>
                  <input
                    id="slug"
                    v-model="form.slug"
                    type="text"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-emerald-500 focus:border-emerald-500"
                  >
                  <p class="mt-1 text-sm text-gray-500">URL-friendly version of the title</p>
                  <p v-if="form.errors.slug" class="mt-1 text-sm text-red-600">{{ form.errors.slug }}</p>
                </div>

                <!-- Excerpt -->
                <div>
                  <label for="excerpt" class="block text-sm font-medium text-gray-700">Excerpt</label>
                  <textarea
                    id="excerpt"
                    v-model="form.excerpt"
                    rows="3"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-emerald-500 focus:border-emerald-500"
                  ></textarea>
                  <p v-if="form.errors.excerpt" class="mt-1 text-sm text-red-600">{{ form.errors.excerpt }}</p>
                </div>

                <!-- Content -->
                <div>
                  <label for="content" class="block text-sm font-medium text-gray-700">Content *</label>
                  <RichTextEditor
                    v-model="form.content"
                    :show-toolbar="true"
                    :show-character-count="true"
                    placeholder="Start writing your blog post content..."
                  />
                  <p v-if="form.errors.content" class="mt-1 text-sm text-red-600">{{ form.errors.content }}</p>
                </div>
              </div>
            </div>

            <!-- Rest of the form remains the same as Create.vue -->
            <!-- ... Media & Settings, SEO Settings sections ... -->
          </div>

          <!-- Form Actions -->
          <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
            <Link
              :href="route('admin.blog-posts.index')"
              class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150"
            >
              Cancel
            </Link>
            <button
              type="submit"
              :disabled="form.processing"
              class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 focus:bg-emerald-700 active:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50"
            >
              <span v-if="form.processing">Updating...</span>
              <span v-else>Update Post</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { PhotoIcon } from '@heroicons/vue/24/outline'
import RichTextEditor from '@/Components/RichTextEditor.vue'

const props = defineProps({
  blog_post: Object,
  categories: Array,
  authors: Array,
})

const tagInput = ref('')

const form = useForm({
  title: props.blog_post.title,
  slug: props.blog_post.slug,
  excerpt: props.blog_post.excerpt,
  content: props.blog_post.content,
  image: null,
  category: props.blog_post.category,
  published_at: props.blog_post.published_at ? new Date(props.blog_post.published_at).toISOString().slice(0, 16) : '',
  is_published: props.blog_post.is_published,
  meta_title: props.blog_post.meta_title,
  meta_description: props.blog_post.meta_description,
  tags: props.blog_post.tags || [],
})

const handleImageUpload = (event) => {
  form.image = event.target.files[0]
}

const addTag = () => {
  const tag = tagInput.value.trim()
  if (tag && !form.tags.includes(tag)) {
    form.tags.push(tag)
  }
  tagInput.value = ''
}

const removeTag = (index) => {
  form.tags.splice(index, 1)
}

const submit = () => {
  form.put(route('admin.blog-posts.update', props.blog_post.id))
}
</script>