<!-- resources/js/Components/Admin/UserSearch.vue -->
<template>
  <div>
    <div class="relative">
      <input
        v-model="query"
        type="text"
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        placeholder="Search users by name or email..."
        @input="handleSearch"
        @focus="open = true"
        @blur="onBlur"
        @keydown.esc="open = false"
        :disabled="loading"
      />
      <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
        <ChevronUpDownIcon class="h-5 w-5 text-gray-400" />
      </div>

      <!-- Loading Indicator -->
      <div v-if="loading && open" class="absolute z-10 mt-1 w-full">
        <div class="bg-white border border-gray-200 rounded-md p-3 shadow-lg">
          <div class="flex items-center justify-center">
            <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-600 mr-2"></div>
            <span class="text-sm text-gray-600">Searching...</span>
          </div>
        </div>
      </div>

      <!-- Dropdown Options -->
      <div
        v-else-if="open && filteredUsers.length > 0"
        class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
      >
        <div
          v-for="user in filteredUsers"
          :key="user.id"
          class="relative cursor-default select-none py-2 pl-10 pr-4 text-gray-900 hover:bg-blue-50 group"
          @mousedown.prevent="selectUser(user)"
        >
          <span class="block truncate font-medium">
            {{ user.name }}
          </span>
          <span class="block truncate text-sm text-gray-500">
            {{ user.email }}
          </span>
          <span
            v-if="modelValue == user.id"
            class="absolute inset-y-0 left-0 flex items-center pl-3 text-blue-600"
          >
            <CheckIcon class="h-5 w-5" />
          </span>
        </div>
      </div>

      <!-- No Results -->
      <div
        v-else-if="open && query && !loading && filteredUsers.length === 0"
        class="absolute z-10 mt-1 w-full"
      >
        <div class="bg-white border border-gray-200 rounded-md p-3 shadow-lg">
          <p class="text-sm text-gray-500 text-center">No users found</p>
        </div>
      </div>
    </div>

    <!-- Selected User Display -->
    <div v-if="selectedUser" class="mt-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
          <div class="flex-shrink-0 h-8 w-8 bg-blue-500 rounded-full flex items-center justify-center">
            <span class="text-xs font-medium text-white">
              {{ getUserInitials(selectedUser.name) }}
            </span>
          </div>
          <div>
            <div class="text-sm font-medium text-gray-900">{{ selectedUser.name }}</div>
            <div class="text-xs text-gray-500">{{ selectedUser.email }}</div>
            <div v-if="selectedUser.roles && selectedUser.roles.length > 0" class="text-xs text-gray-400 capitalize mt-1">
              {{ selectedUser.roles[0].name }}
            </div>
          </div>
        </div>
        <button
          type="button"
          @click="clearSelection"
          class="text-red-400 hover:text-red-600 transition-colors duration-150 p-1"
          title="Remove selection"
        >
          <XMarkIcon class="h-4 w-4" />
        </button>
      </div>
    </div>

    <!-- Error Message -->
    <div v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import {
  CheckIcon,
  ChevronUpDownIcon,
  XMarkIcon,
} from '@heroicons/vue/24/outline'
import axios from 'axios'

const props = defineProps({
  modelValue: [Number, String],
  error: String,
})

const emit = defineEmits(['update:modelValue'])

const open = ref(false)
const query = ref('')
const users = ref([])
const selectedUser = ref(null)
const loading = ref(false)
let searchTimeout = null
let blurTimeout = null

// Fetch users based on search query
const searchUsers = async (search) => {
  if (!search || search.length < 2) {
    users.value = []
    return
  }

  loading.value = true
  try {
    const response = await axios.get(route('admin.users.search.all'), {
      params: {
        search: search,
        limit: 10
      }
    })
    users.value = response.data || []
  } catch (error) {
    console.error('Error searching users:', error)
    users.value = []
  } finally {
    loading.value = false
  }
}

// Handle search input with debounce
const handleSearch = () => {
  clearTimeout(searchTimeout)

  // Clear results if query is too short
  if (query.value.length < 2) {
    users.value = []
    return
  }

  searchTimeout = setTimeout(() => {
    searchUsers(query.value)
  }, 300)
}

// Handle blur event with delay to allow click selection
const onBlur = () => {
  clearTimeout(blurTimeout)
  blurTimeout = setTimeout(() => {
    open.value = false
  }, 150)
}

// Select a user
const selectUser = (user) => {
  selectedUser.value = user
  emit('update:modelValue', user.id)
  open.value = false
  query.value = ''
  users.value = []
}

// Clear selection
const clearSelection = () => {
  selectedUser.value = null
  emit('update:modelValue', null)
  query.value = ''
  open.value = false
  users.value = []
}

// Get user initials for avatar
const getUserInitials = (name) => {
  if (!name) return '??'
  return name
    .split(' ')
    .map(part => part.charAt(0))
    .join('')
    .toUpperCase()
    .slice(0, 2)
}

// Filter users based on search query (local filtering after API returns)
const filteredUsers = computed(() => {
  if (!query.value || query.value.length < 1) {
    return []
  }

  const searchLower = query.value.toLowerCase()
  return users.value.filter((user) =>
    (user.name && user.name.toLowerCase().includes(searchLower)) ||
    (user.email && user.email.toLowerCase().includes(searchLower))
  )
})

// Fetch selected user details if modelValue is set
const fetchSelectedUser = async () => {
  if (!props.modelValue) {
    selectedUser.value = null
    return
  }

  try {
    const response = await axios.post(route('admin.users.by-ids'), {
      ids: [props.modelValue]
    })

    if (response.data && response.data.length > 0) {
      selectedUser.value = response.data[0]
      query.value = selectedUser.value.name
    }
  } catch (error) {
    console.error('Error fetching selected user:', error)
    selectedUser.value = null
  }
}

// Initialize if there's a pre-selected value
onMounted(async () => {
  if (props.modelValue) {
    await fetchSelectedUser()
  }
})

// Watch for external modelValue changes
watch(() => props.modelValue, async (newValue) => {
  if (newValue) {
    await fetchSelectedUser()
  } else {
    selectedUser.value = null
    query.value = ''
    users.value = []
  }
})

// Watch for query changes to open dropdown
watch(query, (newQuery) => {
  if (newQuery && newQuery.length >= 2 && !open.value) {
    open.value = true
  }
})
</script>
