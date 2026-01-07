<!-- resources/js/Pages/Student/Certificates/Index.vue -->
<template>
  <StudentLayout>
    <Head title="My Certificates" />

    <div class="py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex flex-col md:flex-row md:items-center justify-between">
            <div>
              <h1 class="text-3xl font-bold text-gray-900">My Certificates</h1>
              <p class="mt-2 text-gray-600">
                View and manage your earned certificates
              </p>
            </div>
            <div class="mt-4 md:mt-0">
              <button
                @click="exportAllCertificates"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 font-medium"
              >
                <ArrowDownTrayIcon class="h-4 w-4 mr-2" />
                Export All
              </button>
            </div>
          </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
              <div class="w-12 h-12 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-lg flex items-center justify-center mr-4">
                <DocumentTextIcon class="h-6 w-6 text-emerald-600" />
              </div>
              <div>
                <div class="text-sm text-gray-500">Total Certificates</div>
                <div class="text-2xl font-bold text-gray-900">{{ stats.total_certificates }}</div>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
              <div class="w-12 h-12 bg-gradient-to-br from-green-100 to-emerald-100 rounded-lg flex items-center justify-center mr-4">
                <CheckCircleIcon class="h-6 w-6 text-green-600" />
              </div>
              <div>
                <div class="text-sm text-gray-500">Active</div>
                <div class="text-2xl font-bold text-gray-900">{{ stats.active_certificates }}</div>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
              <div class="w-12 h-12 bg-gradient-to-br from-amber-100 to-orange-100 rounded-lg flex items-center justify-center mr-4">
                <ClockIcon class="h-6 w-6 text-amber-600" />
              </div>
              <div>
                <div class="text-sm text-gray-500">Expired</div>
                <div class="text-2xl font-bold text-gray-900">{{ stats.expired_certificates }}</div>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
              <div class="w-12 h-12 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-lg flex items-center justify-center mr-4">
                <ArrowDownTrayIcon class="h-6 w-6 text-blue-600" />
              </div>
              <div>
                <div class="text-sm text-gray-500">Downloads</div>
                <div class="text-2xl font-bold text-gray-900">{{ stats.total_downloads }}</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
          <div class="p-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between space-y-4 md:space-y-0">
              <div class="flex-1 md:mr-4">
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" />
                  </div>
                  <input
                    v-model="filters.search"
                    type="text"
                    placeholder="Search certificates..."
                    class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                  />
                </div>
              </div>
              <div class="flex space-x-4">
                <select
                  v-model="filters.status"
                  class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                >
                  <option value="">All Status</option>
                  <option value="active">Active</option>
                  <option value="expired">Expired</option>
                  <option value="pending">Pending</option>
                </select>
                <select
                  v-model="filters.sort"
                  class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                >
                  <option value="newest">Newest First</option>
                  <option value="oldest">Oldest First</option>
                  <option value="course">Course Name</option>
                </select>
                <button
                  @click="resetFilters"
                  class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
                >
                  Reset
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Certificates Grid -->
        <div v-if="certificates.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="certificate in certificates.data"
            :key="certificate.id"
            class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow duration-300"
          >
            <!-- Certificate Header -->
            <div class="p-6 border-b border-gray-100">
              <div class="flex justify-between items-start mb-4">
                <div>
                  <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold mb-2"
                    :class="getStatusClass(certificate.status)"
                  >
                    {{ certificate.status }}
                  </span>
                  <h3 class="text-lg font-semibold text-gray-900 line-clamp-2">
                    {{ certificate.course?.title }}
                  </h3>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-lg flex items-center justify-center text-white font-bold">
                  C
                </div>
              </div>
              <p class="text-sm text-gray-600">
                {{ certificate.certificate_number }}
              </p>
            </div>

            <!-- Certificate Details -->
            <div class="p-6">
              <div class="space-y-3 mb-6">
                <div class="flex justify-between text-sm">
                  <span class="text-gray-500">Issued:</span>
                  <span class="font-medium text-gray-900">{{ formatDate(certificate.issue_date) }}</span>
                </div>
                <div v-if="certificate.expiry_date" class="flex justify-between text-sm">
                  <span class="text-gray-500">Expires:</span>
                  <span class="font-medium text-gray-900">{{ formatDate(certificate.expiry_date) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-gray-500">Downloads:</span>
                  <span class="font-medium text-gray-900">{{ certificate.download_count }}</span>
                </div>
                <div v-if="certificate.organization" class="flex justify-between text-sm">
                  <span class="text-gray-500">Issued by:</span>
                  <span class="font-medium text-gray-900">{{ certificate.organization.name }}</span>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="flex space-x-3">
                <Link
                  :href="route('student.certificates.show', certificate.id)"
                  class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-lg font-medium hover:from-emerald-600 hover:to-teal-700 transition-colors"
                >
                  <EyeIcon class="h-4 w-4 mr-2" />
                  View
                </Link>
                <button
                  @click="downloadCertificate(certificate)"
                  class="flex-1 inline-flex items-center justify-center px-4 py-2 border border-emerald-300 text-emerald-700 rounded-lg font-medium hover:bg-emerald-50 transition-colors"
                >
                  <ArrowDownTrayIcon class="h-4 w-4 mr-2" />
                  Download
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
          <div class="w-24 h-24 mx-auto mb-6 text-gray-300">
            <DocumentTextIcon class="w-full h-full" />
          </div>
          <h3 class="text-xl font-semibold text-gray-900 mb-2">No Certificates Yet</h3>
          <p class="text-gray-600 mb-6 max-w-md mx-auto">
            You haven't earned any certificates yet. Complete courses to earn certificates.
          </p>
          <Link
            :href="route('student.courses.index')"
            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-lg font-medium hover:shadow-lg transition-all"
          >
            <AcademicCapIcon class="h-5 w-5 mr-2" />
            Browse Courses
          </Link>
        </div>

        <!-- Pagination -->
        <div v-if="certificates.data.length > 0" class="mt-8">
          <div class="flex justify-center">
            <Pagination :links="certificates.links" />
          </div>
        </div>

        <!-- Certificate Tips -->
        <div class="mt-8 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl shadow-sm border border-blue-200 p-6">
          <div class="flex items-start">
            <InformationCircleIcon class="h-6 w-6 text-blue-600 mr-3 flex-shrink-0 mt-1" />
            <div>
              <h4 class="text-lg font-semibold text-gray-900 mb-2">About Certificates</h4>
              <ul class="space-y-2 text-sm text-gray-700">
                <li>• Certificates are issued when you complete a course with all requirements met</li>
                <li>• Each certificate has a unique verification URL for authenticity checks</li>
                <li>• You can share certificates on LinkedIn and other professional networks</li>
                <li>• Certificates expire after 2 years, but can be renewed by retaking the course</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </StudentLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import {
  DocumentTextIcon,
  CheckCircleIcon,
  ClockIcon,
  ArrowDownTrayIcon,
  EyeIcon,
  AcademicCapIcon,
  MagnifyingGlassIcon,
  InformationCircleIcon,
} from '@heroicons/vue/24/outline'
import { ref, watch, reactive } from 'vue'

const props = defineProps({
  certificates: Object,
  stats: Object,
  filters: Object,
})

// Filter state
const filters = reactive({
  search: props.filters.search || '',
  status: props.filters.status || '',
  sort: props.filters.sort || 'newest',
})

// Watch for filter changes
watch(filters, () => {
  router.get(route('student.certificates.index'), filters, {
    preserveState: true,
    preserveScroll: true,
  })
}, { deep: true })

// Format date
const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

// Get status class
const getStatusClass = (status) => {
  const classes = {
    active: 'bg-green-100 text-green-800',
    expired: 'bg-amber-100 text-amber-800',
    pending: 'bg-blue-100 text-blue-800',
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

// Download certificate
const downloadCertificate = (certificate) => {
  router.get(route('student.certificates.download', certificate.id))
}

// Export all certificates
const exportAllCertificates = () => {
  router.post(route('student.certificates.export-all'))
}

// Reset filters
const resetFilters = () => {
  filters.search = ''
  filters.status = ''
  filters.sort = 'newest'
}
</script>