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

        <!-- Success/Error Messages -->
        <div v-if="$page.props.flash.success" class="mb-6">
          <div class="rounded-md bg-green-50 p-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <CheckCircleIcon class="h-5 w-5 text-green-400" />
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-green-800">{{ $page.props.flash.success }}</p>
              </div>
            </div>
          </div>
        </div>

        <div v-if="$page.props.flash.error" class="mb-6">
          <div class="rounded-md bg-red-50 p-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <XCircleIcon class="h-5 w-5 text-red-400" />
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-red-800">{{ $page.props.flash.error }}</p>
              </div>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Left Column - Certificate Preview & Details -->
          <div class="lg:col-span-2 space-y-8">
            <!-- Certificate Preview -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Certificate Preview</h3>
                <div class="flex items-center space-x-2">
                  <span class="text-sm text-gray-500">Views:</span>
                  <span class="text-sm font-medium text-gray-900">{{ certificate.view_count || 0 }}</span>
                </div>
              </div>
              <div class="p-6">
                <!-- Image Preview -->
                <div v-if="certificate.image_url" class="mb-6">
                  <div class="border border-gray-200 rounded-lg overflow-hidden shadow-sm">
                    <img
                      :src="certificate.image_url"
                      :alt="certificate.title"
                      class="w-full h-auto"
                      @load="imageLoaded = true"
                    />
                    <div v-if="!imageLoaded" class="flex items-center justify-center p-12">
                      <div class="animate-pulse flex space-x-4">
                        <div class="rounded-full bg-gray-200 h-12 w-12"></div>
                      </div>
                    </div>
                  </div>
                  <div class="flex flex-wrap gap-3 mt-4">
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
                    <button
                      @click="regenerateImage"
                      class="inline-flex items-center px-3 py-2 border border-orange-300 rounded-md text-sm font-medium text-orange-700 bg-orange-50 hover:bg-orange-100"
                    >
                      <ArrowPathIcon class="h-4 w-4 mr-2" />
                      Regenerate
                    </button>
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

                <!-- Certificate Information -->
                <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div class="p-4 bg-gray-50 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Certificate Information</h4>
                    <dl class="space-y-3">
                      <div>
                        <dt class="text-xs text-gray-500">Certificate Number</dt>
                        <dd class="text-sm font-medium text-gray-900 flex items-center">
                          {{ certificate.certificate_number }}
                          <button
                            @click="copyToClipboard(certificate.certificate_number)"
                            class="ml-2 text-gray-400 hover:text-gray-600"
                            title="Copy to clipboard"
                          >
                            <ClipboardDocumentIcon class="h-4 w-4" />
                          </button>
                        </dd>
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
                            {{ certificate.status.charAt(0).toUpperCase() + certificate.status.slice(1) }}
                          </span>
                          <button
                            v-if="canUpdateStatus"
                            @click="updateStatus"
                            class="ml-2 text-xs text-blue-600 hover:text-blue-800"
                          >
                            Change
                          </button>
                        </dd>
                      </div>
                    </dl>
                  </div>

                  <div class="p-4 bg-gray-50 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Validity Period</h4>
                    <dl class="space-y-3">
                      <div>
                        <dt class="text-xs text-gray-500">Issue Date</dt>
                        <dd class="text-sm text-gray-900">{{ formatDate(certificate.issue_date) }}</dd>
                      </div>
                      <div>
                        <dt class="text-xs text-gray-500">Expiry Date</dt>
                        <dd class="text-sm text-gray-900">
                          {{ certificate.expiry_date ? formatDate(certificate.expiry_date) : 'No expiry' }}
                          <span v-if="isExpiredSoon && !isExpired" class="ml-2 text-xs text-amber-600">
                            (Expires in {{ daysRemaining }} days)
                          </span>
                          <span v-else-if="isExpired" class="ml-2 text-xs text-red-600">
                            (Expired {{ Math.abs(daysRemaining) }} days ago)
                          </span>
                        </dd>
                      </div>
                      <div>
                        <dt class="text-xs text-gray-500">Downloads</dt>
                        <dd class="text-sm text-gray-900 flex items-center">
                          {{ certificate.download_count }}
                          <button
                            @click="downloadCertificate"
                            class="ml-2 text-blue-600 hover:text-blue-800"
                            title="Download PDF"
                          >
                            <ArrowDownTrayIcon class="h-4 w-4" />
                          </button>
                        </dd>
                      </div>
                      <div>
                        <dt class="text-xs text-gray-500">Public Access</dt>
                        <dd class="text-sm text-gray-900">
                          <span class="inline-flex items-center">
                            <div
                              :class="[
                                'w-2 h-2 rounded-full mr-2',
                                certificate.is_public ? 'bg-green-400' : 'bg-red-400'
                              ]"
                            ></div>
                            {{ certificate.is_public ? 'Public' : 'Private' }}
                          </span>
                          <button
                            @click="togglePublicAccess"
                            class="ml-2 text-xs text-blue-600 hover:text-blue-800"
                          >
                            Toggle
                          </button>
                        </dd>
                      </div>
                    </dl>
                  </div>
                </div>
              </div>
            </div>

            <!-- Student & Course Information -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Student & Course Details</h3>
              </div>
              <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                  <!-- Student Info -->
                  <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Student Information</h4>
                    <div class="space-y-4">
                      <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                          {{ certificate.user?.name?.charAt(0) || 'U' }}
                        </div>
                        <div class="ml-4">
                          <p class="text-sm font-medium text-gray-900">{{ certificate.user?.name }}</p>
                          <p class="text-xs text-gray-500">{{ certificate.user?.email }}</p>
                          <p class="text-xs text-gray-500 mt-1">
                            Member since {{ formatDate(certificate.user?.created_at) }}
                          </p>
                        </div>
                      </div>
                      <div class="space-y-2">
                        <Link
                          :href="route('admin.users.show', certificate.user_id)"
                          class="inline-flex items-center text-sm text-blue-600 hover:text-blue-900"
                        >
                          <UserIcon class="h-4 w-4 mr-1" />
                          View Student Profile
                        </Link>
                        <a
                          :href="'mailto:' + certificate.user?.email"
                          class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 block"
                        >
                          <EnvelopeIcon class="h-4 w-4 mr-1" />
                          Send Email
                        </a>
                      </div>
                    </div>
                  </div>

                  <!-- Course Info -->
                  <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Course Information</h4>
                    <div class="space-y-4">
                      <div>
                        <h5 class="text-lg font-semibold text-gray-900">{{ certificate.course?.title }}</h5>
                        <p class="text-sm text-gray-600 mt-1">{{ certificate.course?.description }}</p>
                      </div>
                      <div class="grid grid-cols-2 gap-4">
                        <div>
                          <p class="text-xs text-gray-500">Level</p>
                          <p class="text-sm font-medium text-gray-900">{{ certificate.course?.level }}</p>
                        </div>
                        <div>
                          <p class="text-xs text-gray-500">Subject</p>
                          <p class="text-sm font-medium text-gray-900">{{ certificate.course?.subject }}</p>
                        </div>
                        <div>
                          <p class="text-xs text-gray-500">Duration</p>
                          <p class="text-sm font-medium text-gray-900">{{ certificate.course?.estimated_duration_hours }} hours</p>
                        </div>
                        <div>
                          <p class="text-xs text-gray-500">Enrollment Status</p>
                          <p class="text-sm font-medium text-gray-900">Completed</p>
                        </div>
                      </div>
                      <div>
                        <Link
                          :href="route('admin.courses.show', certificate.course_id)"
                          class="inline-flex items-center text-sm text-blue-600 hover:text-blue-900"
                        >
                          <BookOpenIcon class="h-4 w-4 mr-1" />
                          View Course Details
                        </Link>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Completion Data -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                  <h4 class="text-sm font-medium text-gray-900 mb-4">Completion Details</h4>
                  <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center p-4 bg-gradient-to-br from-emerald-50 to-teal-50 rounded-lg border border-emerald-100">
                      <p class="text-2xl font-bold text-emerald-700">
                        {{ certificate.completion_data?.score || 0 }}%
                      </p>
                      <p class="text-xs text-emerald-600 font-medium">Overall Score</p>
                    </div>
                    <div class="text-center p-4 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg border border-blue-100">
                      <p class="text-2xl font-bold text-blue-700">
                        {{ certificate.completion_data?.modules_completed || 0 }}/{{ certificate.completion_data?.total_modules || 0 }}
                      </p>
                      <p class="text-xs text-blue-600 font-medium">Modules Completed</p>
                    </div>
                    <div class="text-center p-4 bg-gradient-to-br from-purple-50 to-pink-50 rounded-lg border border-purple-100">
                      <p class="text-2xl font-bold text-purple-700">
                        {{ certificate.completion_data?.capstone_completed ? 'Yes' : 'No' }}
                      </p>
                      <p class="text-xs text-purple-600 font-medium">Capstone Approved</p>
                    </div>
                    <div class="text-center p-4 bg-gradient-to-br from-amber-50 to-orange-50 rounded-lg border border-amber-100">
                      <p class="text-2xl font-bold text-amber-700">
                        {{ formatDate(certificate.completion_data?.completed_at) }}
                      </p>
                      <p class="text-xs text-amber-600 font-medium">Completed On</p>
                    </div>
                  </div>

                  <!-- Progress Breakdown -->
                  <div v-if="certificate.completion_data?.breakdown" class="mt-6">
                    <h5 class="text-sm font-medium text-gray-900 mb-3">Progress Breakdown</h5>
                    <div class="space-y-3">
                      <div v-for="(item, index) in certificate.completion_data.breakdown" :key="index" class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">{{ item.label }}</span>
                        <div class="flex items-center space-x-2">
                          <div class="w-32 bg-gray-200 rounded-full h-2">
                            <div
                              class="bg-emerald-600 h-2 rounded-full"
                              :style="{ width: item.percentage + '%' }"
                            ></div>
                          </div>
                          <span class="text-sm font-medium text-gray-900 w-8 text-right">{{ item.value }}/{{ item.total }}</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Issuer Information -->
            <div v-if="certificate.issuer || certificate.organization" class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Issuer Information</h3>
              </div>
              <div class="p-6">
                <div class="flex items-center">
                  <div v-if="certificate.organization?.logo" class="w-16 h-16 rounded-lg overflow-hidden mr-4 border border-gray-200">
                    <img :src="certificate.organization.logo" :alt="certificate.organization.name" class="w-full h-full object-cover" />
                  </div>
                  <div>
                    <h4 class="text-lg font-semibold text-gray-900">{{ certificate.issued_by || certificate.organization?.name || 'Olilearn AI Learning Platform' }}</h4>
                    <p class="text-sm text-gray-600 mt-1">
                      {{ certificate.issuer?.description || 'Authorized certificate issuing body' }}
                    </p>
                    <div class="flex items-center space-x-4 mt-3">
                      <span v-if="certificate.organization?.is_verified" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <CheckBadgeIcon class="h-3 w-3 mr-1" />
                        Verified Organization
                      </span>
                      <a v-if="certificate.organization?.website" :href="certificate.organization.website" target="_blank" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-900">
                        <GlobeAltIcon class="h-4 w-4 mr-1" />
                        Website
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Right Column - Actions & Metadata -->
          <div class="space-y-8">
            <!-- Quick Actions -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
              </div>
              <div class="p-6">
                <div class="space-y-3">
                  <button
                    @click="sendCertificate"
                    class="w-full flex items-center justify-center px-4 py-3 border border-purple-300 rounded-lg bg-purple-50 hover:bg-purple-100 transition-colors"
                  >
                    <PaperAirplaneIcon class="h-5 w-5 text-purple-600 mr-2" />
                    <span class="text-sm font-medium text-purple-700">Send to User</span>
                  </button>
                  <button
                    @click="regenerateImage"
                    class="w-full flex items-center justify-center px-4 py-3 border border-orange-300 rounded-lg bg-orange-50 hover:bg-orange-100 transition-colors"
                  >
                    <ArrowPathIcon class="h-5 w-5 text-orange-600 mr-2" />
                    <span class="text-sm font-medium text-orange-700">Regenerate Image</span>
                  </button>
                  <button
                    @click="updateStatus"
                    class="w-full flex items-center justify-center px-4 py-3 border border-amber-300 rounded-lg bg-amber-50 hover:bg-amber-100 transition-colors"
                  >
                    <PencilIcon class="h-5 w-5 text-amber-600 mr-2" />
                    <span class="text-sm font-medium text-amber-700">Update Status</span>
                  </button>
                  <button
                    @click="renewCertificate"
                    v-if="isExpired || isExpiredSoon"
                    class="w-full flex items-center justify-center px-4 py-3 border border-emerald-300 rounded-lg bg-emerald-50 hover:bg-emerald-100 transition-colors"
                  >
                    <CheckCircleIcon class="h-5 w-5 text-emerald-600 mr-2" />
                    <span class="text-sm font-medium text-emerald-700">
                      {{ isExpired ? 'Renew Certificate' : 'Extend Validity' }}
                    </span>
                  </button>
                  <button
                    @click="viewVerification"
                    class="w-full flex items-center justify-center px-4 py-3 border border-blue-300 rounded-lg bg-blue-50 hover:bg-blue-100 transition-colors"
                  >
                    <QrCodeIcon class="h-5 w-5 text-blue-600 mr-2" />
                    <span class="text-sm font-medium text-blue-700">View Verification</span>
                  </button>
                  <button
                    @click="deleteCertificate"
                    class="w-full flex items-center justify-center px-4 py-3 border border-red-300 rounded-lg bg-red-50 hover:bg-red-100 transition-colors"
                  >
                    <TrashIcon class="h-5 w-5 text-red-600 mr-2" />
                    <span class="text-sm font-medium text-red-700">Delete Certificate</span>
                  </button>
                </div>
              </div>
            </div>

            <!-- Verification Information -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Verification</h3>
              </div>
              <div class="p-6">
                <div class="space-y-4">
                  <div>
                    <p class="text-sm font-medium text-gray-900 mb-2">Verification URL</p>
                    <div class="flex items-center">
                      <input
                        :value="certificate.verification_url"
                        type="text"
                        readonly
                        class="flex-1 px-3 py-2 border border-gray-300 rounded-l-md text-sm bg-gray-50 text-gray-900 truncate"
                      />
                      <button
                        @click="copyVerificationUrl"
                        class="px-3 py-2 border border-l-0 border-gray-300 rounded-r-md bg-white hover:bg-gray-50 transition-colors"
                        title="Copy URL"
                      >
                        <ClipboardDocumentIcon class="h-4 w-4 text-gray-500" />
                      </button>
                    </div>
                    <button
                      @click="openVerificationPage"
                      class="mt-2 text-xs text-blue-600 hover:text-blue-800"
                    >
                      Open verification page →
                    </button>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-900 mb-3">QR Code</p>
                    <div v-if="certificate.qr_code" class="flex flex-col items-center">
                      <div class="p-4 border border-gray-200 rounded-lg bg-white inline-block">
                        <img :src="certificate.qr_code" alt="QR Code" class="w-40 h-40" />
                      </div>
                      <div class="mt-3 flex space-x-2">
                        <button
                          @click="downloadQRCode"
                          class="text-xs text-blue-600 hover:text-blue-800"
                        >
                          Download QR
                        </button>
                        <button
                          @click="regenerateQRCode"
                          class="text-xs text-gray-600 hover:text-gray-800"
                        >
                          Regenerate
                        </button>
                      </div>
                    </div>
                    <div v-else class="text-center py-6 bg-gray-50 rounded-lg">
                      <QrCodeIcon class="mx-auto h-8 w-8 text-gray-400" />
                      <p class="text-xs text-gray-500 mt-2">No QR code generated</p>
                      <button
                        @click="generateQRCode"
                        class="mt-2 text-xs text-blue-600 hover:text-blue-800"
                      >
                        Generate QR Code
                      </button>
                    </div>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-900 mb-2">Verification Status</p>
                    <div class="flex items-center">
                      <div
                        :class="[
                          'w-3 h-3 rounded-full mr-2',
                          certificate.verification_status === 'valid' ? 'bg-green-400' :
                          certificate.verification_status === 'expired' ? 'bg-red-400' :
                          'bg-yellow-400'
                        ]"
                      ></div>
                      <span class="text-sm text-gray-700 capitalize">
                        {{ certificate.verification_status || 'unknown' }}
                      </span>
                      <button
                        @click="verifyNow"
                        class="ml-2 text-xs text-blue-600 hover:text-blue-800"
                      >
                        Verify Now
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Certificate Metadata -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Metadata</h3>
                <button
                  @click="showMetadataEditor = !showMetadataEditor"
                  class="text-sm text-blue-600 hover:text-blue-800"
                >
                  {{ showMetadataEditor ? 'Cancel' : 'Edit' }}
                </button>
              </div>
              <div class="p-6">
                <!-- Edit Mode -->
                <div v-if="showMetadataEditor" class="space-y-4">
                  <div v-for="(metadata, index) in metadataForm.metadata" :key="index" class="flex items-center space-x-3">
                    <div class="flex-1">
                      <input
                        v-model="metadata.key"
                        type="text"
                        placeholder="Key"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"
                      />
                    </div>
                    <div class="flex-1">
                      <input
                        v-model="metadata.value"
                        type="text"
                        placeholder="Value"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"
                      />
                    </div>
                    <button
                      type="button"
                      @click="removeMetadata(index)"
                      class="px-2 py-2 text-red-600 hover:text-red-800"
                    >
                      <TrashIcon class="h-4 w-4" />
                    </button>
                  </div>
                  <div class="flex space-x-3">
                    <button
                      type="button"
                      @click="addMetadata"
                      class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                    >
                      <PlusIcon class="h-4 w-4 mr-2" />
                      Add Field
                    </button>
                    <button
                      @click="saveMetadata"
                      :disabled="metadataForm.processing"
                      class="inline-flex items-center px-3 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50"
                    >
                      <span v-if="metadataForm.processing">
                        <ArrowPathIcon class="h-4 w-4 animate-spin mr-2" />
                        Saving...
                      </span>
                      <span v-else>Save Changes</span>
                    </button>
                  </div>
                </div>

                <!-- View Mode -->
                <div v-else>
                  <dl class="space-y-3">
                    <div v-for="(value, key) in certificate.metadata" :key="key">
                      <dt class="text-xs text-gray-500 capitalize">{{ key.replace(/_/g, ' ') }}</dt>
                      <dd class="text-sm text-gray-900 break-words">
                        <template v-if="typeof value === 'object'">
                          <pre class="text-xs bg-gray-50 p-2 rounded overflow-auto max-h-32">{{ JSON.stringify(value, null, 2) }}</pre>
                        </template>
                        <template v-else>
                          {{ value }}
                        </template>
                      </dd>
                    </div>
                    <div v-if="!certificate.metadata || Object.keys(certificate.metadata).length === 0">
                      <p class="text-sm text-gray-500 text-center py-4">No metadata available</p>
                    </div>
                  </dl>
                </div>

                <!-- System Metadata -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                  <h4 class="text-sm font-medium text-gray-900 mb-3">System Information</h4>
                  <dl class="space-y-2">
                    <div>
                      <dt class="text-xs text-gray-500">Created At</dt>
                      <dd class="text-sm text-gray-900">{{ formatDateTime(certificate.created_at) }}</dd>
                    </div>
                    <div>
                      <dt class="text-xs text-gray-500">Updated At</dt>
                      <dd class="text-sm text-gray-900">{{ formatDateTime(certificate.updated_at) }}</dd>
                    </div>
                    <div v-if="certificate.generated_by">
                      <dt class="text-xs text-gray-500">Generated By</dt>
                      <dd class="text-sm text-gray-900">{{ certificate.generated_by }}</dd>
                    </div>
                    <div v-if="certificate.last_verified_at">
                      <dt class="text-xs text-gray-500">Last Verified</dt>
                      <dd class="text-sm text-gray-900">{{ formatDateTime(certificate.last_verified_at) }}</dd>
                    </div>
                  </dl>
                </div>
              </div>
            </div>

            <!-- Activity Log -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Recent Activity</h3>
              </div>
              <div class="p-6">
                <div class="space-y-4">
                  <div v-for="activity in certificate.activity_log" :key="activity.id" class="flex items-start">
                    <div class="flex-shrink-0">
                      <div :class="[
                        'w-8 h-8 rounded-full flex items-center justify-center',
                        getActivityIconClass(activity.type)
                      ]">
                        <component :is="getActivityIcon(activity.type)" class="h-4 w-4 text-white" />
                      </div>
                    </div>
                    <div class="ml-3 flex-1">
                      <p class="text-sm text-gray-900">{{ activity.description }}</p>
                      <p class="text-xs text-gray-500 mt-1">{{ formatDateTime(activity.created_at) }}</p>
                      <p v-if="activity.user" class="text-xs text-gray-500">
                        by {{ activity.user.name }}
                      </p>
                    </div>
                  </div>
                  <div v-if="!certificate.activity_log || certificate.activity_log.length === 0">
                    <p class="text-sm text-gray-500 text-center py-4">No recent activity</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- All Modals -->
    <!-- Status Update Modal -->
    <Modal :show="showStatusModal" @close="showStatusModal = false" max-width="md">
      <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
          Update Certificate Status
        </h3>
        <div class="space-y-4">
          <div class="p-3 bg-gray-50 rounded-lg">
            <p class="text-sm font-medium text-gray-900">{{ certificate.certificate_number }}</p>
            <p class="text-sm text-gray-500">{{ certificate.course?.title }}</p>
            <p class="text-xs text-gray-500">Current status: <span :class="getStatusClass(certificate.status)" class="px-2 py-0.5 rounded-full text-xs">{{ certificate.status }}</span></p>
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
              <option value="draft">Draft</option>
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
              :disabled="statusForm.processing"
              class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50"
            >
              <span v-if="statusForm.processing">
                <ArrowPathIcon class="h-4 w-4 animate-spin inline mr-2" />
                Updating...
              </span>
              <span v-else>Update Status</span>
            </button>
          </div>
        </div>
      </div>
    </Modal>

    <!-- Send Certificate Modal -->
    <Modal :show="showSendModal" @close="showSendModal = false" max-width="md">
      <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
          Send Certificate to User
        </h3>
        <div class="space-y-4">
          <div class="p-3 bg-gray-50 rounded-lg">
            <p class="text-sm font-medium text-gray-900">{{ certificate.certificate_number }}</p>
            <p class="text-sm text-gray-500">{{ certificate.course?.title }}</p>
            <p class="text-xs text-gray-500">Recipient: {{ certificate.user?.email }}</p>
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
              placeholder="Add a personal message to the user..."
            ></textarea>
          </div>
          <div class="flex items-center">
            <input
              id="include_attachments"
              v-model="sendForm.include_attachments"
              type="checkbox"
              class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
            />
            <label for="include_attachments" class="ml-2 text-sm text-gray-700">
              Include PDF attachment
            </label>
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
              :disabled="sendForm.processing"
              class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 disabled:opacity-50"
            >
              <span v-if="sendForm.processing">
                <PaperAirplaneIcon class="h-4 w-4 animate-spin inline mr-2" />
                Sending...
              </span>
              <span v-else>Send Certificate</span>
            </button>
          </div>
        </div>
      </div>
    </Modal>

    <!-- Renew Certificate Modal -->
    <Modal :show="showRenewModal" @close="showRenewModal = false" max-width="md">
      <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
          {{ isExpired ? 'Renew Certificate' : 'Extend Certificate Validity' }}
        </h3>
        <div class="space-y-4">
          <div class="p-3 bg-gray-50 rounded-lg">
            <p class="text-sm font-medium text-gray-900">{{ certificate.certificate_number }}</p>
            <p class="text-sm text-gray-500">{{ certificate.course?.title }}</p>
            <p class="text-xs text-gray-500">Current expiry: {{ certificate.expiry_date ? formatDate(certificate.expiry_date) : 'No expiry' }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">New Expiry Date *</label>
            <input
              v-model="renewForm.expiry_date"
              type="date"
              :min="new Date().toISOString().split('T')[0]"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
            />
            <div class="mt-2 flex space-x-2">
              <button
                @click="setExpiry(30)"
                class="text-xs px-2 py-1 border border-gray-300 rounded hover:bg-gray-50"
              >
                30 days
              </button>
              <button
                @click="setExpiry(90)"
                class="text-xs px-2 py-1 border border-gray-300 rounded hover:bg-gray-50"
              >
                90 days
              </button>
              <button
                @click="setExpiry(365)"
                class="text-xs px-2 py-1 border border-gray-300 rounded hover:bg-gray-50"
              >
                1 year
              </button>
              <button
                @click="setExpiry(730)"
                class="text-xs px-2 py-1 border border-gray-300 rounded hover:bg-gray-50"
              >
                2 years
              </button>
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Reason (Optional)</label>
            <textarea
              v-model="renewForm.reason"
              rows="2"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              placeholder="Reason for renewal/extension..."
            ></textarea>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Additional Actions</label>
            <div class="space-y-2">
              <label class="inline-flex items-center">
                <input v-model="renewForm.regenerate_files" type="checkbox" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" />
                <span class="ml-2 text-sm text-gray-700">Regenerate certificate files (PDF & Image)</span>
              </label>
              <label class="inline-flex items-center">
                <input v-model="renewForm.notify_user" type="checkbox" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" />
                <span class="ml-2 text-sm text-gray-700">Notify user about the update</span>
              </label>
            </div>
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
              :disabled="renewForm.processing"
              class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50"
            >
              <span v-if="renewForm.processing">
                <ArrowPathIcon class="h-4 w-4 animate-spin inline mr-2" />
                Processing...
              </span>
              <span v-else>{{ isExpired ? 'Renew Certificate' : 'Extend Validity' }}</span>
            </button>
          </div>
        </div>
      </div>
    </Modal>

    <!-- Delete Confirmation Modal -->
    <Modal :show="showDeleteModal" @close="showDeleteModal = false" max-width="sm">
      <div class="p-6">
        <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full">
          <TrashIcon class="h-6 w-6 text-red-600" />
        </div>
        <div class="mt-4 text-center">
          <h3 class="text-lg font-semibold text-gray-900">Delete Certificate</h3>
          <p class="mt-2 text-sm text-gray-500">
            Are you sure you want to delete certificate
            <span class="font-medium text-gray-900">{{ certificate.certificate_number }}</span>?
          </p>
          <p class="mt-2 text-sm text-red-600">
            This action cannot be undone. All associated files and data will be permanently deleted.
          </p>
        </div>
        <div class="mt-6 flex justify-center space-x-3">
          <button
            @click="showDeleteModal = false"
            class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
          >
            Cancel
          </button>
          <button
            @click="confirmDeleteCertificate"
            :disabled="deleteForm.processing"
            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 disabled:opacity-50"
          >
            <span v-if="deleteForm.processing">
              <ArrowPathIcon class="h-4 w-4 animate-spin inline mr-2" />
              Deleting...
            </span>
            <span v-else>Delete Certificate</span>
          </button>
        </div>
      </div>
    </Modal>

    <!-- Verification Modal -->
    <Modal :show="showVerificationModal" @close="showVerificationModal = false" max-width="lg">
      <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
          Certificate Verification
        </h3>
        <div class="space-y-6">
          <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-center">
              <CheckCircleIcon class="h-5 w-5 text-blue-500 mr-2" />
              <p class="text-sm font-medium text-blue-800">Live Verification</p>
            </div>
            <p class="text-sm text-blue-700 mt-2">
              This certificate is publicly verifiable at the URL below:
            </p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <h4 class="text-sm font-medium text-gray-900 mb-3">QR Code</h4>
              <div class="p-4 border border-gray-200 rounded-lg bg-white inline-block">
                <img :src="certificate.qr_code || '/images/qr-placeholder.png'" alt="QR Code" class="w-48 h-48" />
              </div>
              <div class="mt-3 space-x-2">
                <button
                  @click="downloadQRCode"
                  class="text-sm text-blue-600 hover:text-blue-800"
                >
                  Download
                </button>
                <button
                  @click="printQRCode"
                  class="text-sm text-gray-600 hover:text-gray-800"
                >
                  Print
                </button>
              </div>
            </div>

            <div>
              <h4 class="text-sm font-medium text-gray-900 mb-3">Verification Details</h4>
              <dl class="space-y-3">
                <div>
                  <dt class="text-xs text-gray-500">Certificate ID</dt>
                  <dd class="text-sm font-medium text-gray-900">{{ certificate.certificate_number }}</dd>
                </div>
                <div>
                  <dt class="text-xs text-gray-500">Student Name</dt>
                  <dd class="text-sm text-gray-900">{{ certificate.user?.name }}</dd>
                </div>
                <div>
                  <dt class="text-xs text-gray-500">Course</dt>
                  <dd class="text-sm text-gray-900">{{ certificate.course?.title }}</dd>
                </div>
                <div>
                  <dt class="text-xs text-gray-500">Issue Date</dt>
                  <dd class="text-sm text-gray-900">{{ formatDate(certificate.issue_date) }}</dd>
                </div>
                <div>
                  <dt class="text-xs text-gray-500">Validity Status</dt>
                  <dd>
                    <span :class="[
                      'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                      isExpired ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'
                    ]">
                      {{ isExpired ? 'Expired' : 'Valid' }}
                    </span>
                  </dd>
                </div>
              </dl>

              <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Verification URL</label>
                <div class="flex items-center">
                  <input
                    :value="certificate.verification_url"
                    type="text"
                    readonly
                    class="flex-1 px-3 py-2 border border-gray-300 rounded-l-md text-sm bg-gray-50 text-gray-900 truncate"
                  />
                  <button
                    @click="copyVerificationUrl"
                    class="px-3 py-2 border border-l-0 border-gray-300 rounded-r-md bg-white hover:bg-gray-50"
                  >
                    <ClipboardDocumentIcon class="h-4 w-4 text-gray-500" />
                  </button>
                </div>
                <a
                  :href="certificate.verification_url"
                  target="_blank"
                  class="mt-2 inline-flex items-center text-sm text-blue-600 hover:text-blue-800"
                >
                  Open verification page in new tab →
                </a>
              </div>
            </div>
          </div>

          <div class="pt-4 border-t border-gray-200">
            <h4 class="text-sm font-medium text-gray-900 mb-3">Embed Options</h4>
            <div class="space-y-3">
              <div>
                <label class="block text-xs text-gray-500 mb-1">HTML Embed Code</label>
                <div class="flex items-center">
                  <input
                    :value="embedCode"
                    type="text"
                    readonly
                    class="flex-1 px-3 py-2 border border-gray-300 rounded-l-md text-sm bg-gray-50 text-gray-900 font-mono text-xs truncate"
                  />
                  <button
                    @click="copyEmbedCode"
                    class="px-3 py-2 border border-l-0 border-gray-300 rounded-r-md bg-white hover:bg-gray-50"
                  >
                    <ClipboardDocumentIcon class="h-4 w-4 text-gray-500" />
                  </button>
                </div>
              </div>
              <div>
                <label class="block text-xs text-gray-500 mb-1">Direct Link</label>
                <div class="flex items-center">
                  <input
                    :value="certificate.verification_url"
                    type="text"
                    readonly
                    class="flex-1 px-3 py-2 border border-gray-300 rounded-l-md text-sm bg-gray-50 text-gray-900 text-xs truncate"
                  />
                  <button
                    @click="copyVerificationUrl"
                    class="px-3 py-2 border border-l-0 border-gray-300 rounded-r-md bg-white hover:bg-gray-50"
                  >
                    <ClipboardDocumentIcon class="h-4 w-4 text-gray-500" />
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Modal>
  </AdminLayout>
