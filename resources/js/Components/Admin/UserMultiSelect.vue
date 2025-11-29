<!-- resources/js/Components/Admin/UserMultiSelect.vue -->
<template>
  <div class="space-y-4">
    <!-- Search and Add Users -->
    <div class="flex space-x-4">
      <div class="flex-1">
        <label class="block text-sm font-medium text-gray-700 mb-1">
          Search Users
        </label>
        <div class="relative">
          <input
            v-model="query"
            type="text"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            placeholder="Search by name or email..."
            @input="handleSearch"
            @keydown.enter.prevent="addFromSearch"
          />
          <div class="absolute inset-y-0 right-0 flex items-center pr-2">
            <ChevronUpDownIcon class="h-5 w-5 text-gray-400" />
          </div>
          
          <!-- Search Results Dropdown -->
          <div
            v-if="filteredUsers.length > 0 && query.length > 0"
            class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
          >
            <div
              v-for="user in filteredUsers"
              :key="user.id"
              class="relative cursor-default select-none py-2 pl-10 pr-4 hover:bg-blue-600 hover:text-white text-gray-900"
              @click="selectUser(user)"
            >
              <span class="block truncate">
                {{ user.name }} ({{ user.email }})
                <span class="text-xs opacity-75 capitalize"> - {{ user.roles?.[0]?.name || 'No role' }}</span>
              </span>
              <span
                v-if="selectedUser && selectedUser.id === user.id"
                class="absolute inset-y-0 left-0 flex items-center pl-3 text-blue-600"
              >
                <CheckIcon class="h-5 w-5" />
              </span>
            </div>
          </div>

          <!-- Loading State -->
          <div
            v-if="loading"
            class="absolute z-10 mt-1 w-full rounded-md bg-white py-2 px-4 text-sm text-gray-500"
          >
            Searching...
          </div>

          <!-- No Results -->
          <div
            v-if="query.length >= 2 && filteredUsers.length === 0 && !loading"
            class="absolute z-10 mt-1 w-full rounded-md bg-white py-2 px-4 text-sm text-gray-500"
          >
            No users found
          </div>
        </div>
      </div>
      
      <div class="flex items-end">
        <button
          type="button"
          @click="addSelectedUser"
          :disabled="!selectedUser"
          class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-150"
        >
          <PlusIcon class="h-4 w-4" />
        </button>
      </div>
    </div>

    <!-- Selected Users List -->
    <div v-if="selectedUsers.length > 0" class="bg-gray-50 rounded-lg p-4">
      <div class="flex justify-between items-center mb-3">
        <h3 class="text-sm font-medium text-gray-700">
          Selected Users ({{ selectedUsers.length }})
        </h3>
        <button
          type="button"
          @click="clearAll"
          class="text-sm text-red-600 hover:text-red-800 transition-colors duration-150"
        >
          Clear All
        </button>
      </div>
      
      <div class="space-y-2 max-h-60 overflow-y-auto">
        <div
          v-for="user in selectedUsers"
          :key="user.id"
          class="flex items-center justify-between bg-white p-3 rounded-lg border border-gray-200 shadow-sm"
        >
          <div class="flex items-center space-x-3">
            <div class="flex-shrink-0 h-8 w-8 bg-blue-500 rounded-full flex items-center justify-center">
              <span class="text-xs font-medium text-white">
                {{ getUserInitials(user.name) }}
              </span>
            </div>
            <div>
              <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
              <div class="text-xs text-gray-500">{{ user.email }}</div>
              <div class="text-xs text-gray-400 capitalize mt-1">
                {{ user.roles?.[0]?.name || 'No role' }}
              </div>
            </div>
          </div>
          
          <button
            type="button"
            @click="removeUser(user.id)"
            class="text-red-400 hover:text-red-600 transition-colors duration-150 p-1"
          >
            <XMarkIcon class="h-4 w-4" />
          </button>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div
      v-else
      class="bg-gray-50 rounded-lg p-8 text-center border-2 border-dashed border-gray-300"
    >
      <UserGroupIcon class="mx-auto h-12 w-12 text-gray-400" />
      <h3 class="mt-2 text-sm font-medium text-gray-900">No users selected</h3>
      <p class="mt-1 text-sm text-gray-500">
        Search and add users to send them emails.
      </p>
    </div>

    <!-- Role Quick Select -->
    <div class="border-t pt-4">
      <div class="flex items-center justify-between mb-3">
        <label class="block text-sm font-medium text-gray-700">
          Quick Select by Role
        </label>
        <button
          type="button"
          @click="clearAll"
          class="text-xs text-red-600 hover:text-red-800 transition-colors duration-150"
        >
          Clear All
        </button>
      </div>
      
      <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
        <button
          v-for="role in availableRoles"
          :key="role"
          type="button"
          @click="toggleRoleUsers(role)"
          :disabled="loadingRoles"
          class="flex items-center justify-between p-3 border border-gray-300 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-colors duration-150 group disabled:opacity-50 disabled:cursor-not-allowed"
          :class="{
            'border-blue-500 bg-blue-50': hasRoleSelected(role),
            'border-gray-300 bg-white': !hasRoleSelected(role),
          }"
        >
          <span class="text-sm font-medium text-gray-700 capitalize group-hover:text-blue-700">
            {{ role }}
          </span>
          <CheckIcon
            class="h-4 w-4"
            :class="hasRoleSelected(role) ? 'text-blue-600' : 'text-gray-400'"
          />
        </button>
      </div>
    </div>

    <!-- Error Message -->
    <div v-if="error" class="p-3 bg-red-50 border border-red-200 rounded-lg">
      <div class="flex items-center">
        <ExclamationTriangleIcon class="h-4 w-4 text-red-400 mr-2" />
        <span class="text-sm text-red-700">{{ error }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, watch, onMounted } from 'vue'
