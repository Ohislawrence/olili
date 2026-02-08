<!-- resources/js/Pages/Organization/Certificates/Index.vue -->
<template>
  <OrganizationLayout>
    <Head title="Certificates" />

    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex justify-between items-center">
            <div>
              <h1 class="text-3xl font-bold text-gray-900">Certificates</h1>
              <p class="text-gray-600">Manage certificates issued by your organization</p>
            </div>
            <div class="flex space-x-3">
              <Link
                :href="route('organization.certificates.settings')"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
              >
                <Cog6ToothIcon class="h-4 w-4 mr-2" />
                Settings
              </Link>
              <Link
                :href="route('organization.certificates.create')"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700"
              >
                <PlusIcon class="h-4 w-4 mr-2" />
                Issue Certificate
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
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                  <ArrowDownTrayIcon class="h-6 w-6 text-purple-600" />
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
                <p class="text-sm font-medium text-gray-500">Issued This Month</p>
                <p class="text-2xl font-semibold text-gray-900">{{ stats.this_month }}</p>
              </div>
              <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                <CalendarDaysIcon class="h-6 w-6 text-orange-600" />
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-500">Pending</p>
                <p class="text-2xl font-semibold text-gray-900">{{ stats.pending }}</p>
              </div>
              <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <ClockIcon class="h-6 w-6 text-blue-600" />
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-500">Total Students</p>
                <p class="text-2xl font-semibold text-gray-900">{{ stats.total_students || 'N/A' }}</p>
              </div>
              <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                <UserGroupIcon class="h-6 w-6 text-emerald-600" />
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white shadow rounded-lg mb-8">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
          </div>
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
              <button
                @click="showBatchGenerateModal = true"
                class="flex flex-col items-center justify-center p-4 border border-emerald-300 rounded-lg bg-emerald-50 hover:bg-emerald-100 transition-colors"
              >
                <BoltIcon class="h-8 w-8 text-emerald-600 mb-2" />
                <span class="text-sm font-medium text-emerald-700">Batch Generate</span>
                <span class="text-xs text-emerald-600 mt-1">Generate multiple certificates</span>
              </button>

              <button
                @click="showExportModal = true"
                class="flex flex-col items-center justify-center p-4 border border-blue-300 rounded-lg bg-blue-50 hover:bg-blue-100 transition-colors"
              >
                <ArrowDownTrayIcon class="h-8 w-8 text-blue-600 mb-2" />
                <span class="text-sm font-medium text-blue-700">Export Data</span>
                <span class="text-xs text-blue-600 mt-1">CSV or Excel format</span>
              </button>

              <button
                @click="showBulkDownloadModal = true"
                class="flex flex-col items-center justify-center p-4 border border-purple-300 rounded-lg bg-purple-50 hover:bg-purple-100 transition-colors"
              >
                <DocumentArrowDownIcon class="h-8 w-8 text-purple-600 mb-2" />
                <span class="text-sm font-medium text-purple-700">Bulk Download</span>
                <span class="text-xs text-purple-600 mt-1">PDFs or Images</span>
              </button>

              <Link
                :href="route('organization.certificates.settings')"
                class="flex flex-col items-center justify-center p-4 border border-gray-300 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors"
              >
                <Cog6ToothIcon class="h-8 w-8 text-gray-600 mb-2" />
                <span class="text-sm font-medium text-gray-700">Templates</span>
                <span class="text-xs text-gray-600 mt-1">Manage designs</span>
              </Link>
            </div>
          </div>
        </div>

        <!-- Filters -->
        <div class="bg-white shadow rounded-lg mb-8">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Filters</h3>
          </div>
          <div class="p-6">
            <form @submit.prevent="applyFilters" class="space-y-4">
              <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                  <input
                    v-model="form.search"
                    type="text"
                    placeholder="Certificate number, student name, course..."
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
                  <label class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                  <select
                    v-model="form.sort"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                  >
                    <option value="newest">Newest First</option>
                    <option value="oldest">Oldest First</option>
                    <option value="course">Course Name</option>
                    <option value="expiring">Expiring Soon</option>
                  </select>
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                  <input
                    v-model="form.date_from"
                    type="date"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                  <input
                    v-model="form.date_to"
                    type="date"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                  />
                </div>
                <div class="flex items-end space-x-3">
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
              </div>
            </form>
          </div>
        </div>

        <!-- Certificates Table -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <div>
              <h3 class="text-lg font-semibold text-gray-900">Certificates</h3>
              <p class="text-sm text-gray-500">
                {{ certificates.total }} certificate(s) found
                <span v-if="selectedCertificates.length > 0" class="ml-2">
                  • {{ selectedCertificates.length }} selected
                </span>
              </p>
            </div>
            <div class="flex items-center space-x-3">
              <!-- Bulk Actions Dropdown -->
              <div class="relative" v-if="selectedCertificates.length > 0">
                <button
                  @click="showBulkActions = !showBulkActions"
                  class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                >
                  <CheckBadgeIcon class="h-4 w-4 mr-2" />
                  {{ selectedCertificates.length }} selected
                  <ChevronDownIcon class="h-4 w-4 ml-2" />
                </button>
                <div
                  v-if="showBulkActions"
                  class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10 border border-gray-200"
                >
                  <button
                    @click="bulkDownload('pdf')"
                    class="flex items-center w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  >
                    <DocumentArrowDownIcon class="h-4 w-4 mr-2" />
                    Download PDFs
                  </button>
                  <button
                    @click="bulkDownload('image')"
                    class="flex items-center w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  >
                    <PhotoIcon class="h-4 w-4 mr-2" />
                    Download Images
                  </button>
                  <button
                    @click="bulkDownload('zip')"
                    class="flex items-center w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  >
                    <ArchiveBoxIcon class="h-4 w-4 mr-2" />
                    Download as ZIP
                  </button>
                  <div class="border-t border-gray-200"></div>
                  <button
                    @click="bulkUpdateStatus('active')"
                    class="flex items-center w-full text-left px-4 py-2 text-sm text-green-700 hover:bg-gray-100"
                  >
                    <CheckCircleIcon class="h-4 w-4 mr-2" />
                    Mark as Active
                  </button>
                  <button
                    @click="bulkUpdateStatus('expired')"
                    class="flex items-center w-full text-left px-4 py-2 text-sm text-amber-700 hover:bg-gray-100"
                  >
                    <ClockIcon class="h-4 w-4 mr-2" />
                    Mark as Expired
                  </button>
                  <button
                    @click="bulkUpdateStatus('revoked')"
                    class="flex items-center w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-gray-100"
                  >
                    <NoSymbolIcon class="h-4 w-4 mr-2" />
                    Revoke Certificates
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
                    Student
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
                    <div class="flex items-center">
                      <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold text-sm mr-3">
                        {{ certificate.user?.name?.charAt(0) || 'U' }}
                      </div>
                      <div>
                        <div class="text-sm font-medium text-gray-900">{{ certificate.user?.name }}</div>
                        <div class="text-xs text-gray-500">{{ certificate.user?.email }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ certificate.course?.title }}</div>
                    <div class="text-xs text-gray-500">{{ certificate.course?.subject }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ formatDate(certificate.issue_date) }}</div>
                    <div v-if="certificate.expiry_date" class="text-xs" :class="certificate.is_expired ? 'text-red-600' : 'text-gray-500'">
                      {{ certificate.is_expired ? 'Expired' : 'Expires' }}: {{ formatDate(certificate.expiry_date) }}
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
                      <span
                        v-if="certificate.is_expired"
                        class="ml-2 text-xs text-red-600"
                      >
                        Expired
                      </span>
                      <span
                        v-else-if="isExpiringSoon(certificate)"
                        class="ml-2 text-xs text-amber-600"
                      >
                        Expiring soon
                      </span>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <span class="text-sm text-gray-900 mr-2">{{ certificate.download_count }}</span>
                      <button
                        @click="downloadCertificate(certificate)"
                        class="text-blue-600 hover:text-blue-900"
                        title="Download PDF"
                      >
                        <ArrowDownTrayIcon class="h-4 w-4" />
                      </button>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex items-center space-x-2">
                      <Link
                        :href="route('organization.certificates.show', certificate.id)"
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
                        @click="updateStatus(certificate)"
                        class="text-amber-600 hover:text-amber-900"
                        title="Update Status"
                      >
                        <PencilIcon class="h-5 w-5" />
                      </button>
                      <button
                        @click="renewCertificate(certificate)"
                        v-if="certificate.is_expired && certificate.status !== 'revoked'"
                        class="text-green-600 hover:text-green-900"
                        title="Renew Certificate"
                      >
                        <ArrowPathIcon class="h-5 w-5" />
                      </button>
                      <button
                        @click="regenerateImage(certificate)"
                        class="text-orange-600 hover:text-orange-900"
                        title="Regenerate Image"
                      >
                        <ArrowPathIcon class="h-5 w-5" />
                      </button>
                      <button
                        @click="revokeCertificate(certificate)"
                        v-if="certificate.status !== 'revoked'"
                        class="text-red-600 hover:text-red-900"
                        title="Revoke Certificate"
                      >
                        <NoSymbolIcon class="h-5 w-5" />
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
            <h3 class="mt-2 text-sm font-medium text-gray-900">No certificates found</h3>
            <p class="mt-1 text-sm text-gray-500">
              {{ form.search || form.status || form.course_id ? 'Try adjusting your filters' : 'Get started by issuing your first certificate' }}
            </p>
            <div class="mt-6">
              <Link
                :href="route('organization.certificates.create')"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700"
              >
                <PlusIcon class="h-4 w-4 mr-2" />
                Issue Certificate
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

    <!-- All Modals -->
    <!-- Batch Generate Modal -->
    <Modal :show="showBatchGenerateModal" @close="showBatchGenerateModal = false" max-width="2xl">
      <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
          Batch Generate Certificates
        </h3>
        <div class="space-y-6">
          <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <p class="text-sm text-blue-800">
              Generate certificates for multiple students who have completed selected courses.
            </p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">Select Courses</label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div v-for="course in courses" :key="course.id" class="flex items-center">
                <input
                  :id="`course-${course.id}`"
                  v-model="batchGenerateForm.course_ids"
                  type="checkbox"
                  :value="course.id"
                  class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                />
                <label :for="`course-${course.id}`" class="ml-3 cursor-pointer">
                  <span class="text-sm text-gray-900">{{ course.title }}</span>
                  <span class="block text-xs text-gray-500">{{ course.subject }}</span>
                </label>
              </div>
            </div>
            <p class="text-sm text-gray-500 mt-2">
              {{ batchGenerateForm.course_ids.length }} course(s) selected
            </p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Options</label>
            <div class="space-y-3">
              <div class="flex items-center">
                <input
                  id="send_notifications"
                  v-model="batchGenerateForm.send_notifications"
                  type="checkbox"
                  class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                />
                <label for="send_notifications" class="ml-2 text-sm text-gray-700">
                  Send notifications to students
                </label>
              </div>
              <div class="flex items-center">
                <input
                  id="generate_files"
                  v-model="batchGenerateForm.generate_files"
                  type="checkbox"
                  class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                />
                <label for="generate_files" class="ml-2 text-sm text-gray-700">
                  Generate PDF and image files
                </label>
              </div>
              <div class="mt-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date (Optional)</label>
                <input
                  v-model="batchGenerateForm.expiry_date"
                  type="date"
                  :min="new Date().toISOString().split('T')[0]"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                />
              </div>
            </div>
          </div>

          <div class="bg-gray-50 p-4 rounded-lg">
            <h4 class="text-sm font-medium text-gray-900 mb-2">What will happen?</h4>
            <ul class="text-sm text-gray-600 space-y-1">
              <li class="flex items-center">
                <CheckCircleIcon class="h-4 w-4 text-green-500 mr-2" />
                Certificates will be generated for eligible students
              </li>
              <li class="flex items-center">
                <CheckCircleIcon class="h-4 w-4 text-green-500 mr-2" />
                Unique certificate numbers will be assigned
              </li>
              <li class="flex items-center">
                <CheckCircleIcon class="h-4 w-4 text-green-500 mr-2" />
                PDF and image files will be created
              </li>
              <li class="flex items-center">
                <CheckCircleIcon class="h-4 w-4 text-green-500 mr-2" />
                Verification URLs will be generated
              </li>
            </ul>
          </div>

          <div class="flex justify-end space-x-3">
            <button
              @click="showBatchGenerateModal = false"
              class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
            >
              Cancel
            </button>
            <button
              @click="submitBatchGenerate"
              :disabled="batchGenerateForm.processing || batchGenerateForm.course_ids.length === 0"
              :class="[
                'px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white',
                batchGenerateForm.processing || batchGenerateForm.course_ids.length === 0
                  ? 'bg-emerald-400 cursor-not-allowed'
                  : 'bg-emerald-600 hover:bg-emerald-700'
              ]"
            >
              <span v-if="batchGenerateForm.processing">
                <ArrowPathIcon class="h-4 w-4 animate-spin inline mr-2" />
                Generating...
              </span>
              <span v-else>Generate Certificates</span>
            </button>
          </div>
        </div>
      </div>
    </Modal>

    <!-- Export Modal -->
    <Modal :show="showExportModal" @close="showExportModal = false" max-width="md">
      <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
          Export Certificates
        </h3>
        <div class="space-y-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Export Format</label>
            <div class="grid grid-cols-2 gap-4">
              <label class="relative flex items-center justify-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                <input
                  v-model="exportForm.format"
                  type="radio"
                  value="csv"
                  class="sr-only"
                />
                <div class="text-center">
                  <DocumentTextIcon class="mx-auto h-8 w-8 text-gray-400 mb-2" />
                  <span class="text-sm font-medium text-gray-900">CSV</span>
                  <p class="text-xs text-gray-500">Comma separated values</p>
                </div>
              </label>
              <label class="relative flex items-center justify-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                <input
                  v-model="exportForm.format"
                  type="radio"
                  value="excel"
                  class="sr-only"
                />
                <div class="text-center">
                  <TableCellsIcon class="mx-auto h-8 w-8 text-gray-400 mb-2" />
                  <span class="text-sm font-medium text-gray-900">Excel</span>
                  <p class="text-xs text-gray-500">Spreadsheet format</p>
                </div>
              </label>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Date Range (Optional)</label>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="text-xs text-gray-500 mb-1">Start Date</label>
                <input
                  v-model="exportForm.start_date"
                  type="date"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                />
              </div>
              <div>
                <label class="text-xs text-gray-500 mb-1">End Date</label>
                <input
                  v-model="exportForm.end_date"
                  type="date"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                />
              </div>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Status Filter (Optional)</label>
            <select
              v-model="exportForm.status"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="">All Statuses</option>
              <option v-for="status in statuses" :key="status" :value="status">
                {{ status.charAt(0).toUpperCase() + status.slice(1) }}
              </option>
            </select>
          </div>

          <div class="bg-gray-50 p-4 rounded-lg">
            <h4 class="text-sm font-medium text-gray-900 mb-2">What will be exported?</h4>
            <ul class="text-sm text-gray-600 space-y-1">
              <li>Certificate details (number, title, dates)</li>
              <li>Student information (name, email)</li>
              <li>Course details</li>
              <li>Status and download counts</li>
              <li>Verification URLs</li>
            </ul>
          </div>

          <div class="flex justify-end space-x-3">
            <button
              @click="showExportModal = false"
              class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
            >
              Cancel
            </button>
            <button
              @click="submitExport"
              class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700"
            >
              Export Data
            </button>
          </div>
        </div>
      </div>
    </Modal>

    <!-- Bulk Download Modal -->
    <Modal :show="showBulkDownloadModal" @close="showBulkDownloadModal = false" max-width="md">
      <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
          Bulk Download Certificates
        </h3>
        <div class="space-y-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">Download Format</label>
            <div class="grid grid-cols-3 gap-4">
              <label class="relative flex flex-col items-center justify-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                <input
                  v-model="bulkDownloadForm.format"
                  type="radio"
                  value="pdf"
                  class="sr-only"
                />
                <DocumentTextIcon class="h-8 w-8 text-red-500 mb-2" />
                <span class="text-sm font-medium text-gray-900">PDFs</span>
                <p class="text-xs text-gray-500">Document format</p>
              </label>
              <label class="relative flex flex-col items-center justify-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                <input
                  v-model="bulkDownloadForm.format"
                  type="radio"
                  value="image"
                  class="sr-only"
                />
                <PhotoIcon class="h-8 w-8 text-blue-500 mb-2" />
                <span class="text-sm font-medium text-gray-900">Images</span>
                <p class="text-xs text-gray-500">PNG format</p>
              </label>
              <label class="relative flex flex-col items-center justify-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                <input
                  v-model="bulkDownloadForm.format"
                  type="radio"
                  value="zip"
                  class="sr-only"
                />
                <ArchiveBoxIcon class="h-8 w-8 text-purple-500 mb-2" />
                <span class="text-sm font-medium text-gray-900">ZIP</span>
                <p class="text-xs text-gray-500">Both formats</p>
              </label>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Select Certificates</label>
            <div class="space-y-2">
              <label class="inline-flex items-center">
                <input
                  type="radio"
                  v-model="bulkDownloadForm.selection"
                  value="filtered"
                  class="h-4 w-4 text-blue-600 border-gray-300"
                />
                <span class="ml-2 text-sm text-gray-700">All filtered certificates ({{ certificates.total }})</span>
              </label>
              <label class="inline-flex items-center">
                <input
                  type="radio"
                  v-model="bulkDownloadForm.selection"
                  value="selected"
                  :disabled="selectedCertificates.length === 0"
                  class="h-4 w-4 text-blue-600 border-gray-300"
                />
                <span class="ml-2 text-sm text-gray-700">
                  Selected certificates ({{ selectedCertificates.length }})
                  <span v-if="selectedCertificates.length === 0" class="text-gray-400">- None selected</span>
                </span>
              </label>
            </div>
          </div>

          <div class="bg-gray-50 p-4 rounded-lg">
            <h4 class="text-sm font-medium text-gray-900 mb-2">Important Notes</h4>
            <ul class="text-sm text-gray-600 space-y-1">
              <li>• Large downloads may take a few moments to prepare</li>
              <li>• Files will be downloaded as a ZIP archive</li>
              <li>• Only active certificates with valid files will be included</li>
              <li>• Missing files will be generated automatically</li>
            </ul>
          </div>

          <div class="flex justify-end space-x-3">
            <button
              @click="showBulkDownloadModal = false"
              class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
            >
              Cancel
            </button>
            <button
              @click="submitBulkDownload"
              :disabled="bulkDownloadForm.processing"
              :class="[
                'px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white',
                bulkDownloadForm.processing
                  ? 'bg-purple-400 cursor-not-allowed'
                  : 'bg-purple-600 hover:bg-purple-700'
              ]"
            >
              <span v-if="bulkDownloadForm.processing">
                <ArrowPathIcon class="h-4 w-4 animate-spin inline mr-2" />
                Preparing...
              </span>
              <span v-else>Download Files</span>
            </button>
          </div>
        </div>
      </div>
    </Modal>

    <!-- Status Update Modal -->
    <Modal :show="showStatusModal" @close="showStatusModal = false">
      <!-- Status update modal content (similar to previous examples) -->
    </Modal>

    <!-- Revoke Certificate Modal -->
    <Modal :show="showRevokeModal" @close="showRevokeModal = false">
      <!-- Revoke modal content -->
    </Modal>

    <!-- Renew Certificate Modal -->
    <Modal :show="showRenewModal" @close="showRenewModal = false">
      <!-- Renew modal content -->
    </Modal>
  </OrganizationLayout>