</template>

<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3'
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
  UserIcon,
  EnvelopeIcon,
  BookOpenIcon,
  CheckBadgeIcon,
  GlobeAltIcon,
  PlusIcon,
  XCircleIcon,
  ExclamationTriangleIcon,
  ClockIcon,
  DocumentDuplicateIcon,
  PrinterIcon,
} from '@heroicons/vue/24/outline'
import { ref, computed, reactive } from 'vue'

const props = defineProps({
  certificate: Object,
})

// State
const imageLoaded = ref(false)
const showStatusModal = ref(false)
const showSendModal = ref(false)
const showRenewModal = ref(false)
const showDeleteModal = ref(false)
const showVerificationModal = ref(false)
const showMetadataEditor = ref(false)

// Forms
const statusForm = useForm({
  status: 'active',
  reason: '',
})

const sendForm = useForm({
  method: 'both',
  message: '',
  include_attachments: true,
})

const renewForm = useForm({
  expiry_date: '',
  reason: '',
  regenerate_files: false,
  notify_user: true,
})

const deleteForm = useForm({})

const metadataForm = useForm({
  metadata: [],
})

// Computed Properties
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

const canUpdateStatus = computed(() => {
  return !['revoked', 'deleted'].includes(props.certificate.status)
})

const embedCode = computed(() => {
  return `<a href="${props.certificate.verification_url}" target="_blank">Verify Certificate ${props.certificate.certificate_number}</a>`
})

