<!-- resources/js/Pages/Student/Certificates/Show.vue -->
<template>
  <StudentLayout>
    <Head :title="`Certificate - ${certificate.title}`" />

    <div class="py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Navigation -->
        <div class="mb-6">
          <Link
            :href="route('student.certificates.index')"
            class="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-medium"
          >
            <ArrowLeftIcon class="h-4 w-4 mr-2" />
            Back to Certificates
          </Link>
        </div>

        <!-- Certificate Header -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
          <div class="flex flex-col md:flex-row md:items-center justify-between">
            <div>
              <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ certificate.title }}</h1>
              <div class="flex items-center space-x-4">
                <span
                  class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold"
                  :class="getStatusClass(certificate.status)"
                >
                  {{ certificate.status }}
                </span>
                <span class="text-gray-600">{{ certificate.certificate_number }}</span>
              </div>
            </div>
            <div class="mt-4 md:mt-0">
              <button
                @click="shareCertificate"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 font-medium mr-3"
              >
                <ShareIcon class="h-4 w-4 mr-2" />
                Share
              </button>
              <button
                @click="downloadCertificate"
                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-lg font-medium hover:shadow-lg"
              >
                <ArrowDownTrayIcon class="h-4 w-4 mr-2" />
                Download PDF
              </button>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Certificate Preview -->
          <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
              <!-- Certificate Design -->
              <div class="bg-gradient-to-br from-emerald-50 to-teal-50 border-4 border-emerald-200 rounded-2xl p-8 text-center relative overflow-hidden">
                <!-- Background Pattern -->
                <div class="absolute inset-0 opacity-5">
                  <div class="grid grid-cols-4 gap-4">
                    <div v-for="i in 16" :key="i" class="w-8 h-8 border-2 border-emerald-300 rounded-lg"></div>
                  </div>
                </div>

                <!-- Certificate Content -->
                <div class="relative z-10">
                  <!-- Issuer Logo -->
                  <div class="mb-8">
                    <div class="inline-flex items-center justify-center">
                      <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-full flex items-center justify-center text-white text-2xl font-bold mr-3">
                        OL
                      </div>
                      <div class="text-left">
                        <div class="text-sm text-gray-600">Issued by</div>
                        <div class="text-xl font-bold text-gray-900">
                          {{ certificate.organization?.name || 'Olilearn AI Learning Platform' }}
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Certificate Title -->
                  <div class="mb-6">
                    <div class="text-sm text-emerald-600 font-semibold mb-2">CERTIFICATE OF COMPLETION</div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">{{ certificate.course?.title }}</h2>
                  </div>

                  <!-- Student Name -->
                  <div class="mb-8">
                    <div class="text-sm text-gray-600 mb-2">This certifies that</div>
                    <div class="text-4xl font-bold text-emerald-700 mb-2">{{ user.name }}</div>
                    <div class="text-lg text-gray-700">
                      has successfully completed the course requirements and demonstrated proficiency
                    </div>
                  </div>

                  <!-- Course Details -->
                  <div class="mb-8">
                    <div class="inline-block border-t border-b border-gray-300 py-4 px-6">
                      <div class="grid grid-cols-2 gap-6 text-sm">
                        <div>
                          <div class="text-gray-500">Course Level</div>
                          <div class="font-semibold text-gray-900">{{ certificate.course?.level }}</div>
                        </div>
                        <div>
                          <div class="text-gray-500">Completion Date</div>
                          <div class="font-semibold text-gray-900">{{ formatDate(certificate.issue_date) }}</div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Certificate Number & QR -->
                  <div class="flex items-center justify-between">
                    <div class="text-left">
                      <div class="text-sm text-gray-500">Certificate Number</div>
                      <div class="font-mono text-lg font-bold text-gray-900">{{ certificate.certificate_number }}</div>
                    </div>
                    <div class="text-right">
                      <div class="w-24 h-24 bg-white p-2 rounded-lg border border-gray-200">
                        <!-- QR Code Placeholder -->
                        <div class="w-full h-full bg-gray-100 flex items-center justify-center text-gray-400">
                          <QrCodeIcon class="h-12 w-12" />
                        </div>
                      </div>
                      <div class="text-xs text-gray-500 mt-2">Scan to verify</div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Verification Info -->
              <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <div class="flex items-center">
                  <ShieldCheckIcon class="h-5 w-5 text-blue-600 mr-3" />
                  <div class="flex-1">
                    <div class="text-sm font-medium text-blue-900">Certificate Verification</div>
                    <div class="text-xs text-blue-700 mt-1">
                      Verify this certificate at: 
                      <a :href="certificate.verification_url" target="_blank" class="underline hover:text-blue-900">
                        {{ certificate.verification_url }}
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Sidebar Actions -->
          <div>
            <!-- Certificate Details -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Certificate Details</h3>
              <div class="space-y-4">
                <div>
                  <div class="text-sm text-gray-500">Course</div>
                  <div class="font-medium text-gray-900">{{ certificate.course?.title }}</div>
                </div>
                <div>
                  <div class="text-sm text-gray-500">Issue Date</div>
                  <div class="font-medium text-gray-900">{{ formatDate(certificate.issue_date) }}</div>
                </div>
                <div v-if="certificate.expiry_date">
                  <div class="text-sm text-gray-500">Expiry Date</div>
                  <div class="font-medium text-gray-900">{{ formatDate(certificate.expiry_date) }}</div>
                </div>
                <div>
                  <div class="text-sm text-gray-500">Status</div>
                  <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold"
                    :class="getStatusClass(certificate.status)"
                  >
                    {{ certificate.status }}
                  </span>
                </div>
                <div>
                  <div class="text-sm text-gray-500">Downloads</div>
                  <div class="font-medium text-gray-900">{{ certificate.download_count }}</div>
                </div>
              </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
              <div class="space-y-3">
                <button
                  @click="downloadCertificate"
                  class="w-full flex items-center justify-center px-4 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-lg font-medium hover:shadow-lg transition-all"
                >
                  <ArrowDownTrayIcon class="h-5 w-5 mr-2" />
                  Download PDF
                </button>
                <button
                  @click="shareCertificate"
                  class="w-full flex items-center justify-center px-4 py-3 border border-emerald-300 text-emerald-700 rounded-lg font-medium hover:bg-emerald-50 transition-colors"
                >
                  <ShareIcon class="h-5 w-5 mr-2" />
                  Share Certificate
                </button>
                <a
                  :href="certificate.verification_url"
                  target="_blank"
                  class="w-full flex items-center justify-center px-4 py-3 border border-blue-300 text-blue-700 rounded-lg font-medium hover:bg-blue-50 transition-colors"
                >
                  <LinkIcon class="h-5 w-5 mr-2" />
                  View Verification
                </a>
              </div>
            </div>

            <!-- Share Options -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Share On</h3>
              <div class="grid grid-cols-2 gap-3">
                <button
                  @click="shareOnLinkedIn"
                  class="flex items-center justify-center px-4 py-3 bg-[#0077B5] text-white rounded-lg font-medium hover:bg-[#006097] transition-colors"
                >
                  <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                  </svg>
                  LinkedIn
                </button>
                <button
                  @click="shareOnTwitter"
                  class="flex items-center justify-center px-4 py-3 bg-[#1DA1F2] text-white rounded-lg font-medium hover:bg-[#0d8bd9] transition-colors"
                >
                  <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.213c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                  </svg>
                  Twitter
                </button>
                <button
                  @click="shareOnFacebook"
                  class="flex items-center justify-center px-4 py-3 bg-[#1877F2] text-white rounded-lg font-medium hover:bg-[#1666d9] transition-colors"
                >
                  <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                  </svg>
                  Facebook
                </button>
                <button
                  @click="copyLink"
                  class="flex items-center justify-center px-4 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors"
                >
                  <LinkIcon class="h-5 w-5 mr-2" />
                  Copy Link
                </button>
              </div>
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
import {
  ArrowLeftIcon,
  ArrowDownTrayIcon,
  ShareIcon,
  ShieldCheckIcon,
  QrCodeIcon,
  LinkIcon,
} from '@heroicons/vue/24/outline'
import { ref } from 'vue'