</template>

<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import OrganizationLayout from '@/Layouts/OrganizationLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import Modal from '@/Components/Modal.vue'
import {
  DocumentTextIcon,
  CheckCircleIcon,
  ClockIcon,
  ArrowDownTrayIcon,
  EyeIcon,
  PencilIcon,
  TrashIcon,
  PlusIcon,
  PhotoIcon,
  DocumentIcon,
  ArrowPathIcon,
  CalendarDaysIcon,
  UserGroupIcon,
  BoltIcon,
  Cog6ToothIcon,
  DocumentArrowDownIcon,
  ArchiveBoxIcon,
  CheckBadgeIcon,
  ChevronDownIcon,
  NoSymbolIcon,
  TableCellsIcon,
} from '@heroicons/vue/24/outline'
import { ref, reactive, computed, watch } from 'vue'

const props = defineProps({
  certificates: Object,
  stats: Object,
  courses: Array,
  templates: Array,
  filters: Object,
  statuses: Array,
})

// State
const showBatchGenerateModal = ref(false)
const showExportModal = ref(false)
const showBulkDownloadModal = ref(false)
const showStatusModal = ref(false)
const showRevokeModal = ref(false)
const showRenewModal = ref(false)
const showBulkActions = ref(false)

// Selection
const selectedCertificates = ref([])
const selectAll = ref(false)

