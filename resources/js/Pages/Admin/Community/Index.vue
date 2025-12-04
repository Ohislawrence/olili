<template>
  <AdminLayout>
    <Head title="Community Moderation" />

    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex justify-between items-center">
            <div>
              <h1 class="text-3xl font-bold text-gray-900">Community Moderation</h1>
              <p class="mt-2 text-gray-600">
                Manage and moderate community posts and comments
              </p>
            </div>
            <div class="flex space-x-3">
              <Link
                :href="route('admin.community.export')"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
              >
                <ArrowDownTrayIcon class="h-4 w-4 mr-2" />
                Export
              </Link>
              <Link
                :href="route('admin.community.comments.index')"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700"
              >
                <ChatBubbleLeftRightIcon class="h-4 w-4 mr-2" />
                Moderate Comments
              </Link>
            </div>
          </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <NewspaperIcon class="h-8 w-8 text-blue-600" />
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Posts</p>
                <p class="text-2xl font-bold text-gray-900">{{ stats.total }}</p>
              </div>
            </div>
          </div>
          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <CheckCircleIcon class="h-8 w-8 text-green-600" />
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Approved</p>
                <p class="text-2xl font-bold text-gray-900">{{ stats.approved }}</p>
              </div>
            </div>
          </div>
          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <ClockIcon class="h-8 w-8 text-yellow-600" />
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Pending</p>
                <p class="text-2xl font-bold text-gray-900">{{ stats.pending }}</p>
              </div>
            </div>
          </div>
          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <CalendarIcon class="h-8 w-8 text-purple-600" />
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Today</p>
                <p class="text-2xl font-bold text-gray-900">{{ stats.today }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Filters -->
        <div class="mb-6 bg-white rounded-lg shadow p-4">
          <div class="flex flex-wrap gap-4 items-center">
            <div class="flex-1 min-w-[300px]">
              <input
                v-model="filters.search"
                type="text"
                placeholder="Search posts, users, or content..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                @input="search"
              />
            </div>
            <select
              v-model="filters.status"
              class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              @change="filter"
            >
              <option value="">All Status</option>
              <option value="pending">Pending</option>
              <option value="approved">Approved</option>
              <option value="flagged">Flagged</option>
            </select>
            <select
              v-model="filters.type"
              class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              @change="filter"
            >
              <option value="">All Types</option>
              <option value="discussion">Discussion</option>
              <option value="question">Question</option>
              <option value="achievement">Achievement</option>
              <option value="resource">Resource</option>
            </select>
            <button
              @click="clearFilters"
              class="px-4 py-2 text-gray-600 hover:text-gray-800"
            >
              Clear
            </button>
          </div>
        </div>

        <!-- Bulk Actions -->
        <div v-if="selectedPosts.length > 0" class="mb-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <CheckCircleIcon class="h-5 w-5 text-blue-600 mr-2" />
              <span class="text-sm font-medium text-blue-900">
                {{ selectedPosts.length }} posts selected
              </span>
            </div>
            <div class="flex space-x-2">
              <select
                v-model="bulkAction"
                class="px-3 py-1 border border-gray-300 rounded text-sm"
              >
                <option value="">Bulk Actions</option>
                <option value="approve">Approve</option>
                <option value="reject">Reject</option>
                <option value="pin">Pin</option>
                <option value="unpin">Unpin</option>
                <option value="delete">Delete</option>
              </select>
              <button
                @click="applyBulkAction"
                :disabled="!bulkAction"
                class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700 disabled:opacity-50"
              >
                Apply
              </button>
              <button
                @click="clearSelection"
                class="px-3 py-1 text-gray-600 hover:text-gray-800 text-sm"
              >
                Clear
              </button>
            </div>
          </div>
        </div>

        <!-- Posts Table -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">
                    <input
                      type="checkbox"
                      :checked="allSelected"
                      @change="toggleSelectAll"
                      class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    />
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Post
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    User
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Type
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Status
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Stats
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Created
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr
                  v-for="post in posts.data"
                  :key="post.id"
                  class="hover:bg-gray-50"
                >
                  <td class="px-6 py-4 whitespace-nowrap">
                    <input
                      type="checkbox"
                      :checked="selectedPosts.includes(post.id)"
                      @change="toggleSelect(post.id)"
                      class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    />
                  </td>
                  <td class="px-6 py-4">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10" v-if="post.is_pinned">
                        <MapPinIcon class="h-5 w-5 text-yellow-500" />
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">
                          <Link
                            :href="route('admin.community.posts.show', post.id)"
                            class="hover:text-blue-600"
                          >
                            {{ post.title }}
                          </Link>
                        </div>
                        <div class="text-sm text-gray-500 line-clamp-2">
                          {{ post.excerpt }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-8 w-8">
                        <img
                          :src="post.user.profile_photo_url"
                          :alt="post.user.name"
                          class="h-8 w-8 rounded-full"
                        />
                      </div>
                      <div class="ml-3">
                        <div class="text-sm font-medium text-gray-900">
                          {{ post.user.name }}
                        </div>
                        <div class="text-xs text-gray-500">
                          {{ post.user.email }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span
                      class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                      :class="typeClasses[post.type]"
                    >
                      {{ post.type }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span
                      class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                      :class="statusClasses[post.is_approved ? 'approved' : 'pending']"
                    >
                      {{ post.is_approved ? 'Approved' : 'Pending' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <div class="flex space-x-3">
                      <span class="flex items-center">
                        <HeartIcon class="h-4 w-4 text-red-400 mr-1" />
                        {{ post.like_count }}
                      </span>
                      <span class="flex items-center">
                        <ChatBubbleLeftRightIcon class="h-4 w-4 text-blue-400 mr-1" />
                        {{ post.comment_count }}
                      </span>
                      <span class="flex items-center">
                        <EyeIcon class="h-4 w-4 text-gray-400 mr-1" />
                        {{ post.views }}
                      </span>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ formatDate(post.created_at) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex space-x-2">
                      <Link
                        :href="route('admin.community.posts.show', post.id)"
                        class="text-blue-600 hover:text-blue-900"
                      >
                        View
                      </Link>
                      <button
                        v-if="!post.is_approved"
                        @click="approvePost(post.id)"
                        class="text-green-600 hover:text-green-900"
                      >
                        Approve
                      </button>
                      <button
                        v-if="!post.is_approved"
                        @click="rejectPost(post.id)"
                        class="text-red-600 hover:text-red-900"
                      >
                        Reject
                      </button>
                      <button
                        v-if="!post.is_pinned"
                        @click="pinPost(post.id)"
                        class="text-yellow-600 hover:text-yellow-900"
                      >
                        Pin
                      </button>
                      <button
                        v-if="post.is_pinned"
                        @click="unpinPost(post.id)"
                        class="text-gray-600 hover:text-gray-900"
                      >
                        Unpin
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <Pagination :links="posts.links" />
          </div>
        </div>

        <!-- Recent Active Users -->
        <div class="mt-8">
          <h2 class="text-lg font-medium text-gray-900 mb-4">Recent Active Users</h2>
          <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 p-6">
              <div
                v-for="user in recentUsers"
                :key="user.id"
                class="flex items-center space-x-3 p-3 hover:bg-gray-50 rounded-lg"
              >
                <img
                  :src="user.profile_photo_url"
                  :alt="user.name"
                  class="h-10 w-10 rounded-full"
                />
                <div>
                  <div class="text-sm font-medium text-gray-900">
                    {{ user.name }}
                  </div>
                  <div class="text-xs text-gray-500">
                    {{ user.community_posts_count }} posts
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Reject Modal -->
        <div v-if="showRejectModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
          <div class="bg-white rounded-lg p-6 max-w-md w-full">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
              Reject Post
            </h3>
            <textarea
              v-model="rejectReason"
              rows="4"
              placeholder="Enter rejection reason..."
              class="w-full px-3 py-2 border border-gray-300 rounded-md mb-4"
            ></textarea>
            <div class="flex justify-end space-x-3">
              <button
                @click="showRejectModal = false"
                class="px-4 py-2 text-gray-600 hover:text-gray-800"
              >
                Cancel
              </button>
              <button
                @click="confirmReject"
                :disabled="!rejectReason"
                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 disabled:opacity-50"
              >
                Reject
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import {
  NewspaperIcon,
  CheckCircleIcon,
  ClockIcon,
  CalendarIcon,
  ArrowDownTrayIcon,
  ChatBubbleLeftRightIcon,
  HeartIcon,
  EyeIcon,
  MapPinIcon
} from '@heroicons/vue/24/outline'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Pagination.vue'

const props = defineProps({
  posts: Object,
  stats: Object,
  recentUsers: Array,
  filters: Object,
})

const selectedPosts = ref([])
const bulkAction = ref('')
const showRejectModal = ref(false)
const rejectReason = ref('')
const postToReject = ref(null)

const filters = reactive({
  search: props.filters.search || '',
  status: props.filters.status || '',
  type: props.filters.type || '',
})

const typeClasses = {
  discussion: 'bg-blue-100 text-blue-800',
  question: 'bg-purple-100 text-purple-800',
  achievement: 'bg-green-100 text-green-800',
  resource: 'bg-yellow-100 text-yellow-800',
}

const statusClasses = {
  approved: 'bg-green-100 text-green-800',
  pending: 'bg-yellow-100 text-yellow-800',
  flagged: 'bg-red-100 text-red-800',
}

const allSelected = computed(() => {
  return props.posts.data.length > 0 && 
         selectedPosts.value.length === props.posts.data.length
})

let searchTimeout = null

const search = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    filter()
  }, 300)
}

const filter = () => {
  router.get(route('admin.community.index'), filters, {
    preserveState: true,
    replace: true,
  })
}

const clearFilters = () => {
  filters.search = ''
  filters.status = ''
  filters.type = ''
  filter()
}

const toggleSelect = (postId) => {
  const index = selectedPosts.value.indexOf(postId)
  if (index > -1) {
    selectedPosts.value.splice(index, 1)
  } else {
    selectedPosts.value.push(postId)
  }
}

const toggleSelectAll = () => {
  if (allSelected.value) {
    selectedPosts.value = []
  } else {
    selectedPosts.value = props.posts.data.map(post => post.id)
  }
}

const clearSelection = () => {
  selectedPosts.value = []
  bulkAction.value = ''
}

const applyBulkAction = () => {
  if (!bulkAction.value || selectedPosts.value.length === 0) return

  if (bulkAction.value === 'reject') {
    // Show reject modal for bulk actions
    alert('For bulk rejection, please reject posts individually or contact support.')
    return
  }

  router.post(route('admin.community.bulk-action'), {
    action: bulkAction.value,
    ids: selectedPosts.value,
  }, {
    onSuccess: () => {
      selectedPosts.value = []
      bulkAction.value = ''
    },
  })
}

const approvePost = (postId) => {
  router.post(route('admin.community.posts.approve', postId), {}, {
    onSuccess: () => {
      // Refresh data
      router.reload()
    },
  })
}

const rejectPost = (postId) => {
  postToReject.value = postId
  showRejectModal.value = true
}

const confirmReject = () => {
  if (!rejectReason.value) return

  router.post(route('admin.community.posts.reject', postToReject.value), {
    reason: rejectReason.value,
  }, {
    onSuccess: () => {
      showRejectModal.value = false
      rejectReason.value = ''
      postToReject.value = null
      router.reload()
    },
  })
}

const pinPost = (postId) => {
  router.post(route('admin.community.posts.pin', postId), {}, {
    onSuccess: () => {
      router.reload()
    },
  })
}

const unpinPost = (postId) => {
  router.post(route('admin.community.posts.unpin', postId), {}, {
    onSuccess: () => {
      router.reload()
    },
  })
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>