const props = defineProps({
  certificate: Object,
  user: Object,
})

// Format date
const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
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
const downloadCertificate = () => {
  router.get(route('student.certificates.download', props.certificate.id))
}

// Share certificate
const shareCertificate = () => {
  if (navigator.share) {
    navigator.share({
      title: props.certificate.title,
      text: `Check out my certificate for completing "${props.certificate.course?.title}" on Olilearn!`,
      url: props.certificate.verification_url,
    })
  } else {
    // Fallback for browsers that don't support Web Share API
    copyLink()
  }
}

// Share on LinkedIn
const shareOnLinkedIn = () => {
  const url = `https://www.linkedin.com/profile/add?startTask=CERTIFICATION_NAME&name=${encodeURIComponent(props.certificate.course?.title)}&organizationName=${encodeURIComponent(props.certificate.organization?.name || 'Olilearn')}&issueYear=${new Date(props.certificate.issue_date).getFullYear()}&certUrl=${encodeURIComponent(props.certificate.verification_url)}`
  window.open(url, '_blank')
}

// Share on Twitter
const shareOnTwitter = () => {
  const text = `I just completed "${props.certificate.course?.title}" on Olilearn! Check out my certificate:`
  const url = `https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}&url=${encodeURIComponent(props.certificate.verification_url)}`
  window.open(url, '_blank')
}

// Share on Facebook
const shareOnFacebook = () => {
  const url = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(props.certificate.verification_url)}`
  window.open(url, '_blank')
}

// Copy link to clipboard
const copyLink = () => {
  navigator.clipboard.writeText(props.certificate.verification_url)
  alert('Certificate link copied to clipboard!')
}
</script>