// Filter form
const form = reactive({
  search: props.filters.search || '',
  status: props.filters.status || '',
  course_id: props.filters.course_id || '',
  date_from: props.filters.date_from || '',
  date_to: props.filters.date_to || '',
  sort: props.filters.sort || 'newest',
})

// Batch generate form
const batchGenerateForm = useForm({
  course_ids: [],
  send_notifications: true,
  generate_files: true,
  expiry_date: '',
})

// Export form
const exportForm = useForm({
  format: 'csv',
  start_date: '',
  end_date: '',
  status: '',
})

// Bulk download form
const bulkDownloadForm = useForm({
  format: 'zip',
  selection: 'filtered',
})

// Selected certificate for actions
const selectedCertificate = ref(null)

// Helper functions
const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

const getStatusClass = (status) => {
  const classes = {
    active: 'bg-green-100 text-green-800',
    expired: 'bg-amber-100 text-amber-800',
    revoked: 'bg-red-100 text-red-800',
    pending: 'bg-blue-100 text-blue-800',
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const isExpiringSoon = (certificate) => {
  if (!certificate.expiry_date || certificate.is_expired || certificate.status === 'expired') {
    return false
  }
  const expiryDate = new Date(certificate.expiry_date)
  const today = new Date()
  const daysDiff = Math.ceil((expiryDate - today) / (1000 * 60 * 60 * 24))
  return daysDiff <= 30
}

// Filter actions
const applyFilters = () => {
  router.get(route('organization.certificates.index'), form, {
    preserveState: true,
    preserveScroll: true,
  })
}

const resetFilters = () => {
  Object.keys(form).forEach(key => {
    form[key] = ''
  })
  form.sort = 'newest'
  applyFilters()
}

// Watch filters for auto-apply
let filterTimeout = null
watch(form, () => {
  clearTimeout(filterTimeout)
  filterTimeout = setTimeout(() => {
    applyFilters()
  }, 500)
}, { deep: true })

// Selection actions
const toggleSelectAll = () => {
  if (selectAll.value) {
    selectedCertificates.value = props.certificates.data.map(cert => cert.id)
  } else {
    selectedCertificates.value = []
  }
}

watch(selectedCertificates, (newVal) => {
  selectAll.value = newVal.length === props.certificates.data.length && props.certificates.data.length > 0
})

// Certificate actions
const downloadCertificate = (certificate) => {
  window.open(route('organization.certificates.download', certificate.id), '_blank')
}

const updateStatus = (certificate) => {
  selectedCertificate.value = certificate
  showStatusModal.value = true
}

const revokeCertificate = (certificate) => {
  selectedCertificate.value = certificate
  showRevokeModal.value = true
}

const renewCertificate = (certificate) => {
  selectedCertificate.value = certificate
  showRenewModal.value = true
}

const regenerateImage = (certificate) => {
  if (confirm('Regenerate certificate image?')) {
    router.post(route('organization.certificates.regenerate-image', certificate.id), {}, {
      preserveScroll: true,
    })
  }
}

// Batch operations
const bulkDownload = (format) => {
  if (selectedCertificates.value.length === 0) {
    alert('Please select certificates first.')
    return
  }

  showBulkActions.value = false

  const params = new URLSearchParams()
  selectedCertificates.value.forEach(id => params.append('certificate_ids[]', id))
  params.append('format', format)

  window.open(route('organization.certificates.bulk-download') + '?' + params.toString(), '_blank')
  selectedCertificates.value = []
}

const bulkUpdateStatus = (status) => {
  if (selectedCertificates.value.length === 0) {
    alert('Please select certificates first.')
    return
  }

  if (confirm(`Are you sure you want to mark ${selectedCertificates.value.length} certificate(s) as ${status}?`)) {
    // Implement bulk status update
    showBulkActions.value = false
    selectedCertificates.value = []
  }
}

// Modal form submissions
const submitBatchGenerate = () => {
  if (batchGenerateForm.course_ids.length === 0) {
    alert('Please select at least one course.')
    return
  }

  batchGenerateForm.post(route('organization.certificates.batch-generate'), {
    preserveScroll: true,
    onSuccess: () => {
      showBatchGenerateModal.value = false
      batchGenerateForm.reset()
    }
  })
}

const submitExport = () => {
  exportForm.post(route('organization.certificates.export'), {
    preserveScroll: true,
    onSuccess: () => {
      showExportModal.value = false
      exportForm.reset()
    }
  })
}

const submitBulkDownload = () => {
  const params = new URLSearchParams()

  if (bulkDownloadForm.selection === 'selected' && selectedCertificates.value.length === 0) {
    alert('Please select certificates first.')
    return
  }

  if (bulkDownloadForm.selection === 'selected') {
    selectedCertificates.value.forEach(id => params.append('certificate_ids[]', id))
  } else {
    // Add current filter parameters
    Object.keys(form).forEach(key => {
      if (form[key]) {
        params.append(key, form[key])
      }
    })
  }

  params.append('format', bulkDownloadForm.format)

  showBulkDownloadModal.value = false
  window.open(route('organization.certificates.bulk-download') + '?' + params.toString(), '_blank')
}

const exportCertificates = () => {
  showExportModal.value = true
}

// Initialize from props
watch(() => props.filters, (newFilters) => {
  Object.keys(form).forEach(key => {
    form[key] = newFilters[key] || ''
  })
}, { immediate: true })
</script>
