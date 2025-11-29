<!-- resources/js/Components/Admin/UserSearch.vue -->
<template>
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-2">
      Select User
    </label>
    <div class="relative">
      <input
        v-model="query"
        type="text"
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        placeholder="Search users by name or email..."
        @input="handleSearch"
        @focus="open = true"
        @blur="onBlur"
      />
      <div class="absolute inset-y-0 right-0 flex items-center pr-2">
        <ChevronUpDownIcon class="h-5 w-5 text-gray-400" />
      </div>

      <!-- Dropdown Options -->
      <div
        v-if="open && filteredUsers.length > 0"
        class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
      >
        <div
          v-for="user in filteredUsers"
          :key="user.id"
          class="relative cursor-default select-none py-2 pl-10 pr-4 hover:bg-blue-600 hover:text-white text-gray-900"
          @mousedown="selectUser(user)"
        >
          <span class="block truncate">
            {{ user.name }} ({{ user.email }})
          </span>
          <span
            v-if="selectedUser && selectedUser.id === user.id"
            class="absolute inset-y-0 left-0 flex items-center pl-3 text-blue-600"
          >
            <CheckIcon class="h-5 w-5" />
          </span>
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
            <div class="text-xs text-gray-400 capitalize mt-1">
              {{ selectedUser.roles?.[0]?.name || 'No role' }}
            </div>
          </div>
        </div>
        <button
          type="button"
          @click="clearSelection"
          class="text-red-400 hover:text-red-600 transition-colors duration-150 p-1"
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
import { computed, ref, watch, onMounted } from 'vue'
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
let searchTimeout = null

// Fetch users based on search query
const searchUsers = async (search) => {
  if (!search || search.length < 2) {
    users.value = []
    return
  }

  try {
    const response = await axios.get(route('admin.users.search'), {
      params: { search, limit: 10 }
    })
    users.value = response.data
  } catch (error) {
    console.error('Error searching users:', error)
    users.value = []
  }
}

// Handle search input with debounce
const handleSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    searchUsers(query.value)
  }, 300)
}

// Handle blur event with delay to allow click selection
const onBlur = () => {
  setTimeout(() => {
    open.value = false
  }, 200)
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
}

// Get user initials for avatar
const getUserInitials = (name) => {
  return name
    .split(' ')
    .map(part => part.charAt(0))
    .join('')
    .toUpperCase()
    .slice(0, 2)
}

// Filter users based on search query
const filteredUsers = computed(() => {
  return users.value.filter((user) =>
    user.name.toLowerCase().includes(query.value.toLowerCase()) ||
    user.email.toLowerCase().includes(query.value.toLowerCase())
  )
})

// Initialize if there's a pre-selected value
onMounted(async () => {
  if (props.modelValue) {
    try {
      const response = await axios.post(route('admin.users.by-ids'), {
        ids: [props.modelValue]
      })
      if (response.data.length > 0) {
        selectedUser.value = response.data[0]
      }
    } catch (error) {
      console.error('Error fetching pre-selected user:', error)
    }
  }
})

// Watch for external modelValue changes
watch(() => props.modelValue, (newValue) => {
  if (!newValue && selectedUser.value) {
    selectedUser.value = null
    query.value = ''
  }
})
</script>