// Initialize metadata form
if (props.certificate.metadata && typeof props.certificate.metadata === 'object') {
  metadataForm.metadata = Object.entries(props.certificate.metadata).map(([key, value]) => ({
    key,
    value: typeof value === 'object' ? JSON.stringify(value) : String(value)
  }))
}

// Helper Functions
const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
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
    draft: 'bg-gray-100 text-gray-800',
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getActivityIcon = (type) => {
  const icons = {
    created: DocumentTextIcon,
    updated: PencilIcon,
    downloaded: ArrowDownTrayIcon,
    sent: PaperAirplaneIcon,
    verified: CheckCircleIcon,
    expired: ClockIcon,
    renewed: ArrowPathIcon,
  }
  return icons[type] || DocumentTextIcon
}

const getActivityIconClass = (type) => {
  const classes = {
    created: 'bg-emerald-500',
    updated: 'bg-blue-500',
    downloaded: 'bg-purple-500',
    sent: 'bg-indigo-500',
    verified: 'bg-green-500',
    expired: 'bg-amber-500',
    renewed: 'bg-teal-500',
  }
  return classes[type] || 'bg-gray-500'
}

const copyToClipboard = (text) => {
  navigator.clipboard.writeText(text)
    .then(() => {
      alert('Copied to clipboard!')
    })
    .catch(err => {
      console.error('Failed to copy: ', err)
    })
}

