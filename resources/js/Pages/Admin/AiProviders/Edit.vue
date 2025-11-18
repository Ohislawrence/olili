<!-- resources/js/Pages/Admin/AiProviders/Edit.vue -->
<template>
  <AdminLayout>
    <Head :title="`Edit AI Provider - ${provider.name}`" />

    <div class="py-6">
      <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex justify-between items-center">
            <div>
              <h1 class="text-3xl font-bold text-gray-900">Edit AI Provider</h1>
              <p class="mt-2 text-gray-600">
                Update provider configuration and settings
              </p>
            </div>
            <Link
              :href="route('admin.ai-providers.index')"
              class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
            >
              Back to Providers
            </Link>
          </div>
        </div>

        <!-- Form -->
        <div class="bg-white shadow rounded-lg">
          <form @submit.prevent="submit">
            <div class="p-6 space-y-6">
              <!-- Provider Information -->
              <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Provider Information</h3>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                  <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">
                      Provider Name *
                    </label>
                    <input
                      id="name"
                      v-model="form.name"
                      type="text"
                      required
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                      :class="{ 'border-red-300': errors.name }"
                    />
                    <p v-if="errors.name" class="mt-1 text-sm text-red-600">
                      {{ errors.name }}
                    </p>
                  </div>

                  <div>
                    <label for="code" class="block text-sm font-medium text-gray-700">
                      Provider Code *
                    </label>
                    <select
                      id="code"
                      v-model="form.code"
                      required
                      class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                      :class="{ 'border-red-300': errors.code }"
                    >
                      <option
                        v-for="(label, value) in supported_providers"
                        :key="value"
                        :value="value"
                      >
                        {{ label }}
                      </option>
                    </select>
                    <p v-if="errors.code" class="mt-1 text-sm text-red-600">
                      {{ errors.code }}
                    </p>
                  </div>

                  <div class="sm:col-span-2">
                    <label for="api_key" class="block text-sm font-medium text-gray-700">
                      API Key *
                    </label>
                    <input
                      id="api_key"
                      v-model="form.api_key"
                      type="password"
                      required
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                      :class="{ 'border-red-300': errors.api_key }"
                      placeholder="Enter new API key or keep existing"
                    />
                    <p class="mt-1 text-sm text-gray-500">
                      Leave empty to keep the current API key
                    </p>
                  </div>

                  <div class="sm:col-span-2">
                    <label for="base_url" class="block text-sm font-medium text-gray-700">
                      Base URL
                    </label>
                    <input
                      id="base_url"
                      v-model="form.base_url"
                      type="url"
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                      placeholder="https://api.example.com/v1"
                    />
                  </div>
                </div>
              </div>

              <!-- Models Configuration -->
              <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Models Configuration</h3>
                <div class="space-y-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                      Available Models *
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                      <label
                        v-for="model in getDefaultModels(form.code)"
                        :key="model.value"
                        class="flex items-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer"
                        :class="{ 'border-blue-500 bg-blue-50': form.available_models.includes(model.value) }"
                      >
                        <input
                          type="checkbox"
                          :value="model.value"
                          v-model="form.available_models"
                          class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        />
                        <div class="ml-3">
                          <span class="text-sm font-medium text-gray-900">
                            {{ model.label }}
                          </span>
                          <p class="text-xs text-gray-500">
                            {{ model.description }}
                          </p>
                        </div>
                      </label>
                    </div>
                    <p v-if="errors.available_models" class="mt-1 text-sm text-red-600">
                      {{ errors.available_models }}
                    </p>
                  </div>
                </div>
              </div>

              <!-- Pricing & Limits -->
              <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Pricing & Limits</h3>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                  <div>
                    <label for="cost_per_token" class="block text-sm font-medium text-gray-700">
                      Cost per Token *
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">$</span>
                      </div>
                      <input
                        id="cost_per_token"
                        v-model="form.cost_per_token"
                        type="number"
                        step="0.00000001"
                        min="0"
                        required
                        class="block w-full pl-7 pr-12 border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        :class="{ 'border-red-300': errors.cost_per_token }"
                      />
                    </div>
                  </div>

                  <div>
                    <label for="max_tokens_per_request" class="block text-sm font-medium text-gray-700">
                      Max Tokens/Request *
                    </label>
                    <input
                      id="max_tokens_per_request"
                      v-model="form.max_tokens_per_request"
                      type="number"
                      min="1"
                      required
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                      :class="{ 'border-red-300': errors.max_tokens_per_request }"
                    />
                  </div>

                  <div>
                    <label for="rate_limit_per_minute" class="block text-sm font-medium text-gray-700">
                      Rate Limit/Minute *
                    </label>
                    <input
                      id="rate_limit_per_minute"
                      v-model="form.rate_limit_per_minute"
                      type="number"
                      min="1"
                      required
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                      :class="{ 'border-red-300': errors.rate_limit_per_minute }"
                    />
                  </div>
                </div>
              </div>

              <!-- Status -->
              <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Status</h3>
                <div class="space-y-4">
                  <div class="flex items-center">
                    <input
                      id="is_active"
                      v-model="form.is_active"
                      type="checkbox"
                      class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                    />
                    <label for="is_active" class="ml-2 block text-sm text-gray-900">
                      Active Provider
                    </label>
                  </div>
                  <div class="flex items-center">
                    <input
                      id="is_default"
                      v-model="form.is_default"
                      type="checkbox"
                      class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                    />
                    <label for="is_default" class="ml-2 block text-sm text-gray-900">
                      Set as Default Provider
                    </label>
                  </div>
                </div>
              </div>
            </div>

            <!-- Form Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between">
              <button
                type="button"
                @click="deleteProvider"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                :disabled="provider.is_default"
              >
                Delete Provider
              </button>
              <div class="flex space-x-3">
                <Link
                  :href="route('admin.ai-providers.index')"
                  class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                  Cancel
                </Link>
                <button
                  type="submit"
                  :disabled="processing"
                  class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
                >
                  <span v-if="processing">
                    <ArrowPathIcon class="h-4 w-4 animate-spin" />
                  </span>
                  <span v-else>
                    Update Provider
                  </span>
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import { ArrowPathIcon } from '@heroicons/vue/24/outline'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  provider: Object,
  supported_providers: Object,
  errors: Object,
})

