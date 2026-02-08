<!-- resources/js/Pages/Admin/Certificates/Show.vue -->
<template>
  <AdminLayout>
    <Head :title="`Certificate - ${certificate.certificate_number}`" />

    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex justify-between items-center">
            <div>
              <div class="flex items-center space-x-2 mb-2">
                <Link
                  :href="route('admin.users.certificates', certificate.user_id)"
                  class="text-gray-500 hover:text-gray-700"
                >
                  <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                  </svg>
                </Link>
                <h1 class="text-3xl font-bold text-gray-900">Certificate Details</h1>
              </div>
              <p class="text-gray-600">{{ certificate.certificate_number }}</p>
            </div>
            <div class="flex space-x-3">
              <Link
                :href="route('admin.users.certificates', certificate.user_id)"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
              >
                Back to List
              </Link>
              <a
                :href="route('admin.certificates.download', certificate.id)"
                target="_blank"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700"
              >
                <ArrowDownTrayIcon class="h-4 w-4 mr-2" />
                Download PDF
              </a>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Left Column - Certificate Preview -->
          <div class="lg:col-span-2">
            <div class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Certificate Preview</h3>
              </div>
              <div class="p-6">
                <!-- Image Preview -->
                <div v-if="certificate.image_url" class="mb-6">
                  <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <img
                      :src="certificate.image_url"
                      :alt="certificate.title"
                      class="w-full h-auto"
                    />
                  </div>
                  <div class="flex justify-center space-x-3 mt-4">
                    <a
                      :href="certificate.image_url"
                      target="_blank"
                      class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                    >
                      <PhotoIcon class="h-4 w-4 mr-2" />
                      View Full Image
                    </a>
                    <a
                      :href="certificate.image_url"
                      download
                      class="inline-flex items-center px-3 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700"
                    >
                      <ArrowDownTrayIcon class="h-4 w-4 mr-2" />
                      Download Image
                    </a>
                  </div>
                </div>
                <div v-else class="text-center py-12 bg-gray-50 rounded-lg">
                  <DocumentTextIcon class="mx-auto h-12 w-12 text-gray-400" />
                  <h3 class="mt-2 text-sm font-medium text-gray-900">No preview available</h3>
                  <p class="mt-1 text-sm text-gray-500">Certificate image has not been generated yet.</p>
                  <button
                    @click="generateImage"
                    class="mt-4 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700"
                  >
                    <ArrowPathIcon class="h-4 w-4 mr-2" />
                    Generate Image
                  </button>
                </div>

                <!-- Certificate Details -->
                <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div class="p-4 bg-gray-50 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Certificate Information</h4>
                    <dl class="space-y-2">
                      <div>
                        <dt class="text-xs text-gray-500">Certificate Number</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ certificate.certificate_number }}</dd>
                      </div>
                      <div>
                        <dt class="text-xs text-gray-500">Title</dt>
                        <dd class="text-sm text-gray-900">{{ certificate.title }}</dd>
                      </div>
                      <div>
                        <dt class="text-xs text-gray-500">Description</dt>
                        <dd class="text-sm text-gray-900">{{ certificate.description }}</dd>
                      </div>
                      <div>
                        <dt class="text-xs text-gray-500">Status</dt>
                        <dd>
                          <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                            :class="getStatusClass(certificate.status)"
                          >
                            {{ certificate.status }}
                          </span>
                        </dd>
                      </div>
                    </dl>
                  </div>

                  <div class="p-4 bg-gray-50 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Validity Period</h4>
                    <dl class="space-y-2">
                      <div>
                        <dt class="text-xs text-gray-500">Issue Date</dt>
                        <dd class="text-sm text-gray-900">{{ formatDate(certificate.issue_date) }}</dd>
                      </div>
                      <div>
                        <dt class="text-xs text-gray-500">Expiry Date</dt>
                        <dd class="text-sm text-gray-900">
                          {{ certificate.expiry_date ? formatDate(certificate.expiry_date) : 'No expiry' }}
                          <span v-if="isExpiredSoon" class="ml-2 text-xs text-amber-600">
                            (Expires soon)
                          </span>
                          <span v-else-if="isExpired" class="ml-2 text-xs text-red-600">
                            (Expired)
                          </span>
                        </dd>
                      </div>
                      <div>
                        <dt class="text-xs text-gray-500">Days Remaining</dt>
                        <dd class="text-sm text-gray-900">
                          {{ daysRemaining > 0 ? `${daysRemaining} days` : 'Expired' }}
                        </dd>
                      </div>
                      <div>
                        <dt class="text-xs text-gray-500">Downloads</dt>
                        <dd class="text-sm text-gray-900">{{ certificate.download_count }}</dd>
                      </div>
                    </dl>
                  </div>
                </div>
              </div>
            </div>

            <!-- Student & Course Information -->
            <div class="mt-8 bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Student & Course Details</h3>
              </div>
              <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                  <!-- Student Info -->
                  <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Student Information</h4>
                    <div class="space-y-3">
                      <div class="flex items-center">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold">
                          {{ certificate.user?.name?.charAt(0) || 'U' }}
                        </div>
                        <div class="ml-3">
                          <p class="text-sm font-medium text-gray-900">{{ certificate.user?.name }}</p>
                          <p class="text-xs text-gray-500">{{ certificate.user?.email }}</p>
                        </div>
                      </div>
                      <div>
                        <Link
                          :href="route('admin.users.show', certificate.user_id)"
                          class="inline-flex items-center text-sm text-blue-600 hover:text-blue-900"
                        >
                          View Student Profile
                          <svg class="h-4 w-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                          </svg>
                        </Link>
                      </div>
                    </div>
                  </div>

                  <!-- Course Info -->
                  <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Course Information</h4>
                    <div class="space-y-3">
                      <div>
                        <p class="text-sm font-medium text-gray-900">{{ certificate.course?.title }}</p>
                        <p class="text-xs text-gray-500">{{ certificate.course?.subject }}</p>
                      </div>
                      <div class="grid grid-cols-2 gap-2">
                        <div>
                          <p class="text-xs text-gray-500">Level</p>
                          <p class="text-sm text-gray-900">{{ certificate.course?.level }}</p>
                        </div>
                        <div>
                          <p class="text-xs text-gray-500">Duration</p>
                          <p class="text-sm text-gray-900">{{ certificate.course?.estimated_duration_hours }}h</p>
                        </div>
                      </div>
                      <div>
                        <Link
                          :href="route('admin.courses.show', certificate.course_id)"
                          class="inline-flex items-center text-sm text-blue-600 hover:text-blue-900"
                        >
                          View Course Details
                          <svg class="h-4 w-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                          </svg>
                        </Link>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Completion Data -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                  <h4 class="text-sm font-medium text-gray-900 mb-3">Completion Details</h4>
                  <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center">
                      <p class="text-2xl font-semibold text-gray-900">
                        {{ certificate.completion_data?.score || 0 }}%
                      </p>
                      <p class="text-xs text-gray-500">Score</p>
                    </div>
                    <div class="text-center">
                      <p class="text-2xl font-semibold text-gray-900">
                        {{ certificate.completion_data?.modules_completed || 0 }}/{{ certificate.completion_data?.total_modules || 0 }}
                      </p>
                      <p class="text-xs text-gray-500">Modules</p>
                    </div>
                    <div class="text-center">
                      <p class="text-2xl font-semibold text-gray-900">
                        {{ certificate.completion_data?.capstone_completed ? 'Yes' : 'No' }}
                      </p>
                      <p class="text-xs text-gray-500">Capstone</p>
                    </div>
                    <div class="text-center">
                      <p class="text-2xl font-semibold text-gray-900">
                        {{ formatDate(certificate.completion_data?.completed_at) }}
                      </p>
                      <p class="text-xs text-gray-500">Completed</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Right Column - Actions & Metadata -->
          <div>
            <!-- Quick Actions -->
            <div class="bg-white shadow rounded-lg mb-8">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
              </div>
              <div class="p-6">
                <div class="space-y-3">
                  <button
                    @click="sendCertificate"
                    class="w-full flex items-center justify-center px-4 py-3 border border-purple-300 rounded-lg bg-purple-50 hover:bg-purple-100"
                  >
                    <PaperAirplaneIcon class="h-5 w-5 text-purple-600 mr-2" />
                    <span class="text-sm font-medium text-purple-700">Send to User</span>
                  </button>
                  <button
                    @click="regenerateImage"
                    class="w-full flex items-center justify-center px-4 py-3 border border-orange-300 rounded-lg bg-orange-50 hover:bg-orange-100"
                  >
                    <ArrowPathIcon class="h-5 w-5 text-orange-600 mr-2" />
                    <span class="text-sm font-medium text-orange-700">Regenerate Image</span>
                  </button>
                  <button
                    @click="updateStatus"
                    class="w-full flex items-center justify-center px-4 py-3 border border-amber-300 rounded-lg bg-amber-50 hover:bg-amber-100"
                  >
                    <PencilIcon class="h-5 w-5 text-amber-600 mr-2" />
                    <span class="text-sm font-medium text-amber-700">Update Status</span>
                  </button>
                  <button
                    @click="renewCertificate"
                    v-if="isExpired || isExpiredSoon"
                    class="w-full flex items-center justify-center px-4 py-3 border border-emerald-300 rounded-lg bg-emerald-50 hover:bg-emerald-100"
                  >
                    <CheckCircleIcon class="h-5 w-5 text-emerald-600 mr-2" />
                    <span class="text-sm font-medium text-emerald-700">Renew Certificate</span>
                  </button>
                  <button
                    @click="deleteCertificate"
                    class="w-full flex items-center justify-center px-4 py-3 border border-red-300 rounded-lg bg-red-50 hover:bg-red-100"
                  >
                    <TrashIcon class="h-5 w-5 text-red-600 mr-2" />
                    <span class="text-sm font-medium text-red-700">Delete Certificate</span>
                  </button>
                </div>
              </div>
            </div>

            <!-- Verification Information -->
            <div class="bg-white shadow rounded-lg mb-8">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Verification</h3>
              </div>
              <div class="p-6">
                <div class="space-y-4">
                  <div>
                    <p class="text-sm font-medium text-gray-900">Verification URL</p>
                    <div class="mt-1 flex items-center">
                      <input
                        :value="certificate.verification_url"
                        type="text"
                        readonly
                        class="flex-1 px-3 py-2 border border-gray-300 rounded-l-md text-sm bg-gray-50"
                      />
                      <button
                        @click="copyVerificationUrl"
                        class="px-3 py-2 border border-l-0 border-gray-300 rounded-r-md bg-white hover:bg-gray-50"
                      >
                        <ClipboardDocumentIcon class="h-4 w-4 text-gray-500" />
                      </button>
                    </div>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-900 mb-2">QR Code</p>
                    <div v-if="certificate.qr_code" class="p-3 border border-gray-200 rounded-lg inline-block">
                      <img :src="certificate.qr_code" alt="QR Code" class="w-32 h-32" />
                    </div>
                    <div v-else class="text-center py-4 bg-gray-50 rounded-lg">
                      <QrCodeIcon class="mx-auto h-8 w-8 text-gray-400" />
                      <p class="text-xs text-gray-500 mt-2">No QR code generated</p>
                    </div>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-900 mb-2">Public Access</p>
                    <div class="flex items-center">
                      <div
                        :class="[
                          'w-3 h-3 rounded-full mr-2',
                          certificate.is_public ? 'bg-green-400' : 'bg-red-400'
                        ]"
                      ></div>
                      <span class="text-sm text-gray-700">
                        {{ certificate.is_public ? 'Publicly accessible' : 'Private' }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Metadata -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Metadata</h3>
              </div>
              <div class="p-6">
                <dl class="space-y-3">
                  <div>
                    <dt class="text-xs text-gray-500">Created At</dt>
                    <dd class="text-sm text-gray-900">{{ formatDateTime(certificate.created_at) }}</dd>
                  </div>
                  <div>
                    <dt class="text-xs text-gray-500">Updated At</dt>
                    <dd class="text-sm text-gray-900">{{ formatDateTime(certificate.updated_at) }}</dd>
                  </div>
                  <div v-if="certificate.issued_by">
                    <dt class="text-xs text-gray-500">Issued By</dt>
                    <dd class="text-sm text-gray-900">{{ certificate.issued_by }}</dd>
                  </div>
                  <div v-if="certificate.organization">
                    <dt class="text-xs text-gray-500">Organization</dt>
                    <dd class="text-sm text-gray-900">{{ certificate.organization.name }}</dd>
                  </div>
                  <div v-if="certificate.metadata">
                    <dt class="text-xs text-gray-500">Additional Data</dt>
                    <dd class="text-sm text-gray-900">
                      <pre class="text-xs bg-gray-50 p-2 rounded overflow-auto max-h-32">{{ JSON.stringify(certificate.metadata, null, 2) }}</pre>
                    </dd>
                  </div>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- All Modals -->
    <!-- Status Update Modal -->
    <Modal :show="showStatusModal" @close="showStatusModal = false">
      <!-- Same as Certificates.vue -->
    </Modal>

    <!-- Send Certificate Modal -->
    <Modal :show="showSendModal" @close="showSendModal = false">
      <!-- Same as Certificates.vue -->
    </Modal>

    <!-- Renew Certificate Modal -->
    <Modal :show="showRenewModal" @close="showRenewModal = false">
      <!-- Same as Certificates.vue -->
    </Modal>
  </AdminLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Modal from '@/Components/Modal.vue'
import {
  DocumentTextIcon,
  CheckCircleIcon,
  ArrowDownTrayIcon,
  PaperAirplaneIcon,
  PencilIcon,
  TrashIcon,
  PhotoIcon,
  ArrowPathIcon,
  ClipboardDocumentIcon,
  QrCodeIcon,
} from '@heroicons/vue/24/outline'
import { ref, computed } from 'vue'

const props = defineProps({
  certificate: Object,
})

const showStatusModal = ref(false)
const showSendModal = ref(false)
const showRenewModal = ref(false)

const isExpired = computed(() => {
  if (!props.certificate.expiry_date) return false
  return new Date(props.certificate.expiry_date) < new Date()
})

const isExpiredSoon = computed(() => {
  if (!props.certificate.expiry_date || isExpired.value) return false
  const expiryDate = new Date(props.certificate.expiry_date)
  const today = new Date()
  const daysDiff = Math.ceil((expiryDate - today) / (1000 * 60 * 60 * 24))
  return daysDiff <= 30
})

const daysRemaining = computed(() => {
  if (!props.certificate.expiry_date) return Infinity
  const expiryDate = new Date(props.certificate.expiry_date)
  const today = new Date()
  return Math.ceil((expiryDate - today) / (1000 * 60 * 60 * 24))
})

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

const formatDateTime = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
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

const copyVerificationUrl = () => {
  navigator.clipboard.writeText(props.certificate.verification_url)
  alert('Verification URL copied to clipboard!')
}

const generateImage = () => {
  router.post(route('admin.certificates.regenerate-image', props.certificate.id), {}, {
    preserveScroll: true,
  })
}

const sendCertificate = () => {
  showSendModal.value = true
}

const regenerateImage = () => {
  if (confirm('Regenerate certificate image?')) {
    router.post(route('admin.certificates.regenerate-image', props.certificate.id), {}, {
      preserveScroll: true,
    })
  }
}

const updateStatus = () => {
  showStatusModal.value = true
}

const renewCertificate = () => {
  showRenewModal.value = true
}

const deleteCertificate = () => {
  if (confirm(`Are you sure you want to delete certificate ${props.certificate.certificate_number}?`)) {
    router.delete(route('admin.certificates.delete', props.certificate.id), {
      preserveScroll: true,
      onSuccess: () => {
        router.visit(route('admin.users.certificates', props.certificate.user_id))
      }
    })
  }
}
</script>
