<!-- resources/js/Pages/Admin/Users/Index.vue -->
<template>
  <AdminLayout>
    <Head title="User Management" />

    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex justify-between items-center">
            <div>
              <h1 class="text-3xl font-bold text-gray-900">User Management</h1>
              <p class="mt-2 text-gray-600">
                Manage students, tutors, and administrators
              </p>
            </div>
            <Link
              :href="route('admin.users.create')"
              class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
            >
              <PlusIcon class="h-4 w-4 mr-2" />
              Add User
            </Link>
          </div>
        </div>

        <!-- Filters -->
        <div class="mb-6 bg-white rounded-lg shadow p-4">
          <div class="flex flex-wrap gap-4 items-center">
            <div class="flex-1 min-w-[300px]">
              <input
                v-model="filters.search"
                type="text"
                placeholder="Search users..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                @input="search"
              />
            </div>
            <select
              v-model="filters.role"
              class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              @change="filter"
            >
              <option value="">All Roles</option>
              <option value="admin">Administrator</option>
              <option value="tutor">Tutor</option>
              <option value="student">Student</option>
            </select>
            <select
              v-model="filters.status"
              class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              @change="filter"
            >
              <option value="">All Status</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
        </div>

        <!-- Users Table -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
          <UserTable :users="users.data" />

          <!-- Pagination -->
          <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <Pagination :links="users.links" />
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import { PlusIcon } from '@heroicons/vue/24/outline'
import UserTable from '@/Components/Admin/UserTable.vue'
import Pagination from '@/Components/Pagination.vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineProps({
  users: Object,
  filters: Object,
})

const filters = ref({
  search: '',
  role: '',
  status: '',
})

let searchTimeout = null

const search = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    filter()
  }, 300)
}

const filter = () => {
  router.get(route('admin.users.index'), filters.value, {
    preserveState: true,
    replace: true,
  })
}

watch(
  () => [filters.value.role, filters.value.status],
  () => {
    filter()
  }
)
</script>