// Actions
const updateStatus = () => {
  statusForm.status = props.certificate.status
  showStatusModal.value = true
}

const submitStatusUpdate = () => {
  statusForm.patch(route('admin.certificates.update-status', props.certificate.id), {
    preserveScroll: true,
    onSuccess: () => {
      showStatusModal.value = false
      statusForm.reset()
    }
  })
}

const sendCertificate = () => {
  showSendModal.value = true
}

const submitSendCertificate = () => {
  sendForm.post(route('admin.certificates.send', props.certificate.id), {
    preserveScroll: true,
    onSuccess: () => {
      showSendModal.value = false
      sendForm.reset()
    }
  })
}

const renewCertificate = () => {
  renewForm.expiry_date = new Date(new Date().setFullYear(new Date().getFullYear() + 2)).toISOString().split('T')[0]
  showRenewModal.value = true
}

const setExpiry = (days) => {
  const date = new Date()
  date.setDate(date.getDate() + days)
  renewForm.expiry_date = date.toISOString().split('T')[0]
}

const submitRenewCertificate = () => {
  renewForm.patch(route('admin.certificates.renew', props.certificate.id), {
    preserveScroll: true,
    onSuccess: () => {
      showRenewModal.value = false
      renewForm.reset()
    }
  })
}

const generateImage = () => {
  router.post(route('admin.certificates.regenerate-image', props.certificate.id), {}, {
    preserveScroll: true,
    onSuccess: () => {
      imageLoaded.value = false
    }
  })
}

