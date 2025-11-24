<template>
  <AdminLayout>
    <Head title="Create Blog Post" />
    
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Create Blog Post</h1>
          <p class="text-gray-600 mt-2">Create a new blog post for your audience</p>
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
                    placeholder="auto-generated-if-empty"
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
                    placeholder="Brief description of the post..."
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

            <!-- Media & Settings -->
            <div>
              <h3 class="text-lg font-medium text-gray-900 mb-4">Media & Settings</h3>
              
              <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Image Upload -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Featured Image</label>
                  <div class="flex items-center justify-center w-full">
                    <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                      <div class="flex flex-col items-center justify-center pt-5 pb-6">
                        <PhotoIcon class="w-8 h-8 mb-4 text-gray-500" />
                        <p class="mb-2 text-sm text-gray-500">
                          <span class="font-semibold">Click to upload</span> or drag and drop
                        </p>
                        <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                      </div>
                      <input
                        type="file"
                        class="hidden"
                        @change="handleImageUpload"
                        accept="image/*"
                      >
                    </label>
                  </div>
                  <p v-if="form.errors.image" class="mt-1 text-sm text-red-600">{{ form.errors.image }}</p>
                </div>

                <!-- Settings -->
                <div class="space-y-6">
                  <!-- Category -->
                  <div>
                    <label for="category" class="block text-sm font-medium text-gray-700">Category *</label>
                    <select
                      id="category"
                      v-model="form.category"
                      required
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-emerald-500 focus:border-emerald-500"
                    >
                      <option value="">Select a category</option>
                      <option v-for="category in categories" :key="category" :value="category">
                        {{ category }}
                      </option>
                    </select>
                    <p v-if="form.errors.category" class="mt-1 text-sm text-red-600">{{ form.errors.category }}</p>
                  </div>

                  <!-- Publish Settings -->
                  <div class="space-y-4">
                    <div class="flex items-center">
                      <input
                        id="is_published"
                        v-model="form.is_published"
                        type="checkbox"
                        class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded"
                      >
                      <label for="is_published" class="ml-2 block text-sm text-gray-900">
                        Publish immediately
                      </label>
                    </div>

                    <div v-if="!form.is_published">
                      <label for="published_at" class="block text-sm font-medium text-gray-700">Schedule Publication</label>
                      <input
                        id="published_at"
                        v-model="form.published_at"
                        type="datetime-local"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-emerald-500 focus:border-emerald-500"
                      >
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- SEO Settings -->
            <div>
              <h3 class="text-lg font-medium text-gray-900 mb-4">SEO Settings</h3>
              
              <div class="grid grid-cols-1 gap-6">
                <!-- Meta Title -->
                <div>
                  <label for="meta_title" class="block text-sm font-medium text-gray-700">Meta Title</label>
                  <input
                    id="meta_title"
                    v-model="form.meta_title"
                    type="text"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-emerald-500 focus:border-emerald-500"
                    placeholder="Optional - defaults to post title"
                  >
                </div>

                <!-- Meta Description -->
                <div>
                  <label for="meta_description" class="block text-sm font-medium text-gray-700">Meta Description</label>
                  <textarea
                    id="meta_description"
                    v-model="form.meta_description"
                    rows="3"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-emerald-500 focus:border-emerald-500"
                    placeholder="Optional - defaults to post excerpt"
                  ></textarea>
                </div>

                <!-- Tags -->
                <div>
                  <label for="tags" class="block text-sm font-medium text-gray-700">Tags</label>
                  <input
                    id="tags"
                    v-model="tagInput"
                    type="text"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-emerald-500 focus:border-emerald-500"
                    placeholder="Add tags separated by commas"
                    @keydown.enter.prevent="addTag"
                  >
                  <div class="mt-2 flex flex-wrap gap-2">
                    <span
                      v-for="(tag, index) in form.tags"
                      :key="index"
                      class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800"
                    >
                      {{ tag }}
                      <button
                        type="button"
                        @click="removeTag(index)"
                        class="ml-1 hover:text-emerald-900"
                      >
                        ×
                      </button>
                    </span>
                  </div>
                </div>
              </div>
            </div>
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
              <span v-if="form.processing">Creating...</span>
              <span v-else>Create Post</span>
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
  categories: Array,
  authors: Array,
})

const tagInput = ref('')

const form = useForm({
  title: '',
  slug: '',
  excerpt: '',
  content: '',
  image: null,
  category: '',
  published_at: '',
  is_published: false,
  meta_title: '',
  meta_description: '',
  tags: [],
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
  form.post(route('admin.blog-posts.store'))
}
</script>