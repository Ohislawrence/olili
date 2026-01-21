
<template>
  <AdminLayout>
    <Head title="Exam Preps" />

    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="md:flex md:items-center md:justify-between mb-8">
          <div class="flex-1 min-w-0">
            <h1 class="text-2xl font-bold text-gray-900">Exam Preparations</h1>
            <p class="mt-1 text-sm text-gray-600">
              Manage practice exams for students to prepare for their actual exams
            </p>
          </div>
          <div class="mt-4 md:mt-0 flex space-x-3">
            <Link
              :href="route('admin.exam-preps.create')"
              class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 border border-transparent rounded-lg font-semibold text-white shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200"
            >
              <PlusIcon class="h-4 w-4 mr-2" />
              New Exam Prep
            </Link>
          </div>
        </div>

        <!-- Filters -->
        <div class="mb-6 bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
              <label for="search" class="sr-only">Search</label>
              <div class="relative rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" />
                </div>
                <input
                  id="search"
                  v-model="filters.search"
                  type="search"
                  placeholder="Search exam preps..."
                  class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500"
                />
              </div>
            </div>

            <!-- Status Filter -->
            <div>
              <select
                v-model="filters.status"
                class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500"
              >
                <option value="">All Statuses</option>
                <option
                  v-for="(label, value) in statuses"
                  :key="value"
                  :value="value"
                >
                  {{ label }}
                </option>
              </select>
            </div>

            <!-- Exam Board Filter -->
            <div>
              <select
                v-model="filters.exam_board_id"
                class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500"
              >
                <option value="">All Exam Boards</option>
                <option
                  v-for="examBoard in examBoards"
                  :key="examBoard.id"
                  :value="examBoard.id"
                >
                  {{ examBoard.name }}
                </option>
              </select>
            </div>

            <!-- Subject Filter -->
            <div>
              <select
                v-model="filters.subject_id"
                class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500"
              >
                <option value="">All Subjects</option>
                <option
                  v-for="subject in subjects"
                  :key="subject.id"
                  :value="subject.id"
                >
                  {{ subject.name }}
                </option>
              </select>
            </div>
          </div>
        </div>

        <!-- Exam Preps List -->
        <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
          <div v-if="examPreps.data.length > 0" class="divide-y divide-gray-200">
            <div
              v-for="examPrep in examPreps.data"
              :key="examPrep.id"
              class="p-6 hover:bg-gray-50 transition-colors duration-150"
            >
              <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                  <div class="flex items-center space-x-3 mb-2">
                    <h3 class="text-lg font-semibold text-gray-900 truncate">
                      <Link
                        :href="route('admin.exam-preps.show', examPrep.id)"
                        class="hover:text-emerald-600 transition-colors"
                      >
                        {{ examPrep.name }}
                      </Link>
                    </h3>
                    <span
                      :class="[
                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                        examPrep.status === 'active' ? 'bg-emerald-100 text-emerald-800' :
                        examPrep.status === 'draft' ? 'bg-gray-100 text-gray-800' :
                        'bg-amber-100 text-amber-800'
                      ]"
                    >
                      {{ statuses[examPrep.status] }}
                    </span>
                    <span
                      v-if="examPrep.is_public"
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                    >
                      Public
                    </span>
                  </div>

                  <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                    {{ examPrep.description }}
                  </p>

                  <div class="flex items-center space-x-4 text-sm text-gray-500">
                    <div class="flex items-center">
                      <AcademicCapIcon class="h-4 w-4 mr-1" />
                      <span>{{ examPrep.exam_board?.name }}</span>
                    </div>
                    <div class="flex items-center">
                      <QuestionMarkCircleIcon class="h-4 w-4 mr-1" />
                      <span>{{ examPrep.total_questions }} questions</span>
                    </div>
                    <div class="flex items-center">
                      <ClockIcon class="h-4 w-4 mr-1" />
                      <span>{{ examPrep.time_limit_minutes }} minutes</span>
                    </div>
                    <div class="flex items-center">
                      <UserGroupIcon class="h-4 w-4 mr-1" />
                      <span>{{ examPrep.enrolled_count }} enrolled</span>
                    </div>
                    <div class="flex items-center">
                      <CheckCircleIcon class="h-4 w-4 mr-1" />
                      <span>{{ examPrep.passing_score }}% passing</span>
                    </div>
                  </div>
                </div>

                <div class="flex items-center space-x-2 ml-4">
                  <Link
                    :href="route('admin.exam-preps.show', examPrep.id)"
                    class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
                  >
                    View
                  </Link>
                  <Link
                    :href="route('admin.exam-preps.edit', examPrep.id)"
                    class="inline-flex items-center px-3 py-2 border border-emerald-300 shadow-sm text-sm font-medium rounded-md text-emerald-700 bg-white hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
                  >
                    Edit
                  </Link>
                  <button
                    v-if="examPrep.status === 'draft'"
                    @click="publishExamPrep(examPrep)"
                    class="inline-flex items-center px-3 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
                  >
                    Publish
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-else class="text-center py-12">
            <QuestionMarkCircleIcon class="mx-auto h-12 w-12 text-gray-400" />
            <h3 class="mt-2 text-sm font-medium text-gray-900">No exam preps</h3>
            <p class="mt-1 text-sm text-gray-500">
              Get started by creating a new exam prep.
            </p>
            <div class="mt-6">
              <Link
                :href="route('admin.exam-preps.create')"
                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
              >
                <PlusIcon class="h-4 w-4 mr-2" />
                New Exam Prep
              </Link>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="examPreps.data.length > 0" class="mt-6">
          <Pagination :links="examPreps.links" />
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import Pagination from '@/Components/Pagination.vue'
import {
  PlusIcon,
  MagnifyingGlassIcon,
  AcademicCapIcon,
  QuestionMarkCircleIcon,
  ClockIcon,
  UserGroupIcon,
  CheckCircleIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  examPreps: Object,
  filters: Object,
  examBoards: Array,
  subjects: Array,
  statuses: Object,
})

const filters = ref({
  search: props.filters.search || '',
  status: props.filters.status || '',
  exam_board_id: props.filters.exam_board_id || '',
  subject_id: props.filters.subject_id || '',
})

// Watch filters and reload data
watch(filters, () => {
  router.get(route('admin.exam-preps.index'), filters.value, {
    preserveState: true,
    replace: true,
  })
}, { deep: true })

// Publish exam prep
const publishExamPrep = async (examPrep) => {
  if (confirm(`Publish "${examPrep.name}"? This will make it available to students.`)) {
    await router.post(route('admin.exam-preps.publish', examPrep.id), {}, {
      preserveScroll: true,
      onSuccess: () => {
        router.reload({ only: ['examPreps'] })
      },
    })
  }
}
</script>
