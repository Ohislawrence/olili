<!-- resources/js/Pages/Public/CertificateVerification.vue -->
<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 to-emerald-50">
    <div class="py-12">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-12">
          <Link
            :href="route('home')"
            class="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-medium mb-4"
          >
            <ArrowLeftIcon class="h-4 w-4 mr-2" />
            Back to Olilearn
          </Link>
          <h1 class="text-4xl font-bold text-gray-900 mb-4">Certificate Verification</h1>
          <p class="text-lg text-gray-600">
            Verify the authenticity of this certificate
          </p>
        </div>

        <!-- Verification Result -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
          <!-- Status Banner -->
          <div
            class="p-6"
            :class="certificate.is_valid ? 'bg-gradient-to-r from-emerald-50 to-teal-50' : 'bg-gradient-to-r from-amber-50 to-orange-50'"
          >
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <div
                  class="w-12 h-12 rounded-full flex items-center justify-center mr-4"
                  :class="certificate.is_valid ? 'bg-emerald-100' : 'bg-amber-100'"
                >
                  <CheckCircleIcon
                    v-if="certificate.is_valid"
                    class="h-6 w-6 text-emerald-600"
                  />
                  <ExclamationTriangleIcon
                    v-else
                    class="h-6 w-6 text-amber-600"
                  />
                </div>
                <div>
                  <h2 class="text-2xl font-bold text-gray-900">
                    {{ certificate.is_valid ? 'Certificate Verified' : 'Certificate Not Valid' }}
                  </h2>
                  <p class="text-gray-600">
                    Verified on {{ certificate.verification_date }}
                  </p>
                </div>
              </div>
              <div class="text-right">
                <div class="text-sm text-gray-500">Verification ID</div>
                <div class="font-mono text-lg font-bold text-gray-900">
                  {{ certificate.certificate_number }}
                </div>
              </div>
            </div>
          </div>

          <!-- Certificate Details -->
          <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
              <!-- Certificate Info -->
              <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Certificate Information</h3>
                <div class="space-y-4">
                  <div>
                    <div class="text-sm text-gray-500">Certificate Holder</div>
                    <div class="text-lg font-semibold text-gray-900">{{ certificate.student_name }}</div>
                  </div>
                  <div>
                    <div class="text-sm text-gray-500">Course Title</div>
                    <div class="text-lg font-semibold text-gray-900">{{ certificate.course_title }}</div>
                  </div>
                  <div>
                    <div class="text-sm text-gray-500">Issued By</div>
                    <div class="text-lg font-semibold text-gray-900">{{ certificate.issued_by }}</div>
                  </div>
                  <div>
                    <div class="text-sm text-gray-500">Issue Date</div>
                    <div class="text-lg font-semibold text-gray-900">{{ certificate.issue_date }}</div>
                  </div>
                  <div v-if="certificate.expiry_date">
                    <div class="text-sm text-gray-500">Expiry Date</div>
                    <div class="text-lg font-semibold text-gray-900">{{ certificate.expiry_date }}</div>
                  </div>
                </div>
              </div>

              <!-- Verification Details -->
              <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Verification Details</h3>
                <div class="space-y-4">
                  <div class="p-4 bg-gray-50 rounded-lg">
                    <div class="text-sm text-gray-500 mb-2">Verification Status</div>
                    <div class="flex items-center">
                      <div
                        class="w-3 h-3 rounded-full mr-2"
                        :class="certificate.is_valid ? 'bg-emerald-500' : 'bg-amber-500'"
                      ></div>
                      <span class="font-semibold" :class="certificate.is_valid ? 'text-emerald-600' : 'text-amber-600'">
                        {{ certificate.is_valid ? 'Valid Certificate' : 'Certificate Not Valid' }}
                      </span>
                    </div>
                  </div>
                  <div class="p-4 bg-gray-50 rounded-lg">
                    <div class="text-sm text-gray-500 mb-2">Verification Date & Time</div>
                    <div class="font-semibold text-gray-900">{{ certificate.verification_date }}</div>
                  </div>
                  <div class="p-4 bg-gray-50 rounded-lg">
                    <div class="text-sm text-gray-500 mb-2">Certificate ID</div>
                    <div class="font-mono font-semibold text-gray-900">{{ certificate.certificate_number }}</div>
                  </div>
                  <div v-if="certificate.verification_url" class="p-4 bg-blue-50 rounded-lg">
                    <div class="text-sm text-gray-500 mb-2">Verification URL</div>
                    <a
                      :href="certificate.verification_url"
                      class="font-semibold text-blue-600 hover:text-blue-800 break-all"
                    >
                      {{ certificate.verification_url }}
                    </a>
                  </div>
                </div>
              </div>
            </div>

            <!-- QR Code -->
            <div class="mt-8 pt-8 border-t border-gray-200">
              <div class="flex flex-col md:flex-row md:items-center justify-between">
                <div>
                  <h4 class="text-lg font-semibold text-gray-900 mb-2">Scan to Verify</h4>
                  <p class="text-gray-600">
                    Scan this QR code with your mobile device to verify this certificate
                  </p>
                </div>
                <div class="mt-4 md:mt-0">
                  <div class="w-32 h-32 bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                    <!-- QR Code Placeholder -->
                    <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                      <QrCodeIcon class="h-16 w-16 text-gray-400" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Additional Information -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-4">About Certificate Verification</h4>
            <ul class="space-y-2 text-sm text-gray-600">
              <li class="flex items-start">
                <CheckCircleIcon class="h-5 w-5 text-emerald-500 mr-2 flex-shrink-0 mt-0.5" />
                <span>All Olilearn certificates are digitally signed and verifiable</span>
              </li>
              <li class="flex items-start">
                <CheckCircleIcon class="h-5 w-5 text-emerald-500 mr-2 flex-shrink-0 mt-0.5" />
                <span>Certificate information is stored securely on the blockchain</span>
              </li>
              <li class="flex items-start">
                <CheckCircleIcon class="h-5 w-5 text-emerald-500 mr-2 flex-shrink-0 mt-0.5" />
                <span>Each certificate has a unique verification URL</span>
              </li>
              <li class="flex items-start">
                <CheckCircleIcon class="h-5 w-5 text-emerald-500 mr-2 flex-shrink-0 mt-0.5" />
                <span>Employers can verify certificates instantly</span>
              </li>
            </ul>
          </div>

          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-4">Report Suspicious Certificate</h4>
            <p class="text-sm text-gray-600 mb-4">
              If you believe this certificate is fraudulent or has been tampered with, please report it.
            </p>
            <button
              @click="reportCertificate"
              class="inline-flex items-center px-4 py-2 border border-red-300 text-red-700 rounded-lg hover:bg-red-50 font-medium"
            >
              <ExclamationTriangleIcon class="h-4 w-4 mr-2" />
              Report Certificate
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import {
  ArrowLeftIcon,
  CheckCircleIcon,
  ExclamationTriangleIcon,
  QrCodeIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  certificate: Object,
})

// Report certificate
const reportCertificate = () => {
  if (confirm('Report this certificate as suspicious?')) {
    // Handle report logic
    alert('Certificate reported. Our team will investigate.')
  }
}
</script>