<!-- resources/js/Components/Admin/UserTable.vue -->
<template>
  <table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
      <tr>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
          User
        </th>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
          Role
        </th>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
          Courses
        </th>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
          Status
        </th>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
          Last Login
        </th>
        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
          Actions
        </th>
      </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
      <tr
        v-for="user in users"
        :key="user.id"
        class="hover:bg-gray-50"
      >
        <td class="px-6 py-4 whitespace-nowrap">
          <div class="flex items-center">
            <div class="flex-shrink-0 h-10 w-10">
              <img
                class="h-10 w-10 rounded-full"
                :src="user.profile_photo_url"
                :alt="user.name"
              />
            </div>
            <div class="ml-4">
              <div class="text-sm font-medium text-gray-900">
                {{ user.name }}
              </div>
              <div class="text-sm text-gray-500">
                {{ user.email }}
              </div>
            </div>
          </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
          <span
            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
            :class="roleClasses[user.roles[0]?.name]"
          >
            {{ user.roles[0]?.name }}
          </span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
          {{ user.enrollments_count }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
          <span
            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
            :class="user.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
          >
            {{ user.is_active ? 'Active' : 'Inactive' }}
          </span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
         <Link :href="route('admin.users.login-history', user.id)" >{{ formatLastLogin(user.last_login_at) }}</Link>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
          <div class="flex justify-end space-x-2">
            <Link
              :href="route('admin.users.show', user.id)"
              class="text-blue-600 hover:text-blue-900"
            >
              View
            </Link>
            <Link
              :href="route('admin.users.edit', user.id)"
              class="text-indigo-600 hover:text-indigo-900"
            >
              Edit
            </Link>
            <button
              @click="toggleStatus(user)"
              class="text-orange-600 hover:text-orange-900"
            >
              {{ user.is_active ? 'Deactivate' : 'Activate' }}
            </button>
            <button
              v-if="user.id !== $page.props.auth.user.id"
              @click="deleteUser(user)"
              class="text-red-600 hover:text-red-900"
            >
              Delete
            </button>
          </div>
        </td>
      </tr>
    </tbody>
  </table>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'

defineProps({
  users: Array,
})

const roleClasses = {
  admin: 'bg-purple-100 text-purple-800',
  tutor: 'bg-blue-100 text-blue-800',
  student: 'bg-green-100 text-green-800',
}

const formatLastLogin = (lastLoginAt) => {
  if (!lastLoginAt) return 'Never'

  const now = new Date()
  const lastLogin = new Date(lastLoginAt)
  const diffTime = Math.abs(now - lastLogin)
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))

  if (diffDays === 1) return 'Today'
  if (diffDays === 2) return 'Yesterday'
  if (diffDays <= 7) return `${diffDays - 1} days ago`
  if (diffDays <= 30) return `${Math.floor(diffDays / 7)} weeks ago`

  return lastLogin.toLocaleDateString()
}

const toggleStatus = (user) => {
  router.post(route('admin.users.toggle-status', user.id))
}

const deleteUser = (user) => {
  if (confirm(`Are you sure you want to delete ${user.name}? This action cannot be undone.`)) {
    router.delete(route('admin.users.destroy', user.id))
  }
}
</script>
