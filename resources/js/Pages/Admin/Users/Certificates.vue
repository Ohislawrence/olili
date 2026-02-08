<!-- resources/js/Pages/Admin/Users/Certificates.vue - UPDATED -->
<template>
  <AdminLayout>
    <Head :title="`Certificates - ${user.name}`" />

    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex justify-between items-center">
            <div>
              <div class="flex items-center space-x-2 mb-2">
                <Link
                  :href="route('admin.users.show', user.id)"
                  class="text-gray-500 hover:text-gray-700"
                >
                  <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                  </svg>
                </Link>
                <h1 class="text-3xl font-bold text-gray-900">{{ user.name }}</h1>
              </div>
              <p class="text-gray-600">Certificate Management</p>
            </div>
            <div class="flex space-x-3">
              <Link
                :href="route('admin.users.show', user.id)"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
              >
                Back to Profile
              </Link>
              <Link
                :href="route('admin.certificates.generate', { user_id: user.id })"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700"
              >
                <PlusIcon class="h-4 w-4 mr-2" />
                Generate Certificate
              </Link>
            </div>
          </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                  <DocumentTextIcon class="h-6 w-6 text-blue-600" />
                </div>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Total Certificates</p>
                <p class="text-2xl font-semibold text-gray-900">{{ stats.total }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                  <CheckCircleIcon class="h-6 w-6 text-green-600" />
                </div>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Active</p>
                <p class="text-2xl font-semibold text-gray-900">{{ stats.active }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                  <ClockIcon class="h-6 w-6 text-amber-600" />
                </div>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Expired</p>
                <p class="text-2xl font-semibold text-gray-900">{{ stats.expired }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                  <ArrowDownTrayIcon class="h-6 w-6 text-gray-600" />
                </div>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Downloads</p>
                <p class="text-2xl font-semibold text-gray-900">{{ stats.total_downloads }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Additional Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-500">Certificate Rate</p>
                <p class="text-2xl font-semibold text-gray-900">{{ stats.certificate_rate }}%</p>
              </div>
              <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <PercentBadgeIcon class="h-6 w-6 text-purple-600" />
              </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">
              {{ stats.courses_with_certificates }} of {{ stats.completed_courses }} completed courses
            </p>
          </div>

          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-500">Recent (30d)</p>
                <p class="text-2xl font-semibold text-gray-900">{{ stats.recent_30d }}</p>
              </div>
              <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                <CalendarDaysIcon class="h-6 w-6 text-orange-600" />
              </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">Certificates issued in last 30 days</p>
          </div>

          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-500">Expiring Soon</p>
                <p class="text-2xl font-semibold text-gray-900">{{ stats.expiry_distribution.expiring_soon || 0 }}</p>
              </div>
              <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                <ExclamationTriangleIcon class="h-6 w-6 text-red-600" />
              </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">Certificates expiring within 30 days</p>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white shadow rounded-lg mb-8">
          <div class="px-6 py-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
              <button
                @click="generateAllEligibleCertificates"
                class="flex items-center justify-center px-4 py-3 border border-emerald-300 rounded-lg bg-emerald-50 hover:bg-emerald-100"
              >
                <PlusCircleIcon class="h-5 w-5 text-emerald-600 mr-2" />
                <span class="text-sm font-medium text-emerald-700">Generate All Eligible</span>
              </button>
              <button
                @click="exportCertificates"
                class="flex items-center justify-center px-4 py-3 border border-blue-300 rounded-lg bg-blue-50 hover:bg-blue-100"
              >
                <ArrowDownTrayIcon class="h-5 w-5 text-blue-600 mr-2" />
                <span class="text-sm font-medium text-blue-700">Export Data</span>
              </button>
              <button
                @click="showBulkActions = !showBulkActions"
                class="flex items-center justify-center px-4 py-3 border border-purple-300 rounded-lg bg-purple-50 hover:bg-purple-100"
              >
                <ArchiveBoxIcon class="h-5 w-5 text-purple-600 mr-2" />
                <span class="text-sm font-medium text-purple-700">Bulk Actions</span>
              </button>
              <button
                @click="showStats = !showStats"
                class="flex items-center justify-center px-4 py-3 border border-amber-300 rounded-lg bg-amber-50 hover:bg-amber-100"
              >
                <ChartBarIcon class="h-5 w-5 text-amber-600 mr-2" />
                <span class="text-sm font-medium text-amber-700">View Analytics</span>
              </button>
            </div>

            <!-- Bulk Actions Dropdown -->
            <div v-if="showBulkActions" class="mt-4 p-4 border border-gray-200 rounded-lg bg-gray-50">
              <h4 class="text-sm font-medium text-gray-900 mb-3">Bulk Actions</h4>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <button
                  @click="bulkRegenerateImages"
                  class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                >
                  Regenerate Images
                </button>
                <button
                  @click="bulkSendCertificates"
                  class="px-3 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-purple-600 hover:bg-purple-700"
                >
                  Send to User
                </button>
                <button
                  @click="bulkExportPDF"
                  class="px-3 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700"
                >
                  Export as PDFs
                </button>
              </div>
            </div>

            <!-- Stats Panel -->
            <div v-if="showStats" class="mt-4 p-4 border border-gray-200 rounded-lg bg-gray-50">
              <h4 class="text-sm font-medium text-gray-900 mb-3">Certificate Analytics</h4>
              <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                  <p class="text-xs text-gray-500">Avg. Downloads</p>
                  <p class="text-lg font-semibold text-gray-900">
                    {{ Math.round(stats.total_downloads / (stats.total || 1)) }}
                  </p>
                </div>
                <div>
                  <p class="text-xs text-gray-500">Expiry Rate</p>
                  <p class="text-lg font-semibold text-gray-900">
                    {{ Math.round((stats.expired / (stats.total || 1)) * 100) }}%
                  </p>
                </div>
                <div>
                  <p class="text-xs text-gray-500">Active Rate</p>
                  <p class="text-lg font-semibold text-gray-900">
                    {{ Math.round((stats.active / (stats.total || 1)) * 100) }}%
                  </p>
                </div>
                <div>
                  <p class="text-xs text-gray-500">Validity Period</p>
                  <p class="text-lg font-semibold text-gray-900">2 Years</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Filters -->
        <div class="bg-white shadow rounded-lg mb-8">
          <div class="px-6 py-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Filters</h3>
            <form @submit.prevent="applyFilters" class="grid grid-cols-1 md:grid-cols-4 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input
                  v-model="form.search"
                  type="text"
                  placeholder="Certificate number, course name..."
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select
                  v-model="form.status"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="">All Statuses</option>
                  <option v-for="status in statuses" :key="status" :value="status">
                    {{ status.charAt(0).toUpperCase() + status.slice(1) }}
                  </option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Course</label>
                <select
                  v-model="form.course_id"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="">All Courses</option>
                  <option v-for="course in courses" :key="course.id" :value="course.id">
                    {{ course.title }}
                  </option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                <div class="flex space-x-2">
                  <input
                    v-model="form.date_from"
                    type="date"
                    class="w-1/2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    placeholder="From"
                  />
                  <input
                    v-model="form.date_to"
                    type="date"
                    class="w-1/2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    placeholder="To"
                  />
                </div>
              </div>
              <div class="md:col-span-4 flex justify-end space-x-3">
                <button
                  type="button"
                  @click="resetFilters"
                  class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                >
                  Reset
                </button>
                <button
                  type="submit"
                  class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700"
                >
                  Apply Filters
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Certificates Table -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Certificates</h3>
            <div class="flex items-center space-x-3">
              <span class="text-sm text-gray-500">
                {{ certificates.total }} certificate(s)
              </span>
              <div class="relative" v-if="selectedCertificates.length > 0">
                <button
                  @click="showBulkActionMenu = !showBulkActionMenu"
                  class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                >
                  <CheckBadgeIcon class="h-4 w-4 mr-2" />
                  {{ selectedCertificates.length }} selected
                  <ChevronDownIcon class="h-4 w-4 ml-2" />
                </button>
                <div
                  v-if="showBulkActionMenu"
                  class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10 border border-gray-200"
                >
                  <button
                    @click="bulkDownloadPDFs"
                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  >
                    Download PDFs
                  </button>
                  <button
                    @click="bulkDownloadImages"
                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  >
                    Download Images
                  </button>
                  <button
                    @click="bulkSendSelected"
                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  >
                    Send to User
                  </button>
                  <button
                    @click="bulkRegenerateSelected"
                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  >
                    Regenerate Images
                  </button>
                  <button
                    @click="bulkUpdateStatus"
                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  >
                    Update Status
                  </button>
                </div>
              </div>
              <button
                @click="exportCertificates"
                class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
              >
                <ArrowDownTrayIcon class="h-4 w-4 mr-2" />
                Export
              </button>
            </div>
          </div>

          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-8">
                    <input
                      type="checkbox"
                      v-model="selectAll"
                      @change="toggleSelectAll"
                      class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                    />
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Certificate
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Course
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Issue Date
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Status
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Downloads
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="certificate in certificates.data" :key="certificate.id">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <input
                      type="checkbox"
                      :value="certificate.id"
                      v-model="selectedCertificates"
                      class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                    />
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-lg flex items-center justify-center text-white font-bold mr-3">
                        C
                      </div>
                      <div>
                        <div class="text-sm font-medium text-gray-900">{{ certificate.certificate_number }}</div>
                        <div class="text-sm text-gray-500">{{ certificate.title }}</div>
                        <div class="flex items-center mt-1 space-x-2">
                          <a
                            v-if="certificate.image_url"
                            :href="certificate.image_url"
                            target="_blank"
                            class="text-xs text-blue-600 hover:text-blue-800"
                            title="View Image"
                          >
                            <PhotoIcon class="h-3 w-3 inline" />
                          </a>
                          <a
                            v-if="certificate.pdf_url"
                            :href="certificate.pdf_url"
                            target="_blank"
                            class="text-xs text-red-600 hover:text-red-800"
                            title="View PDF"
                          >
                            <DocumentIcon class="h-3 w-3 inline" />
                          </a>
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ certificate.course?.title }}</div>
                    <div v-if="certificate.organization" class="text-xs text-gray-500">
                      Issued by: {{ certificate.organization.name }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ formatDate(certificate.issue_date) }}</div>
                    <div v-if="certificate.expiry_date" class="text-xs text-gray-500">
                      Expires: {{ formatDate(certificate.expiry_date) }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                        :class="getStatusClass(certificate.status)"
                      >
                        {{ certificate.status }}
                      </span>
                      <button
                        v-if="certificate.is_expired"
                        @click="renewCertificate(certificate)"
                        class="ml-2 text-xs text-emerald-600 hover:text-emerald-900"
                        title="Renew Certificate"
                      >
                        Renew
                      </button>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <span class="text-sm text-gray-900 mr-2">{{ certificate.download_count }}</span>
                      <button
                        @click="downloadCertificate(certificate)"
                        class="text-blue-600 hover:text-blue-900"
                        title="Download"
                      >
                        <ArrowDownTrayIcon class="h-4 w-4" />
                      </button>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex items-center space-x-2">
                      <Link
                        :href="route('admin.certificates.show', certificate.id)"
                        class="text-blue-600 hover:text-blue-900"
                        title="View Details"
                      >
                        <EyeIcon class="h-5 w-5" />
                      </Link>
                      <a
                        :href="certificate.verification_url"
                        target="_blank"
                        class="text-emerald-600 hover:text-emerald-900"
                        title="Verify Certificate"
                      >
                        <CheckCircleIcon class="h-5 w-5" />
                      </a>
                      <button
                        @click="sendCertificate(certificate)"
                        class="text-purple-600 hover:text-purple-900"
                        title="Send to User"
                      >
                        <PaperAirplaneIcon class="h-5 w-5" />
                      </button>
                      <button
                        @click="regenerateImage(certificate)"
                        class="text-orange-600 hover:text-orange-900"
                        title="Regenerate Image"
                      >
                        <ArrowPathIcon class="h-5 w-5" />
                      </button>
                      <button
                        @click="updateStatus(certificate)"
                        class="text-amber-600 hover:text-amber-900"
                        title="Update Status"
                      >
                        <PencilIcon class="h-5 w-5" />
                      </button>
                      <button
                        @click="deleteCertificate(certificate)"
                        class="text-red-600 hover:text-red-900"
                        title="Delete"
                      >
                        <TrashIcon class="h-5 w-5" />
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Empty State -->
          <div v-if="certificates.data.length === 0" class="text-center py-12">
            <DocumentTextIcon class="mx-auto h-12 w-12 text-gray-400" />
            <h3 class="mt-2 text-sm font-medium text-gray-900">No certificates</h3>
            <p class="mt-1 text-sm text-gray-500">
              {{ user.name }} hasn't earned any certificates yet.
            </p>
            <div class="mt-6">
              <Link
                :href="route('admin.certificates.generate', { user_id: user.id })"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700"
              >
                <PlusIcon class="h-4 w-4 mr-2" />
                Generate First Certificate
              </Link>
            </div>
          </div>

          <!-- Pagination -->
          <div v-if="certificates.data.length > 0" class="px-6 py-4 border-t border-gray-200">
            <Pagination :links="certificates.links" />
          </div>
        </div>
      </div>
    </div>

    <!-- All Modals Below -->
    <!-- Status Update Modal -->
    <Modal :show="showStatusModal" @close="showStatusModal = false">
      <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
          Update Certificate Status
        </h3>
        <div v-if="selectedCertificate" class="space-y-4">
          <div class="p-3 bg-gray-50 rounded-lg">
            <p class="text-sm font-medium text-gray-900">{{ selectedCertificate.certificate_number }}</p>
            <p class="text-sm text-gray-500">{{ selectedCertificate.course?.title }}</p>
            <p class="text-xs text-gray-500">Current status: {{ selectedCertificate.status }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">New Status</label>
            <select
              v-model="statusForm.status"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="active">Active</option>
              <option value="expired">Expired</option>
              <option value="revoked">Revoked</option>
              <option value="pending">Pending</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Reason (Optional)</label>
            <textarea
              v-model="statusForm.reason"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              placeholder="Reason for status change..."
            ></textarea>
          </div>
          <div class="flex justify-end space-x-3">
            <button
              @click="showStatusModal = false"
              class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
            >
              Cancel
            </button>
            <button
              @click="submitStatusUpdate"
              class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700"
            >
              Update Status
            </button>
          </div>
        </div>
      </div>
    </Modal>

    <!-- Send Certificate Modal -->
    <Modal :show="showSendModal" @close="showSendModal = false">
      <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
          Send Certificate to User
        </h3>
        <div v-if="selectedCertificate" class="space-y-4">
          <div class="p-3 bg-gray-50 rounded-lg">
            <p class="text-sm font-medium text-gray-900">{{ selectedCertificate.certificate_number }}</p>
            <p class="text-sm text-gray-500">{{ selectedCertificate.course?.title }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Method</label>
            <div class="space-y-2">
              <label class="inline-flex items-center">
                <input v-model="sendForm.method" type="radio" value="email" class="h-4 w-4 text-blue-600 border-gray-300" />
                <span class="ml-2 text-sm text-gray-700">Email Only</span>
              </label>
              <label class="inline-flex items-center">
                <input v-model="sendForm.method" type="radio" value="notification" class="h-4 w-4 text-blue-600 border-gray-300" />
                <span class="ml-2 text-sm text-gray-700">In-app Notification Only</span>
              </label>
              <label class="inline-flex items-center">
                <input v-model="sendForm.method" type="radio" value="both" class="h-4 w-4 text-blue-600 border-gray-300" />
                <span class="ml-2 text-sm text-gray-700">Both Email and Notification</span>
              </label>
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Custom Message (Optional)</label>
            <textarea
              v-model="sendForm.message"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              placeholder="Add a personal message..."
            ></textarea>
          </div>
          <div class="flex justify-end space-x-3">
            <button
              @click="showSendModal = false"
              class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
            >
              Cancel
            </button>
            <button
              @click="submitSendCertificate"
              class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700"
            >
              Send Certificate
            </button>
          </div>
        </div>
      </div>
    </Modal>

    <!-- Generate All Eligible Modal -->
    <Modal :show="showGenerateAllModal" @close="showGenerateAllModal = false">
      <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
          Generate All Eligible Certificates
        </h3>
        <div class="space-y-4">
          <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <p class="text-sm text-blue-800">
              This will generate certificates for all completed courses where the user is eligible but hasn't received a certificate yet.
            </p>
          </div>
          <div v-if="eligibleCourses && eligibleCourses.length > 0">
            <label class="block text-sm font-medium text-gray-700 mb-2">Select Courses</label>
            <div class="max-h-60 overflow-y-auto border border-gray-300 rounded-lg p-3">
              <label class="flex items-center mb-2">
                <input
                  type="checkbox"
                  v-model="selectAllCourses"
                  @change="toggleSelectAllCourses"
                  class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                />
                <span class="ml-2 text-sm font-medium text-gray-900">Select All</span>
              </label>
              <div v-for="course in eligibleCourses" :key="course.id" class="flex items-center mb-2">
                <input
                  type="checkbox"
                  :value="course.id"
                  v-model="selectedCourses"
                  class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                />
                <div class="ml-3">
                  <p class="text-sm font-medium text-gray-900">{{ course.title }}</p>
                  <p class="text-xs text-gray-500">Completed: {{ formatDate(course.completed_date) }}</p>
                </div>
              </div>
            </div>
            <p class="text-sm text-gray-500 mt-2">
              {{ selectedCourses.length }} of {{ eligibleCourses.length }} courses selected
            </p>
          </div>
          <div v-else class="text-center py-6">
            <CheckCircleIcon class="mx-auto h-12 w-12 text-gray-400" />
            <p class="mt-2 text-sm text-gray-500">No eligible courses found.</p>
          </div>
          <div class="flex justify-end space-x-3">
            <button
              @click="showGenerateAllModal = false"
              class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
            >
              Cancel
            </button>
            <button
              @click="submitGenerateAll"
              :disabled="selectedCourses.length === 0"
              :class="[
                'px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white',
                selectedCourses.length === 0
                  ? 'bg-emerald-400 cursor-not-allowed'
                  : 'bg-emerald-600 hover:bg-emerald-700'
              ]"
            >
              Generate Certificates
            </button>
          </div>
        </div>
      </div>
    </Modal>

    <!-- Renew Certificate Modal -->
    <Modal :show="showRenewModal" @close="showRenewModal = false">
      <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
          Renew Certificate
        </h3>
        <div v-if="selectedCertificate" class="space-y-4">
          <div class="p-3 bg-gray-50 rounded-lg">
            <p class="text-sm font-medium text-gray-900">{{ selectedCertificate.certificate_number }}</p>
            <p class="text-sm text-gray-500">{{ selectedCertificate.course?.title }}</p>
            <p class="text-xs text-gray-500">Expired on: {{ formatDate(selectedCertificate.expiry_date) }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">New Expiry Date</label>
            <input
              v-model="renewForm.expiry_date"
              type="date"
              :min="new Date().toISOString().split('T')[0]"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
            />
            <p class="text-xs text-gray-500 mt-1">Default: 2 years from today</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Renewal</label>
            <textarea
              v-model="renewForm.reason"
              rows="2"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              placeholder="Optional..."
            ></textarea>
          </div>
          <div class="flex justify-end space-x-3">
            <button
              @click="showRenewModal = false"
              class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
            >
              Cancel
            </button>
            <button
              @click="submitRenewCertificate"
              class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700"
            >
              Renew Certificate
            </button>
          </div>
        </div>
      </div>
    </Modal>
  </AdminLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import Modal from '@/Components/Modal.vue'
import {
  DocumentTextIcon,
  CheckCircleIcon,
  ClockIcon,
  ArrowDownTrayIcon,
  EyeIcon,
  PaperAirplaneIcon,
  PencilIcon,
  TrashIcon,
  PlusIcon,
  PhotoIcon,
  DocumentIcon,
  ArrowPathIcon,
  PlusCircleIcon,
  CalendarDaysIcon,
  ExclamationTriangleIcon,
  ArchiveBoxIcon,
  ChartBarIcon,
  CheckBadgeIcon,
  ChevronDownIcon,
  PercentBadgeIcon,
} from '@heroicons/vue/24/outline'
import { ref, reactive, watch, computed } from 'vue'

const props = defineProps({
  user: Object,
  certificates: Object,
  filters: Object,
  stats: Object,
  courses: Array,
  statuses: Array,
  eligibleCourses: {
    type: Array,
    default: () => []
  },
  organizations: {
    type: Array,
    default: () => []
  },
})

// Filter form
const form = reactive({
  search: props.filters.search || '',
  status: props.filters.status || '',
  course_id: props.filters.course_id || '',
  date_from: props.filters.date_from || '',
  date_to: props.filters.date_to || '',
})

// Status modal
const showStatusModal = ref(false)
const selectedCertificate = ref(null)
const statusForm = reactive({
  status: 'active',
  reason: '',
})

// Send modal
const showSendModal = ref(false)
const sendForm = reactive({
  method: 'both',
  message: '',
})

// Generate All modal
const showGenerateAllModal = ref(false)
const selectedCourses = ref([])
const selectAllCourses = ref(false)

// Renew modal
const showRenewModal = ref(false)
const renewForm = reactive({
  expiry_date: new Date(new Date().setFullYear(new Date().getFullYear() + 2)).toISOString().split('T')[0],
  reason: '',
})

// Bulk selection
const selectedCertificates = ref([])
const selectAll = ref(false)
const showBulkActionMenu = ref(false)

// Toggles
const showBulkActions = ref(false)
const showStats = ref(false)

// Apply filters
const applyFilters = () => {
  router.get(route('admin.users.certificates', props.user.id), form, {
    preserveState: true,
    preserveScroll: true,
  })
}

// Reset filters
const resetFilters = () => {
  Object.keys(form).forEach(key => {
    form[key] = ''
  })
  applyFilters()
}

// Watch for filter changes and apply with debounce
let filterTimeout = null
watch(form, () => {
  clearTimeout(filterTimeout)
  filterTimeout = setTimeout(() => {
    applyFilters()
  }, 500)
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
    revoked: 'bg-red-100 text-red-800',
    pending: 'bg-blue-100 text-blue-800',
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

// Toggle select all
const toggleSelectAll = () => {
  if (selectAll.value) {
    selectedCertificates.value = props.certificates.data.map(cert => cert.id)
  } else {
    selectedCertificates.value = []
  }
}

// Toggle select all courses
const toggleSelectAllCourses = () => {
  if (selectAllCourses.value) {
    selectedCourses.value = props.eligibleCourses.map(course => course.id)
  } else {
    selectedCourses.value = []
  }
}

// Watch selected certificates
watch(selectedCertificates, (newVal) => {
  selectAll.value = newVal.length === props.certificates.data.length && props.certificates.data.length > 0
})

// Watch selected courses
watch(selectedCourses, (newVal) => {
  selectAllCourses.value = newVal.length === props.eligibleCourses.length && props.eligibleCourses.length > 0
})

// Download certificate
const downloadCertificate = (certificate) => {
  window.open(route('admin.certificates.download', certificate.id), '_blank')
}

// Download certificate image
const downloadCertificateImage = (certificate) => {
  window.open(route('admin.certificates.download-image', certificate.id), '_blank')
}

// Export certificates
const exportCertificates = () => {
  router.post(route('admin.certificates.export', props.user.id))
}

// Update certificate status
const updateStatus = (certificate) => {
  selectedCertificate.value = certificate
  statusForm.status = certificate.status
  statusForm.reason = ''
  showStatusModal.value = true
}

const submitStatusUpdate = () => {
  if (!selectedCertificate.value) return

  router.patch(
    route('admin.certificates.update-status', selectedCertificate.value.id),
    statusForm,
    {
      preserveScroll: true,
      onSuccess: () => {
        showStatusModal.value = false
        selectedCertificate.value = null
        statusForm.reason = ''
      }
    }
  )
}

// Send certificate
const sendCertificate = (certificate) => {
  selectedCertificate.value = certificate
  sendForm.method = 'both'
  sendForm.message = ''
  showSendModal.value = true
}

const submitSendCertificate = () => {
  if (!selectedCertificate.value) return

  router.post(
    route('admin.certificates.send', selectedCertificate.value.id),
    sendForm,
    {
      preserveScroll: true,
      onSuccess: () => {
        showSendModal.value = false
        selectedCertificate.value = null
      }
    }
  )
}

// Generate all eligible certificates
const generateAllEligibleCertificates = async () => {
  try {
    const response = await fetch(route('admin.certificates.check-eligible', props.user.id))
    const data = await response.json()

    if (data.eligible_courses && data.eligible_courses.length > 0) {
      selectedCourses.value = data.eligible_courses.map(course => course.id)
      showGenerateAllModal.value = true
    } else {
      alert('No eligible courses found for certificate generation.')
    }
  } catch (error) {
    console.error('Error checking eligible courses:', error)
    alert('Failed to check eligible courses.')
  }
}

const submitGenerateAll = () => {
  if (selectedCourses.value.length === 0) return

  router.post(
    route('admin.certificates.batch-generate'),
    {
      user_id: props.user.id,
      course_ids: selectedCourses.value,
    },
    {
      preserveScroll: true,
      onSuccess: () => {
        showGenerateAllModal.value = false
        selectedCourses.value = []
      }
    }
  )
}

// Renew certificate
const renewCertificate = (certificate) => {
  selectedCertificate.value = certificate
  renewForm.expiry_date = new Date(new Date().setFullYear(new Date().getFullYear() + 2)).toISOString().split('T')[0]
  renewForm.reason = ''
  showRenewModal.value = true
}

const submitRenewCertificate = () => {
  if (!selectedCertificate.value) return

  router.patch(
    route('admin.certificates.renew', selectedCertificate.value.id),
    renewForm,
    {
      preserveScroll: true,
      onSuccess: () => {
        showRenewModal.value = false
        selectedCertificate.value = null
      }
    }
  )
}

// Regenerate image
const regenerateImage = (certificate) => {
  if (confirm('Regenerate certificate image? This will create a new image based on current template.')) {
    router.post(
      route('admin.certificates.regenerate-image', certificate.id),
      {},
      {
        preserveScroll: true,
      }
    )
  }
}

// Delete certificate
const deleteCertificate = (certificate) => {
  if (confirm(`Are you sure you want to delete certificate ${certificate.certificate_number}? This action cannot be undone.`)) {
    router.delete(route('admin.certificates.delete', certificate.id), {
      preserveScroll: true,
    })
  }
}

// Bulk actions
const bulkDownloadPDFs = () => {
  if (selectedCertificates.value.length === 0) return

  const params = new URLSearchParams()
  selectedCertificates.value.forEach(id => params.append('ids[]', id))

  window.open(route('admin.certificates.bulk-download-pdf') + '?' + params.toString(), '_blank')
  selectedCertificates.value = []
  showBulkActionMenu.value = false
}

const bulkDownloadImages = () => {
  if (selectedCertificates.value.length === 0) return

  const params = new URLSearchParams()
  selectedCertificates.value.forEach(id => params.append('ids[]', id))

  window.open(route('admin.certificates.bulk-download-images') + '?' + params.toString(), '_blank')
  selectedCertificates.value = []
  showBulkActionMenu.value = false
}

const bulkSendSelected = () => {
  if (selectedCertificates.value.length === 0) return

  router.post(
    route('admin.certificates.bulk-send'),
    { certificate_ids: selectedCertificates.value },
    {
      preserveScroll: true,
      onSuccess: () => {
        selectedCertificates.value = []
        showBulkActionMenu.value = false
      }
    }
  )
}

const bulkRegenerateSelected = () => {
  if (selectedCertificates.value.length === 0) return

  if (confirm(`Regenerate images for ${selectedCertificates.value.length} certificates?`)) {
    router.post(
      route('admin.certificates.bulk-regenerate'),
      { certificate_ids: selectedCertificates.value },
      {
        preserveScroll: true,
        onSuccess: () => {
          selectedCertificates.value = []
          showBulkActionMenu.value = false
        }
      }
    )
  }
}

const bulkUpdateStatus = () => {
  // Similar to single status update but for multiple
  showBulkActionMenu.value = false
  alert('Bulk status update would go here')
}

// Other bulk actions
const bulkRegenerateImages = () => {
  router.post(
    route('admin.certificates.bulk-regenerate-all', props.user.id),
    {},
    { preserveScroll: true }
  )
}

const bulkSendCertificates = () => {
  router.post(
    route('admin.certificates.bulk-send-all', props.user.id),
    { method: 'both' },
    { preserveScroll: true }
  )
}

const bulkExportPDF = () => {
  window.open(route('admin.certificates.bulk-export-pdf', props.user.id), '_blank')
}
</script>