import {
  CheckIcon,
  ChevronUpDownIcon,
  PlusIcon,
  XMarkIcon,
  UserGroupIcon,
  ExclamationTriangleIcon,
} from '@heroicons/vue/24/outline'
import axios from 'axios'

const props = defineProps({
  modelValue: {
    type: Array,
    default: () => []
  },
  error: String
})

const emit = defineEmits(['update:modelValue'])

const query = ref('')
const selectedUser = ref(null)
const users = ref([])
const selectedUsers = ref([])
const availableRoles = ref(['admin', 'student', 'tutor', 'organization'])
const selectedRoles = ref(new Set())
const loading = ref(false)
const loadingRoles = ref(false)
let searchTimeout = null

// Fetch users based on search query
const searchUsers = async (search) => {
  if (!search || search.length < 2) {
    users.value = []
    loading.value = false
    return
  }

  loading.value = true
  try {
    const response = await axios.get(route('admin.users.search'), {
      params: { search, limit: 20 }
    })
    users.value = response.data
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
  searchTimeout = setTimeout(() => {
    searchUsers(query.value)
  }, 300)
}

// Add from search input (Enter key)
const addFromSearch = () => {
  if (filteredUsers.value.length === 1) {
    selectUser(filteredUsers.value[0])
  }
}

// Select user from dropdown
const selectUser = (user) => {
  selectedUser.value = user
  addSelectedUser()
}

// Filter users based on search query
const filteredUsers = computed(() => {
  return users.value.filter(user => 
    !selectedUsers.value.find(selected => selected.id === user.id)
  )
})

// Add selected user to the list
const addSelectedUser = () => {
  if (selectedUser.value && !selectedUsers.value.find(user => user.id === selectedUser.value.id)) {
    selectedUsers.value.push(selectedUser.value)
    updateModelValue()
    selectedUser.value = null
    query.value = ''
    users.value = []
  }
}

// Remove user from selected list
const removeUser = (userId) => {
  selectedUsers.value = selectedUsers.value.filter(user => user.id !== userId)
  updateSelectedRoles()
  updateModelValue()
}

// Clear all selected users
const clearAll = () => {
  selectedUsers.value = []
  selectedRoles.value.clear()
  updateModelValue()
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

// Update the v-model
const updateModelValue = () => {
  emit('update:modelValue', selectedUsers.value.map(user => user.id))
}

// Toggle users by role
const toggleRoleUsers = async (role) => {
  if (selectedRoles.value.has(role)) {
    // Remove users with this role
    selectedUsers.value = selectedUsers.value.filter(user => 
      !user.roles?.some(r => r.name === role)
    )
    selectedRoles.value.delete(role)
    updateModelValue()
  } else {
    // Add users with this role
    loadingRoles.value = true
    try {
      const response = await axios.get(route('admin.users.by-role', { role }))
      const roleUsers = response.data
      
      // Add users that aren't already selected
      roleUsers.forEach(user => {
        if (!selectedUsers.value.find(selected => selected.id === user.id)) {
          selectedUsers.value.push(user)
        }
      })
      
      selectedRoles.value.add(role)
      updateModelValue()
    } catch (error) {
      console.error('Error fetching users by role:', error)
    } finally {
      loadingRoles.value = false
    }
  }
}

// Check if a role has users selected
const hasRoleSelected = (role) => {
  return selectedRoles.value.has(role)
}

// Update selected roles based on current selection
const updateSelectedRoles = () => {
  selectedRoles.value.clear()
  selectedUsers.value.forEach(user => {
    user.roles?.forEach(role => {
      selectedRoles.value.add(role.name)
    })
  })
}

// Watch for external modelValue changes (if needed)
watch(() => props.modelValue, (newValue) => {
  if (newValue.length === 0 && selectedUsers.value.length > 0) {
    selectedUsers.value = []
    selectedRoles.value.clear()
  }
}, { deep: true })

// Initialize if there are pre-selected values
onMounted(async () => {
  if (props.modelValue && props.modelValue.length > 0) {
    try {
      const response = await axios.post(route('admin.users.by-ids'), {
        ids: props.modelValue
      })
      selectedUsers.value = response.data
      updateSelectedRoles()
    } catch (error) {
      console.error('Error fetching pre-selected users:', error)
    }
  }
})
</script>