const regenerateImage = () => {
  if (confirm('Regenerate certificate image? This will replace the current image.')) {
    generateImage()
  }
}

const generateQRCode = () => {
  router.post(route('admin.certificates.generate-qr', props.certificate.id), {}, {
    preserveScroll: true,
  })
}

const regenerateQRCode = () => {
  if (confirm('Regenerate QR code? This will replace the current QR code.')) {
    generateQRCode()
  }
}

const downloadCertificate = () => {
  window.open(route('admin.certificates.download', props.certificate.id), '_blank')
}

const downloadQRCode = () => {
  if (props.certificate.qr_code) {
    const link = document.createElement('a')
    link.href = props.certificate.qr_code
    link.download = `qr-code-${props.certificate.certificate_number}.png`
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
  }
}

const printQRCode = () => {
  const printWindow = window.open('', '_blank')
  printWindow.document.write(`
    <html>
      <head>
        <title>QR Code - ${props.certificate.certificate_number}</title>
        <style>
          body { font-family: Arial, sans-serif; text-align: center; padding: 20px; }
          .qr-code { margin: 20px auto; }
          .info { margin-top: 20px; }
        </style>
      </head>
      <body>
        <h2>Certificate QR Code</h2>
        <div class="qr-code">
          <img src="${props.certificate.qr_code}" alt="QR Code" width="300" height="300">
        </div>
        <div class="info">
          <p><strong>Certificate Number:</strong> ${props.certificate.certificate_number}</p>
          <p><strong>Student:</strong> ${props.certificate.user?.name}</p>
          <p><strong>Course:</strong> ${props.certificate.course?.title}</p>
          <p><strong>Verification URL:</strong> ${props.certificate.verification_url}</p>
        </div>
      </body>
    </html>
  `)
  printWindow.document.close()
  printWindow.print()
}