const processing = ref(false)

const form = useForm({
  name: props.provider.name,
  code: props.provider.code,
  api_key: '', // Don't pre-fill API key for security
  base_url: props.provider.base_url || '',
  available_models: props.provider.available_models || [],
  cost_per_token: props.provider.cost_per_token,
  max_tokens_per_request: props.provider.max_tokens_per_request,
  rate_limit_per_minute: props.provider.rate_limit_per_minute,
  is_active: props.provider.is_active,
  is_default: props.provider.is_default,
})

const getDefaultModels = (providerCode) => {
  const models = {
    openai: [
      { value: 'gpt-4', label: 'GPT-4', description: 'Most capable model' },
      { value: 'gpt-4-turbo', label: 'GPT-4 Turbo', description: 'Improved GPT-4' },
      { value: 'gpt-3.5-turbo', label: 'GPT-3.5 Turbo', description: 'Fast and cost-effective' },
    ],
    deepseek: [
      { value: 'deepseek-chat', label: 'DeepSeek Chat', description: 'General purpose model' },
      { value: 'deepseek-coder', label: 'DeepSeek Coder', description: 'Code generation' },
    ],
    anthropic: [
      { value: 'claude-3-opus', label: 'Claude 3 Opus', description: 'Most capable model' },
      { value: 'claude-3-sonnet', label: 'Claude 3 Sonnet', description: 'Balanced performance' },
      { value: 'claude-3-haiku', label: 'Claude 3 Haiku', description: 'Fast and efficient' },
    ],
    google: [
      { value: 'gemini-pro', label: 'Gemini Pro', description: 'General purpose model' },
      { value: 'gemini-ultra', label: 'Gemini Ultra', description: 'Most advanced model' },
    ],
    openrouter: [
      { value: 'deepseek-chat-v3.1:free', label: 'deepseek-chat-v3.1:free', description: 'deepseek-chat-v3.1:free' },
      { value: 'openai/gpt-oss-20b:free', label: 'gpt-oss-20b:free', description: 'gpt-oss-20b:free' },
    ],
  }

  return models[providerCode] || []
}

const submit = () => {
  processing.value = true

  // If API key is empty, remove it from the form data to keep the existing one
  if (!form.api_key) {
    const { api_key, ...formWithoutKey } = form
    formWithoutKey.put(route('admin.ai-providers.update', props.provider.id), {
      onFinish: () => {
        processing.value = false
      },
    })
  } else {
    form.put(route('admin.ai-providers.update', props.provider.id), {
      onFinish: () => {
        processing.value = false
      },
    })
  }
}

const deleteProvider = () => {
  if (props.provider.is_default) {
    alert('Cannot delete the default AI provider.')
    return
  }

  if (confirm(`Are you sure you want to delete "${props.provider.name}"? This action cannot be undone.`)) {
    router.delete(route('admin.ai-providers.destroy', props.provider.id))
  }
}
</script>