const copyVerificationUrl = () => {
  copyToClipboard(props.certificate.verification_url)
}

const copyEmbedCode = () => {
  copyToClipboard(embedCode.value)
}

const openVerificationPage = () => {
  window.open(props.certificate.verification_url, '_blank')
}

const viewVerification = () => {
  showVerificationModal.value = true
}

const verifyNow = () => {
  window.open(props.certificate.verification_url, '_blank')
}

const togglePublicAccess = () => {
  if (confirm(`Are you sure you want to make this certificate ${props.certificate.is_public ? 'private' : 'public'}?`)) {
    router.patch(route('admin.certificates.toggle-public', props.certificate.id), {}, {
      preserveScroll: true,
    })
  }
}

const addMetadata = () => {
  metadataForm.metadata.push({ key: '', value: '' })
}

const removeMetadata = (index) => {
  metadataForm.metadata.splice(index, 1)
}

const saveMetadata = () => {
  const metadata = {}
  metadataForm.metadata.forEach(item => {
    if (item.key.trim() && item.value.trim()) {
      try {
        metadata[item.key] = JSON.parse(item.value)
      } catch {
        metadata[item.key] = item.value
      }
    }
  })

  metadataForm.metadata = Object.entries(metadata).map(([key, value]) => ({
    key,
    value: typeof value === 'object' ? JSON.stringify(value) : String(value)
  }))

  metadataForm.patch(route('admin.certificates.update-metadata', props.certificate.id), {
    data: { metadata },
    preserveScroll: true,
    onSuccess: () => {
      showMetadataEditor.value = false
    }
  })
}

const deleteCertificate = () => {
  showDeleteModal.value = true
}

const confirmDeleteCertificate = () => {
  deleteForm.delete(route('admin.certificates.delete', props.certificate.id), {
    preserveScroll: true,
    onSuccess: () => {
      // Redirect to certificates list
      router.visit(route('admin.users.certificates', props.certificate.user_id))
    }
  })
}
</script>
